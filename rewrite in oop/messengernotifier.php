<?php
/**
 * Plugin Name: Messenger Notifier
 * Plugin URI:  https://salehweb.ir/messengernotifier
 * Description: Send messages to messenger apps from WordPress (started by Eitaa).
 * Version:     1.1
 * Author:      salehweb
 * Author URI:  https://salehweb.ir
 * License:     GPL3
 * Text Domain: messengernotifier
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Load necessary files
require_once plugin_dir_path(__FILE__) . 'includes/load.php';

// Run wizard on activation
function messengernotifier_activate() {
    require_once plugin_dir_path(__FILE__) . 'includes/wizard.php';
    MessengerNotifier\Wizard::run();
}
register_activation_hook(__FILE__, 'messengernotifier_activate');

// Initialize the plugin
add_action('plugins_loaded', function () {
    MessengerNotifier\MessengerNotifier::get_instance();
});

function messengernotifier_load_textdomain() {
    load_plugin_textdomain('messengernotifier', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'messengernotifier_load_textdomain');
