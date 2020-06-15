<?php

class Acf_For_Woocommerce_Request_Handler {
    public function __construct() {
        add_action( 'save_post', array( $this, 'save_info_builder' ) );
    }

    public function save_info_builder( $post_id ) {
        if(!isset($_POST["builder_data"])) return;
        $builder_data = $_POST["builder_data"];
        $location_single_product_data = $_POST["builder_location_single_product_data"];
        $location_my_account_data = $_POST["builder_location_my_account_data"];
        $location_checkout_data = $_POST["builder_location_checkout_data"];

        if (isset($location_my_account_data)) {
            update_post_meta( $post_id, '_cpb_my_account', $location_my_account_data );
        }
        if (isset($location_single_product_data)) {
            update_post_meta( $post_id, '_cpb_single_product', $location_single_product_data );
        }
        if (isset($location_checkout_data)) {
            update_post_meta( $post_id, '_cpb_checkout', $location_checkout_data );
        }
        
        update_post_meta( $post_id, '_cpb_builder_data', $builder_data );
    }
}