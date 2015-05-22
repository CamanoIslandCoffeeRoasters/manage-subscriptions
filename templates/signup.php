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
        
        <div class="twocol-one" ><h2 style="color:red;">Script</h2>
            <li>Hi, is this <b>customer name</b>?</li>
            <li>Hi, this is <b><?php echo $current_user->data->display_name; ?></b> with <?php echo get_option('blogname') ?>. How are you doing today?</li>
            <li><b>Wait for reply</b></li>
            <li>I just wanted to give you a quick call to say thank you. Thank you for joining our Coffee Lovers Club and thank you for drinking our coffee. We really appreciate you.</li> 
            <li>Thanks to coffee lovers like you we're able to help our farmers work they way out of poverty. You truly are making a difference, so thank you.</li> 
            <li>Also we wanted to make sure you know about our new streamlined referral program. For every friend you refer to us we'll give $20 off their first shipment and $20 off your next shipment when you're friend joins.</li> 
            <li>It's really easy too. All they have to do is enter your name when they signup and we'll do the rest.</li>  
            <li>Thank you again for drinking our coffee and for choosing to make a difference with your daily cup of coffee.</li>       
        </div>
        
        <div class="twocol-one last">
            <h2 style="color:green;">Voicemail</h2>
            <li>This is a message for <b>customer name</b>.</li> 
            <li>Hi, this is <b><?php echo $current_user->data->display_name; ?></b> with <?php echo get_option('blogname') ?>.</li> 
            <li>I just wanted to give you a quick call to say thank you for joining our Coffee Lovers Club and thank you for drinking our coffee. We really appreciate you. Thanks to coffee lovers like you we're able to help our farmers work they way out of poverty. You truly are making a difference, so thank you.</li>
            <li>Also we wanted to make sure you know about our new streamlined referral program. For every friend you refer to us we'll give $20 off their first shipment and $20 off your next shipment when you're friend joins.</li>
            <li>It's really easy too. All they have to do is enter your name when they signup and we'll do the rest.</li>
            <li>If you would like to know more about how it works please feel free to give us a call at <?php echo $woo_options['woo_contact_number']; ?></li>
            <li>Thanks again, and have a great day.</li>
        </div>

        <?php
    }
    
    function get_orders($portal_type = "") {
        echo "Hello Orders!";
    }
    
    function get_survey($portal_type = "") {
        echo "Hello Survey!";
    }

function signup_table($portal_type) {
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
                <th>Source</th>
                <th>Actions</th>
            </thead>
            <tbody id="table-body">
            <?php
    
            foreach ($order_ids as $order_id) :
                
                $subscription_id = get_post_meta($order_id, "subscription_id", TRUE);
                $subscription = Subscriptions_Subscribers::get_subscription("", $subscription_id);
                
                $_order = new WC_Order($order_id);
        
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
                    <td id="order_email">
                        <span class="order_email"><?php echo $_order->billing_email ?></span>
                    </td>
                    <td id="sub_phone" data-caller-name="<?php echo $subscription->name ?>">
                        <span><?php echo $_order->billing_phone ? str_replace(array('-', '.', ' ', '(', ')'), '', $_order->billing_phone) : str_replace(array('-', '.', ' ', '(', ')'), '', get_user_meta($_order->customer_user, "billing_phone", TRUE)) ?></span>
                    </td>
                    <td id="sub_cancel_date">
                        <?php echo date('Y-m-d', strtotime($_order->order_date)) ?>
                    </td>
                    <td id="sub_cancel_reason" class="<?php echo $failed_reason ?>" >
                        <span title="subscription_source"><?php echo $subscription->source ?></span>
                    </td>
                    <td class="action" data-order-id="<?php echo $_order->id ?>" data-subscription-id="<?php echo $subscription_id ?>">
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
                order_id = $(this).attr("data-order-id");
                subscription_id = $(this).attr("data-subscription-id");
                order_email = $(this).parent().find(".order_email").text();
                exists = $("#failed_order_"+order_id);
                console.log("Email: "+order_email);
                console.log("Order #"+order_id);
                console.log("Subscription #"+subscription_id);
                
                if (exists.length == 0) {
                    $(this).html("Cancel");
                    $(this).parent().after('<tr id="failed_order_'+order_id+'"> \
                                                <td colspan="7"> \
                                                    <div id=""> \
                                                        <form name="form_failed_'+order_id+'" id="form_failed" action="" method="POST"> \
                                                        <div class="sixcol-two" style="margin-bottom:0% !important;"> \
                                                            <select style="width:100%" name="disposition" id="disposition" required="required"> \
                                                                <option value=""> -- SELECT OUTCOME -- </option> \
                                                                <option value="voicemail">Left Voicemail</option> \
                                                                <option value="completed">Completed</option> \
                                                                <option value="moreinfo">More Info</option> \
                                                            </select> \
                                                        </div> \
                                                        <div class="sixcol-three" style="margin-bottom:0% !important;"> \
                                                            <textarea name="email_content" id="email" rows="15" cols="50"></textarea> \
                                                        </div> \
                                                        <div class="sixcol-one last" style="margin-bottom:0% !important;"> \
                                                            <input name="sub" id="submit_reactivation" type="submit" value="submit" /> \
                                                        </div> \
                                                            <input type="hidden" name="order_email" value="'+order_email+'" /> \
                                                            <input type="hidden" name="order_id" value="'+order_id+'" /> \
                                                            <input type="hidden" name="subscription_id" value="'+subscription_id+'" /> \
                                                            <input type="hidden" name="user" value="<?php echo $current_user->data->display_name; ?>" /> \
                                                        </form> \
                                                    </div> \
                                                </td> \
                                            </tr> \
                                            ');
                }else {
                    $("#failed_order_"+order_id).remove();
                    $(this).html('<a class="actions">Open</a>');
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
                    case "completed":
                        $('#email').val("Dear "+first_name+",\n\nThank you for taking my call today, and for drinking our coffee. I also wanted to give you a quick reminder about our new streamlined referral program. For every friend you refer to us we will give them $20 off their first Coffee Lovers Club shipment and $20 off your next Coffee Lovers Club shipment when your friend joins.\n\nIt is really easy too. All they have to do is enter your name when they signup and we will do the rest. Here is a link to learn more about the program: <?php echo site_url('/referral-learn-more') ?> \n\nThank you again for drinking our coffee and for choosing to make a difference with your daily cup of coffee.\n\nSincerely,\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 5PM PST");
                    break;                    
                    
                    case "voicemail":
                        $('#email').val("Hi "+first_name+",\n\nMy name is <?php echo $current_user->data->display_name; ?> with <?php echo get_option('blogname') ?>. I left you a quick voicemail earlier today just to thank you for joining our Coffee Lovers Club and thank you for drinking our coffee. We really appreciate you.\n\nThank you for choosing to make a difference with your daily cup of coffee. Thanks to coffee lovers like you, we’ve been able to help build 42 villages and impact 24,000 people with just your daily cup of coffee. You can buy your coffee anywhere, but with <?php echo get_option('blogname') ?> you’re deciding to make a difference with your coffee.\n\nI also wanted to let you know about our new streamlined referral program. For every friend you refer to us we will give them $20 off their first Coffee Lovers Club shipment and $20 off your next Coffee Lovers Club shipment when your friend joins.\n\nIt is really easy too. All they have to do is enter your name when they signup and we will do the rest.\n\nHere is a link to learn more about the program: <?php echo site_url('/referral-learn-more') ?>\n\nThank you again for drinking our coffee and for choosing to make a difference with your daily cup of coffee.\n\nSincerely,\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 5PM PST");
                    break;
                    
                    case "moreinfo":
                        $('#email').val("Hi "+first_name+",\n\nThank you for taking my call today, and for drinking our coffee. We really appreciate you. As requested here is the link to learn more about our new streamlined referral program: <?php echo site_url('/referral-learn-more') ?>. For every friend you refer to us we will give them $20 off their first Coffee Lovers Club shipment and $20 off your next Coffee Lovers Club shipment when your friend joins.\n\nIt is really easy too. All they have to do is enter your name when they signup and we will do the rest.\n\nThank you again for drinking our coffee and for choosing to make a difference with your daily cup of coffee.\n\nFrom all of us here at <?php echo get_option('blogname') ?>,\n<?php echo $current_user->data->display_name; ?>\nPhone: <?php echo $woo_options['woo_contact_number']; ?>\nMonday - Friday 7AM - 5PM PST");
                    break;

                    
                    default: "";
                    break;
                }
            });
            
            $('#sub_phone').live("click", function() {
                extension = '<?php echo get_user_meta(wp_get_current_user()->ID, "phone_extension", true); ?>';
                number = $(this).text();
                caller_name = $(this).attr('data-caller-name');
                $.getJSON("http://199.195.146.28/call.php?exten="+extension+"&number="+number+"&caller_name="+caller_name+"", function() {});
            });
            
            
            $('#holiday-banner').hide();
            $('#footer-widgets-container').hide();
            $('.breadcrumb-trail').hide();
            $('.woo-breadcrumbs').hide();
            $('.breadcrumb').css({'border-bottom':' !important'});
            $('.menu-item').hide();
            $('.cart').hide();            
        });
        
    </script>