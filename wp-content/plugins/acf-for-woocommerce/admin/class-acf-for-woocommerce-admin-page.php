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
class Acf_For_Woocommerce_Admin_Page {

    public function __construct()
    {
        add_action('admin_menu', [$this, 'addPage'], 30);
    }

    public function addPage()
    {
        add_submenu_page(
            \CastPlugin\CpConstant::SLUG_DASHBOARD,
            "ACF For WooCommerce",
            "ACF For WooCommerce",
            "manage_options",
            'edit.php?post_type=acf_fw_form'
        );
    }

}
new Acf_For_Woocommerce_Admin_Page();

