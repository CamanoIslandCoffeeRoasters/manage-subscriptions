<?php 

	require( $_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );	
	
     global $woocommerce;
     
     $site_title = get_option('blogname');
     
     $added_by           = isset($_POST['user'])                 ? $_POST['user']              : '';
     $subscription_id    = isset($_POST['subscription_id'])      ? $_POST['subscription_id']   : '';
     $email_content      = isset($_POST['email_content'])        ? $_POST['email_content']     : '';
     $disposition        = isset($_POST['disposition'])          ? $_POST['disposition']       : '';
     $one_time_deduction = isset($_POST['one_time_deduction'])   ? $_POST['one_time_deduction']: '';
     $next_shipment      = isset($_POST['next_shipment'])        ? $_POST['next_shipment']     : '';
     
	$subscription = Subscriptions_Subscribers::get_subscription(NULL, $subscription_id);
	$one_time_deduction += $subscription->one_time_deduction;
	
	// Replace Line Breaks with HTML Breaks
     $email_content = str_replace("\n", "<br />", $email_content);
	// Remove Slashes
     $email_content = str_replace('\\', '', $email_content);


     switch ($disposition) {
          
          case "yes":
               $note_title = "Portal Reactivation";
               $note_content = strtok($subscription->name, " ") . " Reactivated!";
               $note_type = "reactivated";
               $mail_subject = $site_title . ': Account Updated';
               
               Subscriptions_Subscribers::update_subscription($subscription->email, $subscription_id, 'status', 'active' );
               
               if (isset($one_time_deduction)) {
                    Subscriptions_Subscribers::update_subscription($subscription->email, $subscription_id, 'one_time_deduction', $one_time_deduction );
               }
               if (isset($next_shipment)) {
                    Subscriptions_Subscribers::update_subscription($subscription->email, $subscription_id, 'next_shipment', $next_shipment );
               }
			
			$subscription = Subscriptions_Subscribers::get_subscription(NULL, $subscription_id);
			
			// Populate Subscription Type
			$email_content = str_replace('%SUBSCRIPTION_TYPE%', $subscription->subscription_type, $email_content);
			// Populate Frequency
			$email_content = str_replace('%FREQUENCY%', $subscription->frequency, $email_content);
			// Populate Frequency
			$email_content = str_replace('%SHIPDATE%', date('m/d/Y', strtotime($subscription->next_shipment)), $email_content);
			// Populate Discount
			$email_content = str_replace('%DISCOUNT%', $subscription->one_time_deduction, $email_content);
			// Populate Subscription Price
			$discount = Subscriptions_Subscribers::get_subscription_total($subscription->products, $subscription->discount);
			$email_content = str_replace('%SUBSCRIPTION_PRICE%', $discount, $email_content);
			
          break;
               
          case "moreinfo":
               // Notes
               $note_title = "More Info";
               $note_content = "Sent More Info to " . strtok($subscription->name, " ") . " via email.";
               $note_type = "info";
               
               //Email
               $mail_subject = $site_title . ' Info, As Requested';
               
          break;
               
          case "no":
               // Notes
               $note_title = "Reactivation Unsuccessful";
               $note_content = "Unsuccessful reactivation attempt.";
               
               // Email
               $mail_subject = $site_title . '';
               
          break;
                    
          case "remove":
               // Notes
               $note_title = "Removed From Portal";
               $note_content = "" . strtok($subscription->name, " ") . " asked to be removed from the calling list.";
               $note_type = "remove";
               
               Subscriptions_Subscribers::update_subscription($subscription->email, $subscription_id, 'cancel_reason', 'remove');
               // Email
               $mail_subject = $site_title . ': Removed';
               
          break;
                         
          case "noanswer":
               // Notes
               $note_title = "No Answer";
               $note_content = "Unable to get a hold of " . strtok($subscription->name, " ") . "";
               
               // Email
               $mail_subject = $site_title . '';
               
          break;
          
          case "unreachable":
               // Notes
               $note_title = "Unreachable";
               $note_content = "Customer Unreachable.";
               $note_type = "unreachable";
               
          break;
               
          default: $disposition = '';
          break;
     }
     
     Subscriptions_Subscribers::update_subscription($subscription->email, $subscription_id, 'contact_last', current_time('mysql'));
     Subscriptions_Notifications::add_subscription_note($subscription_id, $subscription->email, $note_title, $note_content, $added_by, $note_type);

	echo json_encode($disposition);	
	
     if ($disposition != 'unreachable') {
               
          $mail_headers = "MIME-Version: 1.0\r\n";
          $mail_headers .= "Content-type: text/html; charset=utf-8\r\n";
          $mail_attachments = '';
          
      return wp_mail($subscription->email, $mail_subject, $email_content,$mail_headers, $mail_attachments);
     }

?>