<?php

namespace MessengerNotifier;

if (!defined('ABSPATH')) {
    exit;
}

class MessengerNotifier {
    private static $instance = null;

    private function __construct() {
        add_action('plugins_loaded', [$this, 'conditionally_load_hooks']);
    }

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function conditionally_load_hooks() {
        // Load hooks only when their related events occur
        add_action('user_register', function () {
            require_once plugin_dir_path(__FILE__) . 'hooks/user.php';
            new \MessengerNotifier\Hooks\UserHooks();
        });

        add_action('woocommerce_order_status_completed', function () {
            require_once plugin_dir_path(__FILE__) . 'hooks/order.php';
            new \MessengerNotifier\Hooks\OrderHooks();
        });

        add_action('comment_post', function () {
            require_once plugin_dir_path(__FILE__) . 'hooks/comment.php';
            new \MessengerNotifier\Hooks\CommentHooks();
        });

        // Load API classes only if enabled in settings
        if (get_option('messengernotifier_enable_eitaa') === 'yes') {
            require_once plugin_dir_path(__FILE__) . 'api/eitaa.php';
        }

        if (get_option('messengernotifier_enable_bale') === 'yes') {
            require_once plugin_dir_path(__FILE__) . 'api/bale.php';
        }

        if (get_option('messengernotifier_enable_soroush') === 'yes') {
            require_once plugin_dir_path(__FILE__) . 'api/soroush.php';
        }
    }
}

// Initialize the plugin
MessengerNotifier::get_instance();
