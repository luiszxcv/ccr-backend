<?php

class Acf_For_Woocommerce_Admin_Order_Render_Engine extends Acf_For_Woocommerce_Base_Render_Engine
{
    private $place_builder_settings_map = [];
    private $acf_fw_post_ids = [];
    private $rendering_place = '';
    public function __construct()
    {
        global $post_ID;
        $acf_fw_data = get_post_meta( $post_ID, ACF_FOR_WOO_INPUT_PREFIX );
        if(!empty($acf_fw_data))
            $this->saved_values = json_decode($acf_fw_data[0], true);
        $this->acf_fw_post_ids = isset($this->saved_values['post_ids']) ? json_decode($this->saved_values['post_ids'], true) : [];
        $this->place_builder_settings_map = ['billing'=> [], 'shipping'=> [], 'other' => []];
    }

    public function run(){
        $this->process_cpb_data($this->get_cpb_data());
        $this->render();
    }

    function get_display_text($place){
        if($place == 'billing')
            return 'Billing';
        elseif($place == 'shipping')
            return 'Shipping';
        else
            return 'Other';
    }

    function process_cpb_data($settings)
    {
        foreach ($settings as $setting){
            $location = json_decode($setting["location"], true);
            $location = $this->map_checkout_hook_from_my_account($location);
            if(empty($location)) continue;
            $place = $this->get_place_from_hook($location['whereToShow']);
            array_push($this->place_builder_settings_map["{$place}"], $setting['builder_setting']);
        }
    }

    function render() {
        echo "<div class='" . CATS_CLASS_PREFIX . "-form-groups' style='margin-bottom: 10px;'>";
        echo "<div class='" . CATS_CLASS_PREFIX . "-row'>";
        foreach ( $this->place_builder_settings_map as $place => $builder_settings ) {
            echo "<div class='" . CATS_CLASS_PREFIX . "-col " .
                 CATS_CLASS_PREFIX . "-col-sm-4 " .
                 CATS_CLASS_PREFIX . "-col-md-4 " .
                 CATS_CLASS_PREFIX . "-col-xs-4' >";
            echo "<strong> {$this->get_display_text($place)} </strong>";
            $this->rendering_place = $place;
            foreach ($builder_settings as $settings)
            $this->render_each_fields_group( $settings );
            echo "</div>";
        }
        echo "</div>";
        $value = json_encode($this->acf_fw_post_ids);
        echo "<input type='hidden' name='" . ACF_FOR_WOO_INPUT_PREFIX . "[post_ids]' value='{$value}'>";
        echo "</div>";
        $this->render_scripts();
    }

    function get_place_from_hook($hook){
        if(strpos($hook,'billing'))
            return 'billing';
        if(strpos($hook,'shipping'))
            return 'shipping';
        return 'other';
    }

    function get_cpb_data()
    {
        $query_args = array(
            'post_type' => ACF_FW_POST_TYPE,
            'post__in' => $this->acf_fw_post_ids,
            'nopaging' => true,
        );
        $query = new WP_Query($query_args);

        $cpb_settings = [];
        foreach ($query->posts as $post){
            $cpb_setting = [];
            $post_metas = get_post_meta($post->ID);
            if ($post_metas["_cpb_checkout"][0] != "[]" ){
                $cpb_setting["location"] = $post_metas["_cpb_checkout"][0];
                $cpb_setting["builder_setting"] = $post_metas["_cpb_builder_data"][0];
                array_push($cpb_settings, $cpb_setting);
            }elseif($post_metas["_cpb_my_account"][0] != "[]")
            {
                $cpb_setting["location"] = $post_metas["_cpb_my_account"][0];
                $cpb_setting["builder_setting"] = $post_metas["_cpb_builder_data"][0];
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

    public function extract_field_config( $item, $field_group_type = 'normal' ) {
        if($this->rendering_place == 'other')
            return parent::extract_field_config( $item, $field_group_type );
        else
            return parent::extract_field_config( $item, $this->rendering_place );
    }
}
