<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * This is used to define admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
class Cognito_Login {
	
	//Public class where all hooks are added	 
	public $aws;
	
	//The loader that's responsible for maintaining and registering all hooks that power the plugin.
	protected $loader;
	
	//The unique identifier of this plugin.	 
	protected $plugin_name;
	
	//The current version of the plugin.
	protected $version;
	
	//array of plugin settings	 
	protected $opts;
	
	//Plugin Instance
	protected static $_instance = null;
	
	//The plugin text domain for translations, used to uniquely identify this plugin.	 
	protected $text_domain;

	private $shortcodes;
	
	//Main aws Instance, ensures only one instance of WSI is loaded or can be loaded.
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	//Cloning is forbidden.	 	
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wsi' ), '2.1' );
	}

	
	//Unserializing instances of this class is forbidden.	 	 
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wsi' ), '2.1' );
	}

	
	//Auto-load in-accessible properties on demand.	 
	public function __get( $key ) {
		if ( in_array( $key, array( 'payment_gateways', 'shipping', 'mailer', 'checkout' ) ) ) {
			return $this->$key();
		}
	}

	/**
	 * Define the core functionality of the plugin.	 
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 */
	public function __construct() {

		$this->plugin_name  = 'cognito-login';
		$this->text_domain  = 'aws';
		$this->version      = AWS_VERSION_LOGIN;
		$this->opts         = get_option('aws_settings');

		$this->load_dependencies();		
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.	 
	 * Include the following files that make up the plugin:	 
	 * - Cognito_Login_Loader. Orchestrates the hooks of the plugin.	 
	 * - Cognito_Login_Admin. Defines all hooks for the admin area.
	 * - Cognito_Login_Public. Defines all hooks for the public side of the site.	 
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cognito-login-loader.php';		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cognito-login-shortcodes.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cognito-login-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cognito-login-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cognito-login-public.php';		

		$this->loader = new Cognito_Login_Loader();
		$this->shortcodes = new Cognito_Login_Shortcodes( $this->get_plugin_name(), $this->get_version() );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Cognito_Login_Admin( $this->get_plugin_name(), $this->get_version() );		

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_items');
		$this->loader->add_action( 'admin_init', $plugin_admin, 'create_settings');		
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_admin_styles');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 */
	private function define_public_hooks() {

		$this->aws = new Cognito_Login_Public( $this->get_plugin_name(), $this->get_version() );

			$this->loader->add_action( 'login_form', $this->aws, 'print_button' );
			$this->loader->add_action( 'login_form', $this->aws, 'add_aws_button' );			
			$this->loader->add_action( 'login_enqueue_scripts', $this->aws, 'enqueue_styles' );
			$this->loader->add_action( 'wp_enqueue_scripts', $this->aws, 'enqueue_styles' );
			$this->loader->add_action( 'aws_login_button_code', $this->aws, 'print_button_code' );
			$this->loader->add_action( 'get_pool_data', $this->aws, 'display_pool_data' );
			$this->loader->add_action( 'login_enqueue_scripts', $this->aws, 'enqueue_scripts' );
			$this->loader->add_action( 'wp_enqueue_scripts', $this->aws, 'enqueue_scripts' );			
			$this->loader->add_action( 'wp_ajax_nopriv_aws_cognito_login', $this->aws, 'login_user' );										
			$this->loader->add_action( 'wp_ajax_aws_cognito_login', $this->aws, 'login_user' );		
			$this->loader->add_action( 'wp_ajax_nopriv_aws_cognito_register', $this->aws, 'register_user' );										
			$this->loader->add_action( 'wp_ajax_aws_cognito_register', $this->aws, 'register_user' );	
			$this->loader->add_action( 'wp_ajax_nopriv_aws_cognito_activation', $this->aws, 'activate_user' );										
			$this->loader->add_action( 'wp_ajax_aws_cognito_activation', $this->aws, 'activate_user' );
			$this->loader->add_action( 'wp_ajax_nopriv_aws_cognito_check_user', $this->aws, 'check_user' );										
			$this->loader->add_action( 'wp_ajax_aws_cognito_check_user', $this->aws, 'check_user' );
			$this->loader->add_action( 'wp_ajax_nopriv_aws_cognito_reset', $this->aws, 'reset_pass' );										
			$this->loader->add_action( 'wp_ajax_aws_cognito_reset', $this->aws, 'reset_pass' );		
			$this->loader->add_action( 'wp_ajax_nopriv_aws_cognito_replace_check', $this->aws, 'check_replace_user' );										
			$this->loader->add_action( 'wp_ajax_aws_cognito_replace_check', $this->aws, 'check_replace_user' );					
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
