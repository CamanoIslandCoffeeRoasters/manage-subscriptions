<?php
    if (!class_exists("Reactivate")) {
        class Reactivate  {

            public function __construct($parent_class) {

                // Keep the parent class here so we can use it to grab some global data
                $this->parent = $parent_class;
                // Keep track of the portal type executed in the parent, which is obvious here by the file we're in
                $this->portal_type = $this->parent->portal_type;
                // Set the data type so we know how to get data out of it
                $this->data_type = "subscription";
                // Get the class to display the rows
                $this->display = new Display($this->parent, $this->data_type);
                // Build array of cells, columns, and rows to handle, and whether or not to use them as column headers
                // Simply add a column_name from the database table to have it included, then customize how the column is displayed in Display.class.php
                $this->display_data = array(
                    'subscription_id'   => array('name' => 'ID',                'column' => true),
                    'cancel_date'       => array('name' => 'Cancel Date',       'column' => true),
                    'name'              => array('name' => 'Name',              'column' => true),
                    'email'             => array('name' => 'Email',             'column' => true),
                    'phone'             => array('name' => 'Phone',             'column' => true),
                    'cancel_reason'     => array('name' => 'Reason',            'column' => true),
                    'reactivate'        => array('name' => 'Reactivate',        'column' => true),
                    'actions'           => array('name' => 'Actions',           'column' => true)
                );

            }

        	function get_script() {
                global $current_user;
                $woo_options = get_option('woo_options');
                ?>
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
        	     <div>
        		   <iframe src="https://docs.google.com/a/camanoislandmanagement.com/forms/d/1ZD01yOdEPY_43oJsg8Zmaf2ze3QvdFXVD_YPHkJ6wA0/viewform?embedded=true" width="900" height="1200" frameborder="0" marginheight="0" marginwidth="0">Loading...</iframe>
        		</div>
        		<?php
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

                           if (exists.length == 0) {
                               row = $(this).parent();
                               email = $(this).parent().find(".row_email").text();
                               console.log(email);
                               $('span.actions', this).removeClass('open').addClass('close').text("Cancel");
                               $(this).parent().after('<tr id="row_'+row_id+'" data-row-id="'+row_id+'"> \
                                                          <td colspan="7"> \
                                                               <div id="'+row_id+'"> \
                                                                    <form name="form_row_'+row_id+'" id="form_row" action="" method="POST"> \
                                                                    <div class="sixcol-two" style="margin-bottom:0% !important;"> \
                                                                         <select style="width:50%" name="disposition" id="disposition" required="required"> \
                                                                              <option value="">             Reactivated? </option> \
                                                                              <option value="yes">          Yes</option> \
                                                                              <option value="moreinfo">     More Info</option> \
                                                                              <option value="callback">     Callback</option> \
                                                                              <option value="no">           No</option> \
                                                                              <option value="remove">       No - Remove</option> \
                                                                              <option value="noanswer">     No Answer</option> \
                                                                              <option value="unreachable">  Unreachable</option> \
                                                                         </select> \
                                                                    </div> \
                                                                     <div class="sixcol-three" style="margin-bottom:0% !important;"> \
                                                                         <textarea name="email_content" id="email" rows="15" cols="50"></textarea> \
                                                                    </div> \
                                                                    <div class="sixcol-one last" style="margin-bottom:0% !important;"> \
                                                                         <input name="sub" id="submit_reactivation" type="submit" value="submit" /> \
                                                                    </div> \
                                                                         <input type="hidden" name="subscription_id" value="'+row_id+'" /> \
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
                            row_id = ($(this).parents('tr').attr('data-row-id'));
                            console.log(row_id);
                            first_name = $('#first_name_'+row_id).html();

                            if (($('#after_disposition').length > 0) ||  ($('#contact_callback').length > 0)) {
                                 $('#after_disposition').remove();
                                 $('#contact_callback').remove();
                            }

                            switch ($(':selected', this).val()) {
                                 case "yes":
                                      $('#email').html('It was great to chat with you today. I\'m so glad we could get you all set up with the Coffee Lover\'s Club again.\n\nJust to review, we\'ve got you in the %SUBSCRIPTION_TYPE%, set to ship out on %SHIPDATE% and with a frequency of %FREQUENCY% weeks. Like I said on the phone, your first order will be $%DISCOUNT% off. After that, your subsequent shipments will be the regular club price of $%SUBSCRIPTION_PRICE%.\n\nWe\'re so glad to have you back in the Coffee Lover\'s Club. If you have any further questions or needs, please give our Customer Care department a call at <?php echo $woo_options['woo_contact_number']; ?>, or you can make edits to your subscription at this url: <?php echo site_url( '/my-account' ); ?> .\n\nThank you for choosing to make a difference with your daily cup of coffee.\n\nSincerely,\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday, 7AM - 5PM PST');
                                      $('#disposition').after('<div id="after_disposition"><br /> \
                                                                    <div class="" style="margin-bottom:0% !important;"> \
                                                                         <input type="date" name="next_shipment" id="next_shipment" value="<?php echo date("Y-m-d", strtotime("+1 day")); ?>" /> \
                                                                         <input type="number" name="one_time_discount" step="any" id="one_time_discount" placeholder="Add discount?"  value="" /> \
                                                                    </div> \
                                                               </div> \
                                                               ');
                                 break;

                                 case "moreinfo":
                                      $('#email').val('Thank you for taking my call today. We know how valuable your time is and appreciate that you took a few minutes to give us some feedback. Per your request for more information, I’ve included the details of our Coffee Lover’s Club below.\n\nWe offer three types of memberships in the Coffee Lover’s Club. The 2lb Club, which is $34.99 per shipment. The 3lb Club, which is $44.99 per shipment. And the 4lb Club, which is $52.99 per shipment.\n\nAs a member you’ll still enjoy these member-only perks:\n\n~ Free Shipping\n~ Custom Shipping Frequency from 2 - 10 weeks\n~ Access to ALL of our Freshly Roasted, Organic, Shade-Grown Coffees\n~ Club price discount.\n\nIf you decide you would like to rejoin, please feel free to give us a call at our customer care number: <?php echo $woo_options['woo_contact_number']; ?>.  Be sure to mention the 50% off discount we discussed on the phone, and we will gladly honor it for you.\n\nFrom all of us here at <?php echo get_option('blogname') ?>,\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 5PM PST');
                                 break;

                                 case "callback":
                                      $('#email').val('Thank you for taking my call today. We know how valuable your time is and appreciate that you took a few minutes to give us some feedback. Per your request for more information, I’ve included the details of our Coffee Lover’s Club below.\n\nWe offer three types of memberships in the Coffee Lover’s Club. The 2lb Club, which is $34.99 per shipment. The 3lb Club, which is $44.99 per shipment. And the 4lb Club, which is $52.99 per shipment.\n\nAs a member you’ll still enjoy these member-only perks:\n\n~ Free Shipping\n~ Custom Shipping Frequency from 2 - 10 weeks\n~ Access to ALL of our Freshly Roasted, Organic, Shade-Grown Coffees\n~ Club price discount.\n\nIf you decide you would like to rejoin, please feel free to give us a call at our customer care number: <?php echo $woo_options['woo_contact_number']; ?>.  Be sure to mention the 50% off discount we discussed on the phone, and we will gladly honor it for you.\n\nFrom all of us here at <?php echo get_option('blogname') ?>,\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 5PM PST');
                                      $('#disposition').after('<div id="contact_callback"> \
                                                                  <br /><br /> \
                                                                  <input type="text" class="date-picker" name="contact_callback" value="<?php echo date('m/d/Y',strtotime('+ 1 week')); ?>" > \
                                                               </div> \
                                                              ');
                                      $('.date-picker').datepicker({numberOfMonths:[1,1]});
                                 break;

                                 case "no":
                                      $('#email').val('Thank you for taking my call today. We know how valuable your time is and appreciate that you took a few minutes to give us some feedback.\n\nIf you decide you would like to rejoin, please feel free to give us a call at our customer care number: <?php echo $woo_options['woo_contact_number']; ?>.  Be sure to mention the 50% off discount we discussed on the phone, and we will gladly honor it for you.\n\nFrom all of us here at <?php echo get_option('blogname') ?>,\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 5PM PST');
                                 break;

                                 case "remove":
                                      $('#email').val('Thank you for taking my call today. We know how valuable your time is and appreciate that you took a few minutes to give us some feedback.\n\nWe have removed you from our call list. You will not receive any more calls unless you decide to rejoin the Coffee Lover’s Club.\n\nIf you decide you would like to rejoin, please feel free to give us a call at our customer care number: <?php echo $woo_options['woo_contact_number']; ?>.\n\nBe sure to mention the 50% off discount we discussed on the phone, and we will gladly honor it for you.\n\nFrom all of us here at <?php echo get_option('blogname') ?>,\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 5PM PST');
                                 break;

                                 case "noanswer":
                                      $('#email').val('My name is <?php echo $current_user->data->display_name; ?> with <?php echo get_option('blogname') ?>. I tried giving you a call earlier today, but seemed to have missed you.\n\nAnyway, I was calling to let you know about a special offer we’re extending to all of our former Coffee Lovers Club members. We’d love to have you back. To show you how much we want to give you 50% off your next shipment when you rejoin the Coffee Lover’s Club.\n\nJust give us a call back at our customer care number <?php echo $woo_options['woo_contact_number']; ?> and be sure to mention the 50% off rejoin discount.\n\nThank you and from all of us here at <?php echo get_option('blogname') ?>, have a great day.\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 5PM PST');
                                 break;

                                 case "unreachable":
                                      $('#email').val('My name is <?php echo $current_user->data->display_name; ?> with <?php echo get_option('blogname') ?>. I tried giving you a call earlier today, but seemed to have missed you.\n\nAnyway, I was calling to let you know about a special offer we’re extending to all of our former Coffee Lovers Club members. We’d love to have you back. To show you how much we want to give you 50% off your next shipment when you rejoin the Coffee Lover’s Club.\n\nJust give us a call back at our customer care number <?php echo $woo_options['woo_contact_number']; ?> and be sure to mention the 50% off rejoin discount.\n\nThank you and from all of us here at <?php echo get_option('blogname') ?>, have a great day.\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 5PM PST');
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
 ?>
