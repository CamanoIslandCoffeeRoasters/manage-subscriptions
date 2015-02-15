<?php 

global $current_user;
	$portal_type = $_GET['portal_type'] ? $_GET['portal_type'] : "reactivate";
	include $portal_type. '.php';
						
     if ( !empty($current_user->roles) ) : 
          if ( ($current_user->roles[0] == 'administrator') ) : 
               echo "Greetings and Salutations, " . $current_user->data->display_name;
    ?>
     <br />
     <br />
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
               <div style="clear:both;"></div>
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
     	<div style="clear:both;"></div>
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
 
     <?php endif; ?>

<?php
	// GET TABLE DATA
	function get_data( $portal_type = "" ) {
		global $wpdb;
          $manage_subscriptions = get_option('manage_subscriptions'); 
          
		switch ($portal_type) {
			case "reactivate":
                    $data = $wpdb->get_col("SELECT subs.subscription_id 
                                                  FROM  " . $wpdb->prefix . "subscriptions subs
                                                  WHERE subs.status = 'canceled' 
                                                  AND subs.cancel_reason != 'remove' 
                                                  AND subs.cancel_date < '". date('Y-m-d', strtotime('-' . $manage_subscriptions['cancel_date']. ' days')). "'
                                                  AND ((subs.contact_last IS NULL)
                                                  OR (DATE(subs.contact_last) < '" . date('Y-m-d', strtotime('-' . $manage_subscriptions['contact_last_subscription'].' days'))."'))
                                                  ORDER BY subs.cancel_reason DESC
                                                  LIMIT 0, ". $manage_subscriptions['num_rows'] . "");
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
										AND DATE(meta.meta_value) < '" . date('Y-m-d', strtotime('-'.$manage_subscriptions['contact_last_order'].' days')). "')) 
										ORDER BY posts.post_date DESC 
										LIMIT 0, ". $manage_subscriptions['num_rows'] . "");
			break;
			
			case "expired":
				$complete_search = check_expired_cards();

				$data = $wpdb->get_col("SELECT user_id 
										FROM {$wpdb->usermeta}
										WHERE meta_key = '_wc_authorize_net_cim_payment_profiles'
										AND meta_value REGEXP '$complete_search'
										LIMIT 0, ". $manage_subscriptions['num_rows'] . "");
			break;
				
			default: $data = "";
			break;
		} 
		return $data;
	}


	function check_expired_cards() {

			// 2 digit year i.e. 2015 would be 15
			$this_year = date('y');
			// 2 digit last year i.e. 2014 would be 14
			$last_year = date('y', strtotime('-1 year'));
			// 2 digit month i.e. September would be 09
			$this_month = date('m');
			
			// checks if the first digit of the month is a 0 or 1 (Jan - Sept, or October - December)
			$months = substr($this_month, 0, 1) ;
			// Pre-populate query for last year, since it's static (note pipe at the end) 
			$search_last_year = "0[1-9]/$last_year|1[0-2]/$last_year|";
			
			// Depending on whether we're above or below 10 
			switch ($months) {
				// Below 10
				case '0':
					// Check the second digit of the month
					$month = substr($this_month, -1, 1);
					// TRUE: If it's above 1, we need to add a "-" character to separate it from the other
					// FALSE: If it's equal to 1, only search for 0[1] in the Regexp string
					$month > 1 ? $month = "1-$month" : $month = '1' ;
					// Add month to brackets, and tack on the 2 digit year on the end
					$search_this_year = "0[$month]/$this_year";
				break;
				// Above 10
				case '1':
					// Check the second digit of the month
					$month = substr($this_month, -1, 1);
					// TRUE: If it's above 0, we need to add a "-" character to separate it from the other
					// FALSE: If it's equal to 0, only search for 1[0] in the Regexp string
					$month > 0 ? $month = "0-$month" : $month = '0' ;
					// Add first 9 months, add the 10 (and above) brackets, and tack on the 2 digit year on the end
					$search_this_year = "0[1-9]/$this_year|1[$month]/$this_year";
				break;
				default: '';
				break;
			}
			// Append search for last year and this year into one for SQL search
			$complete_search = $search_last_year . $search_this_year;
			 
		return $complete_search; 
		
	}
