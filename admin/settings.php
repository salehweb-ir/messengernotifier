<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Retrieve saved token and channel ID from options
$messengernotifier_token = get_option('messengernotifier_token_eitaa_api');
$messengernotifier_eitaa_channel_id = get_option('messengernotifier_eitaa_channel_id');
$messengernotifier_page_id = get_option('messengernotifier_pageid');
$page_url = $messengernotifier_page_id ? get_permalink($messengernotifier_page_id) : ''; 

// Check for success message
if (isset($_GET['success']) && $_GET['success'] == 'true') {
    echo '<div class="notice notice-success is-dismissible"><p>';
	esc_html_e('Configuration saved and form created successfully!', 'messengernotifier');
	echo '</p></div>';
}
?>

<div class="messengernotifier wrap">
    <h1><?php esc_html_e('MessengerNotifier Settings', 'messengernotifier'); ?></h1>

    <form method="post" action="options.php">
        <?php settings_fields('messengernotifier_options_group'); ?>
        <?php do_settings_sections('messengernotifier'); ?>
        <table class="messengernotifier form-table">
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Eitaa API Token', 'messengernotifier'); ?></th>
                <td><input type="text" name="messengernotifier_token_eitaa_api" value="<?php echo esc_attr($messengernotifier_token); ?>" readonly /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Eitaa Channel ID', 'messengernotifier'); ?></th>
                <td><input type="text" name="messengernotifier_eitaa_channel_id" value="<?php echo esc_attr($messengernotifier_eitaa_channel_id); ?>" readonly /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Anonymous message page link', 'messengernotifier'); ?></th>
                <td>
					<input type="text" name="messengernotifier_page_link" value="<?php echo esc_url($page_url); ?>" readonly />
					<?php if ($page_url) : ?>
						<p><a href="<?php echo esc_url($page_url); ?>" target="_blank"><?php esc_html_e('View Page', 'messengernotifier'); ?></a></p>
					<?php else : ?>
						<p style="color: red;"><?php esc_html_e('Page not found!', 'messengernotifier'); ?></p>
					<?php endif; ?>
				</td>
            </tr>
        </table>
    </form>
</div>
