<?php
add_action('comment_post', 'messengernotifier_new_comment' , 10, 2);

function messengernotifier_new_comment($comment_ID, $comment_approved) {

// get token and ID from options
	$token = get_option('messengernotifier_token_eitaa_api');
	$channel_id = get_option('messengernotifier_eitaa_channel_id');
    $comment = get_comment($comment_ID);
    $post = get_post($comment->comment_post_ID);

    // Define localized status mappings with emojis
    $status_map = [
        1     => "✅ " . __("Approved", "messengernotifier"),
        0     => "⏳ " . __("Pending Moderation", "messengernotifier"),
        'spam' => "🚫 " . __("Marked as Spam", "messengernotifier")
    ];

    // Get status text with localization
    $status_text = $status_map[$comment_approved] ?? "❓ " . __("Unknown Status", "messengernotifier");

    // Email content with emojis
    $subject = sprintf("🆕💬 " . __("New Comment on: %s", "messengernotifier"), $post->post_title);
    $message  = "🔔 " . __("A new comment has been posted on your site.", "messengernotifier") . "\n\n";
    $message .= "👤 " . sprintf(__("Author: %s", "messengernotifier"), $comment->comment_author) . "\n";
    $message .= "📌 " . sprintf(__("Status: %s", "messengernotifier"), $status_text) . "\n\n";
    $message .= "💬 " . __("Comment:", "messengernotifier") . "\n" . $comment->comment_content . "\n\n";
    $message .= "🔗 " . sprintf(__("View Comment: %s", "messengernotifier"), get_comment_link($comment_ID)) . "\n\n";

    // Add moderation actions if comment is pending
    if ($comment_approved == 0) {
        $message .= "⚠️ " . __("This comment is awaiting moderation.", "messengernotifier") . "\n";
        $message .= "✅ " . sprintf(__("Approve: %s", "messengernotifier"), admin_url("comment.php?action=approve&c={$comment_ID}")) . "\n\n";
        $message .= "🚫 " . sprintf(__("Mark as Spam: %s", "messengernotifier"), admin_url("comment.php?action=spam&c={$comment_ID}")) . "\n\n";
        $message .= "🗑️ " . sprintf(__("Trash: %s", "messengernotifier"), admin_url("comment.php?action=trash&c={$comment_ID}")) . "\n\n";
    }
    
    $messengernotifier_hashtag = __('#comment','messengernotifier');

    // Send notification to Eitaa
    messengernotifier_send_text_message( $token, $channel_id, $message, $messengernotifier_hashtag);
};
?>