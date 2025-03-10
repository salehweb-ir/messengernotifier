<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include the send message functions from the includes folder
include_once plugin_dir_path(__DIR__) . '/includes/send-message.php';

// display form
function messengernotifier_display_form() {
    if (isset($_POST['submit'])) {
		
		// check nonce before processing form
    	if (!isset($_POST['messengernotifier_nonce']) || 
			!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['messengernotifier_nonce'])), 'messengernotifier_nonce_action')) {
			wp_die(esc_html__('Security check failed.', 'messengernotifier'));
		}

        // get message from form
        $message = sanitize_text_field(wp_unslash($_POST['message']));

        // get token and ID from options
        $token = get_option('messengernotifier_token_eitaa_api');
        $channel_id = get_option('messengernotifier_eitaa_channel_id');
		$hashtag = __('#Message','messengernotifier');

        // send message to Eitaa
        $send_result = messengernotifier_send_text_message($token, $channel_id, $message ,$hashtag);

        if ($send_result['success']) {
			echo '<div class="notice notice-success is-dismissible"><p>';
        } else {
			echo '<div class="notice notice-error is-dismissible"><p>';
        }
			esc_html($send_result['error']);
			echo '</p></div>';
    }
    ?>

    <div class="messengernotifier-page-content">
        <h1><?php esc_html_e('Submit Your Message', 'messengernotifier'); ?></h1>
        <form method="post" class="messengernotifier-form" enctype="multipart/form-data">
			<?php wp_nonce_field('messengernotifier_nonce_action', 'messengernotifier_nonce'); ?>
            <label for="messengernotifier-message"><?php esc_html_e('Your Message', 'messengernotifier'); ?></label>
            <textarea id="messengernotifier-message" name="message" required></textarea>
            <input type="submit" name="submit" value="<?php esc_html_e('Submit', 'messengernotifier'); ?>" class="messengernotifier-button messengernotifier-button-primary">
        </form>
    </div>

    <?php
}

messengernotifier_display_form();
?>