<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// send text message to eitaa
function messengernotifier_send_text_message($token, $channel_id, $message) {
    $url = "https://eitaayar.ir/api/".$token."/sendMessage";

    $post_fields = array(
        'chat_id' => $channel_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    );

    $args = array(
        'body'    => $post_fields,
        'timeout' => 45, // set wait time to avoid timeout
        'headers' => array(
        'Content-Type' => 'application/x-www-form-urlencoded'
        ),
    );

    $response = wp_remote_post($url, $args);

    // check error 
    if (is_wp_error($response)) {
        error_log('Eitaa API Error: ' . $response->get_error_message());
        return false;
    }

    // retrieve response code and body from API
    $http_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    // set API response log
    error_log('Eitaa API Response: ' . $response_body);

    return $http_code == 200;
}
?>
