<?php

/**
 * The admin-specific functionality of the plugin. 
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript. 
 */
class Cognito_Login_Admin {
	/**
	 * location of admin views
	 */
	protected $views;

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
		$this->views = trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/views' );
	}

	/**
	 *  Add item to wordpress admin side menu
	 */	
	public function add_menu_items() {

		add_menu_page(			
			'Cognito Login',
			'Cognito Login',
			apply_filters('aws/settings_capabilities', 'manage_options'),
			'cognito_login',
			array( $this, 'display_settings_page' )
		);
	}

	/**
	 *  Display setting page views
	 */		
	public function display_settings_page() {
		include_once $this->views . 'settings-page.php';
	}

	/**
	 *  Create settings
	 */		
	public function create_settings() {
		$settings = new Cognito_Login_Settings( $this->plugin_name, $this->version);
		$settings->register();
	}
	
	/**
	 *  Register and enqueue style
	 */	
	public function enqueue_admin_styles() {			
		wp_register_style('aws-admin-css', plugin_dir_url( __FILE__ ) . 'assets/css/admin.css', '', $this->version);		
		wp_enqueue_style('aws-admin-css' );		

	}	
	
	/**
	 *
	 * Register and enqueue scripts.
	public function admin_scripts() {

		global $pagenow;
		if (  ( isset($_GET['page']) && 'cognito_login' == $_GET['page']  ) || $pagenow == 'profile.php' ) {

			wp_enqueue_style( 'aws-admin-css', plugins_url( 'assets/css/admin.css', __FILE__ ) , '', $this->version );
			wp_enqueue_style( 'aws-public-css', plugins_url( 'public/css/cognito-login.css', dirname( __FILE__ ) ) , '', $this->version );
			wp_enqueue_style( 'aws-public-form-css', plugins_url( 'public/css/cognito-login-form.css', dirname( __FILE__ ) ) , '', $this->version );
			wp_enqueue_script( 'aws-public-js', plugins_url( 'public/js/cognito-login.js', dirname( __FILE__ ) ) , '', $this->version );
		}
	}
	 */
}
