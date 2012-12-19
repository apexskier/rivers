<h3>Register</h3>
<form id="register" name="register" method="post" action="/app/php/actions/register.php">
	<label name="fname">First Name</label>
	<input type="text" name="fname" required>
	<label name="lname">Last Name</label>
	<input type="text" name="lname" required>
	
	<label name="email">Email</label>
	<input type="email" name="email" required>
	
	<label name="username">Username</label>
	<input type="text" name="username" required>
	
	<label name="password">Password</label>
	<input type="password" name="password" required>
	<label name="cpassword">Confirm Password</label>
	<input type="password" name="cpassword" required>
	<span class="help-block">Password must be longer than 8 characters.</span>
	
	<div id="recaptcha"></div>
	
	<?php
		//require($_SERVER['DOCUMENT_ROOT'] . "/app/php/recaptchalib.php");
		//echo recaptcha_get_html("6Lfe0tUSAAAAACJW64aO2_fowRUsjV-zsahPZePZ");
	?>
	
	<button type="submit" name="Submit" class="btn btn-primary submit">Sign up!</button>
</form>