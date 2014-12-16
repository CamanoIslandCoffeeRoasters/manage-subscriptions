<?php
/*
Plugin Name: Manage Subscriptions
Description: Customized Management of CBW Subscriptions, i.e. reactivations, expired credit cards, cold-calling, etc.
Author: tobinfekkes	
Author URI: http://tobinfekkes.com
Version: 1.0.0
*/

define( 'MANAGE_SUBSCRIPTIONS', untrailingslashit( plugin_dir_path( __FILE__ ) ));

//include MANAGE_SUBSCRIPTIONS . '/css/style.css';


			if ( !is_admin() ) {
				function manage_subscriptions_css_and_js() {
					wp_register_style('manage_subscriptions_css', plugins_url('css/style.css',__FILE__ ));
					wp_enqueue_style('manage_subscriptions_css');
					wp_register_script('manage_subscriptions_js', plugins_url('js/admin.js',__FILE__ ));
					wp_enqueue_script('manage_subscriptions_js');

				}
			}




add_action( 'wp_enqueue_scripts','manage_subscriptions_css_and_js');

// HELLO WORLD AGAIN!
