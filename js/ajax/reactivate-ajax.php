<?php 

	require( $_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );	
	
     global $woocommerce;
     
     $site_title = get_option('blogname');
     
     $user               = isset($_POST['user'])                 ? $_POST['user']              : '';
     $subscription_id    = isset($_POST['subscription_id'])      ? $_POST['subscription_id']   : '';
     $email_content      = isset($_POST['email_content'])        ? $_POST['email_content']     : '';
     $disposition        = isset($_POST['disposition'])          ? $_POST['disposition']       : '';
     $one_time_deduction = isset($_POST['one_time_deduction'])   ? $_POST['one_time_deduction']: '';
     $next_shipment      = isset($_POST['next_shipment'])        ? $_POST['next_shipment']     : '';
	$subscription = Subscriptions_Subscribers::get_subscription(NULL, $subscription_id);
	$one_time_deduction += $subscription->one_time_deduction;
     $added_by = $user;
	
	// Replace Line Breaks with HTML Breaks
     $email_content = str_replace("\n", "<br />", $email_content);
	// Remove Slashes
     $email_content = str_replace('\\', '', $email_content);
	
     
     
     switch ($disposition) {
          
          case "yes":
               $note_title = "Portal Reactivation";
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
               $note_title = "More Info";
               $mail_subject = $site_title . ' Info, As Requested';
               
          break;
               
          case "no":
               $note_title = "Reactivation Unsuccessful";
               $mail_subject = $site_title . '';
               
          break;
                    
          case "remove":
               $note_type = "remove";
               $note_title = "Removed From Portal";
               $mail_subject = $site_title . ': Removed';
               
          break;
                         
          case "noanswer":
               $note_title = "No Response";
               $mail_subject = $site_title . '';
               
          break;
          
          case "unreachable":
               $note_title = "Unreachable";
               $mail_subject = $site_title . '';
               
          break;
               
          default: $disposition = '';
          break;
     }
	
	$note_content = $email_content;
     Subscriptions_Notifications::add_subscription_note($subscription_id, $subscription_email, $note_title, $note_content, $added_by, $note_type);

	echo json_encode($disposition);	
	
     if ($disposition != 'unreachable') {
               
          $mail_headers = "MIME-Version: 1.0\r\n";
          $mail_headers .= "Content-type: text/html; charset=utf-8\r\n";
          $mail_attachments = '';
          
          return wp_mail($subscription->email, $mail_subject, $email_content,$mail_headers, $mail_attachments);
     }

?>