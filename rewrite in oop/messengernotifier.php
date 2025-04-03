<?php
/*
Plugin Name: Messenger Notifier
Plugin URI: https://salehweb.ir/messengernotifier
Description: Send messages to messenger apps from WordPress (started by Eitaa).
Version: 1.1
Author: salehweb
Author URI: https://salehweb.ir
License: GPL3
Text Domain: messengernotifier
Domain Path: /languages
*/

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Use the main class
use MessengerNotifier\MessengerNotifier;

// Load the main plugin class
require_once plugin_dir_path(__FILE__) . 'includes/load.php';

// Initialize the plugin
function run_messenger_notifier() {
    MessengerNotifier::get_instance();
}
add_action('plugins_loaded', 'run_messenger_notifier');
