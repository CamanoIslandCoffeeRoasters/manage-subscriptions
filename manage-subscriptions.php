<?php
/*
Plugin Name: Manage Subscriptions
Description: Customized Management of CBW Subscriptions, i.e. reactivations, expired credit cards, cold-calling, etc.
Author: tobinfekkes
Author URI: http://tobinfekkes.com
Version: 3.0.0
*/

define( 'MANAGE_SUBSCRIPTIONS', untrailingslashit( plugin_dir_path( __FILE__ ) ));

$plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
require($plugin_path. '/classes/Reactivate.class.php');
require($plugin_path. '/classes/Failed.class.php');
require($plugin_path. '/classes/Referral.class.php');
require($plugin_path. '/classes/Expired.class.php');
require($plugin_path. '/classes/Signup.class.php');
require($plugin_path. '/classes/Display.class.php');

if (!class_exists("Manage_Subscriptions")) {
    class Manage_Subscriptions {

        // @var Array of all available portal types
        var $portal_types;
        // @var string Keep track of which type of portal we're on
        var $portal_type;
        // @var array Populate navigational buttons
        var $navigation;
        // @var array Populate settings per user from the Manage Subscriptions Admin Options page
        var $settings;
        // @var int Keep traack of the logged-in user to handle their individual settings for display and emails etc
        var $user_id;


        public function __construct() {

            add_action('admin_init', array($this, 'register_manage_subscriptions_settings'));

            add_action('admin_menu', array($this, 'manage_subscriptions_admin'));

            add_action( 'the_post', array($this, 'woocommerce_loaded' ) );

            add_action('plugins_loaded', array($this, 'manage_subscriptions_init'));

            add_shortcode('manage_subscriptions_portal', array($this, 'manage_subscriptions_portal_shortcode'));
            // Set up the global portal types. These are not necessarily true for each user, but they're available. They can be disabled in the admin
            $this->init_portal_types = array( 'reactivate'  =>  'Reactivations',
                                              'failed'      =>  'Failed Orders',
                                              'expired'     =>  'Expired Cards',
                                              'referral'    =>  'Referrals',
                                              'signup'      =>  'Signups',
                                          );
            // Set up the global navigation options. These are not necessarily true for each user, but they're available. They can be disabled in the admin
            $this->init_navigations = array( 'customers'    => 'Customers',
                                             'script'       => 'Script',
                                             'survey'       => 'Survey'
                                         );
            // Default to false, so it's only displayed to someone who's logged in and has admin privileges
            $this->has_access = false;
        }

        public function manage_subscriptions_init() {
            // Get the logged in user, if it exists
            $this->user_id = get_current_user_id();
            // Make sure to only run this
            if (is_super_admin($this->user_id)) {
                // Initiate all the variables associated with this user, saved in the admin options
                $this->settings = get_option("manage_subscriptions_{$this->user_id}");
                // Make sure this user has portal settings before trying to load them
                if (isset($this->settings['portal_types'])) {
                    // Get the portal type buttons to display for this user
                    $this->portal_types = isset($this->settings['portal_types']) ? $this->settings['portal_types'] : '';
                    // Get the navigational buttons to display for this user
                    $this->navigations = isset($this->settings['navigations']) ? $this->settings['navigations'] : '';
                    // Find out which portal we're on, and if it's not set, set a default one
                    $this->portal_type = isset($_GET['portal_type']) ? $_GET['portal_type'] : current(array_keys($this->portal_types));
                    // Our classes are stored as proper case, so we have to capitalize the first character to match the class
                    $portal_type = ucwords($this->portal_type);
                    // Initiate the right class, and ONLY the right class, that's being veiwed on this page load
                    $this->portal_class = new $portal_type($this);
                }
            }
        }


        public function manage_subscriptions_greeting() {
            global $current_user;
            if ( !empty($current_user->roles) ) :
                if ( ($current_user->roles[0] == 'administrator') ) :
                    if (empty($this->portal_types) || empty($this->navigations)) :
                        echo "<br />Please configure your settings in order to use this portal. <a target='_blank' href='" . site_url('wp-admin/admin.php?page=manage_subscriptions') . "'>Configure Settings</a><br /><br />";
                    else:
                        echo "Greetings and Salutations, " . $current_user->data->display_name;
                        $this->has_access = true;
                    endif;
                else: echo "You do not have sufficient permissions to view this page";
                endif;
            else: echo "You must be logged in to view this page. ";
            ?>
            <a href="<?php echo wp_login_url(site_url('/portal')) ?>">Login here</a>

            <?php endif;
        }


        public function manage_subscriptions_portal_types() {
            global $post; // to build the links for the page we're currently on, so that the shortcode can be used on any page

            $html = '';
            $i = 0;
            $count = count($this->portal_types);

            foreach ($this->portal_types as $key => $name) :
                $i++;
                $last = ($i == $count) ? "last" : "";

                $html .= sprintf('<a href="%s">', site_url() . '/' . $post->post_name .'/?portal_type='. $key);
                $html .= sprintf('<div class="%scol-one %s %s">', $this->int_to_word($count), $last, ($this->portal_type == $key) ? 'portal_type': 'subscriptions-nav');
                $html .= ucwords($name);
                $html .= '</div>';
                $html .= '</a>';
             endforeach;
             $html .= '<br /><br />';
             $html .= '<div style="clear:both;"></div>';
             return $html;
        }

        public function manage_subscriptions_navigation() {
            $html = '';
            $i = 0;

            $count = count($this->navigations);
            foreach ($this->navigations as $navigation => $navigation_name) :
                $i++;
                $last = ($i == $count) ? "last" : "";
                $html .= sprintf('<div class="%scol-one %s call-flow" id="%s">', $this->int_to_word($count),  $last, $navigation);
                $html .= sprintf('<h4>%s</h4>', $navigation_name);
                $html .= '</div>';
            endforeach;
            $html .= '<div style="clear:both;"></div>';

            return $html;
        }

        public function manage_subscriptions_content() {
            $html = '';
            $i = 0;

            foreach ($this->navigations as $navigation => $navigation_name) :
                $get_content = "get_" . $navigation;
                echo sprintf('<div id="%s_content" %s>', $navigation, ($i > 0) ? 'style="display:none;"' : '');
                echo $this->portal_class->$get_content();
                echo  '</div>';
                $i++;
            endforeach;

            //return $html;
        }


        function manage_subscriptions_portal_shortcode() {
            echo $this->manage_subscriptions_greeting();
            if ($this->has_access) {
                echo '<div id="subscriptions_container">';
                echo $this->manage_subscriptions_portal_types();
                echo $this->manage_subscriptions_navigation();
                echo $this->manage_subscriptions_content();
                echo '</div>';
                echo $this->portal_class->get_js();
                echo $this->clear_locked_ids();
            }
        }

        public function get_row_data_from_type($data_type, $id) {
            if (!$data_type || !$id) return;
            global $woocommerce;

            if ($data_type == "order") {
                return new WC_Order($id);
            }

            elseif ($data_type == "subscription") {
                return Subscriptions_Subscribers::get_subscription(NULL, $id);
            }

            elseif ($data_type == "user") {
                return new WP_User($id);
            }
        }

        public static function get_row_id($data_type, $data) {
            if (!$data_type || !$data) return;

            if ($data_type == "order") {
                return $data->id;
            }

            elseif ($data_type == "subscription") {
                return $data->subscription_id;
            }

            elseif ($data_type == "user") {
                return $data->ID;
            }
        }

        function get_table_header() {
            $columns = array();
            // Get the array from the construct and iterate through to keep only the ones that will be displayed as columns
            foreach ($this->portal_class->display_data as $slug => $item) {
                if ($item['column'] == true) {
                    // Save array of column slugs and names for use below in the display of the thead
                    $columns = array_merge( $columns, array($slug => $item['name']));
                }
            }
            return $columns;
        }

        function display_table() {
            // Initiate fresh variables for storing HTML
            $table = $table_head = $table_row = $table_cell = '';
            // Get the IDs of the data we're going to iterate through in our table
            $data = $this->get_data($this->portal_type);
            // Start the table, tbody, and thead
            $table_head .= '<table class="portal_table"><tbody><thead>';
            // Find out which elements in the array are marked to be displayed as columns, denoted by 'true' in the _construct array
            foreach($this->get_table_header() as $column => $name){
                $table_head .= "<th id='col_$column'>$name</th>";
            }
            // End thead
            $table_head .= "</thead>";
            // Add it to the parent HTML that will be returned
            $table .= $table_head;

            foreach ($data as $id) {
                // Get the array/object of data that will populate the row
                $row_data = $this->get_row_data_from_type($this->portal_class->data_type, $id);
                // Get the unique ID of the array/object so that the jQuery/AJAX can use it open/close rows and send data
                $id = $this->get_row_id($this->portal_class->data_type, $row_data);
                // Reset html
                $table_row = '';
                $table_cell = '';
                // Check to see if this is a callback row
                $callback = in_array($id, $this->callbacks) ? "class='contact_callback'" : "";
                // Start new unique row with ID
                $table_row .= "<tr id='$id' $callback>";
                foreach ($this->portal_class->display_data as $slug => $name) {
                    if ($name['column'] == true) {
                        // If this column display is set to true, get the cell for it
                        $table_cell .= $this->portal_class->display->get_cell($row_data, $slug);
                    }
                }
                // End the row
                $table_row .= $table_cell . "</tr>";
                // Add it to the parent table to be returned
                $table .= $table_row;
            }
            // Close the tbody and table
            $table .= '</tbody></table>';
            // Send it to the screen
            return $table;
        }


        public function get_data( $portal_type = "" ) {
            global $wpdb;
              $manage_subscriptions = get_option("manage_subscriptions_" . get_current_user_id());
              $manage_subscriptions_locked = get_option("manage_subscriptions_locked_{$portal_type}");
              $locked_ids = array();
              $portal_types = array_keys($this->init_portal_types);
              if ($manage_subscriptions_locked) {
                foreach ($manage_subscriptions_locked as $user_id => $locked) {
                  if ($user_id !=  wp_get_current_user()->ID) {
                      $locked_ids = array_merge($locked_ids, $locked);

                  }
                }
              }
              $locked_ids = (empty($locked_ids)) ?  "''" : implode(',', $locked_ids);

            switch ($portal_type) {
                // Reactivate
                case $portal_types[0]:
                    unset($portal_types[0]);
                    $data = $wpdb->get_col("SELECT subs.subscription_id
                                              FROM  " . $wpdb->prefix . "subscriptions subs
                                              WHERE subs.status = 'canceled'
                                              AND subs.cancel_reason != 'remove'
                                              AND subs.cancel_date < '". date('Y-m-d', strtotime('-' . $manage_subscriptions['cancel_date']. ' days')). "'
                                              AND ((subs.contact_last IS NULL)
                                              OR (DATE(subs.contact_last) < '" . date('Y-m-d', strtotime('-' . $manage_subscriptions['contact_last_subscription'].' days'))."'))
                                              AND subs.subscription_id NOT IN (" . $locked_ids . ")
                                              ORDER BY subs.cancel_date DESC
                                              LIMIT 0, ". $manage_subscriptions['num_rows'] . "");

                    $callbacks = $wpdb->get_col("SELECT subs.subscription_id
                                               FROM " . $wpdb->prefix . "subscriptions subs
                                               JOIN " . $wpdb->prefix . "subscriptionmeta meta
                                                  ON subs.subscription_id = meta.subscription_id
                                               WHERE meta.meta_key = 'contact_callback'
                                               AND DATE(meta.meta_value) < '" . date("Y-m-d") . "'
                                           ");

                    $this->callbacks = $callbacks;

                break;
                // Failed
                case $portal_types[1]:
                    unset($portal_types[1]);
                    $data = $wpdb->get_col("SELECT distinct(posts.ID)
                                                FROM {$wpdb->posts} posts
                                                LEFT JOIN {$wpdb->postmeta} meta
                                                     ON meta.post_id = posts.ID
                                                LEFT JOIN {$wpdb->postmeta} meta2
                                                     ON meta2.post_id = posts.ID
                                                WHERE posts.post_type = 'shop_order'
                                                AND posts.post_status = 'wc-failed'
                                                AND posts.ID NOT IN (" . $locked_ids . ")
                                                AND ((meta.post_id NOT IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'contact_last'))
                                                OR (meta.meta_key = 'contact_last'
                                                    AND ((meta2.post_id NOT IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'contact_amount'))
                                                OR (meta2.meta_key = 'contact_amount' AND meta2.meta_value <= 3 ))
                                                    AND DATE(meta.meta_value) < '" . date('Y-m-d', strtotime('-'.$manage_subscriptions['contact_last_order'].' days')). "'))
                                                ORDER BY posts.post_date DESC
                                                LIMIT 0, ". $manage_subscriptions['num_rows'] . "");

                    $callbacks = $wpdb->get_col("SELECT post_id
                                               FROM {$wpdb->postmeta} meta
                                               WHERE meta.meta_key = 'contact_callback'
                                               AND DATE(meta.meta_value) < '" . date("Y-m-d") . "'
                                           ");
                   $this->callbacks = $callbacks;
                break;
                // Expired
                case $portal_types[2]:
                    unset($portal_types[2]);
                    $complete_search = $this->check_expired_cards();

                    $data = $wpdb->get_col("SELECT user_id
                                                FROM {$wpdb->usermeta}
                                                WHERE meta_key = '_wc_authorize_net_cim_payment_profiles'
                                                AND user_id NOT IN (" . $locked_ids . ")
                                                AND meta_value REGEXP '$complete_search'
                                                LIMIT 0, ". $manage_subscriptions['num_rows'] . "");
                break;

                // Referral
                case $portal_types[3]:
                    unset($portal_types[3]);
                    $data = $wpdb->get_col("SELECT DISTINCT(subs.subscription_id)
                                                FROM {$wpdb->prefix}subscriptions subs
                                                WHERE subs.status = 'active'
                                                AND subs.subscription_id NOT IN (" . $locked_ids . ")
                                                AND subs.subscription_id NOT IN
                                                    (SELECT subscription_id FROM {$wpdb->prefix}subscriptionmeta WHERE meta_key = 'referral_contact')
                                                ORDER BY subs.subscription_start DESC
                                                LIMIT 0, ". $manage_subscriptions['num_rows'] . "");
                break;

                // Signup
                case $portal_types[4]:
                    unset($portal_types[4]);
                    $data = $wpdb->get_col("SELECT ID
                                                FROM {$wpdb->posts} posts
                                                JOIN {$wpdb->postmeta} meta
                                                    ON posts.ID = meta.post_id
                                                WHERE posts.post_type = 'shop_order'
                                                AND posts.ID NOT IN (" . $locked_ids . ")
                                                AND posts.post_status IN ('wc-processing', 'wc-completed')
                                                AND posts.ID NOT IN
                                                    (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'contact_new_signup')
                                                AND meta.meta_key = 'subscription_welcome_id'
                                                ORDER BY posts.post_date DESC
                                                LIMIT 0, ". $manage_subscriptions['num_rows'] . "");
                break;

                default: $data = "";
                break;
            }

            $data = isset($callbacks) ? $data = array_merge($callbacks, $data) : $data;

            foreach ($portal_types as $type) {

                $locked_options = array();

                $locked_options = get_option("manage_subscriptions_locked_{$type}");

                unset($locked_options[wp_get_current_user()->ID]);

                update_option("manage_subscriptions_locked_{$type}", $locked_options);
            }

            $manage_subscriptions_locked[wp_get_current_user()->ID] = array_keys(array_flip($data));

            update_option("manage_subscriptions_locked_{$portal_type}",$manage_subscriptions_locked);

            return $data;
        }

        public function get_failed_reason($order_id){
            global $wpdb;

            if ($wpdb->get_results("SELECT comment_content FROM {$wpdb->comments} WHERE comment_post_ID = $order_id AND comment_content LIKE '%declined%'")) {
                $failed_reason = "declined";
            }elseif ($wpdb->get_results("SELECT comment_content FROM {$wpdb->comments} WHERE comment_post_ID = $order_id AND comment_content LIKE '%expired%'")) {
                $failed_reason = "expired";
            }

            return (isset($failed_reason)) ? $failed_reason : '';

        }

        public function int_to_word($int = 0) {

            switch ($int) {
                case 1: $int = "one";   break;
                case 2: $int = "two";   break;
                case 3: $int = "three"; break;
                case 4: $int = "four";  break;
                case 5: $int = "five";  break;
                case 6: $int = "six";   break;
                case 7: $int = "seven"; break;
                case 8: $int = "eight"; break;
                case 9: $int = "nine";  break;
            }
            return $int;
        }

        public function check_expired_cards() {

                // 2 digit year i.e. 2015 would be 15
                $this_year = date('y');
                // 2 digit last year i.e. 2014 would be 14
                $last_year = date('y', strtotime('-1 year'));
                // 2 digit month i.e. September would be 09
                $this_month = date('m');

                // checks if the first digit of the month is a 0 or 1 (Jan - Sept, or October - December)
                $months = substr($this_month, 0, 1) ;
                // Pre-populate query for last year, since it's static (note pipe at the end)
                $search_last_year = "0[1-9]/$last_year|1[0-2]/$last_year|";

                // Depending on whether we're above or below 10
                switch ($months) {
                    // Below 10
                    case '0':
                        // Check the second digit of the month
                        $month = substr($this_month, -1, 1);
                        // TRUE: If it's above 1, we need to add a "-" character to separate it from the other
                        // FALSE: If it's equal to 1, only search for 0[1] in the Regexp string
                        $month > 1 ? $month = "1-$month" : $month = '1' ;
                        // Add month to brackets, and tack on the 2 digit year on the end
                        $search_this_year = "0[$month]/$this_year";
                    break;
                    // Above 10
                    case '1':
                        // Check the second digit of the month
                        $month = substr($this_month, -1, 1);
                        // TRUE: If it's above 0, we need to add a "-" character to separate it from the other
                        // FALSE: If it's equal to 0, only search for 1[0] in the Regexp string
                        $month > 0 ? $month = "0-$month" : $month = '0' ;
                        // Add first 9 months, add the 10 (and above) brackets, and tack on the 2 digit year on the end
                        $search_this_year = "0[1-9]/$this_year|1[$month]/$this_year";
                    break;
                    default: '';
                    break;
                }
                // Append search for last year and this year into one for SQL search
                $complete_search = $search_last_year . $search_this_year;

            return $complete_search;

        }

        public function woocommerce_loaded() {
            global $post;

            if (has_shortcode( $post->post_content, 'manage_subscriptions_portal')) {
        			wp_register_style('manage_subscriptions_css', plugins_url('css/style.css',__FILE__ ));
        			wp_enqueue_style('manage_subscriptions_css');

                    wp_register_script('manage_subscriptions_js', plugins_url('js/admin.js',__FILE__ ));
        			wp_enqueue_script('manage_subscriptions_js');
            }
        }

        function register_manage_subscriptions_settings() {
            register_setting('manage_subscriptions_group', "manage_subscriptions_{$this->user_id}");
        }

        function manage_subscriptions_admin() {
            add_menu_page('Manage Subscriptions', 'Manage Subscriptions', 'manage_options', 'manage_subscriptions', array($this, 'manage_subscriptions_callback'), '');
        }

        function manage_subscriptions_callback() {
            include MANAGE_SUBSCRIPTIONS . '/admin/options.php';
        }

        function clear_locked_ids() {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    /**
                     * This javascript file checks for the brower/browser tab action.
                     * It is based on the file menstioned by Daniel Melo.
                     * Reference: http://stackoverflow.com/questions/1921941/close-kill-the-session-when-the-browser-or-tab-is-closed
                     */
                    var validNavigation = false;

                    function wireUpEvents() {
                      /**
                       * For a list of events that triggers onbeforeunload on IE
                       * check http://msdn.microsoft.com/en-us/library/ms536907(VS.85).aspx
                       *
                       * onbeforeunload for IE and chrome
                       * check http://stackoverflow.com/questions/1802930/setting-onbeforeunload-on-body-element-in-chrome-and-ie-using-jquery
                       */
                      var dont_confirm_leave = 0; //set dont_confirm_leave to 1 when you want the user to be able to leave withou confirmation
                      var leave_message = 'Your Portal Session will be cleared.'
                      function goodbye(e) {
                        if (!validNavigation) {
                          if (dont_confirm_leave!==1) {
                            if(!e) e = window.event;
                            //e.cancelBubble is supported by IE - this will kill the bubbling process.
                            e.cancelBubble = true;
                            e.returnValue = leave_message;
                            //e.stopPropagation works in Firefox.
                            if (e.stopPropagation) {
                              e.stopPropagation();
                              e.preventDefault();
                            }
                            //return works for Chrome and Safari
                            //return leave_message;
                            return clear_locked_ids();
                          }
                        }
                      }
                      window.onbeforeunload = goodbye;

                      // Attach the event keypress to exclude the F5 refresh
                      $(document).bind('keypress', function(e) {
                        if (e.keyCode == 116){
                            console.log(e);
                          validNavigation = true;
                        }
                      });

                      // Attach the event click for all links in the page
                      $("a").bind("click", function() {
                        validNavigation = true;
                      });

                      // Attach the event submit for all forms in the page
                      $("form").bind("submit", function() {
                        validNavigation = true;
                      });

                      // Attach the event click for all inputs in the page
                      $("input[type=submit]").bind("click", function() {
                        validNavigation = true;
                      });

                    }

                    // Wire up the events as soon as the DOM tree is ready
                    $(document).ready(function() {
                      wireUpEvents();
                    });

                    function clear_locked_ids() {
                        user_id = "<?php echo wp_get_current_user()->ID ?>";
                        $.getJSON("<?php echo site_url() ?>/wp-content/plugins/manage-subscriptions/js/ajax/clear_locked_ids.php?user_id="+user_id, function() {});
                    }

                    $('.portal_table').on('click', '.row_phone', function() {
                       extension = '<?php echo get_user_meta(wp_get_current_user()->ID, "phone_extension", true); ?>';
                       number = $(this).text();
                       caller_name = $(this).attr('data-caller-name');
                       $.getJSON("http://sip.camanoislandmanagement.com/call.php?exten="+extension+"&number="+number+"&caller_name="+caller_name+"", function() {});
                    });

                    $(".portal_table").on('submit', '#form_row', function(event) {
                        event.preventDefault();
                        // Gets parent row_id, i.e. original tr that created the action tr
                        row_id = ($(this).parents('div').attr('id'));
                        safeUrl = <?php echo json_encode(plugins_url( 'manage-subscriptions/js/ajax/' . $this->portal_type . '-ajax.php' )); ?>;
                        $.ajax({
                            type: 'POST',
                            url: safeUrl,
                            data: $(this).serialize(),
                            dataType: 'JSON'
                        })
                        .done(function(data) {
                            console.log(data);
                            // Remove the parent tr
                            $('#'+row_id).remove();
                            // Remove the child, action tr
                            $('#row_'+row_id).remove();
                            $('div#copyright').replaceWith('<div id="copyright" class="col-left"><h2>#'+row_id+' Finished</h2></div>');
                            $('#copyright').delay(3000).fadeTo(3000, 0.01);
                        });
                    });
                    // Alter the submit button so it can't be double clicked, prevents resubmission of the form
                    $('.portal_table').on('submit', '#form_row', function(event) {
                        $('input[type="submit"]').val("Saving . . ").attr("disabled", true);
                    });
                });
         </script>
     <?php

        }



    } // End class Manage_Subscriptions

    // Instantiate our plugin class and add it to the set of globals
	$GLOBALS['manage_subscriptions'] = new Manage_Subscriptions();
} // end class_exists()
