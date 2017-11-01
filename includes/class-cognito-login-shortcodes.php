<?php
/**
 * The shortcodes class.
 *
 * All plugins shortcodes are defined on this class
 */

class Cognito_Login_Shortcodes {

	/**
	 * The ID of this plugin.	 
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.	 
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->register_shortcodes();
	}

	/**
	 * Register all plugin shortcodes
	 */
	public function register_shortcodes() {
		add_shortcode( 'aws_login_button', array( $this, 'login_button' ) );
		add_shortcode('aws_login_form',  array( $this, 'login_form_creation' ));
	}

	/**
	 * Simple display cognito login button
	 * [aws_login_button]
	 */
	public function login_button(){		
		if( is_user_logged_in())
			return;
		do_action('aws_login_button_code');
	}

	/**
	 * Simple display cognito login form
	 * [aws_login_form]
	 */
	public function login_form_creation(){		
		$opts = get_option( 'aws_settings' );
		$opts = get_option( 'aws_settings' );
		$aws_user_pool_id = isset( $opts['aws_user_pool_id'] ) ? $opts['aws_user_pool_id'] : '';
		$aws_client_id = isset( $opts['aws_client_id'] ) ? $opts['aws_client_id'] : '';		
		if($aws_user_pool_id == null || $aws_client_id== null)
		{
			echo "<b> You haven't fill in user pool id and client id in setting page </b>"; 
		}
		else {
			$opts = get_option( 'aws_settings_customization' );		
			$aws_customization_form = isset( $opts['aws_customization_form'] ) ? $opts['aws_customization_form'] : '';	
			$aws_customization_input = isset( $opts['aws_customization_input'] ) ? $opts['aws_customization_input'] : '';
			$aws_customization_button = isset( $opts['aws_customization_button'] ) ? $opts['aws_customization_button'] : '';			
			if( is_user_logged_in())
			{
				//$redirect_to=user_admin_url();
				//wp_safe_redirect($redirect_to);	
			}
			echo "
			<div class='aws-cognito-loader-box'>
				<div class='aws-cognito-loader'></div>
			</div>
			<div class='aws-cognito-form'>
			
			  <form class='$aws_customization_form aws-cognito-register-box aws-cognito-login-toggle  aws-cognito-confirm-toggle'>
				<input class='aws-cognito-input-field $aws_customization_input' id='aws-cognito-register-username' name='aws-cognito-register-username' type='text' placeholder='Username'  required/>
				<input class='aws-cognito-input-field $aws_customization_input' id='aws-cognito-register-email' name='aws-cognito-register-email' type='email' placeholder='Email address' required/>
				<input class='aws-cognito-input-field $aws_customization_input' id='aws-cognito-register-password' name='aws-cognito-register-password' type='password' placeholder='Password' required/>
				<input class='aws-cognito-input-field $aws_customization_input' id='aws-cognito-register-confirm-password' name='aws-cognito-register-confirm-password' type='password' placeholder='Confirm Password' required/>
				<button class='aws-cognito-register-button $aws_customization_button' type='button'> Register </button>
				<button class='aws-cognito-cancel-button $aws_customization_button aws-cognito-login-toggle-button' type='button'> Sign In </button>				
			  </form>
			  
			  <form class='$aws_customization_form aws-cognito-login-box aws-cognito-forgot-toggle aws-cognito-login-toggle aws-cognito-confirm-login-toggle aws-cognito-hidden-login-toggle aws-cognito-forgot-login-toggle'>
				<input class='aws-cognito-input-field $aws_customization_input' id='aws-cognito-login-username' type='text' name='aws-cognito-login-username' placeholder='Username' required />
				<input class='aws-cognito-input-field $aws_customization_input' id='aws-cognito-login-password' type='password'  name='aws-cognito-login-password' placeholder='Password'  required/>
				<label class='aws-cognito-forgot-label aws-cognito-forgot-toggle-button'><u> Forgot password</u> </label>
				<button class='aws-cognito-login-button $aws_customization_button' type='button'> Login </button>
				<button class='aws-cognito-cancel-button $aws_customization_button aws-cognito-login-toggle-button' type='button'> Sign Up </button>				
			  </form>
			  
			  <form class='$aws_customization_form aws-cognito-confirm-box aws-cognito-confirm-toggle aws-cognito-confirm-login-toggle aws-cognito-hidden-login-toggle'>
				  <p class='aws-cognito-message-left'>Please check your email and insert verification code to activate your account.</p>
				  <input class='aws-cognito-input-field $aws_customization_input' id='aws-cognito-confirm-verfication-code' name='aws-cognito-confirm-verfication-code' type='text' placeholder='Verification code'  required/>
				  <label class='aws-cognito-forgot-label aws-cognito-resend-button'><u>Resend verification code</u> </label>
				  <button class='aws-cognito-confirm-verification-button $aws_customization_button' type='button'> Confirm</button>
				  <button class='$aws_customization_button aws-cognito-hidden-login-toggle-button aws-cognito-cancel-button' type='button'> Cancel</button> 
			  </form>
			  
			  <form class='$aws_customization_form aws-cognito-forgot-box aws-cognito-forgot-toggle aws-cognito-forgot-verification-toggle'>
				  <p class='aws-cognito-message-left'>Please insert your username, verification code will send to your email address.</p>
				  <input class='aws-cognito-input-field $aws_customization_input' id='aws-cognito-forgot-username' name='aws-cognito-forgot-username' type='text' placeholder='Username' required />
				  <button class='aws-cognito-forgot-button $aws_customization_button' type='button'> Confirm</button>
				  <button class='aws-cognito-forgot-toggle-button aws-cognito-cancel-button $aws_customization_button' type='button'> Cancel</button> 
			  </form>

			  <form class='$aws_customization_form aws-cognito-forgot-verification-box aws-cognito-forgot-verification-toggle aws-cognito-forgot-login-toggle'>
				  <p class='aws-cognito-message-left'>Please insert verification code and your new password.</p>
				  <input class='aws-cognito-input-field $aws_customization_input' id='aws-cognito-forgot-verification' name='aws-cognito-forgot-verification' type='text' placeholder='Verification Code' required />
				  <input class='aws-cognito-input-field $aws_customization_input' id='aws-cognito-forgot-password' name='aws-cognito-forgot-password' type='password' placeholder='New Password' required />
				  <label class='aws-cognito-forgot-label aws-cognito-forgot-resend-button'><u>Resend verification code</u> </label>
				  <button class='aws-cognito-forgot-verification-button $aws_customization_button' type='button'> Confirm</button>
				  <button class='aws-cognito-forgot-verification-toggle-button aws-cognito-cancel-button $aws_customization_button' type='button'> Cancel</button> 
			  </form>
			  
			</div>";
		}
	}
	
}