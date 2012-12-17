<?php
	//Start session
	session_start();
	
	require($_SERVER['DOCUMENT_ROOT'] . "/app/php/database.php");
	
	$errmsg_arr = array();
	$errflag = false;
	
	//Function to sanitize values received from the form. Prevents SQL injection
	function clean($str) {
		$str = @trim($str);
		if (get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}
	
	//Sanitize the POST values
	$username = clean($_POST['username']);
	$password = clean($_POST['password']);
	
	//Input Validations
	if($username == '') {
		$errmsg_arr[] = 'Username missing';
		$errflag = true;
	}
	if($password == '') {
		$errmsg_arr[] = 'Password missing';
		$errflag = true;
	}
	
	//If there are input validations, redirect back to the login form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header('Location: http://rivers.camlittle.com/');
		exit();
	}
	
	//Create query
	$result = mysql_query("SELECT * FROM users WHERE username = '$username' AND password = '" . md5($_POST['password']) . "'");
	
	//Check whether the query was successful or not
	if ($result) {
		if (mysql_num_rows($result) == 1) {
			//Login Successful
			session_regenerate_id();
			$user = mysql_fetch_assoc($result);
			$_SESSION['SESS_USERNAME'] = $user['username'];
			$_SESSION['SESS_FIRST_NAME'] = $user['firstname'];
			$_SESSION['SESS_LAST_NAME'] = $user['lastname'];
			$_SESSION['SUCCESS'] = "Logged in.";
			session_write_close();
			header('Location: http://rivers.camlittle.com/');
			exit();
		} else {
			$errmsg_arr[] = 'Login failed';
			$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
			session_write_close();
			header('Location: http://rivers.camlittle.com/');
			exit();
		}
	} else {
		die("Query failed");
	}
?>