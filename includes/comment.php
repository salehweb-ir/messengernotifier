<?php
add_action('comment_post', function($comment_ID, $comment_approved) {
    $comment = get_comment($comment_ID);
    $post = get_post($comment->comment_post_ID);
    $admin_email = get_option('admin_email');

    // Define localized status mappings
    $status_map = [
        1     => __("Approved", "text-domain"),
        0     => __("Pending Moderation", "text-domain"),
        'spam' => __("Marked as Spam", "text-domain")
    ];

    // Get status text with localization
    $status_text = $status_map[$comment_approved] ?? __("Unknown Status", "text-domain");

    // Email content
    $subject = sprintf(__("New Comment on: %s", "text-domain"), $post->post_title);
    $message  = __("A new comment has been posted on your site.", "text-domain") . "\n\n";
    $message .= sprintf(__("Author: %s", "text-domain"), $comment->comment_author) . "\n";
    $message .= sprintf(__("Status: %s", "text-domain"), $status_text) . "\n\n";
    $message .= __("Comment:", "text-domain") . "\n" . $comment->comment_content . "\n\n";
    $message .= sprintf(__("View Comment: %s", "text-domain"), get_comment_link($comment_ID)) . "\n\n";

    // Add moderation actions if comment is pending
    if ($comment_approved == 0) {
        $message .= __("This comment is awaiting moderation.", "text-domain") . "\n";
        $message .= sprintf(__("Approve: %s", "text-domain"), admin_url("comment.php?action=approve&c={$comment_ID}")) . "\n";
        $message .= sprintf(__("Mark as Spam: %s", "text-domain"), admin_url("comment.php?action=spam&c={$comment_ID}")) . "\n";
        $message .= sprintf(__("Trash: %s", "text-domain"), admin_url("comment.php?action=trash&c={$comment_ID}")) . "\n";
    }

    // Send notification to Eitaa
    
}, 10, 2);

?>
