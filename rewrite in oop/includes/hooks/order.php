<?php
namespace MessengerNotifier\Hooks;

use MessengerNotifier\API\Eitaa;
use WC_Order;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class OrderHook {
    public function __construct() {
        add_action('woocommerce_payment_complete', [$this, 'send_order_notification']);
    }

    public function send_order_notification($order_id) {
        // Ensure Eitaa API is enabled
        if (get_option('messengernotifier_eitaa_enabled') !== 'yes') {
            return;
        }

        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }

        // Extract order details
        $customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
        $total_price   = strip_tags(wc_price($order->get_total()));
        $order_status  = wc_get_order_status_name($order->get_status());
        $order_link    = admin_url("admin.php?page=wc-orders&action=edit&id={$order_id}");

        // Construct message
        $message  = "🛒 " . __('New order received!', 'messengernotifier') . "\n";
        $message .= "🔢 " . __('Order ID:', 'messengernotifier') . " {$order_id}\n";
        $message .= "👤 " . __('Customer:', 'messengernotifier') . " {$customer_name}\n";
        $message .= "💰 " . __('Total amount:', 'messengernotifier') . " {$total_price}\n";
        $message .= "📌 " . __('Order status:', 'messengernotifier') . " {$order_status}\n";
        $message .= "🔗 " . __('Order Link:', 'messengernotifier') . " {$order_link}\n";

        // Send to Eitaa
        $eitaa = new Eitaa();
        $eitaa->send_text_message($message, '#new_order');
    }
}

// Initialize the hook
new OrderHook();
?>
