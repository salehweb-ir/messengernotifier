<?php
add_action( 'woocommerce_payment_complete', 'messengernotifier_send_order_to_eitaa' );

function messengernotifier_send_order_to_eitaa( $order_id ) {
    
    // get token and ID from options
	$token = get_option('messengernotifier_token_eitaa_api');
	$channel_id = get_option('messengernotifier_eitaa_channel_id');
	
	$order = wc_get_order( $order_id );
	$messengernotifier_hashtag = __('#new_order','messengernotifier');

	if ( ! $order ) {
    messengernotifier_send_text_message( $token, $channel_id, $order, $messengernotifier_hashtag);
        return;
    }

	$customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();

    $message = "🛒 " . __( 'New order received!', 'messengernotifier' ) . "\n";
    $message .= "🔢 " . __( 'Order ID:', 'messengernotifier' ) . " {$order_id}\n";
    $message .= "👤 " . __( 'Customer:', 'messengernotifier' ) . " {$customer_name}\n";
    
    $total_price    = strip_tags( wc_price( $order->get_total() ) );
    $order_status   = wc_get_order_status_name( $order->get_status() );

    $order_link = admin_url( "admin.php?page=wc-orders&action=edit&id={$order_id}" );

    $message .= "💰 " . __( 'Total amount:', 'messengernotifier' ) . " {$total_price}\n";
    $message .= "📌 " . __( 'Order status:', 'messengernotifier' ) . " {$order_status}\n";
    $message .= "🔗 " . __( 'Order Link:', 'messengernotifier' ) . " {$order_link}\n";
	
    messengernotifier_send_text_message( $token, $channel_id, $order, $messengernotifier_hashtag);
}
?>