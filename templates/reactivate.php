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
					<td id="sub_id"><a href="<?php echo get_option('siteurl') ?>/wp-admin/admin.php?page=edit-subscription&user=<?php echo $subscription->email ?>&subscription_id=<?php echo $subscription->subscription_id ?>" target="_blank"><?php echo $subscription->subscription_id ?></a></td>
					<td id="sub_name"><?php echo $subscription->name ?></td>
					<td id="sub_email"><?php echo $subscription->email ?></td>
					<td id="sub_phone"><span onclick="this.select();"><?php echo $user_billing_phone ?></span></td>
					<td id="sub_cancel_date"><?php echo $subscription->cancel_date ?></td>
					<td id="sub_cancel_reason"><?php echo $subscription->cancel_reason ?></td>
					<td class="reactivate" id="<?php echo $subscription_id ?>"><a href"">Reactivate</a></td>
<!--					<tr id="reactivate_customer_'+sub_id+'"><form name="form_reactivate_'+sub_id+'" id="form_reactivate" action="" method="POST"><td colspan="2"><input name="note_content" type="text" placeholder="leave note" /></td><td colspan="1"><input name="discount" type="text" placeholder="discount" /></td><td colspan="1"><input name="next_shipment" type="date" title="Next Shipment Date" value="<?php echo date("Y-m-d", strtotime("+1 day")); ?>" /></td><td colspan="2"><textarea name="email" id="email" value="hello" cols="5"></textarea></td><td colspan="1"><input name="sub" id="submit_reactivation" type="submit" value="submit" /><input type="hidden" name="subscription_id" value="'+sub_id+'" /><input type="hidden" name="note_author" value="<?php echo $current_user->data->display_name; ?>" /></td></form></tr> -->
				</tr>
				
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		
	}
	
	?>
	<script>
		jQuery(document).ready(function($) {
			$('.reactivate').live('click', function() {
				sub_id = $(this).attr("id");
				//sub_email = $(this).find(".sub_email").text();
				//console.log(sub_email);
				exists = $("#reactivate_customer_"+sub_id);
				console.log("Reactivate Subscription #"+sub_id);
				
				if (exists.length == 0) {
					$(this).html("Cancel");
					$(this).parent().after('<tr id="reactivate_customer_'+sub_id+'"> \
												<td colspan="7"> \
													<div> \
														<form name="form_reactivate_'+sub_id+'" id="form_reactivate" action="" method="POST"> \
															<input name="note_content" type="text" placeholder="leave note" /> \
															<input name="discount" type="text" placeholder="discount" /> \
															<input name="next_shipment" type="date" title="Next Shipment Date" value="<?php echo date("Y-m-d", strtotime("+1 day")); ?>" /> \
															<textarea name="email" id="email" value="hello" cols="5"></textarea> \
															<input name="sub" id="submit_reactivation" type="submit" value="submit" /> \
															<input type="hidden" name="subscription_id" value="'+sub_id+'" /> \
															<input type="hidden" name="note_author" value="<?php echo $current_user->data->display_name; ?>" /> \
														</form> \
													</div> \
												</td> \
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
				safeUrl = <?php echo json_encode(plugins_url( 'manage-subscriptions/js/ajax/reactivate-ajax.php' )); ?>;
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
		