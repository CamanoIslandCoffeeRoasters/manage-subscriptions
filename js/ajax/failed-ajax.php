<?php

	require( $_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );
	global $woocommerce;
	$mailer = WC()->mailer();

	$site_title = htmlspecialchars_decode(get_option('blogname'));
	$user 			= isset($_POST['user']) 			? $_POST['user']			: '';
	$order_id 		= isset($_POST['order_id']) 		? $_POST['order_id']		: '';
	$email_content 	= isset($_POST['email_content']) 	? $_POST['email_content']	: '';
	$disposition 	= isset($_POST['disposition']) 		? $_POST['disposition']		: '';
	$cancel_reason 	= isset($_POST['cancel_reason']) 	? $_POST['cancel_reason']	: '';
	$contact_callback	= isset($_POST['contact_callback']) 	? $_POST['contact_callback']	: '';

	// Replace inlne line breaks with HTML line breaks for display in email
	$email_content = str_replace("\n", "<br />", $email_content);

	// Delete callback post meta
	delete_post_meta($order_id, 'contact_callback');

	// Check the number of times this person has been contacted
	$contact_amount = get_post_meta($order_id, "contact_amount", TRUE) ? get_post_meta($order_id, "contact_amount", TRUE) : 0 ;
	$contact_amount++;
	$_order = new WC_Order($order_id);

	switch ($disposition) {

		case "updated":

			$_order->add_order_note("Updated Card, $user");
			$_order->update_status('wc-cancelled');

			$message = json_encode( array( "order_id" => $order_id, "contact_amount" => $contact_amount) );

			$mail_email = $_order->billing_email;
			$mail_subject = $site_title . ' Account Updated';
		break;

		case "voicemail":

			$_order->add_order_note("Voicemail, $user");

			$message = json_encode( array( "order_id" 	=> $order_id ) );

			$mail_email = $_order->billing_email;
			$mail_subject = $site_title . ' Voicemail';

		break;

		case "canceled":

			$note_title = 'Updated Status from call portal.';
			$note_content = 'Status changed to canceled. Canceled because of ' . $cancel_reason;
			$added_by = $user;
            $note_type = 'portal';

			if ($subscription_id = get_post_meta($order_id, "subscription_id", TRUE) ) :

				Subscriptions_Subscribers::cancel_subscription( $subscription_id, $cancel_reason );
				Subscriptions_Notifications::add_subscription_note($subscription_id, $_order->billing_email, $note_title, $note_content, $added_by, $note_type );

			endif;

			$_order->update_status('wc-cancelled');

			$message = json_encode( array( "order_id" => $order_id ) );

			$mail_email = $_order->billing_email;
			$mail_subject = $site_title . ' Subscription Canceled';
		break;

		case "email":
			$message = json_encode(array("email"	=> "emailed!" ));

			$mail_email = $_order->billing_email;
			$mail_subject = $site_title . ': Response Requested';
		break;

		case "callback":
			$contact_callback = date('Y-m-d', strtotime($contact_callback));
			update_post_meta($order_id, "contact_callback", $contact_callback );

			$message = json_encode(array("email"	=> "emailed!" ));

			$mail_email = $_order->billing_email;
			$mail_subject = $site_title;
		break;

		case "remove":

			$_order->add_order_note("Removed from Portal");
			add_post_meta($order_id, 'portal_remove', 'true');
			$message = json_encode(array("removed"	=> "remove" ));
		break;

		case "unreachable":

			$_order->add_order_note("Unreachable for contact from $user");

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
	if (!in_array($disposition, array('unreachable', 'remove')) ) :

	   $greeting = (current_time('H') > 12) ? "Good Afternoon " : "Good Morning ";
	   $mailer->send( $mail_email, $mail_subject, $mailer->wrap_message("<img src='http://camanoislandcoffee.com/wp-content/uploads/2014/06/CICR-Logo-Color1.png' style='width:175px;margin-left:auto;margin-right:auto;'/>", '<h2> ' .  $greeting . ucwords($_order->billing_first_name) . ',</h2><br /><br />' .  $email_content ), '', '' );
	endif;





?>
