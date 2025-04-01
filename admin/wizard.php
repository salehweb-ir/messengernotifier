<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
// Include the send message functions
include_once plugin_dir_path(__FILE__) . '../includes/send-message.php';
// display wizard
function messengernotifier_display_wizard() {
    if (isset($_POST['submit'])) {
		if (!isset($_POST['messengernotifier_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['messengernotifier_nonce'])), 'messengernotifier_nonce_action')) {
			wp_die(esc_html__('Security check failed.', 'messengernotifier'));
		}
        // get form fields
		$token      = sanitize_text_field(wp_unslash($_POST['token']));
		$channel_id = sanitize_text_field(wp_unslash($_POST['channel_id']));
		$test_message = sanitize_text_field(wp_unslash($_POST['test_message']));
		$page_title = sanitize_text_field(wp_unslash($_POST['page_title']));
		$page_slug = sanitize_text_field(wp_unslash($_POST['page_slug']));
		$hashtag = __('#Test','messengernotifier');
        // test connection to messenger
        $send_result = messengernotifier_send_text_message($token, $channel_id, $test_message, $hashtag);
        if ($send_result['success']) {
            // save eitaa info in wp options
            update_option('messengernotifier_token_eitaa_api', $token);
            update_option('messengernotifier_eitaa_channel_id', $channel_id);
            // create new page contains template shortcode 
            $page_content = '[messengernotifier_default_template]'; // use shortcode to display default template form
            // check if page doesn't exist
            $page_check = new WP_Query(array(
				'post_type'  => 'page',
				'title'      => $page_title,
				'fields'     => 'ids', // only page ID returns
				'post_status'  => 'publish',
			));
			if (!$page_check->have_posts()) {
				$page_args = array(
					'post_type'		=> 'page',
					'post_title'	=> $page_title,
					'post_name'		=> $page_slug,
					'post_content'	=> $page_content,
					'post_status'	=> 'publish',
					'post_author'	=> get_current_user_id(),
				);
				$page_id = wp_insert_post($page_args);
			}
			    // Store Page ID in WordPress settings
				if ($page_id && !is_wp_error($page_id)) {
					update_option('messengernotifier_pageid', $page_id);
				}
			
            // redirect to setting page with success message
            wp_safe_redirect(admin_url('admin.php?page=messengernotifier&success=true'));
            exit;
        } else {
			echo '<div class="notice notice-error is-dismissible"><p>';
			esc_html($send_result['error']);
			echo '</p></div>';
		}
    }
    ?>

    <div class="messengernotifier-wrap">
        <h1><?php esc_html_e('MessengerNotifier Setup Wizard', 'messengernotifier'); ?></h1>

        <form method="post" class="messengernotifier-wizard-form">
			<?php wp_nonce_field('messengernotifier_nonce_action', 'messengernotifier_nonce'); ?>
            <h2><?php esc_html_e('Step 1: Eitaa API Information', 'messengernotifier'); ?></h2>
			<p>
				<?php 
				printf(
					/* translators: %s: Link to eitaa API documentation */
					esc_html__('Please follow the instructions on %s to get your API token.', 'messengernotifier'),
					sprintf(
						'<a href="https://eitaayar.ir/admin/api" target="_blank">%s</a>',
						esc_html__('eitaa API documentation', 'messengernotifier')
					)
				);
				?>
			</p>
            <label for="token"><?php esc_html_e('Eitaa API Token', 'messengernotifier'); ?></label>
            <input type="text" name="token" id="token" required>

            <label for="channel_id"><?php esc_html_e('Eitaa Channel ID', 'messengernotifier'); ?></label>
            <input type="text" name="channel_id" id="channel_id" required>

            <label for="page_title"><?php esc_html_e('Anonymous message page title', 'messengernotifier'); ?></label>
            <input type="text" name="page_title" id="page_title" required>

            <label for="page_slug"><?php esc_html_e('Anonymous message page slug', 'messengernotifier'); ?></label>
            <input type="text" name="page_slug" id="page_slug" required>
			<span><?php esc_html_e('It is recommended to use Latin words for better readability of the anonymous message link when sharing.'); ?></span>

            <h2><?php esc_html_e('Step 2: Test Connection', 'messengernotifier'); ?></h2>
            <label for="test_message"><?php esc_html_e('Test Message', 'messengernotifier'); ?></label>
            <input type="text" name="test_message" id="test_message" required>

            <input type="submit" name="submit" value="<?php esc_html_e('Test and Save', 'messengernotifier'); ?>" class="messengernotifier-button messengernotifier-button-primary">
        </form>
    </div>

    <?php
}
messengernotifier_display_wizard();
?>