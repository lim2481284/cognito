<?php

/*
*  Upgrader Class
*/

class AWS_Upgrader {

	public function upgrade_plugin() {
		global $wpdb;
		$current_version = get_option('aws_version');

		if( !get_option('aws_plugin_updated') ) {			
			if ( ! empty( $current_version ) && version_compare( $current_version, AWS_VERSION_LOGIN, '<' ) ) {
				update_option( 'aws_plugin_updated', true );
			}
		}
		// to prevent unauthorized access , delete all aws_user_ids
		if ( ! empty( $current_version )) {
			$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key = '_aws_user_id'");
		}
	}
}