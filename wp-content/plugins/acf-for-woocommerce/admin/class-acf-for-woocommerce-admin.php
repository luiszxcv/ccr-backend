<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://misite.com
 * @since      1.0.0
 *
 * @package    Acf_For_Woocommerce
 * @subpackage Acf_For_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Acf_For_Woocommerce
 * @subpackage Acf_For_Woocommerce/admin
 * @author     Thuong <truonghoaithuong2000@gmail.com>
 */
class Acf_For_Woocommerce_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Acf_For_Woocommerce_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Acf_For_Woocommerce_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/acf-for-woocommerce-admin.css', array(), $this->version, 'all' );
        wp_enqueue_style( 'vua-and', plugin_dir_url( __FILE__ ) . 'css/vue-ant.css', array(), $this->version, 'all' );
        wp_enqueue_style( 'grid', plugin_dir_url( __FILE__ ) . 'css/_grid.css', array(), $this->version, 'all' );
        wp_enqueue_style( 'style-builder', plugin_dir_url( __FILE__ ) . 'css/style-builder.css', array(), $this->version, 'all' );
        wp_enqueue_style( 'fontawesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome-4.7.0/css/font-awesome.min.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Acf_For_Woocommerce_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Acf_For_Woocommerce_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script( 'moment', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js');
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/acf-for-woocommerce-admin.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( 'vue', plugin_dir_url( __FILE__ ) . 'js/vue.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( 'sortable', 'https://cdn.jsdelivr.net/npm/sortablejs@1.8.3/Sortable.min.js' );
        wp_enqueue_script( 'draggable', plugin_dir_url( __FILE__ ) . 'js/draggable.js' );
        wp_enqueue_script( 'step', plugin_dir_url( __FILE__ ) . 'js/step.min.js', array( 'moment', 'jquery', 'vue', 'draggable', 'sortable', 'antd' ), $this->version, false );
        wp_enqueue_script( 'antd', 'https://cdn.jsdelivr.net/npm/ant-design-vue@1.3.5/dist/antd.min.js' );
        wp_enqueue_script( 'underscorejs', 'https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.9.1/underscore-min.js' );
        wp_enqueue_script( 'axios', 'https://unpkg.com/axios/dist/axios.min.js' );
        wp_enqueue_media();
    }

}

