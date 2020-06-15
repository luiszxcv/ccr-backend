<?php

add_action( 'rest_api_init', 'acf_register_routes' );

function acf_register_routes() {
    register_rest_route( ACF_FW_POST_TYPE, 'api/v1/users_and_roles/(?P<query>.*)', [
        'methods'  => WP_REST_Server::READABLE,
        'callback' => 'get_all_user_and_roles'
    ] );
    register_rest_route(ACF_FW_POST_TYPE, 'api/v1/products/(?P<query>.*)', [
        'methods'  => WP_REST_Server::READABLE,
        'callback' => 'get_products'
    ] );
    register_rest_route(ACF_FW_POST_TYPE, 'api/v1/categories/(?P<query>.*)', [
        'methods'  => WP_REST_Server::READABLE,
        'callback' => 'get_wp_categories'
    ] );
    register_rest_route(ACF_FW_POST_TYPE, 'api/v1/attributes/(?P<query>.*)', [
        'methods'  => WP_REST_Server::READABLE,
        'callback' => 'get_wp_attributes'
    ] );
    register_rest_route(ACF_FW_POST_TYPE, 'api/v1/files', [
        'methods'  => WP_REST_Server::CREATABLE,
        'callback' => 'handle_files'
    ] );
}

function handle_files() {
    return handle_file_upload();
}

function handle_file_upload(){
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    $upload_overrides = array(
        'test_form' => false
    );
    if(!empty($_FILES)){
        foreach ($_FILES as $key => $value){
            if($value['error'] != 0) new WP_Error( 'invalid_file', 'Invalid File', array( 'status' => 400 ) );
            $uploadedfile = $value;
            $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
            return $movefile;
        }
    }

}
/* ROLE */

function get_all_roles($term) {
    global $wp_roles;

    $term = strtolower ($term);
    $roles = array_map( function ( $key, $value ) {
        return [
            'value' => $key,
            'title' => $value
        ];
    }, array_keys( $wp_roles->get_names() ), $wp_roles->get_names() );
    $roles = array_slice($roles, 0, 5);

    if (strcmp ($term, "all") != 0) {
        $filterd_roles = [];
        foreach ($roles as &$item) {
            if (strpos($item['value'], $term) !== false) {
                array_push($filterd_roles, $item);
            }
        }
        $roles = $filterd_roles;
    }

    $data = [
        'title'    => 'Role',
        'value'    => 'Role',
        'children' => $roles
    ];
    return empty($roles) ? null : $data;
}

/* USER */

function get_all_users($term) {
    $users = [];
    if (strcmp ($term, "all") != 0) {
        $query = get_userdatabylogin($term);
        if ($query != false) {
            array_push($users, $query);
        }
    } else {
        $users = get_users( array(
            'fields' => array( 'ID', 'user_login'),
            'number' => 5
        ) );
    }
    $users = user_data($users);

    $data = [
        'title'    => 'User',
        'value'    => 'User',
        'children' => $users
    ];
    return empty($users) ? null : $data;
}

function user_data($users) {
    $users = array_map( function ( $item ) {
        return [
            'value' => $item->ID,
            'title' => $item->user_login
        ];
    }, $users);
    return $users;
}


/* PRODUCT */

function get_products(WP_REST_Request $request) {
    $term = $request->get_param('query');
    global $wpdb;
    $post_table = $wpdb->prefix . "posts";
    if (strcmp ($term, "all") != 0) {
        $products = $wpdb->get_results("
            SELECT ID, post_title
            FROM $post_table
            WHERE post_title LIKE '%$term%'
            AND post_type='product'
            AND post_status='publish'");
    } else {
        $products = $wpdb->get_results("
            SELECT ID, post_title
            FROM $post_table
            WHERE post_type='product' AND post_status='publish'
            LIMIT 10 ");
    }
    $products = array_map('map_products', $products);
    return $products;
}

function map_products($item) {
    $children = [];
    $children_ids =  (new WC_Product_Variable($item->ID))->get_children();
    if(!empty($children_ids)){
        $children = (new WP_Query(['post_type' => 'product_variation', 'post__in' => $children_ids]))->posts;
        $children = array_map(function ($item) {
            return [
                'value' => $item->ID,
                'title' => $item->post_title
            ];
        }, $children);
        array_unshift($children, ['value' => $item->ID,'title' => $item->post_title]);
    }
    return [
        'value' => empty($children)? $item->ID : 0,
        'title' => $item->post_title,
        'children' => $children
    ];
}
/* CATEGORY */

function get_wp_categories(WP_REST_Request $request) {
    $term = $request->get_param('query');
    $categories = [];
    if (strcmp ($term, "all") != 0) {
        $cat = get_term_by( 'id', $term, 'category' );
        if ( $cat ) {
            $cat_item = [
                'value'  => $cat->term_id,
                'title'  => $cat->name
            ];
            array_push($categories, $cat_item);
        }
    } else {
        $args = array(
            'taxonomy' => 'product_cat',
            'hide_empty' => 0,
            'number' => 10
        );
        $categories = get_categories($args);
        $categories = array_map(function ($item) {
            return [
                'value'  => $item->cat_ID,
                'title'  => $item->name
            ];
        }, $categories);
    }

    return $categories;
}


/* ATTRIBUTE */

function get_wp_attributes(WP_REST_Request $request) {
    $term = $request->get_param('query');
    $result = [];
    $attributes = get_all_attributes($term);
    $terms = get_all_terms($term);
    if ($attributes != null) {
        array_push($result, $attributes);
    }
    if ($terms != null) {
        array_push($result, $terms);
    }
    return $result;
}

function get_all_attributes($term) {
    global $wpdb;
    $attributes = [];
    $list = [];
    if (strcmp ($term, "all") != 0) {
        $queries = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies 
                   WHERE attribute_label LIKE '%{$term}%' ORDER BY attribute_label ASC;");

        $list = array_map(function ($item) {
            return [
                'value' => $item->attribute_name,
                'title' => $item->attribute_label
            ];
        }, $queries);

    } else {
        $args = array(
            'taxonomy' => 'attribute',
            'hide_empty' => false,
            'number' => 5
        );
        $attributes = wc_get_attribute_taxonomies();

        foreach ($attributes as $item) {
            $value = [
                'value'  => $item->attribute_name,
                'title'  => $item->attribute_label
            ];
            array_push($list, $value);
        }
    }


    $data = [
        'title'    => 'Attribute',
        'value'    => 'Attribute',
        'children' => $list
    ];
    return empty($list) ? null : $data;
}

function get_all_terms($term) {
    $terms = [];
    if (strcmp ($term, "all") != 0) {
        $terms = get_terms(array(
            'name' => $term
        ));
    } else {

        $attribute_name = wc_get_attribute_taxonomy_names();
        $terms = get_terms(array(
            'taxonomy' => $attribute_name,
            'number'   => 5
        ));
    }
    $terms = array_map(function ($item) {
        return [
            'value'  => $item->term_id,
            'title'  => $item->name
        ];
    }, $terms);

    $data = [
        'title'    => 'Term',
        'value'    => 'Term',
        'children' => $terms
    ];
    return empty($terms) ? null : $data;
}


function get_all_user_and_roles(WP_REST_Request $request) {
    $term = $request->get_param('query');
    $roles = get_all_roles($term);
    $users = get_all_users($term);
    $result = [];
    if ($roles != null) {
       array_push($result, $roles);
    }
    if ($users != null) {
        array_push($result, $users);
    }
    return $result;
}