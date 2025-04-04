<?php
namespace MessengerNotifier\API;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Eitaa {
    private $token;
    private $channel_id;

    public function __construct() {
        $this->token      = get_option('messengernotifier_eitaa_token');
        $this->channel_id = get_option('messengernotifier_eitaa_channel_id');
    }

    /**
     * Send a text message to Eitaa
     * 
     * @param string $message The message to send
     * @param string $hashtag The message type (e.g., #test, #message, #order)
     * @return array API response with success or error message
     */
    public function send_text_message($message, $hashtag = '') {
        if (empty($this->token) || empty($this->channel_id)) {
            return [
                'success' => false,
                'error'   => __('Eitaa API token or channel ID is missing.', 'messengernotifier'),
            ];
        }

        $url = "https://eitaayar.ir/api/{$this->token}/sendMessage";

        $post_fields = [
            'chat_id'    => $this->channel_id,
            'text'       => $message . "\n\n" . $hashtag,
            'parse_mode' => 'HTML'
        ];

        $args = [
            'body'    => $post_fields,
            'timeout' => 45,
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
        ];

        $response = wp_remote_post($url, $args);

        // Handle API response
        if (is_wp_error($response)) {
            return [
                'success' => false,
                'error'   => $response->get_error_message(),
            ];
        }

        $http_code     = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);

        return [
            'success' => ($http_code == 200),
            'response' => $response_body,
        ];
    }
}
?>
