<?php
	session_start();
	
	require($_SERVER['DOCUMENT_ROOT'] . "/app/php/database.php");
	
	$errmsg_arr = array();
	$errflag = false;
	
	//Function to sanitize values received from the form. Prevents SQL injection
	function clean($str) {
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}
	
	//Sanitize the POST values
	$fname = clean($_POST['fname']);
	$lname = clean($_POST['lname']);
	$email = clean($_POST['email']);
	$username = clean($_POST['username']);
	$password = clean($_POST['password']);
	$cpassword = clean($_POST['cpassword']);
	
	//Input Validations
	require($_SERVER['DOCUMENT_ROOT'] . "/app/php/recaptchalib.php");
	$privatekey = "6Lfe0tUSAAAAAPZnUS1Tf6MOby98Suxbo9LrZyb4";
	$resp = recaptcha_check_answer ($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

	if (!$resp->is_valid) {
		$errmsg_arr[] = "The reCAPTCHA wasn't entered correctly. Try it again.";
		$errflag = true;
	}
	if ($fname == '') {
		$errmsg_arr[] = 'First name missing';
		$errflag = true;
	}
	if ($lname == '') {
		$errmsg_arr[] = 'Last name missing';
		$errflag = true;
	}
	if ($email == '') {
		$errmsg_arr[] = 'Email missing';
		$errflag = true;
	}
	if ($username == '') {
		$errmsg_arr[] = 'username ID missing';
		$errflag = true;
	}
	if ($password == '') {
		$errmsg_arr[] = 'Password missing';
		$errflag = true;
	} else if (strlen($password) < 8) {
		$errmsg_arr[] = 'Password is to short';
		$errflag = true;
	}
	if ($cpassword == '') {
		$errmsg_arr[] = 'Confirm password missing';
		$errflag = true;
	}
	if ( strcmp($password, $cpassword) != 0 ) {
		$errmsg_arr[] = 'Passwords do not match';
		$errflag = true;
	}
	
	//Check for duplicate username ID
	if ($username != '') {
		$result = mysql_query("SELECT * FROM users WHERE username = '$username'");
		if ($result) {
			if(mysql_num_rows($result) > 0) {
				$errmsg_arr[] = 'Username already in use';
				$errflag = true;
			}
			@mysql_free_result($result);
		} else {
			die("Query failed");
		}
	}
	
	//If there are input validations, redirect back to the home page
	if ($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header('Location: http://rivers.camlittle.com/');
		exit();
	}

	//Create INSERT query
	$result = @mysql_query("INSERT INTO users (firstname, lastname, username, password, email) VALUES ('$fname', '$lname', '$username', '" . md5($_POST['password']) . "', '$email')");
	
	//Check whether the query was successful or not
	if ($result) {
		$id = mysql_insert_id();
		
		$query = mysql_query("SELECT * FROM users WHERE id = '$id'");
		session_regenerate_id();
		$user = mysql_fetch_assoc($query);
		$_SESSION['SESS_USERNAME'] = $user['username'];
		$_SESSION['SESS_FIRST_NAME'] = $user['firstname'];
		$_SESSION['SESS_LAST_NAME'] = $user['lastname'];
		$_SESSION['SUCCESS'] = "Successfully registered and logged in.";
		session_write_close();
		header('Location: http://rivers.camlittle.com/');
		exit();
	} else {
		die("Query failed");
	}
?>