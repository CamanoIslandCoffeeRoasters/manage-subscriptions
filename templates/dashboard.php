<?php 

global $current_user;
	$portal_type = $_GET['portal_type'] ? $_GET['portal_type'] : "reactivate";
	include $portal_type. '.php';
						
if ( !empty($current_user->roles) ) : 
    if ( ($current_user->roles[0] == 'administrator') ) : 
        echo "<br />Greetings and Salutations, " . $current_user->data->display_name;
    ?>

    
<div id="subscriptions_container">

	<div class="threecol-one <?php echo ($portal_type == 'reactivate') ? 'portal_type': 'subscriptions-nav' ?>">
		<a href="<?php echo get_option('siteurl') ?>/portal/?portal_type=reactivate">Reactivations</a>
	</div>
	<div class="threecol-one <?php echo ($portal_type == 'failed') ? 'portal_type': 'subscriptions-nav' ?>">
		<a href="<?php echo get_option('siteurl') ?>/portal/?portal_type=failed">Failed Orders</a>
	</div>
	<div class="threecol-one last <?php echo ($portal_type == 'expired') ? 'portal_type': 'subscriptions-nav' ?>">
		<a href="<?php echo get_option('siteurl') ?>/portal/?portal_type=expired">Expired Cards</a>
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
		<?php echo get_customers($portal_type); ?>
	</div>
	
	<div id="script_content" style="display:none;">
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
?>
 <a href="<?php echo wp_login_url(site_url('/portal')) ?>">Login here</a> 
 
<?php 
endif;

?>


<?php

		// GET TABLE DATA
	function get_data( $portal_type = "" ) {
		global $wpdb;
		
		switch ($portal_type) {
			case "reactivate":
				$data = $wpdb->get_col("SELECT subscription_id 
										FROM  " . $wpdb->prefix . "subscriptions 
										WHERE status = 'canceled' 
										AND cancel_reason != '' 
										AND cancel_date < '". date('Y-m-d', strtotime('-3 months')). "' 
										LIMIT 0, 10");
			break;
			
			case "failed":
				$data = $wpdb->get_col("SELECT distinct(posts.ID) 
										FROM {$wpdb->posts} posts 
										LEFT JOIN {$wpdb->postmeta} meta 
										ON meta.post_id = posts.ID 
										WHERE posts.post_type = 'shop_order' 
										AND posts.post_status = 'wc-failed' 
										AND ((meta.post_id NOT IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'contact_last'))
										OR (meta.meta_key = 'contact_last' 
										AND DATE(meta.meta_value) < '" . date('Y-m-d', strtotime('-7 days')). "')) 
										ORDER BY posts.post_date DESC 
										LIMIT 0, 40");
			break;
			
			case "expired":
				$data = $wpdb->get_col("SELECT meta_value as user_id 
										FROM {$wpdb->postmeta} 
										WHERE meta_key = '_customer_user' 
										AND post_id IN 
											(SELECT post_id 
											FROM {$wpdb->postmeta} 
											WHERE meta_key = '_wc_authorize_net_cim_card_exp_date' 
											AND meta_value 
											REGEXP '/14$') 
										LIMIT 0, 10");
			break;
				
			default: $data = "";
			break;
		} 
		return $data;
	}

