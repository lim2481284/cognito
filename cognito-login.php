<?php

/**
 *    
 * @wordpress-plugin
 * Plugin Name:       AWS Cognito Login 
 * Description:       AWS Cognito Login. Create your own custom cognito login form in wordpress.
 * Version:           1.0.0
 * Author:            Wavelet   
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define plugin version 
define( 'AWS_VERSION_LOGIN', '1.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cognito-login-activator.php
 */
function aws_acl_activate_cognito_login() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cognito-login-activator.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aws-upgrader.php';
	Cognito_Login_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cognito-login-deactivator.php
 */
function aws_acl_deactivate_cognito_login() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cognito-login-deactivator.php';
	Cognito_Login_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'aws_acl_activate_cognito_login' );
register_deactivation_hook( __FILE__, 'aws_acl_deactivate_cognito_login' );

/** 
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cognito-login.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-cognito-devops.php';

/**
 * Begins execution of the plugin.
 */
function aws_acl_run_cognito_login() {
	$plugin = Cognito_Login::instance();
	$plugin->run();	
	
	$plugin_devops = new Cognito_Devops(  __FILE__,'lim2481284','cognito','');
	return $plugin;
}
$GLOBALS['aws'] = aws_acl_run_cognito_login();


