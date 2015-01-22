<?php 

$options = get_option('manage_subscriptions_option');

?>
<div class="wrap">
	<?php screen_icon(); ?>
	<form method="POST" action="options.php">
		<?php 
			options_init();
			settings_fields('manage_subscriptions_group');
			do_settings_sections('my-setting-admins');
			submit_button();
		?>
	</form>
</div>


<?php 	




				
	function options_init() {
		
        register_setting(
            'manage_subscriptions_group', // Option group
            'manage_subscriptions_option', // Option name
            'sanitize' // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            '<h1>Referral Settings</h1>', // Title
            'print_section_info', // Callback
            'my-setting-admins' // Page
        );  

        add_settings_field(
            'days_valid', // ID
            'Days Valid', // Title 
            'days_valid_callback', // Callback
            'my-setting-admins', // Page
            'setting_section_id' // Section           
        ); 
	} 
	
	function print_section_info() {
		echo "Add you settings below";
	}
	
	function sanitize( $input ) {
        	
        $new_input = array();
        
        if( isset( $input['days_valid'] ) )
            $new_input['days_valid'] = absint( $input['days_valid'] );
		
	return $new_input;
    }
	
	
	
	function days_valid_callback()
	    {
	        printf(
	            '<input type="text" id="days_valid" name="referral_option[days_valid]" value="%s" />',
	            isset( $options['days_valid'] ) ? esc_attr( $options['days_valid']) : ''
	        );
	    }
