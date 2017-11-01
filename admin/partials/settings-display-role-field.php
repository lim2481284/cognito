<?php
/**
 * Represents the partial view for where users can enter user pool id 
 */
?>
<select name="aws_settings[aws_role_setting]" value=' <?php echo $aws_role_setting; ?>'>
	<option value='subscriber' <?php if($aws_role_setting=='subscriber'){echo 'selected="selected"';} ?> >Subscriber</option>
	<option value='contributor' <?php if($aws_role_setting=='contributor'){echo 'selected="selected"';} ?> >Contributor</option>
	<option value='author' <?php if($aws_role_setting=='author'){echo 'selected="selected"';} ?>  >Author</option>
	<option value='editor' <?php if($aws_role_setting=='editor'){echo 'selected="selected"';} ?> >Editor</option>
	<option value='administrator' <?php if($aws_role_setting=='administrator'){echo 'selected="selected"';} ?> >Administrator</option>

</select>
<p class="description" > New creation user's role   </p>