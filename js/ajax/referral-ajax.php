<?php 

	require( $_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );	
	
     global $woocommerce;
     
     $site_title = get_option('blogname');
     
     $added_by           = isset($_POST['user'])                 ? $_POST['user']              : '';
     $subscription_id    = isset($_POST['subscription_id'])      ? $_POST['subscription_id']   : '';
     $email_content      = isset($_POST['email_content'])        ? $_POST['email_content']     : '';
     $disposition        = isset($_POST['disposition'])          ? $_POST['disposition']       : '';
     
     $subscription = Subscriptions_Subscribers::get_subscription(NULL, $subscription_id);
	
	 // Replace Line Breaks with HTML Breaks
     $email_content = str_replace("\n", "<br />", $email_content);
	 // Remove Slashes
     $email_content = str_replace('\\', '', $email_content);


     switch ($disposition) {
          
          case "note":
          case "email":
                $note_title = "Portal Referral";
                $note_content = "Contacted " . strtok($subscription->name, " ") . " about Referrals";
                $note_type = "referral";
                $mail_subject = $site_title . " Referrals";
                Subscriptions_Notifications::add_subscription_note($subscription_id, $subscription->email, $note_title, $note_content, $added_by, $note_type);
          break;
               
          case "none":
          break;
                    
          case "remove":
               // Notes
               $note_title = "Removed From Portal";
               $note_content = "" . strtok($subscription->name, " ") . " asked to be removed from the calling list.";
               $note_type = "remove";
               Subscriptions_Notifications::add_subscription_note($subscription_id, $subscription->email, $note_title, $note_content, $added_by, $note_type);
               Subscriptions_Subscribers::update_subscription($subscription->email, $subscription_id, 'cancel_reason', 'remove');
               
          break;
               
          default: $disposition = '';
          break;
     }
     
     if ($disposition != "none") {
        Subscriptions_Subscribers::update_subscription($subscription->email, $subscription_id, 'contact_last', current_time('mysql'));
        Subscriptions_Subscribers::add_subscription_meta($subscription_id, 'referral_contact', "true");
     }

	echo json_encode($disposition);	
	
     if ($disposition == 'email') {
               
          $mail_headers = "MIME-Version: 1.0\r\n";
          $mail_headers .= "Content-type: text/html; charset=utf-8\r\n";
          $mail_attachments = '';
          
      return wp_mail($subscription->email, $mail_subject, $email_content,$mail_headers, $mail_attachments);
     }

?>