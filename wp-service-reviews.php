<?php
/**
 * Plugin Name: Service Reviews & Ratings
 * Plugin URI: https://trturkey.net
 * Description: نظام تقييمات الخدمات المتقدم مع لوحة تحكم شاملة
 * Version: 1.0.0
 * Author: TR Turkey
 * Author URI: https://trturkey.net
 * License: GPL-2.0-or-later
 * Text Domain: service-reviews
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WSRP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WSRP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WSRP_PLUGIN_VERSION', '1.0.0');

// تفعيل الإضافة
register_activation_hook(__FILE__, 'wsrp_activate_plugin');
function wsrp_activate_plugin() {
    require_once WSRP_PLUGIN_DIR . 'includes/class-rating-database.php';
    WSRP_Rating_Database::create_tables();
}

// تحميل الملفات الأساسية
require_once WSRP_PLUGIN_DIR . 'includes/class-rating-database.php';
require_once WSRP_PLUGIN_DIR . 'includes/class-rating-handler.php';
require_once WSRP_PLUGIN_DIR . 'includes/class-rating-shortcode.php';

if (is_admin()) {
    require_once WSRP_PLUGIN_DIR . 'admin/class-admin-menu.php';
    require_once WSRP_PLUGIN_DIR . 'admin/class-ratings-list.php';
}

// تحميل النماذج والأمام
require_once WSRP_PLUGIN_DIR . 'public/class-rating-form.php';

// تفعيل الفئات الرئيسية
add_action('plugins_loaded', 'wsrp_init_plugin');
function wsrp_init_plugin() {
    new WSRP_Rating_Handler();
    new WSRP_Rating_Shortcode();
    new WSRP_Rating_Form();
    
    if (is_admin()) {
        new WSRP_Admin_Menu();
    }
}

// تحميل ملفات CSS و JS
add_action('wp_enqueue_scripts', 'wsrp_enqueue_frontend_scripts');
function wsrp_enqueue_frontend_scripts() {
    wp_enqueue_style('wsrp-style', WSRP_PLUGIN_URL . 'public/css/style.css', [], WSRP_PLUGIN_VERSION);
    wp_enqueue_script('wsrp-form', WSRP_PLUGIN_URL . 'public/js/form.js', ['jquery'], WSRP_PLUGIN_VERSION, true);
    wp_localize_script('wsrp-form', 'wsrp_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wsrp_nonce')
    ]);
}

add_action('admin_enqueue_scripts', 'wsrp_enqueue_admin_scripts');
function wsrp_enqueue_admin_scripts($hook) {
    if (strpos($hook, 'service-reviews') !== false) {
        wp_enqueue_style('wsrp-admin', WSRP_PLUGIN_URL . 'admin/css/admin.css', [], WSRP_PLUGIN_VERSION);
        wp_enqueue_script('wsrp-admin', WSRP_PLUGIN_URL . 'admin/js/admin.js', ['jquery'], WSRP_PLUGIN_VERSION, true);
    }
}

// إضافة قائمة الإعدادات
add_action('admin_menu', 'wsrp_add_admin_menu');
function wsrp_add_admin_menu() {
    add_menu_page(
        'تقييمات الخدمات',
        'تقييمات الخدمات',
        'manage_options',
        'service-reviews',
        'wsrp_render_admin_page',
        'dashicons-star-filled',
        30
    );
}

function wsrp_render_admin_page() {
    $admin = new WSRP_Admin_Menu();
    $admin->render_page();
}
?>