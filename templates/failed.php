<?php 

	// Display four areas of data
	function get_customers($portal_type = "") {
		
		$portal_table = $portal_type . '_table';
		$table = $portal_table($portal_type);
		return $table;
	}
	
	function get_script($portal_type = "") {
		echo "Hello Scripts!";
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
						<span id="first_name"><?php echo $_order->shipping_first_name ?></span>
						<span id="last_name"><?php echo $_order->shipping_last_name ?></span>
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
					<td id="sub_cancel_reason" class="<?php echo $failed_reason ?>">
						<?php echo $_order->wc_authorize_net_cim_card_type . ' <b><u>' . $_order->wc_authorize_net_cim_card_last_four . '</u></b> ' . $_order->wc_authorize_net_cim_card_exp_date ?>
					</td>
					<td class="action" id="<?php echo $_order->id ?>">
						<a class="actions" href"">Open
						</a>
					</td>
				</tr>
				
				<?php endforeach; ?>
			</tbody>
		</table>
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
																<option value="updated">Update Card</option> \
																<option value="voicemail">Voicemail</option> \
																<option value="canceled">Cancel</option> \
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
					$('#copyright').delay(2000).fadeTo(3000, 0.01);
				});
			});
			$('#disposition').live('change', function(event) {
				first_name = $(this).prevAll('#first_name').text();
				console.log($(this).prevAll('#first_name').text());
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
													<option value="nasty">Nasty</option> \
													<option value="gross">Gross</option> \
													<option value="eeeeew">Eeeeew</option> \
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