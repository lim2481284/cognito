<?php
/**
 * Represents the partial view for where users can enter user pool id 
 */
?>

<input type="password" name="aws_settings[aws_user_pool_id]" value="<?php echo $aws_user_pool_id; ?>" placeholder="Cognito User Pool ID" />
<p class="description" >Register <a href="https://aws.amazon.com/cognito/"  target="_blank">cognito account </a> and paste User Pool ID here<br> Note : Login button and login form will not display if user pool ID is wrong.</p>	 