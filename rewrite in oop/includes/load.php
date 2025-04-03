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
        add_action('user_register', [$this, 'handle_user_registration']);
        add_action('woocommerce_order_status_completed', [$this, 'handle_order_completion']);
        add_action('comment_post', [$this, 'handle_comment_post']);

        // Conditionally load API classes
        $this->load_api_classes();
    }

    /** Hook Handlers **/
    public function handle_user_registration() {
        $file = plugin_dir_path(__FILE__) . 'hooks/user.php';
        if (file_exists($file)) {
            require_once $file;
            new \MessengerNotifier\Hooks\UserHooks();
        }
    }

    public function handle_order_completion() {
        $file = plugin_dir_path(__FILE__) . 'hooks/order.php';
        if (file_exists($file)) {
            require_once $file;
            new \MessengerNotifier\Hooks\OrderHooks();
        }
    }

    public function handle_comment_post() {
        $file = plugin_dir_path(__FILE__) . 'hooks/comment.php';
        if (file_exists($file)) {
            require_once $file;
            new \MessengerNotifier\Hooks\CommentHooks();
        }
    }

    /** Load API classes only if enabled **/
    private function load_api_classes() {
        $apis = [
            'eitaa'   => 'messengernotifier_eitaa_enabled',
            'bale'    => 'messengernotifier_bale_enabled',
            'soroush' => 'messengernotifier_soroush_enabled',
        ];

        foreach ($apis as $api => $option_name) {
            if (get_option($option_name) === 'yes') {
                $file = plugin_dir_path(__FILE__) . "api/{$api}.php";
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        }
    }
}

// Initialize the plugin
MessengerNotifier::get_instance();
