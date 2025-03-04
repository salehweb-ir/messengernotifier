<?php
/*
Plugin Name: Messenger Notifier
Plugin URI: https://salehweb.ir/messengernotifier
Description: Send message to messenger apps from WordPress (started by eitaa).
Version: 0.1
Author: salehweb
Author URI: https://salehweb.ir
License: GPL2
Text Domain: messengernotifier
Domain Path: /languages
*/

// Load plugin textdomain for translations
function messengernotifier_load_textdomain() {
    load_plugin_textdomain('v', false, basename(dirname(__FILE__)) . '/languages');
}
add_action('init', 'messengernotifier_load_textdomain');

// Activation hook
function messengernotifier_activate() {
    load_plugin_textdomain('messengernotifier', false, basename(dirname(__FILE__)) . '/languages');
    add_option('messengernotifier_do_activation_redirect', true);
}
register_activation_hook(__FILE__, 'messengernotifier_activate');

// add shortcode to display default form template
function messengernotifier_default_template_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/default.php';
    return ob_get_clean();
}
add_shortcode('default_template', 'messengernotifier_default_template_shortcode');

// Redirect to wizard
function messengernotifier_activation_redirect() {
    if (get_option('messengernotifier_do_activation_redirect', false)) {
        delete_option('messengernotifier_do_activation_redirect');
        if (!isset($_GET['activate-multi'])) {
            wp_safe_redirect(admin_url('admin.php?page=messengernotifier_wizard'));
            exit;
        }
    }
}
add_action('admin_init', 'messengernotifier_activation_redirect');

// Enqueue scripts and styles
function messengernotifier_enqueue_scripts() {
    wp_enqueue_style('messengernotifier-admin-css', plugin_dir_url(__FILE__) . 'assets/admin/css/admin-styles.css');
    wp_enqueue_style('messengernotifier-wizard-css', plugin_dir_url(__FILE__) . 'assets/admin/css/wizard-styles.css');
    wp_enqueue_script('messengernotifier-wizard-js', plugin_dir_url(__FILE__) . 'assets/admin/js/wizard.js', array('jquery'), null, true);
    wp_enqueue_style('messengernotifier-user-css', plugin_dir_url(__FILE__) . 'assets/user/css/user-styles.css');
    wp_enqueue_script('messengernotifier-form-js', plugin_dir_url(__FILE__) . 'assets/user/js/form-interactions.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'messengernotifier_enqueue_scripts');
add_action('wp_enqueue_scripts', 'messengernotifier_enqueue_scripts');

// Add menu and settings page
function messengernotifier_add_admin_menu() {
    add_menu_page(
        __('MessengerNotifier', 'messengernotifier'),
        __('MessengerNotifier', 'messengernotifier'),
        'manage_options',
        'messengernotifier',
        'messengernotifier_settings_page',
        'dashicons-email-alt',
        6
    );

    add_submenu_page(
        null,
        __('MessengerNotifier Setup Wizard', 'messengernotifier'),
        __('MessengerNotifier Setup Wizard', 'messengernotifier'),
        'manage_options',
        'messengernotifier_wizard',
        'messengernotifier_wizard_page'
    );
}
add_action('admin_menu', 'messengernotifier_add_admin_menu');

function messengernotifier_sanitize_text_field($input) {
    return sanitize_text_field(wp_unslash($input)); // unslash and sanitize value before save to options
}

// Register settings
function messengernotifier_register_settings() {
	
	register_setting('messengernotifier_options_group', 'token_eitaa_api', array('sanitize_callback' => 'messengernotifier_sanitize_text_field'));
	register_setting('messengernotifier_options_group', 'eitaa_channel_id', array('sanitize_callback' => 'messengernotifier_sanitize_text_field'));

}
add_action('admin_init', 'messengernotifier_register_settings');

// Settings page callback
function messengernotifier_settings_page() {
    include plugin_dir_path(__FILE__) . 'admin/settings.php';
}

// Wizard page callback
function messengernotifier_wizard_page() {
    include plugin_dir_path(__FILE__) . 'admin/wizard.php';
}

require_once plugin_dir_path( __FILE__ ) . 'includes/send-message.php';
?>