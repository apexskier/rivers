<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/actions/edit/head.php");
	
	$marker_name = trim(htmlspecialchars($_POST['marker_name'], ENT_QUOTES));
	$description = nl2br(trim(htmlspecialchars($_POST['description'], ENT_QUOTES)));
	$now = date("Y-m-d H:i:s");
	$id = $_POST['id'];
	
	$sql_string = "UPDATE markers SET marker_name = '$marker_name', description = '$description', date_modified = '$now', updated_by = '" . $_SESSION['SESS_USERNAME'] . "' WHERE id = '$id'";
	performSQL($sql_string, 'marker');

?>