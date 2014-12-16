<?php 

global $current_user;
$portal_type = $_GET['portal_type'] ? $_GET['portal_type'] : "reactivate";

						
if ( !empty($current_user->roles) ) : 
    if ( ($current_user->roles[0] == 'administrator') ) : 
        echo "Greetings and Salutations, " . $current_user->data->display_name;
    ?>

    
<div id="subscriptions_container">

	<div class="threecol-one <?php echo ($portal_type == 'reactivate') ? 'portal_type': 'subscriptions-nav' ?>">
		<a href="//camanoislandcoffee.com/portal/?portal_type=reactivate">Reactivations</a>
	</div>
	<div class="threecol-one <?php echo ($portal_type == 'failed') ? 'portal_type': 'subscriptions-nav' ?>">
		<a href="//camanoislandcoffee.com/portal/?portal_type=failed">Failed Orders</a>
	</div>
	<div class="threecol-one last <?php echo ($portal_type == 'expired') ? 'portal_type': 'subscriptions-nav' ?>">
		<a href="//camanoislandcoffee.com/portal/?portal_type=expired">Expired Cards</a>
	</div>

	<hr /><hr />


	<div class="fourcol-one call-flow" id="customer">
		<h4>Customers</h4>
	</div>
	<div class="fourcol-one call-flow" id="script">
		<h4>Script</h4>
	</div>
	<div class="fourcol-one call-flow" id="order">
		<h4>Order</h4>	
	</div>
	<div class="fourcol-one last call-flow" id="survey">
		<h4>Survey</h4>	
	</div>
	
	
	<div id="customer_content">
		<h4></h4>
		<?php echo get_customers($portal_type); ?>
	</div>
	
	<div id="script_content" style="display:none;">
		<h4>Here is a script to read</h4>
		<?php echo get_script($portal_type); ?>
	</div>
	
	<div id="order_content" style="display:none;">
		<h4>Here is where you can place an order</h4>
		<?php echo get_orders($portal_type); ?>
	</div>
	
	<div id="survey_content" style="display:none;">
		<h4>Here is where you can submit a survey</h4>
		<?php echo get_survey($portal_type); ?>
	</div>

</div>
<?php 
	else: echo "You do not have sufficient permissions to view this page";
	endif;
else: echo "You must be logged in to view this page. ";
echo '<a href="http://camanoislandcoffee.com/wp-login.php?redirect_to=http%3A%2F%2Fcamanoislandcoffee.com%2Fportal">Login here</a>';
endif;

?>

<?php
	
	// Display four areas of data
	function get_customers($portal_type = "") {
		
		$portal_table = $portal_type . '_table';
		$table = $portal_table($portal_type);
		return $table;
		
	}
	
	function get_script($portal_type = "") {
		echo "Hello Script!";
	}
	
	function get_orders($portal_type = "") {
		echo "Hello Orders!";
	}
	
	function get_survey($portal_type = "") {
		echo "Hello Survey!";
	}
	
	function reactivate_table($portal_type) {
		?>
		<table class="portal_table" width="100%" cellpadding="3" cellspacing="4">
			<thead>
				<th>Sub ID</th>
				<th>Name</th>
				<th>Email</th>
				<th>Phone</th>
				<th>Cancel Date</th>
				<th>Cancel Reason</th>
				<th>Actions</th>
			</thead>
			<tbody>
			<?php
		
			$subscription_ids = get_data($portal_type);
	
			foreach ($subscription_ids as $subscription_id) :
				$subscription = Subscriptions_Subscribers::get_subscription(NULL, $subscription_id);
				$user = get_user_by('email', $subscription->email);
				$user_billing_phone = str_replace(array('-', '.', ' '), '', get_user_meta($user->ID, 'billing_phone', TRUE));
		
			?>
				<tr>
					<td id="sub_name"><a href="<?php echo get_option('siteurl') ?>/wp-admin/admin.php?page=edit-subscription&user=<?php echo $subscription->email ?>&subscription_id=<?php echo $subscription->subscription_id ?>" target="_blank"><?php echo $subscription->subscription_id ?></a></td>
					<td id="sub_name"><?php echo $subscription->name ?></td>
					<td class="sub_email" id="sub_email"><?php echo $subscription->email ?></td>
					<td id="sub_phone"><span onclick="this.select();"><?php echo $user_billing_phone ?></span></td>
					<td id="sub_cancel_date"><?php echo $subscription->cancel_date ?></td>
					<td id="sub_cancel_reason"><?php echo $subscription->cancel_reason ?></td>
					<td class="reactivate" id="<?php echo $subscription_id ?>"><a href"">Reactivate</a></td>
					
				</tr>
				
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		
	}
	
	function failed_table($portal_type) {
		$order_ids = get_data($portal_type);
		global $woocommerce, $wpdb;
		
		?>
		<table class="portal_table" width="100%" cellpadding="3" cellspacing="4">
			<thead>
				<th>Order #</th>
				<th>Name</th>
				<th>Email</th>
				<th>Phone</th>
				<th>Order Date</th>
				<th>Failed Card</th>
				<th>Actions</th>
			</thead>
			<tbody>
			<?php
	
			foreach ($order_ids as $order_id) :
				
				$_order = new WC_Order($order_id);
				
				if ($wpdb->get_results("SELECT comment_content FROM {$wpdb->comments} WHERE comment_post_ID = $order_id AND comment_content LIKE '%declined%'")) {
					$failed_reason = "declined";
				}elseif ($wpdb->get_results("SELECT comment_content FROM {$wpdb->comments} WHERE comment_post_ID = $order_id AND comment_content LIKE '%expired%'")) {
					$failed_reason = "expired";
				}
		
			?>
				<tr>
					<td id="sub_name"><a href="<?php echo get_option('siteurl') ?>/wp-admin/post.php?action=edit&post=<?php echo $_order->id ?>" target="_blank"><?php echo $_order->id ?></a></td>
					<td id="sub_name"><?php echo $_order->shipping_first_name . ' ' . $_order->shipping_last_name ?></td>
					<td class="sub_email" id="sub_email"><?php echo $_order->billing_email ?></td>
					<td id="sub_phone"><span onclick="this.focus();this.select();"><?php echo $_order->billing_phone ? str_replace(array('-', '.', ' '), '', $_order->billing_phone) : str_replace(array('-', '.', ' '), '', get_user_meta($_order->customer_user, "billing_phone", TRUE)) ?></span></td>
					<td id="sub_cancel_date"><?php echo date('Y-m-d', strtotime($_order->order_date)) ?></td>
					<td id="sub_cancel_reason" class="<?php echo $failed_reason ?>"><?php echo $_order->wc_authorize_net_cim_card_type . ' <b><u>' . $_order->wc_authorize_net_cim_card_last_four . '</u></b> ' . $_order->wc_authorize_net_cim_card_exp_date ?></td>
					<td class="reactivate" id="<?php echo $_order->id ?>"><a href"">Payment</a></td>
				</tr>
				
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
	
	function expired_table($portal_type) {
		
		$expired_user_ids = get_data($portal_type);
				?>
		<table class="portal_table" width="100%" cellpadding="3" cellspacing="4">
			<thead>
				<th>User ID</th>
				<th>Name</th>
				<th>Email</th>
				<th>Phone</th>
				<th>Auth Profile</th>
				<th>Active Card</th>
				<th>Actions</th>
			</thead>
			<tbody>
			<?php
	
			foreach ($expired_user_ids as $user[0] => $user_id) :
				
				$_user = get_user_by("id", $user_id);
				$profile_id = get_user_meta($user_id, '_wc_authorize_net_cim_profile_id', TRUE);
				$payment_profiles = get_user_meta($user_id, '_wc_authorize_net_cim_payment_profiles', TRUE);
		
			?>
				<tr>
					<td id="sub_name"><a href="<?php echo get_option('siteurl') ?>/wp-admin/user-edit.php?user_id=<?php echo $_user->ID ?>" target="_blank"><?php echo $_user->ID ?></a></td>
					<td id="sub_name"><?php echo $_user->display_name ?></td>
					<td class="sub_email" id="sub_email"><?php echo $_user->user_email ?></td>
					<td id="sub_phone"><span onclick="this.focus();this.select();"><?php echo $_user->billing_phone ? str_replace(array('-', '.', ' '), '', $_user->billing_phone) : str_replace(array('-', '.', ' '), '', $_user->shipping_phone) ?></span></td>
					<td id="sub_cancel_date"><?php echo $profile_id ?></td>
					<td id="sub_cancel_reason" class="<?php echo $failed_reason ?>">
							<?php foreach ($payment_profiles as $profile_id => $_profile) :
									if ($_profile['active']) :
											echo substr($_profile['type'],0,1) . ' <b><u>' . $_profile['last_four'] . '</u></b> ' . $_profile['exp_date'];
									endif;
								  endforeach;
							?>
					<td class="reactivate" id="<?php echo $_order->id ?>"><a href"">Update CC</a></td>
				</tr>
				
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
	
	
	
	
	// Build Table
	function get_table($portal_type) {
		
		$table_data = get_data($portal_type);

		
	}
	
	
	// GET TABLE DATA
	function get_data( $portal_type = "" ) {
		global $wpdb;
		
		switch ($portal_type) {
			case "reactivate":
				$data = $wpdb->get_col("SELECT subscription_id FROM  " . $wpdb->prefix . "subscriptions WHERE status = 'canceled' AND cancel_reason != '' AND cancel_date < '". date('Y-m-d', strtotime('-3 months')). "' LIMIT 0, 10");
			break;
			
			case "failed":
				$data = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'shop_order' and post_status = 'wc-failed' ORDER BY post_date DESC LIMIT 0, 10");
			break;
			
			case "expired":
				$data = $wpdb->get_col("SELECT meta_value as user_id FROM {$wpdb->postmeta} where meta_key = '_customer_user' AND post_id IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wc_authorize_net_cim_card_exp_date' and meta_value REGEXP '/14$') LIMIT 0, 10");
			break;
				
			default: $data = "";
			break;
		} 
		return $data;
	}
	
	
	?>
	<script>
		jQuery(document).ready(function($) {
			$('.reactivate').live('click', function() {
				sub_id = $(this).attr("id");
				sub_email = $(this).find(".sub_email").text();
				console.log(sub_email);
				exists = $("#reactivate_customer_"+sub_id);
				console.log("Reactivate Subscription #"+sub_id);
				
				if (exists.length == 0) {
					$(this).html("Cancel");
					$(this).parent().after('<tr style="border:none;" id="reactivate_customer_'+sub_id+'"> \
													<form name="form_reactivate_'+sub_id+'" id="form_reactivate" action="" method="POST"> \
													<td colspan="2"> \
														<input name="note_content" type="text" placeholder="leave note" /> \
													</td><td colspan="1"> \
														<input name="discount" type="text" placeholder="discount" /> \
													</td><td colspan="1"> \
														<input name="next_shipment" type="date" title="Next Shipment Date" value="<?php echo date("Y-m-d", strtotime("+1 day")); ?>" /> \
													</td><td colspan="2"> \
														<textarea name="email" id="email" value="hello" cols="5"></textarea> \
													</td><td colspan="1"> \
														<input name="sub" id="submit_reactivation" type="submit" value="submit" /> \
														<input type="hidden" name="subscription_id" value="'+sub_id+'" /> \
														<input type="hidden" name="note_author" value="<?php echo $current_user->data->display_name; ?>" /> \
													</td> \
												</form> \
											</tr> \
											');
				}else {
					$("#reactivate_customer_"+sub_id).remove();
					$(this).html("Reactivate");
				}
			});
			$("#form_reactivate").live('submit', function(event) {
				event.preventDefault();
				console.log("Form submitted");
				safeUrl = <?php echo json_encode(plugins_url( 'manage-subscriptions/js/ajax/reactivate.php' )); ?>;
				$.ajax({
					type: 'POST',
					url: safeUrl,
					data: $(this).serialize(),
					dataType: 'JSON'
				})
				.done(function(data) {
					console.log(data);
				});
			});
			
			$('#holiday-banner').hide();
			$('#footer-widgets-container').hide();
			$('.breadcrumb-trail').hide();
			$('.breadcrumb').css({'border-bottom':''});
			
			
		});
		
	</script>
		