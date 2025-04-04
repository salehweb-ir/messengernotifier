<?php
namespace MessengerNotifier\Hooks;

use MessengerNotifier\API\Eitaa;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CommentHook {
    public function __construct() {
        add_action('comment_post', [$this, 'send_new_comment_notification'], 10, 2);
    }

    public function send_new_comment_notification($comment_ID, $comment_approved) {
        // Ensure Eitaa API is enabled
        if (get_option('messengernotifier_eitaa_enabled') !== 'yes') {
            return;
        }

        // Get comment & post details
        $comment = get_comment($comment_ID);
        $post = get_post($comment->comment_post_ID);

        // Localized Status Mappings with Emojis
        $status_map = [
            1     => "✅ " . __("Approved", "messengernotifier"),
            0     => "⏳ " . __("Pending Moderation", "messengernotifier"),
            'spam' => "🚫 " . __("Marked as Spam", "messengernotifier")
        ];

        // Get Status Text
        $status_text = $status_map[$comment_approved] ?? "❓ " . __("Unknown Status", "messengernotifier");

        // Construct Message
        $message  = "🆕💬 " . sprintf(__("New Comment on: %s", "messengernotifier"), esc_html($post->post_title)) . "\n";
        $message .= "👤 " . sprintf(__("Author: %s", "messengernotifier"), esc_html($comment->comment_author)) . "\n";
        $message .= "📌 " . sprintf(__("Status: %s", "messengernotifier"), $status_text) . "\n\n";
        $message .= "💬 " . __("Comment:", "messengernotifier") . "\n" . esc_html($comment->comment_content) . "\n\n";
        $message .= "🔗 " . sprintf(__("View Comment: %s", "messengernotifier"), get_comment_link($comment_ID)) . "\n\n";

        // Add Moderation Actions if Pending
        if ($comment_approved == 0) {
            $message .= "⚠️ " . __("This comment is awaiting moderation.", "messengernotifier") . "\n";
            $message .= "✅ " . sprintf(__("Approve: %s", "messengernotifier"), admin_url("comment.php?action=approve&c={$comment_ID}")) . "\n";
            $message .= "🚫 " . sprintf(__("Mark as Spam: %s", "messengernotifier"), admin_url("comment.php?action=spam&c={$comment_ID}")) . "\n";
            $message .= "🗑️ " . sprintf(__("Trash: %s", "messengernotifier"), admin_url("comment.php?action=trash&c={$comment_ID}")) . "\n\n";
        }

        // Send Message to Eitaa
        $eitaa = new Eitaa();
        $eitaa->send_text_message($message, '#comment');
    }
}

// Initialize the hook
new CommentHook();
?>
