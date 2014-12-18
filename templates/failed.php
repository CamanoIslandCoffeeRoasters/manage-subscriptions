<?php 

	// Display four areas of data
	function get_customers($portal_type = "") {
		
		$portal_table = $portal_type . '_table';
		$table = $portal_table($portal_type);
		return $table;
	}
	
	function get_script($portal_type = "") {
		?>
		
		<div class="twocol-one"><h2>Declined</h2>
		
		Hi, is this <b> customer name </b> ? <br />
		Hi, this is <b> name </b> with Camano Island Coffee Roasters. How are you doing today? <br />
		<b> wait for reply </b> <br />
		Great! The reason I’m calling you today is your latest coffee shipment failed to process due to an issue with your card on file. <br /> 
		We just need to update your card and then we can get your next shipment out.<br /><br />
		The card we have on file ends in <b> cc last four </b> and has an expiration date of <b> expiration date </b>. Would you like us to try running this card again or use a different card?<br />
		<b>wait for reply. Make any changes necessary. </b><br />
		
		Alright, and your next shipment is set to go to  <b>confirm shipping address</b>. Is this correct? <br /> 
		<b>wait for reply</b><br />
		
		<b>If confirmed correct: reprocess card -- wait to ensure it works</b>
		
		<b>If card DOES reprocess correctly</b>
		
		OK, great! We’ll get that next shipment right out to you. 
		
		Is there anything else I can do for you while you have me on the phone? <br /><br />
		<b>wait for reply</b> <br />
		
		OK. Thank you again for drinking Camano Island Coffee and have a great day.<br />
		<br /><br />
		<b>If card does not process</b><br />
		It looks like that card failed again. Do you have another card you’d like to use? <wait for reply>
		
		<Make any changes necessary>
		
		<reprocess card -- wait to ensure it works>
			
		</div>
		
		
		<div class="twocol-one last"><h2>Expired</h2>
		
			Hi, is this <member’s name>? Hi, this is <insert your name here> with Camano Island Coffee Roasters. 
			
			How are you doing today? <wait for reply> Great! 
			
			The reason I’m calling you today is your latest coffee shipment failed to process due to an issue with your card on file. 
			
			We just need to update your card and then we can get your next shipment out. The card we have on file ends in <insert last four here> and has an expiration date of <insert expiration date here>.
			
			Do you have an updated expiration date for that card or a new card you would like to use? <wait for reply> 
			
			<Make any changes necessary>
			
			Alright, and your next shipment is set to go to <confirm shipping address>. Is this correct? <wait for reply>
			
			<If confirmed correct: reprocess card -- wait to ensure it works>
			
			<If card DOES reprocess correctly>
			
			OK, great! We’ll get that next shipment right out to you. 
			
			Is there anything else I can do for you while you have me on the phone? <wait for reply>
			
			OK. 
			
			Thank you again for drinking Camano Island Coffee and have a great day.
			####
			<If card does not process>
			It looks like that card failed again. Do you have another card you’d like to use? <wait for reply>
			
			<Make any changes necessary>
			
			<reprocess card -- wait to ensure it works>
				
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
							<span id="first_name"><?php echo $_order->shipping_first_name ?></span>
							<span id="last_name"><?php echo $_order->shipping_last_name ?></span>
						</a>
					</td>
					<td class="order_email" id="order_email">
						<?php echo $_order->billing_email ?>
					</td>
					<td id="sub_phone">
						<?php echo $_order->billing_phone ? str_replace(array('-', '.', ' '), '', $_order->billing_phone) : str_replace(array('-', '.', ' '), '', get_user_meta($_order->customer_user, "billing_phone", TRUE)) ?>
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
																<option value="canceled">Delete Order, Set Club to Canceled</option> \
															</select> \
														</div> \
														<div class="sixcol-three" style="margin-bottom:0% !important;"> \
															<textarea name="email_content" id="email" rows="5" cols="40"></textarea> \
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
				first_name = $(this).prevAll('#first_name:first').text();

				switch ($(this).val()) {
					case "updated":
						$('#email').val('Dear '+first_name+', Thank you for taking my call. We\'re very glad to update your card and get some coffee to you. Thank you for continuing to support uor farmers with your daily cup of coffee!');
					break;
					
					case "voicemail":
						$('#email').val('Dear '+first_name+', consider yourself voicemailed!');
					break;
					
					case "canceled":
						$('#email').val('Dear '+first_name+', consider yourself canceled!');
						$('#disposition').after('<br /><br /><select name="cancel_reason" style="width:100%" required="required"> \
													<option value=""> -- REASON FOR CANCELING -- </option> \
													<option value="finances">Finances</option> \
													<option value="health">Health</option> \
													<option value="moving">Moving</option> \
													<option value="upset">Upset</option> \
												 </select> \
												');
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