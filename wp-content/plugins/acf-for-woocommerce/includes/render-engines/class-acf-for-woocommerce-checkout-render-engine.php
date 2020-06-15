<?php


class Acf_For_Woocommerce_Checkout_Render_Engine extends Acf_For_Woocommerce_Base_Render_Engine {

    private $post_ids = [];
    function load_acf_for_woo_saved_values() {
        $key = ACF_FOR_WOO_INPUT_PREFIX;
        $user_meta = get_user_meta(get_current_user_id());
        if(isset($user_meta["{$this->input_key}"])){
            $this->saved_values = json_decode($user_meta["{$this->input_key}"][0], true);
        }
    }

    public function process_cpb_data($settings)
    {
        foreach ($settings as $setting){
            $location = json_decode($setting["location"], true);
            $location = $this->map_checkout_hook_from_my_account($location);
            if (empty($location)) continue;
            if($this->should_render($location["whenToShow"])) {
                array_push($this->post_ids, $setting['post_id']);
                if (!isset($this->hooks_builder_settings_map["{$location['whereToShow']}"]))
                    $this->hooks_builder_settings_map["{$location['whereToShow']}"] = [$setting["builder_setting"]];
                else array_push($this->hooks_builder_settings_map["{$location['whereToShow']}"], $setting["builder_setting"]);
                add_action($location['whereToShow'], array($this, 'render') );
            }
        }
    }

    function render(){
        parent::render();

        $value = json_encode($this->post_ids);
        echo "<input type='hidden' name='" . ACF_FOR_WOO_INPUT_PREFIX . "[post_ids]' value='{$value}'>";
    }

    function render_label($label_name, $for_id, $name_input, $is_required = false){
        parent::render_label($label_name, $for_id, $name_input, $is_required);
        $label_name_input = preg_replace('/normal|billing|shipping/', 'label', $name_input);
        echo ("<input name='{$label_name_input}' type='hidden' value='{$label_name}'/>");
    }

    function build_product_logic($json_block){
        if (empty($json_block["id"])) return "true";
        $item_ids = array_map(function ($item){
            return $item['data']->get_id();
        },array_values(WC()->cart->get_cart()));
        $result = sizeof(array_intersect($item_ids, $json_block["id"])) > 0 ? "true" : "false";
        return $result;
    }
    function get_cpb_data() {
        $query_args = array(
            'post_type' => ACF_FW_POST_TYPE,
            'nopaging'  => true
        );
        $query = new WP_Query( $query_args );
        $cpb_settings = [];
        foreach ( $query->posts as $post ) {
            $cpb_setting = [];
            $post_metas  = get_post_meta( $post->ID );
            if ($post_metas["_cpb_checkout"][0] != "[]" ){
                $cpb_setting["location"] = $post_metas["_cpb_checkout"][0];
                $cpb_setting["builder_setting"] = $post_metas["_cpb_builder_data"][0];
                $cpb_setting['post_id'] = $post->ID;
                array_push($cpb_settings, $cpb_setting);
            }elseif($post_metas["_cpb_my_account"][0] != "[]"){
                $cpb_setting["location"] = $post_metas["_cpb_my_account"][0];
                $cpb_setting["builder_setting"] = $post_metas["_cpb_builder_data"][0];
                $cpb_setting['post_id'] = $post->ID;
                array_push($cpb_settings, $cpb_setting);
            }
        }

        return $cpb_settings;
    }


    function map_checkout_hook_from_my_account($location){
        switch ($location['whereToShow']){
            case 'woocommerce_before_edit_address_form_billing':
                $location['whereToShow'] = 'woocommerce_before_checkout_billing_form';
                break;
            case 'woocommerce_after_edit_address_form_billing':
                $location['whereToShow'] = 'woocommerce_after_checkout_billing_form';
                break;
            case 'woocommerce_before_edit_address_form_shipping':
                $location['whereToShow'] = 'woocommerce_before_checkout_shipping_form';
                break;
            case 'woocommerce_after_edit_address_form_shipping':
                $location['whereToShow'] = 'woocommerce_after_checkout_shipping_form';
                break;
            case 'woocommerce_edit_account_form_start':
            case 'woocommerce_edit_account_form_end':
                return [];
            default:
                break;
        }
        return $location;
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