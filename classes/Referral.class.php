<?php
	if (!class_exists('Referral')) {

        class Referral {

            public function __construct() {
                $this->portal_type = $GLOBALS['manage_subscriptions']->portal_type;
				$this->data_type = "subscription";
            }

        	function get_script() {
        		global $current_user;
                  $woo_options = get_option('woo_options'); ?>

                  <div class="" ><h2 style="color:red;">Direct Sales</h2>
                       <li>Hi, is <b><i>Former Customer's Name</i></b>?</li>
                       <li>Hi, this is <b><?php echo $current_user->data->display_name; ?></b> with <?php echo get_option('blogname') ?>. How are you doing today?</li>
                       <li>Great! Well hey, I was looking over your old account with us, and I see here that your reason for cancelling was <b>~ insert cancellation reason here ~</b>. Is that correct?</li>
                       <br />
                       <div class="fourcol-one">
                            <a id="money" class="click" href="#money_div"><h3><u>Too much money</u></h3></a>
                       </div>
                       <div class="fourcol-one">
                            <a id="coffee" class="click" href="#coffee_div"><h3><u>Too much coffee</u></h3></a>
                       </div>
                       <div class="fourcol-one">
                            <a id="health" class="click" href="#health_div"><h3><u>Health reasons</u></h3></a>
                       </div>
                       <div class="fourcol-one last">
                            <a id="voicemail" class="click" href="#voicemail_div"><h3><u>Voicemail</u></h3></a>
                       </div>
                       <div style="clear:both;"></div>
                       <br />
                       <br />
                       <br />
                       <div id="money_div" class="money option">
                            <li><b>Too much money formerly 3lb or 4lb:</b></li>
                            <li>I’m wondering if you might be interested in joining our 2lb club? It’s only $35 per shipment, you can still adjust your shipping frequency from 2 to 10 weeks, you still get free shipping. Plus, we’ve got a deal for 50% off your first shipment - so your total would only be $17.50. Would you like to give that a try?</li>
                       </div>
                       <br />
                       <div class="money option">
                            <li><b>Too much money formerly 2lb:</b></li>
                            <li>I’m wondering if you might be interested in joining our 4lb club? I know that sounds crazy, so hear me out - It’s only $13 per pound (you previously were paying $17.50 in the 2lb club). You still get free shipping, and you can still adjust your shipping frequency from 2 to 10 weeks. Ideally, you’d set it to 8 weeks so you’d be getting the same amount of coffee as you were before. Plus, we’ve got a deal for 50% off your first shipment - so your total would only be $26. Would you like to give that a try?</li>
                       </div>
                       <br />
                       <div id="coffee_div"  class="coffee option">
                            <li><b>Too much coffee formerly 3lb or 4lb:</b></li>
                            <li>I’m wondering if you might be interested in joining our 2lb club? You’d get less coffee, but still be able to adjust your shipping frequency from 2 to 10 weeks, and it also comes with free shipping. Plus, we’ve got a deal for 50% off your first shipment - so your total would only be $17.50. Would you like to give that a try?</li>
                       </div>
                       <br />
                       <div class="coffee option">
                            <li><b>Too much coffee formerly 2lb:</b></li>
                            <li>I’m wondering if adjusting your shipping frequency from <b>~ use old frequency ~</b> to out to 10 weeks might work better for you. You know, I’ve got a deal for 50% off your first shipment - so your total would only be $17.50. You can try it out, see how the longer frequency works for you - plus it’s still got the free shipping. Would you like to give that a try?</li>
                       </div>
                       <br />
                       <div id="health_div" class="health option">
                            <li><b>Health Reasons</b></li>
                            <li>I totally understand experiencing health issues with coffee, But I want to ask if you have ever tried our decaf or sumatran coffees? Our Sumatra naturally has the lowest caffeine of all our regular coffees, and our Swiss Water Processed decaf removes 99.9% of caffeine (plus it’s healthier than chemical-processed decaf, which is the industry norm). So, if you suffer from caffeine sensitivity, our Sumatran coffee is a great place to start. I can set you up to get a 2lb shipment of our Sumatra dark, and give you 50% off that first shipment so your total today would be only $17.50. Would you like to give that a try?</li>
                       </div>
                       <br /><br />
                       <div id="voicemail_div" class="voicemail option">
                            <h2><b>Voicemail Script</b></h2>
                            <li>Hi, this is a message for <b><i>Former Customer's Name</i></b>. My name is <b><?php echo $current_user->data->display_name; ?></b> with <?php echo get_option('blogname') ?>. I just wanted to have a quick chat to see how you’re doing and ask you a few questions about your past experiences with us. Plus, I’ve got a special offer available for rejoining the club, so we could talk about that, too!</li>
                            <li>Please give me a call on my direct line, which is <?php echo $woo_options['woo_contact_number']; ?>. Again, my name is <?php echo $current_user->data->display_name; ?> and my number is <?php echo $woo_options['woo_contact_number']; ?>.</li>
                            <li>Thanks and have a lovely day!</li>
                       </div>
                  </div>
                  <br />

                  <script>
                       jQuery(document).ready(function($) {
                            $('.click').click(function() {
                                 div = $(this).attr('id');
                                 $('.option').css({'background-color' : '',
                                                   'color' : ''
                                                   });
                                 $('.'+div).css({'background-color' : '#85AAB1',
                                                 'color' : 'white',
                                                 'border-radius' : '8px',
                                                 'padding' : '0px 8px 0px 8px'
                                                });
                            });
                       });
                  </script>

                  <?php
        	}

        	function get_orders() {
        		echo "Hello Orders!";
        	}

        	function get_survey() {
        	     ?>
        	     <div >
        		   <iframe src="https://docs.google.com/a/camanoislandmanagement.com/forms/d/1ZD01yOdEPY_43oJsg8Zmaf2ze3QvdFXVD_YPHkJ6wA0/viewform?embedded=true" width="900" height="1200" frameborder="0" marginheight="0" marginwidth="0">Loading...</iframe>
        		</div>
        		<?php
        	}

        	function get_customers() {
        		global $woocommerce, $wpdb, $current_user;

                $subscription_ids = Manage_Subscriptions::get_data($this->portal_type);
                $woo_options = get_option('woo_options');

                  if (!$subscription_ids) echo "<h1>No Reactivations with current settings</h1><a target='_blank' href='" . site_url('wp-admin/admin.php?page=manage_subscriptions') . "'>Edit Settings</a><br /><br />";
                  else {
                       ?>
                  <div id="data-table">
                  <table class="portal_table" width="100%" cellpadding="3" cellspacing="4">
                       <thead>
                            <th>SubID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Signup</th>
                            <th>Source</th>
                            <th>Actions</th>
                       </thead>
                       <?php

                       foreach ($subscription_ids as $subscription_id) :

                       $subscription = Subscriptions_Subscribers::get_subscription(NULL, $subscription_id);
                       $user = get_user_by('email', $subscription->email);
                       $phone = get_user_meta($user->ID, "billing_phone", TRUE);
                  ?>
                       <tbody id="table-body">
                            <tr id="row-<?php echo $subscription_id ?>">
                                 <td id="subscription_id">
                                      <a href="<?php echo get_option('siteurl') ?>/wp-admin/admin.php?page=edit-subscription&user=<?php echo $subscription->email ?>&subscription_id=<?php echo $subscription_id ?>" target="_blank">
                                           <?php echo $subscription_id ?>
                                      </a>
                                 </td>
                                 <td id="order_name">
                                      <a href="<?php echo get_option('siteurl') ?>/wp-admin/admin.php?page=edit-subscription&user=<?php echo $subscription->email ?>&subscription_id=<?php echo $subscription_id ?>" target="_blank">
                                           <span id="first_name_<?php echo $subscription_id ?>"><?php echo $subscription->name ?></span>
                                      </a>
                                 </td>
                                 <td class="order_email" id="order_email">
                                      <?php echo $subscription->email ?>
                                 </td>
                                 <td id="sub_phone" data-caller-name="<?php echo $subscription->name ?>">
                                      <?php echo str_replace(array('-', '.', ' ', '(', ')'), '', $phone); ?>
                                 </td>
                                 <td id="sub_cancel_date">
                                      <?php echo date('m/d/Y', strtotime($subscription->subscription_start)); ?>
                                 </td>
                                 <td id="sub_cancel_reason" class="<?php  ?>" >
                                      <span><?php echo $subscription->source;   ?></span>
                                 </td>
                                 <td class="action" id="<?php echo $subscription_id;  ?>">
                                      <a class="actions" href"">Open
                                      </a>
                                 </td>
                            </tr>
                            <?php endforeach; ?>
                       </tbody>
                  </table>
                  </div>
                  <br />
                  <?php
                        }
                    ?>

                  <script>
                  jQuery(document).ready(function($) {
                       $('.action').live('click', function() {
                            subscription_id = $(this).attr("id");
                            order_email = $(this).find(".order_email").text();
                            exists = $("#failed_order_"+subscription_id);

                            $('[id^="failed_order_"]').remove();
                            if (exists.length == 0) {
                                 $(this).html("Cancel");
                                 $(this).parent().slideDown("slow").after('<tr id="failed_order_'+subscription_id+'"> \
                                                          <td colspan="7"> \
                                                               <div id=""> \
                                                                    <form name="form_failed_'+subscription_id+'" id="form_failed" action="" method="POST"> \
                                                                    <div class="sixcol-two" style="margin-bottom:0% !important;"> \
                                                                         <select style="width:50%" name="disposition" id="disposition" required="required"> \
                                                                              <option value=""              > - - Outcome - - </option> \
                                                                              <option value="note"          >Note</option> \
                                                                              <option value="email"         >Email</option> \
                                                                              <option value="none"          >None</option> \
                                                                              <option value="remove"        >Remove</option> \
                                                                         </select> \
                                                                    </div> \
                                                                     <div class="sixcol-three" style="margin-bottom:0% !important;"> \
                                                                         <textarea name="email_content" id="email" rows="10" cols="50"></textarea> \
                                                                    </div> \
                                                                    <div class="sixcol-one last" style="margin-bottom:0% !important;"> \
                                                                         <input name="sub" id="submit_reactivation" type="submit" value="submit" /> \
                                                                    </div> \
                                                                         <input type="hidden" name="subscription_id" value="'+subscription_id+'" /> \
                                                                         <input type="hidden" name="user" value="<?php echo $current_user->data->display_name; ?>" /> \
                                                                    </form> \
                                                               </div> \
                                                          </td> \
                                                     </tr> \
                                                               ');
                            }else {
                                 $("#failed_order_"+subscription_id).remove();
                                 $(this).html('<a class="actions">Open</a>');
                            }
                       });
                       $("#form_failed").live('submit', function(event) {
                            $('#submit_reactivation').attr('disabled', true).val('Wait...');
                            event.preventDefault();
                            console.log("Form submitted");
                            safeUrl = <?php echo json_encode(plugins_url( 'manage-subscriptions/js/ajax/' . $this->portal_type . '-ajax.php' )); ?>;
                            $.ajax({
                                 type: 'POST',
                                 url: safeUrl,
                                 data: $(this).serialize(),
                                 dataType: 'JSON'
                            })
                            .done(function(data) {
                                 console.log(data);
                                 $('#row-'+subscription_id).remove();
                                 $('#failed_order_'+subscription_id).remove();
                                 $('div#copyright').replaceWith('<div id="copyright" class="col-left"><h2>Order #'+subscription_id+' Finished</h2></div>');
                                 $('#copyright').delay(3000).fadeTo(3000, 0.01);
                            });
                       });
                       $('#disposition').live('change', function(event) {
                            first_name = $('#first_name_'+subscription_id).html();
                            if ($('#after_disposition').length > 0) {
                                 $('#after_disposition').remove();
                            }

                            switch ($(this).val()) {
                                 case "note":
                                      $('#email').show().val('').prop("placeholder" , 'Insert note regarding call outcome . . . ');
                                 break;

                                 case "email":
                                      $('#email').val('Hi '+first_name+',\n\nMy name is <?php echo $current_user->data->display_name; ?> with <?php echo get_option('blogname') ?>. I tried giving you a call earlier today, but seemed to have missed you.\n\nAnyway, I was calling to let you know about our referral program, where you get $20 for each person you refer for a subscription. They simply include your name on checkout, and we take care of the rest. They get $20 off their first order, and you get $20 off your next shipment.\n\nIn the meantime, we appreciate that you have chosen us as your coffee provider! Thank you and from all of us here at <?php echo get_option('blogname') ?>, have a great day.\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 4PM PST');
                                 break;

                                 case "none":
                                      $('#email').hide('');
                                 break;

                                 case "remove":
                                      $('#email').show().val('Removed from call list after Referral Call');
                                 break;

                                 default: "";
                                 break;
                            }
                       });

                        $('#sub_phone').live("click", function() {
                            extension = '<?php echo get_user_meta(wp_get_current_user()->ID, "phone_extension", true); ?>';
                            number = $(this).text();
                            caller_name = $(this).attr('data-caller-name');
                            $.getJSON("http://sip.camanoislandmanagement.com/call.php?exten="+extension+"&number="+number+"&caller_name="+caller_name+"", function() {});
                        });

                  });
             </script>
             <?php }
         }
     }
