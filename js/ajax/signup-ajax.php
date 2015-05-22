<?php 

    require( $_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );
    global $woocommerce;
    
    $site_title = get_option('blogname');
    $user           = isset($_POST['user'])             ? $_POST['user']            : '';
    $order_id       = isset($_POST['order_id'])         ? $_POST['order_id']        : '';
    $subscription_id= isset($_POST['subscription_id'])  ? $_POST['subscription_id'] : '';
    $email_content  = isset($_POST['email_content'])    ? $_POST['email_content']   : '';

    // Replace inlne line breaks with HTML line breaks for display in email
    $email_content = htmlspecialchars($email_content);
    $email_content = str_replace("\n", "<br />", $email_content);
    $subscription = Subscriptions_Subscribers::get_subscription("", $subscription_id);

    update_post_meta($order_id, "contact_new_signup", "true");
    
    $note_title = "New Subscription";
    $note_content = $email_content;
    $added_by = $user;
    $note_type = "contact_new_signup";
    
    Subscriptions_Notifications::add_subscription_note($subscription_id, $subscription->email, $note_title, $note_content, $added_by, $note_type);
    
    $message = json_encode( array( "order_id" => $order_id));
    
    echo $message;
    
    
    // Send Email to Customer
    $mail_email = $subscription->email;
    $mail_subject = $site_title . ' Referrals';
    $mail_message = $email_content;

    $mail_headers = "MIME-Version: 1.0\r\n";
    $mail_headers .= "Content-type: text/html; charset=utf-8\r\n";
    $mail_attachments = '';
    
    return wp_mail( "tobin.fekkes@camanoislandmanagement.com", $mail_subject, $mail_message, $mail_headers, $mail_attachments );

    
?>