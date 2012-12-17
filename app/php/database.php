<?php
	$link = mysql_connect('mysql.camlittle.com', 'camlittle', 'KayaKing@010');
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db('riverdatabase') or die('Unable to select database');
	
	//Start session
	session_start();
	$logged_in = false;
	
	//Check whether the session variable SESS_MEMBER_ID is present or not
	if (trim($_SESSION['SESS_USERNAME']) != '') {
		$logged_in = true;
	}
?>