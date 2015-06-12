<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<?php 

global $wpdb;


$manage_subscriptions = get_option('manage_subscriptions');
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
                              <input id="contact_last_order" name="manage_subscriptions[contact_last_order]" type="number" min="1" max="100" value="<?php echo $manage_subscriptions['contact_last_order'] ?>" required="required" />
                         </td>
                    </tr>
                    <tr>
                         <th scope="row">
                              <label for="contact_last_subscription">Days Since Last Contact on Canceled Subscription</label>
                         </th>
                         <td>
                              <input id="contact_last_subscription" name="manage_subscriptions[contact_last_subscription]" type="number" min="1" max="100" value="<?php echo $manage_subscriptions['contact_last_subscription'] ?>" required="required" />
                         </td>
                    </tr>                     
                    <tr>
                         <th scope="row">
                              <label for="cancel_date">Days Since Canceled Subscription</label>
                         </th>
                         <td>
                              <input id="cancel_date" name="manage_subscriptions[cancel_date]" type="number" min="1" max="500" value="<?php echo $manage_subscriptions['cancel_date'] ?>" required="required" />
                         </td>
                    </tr>
                    <tr>
                         <th scope="row">
                              <label for="num_rows">Number of Rows to Display</label>
                         </th>
                         <td>
                              <input id="num_rows" name="manage_subscriptions[num_rows]" type="number" min="1" max="150" value="<?php echo $manage_subscriptions['num_rows'] ?>" required="required" />
                         </td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
</div>

