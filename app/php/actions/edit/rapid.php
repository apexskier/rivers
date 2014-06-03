<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/actions/edit/head.php");
	
	$rapid_name = trim(htmlspecialchars($_POST['rapid_name'], ENT_QUOTES));
	$class = $_POST['class'];
	$description = nl2br(trim(htmlspecialchars($_POST['description'], ENT_QUOTES)));
	$now = date("Y-m-d H:i:s");
	$id = $_POST['id'];
	
	if ($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		if (isset($_SERVER['HTTP_REFERER'])) {
			header('Location: ' . $_SERVER['HTTP_REFERER']);
		} else {
			header('Location: http://rivers.camlittle.com/');
		}
		exit();
	}
	
	$sql_string = "UPDATE rapids SET rapid_name = '$rapid_name', class = '$class', description = '$description', date_modified = '$now', updated_by = '" . $_SESSION['SESS_USERNAME'] . "' WHERE id = '$id'";
	performSQL($sql_string, 'rapid');	
?>