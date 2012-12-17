<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/actions/add/head.php");
	
	$river_name = trim(htmlspecialchars($_POST['river_name']));
	
	if ($river_name == "") {
		array_push($errmsg_arr, "No river name provided.");
		$errflag = true;
	}
	
	$rivername_query = mysql_query("SELECT * FROM rivers WHERE river_name = '$river_name'");
	$num_rows = mysql_num_rows($rivername_query);
	if ($num_rows > 0) {
		array_push($errmsg_arr, "River already exists.");
		$errflag = true;
	}
	@mysql_free_result($rivername_query);
	
	$sql_string = "INSERT INTO rivers (river_name, user) VALUES ('$river_name', '" . $_SESSION['SESS_USERNAME'] . "')";
	if ($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header('Location: http://rivers.camlittle.com/');
		exit();
	} else {
		performSQL($sql_string, 'river');
	}

?>