<?php
namespace MessengerNotifier\API;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Eitaa {
    private $api_url = "https://eitaayar.ir/api/";

    public function send_text_message($message, $hashtag = '') {
        // Get token and channel ID from settings
        $token = get_option('messengernotifier_eitaa_token');
        $channel_id = get_option('messengernotifier_eitaa_channel_id');

        if (!$token || !$channel_id) {
            return [
                'success' => false,
                'error'   => __('Missing API credentials.', 'messengernotifier'),
            ];
        }

        $post_fields = [
            'chat_id' => $channel_id,
            'text'    => $message . "\n\n" . $hashtag,
            'parse_mode' => 'HTML',
        ];

        $args = [
            'body'    => $post_fields,
            'timeout' => 45,
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
        ];

        $response = wp_remote_post($this->api_url . $token . "/sendMessage", $args);

        if (is_wp_error($response)) {
            return [
                'success' => false,
                'error'   => $response->get_error_message(),
            ];
        }

        return [
            'success' => wp_remote_retrieve_response_code($response) == 200,
            'response' => wp_remote_retrieve_body($response),
        ];
    }
}
?>
