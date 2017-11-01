<?php
/**
 * Displays the UI for editing Cognito Login
 */
 
// Handle the active tab
if( isset( $_GET[ 'tab' ] ) ) {
    $active_tab = $_GET[ 'tab' ];
}
else 
{
	$active_tab ="main_setting";
}
?>

<div class="wrap">
	
	<h2 class="nav-tab-wrapper">
		<a href="?page=cognito_login&tab=main_setting" class="nav-tab <?php echo $active_tab == 'main_setting' ? 'nav-tab-active' : ''; ?>">Main settings</a>
		<a href="?page=cognito_login&tab=shortcode" class="nav-tab <?php echo $active_tab == 'shortcode' ? 'nav-tab-active' : ''; ?>">Shortcode list</a>
		<a href="?page=cognito_login&tab=customization" class="nav-tab <?php echo $active_tab == 'customization' ? 'nav-tab-active' : ''; ?>">Customization</a>
	</h2>
	
		<div class='aws-cognito-setting-box'>
			<?php
				if( $active_tab == 'main_setting' ) {
					echo '<form method="post" action="options.php">';
					//Display option field for the setting form 
					settings_fields( 'aws_settings' );
					
					//Display UI for the setting form
					do_settings_sections( 'aws-section' );
					
					submit_button();
					echo '</form>';
					
				} else if ( $active_tab == 'shortcode' ) {
				?>
					<h2> Shortcode List </h2>
					<br>
					<table class='aws-cognito-setting-table'> 
						<tr>
							<td class='table-shortcode-row'> [aws_login_form] </td>
							<td> This shortcode contain AWS Cognito login form which directly access your aws cognito user pool API </td>
						</tr>
						<tr>
							<td class='table-shortcode-row'>[aws_login_button]</td>
							<td> This shortcode contain AWS Cognito login button which redirect user to your AWS Cognito login form page URL. </td>
						</tr>			
					</table>
				<?php
				} 
				else if ( $active_tab == 'customization' ) {
					echo '<form method="post" action="options.php">';
					//Display option field for the customization form 
					settings_fields( 'aws_settings_customization' );
 
					//Display UI for the customization form
					do_settings_sections( 'aws-customization' );
					
					submit_button();	
					
					echo '</form>';
				}
				
			?>
		</div>	

</div><!-- .wrap -->