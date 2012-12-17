<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/scripts/add-exec-head.php");
	
	$rapid_name = trim(htmlspecialchars($_POST['rapid_name'], ENT_QUOTES));
	$class = $_POST['class'];
	$river = $_POST['river'];
	$lat = trim($_POST['lat']);
	$lng = trim($_POST['lng']);
	$description = nl2br(trim(htmlspecialchars($_POST['description'], ENT_QUOTES)));
	
	if ($lat == "") {
		$errmsg_arr[] = "No latitude provided.";
		$errflag = true;
	}
	if ($lng == "") {
		$errmsg_arr[] = "No longitude provided.";
		$errflag = true;
	}
	
	$sql_string = "INSERT INTO rapids (rapid_name, class, river, lat, lng, description, date_added, user) VALUES ('$rapid_name', '$class', '$river', '$lat', '$lng', '$description', '$now', '" . $_SESSION['SESS_USERNAME'] . "')";
	if ($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header('Location: http://rivers.camlittle.com/');
		exit();
	} else {
		performSQL($sql_string, 'rapid');
	}
	
?>