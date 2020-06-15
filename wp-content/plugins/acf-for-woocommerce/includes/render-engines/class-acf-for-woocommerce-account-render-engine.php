<?php


class Acf_For_Woocommerce_Account_Render_Engine extends Acf_For_Woocommerce_Base_Render_Engine {

    function load_acf_for_woo_saved_values() {
        $user_meta = get_user_meta(get_current_user_id());
        if(isset($user_meta["{$this->input_key}"])){
            $this->saved_values = json_decode($user_meta["{$this->input_key}"][0], true);
        }
    }

    function get_cpb_data() {
        $query_args = array(
            'post_type' => ACF_FW_POST_TYPE,
            'nopaging'  => true
        );
        $query_args = array_merge( $query_args, [ "meta_key" => ACF_FOR_WOO_LOCATION_KEYS["account"] ] );
        $query = new WP_Query( $query_args );
        $cpb_settings = [];
        foreach ( $query->posts as $post ) {
            $cpb_setting = [];
            $post_metas  = get_post_meta( $post->ID );
            if(isset($post_metas["{$query_args["meta_key"]}"]) && $post_metas["{$query_args["meta_key"]}"][0] != "[]")
            {
                $cpb_setting["location"] = $post_metas["{$query_args["meta_key"]}"][0];
                $cpb_setting["builder_setting"] = $post_metas["_cpb_builder_data"][0];
                array_push($cpb_settings, $cpb_setting);
            }
        }

        return $cpb_settings;
    }
    function get_field_groups_type(){
        if(stripos(current_action(),'billing')){
            return 'billing';
        }elseif (stripos(current_action(),'shipping')){
            return 'shipping';
        }else
            return 'normal';
    }

    function extract_field_config( $item, $field_group_type = 'normal') {
        $field_groups_type = $this->get_field_groups_type();
        return parent::extract_field_config( $item, $field_groups_type );
    }
}