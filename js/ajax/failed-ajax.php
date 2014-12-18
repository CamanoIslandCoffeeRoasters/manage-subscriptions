<?php 

	require( $_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );
	global $woocommerce;
	
	
	$user 			= isset($_POST['user']) 			? $_POST['user']			: '';
	$order_id 		= isset($_POST['order_id']) 		? $_POST['order_id']		: '';
	$email_content 	= isset($_POST['email_content']) 	? $_POST['email_content']	: '';
	$disposition 	= isset($_POST['disposition']) 		? $_POST['disposition']		: '';
	$cancel_reason 	= isset($_POST['cancel_reason']) 	? $_POST['cancel_reason']	: '';
	
	switch ($disposition) {
		
		case "updated":
			
			$_order = new WC_Order($order_id);
			
			update_post_meta($order_id, "contact_last", date('Y-m-d'));
			update_post_meta($order_id, "contact_type", $disposition);
			
			$_order->add_order_note($email_content);
			
			$_order->update_status('wc-cancelled');
			
			$message = json_encode(
								array( 
									"order" 			=> $_order, 
									"email_content" 	=> $email_content, 
									"order_id" 			=> $order_id, 
									"disposition" 		=> $disposition, 
									"user"				=> $user,
								)
							);
		break;
			
		case "voicemail":
			
			$_order = new WC_Order($order_id);
			
			update_post_meta($order_id, "contact_last", date('Y-m-d'));
			update_post_meta($order_id, "contact_type", $disposition);
			
			$_order->add_order_note($email_content);
			
			$message = json_encode(
					array( 
						"order" 			=> $_order, 
						"email_content" 	=> $email_content, 
						"order_id" 			=> $order_id, 
						"disposition" 		=> $disposition, 
						"user"				=> $user,
					)
				);
			
			break;
			
		case "canceled":
			
			$_order = new WC_Order($order_id);
			
			if ($subscription_id = get_post_meta($order_id, "subscription_id", TRUE) ) :
			
				Subscriptions_Subscribers::update_susbscription($_order->billing_email, $subscription_id, 'status', $disposition);
				Subscriptions_Subscribers::update_susbscription($_order->billing_email, $subscription_id, 'cancel_reason', $cancel_reason);
				Subscriptions_Subscribers::update_susbscription($_order->billing_email, $subscription_id, 'cancel_date', date('Y-m-d'));
			
			endif;
			
						$message = json_encode(
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
			
		break;
			
		default: "";
		break;
	}
	
	echo $message;
	
	

?>