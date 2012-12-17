<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/scripts/add-exec-head.php");

	$playspot_name = trim(htmlspecialchars($_POST['playspot_name'], ENT_QUOTES));
	$river = $_POST['river'];
	$gauge_id = $_POST['gauge_id'];
	$gauge_max = trim($_POST['gauge_max']);
	$gauge_min = trim($_POST['gauge_min']);
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
	
	$sql_string = "INSERT INTO playspots (playspot_name, river, gauge_id, gauge_max, gauge_min, lat, lng, description, date_added, user) VALUES ('$playspot_name', '$river', '$gauge_id', '$gauge_max', '$gauge_min', '$lat', '$lng', '$description', '$now', '" . $_SESSION['SESS_USERNAME'] . "')";
	if ($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header('Location: http://rivers.camlittle.com/');
		exit();
	} else {
		performSQL($sql_string, 'playspot');
	}

?>