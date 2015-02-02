<?php 

	require( $_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );
	global $woocommerce;
	
	$site_title = get_option('blogname');
	$user 			= isset($_POST['user']) 			? $_POST['user']			: '';
	$order_id 		= isset($_POST['order_id']) 		? $_POST['order_id']		: '';
	$email_content 	= isset($_POST['email_content']) 	? $_POST['email_content']	: '';
	$disposition 	     = isset($_POST['disposition']) 	? $_POST['disposition']		: '';
	$cancel_reason 	= isset($_POST['cancel_reason']) 	? $_POST['cancel_reason']	: '';
	
	// Replace inlne line breaks with HTML line breaks for display in email
	$email_content = str_replace("\n", "<br />", $email_content);
	
	// Check the number of times this person has been contacted
	$contact_amount = get_post_meta($order_id, "contact_amount", TRUE) ? get_post_meta($order_id, "contact_amount", TRUE) : 0 ;
	$contact_amount++;
	
	switch ($disposition) {
		
		case "updated":
			
			$_order = new WC_Order($order_id);
			$_order->add_order_note($email_content);
			$_order->update_status('wc-cancelled');
			
			$message = json_encode( array( "order_id" => $order_id, "contact_amount" => $contact_amount) );
							
			$mail_email = $_order->billing_email;
			$mail_subject = $site_title . ' Account Updated';
			$mail_message = $email_content;
								
		break;

		case "voicemail":
			
			$_order = new WC_Order($order_id);
			$_order->add_order_note($email_content);
			
			$message = json_encode( array( "order_id" 	=> $order_id ) );
				
			$mail_email = $_order->billing_email;
			$mail_subject = $site_title . ' Voicemail';
			$mail_message = $email_content;
			
		break;
			
		case "canceled":
			
			$_order = new WC_Order($order_id);
			
			$note_title = 'Updated Status from call portal.';
			$note_content = 'Status changed to canceled. Canceled because of ' . $cancel_reason;
			$added_by = $user;
               $note_type = 'portal';
			
			if ($subscription_id = get_post_meta($order_id, "subscription_id", TRUE) ) :
			
				Subscriptions_Subscribers::update_subscription($_order->billing_email, $subscription_id, 'status', $disposition);
				Subscriptions_Subscribers::update_subscription($_order->billing_email, $subscription_id, 'cancel_reason', $cancel_reason);
				Subscriptions_Subscribers::update_subscription($_order->billing_email, $subscription_id, 'cancel_date', date('Y-m-d'));
				
				Subscriptions_Notifications::add_subscription_note($subscription_id, $_order->billing_email, $note_title, $note_content, $added_by, $note_type );
			
			endif;
			
			$_order->update_status('wc-cancelled');
			
			$message = json_encode( array( "order_id" => $order_id ) );
							
			$mail_email = $_order->billing_email;
			$mail_subject = $site_title . ' Subscription Canceled';
			$mail_message = $email_content;
			
		break;
		
		case "email":
			$message = json_encode(array("email"	=> "emailed!" ));
			
			$_order = new WC_Order($order_id);
			
			$mail_email = $_order->billing_email;
			$mail_subject = $site_title . ': Response Requested';
			$mail_message = $email_content;
		break;
			
		case "unreachable":
			
			$_order = new WC_Order($order_id);
			$_order->add_order_note($email_content);
			
			$message = json_encode(array("unreachable"	=> "unreachable!" ));
		break; 
			
		default: "";
		break;
	}

	update_post_meta($order_id, "contact_last", date('Y-m-d'));
	update_post_meta($order_id, "contact_type", $disposition);
	update_post_meta($order_id, "contact_amount", $contact_amount);
	
	echo $message;
	
	
	// Send Email to Customer
	if ($disposition != "unreachable") :
		
		$mail_headers = "MIME-Version: 1.0\r\n";
		$mail_headers .= "Content-type: text/html; charset=utf-8\r\n";
		$mail_attachments = '';
		
		return wp_mail( $mail_email, $mail_subject, $mail_message, $mail_headers, $mail_attachments );
	endif;
	

?>