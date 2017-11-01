<?php

/**
 * Fired during plugin activation. 
 * This class defines all code necessary to run during the plugin's activation.
 */
class Cognito_Login_Activator {

	// When activate plugin auto upgrade to latest plugin version
	public static function activate() {
		$upgrader = new AWS_Upgrader( 'aws', AWS_VERSION_LOGIN);
		$upgrader->upgrade_plugin();

		update_option('aws_version', AWS_VERSION_LOGIN);		
	}

}
