<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/actions/edit/head.php");
	
	$run_name = trim(htmlspecialchars($_POST['run_name'], ENT_QUOTES));
	$gauge_max = trim($_POST['gauge_max']);
	$gauge_min = trim($_POST['gauge_min']);
	$description = nl2br(trim(htmlspecialchars($_POST['description'], ENT_QUOTES)));
	$now = date("Y-m-d H:i:s");
	$id = $_POST['id'];
	
	$sql_string = "UPDATE runs SET run_name = '$run_name', gauge_max = '$gauge_max', gauge_min = '$gauge_min', description = '$description', date_modified = '$now', updated_by = '" . $_SESSION['SESS_USERNAME'] . "' WHERE id = '$id'";
	performSQL($sql_string, 'run');	
?>