<?php
/**
 * Represents the partial view for where users can enter user pool id 
 */
?>
<input type="text" name="aws_settings[aws_display_login_url]" value="<?php echo $aws_display_login_url; ?>" placeholder="Login form URL" />
<p class="description" > Redirect user to this URL when user clicked on AWS Cognito login button  </p>