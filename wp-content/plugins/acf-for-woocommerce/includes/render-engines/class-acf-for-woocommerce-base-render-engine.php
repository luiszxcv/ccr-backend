<?php

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class Acf_For_Woocommerce_Base_Render_Engine
{
    protected $input_key = '';
    protected $saved_values = null;
    protected $hooks_builder_settings_map = [];
    private $current_repeater = ['id' => '', 'block' => ''];
    private $elements_config = [];

    public function __construct(){
        $this->input_key = ACF_FOR_WOO_INPUT_PREFIX;
    }

    public function run(){
        $this->load_acf_for_woo_saved_values();
        $this->process_cpb_data($this->get_cpb_data());
    }

    function load_acf_for_woo_saved_values() {
    }

    public function process_cpb_data( $settings ) {
        foreach ( $settings as $setting ) {
            $location = json_decode( $setting["location"], true );
            if ( empty( $location ) ) {
                continue;
            }
            if ( $this->should_render( $location["whenToShow"] ) ) {
                if ( ! isset( $this->hooks_builder_settings_map["{$location['whereToShow']}"] ) ) {
                    $this->hooks_builder_settings_map["{$location['whereToShow']}"] = [ $setting["builder_setting"] ];
                } else {
                    array_push( $this->hooks_builder_settings_map["{$location['whereToShow']}"], $setting["builder_setting"] );
                }
                add_action( $location['whereToShow'], array( $this, 'render' ) );
            }
        }
    }

    function should_render($whenToShow){
        if ($whenToShow['or'] == '') return true;
        $logical_string = $this->build_logical_string($whenToShow['or']);
        $expressionLanguage = new ExpressionLanguage();
        $result = $expressionLanguage->evaluate($logical_string);
        eval( "\$result = " . $logical_string . ";");
        return $result;
    }
    function build_logical_string($or_groups){
        if($or_groups == []) return "true";
        $logic_string = "";
        foreach ($or_groups as $index => $and_group){
            if ($index>0) $logic_string .= " || ";
            $logic_string .= "(";
            $logic_string .= $this->build_and_logic_group($and_group["and"]);
            $logic_string .= ")";
        }
        return $logic_string;
    }
    function build_and_logic_group($and_group){
        if (empty($and_group)) return "true";
        $str = "";
        foreach ($and_group as $index => $each_block){
            if ($index>0) $str .= " && ";
            $str .= "(";
            $str .= $this->build_each_block_logic_string($each_block);
            $str .= ")";
        }
        return $str;
    }
    function build_each_block_logic_string($json_block){
        switch ($json_block['target']){
            case 'User':
                if (empty($json_block["id"])) $block_string = "false";
                else {
                    $block_string = in_array(wp_get_current_user()->ID, $json_block["id"]) ? "true" : "false";
                }
                if (!empty($json_block["value"])){
                    $block_string .= " || " . (sizeof(array_intersect(wp_get_current_user()->roles, $json_block["value"])) > 0 ? "true" : "false") ;
                }
                break;
            case 'Product':
                $block_string = $this->build_product_logic($json_block);
                break;
            case 'Category':
                if (empty($json_block["id"])) $block_string = "true";
                else {
                    $block_string = sizeof(array_intersect(wc_get_product()->get_category_ids(), $json_block["id"])) > 0 ? "true" : "false";
                }
                break;
            case 'Attribute':
                if (empty($json_block["id"])) $block_string = "true";
                else {
                    $block_string = sizeof(array_intersect(array_reduce(wc_get_product()->get_attributes(),
                        function ($ids, $attribute){return array_merge($ids, $attribute['data']['options']);}, []), $json_block["id"])) > 0 ? "true" : "false";
                }
                break;
            default: $block_string = "true";
        }

        return $block_string;
    }

    function build_product_logic($json_block){
        return "true";
    }

    function get_cpb_data()
    {

    }

    function render(){
        $current_action = current_action();
        echo "<div class='" . CATS_CLASS_PREFIX . "-form-groups' style='margin-bottom: 10px;'>";
        foreach($this->hooks_builder_settings_map["{$current_action}"] as $builder_setting){
            $this->render_each_fields_group($builder_setting);
        }
        echo "</div>";
        $this->render_scripts();
    }

    function render_scripts(){
        $class_prefix = CATS_CLASS_PREFIX;
        $elements = json_encode($this->elements_config);
        $api_endpoint = get_home_url();
        echo "<script>update_global_value({$elements}, '{$class_prefix}', '{$api_endpoint}')</script>";
    }

    function render_each_fields_group($setting) {
        $data = json_decode($setting, true);
        echo ("<div class='" . CATS_CLASS_PREFIX . "-form-group'>");
        foreach ($data as $row) {
            $this->render_row($row);
        }
        echo ("</div>");
    }

    function render_row($row) {
        echo "<div class='" . CATS_CLASS_PREFIX ."-row'>";
        foreach ($row['cols'] as $col) {
            $this->render_column($col);
        }
        echo "</div>";
    }

    function render_column($col) {
        $class = CATS_CLASS_PREFIX . "-col ".
                 CATS_CLASS_PREFIX . "-col-sm-{$col['width']['sm']} ".
                 CATS_CLASS_PREFIX . "-col-md-{$col['width']['md']} ".
                 CATS_CLASS_PREFIX . "-col-xs-{$col['width']['xs']}";
        foreach ($col['elements'] as $element) {
            $field_config = $this->extract_field_config($element);
            array_push($this->elements_config, $field_config);
            echo "<div class='{$class}'>";
            $this->render_each_element($field_config);
            echo "</div>";

        }
    }

    function render_each_element($field_config){
        $class_name = CATS_CLASS_PREFIX . "-element";
        echo ("<div class='{$class_name}' id='{$field_config['field_name']}'>");
        $this->render_label($field_config['label'], $field_config['input_id'], $field_config['name_input'], $field_config['required']);
        switch ($field_config['field_type']) {
            case 'text_field':
                $this->render_text_field($field_config['input_id'], $field_config['name_input'],
                    $field_config['text_field_type'], $field_config['value'],$field_config['place_holder'],
                    $field_config['required'], $field_config['validation_rules']);
                break;
            case 'radio':
                $this->render_radio($field_config['input_id'], $field_config['name_input'],
                    $field_config['options'], $field_config['value']);
                break;
            case 'select':
                $this->render_selectbox($field_config['input_id'], $field_config['name_input'],
                    $field_config['options'], $field_config['value']);
                break;
            case 'checkbox':
                $this->render_checkbox($field_config['input_id'], $field_config['name_input'],
                    $field_config['options'], $field_config['value']);
                break;
            case 'text-area':
                $this->render_text_area( $field_config['input_id'], $field_config['name_input'],
                    $field_config['value'],$field_config['place_holder'],
                    $field_config['required'], $field_config['limit']);
                break;
            case 'date-time-picker':
                $this->render_datetime_picker( $field_config['input_id'], $field_config['name_input'],
                    $field_config['value']);
                break;
            case 'slide-range':
                $this->render_slide_range($field_config['input_id'], $field_config['name_input'],
                    $field_config['slider_options'], $field_config['value']);
                break;
            case 'file-upload':
                $this->render_file_upload($field_config['input_id'], $field_config['name_input'], $field_config['value']);
                break;
            case 'image-upload':
                $this->render_image_upload($field_config['input_id'], $field_config['name_input'], $field_config['value']);
                break;
            case 'repeater':
                $this->render_repeater( $field_config['input_id'], $field_config['name_input'], $field_config['repeater_rows'] );
                break;
        }
        echo ("</div>");
    }

    function render_label($label_name, $for_id, $name_input, $is_required = false){
        $class = CATS_CLASS_PREFIX . "-label";
        $for_attribute = ($for_id)? "for={$for_id}" : '';
        echo ("<label class='{$class}' $for_attribute>{$label_name}");
        if ($is_required) echo ("<span class='required'>*</span>");
        echo ("</label>");
    }

    function render_text_field($id, $name_input, $type, $value = '', $place_holder = '', $is_required = false,  $validation_rules = null){
        $class_name = CATS_CLASS_PREFIX . "-text-field ";
        $rules = '';
        if($validation_rules && $validation_rules['limit'] && $validation_rules['limit'] != 0)
            $rules = "max_length={$validation_rules['limit']}";
        if($is_required)
            $rules .= " required ";

        echo ("<input class='{$class_name}' id='{$id}' name='{$name_input}' type='{$type}'
                value='{$value}' placeholder='{$place_holder}' {$rules} >");

    }


    function render_radio($id, $name_input, $options, $value = ''){
        if (empty($options))
            echo ("<p><i> Please input the options </i></p>");
        else {
            echo ("<input class='" . CATS_CLASS_PREFIX . "-field-value-input" . "' name='{$name_input}' id='{$id}' type='hidden' value='{$value}'/>");
            foreach ($options as $index => $option) {
                $input_id = "option". strval($index) . $name_input;
                echo ("<input id='{$input_id}' type='radio' name='{$name_input}' value='$option' " .
                      "class='" . CATS_CLASS_PREFIX . "-radio-option' " .
                     ($option == $value ? "checked" : '') . " ><label for='{$input_id}'> {$option} </label><br>");
            }
        };
    }

    function render_checkbox($id, $name_input, $options, $value = ''){
        if (empty($options))
            echo ("<p><i> Please input the options </i></p>");
        else {
            echo ("<input class='" . CATS_CLASS_PREFIX . "-field-value-input" . "' name='{$name_input}' id='{$id}' type='hidden' value='{$value}'/>");
            foreach ($options as $index => $option) {
                $input_id = "option". strval($index) . $name_input;
                $input_class = CATS_CLASS_PREFIX . "-checkbox-option";
                $checked = (strpos($value, $option) !== false) ? "checked" : '';
                echo ("<input id='{$input_id}' type='checkbox' class='{$input_class}' value='$option' 
                    {$checked} ><label for='{$input_id}'> {$option} </label><br>");
            };
        };
    }

    function render_selectbox($id, $name_input, $options, $value = '') {
        if (empty($options))
            echo ("<p><i> Please input the options </i></p>");
        else {
            echo ("<input class='" . CATS_CLASS_PREFIX . "-field-value-input" . "' name='{$name_input}' id='{$id}' type='hidden' value='{$value}'/>");
            echo ("<select class='" . CATS_CLASS_PREFIX . "-select name='{$name_input}' >");
            foreach ( $options as $index => $option) {
                echo ("<option value='{$option}'" .
                      ($value == $option ? " selected" : "") . " >{$option}</option>");
            }
            echo ("</select>");
        };
    }

    function render_slide_range($id, $name_input, $slider_options,  $value = '') {
        echo ("<div class='" . CATS_CLASS_PREFIX . "-slide-range-wrap' >");
        echo ("<span>{$slider_options['prepend']}</span>");
        echo ("<input class='" . CATS_CLASS_PREFIX . "-slide-input' type='range' min='{$slider_options['min_value']}' max='{$slider_options['max_value']}'
                value='{$value}' step='{$slider_options['step']}' name='{$name_input}' >");
        echo ("<input class='" . CATS_CLASS_PREFIX . "-slide-number-input' type='number' value='{$value}' name='{$name_input}' >");
        echo ("<span>{$slider_options['append']}</span>");
        echo ("</div>");

    }

    function render_text_area($id, $name_input, $value = '', $place_holder = '', $is_required = false, $limit = null) {
        echo ("<input class='" . CATS_CLASS_PREFIX . "-field-value-input" . "' id='{$id}' type='hidden' value='{$value}'/>");
        echo ("<textarea class='" . CATS_CLASS_PREFIX . "-text-area' ".
              "placeholder='{$place_holder}' name='{$name_input}'" .
              ($is_required ? "required " : " ") .
              ($limit? "maxlength='{$limit}'": "") .
              ">{$value}</textarea>");
    }

    function render_file_upload($id, $name_input, $value = ''){
        echo ("<input class='" . CATS_CLASS_PREFIX . "-field-value-input' name='{$name_input}' type='hidden' value='{$value}'/>");
        $file_name = basename($value);
        echo ("<a class='" . CATS_CLASS_PREFIX . "-file-url' href='{$value}'>{$file_name}</a>");
        echo ("<a class='" . CATS_CLASS_PREFIX . "-remove-file-btn ".
              CATS_CLASS_PREFIX . "-remove-btn' " .
              (($value == '')? "style='display: none' " : " " ) .
              "><i class='fa fa-times-circle'></i></a>");
        echo ("<input type='file' name='{$id}' class='" . CATS_CLASS_PREFIX . "-upload-file-input' ".
              (($value == '')? " />" : "style='display: none' />") );
        echo ("<i class='fa fa-spinner fa-spin' style='display: none; font-size:20px'></i>");
        echo ("<span class='error-msg' style='display: none; color: red'></span>");
    }

    function render_image_upload($id, $name_input, $value = ''){
        echo ("<input class='" . CATS_CLASS_PREFIX . "-field-value-input' name='{$name_input}' type='hidden' value='{$value}'/>");
        echo ("<div class='" . CATS_CLASS_PREFIX . "-img-container' " .
              ($value=='' ? "style='display: none'>" : ">"));
        echo ("<img class='" . CATS_CLASS_PREFIX . "-img' src='{$value}' />");
        echo ("<a class='" . CATS_CLASS_PREFIX . "-remove-img-btn " .
              CATS_CLASS_PREFIX . "-remove-btn' " .
              "><i class='fa fa-times-circle' aria-hidden='true'></i></a>");
        echo ("</div>");
        echo ("<input type='file' name='{$id}' accept='image/*' class='" . CATS_CLASS_PREFIX . "-upload-img-input' ".
              (($value == '')? " />" : "style='display: none' />") );
        echo ("<i class='fa fa-spinner fa-spin' style='display: none; font-size:20px'></i>");
        echo ("<span class='error-msg' style='display: none; color: red'></span>");
    }

    function render_datetime_picker($id, $name_input, $value){
        echo ("<input type='datetime-local' name='$name_input' value='{$value}'>");
    }

    function render_repeater($id, $name_input, $repeater_rows) {
        echo ("<div class='" . CATS_CLASS_PREFIX . "-repeater-wrap'>");
        echo ("<div class='" . CATS_CLASS_PREFIX . "-repeater-blocks'>");
        //render template for cloning
        $this->render_repeater_block($repeater_rows, $id, 'template');
        //render normal repeater block
        $this->render_repeater_block($repeater_rows, $id, 'origin');
        if(isset($this->saved_values)){
            foreach ($this->saved_values as $value){
                if(isset($value["{$id}"])){
                    foreach ($value["{$id}"] as $key => $value){
                        if($key == 'origin' || $key == 'template' || $key == 'self') continue;
                        $this->render_repeater_block($repeater_rows, $id, $key);
                    }
                }
            }
        }
        echo ("</div>");
        echo ("<button class='" . CATS_CLASS_PREFIX . "-repeater-add-btn'> Add Row </button>");
        echo ("</div>");
    }

    function render_repeater_block($rows, $id, $block){
        $this->current_repeater = ['id' => $id, 'block' => $block];
        echo ("<div class='" . CATS_CLASS_PREFIX . "-repeater-block ");
        if($block == 'template') echo (CATS_CLASS_PREFIX . "-repeater-template' style='display:none'>");
        else echo "'>";

        echo ("<a class='" . CATS_CLASS_PREFIX . "-remove-repeater-btn " .
              CATS_CLASS_PREFIX . "-remove-btn' " .
              "><i class='fa fa-times-circle' aria-hidden='true'></i></a>");

        foreach ($rows as $row) {
            $this->render_row($row);
        }
        echo ("</div>");
        $this->current_repeater = ['id' => '', 'block' => ''];
    }

    function extract_field_config ($item, $field_group_type = 'normal') {
        $place_holder = empty($item['settings']['placeholder']['value']) ? "Place holder" : $item['settings']['placeholder']['value'];
        $options = empty($item['settings']['options']['value']) ? "" : $item['settings']['options']['value'];
		$validation = empty($item['settings']['type']['validation']) ? "" : $item['settings']['type']['validation'];
        $name_input = ACF_FOR_WOO_INPUT_PREFIX . "[{$field_group_type}]" . "[{$item['name']}]";
		$field_name = $item['field_name'];
		$input_id = $item['name'];
        $value = isset($item['settings']['default']['value']) ? $item['settings']['default']['value'] : '';
        if(($this->current_repeater['id'] == '') && isset($this->saved_values) && isset($this->saved_values["{$field_group_type}"]))
            $value = isset($this->saved_values["{$field_group_type}"]["{$item['name']}"])?
                $this->saved_values["{$field_group_type}"]["{$item['name']}"] : '';
        if($this->current_repeater['id'] != ''){
            $name_input = ACF_FOR_WOO_INPUT_PREFIX . "[{$field_group_type}]" .
                          "[{$this->current_repeater['id']}]" . "[{$this->current_repeater['block']}]" . "[{$item['name']}]";
            $field_name = $this->current_repeater['id'] . "_" . $this->current_repeater['block'] . $item['field_name'];
            if(isset($this->saved_values))
            {
                if(isset($this->saved_values["{$field_group_type}"]["{$this->current_repeater['id']}"]["{$this->current_repeater['block']}"]["{$item['name']}"]))
                    $value = $this->saved_values["{$field_group_type}"]["{$this->current_repeater['id']}"]["{$this->current_repeater['block']}"]["{$item['name']}"];
            }
            $input_id = $this->current_repeater['id'] . "_" . $this->current_repeater['block'] . $item['name'];
        }
        $config = array (
            'field_type' => $item['id'],
            'input_id' => $input_id,
            'name_input' => $name_input,
            'label' =>  $item['settings']['label']['value'] ? $item['settings']['label']['value'] : 'Default label',
            'value' => $value,
            'options'=> $options == "" ? [] : explode("\n", $options),
            'place_holder' =>  $place_holder,
            'field_name' => $field_name,
            'required' => isset($item['settings']['required']) ? $item['settings']['required']['value'] : false,
            'validation_rules' => $validation,
            'conditional_settings' => $item['condition_settings']
        );
        switch ($item['id']){
            case 'text_field':
                $config['text_field_type'] = empty($item['settings']['type']['value']) ? "" : $item['settings']['type']['value'];
            break;
            case 'slide-range':
                $slider_options = array(
                    'max_value' => isset($item['settings']['max_value'])? $item['settings']['max_value']['value'] : '',
                    'min_value' => isset($item['settings']['min_value'])? $item['settings']['min_value']['value'] : '',
                    'prepend' => isset($item['settings']['prepend'])? $item['settings']['prepend']['value'] : '',
                    'append' => isset($item['settings']['append'])? $item['settings']['append']['value']: '',
                    'step' => isset($item['settings']['step'])? $item['settings']['step']['value'] : ''
                );
                $config['slider_options'] = $slider_options;
                break;
            case 'text-area':
                $config['limit'] = $item['settings']['limit_character']['value'];
                break;
            case 'repeater':
                $config['repeater_rows'] = isset($item['settings']['layout'])? $item['settings']['layout']['rows'] : '';
                $config['name_input'] = $name_input . "[self]";
                break;
        }
        return $config;
    }

}
