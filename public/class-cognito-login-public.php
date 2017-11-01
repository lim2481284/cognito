<?php

/**
 * Defines the plugin name, version
 * enqueue the admin-specific stylesheet and JavaScript.

 */
class Cognito_Login_Public {

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
		$this->opts= get_option('aws_settings');		
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles() {	
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cognito-login.css', array(), $this->version, 'all' );		
		wp_enqueue_style( $this->plugin_name );		

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_scripts() {
		
		
		do_action('get_pool_data');
		//register cognito api script 
		wp_enqueue_script( $this->plugin_name.'_aws_cognito', plugin_dir_url( __FILE__ ) . 'js/aws-cognito-sdk.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'_amazon_cognito', plugin_dir_url( __FILE__ ) . 'js/amazon-cognito-identity.min.js', array( 'jquery' ), $this->version, false );	
		
		//register ajax script 
		wp_register_script($this->plugin_name.'_login_form', plugin_dir_url( __FILE__ ) . 'js/cognito-login.js', array('jquery' ), $this->version, false  );		
		wp_enqueue_script( $this->plugin_name.'_login_form');
		wp_localize_script($this->plugin_name.'_login_form', 'cognito_ajax', array( 'ajax_url' => admin_url('admin-ajax.php')) );
	}

	/**
	 * Print the button on login page
	 */
	public function print_button() {
		
		$redirect = ! empty( $_GET['redirect_to'] ) ? esc_url($_GET['redirect_to']) : ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$opts = get_option( 'aws_settings' );
		// if we are in login page we don't want to redirect back to it
		if ( isset( $GLOBALS['pagenow'] ) && in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) && empty($_GET['redirect_to']) )
			$redirect = '';
		
		if(isset($opts['aws_display_login'])){			
			?>
			
			<script>
			jQuery(document).ready(function(){
				<?php ob_start(); /* Buffer javascript contents so we can run it through a filter */ ?>			
				var loginform = jQuery('#loginform');
				loginform.prepend('<h3 class="text-or"> or </h3>');
				loginform.prepend("<?php do_action('aws_login_button_code'); ?>");	
			});
			</script>
			<?php
		}
	}

	/**
	 * Login button HTML code 
	 */
	public function print_button_code() {
			
		$opts = get_option( 'aws_settings' );
		$aws_user_pool_id = isset( $opts['aws_user_pool_id'] ) ? $opts['aws_user_pool_id'] : '';
		$aws_client_id = isset( $opts['aws_client_id'] ) ? $opts['aws_client_id'] : '';
		
		if($aws_user_pool_id == null || $aws_client_id== null)
		{
			echo "<b> You haven't fill in user pool id and client id in setting page </b>"; 
		}
		else 
		{
		
			//parse url with http or https
			$url =isset($opts['aws_display_login_url'])?$opts['aws_display_login_url']:"" ; 
			if($url)
			{			
				$url_check=parse_url("$url");
				if(empty($url_check['scheme']))
					$url = 'https://' . ltrim($url, '/');
				else if ($url_check['scheme']=='http')
					$url = preg_replace("/^http:/i", "https:", $url);
			}
			
			//print login button HTMl code
			echo "<a href='$url' class='css-aws-login js-aws-login'><div>Login with Cognito</div></a>";
		}
	}
	
	
	/**
	 * Get pool data 
	 */
	public function display_pool_data() {
		$opts = get_option( 'aws_settings' );
		$aws_user_pool_id = isset( $opts['aws_user_pool_id'] ) ? $opts['aws_user_pool_id'] : ''	;
		$aws_client_id = isset( $opts['aws_client_id'] ) ? $opts['aws_client_id'] : '';
		echo " 		
			<script>
				var user_pool_id = '$aws_user_pool_id';
				var client_id = '$aws_client_id'
			</script>
		";			
	}

	/**
	 * Main function that handles user registration
	 */
	public function register_user() {		
		$nickname = sanitize_text_field($_POST['registerUser']);
		$registerUser = sanitize_text_field($_POST['registerUser']."_aws_account");				
		$registerPass = sanitize_text_field($_POST['registerPass']);
		$registerEmail = sanitize_text_field($_POST['registerEmail']);
		$opts = get_option( 'aws_settings' );		
		$aws_role_setting  = isset( $opts['aws_role_setting'] ) ? $opts['aws_role_setting'] : '';	
		//Check user exists
		$user_id = username_exists( $registerUser );
		if ( !$user_id && email_exists($registerEmail) == false ) {
			
			//Create wordpress user 
			$user_id = wp_create_user( $registerUser, $registerPass, $registerEmail );
			if( !is_wp_error($user_id) ) {
				$user = get_user_by( 'id', $user_id );
				$user->set_role( "$aws_role_setting");
				//Remove role to set user inactive before confirm verification code 
				//$user->remove_role('contributor');
				$response ="Register success";
				wp_update_user( array ('ID' => $user_id, 'display_name'=> $nickname) ) ;
			}
			else 
			{		
				$error_string = $user_id->get_error_message();
				$response = $error_string ; 
				$status = "failed";
			}
		}
		else 
		{		
			$status = "failed";
			$response ="User already exists";
		}
		
		//return response to AJAX
		$return = array(
			'state'	=> "$status",
			'message'	=> "$response"			
		);
		wp_send_json( $return );
		
		die();exit();
			
	}


	/**
	 * Check user exist
	 */
	public function check_user() {		
		$username = sanitize_text_field($_POST['checkUser']."_aws_account");
		$email = sanitize_text_field($_POST['checkEmail']);
		$user_id = username_exists( $username );
		if($user_id)	
		{	
			$return = array(
				'state'	=> "true",
				'message' => "username"
			);
			wp_send_json( $return );						
		}
		else if( email_exists($email))
		{
			$return = array(
				'state'	=> "true",
				'message' => "email"
			);
			wp_send_json( $return );			
		}			
		die();exit();		
	}	

	
	/**
	 * Check user exist
	 */
	public function reset_pass() {		
		$username = sanitize_text_field($_POST['forgotUser']."_aws_account");
		$pass = sanitize_text_field($_POST['forgotPass']);
		$user_id = username_exists( $username );		
		wp_update_user( array ('ID' => $user_id, 'user_pass' => $pass) ) ;
		$return = array(
			'state'	=> "$user_id"			
		);
		wp_send_json( $return );			
		die();exit();		
	}		
	
	
	/**
	 * Main function that handles user activation
	 */
	public function activate_user() {	
		$opts = get_option( 'aws_settings' );		
		$aws_role_setting  = isset( $opts['aws_role_setting'] ) ? $opts['aws_role_setting'] : '';	
		$username = sanitize_text_field($_POST['username']);
		$user_id = username_exists( $username );
		$user = get_user_by( 'id', $user_id );
		$user->set_role( "$aws_role_setting" );		
		die();exit();		
	}		


	/**
	 * Main function to replace wordpress user account with cognito account 
	 */
	public function check_replace_user() {	
		$username =sanitize_text_field($_POST['replaceUser']);		
		$password =sanitize_text_field($_POST['replacePass']);
		$email =sanitize_text_field($_POST['replaceEmail']);
		$user_id = username_exists( $username );
		if(!($user_id))
		{
			$username =sanitize_text_field($_POST['replaceUser']."_aws_account");
			$user_id = username_exists( $username );
		}		
		if($user_id)
		{
			$info = array();
			$info['user_login'] = $username;
			$info['user_password'] = $password;
			$info['remember'] = false;
			$user_signon = wp_signon( $info, false );			
			if ( is_wp_error($user_signon) ){
				$return = array(
					'state'	=> "failed",
					'message'=> "Wrong password"
				);						
			}
			else 
			{
				$user = get_user_by( 'email', $email );
				$user2 = get_user_by( 'id', $user_id );
				if($user == $user2)
				{					
					$userId = $user->ID;
					wp_delete_user( $userId );			
					$return = array(
						'state'	=> "success",
						'message'=> "User account deleted"
					);							
				}else 
				{	
					$return = array(
						'state'	=> "failed",
						'message'=> "Username not correct"
					);						
				}				
			}
			wp_send_json( $return );	
		}
		else 
		{
			$return = array(
				'state'	=> "failed",
				'message'=> "Username doesn't exist"
			);	
			wp_send_json( $return );
		}		
	}
	/**
	 * Main function that handles user login
	 */
	public function login_user() {		
		$nickname = sanitize_text_field($_POST['loginUser']);
	    $username = sanitize_text_field($_POST['loginUser']."_aws_account");		
		$password = sanitize_text_field($_POST['loginPass']);
		$email = sanitize_text_field($_POST['loginEmail']);
		$opts = get_option( 'aws_settings' );		
		$aws_role_setting  = isset( $opts['aws_role_setting'] ) ? $opts['aws_role_setting'] : '';
		$user_id = username_exists( $username );
		if ( !($user_id) ) {
			
			//Create wordpress user 
			$user_id = wp_create_user( $username, $password, $email );
			if( !is_wp_error($user_id) ) {
				$user = get_user_by( 'id', $user_id );
				$user->set_role( "$aws_role_setting" );									
				$user->set_nickname( "$nickname" );	
				$user->set_display_name( "$nickname" );	
				wp_update_user( array ('ID' => $user_id, 'display_name'=> $nickname) ) ;
			}
			else 
			{
				if(email_exists($email))
				{
					$return = array(
						'state'	=> "failed",
						'message'=> "email existed"
					);					
				}
				else 
				{				
					$error_string = $user_id->get_error_message();
					$response = $error_string ; 
					$return = array(
						'state'	=> "failed",
						'message'=> "$response"
					);
				}
				wp_send_json( $return );
			}
		}
	
		$info = array();
		$info['user_login'] = $username;
		$info['user_password'] = $password;
		$info['remember'] = true;
		
		$user_signon = wp_signon( $info, false );
		$id=  $user_signon->ID;
		if ( is_wp_error($user_signon) ){
			$message = "Wrong username or password";
			$status = "failed";			
		} else {
			wp_clear_auth_cookie();
			
			wp_set_current_user($id); 
			wp_set_auth_cookie($id);		
			do_action('wp_login', $username, $user_signon);		
			//$redirect_to=user_admin_url();
			//wp_safe_redirect($redirect_to);			
			
			//$redirect_to = $_SERVER['REQUEST_URI'];
			//wp_safe_redirect($redirect_to);
			//exit;
		}
		
		$return = array(
			'state'	=> "$status",
			'message'=> "$message"
		);
		wp_send_json( $return );
		
		die();
			
	}	
	
	
	/**
	 * Try to retrieve an user by email or username
	 */
	private function getUserBy( $user ) {

		// if the user is logged in, pass curent user
		if( is_user_logged_in() )
			return wp_get_current_user();

		$user_data = get_user_by('email', $user['user_email']);

		if( ! $user_data ) {
			$users     = get_users(
				array(
					'meta_key'    => '_aws_user_id',
					'meta_value'  => $user['aws_user_id'],
					'number'      => 1,
					'count_total' => false
				)
			);
			if( is_array( $users ) )
				$user_data = reset( $users );
		}
		return $user_data;
	}

	
	
	/**
	 * Generated a friendly username for cognito users
	 */
	private function generateUsername( $user ) {
		global $wpdb;

		do_action( 'aws/generateUsername', $user );

		if( !empty( $user['first_name'] ) && !empty( $user['last_name'] ) )
			$username = $this->cleanUsername( trim( $user['first_name'] ) .'-'. trim( $user['last_name'] ) );

		if( ! validate_username( $username ) ) {
			$username = '';
			// use email
			$email    = explode( '@', $user['email'] );
			if( validate_username( $email[0] ) )
				$username = $this->cleanUsername( $email[0] );
		}

		// User name can't be on the blacklist or empty
		$illegal_names = get_site_option( 'illegal_names' );
		if ( empty( $username ) || in_array( $username, (array) $illegal_names ) ) {
			// we used all our options to generate a nice username. Use id instead
			$username = 'aws_' . $user['id'];
		}

		// "generate" unique suffix
		$suffix = $wpdb->get_var( $wpdb->prepare(
			"SELECT 1 + SUBSTR(user_login, %d) FROM $wpdb->users WHERE user_login REGEXP %s ORDER BY 1 DESC LIMIT 1",
			strlen( $username ) + 2, '^' . $username . '(-[0-9]+)?$' ) );

		if( !empty( $suffix ) ) {
			$username .= "-{$suffix}";
		}
		return apply_filters( 'aws/generateUsername', $username );
	}

	/**
	 * Simple pass sanitazing functions to a given string
	 */
	private function cleanUsername( $username ) {
		return sanitize_title( str_replace('_','-', sanitize_user(  $username  ) ) );
	}


	/**
	 * Add aws button is user is not logged
	 */
	public function add_aws_button() {
		if( ! is_user_logged_in() )
			do_action( 'cognito_login_button' );
	}

}
