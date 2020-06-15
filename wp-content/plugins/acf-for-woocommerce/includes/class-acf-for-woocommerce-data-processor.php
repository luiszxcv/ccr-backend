<?php

class Acf_For_Woocommerce_Data_Processor
{
    private $keys_to_add_to_formats = "";
    private $input_key = '';
    public function __construct()
    {
        $this->input_key = ACF_FOR_WOO_INPUT_PREFIX;
        $this->register_single_product_processor();
        $this->register_my_account_processor();
        $this->register_checkout_processor();
        $this->register_admin_processor();
    }

    function register_admin_processor(){
        add_action( 'personal_options_update', [$this, 'save_acf_for_woo_user_meta']);
        add_action( 'edit_user_profile_update', [$this, 'save_acf_for_woo_user_meta']);
        add_action( 'woocommerce_process_shop_order_meta', [$this, 'save_shop_order_meta_data']);
    }

    function save_shop_order_meta_data($post_id){
        if(isset($_POST["{$this->input_key}"])) {
            update_post_meta( $post_id, $this->input_key, json_encode( $_POST["{$this->input_key}"] ) );
        }
    }
    function save_acf_for_woo_user_meta($user_id){
        if(isset($_POST["{$this->input_key}"])){
            update_user_meta($user_id, $this->input_key, json_encode($_POST["{$this->input_key}"]));
        }
    }

    function register_checkout_processor(){

        $this->get_checkout_posted_data();
        $this->save_item_meta_data();
        $this->save_data_to_order();
    }

    function get_checkout_posted_data(){
        add_filter('woocommerce_checkout_posted_data', function ($data){
            $acf_fw_inputs = $_POST["{$this->input_key}"];
            $data["{$this->input_key}"] = $acf_fw_inputs;
            return $data;
        });
    }

    function save_item_meta_data(){
        add_action('woocommerce_checkout_create_order_line_item', function ($item, $cart_item_key, $values ){
//            $item->add_meta_data('acf_fw', $values['acf_fw']);
            $acf_fw_data = $values["{$this->input_key}"];
            if($acf_fw_data){
                foreach ($acf_fw_data['normal'] as $key => $value)
                    $item->add_meta_data($acf_fw_data['label']["{$key}"], $value);
            }

        },10,3);
    }

    function save_data_to_order(){
        add_action('woocommerce_checkout_create_order', function ($order, $data){
            $order->update_meta_data($this->input_key, json_encode($data["{$this->input_key}"]));
        },10,2);
    }

    function register_my_account_processor(){
        add_action( 'woocommerce_save_account_details', function ($customer_id){
            $acf_fw_inputs = $_POST["{$this->input_key}"];
            $saved_data = get_user_meta($customer_id);
            $all_data = isset($saved_data["{$this->input_key}"])? json_decode(get_user_meta($customer_id)["{$this->input_key}"][0], true) : [];
            foreach( $acf_fw_inputs as $key => $value ){
                $all_data["{$key}"] = $value;
            }
            update_user_meta($customer_id, $this->input_key, json_encode($all_data));
        });

        add_action('woocommerce_after_save_address_validation', function ($user_id){
            $acf_fw_inputs = $_POST["{$this->input_key}"];
            $all_data = json_decode(get_user_meta($user_id)["{$this->input_key}"][0], true);
            foreach ($acf_fw_inputs as $key => $value){
                $all_data["{$key}"] = $value;
            }
            update_user_meta($user_id, $this->input_key, json_encode($all_data));
        },10);
    }

    function register_single_product_processor(){

        add_filter('woocommerce_add_cart_item_data', function ($cart_item_data, $product_id){
            if (!isset($_POST["{$this->input_key}"])) return $cart_item_data;
            $acf_fw_inputs = $_POST["{$this->input_key}"];
            if(isset($acf_fw_inputs['total_additional_charge']) && $acf_fw_inputs['total_additional_charge'] != '0')
                $acf_fw_inputs = $this->update_pricing_inputs($acf_fw_inputs);
            $cart_item_data["{$this->input_key}"] = $acf_fw_inputs;

            //update price
            $product = wc_get_product( $product_id );
            $price = $product->get_price();
            $cart_item_data['total_price'] = $price + (int)$acf_fw_inputs['total_additional_charge'];

            return $cart_item_data;
        },10,2);

        add_filter( 'woocommerce_get_item_data', function ($item_data, $cart_item){
            if(!isset($cart_item["{$this->input_key}"])) return $item_data;
            $values = $cart_item["{$this->input_key}"]['normal'];
            $labels = $cart_item["{$this->input_key}"]['label'];

            foreach ($values as $key=>$value){
                if(is_array($value)){ //repeater
                    $item_data[] = array(
                        'key'     => $labels["{$key}"]['self'],
                        'name'    => $labels["{$key}"]['self'],
                        'value'   => $this->build_repeater_value_for_cart_item($value, $labels["{$key}"]),
                    );
                }elseif($value != ''){
                    $item_data[] = array(
                        'key'     => $labels["{$key}"],
                        'name'    => $labels["{$key}"],
                        'value'   => wc_clean( $value ),
                    );
                }
            }
            return $item_data;
        }, 10, 2 );

        add_action( 'woocommerce_before_calculate_totals', function ($cart_obj){
            foreach( $cart_obj->get_cart() as $key=>$value ) {
                if( isset( $value['total_price'] ) ) {
                    $price = $value['total_price'];
                    $value['data']->set_price( ( $price ) );
                }
            }
        });

    }

    function build_repeater_value_for_cart_item($repeater_values, $repeater_labels){
        $text_array = '';
        foreach ($repeater_values as $block_key => $block_value){
            $block_text = [];
            if($block_key == 'template') continue;
            foreach ($block_value as $key => $value){
                array_push($block_text, $repeater_labels["{$block_key}"]["{$key}"] . ': ' . $value);
            }
            $text_array .= join(', ', $block_text) . "\n\n";
        }
        return "\n" . $text_array;
    }

    function update_pricing_inputs($acf_fw_inputs){
        $currency_symbol = get_woocommerce_currency_symbol();
        $charge_map = json_decode(preg_replace('/[\x5C ]/','', $acf_fw_inputs['total_additional_charge_map']), true);
        foreach ($charge_map as $full_name_input => $charge){
            $name_input = '';
            preg_match('/_cpb_\d+/', $full_name_input, $name_input);
            $name_input = end($name_input);
            $acf_fw_inputs['normal']["{$name_input}"] .= " +" . $currency_symbol . strval($charge);
        }
        return $acf_fw_inputs;
    }

}