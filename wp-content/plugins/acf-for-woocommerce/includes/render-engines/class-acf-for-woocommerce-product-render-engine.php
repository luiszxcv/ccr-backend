<?php

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class Acf_For_Woocommerce_Product_Render_Engine extends Acf_For_Woocommerce_Base_Render_Engine {

    public function process_cpb_data( $settings ) {
        foreach ( $settings as $setting ) {
            $location = json_decode( $setting["location"], true );
            if ( empty( $location ) ) {
                continue;
            }
            $should_render = $this->should_render( $location["whenToShow"] );
            if ( $should_render['logic'] ) {
                $each_form_setting = ['builder'=>$setting["builder_setting"], 'variation_data'=>$should_render['variation_data']];
                if ( ! isset( $this->hooks_builder_settings_map["{$location['whereToShow']}"] ) ) {
                    $this->hooks_builder_settings_map["{$location['whereToShow']}"] = [ $each_form_setting ];
                } else {
                    array_push( $this->hooks_builder_settings_map["{$location['whereToShow']}"], $each_form_setting );
                }
                add_action( $location['whereToShow'], array( $this, 'render' ) );
            }
        }
    }

    function get_cpb_data() {
        $query_args = array(
            'post_type' => ACF_FW_POST_TYPE,
            'nopaging'  => true
        );
        $query_args = array_merge( $query_args, [ "meta_key" => ACF_FOR_WOO_LOCATION_KEYS["product"] ] );
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

    function render(){
        parent::render();
        $this->render_total_additional_charge();
    }

    function render_each_fields_group($setting) {
        $data = json_decode($setting['builder'], true);
        echo ("<div class='" . CATS_CLASS_PREFIX . "-form-group'>");
        foreach ($data as $row) {
            $this->render_row($row);
        }
        $variation_data = $setting['variation_data'];
        echo "<input type=hidden class='acf-fw-variation-data' value='{$variation_data}'>";
        echo ("</div>");
    }

    function should_render($whenToShow){
        if ($whenToShow['or'] == '') return ['logic' => true, 'variation_data' => ''];
        $logical_string_raw = $this->build_logical_string($whenToShow['or']);
        $logical_string = preg_replace('/\[.*includes\(wc_variation_id\)/','true', $logical_string_raw);
        $expressionLanguage = new ExpressionLanguage();
        $result = $expressionLanguage->evaluate( $logical_string);
        return ['logic' => $result, 'variation_data' => $logical_string_raw];
    }

    function build_product_logic($json_block){
        if (empty($json_block["id"])) return "true";
        else{
            if(wc_get_product() == false || wc_get_product() == null) return "false";
            $result = in_array(wc_get_product()->get_id(), $json_block["id"]);
            $result_str = $result ? "true" : "false" ;
            if(!(wc_get_product()->is_type('variable')))
                return $result_str;
            else{
                if($result)
                    return $result_str;
                else
                    return json_encode($json_block["id"]) . ".includes(wc_variation_id)";
            }
        }
    }

    function render_label($label_name, $for_id, $name_input, $is_required = false){
        parent::render_label($label_name, $for_id, $name_input, $is_required);
        $label_name_input = str_replace('normal', 'label', $name_input);
        echo ("<input name='{$label_name_input}' type='hidden' value='{$label_name}'/>");
    }

    function render_total_additional_charge(){
        echo "<div class='" . CATS_CLASS_PREFIX . "-total-additional-charge-wrap' >";
        echo "<input id='" . CATS_CLASS_PREFIX . "-total-additional-charge-map-input' type='hidden' name='" .
             ACF_FOR_WOO_INPUT_PREFIX . "[total_additional_charge_map]' value='{}'>";
        echo "<input id='" . CATS_CLASS_PREFIX . "-total-additional-charge-input' type='hidden' name='" .
             ACF_FOR_WOO_INPUT_PREFIX . "[total_additional_charge]' value='0'>";
        echo "<label> Total additional charge: </label>";
        $currency_symbol = get_woocommerce_currency_symbol();
        echo "<span>{$currency_symbol}</span>";
        echo "<span id='" . CATS_CLASS_PREFIX . "-total-additional-charge-span'></span>";
        echo "</div>";
    }

    function extract_field_config( $item, $field_groups_type = 'normal' ) {
        $config = parent::extract_field_config ($item);
        if(!empty($item['settings']['options']['pricing']))
            $config['pricing_settings'] = $item['settings']['options']['pricing'];
        return  $config;
    }
}