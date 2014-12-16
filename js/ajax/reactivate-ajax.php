<?php 

	require( $_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );
	
	$note_content = $_POST['note_content'];
	$note_author = $_POST['note_author'];
	$subscription_id = $_POST['subscription_id'];
	$next_shipment = $_POST['next_shipment'];
	$status = "active";
	
	$subscription = Subscriptions_Subscribers::get_subscription(NULL, $subscription_id);
	
	
	echo json_encode($subscription);

?>