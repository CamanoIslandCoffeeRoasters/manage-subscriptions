<?php
/*
Plugin Name: Manage Subscriptions
Description: Customized Management of CBW Subscriptions, i.e. reactivations, expired credit cards, cold-calling, etc.
Author: tobinfekkes	
Author URI: http://tobinfekkes.com
Version: 1.0.0
*/

define( 'MANAGE_SUBSCRIPTIONS', untrailingslashit( plugin_dir_path( __FILE__ ) ));

          
        add_action('admin_init', 'register_manage_subscriptions_settings');
          
        function register_manage_subscriptions_settings() {
                register_setting('manage_subscriptions_group', 'manage_subscriptions');
        }
        
        
        add_action('admin_menu', 'Manage_Subscriptions');
        
        function Manage_Subscriptions() {
                add_menu_page('Manage Subscriptions', 'Manage Subscriptions', 'manage_options', 'manage_subscriptions', 'manage_subscriptions_callback', '');
        }
        
        function manage_subscriptions_callback() {
                include MANAGE_SUBSCRIPTIONS . '/admin/options.php';
        }

		function manage_subscriptions_css_and_js() {
			if ( is_page('portal') ) {
					wp_register_style('manage_subscriptions_css', plugins_url('css/style.css',__FILE__ ));
					wp_enqueue_style('manage_subscriptions_css');
					wp_register_script('manage_subscriptions_js', plugins_url('js/admin.js',__FILE__ ));
					wp_enqueue_script('manage_subscriptions_js');

				}
			}
	
        add_action( 'wp_enqueue_scripts','manage_subscriptions_css_and_js');