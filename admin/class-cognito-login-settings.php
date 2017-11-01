<?php
class Cognito_Login_Settings {

	public function __construct() {
		$this->views    = trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/' );
		
		//Declare field ID and field name 
		$this->fields   = array(
			'user_pool_id'        => 'User Pool id',
			'client_id'   => 'Client id',	
			'login_url'   => 'Login form URL ',
			'login_button'   => 'Login button ',
			'role_setting'	=> 'User role'
			
		);
		
		$this->customize_fields   = array(
			'customization_input'       => 'Input field',
			'customization_form'        => 'Login form',
			'customization_button'      => 'Button'
		);		
		
	}

	/**
	 * Register sections fields and settings
	 */
	public function register() {

		register_setting(
			'aws_settings',		// Group of options
			'aws_settings',     	        // Name of options
			array( $this, 'sanitize' )	// Sanitization function
		);
 
		register_setting(
			'aws_settings_customization',		// Group of options
			'aws_settings_customization',     	        // Name of options
			array( $this, 'sanitize' )	// Sanitization function
		);		
		
		add_settings_section(
			'aws-main',			// ID of the settings section
			'Main Settings',  			// Title of the section
			'',
			'aws-section'		// ID of the page
		);

		// section for form customization
		add_settings_section(
			'aws-main-customization',			// ID of the settings section
			'Add extra class name',  			// Title of the section
			'',
			'aws-customization'		// ID of the page
		);

		
		foreach( $this->fields as $key => $name) {
			add_settings_field(
				$key,        // The ID of the settings field
				$name,                // The name of the field of setting(s)
				array( $this, 'display_'.$key ),
				'aws-section',        // ID of the page on which to display these fields
				'aws-main'            // The ID of the setting section
			);
		}
		
		//add customization field setting
		foreach( $this->customize_fields as $key => $name) {
			add_settings_field(
				$key,        // The ID of the settings field
				$name,                // The name of the field of setting(s)
				array( $this, 'display_'.$key ),
				'aws-customization',        // ID of the page on which to display these fields
				'aws-main-customization'            // The ID of the setting section
			);
		}		
	}

	/**
	 * Display user pool id field
	 */
	public function display_user_pool_id() {
		// Now grab the options based on what we're looking for
		$opts = get_option( 'aws_settings' );		
		$aws_user_pool_id = isset( $opts['aws_user_pool_id'] ) ? $opts['aws_user_pool_id'] : '';
		// And display the view
		include_once $this->views . 'settings-user-pool-id-field.php';
	}

	/**
	 * Display user role field
	 */
	public function display_role_setting() {
		// Now grab the options based on what we're looking for
		$opts = get_option( 'aws_settings' );		
		$aws_role_setting  = isset( $opts['aws_role_setting'] ) ? $opts['aws_role_setting'] : '';
		// And display the view
		include_once $this->views . 'settings-display-role-field.php';
	}	
	
	/**
	 * Display client id field
	 */
	public function display_client_id() {
		// Now grab the options based on what we're looking for
		$opts = get_option( 'aws_settings' );
		$aws_client_id = isset( $opts['aws_client_id'] ) ? $opts['aws_client_id'] : '';
		// And display the view
		include $this->views . 'settings-client-id-field.php';
	}

	/**
	 * Display login button field
	 */
	public function display_login_button() {
		// Now grab the options based on what we're looking for
		$opts = get_option( 'aws_settings' );
		$aws_display_login = isset( $opts['aws_display_login'] ) ? $opts['aws_display_login'] : '';
		// And display the view
		include $this->views . 'settings-display-login-field.php';
	}

	/**
	 * Display login url field
	 */
	public function display_login_url() {
		// Now grab the options based on what we're looking for
		$opts = get_option( 'aws_settings' );
		$aws_display_login_url = isset( $opts['aws_display_login_url'] ) ? $opts['aws_display_login_url'] : '';
		// And display the view
		include $this->views . 'settings-display-login-url-field.php';
	}
	
	/**
	 * Display customization field
	 */
	public function display_customization_input() {
		// Now grab the options based on what we're looking for
		$opts = get_option( 'aws_settings_customization' );
		$aws_customization_input = isset( $opts['aws_customization_input'] ) ? $opts['aws_customization_input'] : '';		
		// And display the view
		include $this->views . 'settings-display-customization-input-field.php';
	}
	
	/**
	 * Display customization field
	 */
	public function display_customization_form() {
		// Now grab the options based on what we're looking for
		$opts = get_option( 'aws_settings_customization' );		
		$aws_customization_form = isset( $opts['aws_customization_form'] ) ? $opts['aws_customization_form'] : '';		
		// And display the view
		include $this->views . 'settings-display-customization-form-field.php';
	}
	
	/**
	 * Display customization field
	 */
	public function display_customization_button() {
		// Now grab the options based on what we're looking for
		$opts = get_option( 'aws_settings_customization' );	
		$aws_customization_button = isset( $opts['aws_customization_button'] ) ? $opts['aws_customization_button'] : '';				
		// And display the view
		include $this->views . 'settings-display-customization-button-field.php';
	}

	
	/**
	 * Simple sanitize function
	 */
	public function sanitize( $input ) {

		$new_input = array();

		// Loop through the input and sanitize each of the values
		foreach ( $input as $key => $val ) {
			$new_input[ $key ] = sanitize_text_field( $val );
		}

		return $new_input;
	}
}