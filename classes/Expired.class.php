<?php
    if (!class_exists('Expired')) {
        class Expired {
            public function __construct() {
                $this->portal_type = $GLOBALS['manage_subscriptions']->portal_type;
                $this->data_type = "user";

                $this->display = array(
                    'customer_user'         => 'User',
                    'id'                    => 'ID',
                    'order_date'            => 'Date',
                    'billing_first_name'    => 'First Name',
                    'billing_last_name'     => 'Last Name',
                    'billing_email'         => 'Email',
                    'billing_phone'         => 'Phone',
                    'billing_phone'         => 'Phone',
                    '_wc_authorize_net_cim_credit_card_card_type'           => 'Card Type',
                    '_wc_authorize_net_cim_credit_card_card_expiry_date'    => 'Expires',
                    '_wc_authorize_net_cim_credit_card_account_four'        => 'Last Four'
                );
            }


            function get_script() {
                echo "Hello Script!";
            }

            function get_orders() {
                echo "Hello Orders!";
            }

            function get_survey() {
                echo "Hello Survey!";
            }

            function get_new_table() {
                $data = Manage_Subscriptions::get_data($this->portal_type);

                foreach ($this->display as $slug => $name) {
                    echo $slug . ' - - ' . $name;
                    echo "<br />";
                }
            }

            function get_customers() {
                global $current_user;
                $expired_user_ids = Manage_Subscriptions::get_data($this->portal_type);

                if (!$expired_user_ids) echo "<h1>No Expired Cards with current settings</h1><a target='_blank' href='" . site_url('wp-admin/admin.php?page=manage_subscriptions') . "'>Edit Settings</a><br /><br />";
                  else {
                ?>
                <div id="data-table">
                    <table class="portal_table" width="100%" cellpadding="3" cellspacing="4">
                        <thead>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Auth Profile</th>
                            <th>Active Card</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                        <?php

                        foreach ($expired_user_ids as $user[0] => $user_id) :

                            $_user = get_user_by("id", $user_id);
                            $profile_id = get_user_meta($user_id, '_wc_authorize_net_cim_profile_id', TRUE);
                            $payment_profiles = get_user_meta($user_id, '_wc_authorize_net_cim_payment_profiles', TRUE);

                        ?>
                            <tr>
                                <td id="sub_id"><a href="<?php echo get_option('siteurl') ?>/wp-admin/user-edit.php?user_id=<?php echo $_user->ID ?>" target="_blank"><?php echo $_user->ID ?></a></td>
                                <td id="sub_name"><?php echo $_user->display_name ?></td>
                                <td class="sub_email" id="sub_email"><?php echo $_user->user_email ?></td>
                                <td id="sub_phone"><span onclick="this.focus();this.select();"><?php echo $_user->billing_phone ? str_replace(array('-', '.', ' ', '(', ')'), '', $_user->billing_phone) : str_replace(array('-', '.', ' '), '', $_user->shipping_phone) ?></span></td>
                                <td id="sub_cancel_date"><?php echo $profile_id ?></td>
                                <td id="sub_cancel_reason" class="<?php echo $failed_reason ?>">
                                        <?php foreach ($payment_profiles as $profile_id => $_profile) :
                                                if ($_profile['active']) :
                                                        echo substr($_profile['type'],0,1) . ' <b><u>' . $_profile['last_four'] . '</u></b> ' . $_profile['exp_date'];
                                                endif;
                                              endforeach;
                                        ?>
                                <td class="reactivate" id="<?php echo $_order->id ?>"><a class="actions" href"">Open</a></td>
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
                        $('.reactivate').live('click', function() {
                            sub_id = $(this).attr("id");
                            sub_email = $(this).find(".sub_email").text();
                            console.log(sub_email);
                            exists = $("#reactivate_customer_"+sub_id);

                            $('[id^="reactivate_customer_"]').remove();
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
                                $(this).html('<a class="actions">Open</a>');
                            }
                        });
                        $("#form_reactivate").live('submit', function(event) {
                            event.preventDefault();
                            console.log("Form submitted");
                            safeUrl = <?php echo json_encode(plugins_url( 'manage-subscriptions/js/ajax/expired-ajax.php' )); ?>;
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
                    });

                </script>
            <?php
            }
        }
    }
