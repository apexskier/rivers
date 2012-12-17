<?php
	//Start session
	session_start();
	
	//Unset the variables stored in session
	unset($_SESSION['SESS_USERNAME']);
	unset($_SESSION['SESS_FIRST_NAME']);
	unset($_SESSION['SESS_LAST_NAME']);
	
	$_SESSION['SUCCESS'] = "Logged out.";
	session_write_close();
	header('Location: http://rivers.camlittle.com/');
?>