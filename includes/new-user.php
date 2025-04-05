<?php
function myplugin_get_clean_registration_data($post_data) {
    $sensitive_keys = [
        'user_pass', 'pass1', 'pass2',
        '_wpnonce', '_wp_http_referer',
        'woocommerce-register-nonce', 'digits_otp', 'digits_password',
        'security_answer', 'credit_card', 'payment_method',
    ];

    foreach ($post_data as $key => $value) {
        if (stripos($key, 'pass') !== false || stripos($key, 'nonce') !== false || in_array($key, $sensitive_keys)) {
            unset($post_data[$key]);
        } elseif (is_array($value)) {
            $post_data[$key] = myplugin_get_clean_registration_data($value);
        } else {
            $post_data[$key] = sanitize_text_field($value);
        }
    }

    return $post_data;
}
add_action('user_register', 'myplugin_send_registration_data_to_api', 20, 1);

function myplugin_send_registration_data_to_api($user_id) {
    if (empty($_POST)) return;

    $user = get_userdata($user_id);
    $clean_data = myplugin_get_clean_registration_data($_POST);

    // Optionally include basic user data
    $payload = [
        'user_id'    => $user_id,
        'user_login' => $user->user_login,
        'user_email' => $user->user_email,
        'form_data'  => $clean_data,
    ];

    $response = wp_remote_post('https://your-api-endpoint.com/register', [
        'method'  => 'POST',
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer YOUR_API_TOKEN', // optional
        ],
        'body'    => wp_json_encode($payload),
        'timeout' => 15,
    ]);

    if (is_wp_error($response)) {
        error_log('API request failed: ' . $response->get_error_message());
    } else {
        error_log('Registration data sent to API successfully.');
    }
}
?>