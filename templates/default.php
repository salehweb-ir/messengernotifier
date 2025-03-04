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
    	if (!isset($_POST['messengernotifier_nonce']) || !wp_verify_nonce(wp_unslash($_POST['messengernotifier_nonce']), 'messengernotifier_nonce_action')) {        			wp_die(esc_html__('Security check failed.', 'messengernotifier')); // 
    	}
        // get message from form
        $message = sanitize_text_field(wp_unslash($_POST['message']));

        // get token and ID from options
        $token = get_option('token_eitaa_api');
        $channel_id = get_option('eitaa_channel_id');

        // send message to Eitaa
        $send_result = messengernotifier_send_text_message($token, $channel_id, $message);

        if ($send_result) {
			echo '<div class="notice notice-success is-dismissible"><p>';
			esc_html_e('Message sent successfully.', 'messengernotifier');
			echo '</p></div>';
        } else {
			echo '<div class="notice notice-error is-dismissible"><p>';
			esc_html_e('Failed to send message. please try again later or contact site admin.', 'messengernotifier');
			echo '</p></div>';
        }
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