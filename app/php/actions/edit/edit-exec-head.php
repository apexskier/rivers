<?php
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/database.php");
	
	$errmsg_arr = array();
	$errflag = false;
	$now = date("Y-m-d H:i:s");
	
	if (!$logged_in) {
		$errmsg_arr[] = "Not logged in.";
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header('Location: http://rivers.camlittle.com/');
		exit();
	}
	
	function onError() {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header('Location: http://rivers.camlittle.com/');
		exit();
	}
	
	function performSQL($sql_string, $type) {
		if (!$errflag) {
			$query = mysql_query($sql_string);
			if ($query) {
				$points = 1;
				mysql_query("UPDATE users SET contributions = contributions + $points WHERE username = '" . $_SESSION['SESS_USERNAME'] . "'");
				$cache_query = mysql_query("UPDATE cache_times SET $type = '" . date("Y-m-d H:i:s") . "' WHERE id = 1");
				if ($cache_query) {
					$_SESSION['SUCCESS'] = "Successfully updated $type.";
					session_write_close();
					mysql_close($link);
					header('Location: http://rivers.camlittle.com/');
					exit();
				} else {
					$errmsg_arr[] = "Error updating cache times in database: " . mysql_error();
					onError();
				}
			} else {
				$errmsg_arr[] = "Error inserting $type into database: " . mysql_error();
			}
		}
		onError();
	}
	
?>