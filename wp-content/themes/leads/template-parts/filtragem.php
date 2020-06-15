
<?php


/*
Verificando se o usuário é ADM ou Author
*/
/*add_filter( 'rest_authentication_errors', function( $result ) {
    if ( ! empty( $result ) ) {
      return $result;
    }
    if ( ! is_user_logged_in() ) {
      return new WP_Error( 'rest_not_logged_in', 'Você não está logado!.', array( 'status' => 401 ) );
    }

    if ( ! current_user_can( 'administrator' )  || ! current_user_can( 'author' )) {
      return new WP_Error( 'rest_not_admin', 'Você não tem permissões suficiente.', array( 'status' => 401 ) );
    }

    return $result;
});*/
#---------------------------------------------------


/*
ative/disable frontend
*/
//add_filter( 'wp_headless_rest__disable_front_end', '__return_false' );
#---------------------------------------------------


/*
ative/disable filtro da api rest
*/
add_filter ('wp_headless_rest__enable_rest_cleanup', '__return_true');
#---------------------------------------------------


/*
filtrando endpoints
*/
add_filter( 'wp_headless_rest__rest_endpoints_to_remove', 'wp_rest_headless_disable_endpoints' );
function wp_rest_headless_disable_endpoints( $endpoints_to_remove ) {

    $endpoints_to_remove = array(
        '/wp/v2/media',
        '/wp/v2/types',
        '/wp/v2/statuses',
        '/wp/v2/taxonomies',
        '/wp/v2/categories',
        '/wp/v2/search',
        '/wp/v2/tags',
        //'/wp/v2/users',
        '/wp/v2/informes',
        '/wp/v2/posts',
        '/wp/v2/pages',
        '/wp/v2/comments',
        '/wp/v2/settings',
        '/wp/v2/themes',
        '/wp/v2/blocks',
        '/wp/v2/block-renderer',
        '/oembed/',
        //JETPACK
        'jp_pay_product',
        'jp_pay_order',
    );

    if ( ! is_user_logged_in() ) {
        //return new WP_Error( 'rest_not_logged_in', 'Você não está logado!.', array( 'status' => 401 ) );
        $endpoints_to_remove = array(
            '/wp/v2/media',
            '/wp/v2/types',
            '/wp/v2/statuses',
            '/wp/v2/taxonomies',
            '/wp/v2/categories',
            '/wp/v2/search',
            '/wp/v2/tags',
            //'/wp/v2/users',
            //'/wp/v2/informes',
            '/wp/v2/posts',
            '/wp/v2/pages',
            '/wp/v2/comments',
            '/wp/v2/settings',
            '/wp/v2/themes',
            '/wp/v2/blocks',
            '/wp/v2/block-renderer',
            '/oembed/',
            //JETPACK
            'jp_pay_product',
            'jp_pay_order',
        );
    }
    else{
        if ( current_user_can( 'administrator' ) ) {
            $endpoints_to_remove = array(
                '/wp/v2/media',
                '/wp/v2/types',
                '/wp/v2/statuses',
                //'/wp/v2/taxonomies',
                //'/wp/v2/tags',
                //'/wp/v2/users',
                '/wp/v2/posts',
                '/wp/v2/pages',
                '/wp/v2/comments',
                '/wp/v2/settings',
                '/wp/v2/themes',
                '/wp/v2/blocks',
                '/wp/v2/block-renderer',
                '/oembed/',
                //JETPACK
                'jp_pay_product',
                'jp_pay_order',
            );
        }
        if ( current_user_can( 'author' ) ) {
            $endpoints_to_remove = array(
                '/wp/v2/media',
                '/wp/v2/types',
                '/wp/v2/statuses',
                '/wp/v2/taxonomies',
                //'/wp/v2/tags',
                //'/wp/v2/users',
                '/wp/v2/posts',
                '/wp/v2/pages',
                '/wp/v2/comments',
                '/wp/v2/settings',
                '/wp/v2/themes',
                '/wp/v2/blocks',
                '/wp/v2/block-renderer',
                '/oembed/',
                //JETPACK
                'jp_pay_product',
                'jp_pay_order',
            );
        }
    }
	return $endpoints_to_remove;
}
#---------------------------------------------------


/*
filtrando caminhos específico respostas
*/
#Select which post types to clean:
add_filter( 'wp_headless_rest__post_types_to_clean', 'wp_rest_headless_clean_post_types' );
function wp_rest_headless_clean_post_types( $post_types_to_clean ) {

	$post_types_to_clean = array(
        'informes',
	);

	return $post_types_to_clean;
}
#Select what to remove from the response object:
add_filter( 'wp_headless_rest__rest_object_remove_nodes', 'wp_rest_headless_clean_response_nodes' );
function wp_rest_headless_clean_response_nodes( $items_to_remove ) {

	$items_to_remove = array(
		'guid',
		'_links',
		'ping_status',
	);

	return $items_to_remove;
}
#---------------------------------------------------

?>

