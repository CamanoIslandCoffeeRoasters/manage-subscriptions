<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<?php

global $wpdb;

$manage_subscriptions = get_option("manage_subscriptions_{$this->user_id}");
$portal_types = $this->init_portal_types;
$navigations = $this->init_navigations;
?>

<div id="manage_subscription_options">
    <div class="wrap">
        <h2>Manage Subscriptions</h2>
        <div id='message' class='updated fade hidden'><p><strong></strong></p></div>
        <form method="POST" action="options.php">
           <?php settings_fields( 'manage_subscriptions_group' ); ?>
           <?php do_settings_sections( 'manage_subscriptions_group' ); ?>
            <table class="form-table">
                <tbody>
                    <tr>
                         <th scope="row">
                              <label for="contact_last_order">Days Since Last Contact on Failed Order</label>
                         </th>
                         <td>
                              <input id="contact_last_order" name="manage_subscriptions_<?php echo $this->user_id ?>[contact_last_order]" type="number" min="1" max="100" value="<?php echo $manage_subscriptions['contact_last_order'] ?>" required="required" />
                         </td>
                    </tr>
                    <tr>
                         <th scope="row">
                              <label for="contact_last_subscription">Days Since Last Contact on Canceled Subscription</label>
                         </th>
                         <td>
                              <input id="contact_last_subscription" name="manage_subscriptions_<?php echo $this->user_id ?>[contact_last_subscription]" type="number" min="1" max="500" value="<?php echo $manage_subscriptions['contact_last_subscription'] ?>" required="required" />
                         </td>
                    </tr>
                    <tr>
                         <th scope="row">
                              <label for="cancel_date">Days Since Canceled Subscription</label>
                         </th>
                         <td>
                              <input id="cancel_date" name="manage_subscriptions_<?php echo $this->user_id ?>[cancel_date]" type="number" min="1" max="500" value="<?php echo $manage_subscriptions['cancel_date'] ?>" required="required" />
                         </td>
                    </tr>
                    <tr>
                         <th scope="row">
                              <label for="num_rows">Number of Rows to Display</label>
                         </th>
                         <td>
                              <input id="num_rows" name="manage_subscriptions_<?php echo $this->user_id ?>[num_rows]" type="number" min="1" max="200" value="<?php echo $manage_subscriptions['num_rows'] ?>" required="required" />
                         </td>
                    </tr>
                    <tr>
                         <th scope="row">
                              <label for="portal_types">Portal Types to Display</label>
                         </th>
                         <td>
                             <?php foreach ($portal_types as $portal_type => $portal_name) { ?>
                                <input id="portal_types_<?php echo $portal_type?>"
                                name="manage_subscriptions_<?php echo $this->user_id ?>[portal_types][<?php echo $portal_type ?>]"
                                type="checkbox"
                                value="<?php echo $portal_name ?>"
                                <?php (isset($manage_subscriptions['portal_types'][$portal_type])) ? checked( $manage_subscriptions['portal_types'][$portal_type], $portal_name, true) : '' ?> />
                                <label for="portal_types_<?php echo $portal_type ?>"><?php echo ucwords($portal_name) ?></label>
                                <br />
                             <?php } ?>
                         </td>
                    </tr>
                    <tr>
                         <th scope="row">
                              <label for="navigation">Navigation Types to Display</label>
                         </th>
                         <td>
                             <?php foreach ($navigations as $navigation => $navigation_name) { ?>
                                <input id="navigation_<?php echo $navigation?>"
                                name="manage_subscriptions_<?php echo $this->user_id ?>[navigations][<?php echo $navigation ?>]"
                                type="checkbox"
                                value="<?php echo $navigation_name ?>"
                                <?php (isset($manage_subscriptions['navigations'][$navigation])) ? checked($manage_subscriptions['navigations'][$navigation], $navigation_name, true) : '' ?> />
                                <label for="navigation_<?php echo $navigation ?>"><?php echo ucwords($navigation_name) ?></label>
                                <br />
                             <?php } ?>
                         </td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
</div>
