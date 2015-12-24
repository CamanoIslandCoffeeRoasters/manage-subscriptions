<?php

    require $_SERVER['DOCUMENT_ROOT'] . "/wp-load.php";

    $user_id = $_GET['user_id'];

    $portal_types = array("reactivate", "failed", "expired", "referral", "signup" );

    foreach ($portal_types as $type) {

        $locked_options = array();

        $locked_options = get_option("manage_subscriptions_locked_{$type}");

        unset($locked_options[$user_id]);

        update_option("manage_subscriptions_locked_{$type}", $locked_options);

    }

?>
