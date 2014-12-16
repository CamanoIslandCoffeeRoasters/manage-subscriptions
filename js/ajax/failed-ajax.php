<?php 

	require( $_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );
	global $woocommerce;
	
	
	$user = $_POST['user'];
	$order_id = $_POST['order_id'];
	$email_content = $_POST['email_content'];
	$disposition = $_POST['disposition'];
	$cancel_reason = $_POST['cancel_reason'];
	
	switch ($disposition) {
		
		case "updated":
			
		break;
			
		case "voicemail":
			
		break;
			
		case "canceled":
			
		break;
			
		default: "";
		break;
		
	}
	
	$_order = new WC_Order($order_id);
	
	$subscription_id = get_post_meta($order_id, "subscription_id", TRUE);
	
	//update_post_meta($order_id, "contact_last", date('Y-m-d'));
	//update_post_meta($order_id, "contact_type", $disposition);
	
	$_order->add_order_note($note_content);
	
	//$_order->update_status('wc-' . $disposition);
	
	echo json_encode(
					array( 
							"order" 			=> $_order, 
							"email_content" 	=> $email_content, 
							"order_id" 			=> $order_id, 
							"disposition" 		=> $disposition, 
							"user"				=> $user,
							"subscription_id"	=> $subscription_id,
							"cancel_reason"		=> $cancel_reason,
						)
					);

?>