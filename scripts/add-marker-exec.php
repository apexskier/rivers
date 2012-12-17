<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/scripts/add-exec-head.php");
	
	$marker_name = trim(htmlspecialchars($_POST['marker_name'], ENT_QUOTES));
	$marker_type = trim($_POST['marker_type']);
	$river = trim($_POST['river']);
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
	
	$sql_string = "INSERT INTO markers (marker_name, marker_type, river, lat, lng, description, date_added, user) VALUES ('$marker_name', '$marker_type', '$river', '$lat', '$lng', '$description', '$now', '" . $_SESSION['SESS_USERNAME'] . "')";
	if ($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header('Location: http://rivers.camlittle.com/');
		exit();
	} else {
		performSQL($sql_string, 'marker');
	}
?>