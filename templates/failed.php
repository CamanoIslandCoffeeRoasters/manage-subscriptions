<?php 

	// Display four areas of data
	function get_customers($portal_type = "") {
		
		$portal_table = $portal_type . '_table';
		$table = $portal_table($portal_type);
		return $table;
	}
	
	function get_script($portal_type = "") {
		?>
		<?php global $current_user; ?>
		<?php $woo_options = get_option('woo_options'); ?>
		
		<div class="twocol-one" ><h2 style="color:red;">Declined</h2>
			<li>Hi, is this <b>customer name</b>?</li>
			<li>Hi, this is <b><?php echo $current_user->data->display_name; ?></b> with <?php echo get_option('blogname') ?>. How are you doing today?</li>
			<li><b>Wait for reply</b></li>
			<li>Great! The reason I’m calling you today is your latest coffee shipment failed to process due to an issue with your card on file.</li> 
			<li>We just need to update your card and then we can get your next shipment out.</li> 
			<li>The card we have on file ends in <b> cc last four </b> and has an expiration date of <b>expiration date</b>. Would you like us to try running this card again or use a different card?</li> 
			<li><b>Wait for reply. Make any changes necessary.</b></li> 
			<li>Alright, and your next shipment is set to go to <b>confirm shipping address</b>. Is this correct?</li>  
			<li><b>Wait for reply</b></li> 
			<li><b>If confirmed correct: reprocess card -- wait to ensure it works</b></li>
			<li><u>If card DOES reprocess correctly</u></li>	
			<li>OK, great! We’ll get that next shipment right out to you.		
			<li>Is there anything else I can do for you while you have me on the phone? <br /></li>
			<li><b>Wait for reply</b></li>	
			<li>OK. Thank you again for drinking <?php echo get_option('blogname') ?> and have a great day.</li>
			<li><u>If card does NOT process</u></li>
			<li>It looks like that card failed again. Do you have another card you’d like to use?</li>
			<li>Wait for reply. Make any changes necessary.</li>
			<li><b>Reprocess card -- wait to ensure it works</b></li>
		</div>
		
		<div class="twocol-one last">
			<h2 style="color:green;">Expired</h2>
			<li>Hi, is this <b> customer name </b> ? </li>
			<li>Hi, this is <b><?php echo $current_user->data->display_name; ?></b> with <?php echo get_option('blogname') ?>. How are you doing today? </li>
			<li><b>Wait for reply</b></li>
			<li>Great! The reason I’m calling you today is your latest coffee shipment failed to process due to an issue with your card on file.</li>
			<li>We just need to update your card and then we can get your next shipment out. The card we have on file ends in -- insert last four -- and has an expiration date of  -- insert expiration date --.</li>
			<li>Do you have an updated expiration date for that card or a new card you would like to use?</li>
			<li><b>Wait for reply. Make any changes necessary</b></li>
			<li>Alright, and your next shipment is set to go to -- confirm shipping address --. Is this correct?</li>
			<li><b>Wait for reply</b></li>
			<li>If confirmed correct: reprocess card -- wait to ensure it works</li>
			<li><u>If card DOES reprocess correctly</u></li>
			<li>OK, great! We’ll get that next shipment right out to you.</li>
			<li>Is there anything else I can do for you while you have me on the phone?</li>
			<li><b>Wait for reply</b></li>
			<li>OK.	Thank you again for drinking <?php echo get_option('blogname') ?> and have a great day.</li>
			<li><u>If card does NOT process</u></li>
			<li>It looks like that card failed again. Do you have another card you’d like to use?</li> 
			<li><b>Wait for reply. Make any changes necessary</b></li>
			<li><u>reprocess card -- wait to ensure it works</u></li>
			<br />
			<br />
			<h2>Voicemail</h2>
			<li>This is a message for <b>customer name</b>.</li> 
			<li>Hi, this is <b><?php echo $current_user->data->display_name; ?></b> with <?php echo get_option('blogname') ?>.</li> 
			<li>Unfortunately your last coffee shipment failed to process due to an issue with your card on file.</li>
			<li>We just need to update your card and then we can get your next shipment out. Please give us a call back at <?php echo $woo_options['woo_contact_number']; ?></li>
			<li>Thank you and have a nice day.</li>
		</div>

				
		<?php
	}
	
	function get_orders($portal_type = "") {
		echo "Hello Orders!";
	}
	
	function get_survey($portal_type = "") {
		echo "Hello Survey!";
	}

function failed_table($portal_type) {
		$order_ids = get_data($portal_type);
		global $woocommerce, $wpdb;
		
		$woo_options = get_option('woo_options');
		
		?>
		<div id="data-table">
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
			<tbody id="table-body">
			<?php
	
			foreach ($order_ids as $order_id) :
				
				$subscription_id = get_post_meta($order_id, "subscription_id", TRUE);
				
				$failed_reason = "";
				
				$_order = new WC_Order($order_id);
				
				if ($wpdb->get_results("SELECT comment_content FROM {$wpdb->comments} WHERE comment_post_ID = $order_id AND comment_content LIKE '%declined%'")) {
					$failed_reason = "declined";
				}elseif ($wpdb->get_results("SELECT comment_content FROM {$wpdb->comments} WHERE comment_post_ID = $order_id AND comment_content LIKE '%expired%'")) {
					$failed_reason = "expired";
				}
		
			?>
				<tr id="row-<?php echo $_order->id ?>">
					<td id="order_id">
						<a href="<?php echo get_option('siteurl') ?>/wp-admin/post.php?action=edit&post=<?php echo $_order->id ?>" target="_blank">
							<?php echo $_order->id ?>
						</a>
					</td>
					<td id="order_name">
						<a href="<?php echo get_option('siteurl') . '/wp-admin/admin.php?page=edit-subscription&user='.$_order->billing_email.'&subscription_id='.$subscription_id ?>">
							<span id="first_name_<?php echo $_order->id ?>"><?php echo $_order->shipping_first_name ?></span>
							<span id="last_name_<?php echo $_order->id ?>"><?php echo $_order->shipping_last_name ?></span>
						</a>
					</td>
					<td class="order_email" id="order_email">
						<?php echo $_order->billing_email ?>
					</td>
					<td id="sub_phone">
						<?php echo $_order->billing_phone ? str_replace(array('-', '.', ' ', '(', ')'), '', $_order->billing_phone) : str_replace(array('-', '.', ' ', '(', ')'), '', get_user_meta($_order->customer_user, "billing_phone", TRUE)) ?>
					</td>
					<td id="sub_cancel_date">
						<?php echo date('Y-m-d', strtotime($_order->order_date)) ?>
					</td>
					<td id="sub_cancel_reason" class="<?php echo $failed_reason ?>" >
						<span title="<?php echo $failed_reason ?>"><?php echo $_order->wc_authorize_net_cim_card_type . ' <b><u>' . $_order->wc_authorize_net_cim_card_last_four . '</u></b> ' . $_order->wc_authorize_net_cim_card_exp_date ?></span>
					</td>
					<td class="action" id="<?php echo $_order->id ?>">
						<a class="actions" href"">Open
						</a>
					</td>
				</tr>
				
				<?php endforeach; ?>
			</tbody>
		</table>
		</div>
		<?php
	}

	?>
	
		<script>
		jQuery(document).ready(function($) {
			$('.action').live('click', function() {
				order_id = $(this).attr("id");
				order_email = $(this).find(".order_email").text();
				console.log(order_email);
				exists = $("#failed_order_"+order_id);
				console.log("Order #"+order_id);
				
				if (exists.length == 0) {
					$(this).html("Cancel");
					$(this).parent().after('<tr id="failed_order_'+order_id+'"> \
												<td colspan="7"> \
													<div id=""> \
														<form name="form_failed_'+order_id+'" id="form_failed" action="" method="POST"> \
														<div class="sixcol-two" style="margin-bottom:0% !important;"> \
															<select style="width:100%" name="disposition" id="disposition" required="required"> \
																<option value=""> -- SELECT OUTCOME -- </option> \
																<option value="updated">Updated Credit Card</option> \
																<option value="voicemail">Left Voicemail</option> \
																<option value="canceled">Cancel Subscription</option> \
																<option value="email">Email</option> \
																<option value="unreachable">Unreachable</option> \
															</select> \
														</div> \
														<div class="sixcol-three" style="margin-bottom:0% !important;"> \
															<textarea name="email_content" id="email" rows="15" cols="50"></textarea> \
														</div> \
														<div class="sixcol-one last" style="margin-bottom:0% !important;"> \
															<input name="sub" id="submit_reactivation" type="submit" value="submit" /> \
														</div> \
															<input type="hidden" name="order_id" value="'+order_id+'" /> \
															<input type="hidden" name="user" value="<?php echo $current_user->data->display_name; ?>" /> \
														</form> \
													</div> \
												</td> \
											</tr> \
											');
				}else {
					$("#failed_order_"+order_id).remove();
					$(this).text("Open");
				}
			});
			$("#form_failed").live('submit', function(event) {
				event.preventDefault();
				console.log("Form submitted");
				safeUrl = <?php echo json_encode(plugins_url( 'manage-subscriptions/js/ajax/' . $portal_type . '-ajax.php' )); ?>;
				$.ajax({
					type: 'POST',
					url: safeUrl,
					data: $(this).serialize(),
					dataType: 'JSON'
				})
				.done(function(data) {
					console.log(data);
					$('#row-'+order_id).remove();
					$('#failed_order_'+order_id).remove();
					$('div#copyright').replaceWith('<div id="copyright" class="col-left"><h2>Order #'+order_id+' Finished</h2></div>');
					$('#copyright').delay(3000).fadeTo(3000, 0.01);
				});
			});
			$('#disposition').live('change', function(event) {
				first_name = $('#first_name_'+order_id).html();
				if ($('#cancel_reason').length > 0) {
					$('#cancel_reason').remove();
				}
				
				switch ($(this).val()) {
					case "updated":
						$('#email').val('Dear '+first_name+',\n\nThank you for taking my call today. I’m glad we were able to update your card. If you need to make any future edits to your account, you can do so in your account at this url: <?php echo site_url( '/my-account' ); ?> .\n\nAlso, if you need any help with your account, please give us a call at <?php echo $woo_options['woo_contact_number']; ?>.\n\nThank you again for being a loyal Coffee Lover’s Club member. Thanks to coffee lovers like you we’ve been able to help build 42 villages and impact 24,000 people with just your daily cup of coffee. You can buy your coffee anywhere, but with <?php echo get_option('blogname') ?> you’re deciding to make a difference with your coffee.\n\nSincerely,\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 5PM PST');
					break;
					
					case "voicemail":
						$('#email').val('Dear '+first_name+',\n\nMy name is <?php echo $current_user->data->display_name; ?> with <?php echo get_option('blogname') ?>. I left you a quick voicemail regarding your account.\n\nYour card on file is declining your latest Coffee Lover’s Club shipment. Please give us a call back at <?php echo $woo_options['woo_contact_number']; ?> at your earliest convenience.\n\nThank you for choosing to make a difference with your daily cup of coffee. Thanks to coffee lovers like you, we’ve been able to help build 42 villages and impact 24,000 people with just your daily cup of coffee. You can buy your coffee anywhere, but with <?php echo get_option('blogname') ?> you’re deciding to make a difference with your coffee.\n\nSincerely,\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 5PM PST');
					break;
					
					case "canceled":
						$('#email').val('Dear '+first_name+',\n\nThank you for taking my call today. I have canceled your Coffee Lover’s Club. You will no longer receive any new orders.\n\nWhen you decide to restart your Coffee Lover’s Club subscription, please give us a call at <?php echo $woo_options['woo_contact_number']; ?>.\n\nThank you for choosing to make a difference with your daily cup of coffee. Thanks to coffee lovers like you we’ve been able to help build 42 villages and impact 24,000 people with just your daily cup of coffee.\nWe hope you’ll rejoin us in the not too distant future.\n\nSincerely,\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 5PM PST');
						$('#disposition').after('<br /><br /> \
												<select name="cancel_reason" id="cancel_reason" style="width:100%" required="required"> \
													<option value=""> -- REASON FOR CANCELING -- </option> \
													<option value="finances">Finances</option> \
													<option value="health">Health</option> \
													<option value="moving">Moving</option> \
													<option value="upset">Upset</option> \
													<option value="unreachable">Unreachable</option> \
													<option value="other">Other</option> \
												 </select> \
												');
					break;
					
					case "email":
						$('#email').val('Dear '+first_name+',\n\nThere seems to be an issue with the payment profile on your account. We have been unable to reach by phone, so if you could give us a call at <?php echo $woo_options['woo_contact_number']; ?>, we would greatly appreciate it.\n\nSincerely,\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 5PM PST');
					break;
					
					case "unreachable":
						$('#email').val('Customer Unreachable.');
					break;
					
					default: "";
					break;
				}
			});
			
			$('#holiday-banner').hide();
			$('#footer-widgets-container').hide();
			$('.breadcrumb-trail').hide();
			$('.breadcrumb').css({'border-bottom':' !important'});
			
			
		});
		
	</script>