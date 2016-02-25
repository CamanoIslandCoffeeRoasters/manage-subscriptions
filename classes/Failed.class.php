<?php
    if (!class_exists('Failed')) {
        class Failed {

            public function __construct($parent_class) {

                // Keep the parent class here so we can use it to grab some global data
                $this->parent = $parent_class;
                // Keep track of the portal type executed in the parent, which is obvious here by the file we're in
                $this->portal_type = $this->parent->portal_type;
                // Set the data type so we know how to get data out of it
                $this->data_type = "order";
                // Get the class to display the rows
                $this->display = new Display($this->parent, $this->data_type);
                // Build array of cells, columns, and rows to handle, and whether or not to use them as column headers
                $this->display_data = array(
                    'id'                                                    => array('name' => 'Order ID',      'column' => true),
                    'order_date'                                            => array('name' => 'Date',          'column' => true),
                    'customer_user'                                         => array('name' => 'User',          'column' => true),
                    'billing_email'                                         => array('name' => 'Email',         'column' => true),
                    'billing_phone'                                         => array('name' => 'Phone',         'column' => true),
                    'billing_first_name'                                    => array('name' => 'First Name',    'column' => false),
                    'billing_last_name'                                     => array('name' => 'Last Name',     'column' => false),
                    'reason'                                                => array('name' => 'Reason',        'column' => true),
                    'actions'                                               => array('name' => 'Actions',       'column' => true),
                    '_wc_authorize_net_cim_credit_card_card_type'           => array('name' => 'Card Type',     'column' => false),
                    '_wc_authorize_net_cim_credit_card_card_expiry_date'    => array('name' => 'Expires',       'column' => false),
                    '_wc_authorize_net_cim_credit_card_account_four'        => array('name' => 'Last Four',     'column' => false)
                );
            }

        	function get_script() {
                global $current_user;
        		 $woo_options = get_option('woo_options');

                 ?>
                 <style>
                 .twocol-one.script {background:white; border-radius: 8px;}
                 .twocol-one.script li, h2 {padding:8px;}
                 </style>
        		<div class="twocol-one script">
                    <div class="box" style="margin:0%;">
                        <div class="dashboard-subscription-box-header">
                            <h2 class="title declined" style="font-weight:700;">Declined</h2>
                        </div>
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
                </div>

        		<div class="twocol-one script last">
                    <div class="box" style="margin:0%;">
                        <div class="dashboard-subscription-box-header">
                            <h2 class="title expired" style="font-weight:700;">Expired</h2>
                        </div>
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
                    </div>
                </div>
                <div class="twocol-one script">
                    <div class="box" style="margin:0%;">
                        <div class="dashboard-subscription-box-header">
                            <h2 class="title" style="color:#fff; font-weight:700;">Voicemail</h2>
                        </div>
            			<li>This is a message for <b>customer name</b>.</li>
            			<li>Hi, this is <b><?php echo $current_user->data->display_name; ?></b> with <?php echo get_option('blogname') ?>.</li>
            			<li>Unfortunately your last coffee shipment failed to process due to an issue with your card on file.</li>
            			<li>We just need to update your card and then we can get your next shipment out. Please give us a call back at <?php echo $woo_options['woo_contact_number']; ?></li>
            			<li>Thank you and have a nice day.</li>
                    </div>
                </div>
                <div class="twocol-one script last"></div>

        		<?php
        	}

        	function get_orders($portal_type = "") {
        		echo "Hello Orders!";
        	}

        	function get_survey($portal_type = "") {
        		echo "Hello Survey!";
        	}

            function get_customers() {
        		return $this->parent->display_table();

             }

            public function get_js() {
                global $current_user;
                $woo_options = get_option('woo_options');
                ?>
        		<script>
        		jQuery(document).ready(function($) {
                    $('.portal_table').on('click', '.action', function() {
                         row_id = $(this).attr("id");
                         exists = $("#row_"+row_id);
			             $('[id^="row_"]').remove();
                         $('span.actions.close').removeClass('close').addClass('open').text("Open");
                         // Check if there is already a row being displayed
			             if (exists.length == 0) {
                            row = $(this).parent();
                            email = $(this).parent().find(".row_email").text();
                            $('span.actions', this).removeClass('open').addClass('close').text("Cancel");
        					$(this).parent().after('<tr id="row_'+row_id+'"> \
        												<td colspan="7"> \
        													<div id="'+row_id+'"> \
        														<form name="form_row_'+row_id+'" id="form_row" action="" method="POST"> \
        														<div class="sixcol-two" style="margin-bottom:0% !important;"> \
        															<select style="width:100%" name="disposition" id="disposition" required="required"> \
        																<option value=""> -- SELECT OUTCOME -- </option> \
        																<option value="updated">Updated Credit Card</option> \
        																<option value="voicemail">Left Voicemail</option> \
        																<option value="canceled">Cancel Subscription</option> \
        																<option value="email">Email</option> \
                                                                        <option value="callback">Callback</option> \
                                                                        <option value="updated_card">Updated Card</option> \
                                                                        <option value="remove">Remove</option> \
        																<option value="unreachable">Unreachable</option> \
        															</select> \
        														</div> \
        														<div class="sixcol-three" style="margin-bottom:0% !important;"> \
        															<textarea name="email_content" id="email" rows="15" cols="50"></textarea> \
        														</div> \
        														<div class="sixcol-one last" style="margin-bottom:0% !important;"> \
        															<input name="sub" id="submit_reactivation" type="submit" value="submit" /> \
        														</div> \
        															<input type="hidden" name="order_id" value="'+row_id+'" /> \
        															<input type="hidden" name="user" value="<?php echo $current_user->data->display_name; ?>" /> \
        														</form> \
        													</div> \
        												</td> \
        											</tr> \
        											');
        				}else {
        					$("#row_"+row_id).remove();
        					$('span.actions', this).removeClass('close').addClass('open').text("Open");
        				}
        			});


        			$('.portal_table').on('change', '#disposition', function(event) {
        				first_name = $('#first_name_'+row_id).html();
        				if (($('#cancel_reason').length > 0) ||  ($('#contact_callback').length > 0)) {
        					$('#cancel_reason').remove();
                            $('#contact_callback').remove();
        				}

        				switch ($(':selected', this).val()) {
        					case "updated":
        						$('#email').val('Thank you for taking my call today. I’m glad we were able to update your card. If you need to make any future edits to your account, you can do so in your account at this url: <?php echo site_url( '/my-account' ); ?> .\n\nAlso, if you need any help with your account, please give us a call at <?php echo $woo_options['woo_contact_number']; ?>.\n\nThank you again for being a loyal Coffee Lover’s Club member. Thanks to coffee lovers like you we’ve been able to help build 42 villages and impact 24,000 people with just your daily cup of coffee. You can buy your coffee anywhere, but with <?php echo get_option('blogname') ?> you’re deciding to make a difference with your coffee.\n\nSincerely,\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 4PM PST');
        					break;

        					case "voicemail":
        						$('#email').val('My name is <?php echo $current_user->data->display_name; ?> with <?php echo get_option('blogname') ?>. I left you a quick voicemail regarding your account.\n\nYour card on file is declining your latest Coffee Lover’s Club shipment. Please give us a call back at <?php echo $woo_options['woo_contact_number']; ?> at your earliest convenience.\n\nThank you for choosing to make a difference with your daily cup of coffee. Thanks to coffee lovers like you, we’ve been able to help build 42 villages and impact 24,000 people with just your daily cup of coffee. You can buy your coffee anywhere, but with <?php echo get_option('blogname') ?> you’re deciding to make a difference with your coffee.\n\nSincerely,\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 4PM PST');
        					break;

        					case "canceled":
        						$('#email').val('Thank you for taking my call today. I have canceled your Coffee Lover’s Club. You will no longer receive any new orders.\n\nWhen you decide to restart your Coffee Lover’s Club subscription, please give us a call at <?php echo $woo_options['woo_contact_number']; ?>.\n\nThank you for choosing to make a difference with your daily cup of coffee. Thanks to coffee lovers like you we’ve been able to help build 42 villages and impact 24,000 people with just your daily cup of coffee.\nWe hope you’ll rejoin us in the not too distant future.\n\nSincerely,\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 4PM PST');
        						$('#disposition').after('<br /><br /> \
        												<select name="cancel_reason" id="cancel_reason" style="width:100%;" required="required"> \
        													<option value=""> -- REASON FOR CANCELING -- </option> \
        													<option value="billing">Billing</option> \
        													<option value="death">Death</option> \
        													<option value="duplicate">Duplicate</option> \
        													<option value="dislike">Dislike</option> \
        													<option value="finances">Finances</option> \
        													<option value="quantity">Quantity</option> \
        													<option value="gift">Gift</option> \
        													<option value="health">Health</option> \
        													<option value="keurig">Keurig</option> \
        													<option value="obligation">Obligation</option> \
        													<option value="marketplace">Marketplace</option> \
        													<option value="moving">Moving</option> \
        													<option value="traveling">Traveling</option> \
        													<option value="upset">Upset</option> \
        													<option value="note">See Note</option> \
        												 </select> \
        												');
        					break;

        					case "email":
        						$('#email').val('There seems to be an issue with the payment profile on your account. We have been unable to reach by phone, so if you could give us a call at <?php echo $woo_options['woo_contact_number']; ?>, we would greatly appreciate it.\n\nSincerely,\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 4PM PST');
        					break;

                            case "callback":
                                $('#email').val('We\'re going to call you back on this date: <?php echo $woo_options['woo_contact_number']; ?>, we would greatly appreciate it.\n\nSincerely,\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 4PM PST');
                                $('#disposition').after('<div id="contact_callback"> \
                                                            <br /><br /> \
                                                            <input type="text" class="date-picker" name="contact_callback" value="<?php echo date('m/d/Y',strtotime('+ 1 week')); ?>" > \
                                                         </div> \
                                                        ');
                                $('.date-picker').datepicker({numberOfMonths:[1,1]});
                            break;

                            case "updated_card":
        						$('#email').val('Thank you for updating your card in your account. Your order is now processing and will ship out on our next available shipping day. Everything looks good to go and your coffee will be on its way shortly!\n\nThank you for partnering with us in making a difference with your daily cup of coffee.\n\nSincerely,\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 4PM PST');
        					break;

                            case "remove":
        						$('#email').val('Removed from Portal');
        					break;

        					case "unreachable":
        						$('#email').val('Customer Unreachable.');
        					break;

        					default: $('#email').val('Customer Unreachable.');
        					break;
        				}
        			});
        		});

        	</script>
        <?php }
    }
}
