<?php
    if (!class_exists("Display")) {
        class Display {

            public function __construct($parent, $portal_type) {
                $this->parent = $parent;
                $this->portal_type = $portal_type;
            }

            function get_cell($row_data, $slug) {
                $cell = '';
                switch ($this->portal_type) {
                    // Controls for displaying data in an order
                    case 'order':

                        switch ($slug) {

                            case 'id':
                                $cell .= "<td>";
                                $cell .= sprintf('<a href="%s/wp-admin/post.php?action=edit&post=%d" target="_blank">%d</a>', get_option('siteurl'), $row_data->id, $row_data->id);
                                $cell .= "</td>";
                            break;

                            case 'order_date':
                                $cell .= "<td>";
                                $cell .= date("m/d/Y", strtotime($row_data->$slug));
                                $cell .= "</td>";
                            break;

                            case 'customer_user':
                                $name = "<span id='first_name_{$row_data->id}'>{$row_data->shipping_first_name}</span>&nbsp;<span id='last_name_{$row_data->id}'>{$row_data->shipping_last_name}</span>";
                                $cell .= "<td>";
                                if ($is_subscription = get_post_meta($row_data->id, 'subscription_id', true)) {
                                    $cell .= Subscriptions_Subscribers::subscription_link($is_subscription, $row_data->billing_email, $name, TRUE);
                                }else {
                                    $cell .= $name;
                                }
                                $cell .= "</td>";
                            break;

                            case 'billing_email':
                                $cell .= "<td class='row_email'>";
                                $cell .= $row_data->$slug;
                                $cell .= "</td>";
                            break;

                            case 'billing_phone':
                                $cell .= "<td class='row_phone' data-caller-name='{$row_data->billing_first_name} {$row_data->billing_last_name}'>";
                                $cell .= ($row_data->$slug) ? str_replace(array('-', '.', ' ', '(', ')'), '', $row_data->$slug) : str_replace(array('-', '.', ' ', '(', ')'), '', get_user_meta($row_data->customer_user, "billing_phone", TRUE));
                                $cell .= "</td>";
                            break;

                            case 'reason':
                                // Add CSS class for tpye of failed order
                                $cell .= "<td class='{$this->parent->get_failed_reason($row_data->id)}'>";
                                // Make sure there is an expiration date before parsing it
                                if (!empty($row_data->wc_authorize_net_cim_credit_card_card_expiry_date)) {
                                    $expires = explode('-', $row_data->wc_authorize_net_cim_credit_card_card_expiry_date);
                                    $cell .= sprintf("%s %s<br />", $row_data->wc_authorize_net_cim_credit_card_card_type, $expires[1] .'/' . $expires[0]);
                                    $cell .= sprintf("****%s", $row_data->wc_authorize_net_cim_credit_card_account_four);
                                }else {
                                    $cell .= "No card";
                                }
                                $cell .= "</td>";
                            break;

                            case 'actions':
                                $cell .= "<td class='action' id='{$row_data->id}'>";
                                $cell .= "<span class='actions open'>Open</span>";
                                $cell .= "</td>";
                            break;


                            default: $cell = "<td>{$row_data->$slug}</td>";
                            break;
                        } // end switch $slug

                    break;
                    // Controls for displaying data in a subscription
                    case 'subscription':
                    $user_id = get_user_by("email", $row_data->email)->ID;
                        switch ($slug) {
                            case 'subscription_id':
                                $subscription_link = Subscriptions_Subscribers::subscription_link($row_data->subscription_id, $row_data->email, '', false);
                                $cell .= "<td>";
                                $cell .= sprintf("<a href='%s' target='_blank'>%s</a>", $subscription_link, $row_data->$slug);
                                $cell .= "</td>";
                            break;

                            case 'email':
                                $cell .= "<td class='row_$slug'>";
                                $cell .= $row_data->$slug;
                                $cell .= "</td>";
                            break;

                            case 'name':
                                $subscription_name = explode(" ", $row_data->name);
                                $name = "<span id='first_name_{$row_data->subscription_id}'>$subscription_name[0]</span>&nbsp;<span id='last_name_{$row_data->subscription_id}'>$subscription_name[1]</span>";
                                $cell .= "<td>";
                                $cell .= Subscriptions_Subscribers::subscription_link($row_data->subscription_id, $row_data->email, $name, TRUE);
                                $cell .= "</td>";
                            break;

                            case 'phone':
                                $cell = "<td class='row_phone' data-caller-name='{$row_data->name}'>";
                                $cell .= ($phone = get_user_meta($user_id, 'billing_phone', true)) ? str_replace(array('-', '.', ' ', '(', ')'), '', $phone) : '';
                                $cell .= "</td>";
                            break;

                            case 'cancel_date':
                                $cell .= "<td>";
                                $cell .= date("m/d/Y", strtotime($row_data->$slug));
                                $cell .= "</td>";
                            break;

                            case 'reactivate':
                                $cell .= "<td>";
                                $cell .= (isset($row_data->meta->$slug)) ? 'Previously Reactivated' : "not reactivated";
                                $cell .= "</td>";
                            break;

                            case 'actions':
                                $cell = "<td class='action' id='{$row_data->subscription_id}'>";
                                $cell .= "<span class='actions open'>Open</span>";
                                $cell .= "</td>";
                            break;

                            default: $cell = "<td>{$row_data->$slug}</td>";
                            break;
                        }
                    break;
                    // Controls for displaying data in a user
                    case 'user':
                        switch ($slug) {
                            case 'id':
                                    $cell .= "<td>";
                                    $cell .= $row_data->$slug;
                                    $cell .= "</td>";
                            break;

                            default: $cell = "<td>{$row_data->$slug}</td>";

                        }
                    break;

                } // end switch $data_type
                return $cell;
            } // End get_cell()
        } // End class
    } // end if class_exists
 ?>
