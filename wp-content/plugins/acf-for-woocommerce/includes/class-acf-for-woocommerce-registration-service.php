<?php
define('ACF_FOR_WOOCOMMERCE_VERSION', '1.0.0');
define('ACF_FW_POST_TYPE', 'acf_fw_form');
define('ACF_FOR_WOO_LOCATION_KEYS', [
    "product" => "_cpb_single_product",
    "account"=>"_cpb_my_account",
    "checkout"=>"_cpb_checkout"]);
define('ACF_FOR_WOO_INPUT_PREFIX', 'acf_fw');
define('CATS_CLASS_PREFIX', 'cp-acf-fw');



class Acf_For_Woocommerce_Registration_Service
{

    private $is_acf_fw_rendered = false;
    public function __construct()
    {
        $this->register_post_type();
        $this->register_builder_meta_box();
        $this->register_public_render();
        $this->register_admin_render();
    }

    function register_admin_render(){
        $this->register_admin_edit_user_render();
        $this->register_admin_edit_order_render();
    }

    function register_public_render(){
        add_action( 'woocommerce_before_single_product', array($this, 'load_product_render_engine'));
        add_action( 'woocommerce_before_checkout_form', array($this, 'load_checkout_render_engine'));
        add_action( 'woocommerce_account_content', array($this, 'load_account_render_engine'));

    }

    function load_product_render_engine(){
        $render_engine = new Acf_For_Woocommerce_Product_Render_Engine();
        $render_engine->run();
    }

    function load_checkout_render_engine(){
        $render_engine = new Acf_For_Woocommerce_Checkout_Render_Engine();
        $render_engine->run();
    }
    function load_account_render_engine(){
        $render_engine = new Acf_For_Woocommerce_Account_Render_Engine();
        $render_engine->run();
    }

    function register_admin_edit_user_render(){
        add_action('edit_user_profile', [$this, 'acf_for_woo_admin_edit_user_render'], 50);
        add_action('show_user_profile', [$this, 'acf_for_woo_admin_edit_user_render'], 50);
    }

    function acf_for_woo_admin_edit_user_render(){
        $admin_render = new Acf_For_Woocommerce_Admin_User_Render_Engine();
        $admin_render->run();
    }

    function register_admin_edit_order_render()
    {
        add_action("add_meta_boxes", array($this, 'add_acf_for_woo_fields_meta_box'));
    }


    function register_post_type()
    {
        add_action('init', array($this, 'acfwc_register_form_post_type'));
    }

    function add_acf_for_woo_fields_meta_box(){
        global $post_ID;
        $acf_fw_data = get_post_meta($post_ID, ACF_FOR_WOO_INPUT_PREFIX);
        $acf_fw_data = empty($acf_fw_data) ? [] : json_decode($acf_fw_data[0], true) ;
        if(isset($acf_fw_data['post_ids']) && $acf_fw_data['post_ids'] != '' ){
            add_meta_box("acf_for_woo_fields_meta_box", "ACF For Woo Custom Fields", array($this, 'acf_for_woo_meta_box_render'), 'shop_order');
        }
    }

    function acf_for_woo_meta_box_render(){
        $admin_render = new Acf_For_Woocommerce_Admin_Order_Render_Engine();
        $admin_render->run();
    }

    function register_builder_meta_box()
    {
        add_action("add_meta_boxes", array($this, 'add_builder_meta_box'));
    }

    function add_builder_meta_box()
    {
        add_meta_box("acf_form_builder_box", "Build your form", array($this, 'wcpa_meta_box_markup'), ACF_FW_POST_TYPE, "normal", "high", null);
    }

    function acfwc_register_form_post_type()
    {
        $labels = array(
            'name' => 'Forms',
            'singular_name' => 'Product Form',
            'name_admin_bar' => 'ACF_Form'
        );
        $args = array(
            'labels' => $labels,
            'public' => true,
            'menu_position' => 5,
            'show_in_menu' => false,
            'supports' => array('title')
        );
        register_post_type(ACF_FW_POST_TYPE, $args);
    }

    public function wcpa_meta_box_markup($post)
    {

            include plugin_dir_path(__FILE__) . 'views/form-edit.php';

    }
}