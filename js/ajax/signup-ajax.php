<?php 

    require( $_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );
    global $woocommerce;
    
    $site_title = get_option('blogname');
    $user           = isset($_POST['user'])             ? $_POST['user']            : '';
    $order_id       = isset($_POST['order_id'])         ? $_POST['order_id']        : '';
    $subscription_id= isset($_POST['subscription_id'])  ? $_POST['subscription_id']        : '';
    $email_content  = isset($_POST['email_content'])    ? $_POST['email_content']   : '';

    // Replace inlne line breaks with HTML line breaks for display in email
    $email_content = str_replace("\n", "<br />", $email_content);
    
    $subscription = Subscriptions_Subscribers::get_subscription("", $subscription_id);

    update_post_meta($order_id, "contact_new_signup", "true");
    
    $note_title = "This is a new signup!";
    $note_content = $email_content;
    $added_by = $user;
    $note_type = "contact_new_signup";
    
    Subscriptions_Notifications::add_subscription_note($subscription_id, $subscription->email, $note_title, $note_content, $added_by, $note_type);
    
    $message = json_encode( array( "order_id" => $order_id));
    
    echo $message;
    
?>