<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/actions/edit/head.php");

	$playspot_name = trim(htmlspecialchars($_POST['playspot_name'], ENT_QUOTES));
	$gauge_max = trim($_POST['gauge_max']);
	$gauge_min = trim($_POST['gauge_min']);
	$description = nl2br(trim(htmlspecialchars($_POST['description'], ENT_QUOTES)));
	$now = date("Y-m-d H:i:s");
	$id = $_POST['id'];
	
	if ($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header('Location: http://rivers.camlittle.com/');
		exit();
	}
	
	$sql_string = "UPDATE playspots SET playspot_name = '$playspot_name', gauge_max = '$gauge_max', gauge_min = '$gauge_min', description = '$description', date_modified = '$now', updated_by = '" . $_SESSION['SESS_USERNAME'] . "' WHERE id = '$id'";
	performSQL($sql_string, 'playspot');
?>