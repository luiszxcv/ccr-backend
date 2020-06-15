<?php

class Acf_For_Woocommerce_Admin_User_Render_Engine extends Acf_For_Woocommerce_Base_Render_Engine
{
    private $place_builder_settings_map = [];
    private $acf_fw_meta_key = '';
    private $rendering_place = '';
    private $render_user = null;
    public function __construct($user=null)
    {
        if(!isset($user)){
            global $profileuser;
            $this->render_user = $profileuser;
        }
        $acf_fw_key = ACF_FOR_WOO_INPUT_PREFIX;
        $this->saved_values = json_decode(get_user_meta($this->render_user->ID)["{$acf_fw_key}"][0], true);
        $this->acf_fw_meta_key = '_cpb_my_account';
        $this->place_builder_settings_map = ['details' => [], 'billing'=> [], 'shipping'=> []];
    }

    public function run(){
        $this->process_cpb_data($this->get_cpb_data());
        if(empty($this->place_builder_settings_map['details']) &&
           empty($this->place_builder_settings_map['billing']) &&
           empty($this->place_builder_settings_map['shipping']))
            return;
        $this->render();
    }

    public function process_cpb_data($settings)
    {
        foreach ($settings as $setting){
            $location = json_decode($setting["location"], true);
            if($this->should_render($location['whenToShow'])){
                $place = $this->get_place_from_hook($location['whereToShow']);
                array_push($this->place_builder_settings_map["{$place}"], $setting['builder_setting']);
            }

        }
    }

    function render() {
        echo "<h2>ACF For Woo Custom Fields</h2>";
        echo "<div class='" . CATS_CLASS_PREFIX . "-form-groups' style='margin-bottom: 10px;'>";
        echo "<div class='" . CATS_CLASS_PREFIX . "-fw-row'>";
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
        echo "</div>";
        $this->render_scripts();
    }

    function get_display_text($place){
        if($place == 'billing')
            return 'Billing';
        elseif($place == 'shipping')
            return 'Shipping';
        else
            return 'Account Details';
    }

    function get_place_from_hook($hook){
        if(strpos($hook,'billing'))
            return 'billing';
        if(strpos($hook,'shipping'))
            return 'shipping';
        return 'details';
        return 'undefined';
    }

    function get_cpb_data()
    {
        $query_args = array(
            'post_type' => ACF_FW_POST_TYPE,
            'nopaging' => true,
        );
        $query = new WP_Query($query_args);

        $cpb_settings = [];
        foreach ($query->posts as $post){
            $cpb_setting = [];
            $post_metas = get_post_meta($post->ID);
            if(isset($post_metas["{$this->acf_fw_meta_key}"]) && $post_metas["{$this->acf_fw_meta_key}"][0] != "[]")
            {
                $cpb_setting["location"] = $post_metas["{$this->acf_fw_meta_key}"][0];
                $cpb_setting["builder_setting"] = $post_metas["_cpb_builder_data"][0];
                array_push($cpb_settings, $cpb_setting);
            }
        }
        return $cpb_settings;
    }

    function should_render($whenToShow){
        if ($whenToShow['or'] == '') return true;
        $result = false;
        $logical_string = $this->build_logical_string($whenToShow['or']);
        eval( "\$result = " . $logical_string . ";");
        return $result;
    }

    function build_each_block_logic_string($json_block){
        $block_string = "";
        switch ($json_block['target']){
            case 'User':
                if (empty($json_block["id"])) $block_string = "false";
                else $block_string = "in_array(\$this->render_user->ID, ". json_encode($json_block["id"]) . ")";
                if (!empty($json_block["value"]))
                    $block_string .= " || sizeof(array_intersect(\$this->render_user->roles, ". json_encode($json_block["value"]) .")) > 0";
                break;
            default: $block_string = "true";
        }

        return $block_string;
    }

    public function extract_field_config( $item, $field_group_type = 'normal' ) {
        if($this->rendering_place == 'details')
            return parent::extract_field_config( $item, $field_group_type );
        else
            return parent::extract_field_config( $item, $this->rendering_place );
    }

}
