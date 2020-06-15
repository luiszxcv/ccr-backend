<?php 

/*function prepare_restful_categories($response, $item, $request) {
}
add_filter('rest_prepare_category', 'prepare_restful_categories', 10, 3);*/



add_action( 'rest_api_init', 'slug_register_meta' );
function slug_register_meta() {
    register_rest_field( 'category',
        'acf', 
        array(
            'get_callback'    => 'slug_get_meta',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}
function slug_get_meta( $object, $field_name, $request ) {
    
    /*$term_meta = $request->get_json_params()['acf'] ?? [];
    foreach( $term_meta as $meta_key => $meta_value ){
        update_term_meta( $object[ 'id' ], $meta_key, $meta_value );
    }*/
    $user_id = get_current_user_id();
    update_term_meta( $object[ 'id' ], "proprietario", $user_id);

    return  get_term_meta( $object[ 'id' ] );
}
?>