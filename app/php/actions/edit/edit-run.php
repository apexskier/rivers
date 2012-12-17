<?php require($_SERVER['DOCUMENT_ROOT'] . "/app/php/database.php"); ?>
<?php

if (!$logged_in) {
	$errmsg_arr[] = "Not logged in.";
	$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
	session_write_close();
	header('Location: http://rivers.camlittle.com/');
	exit();
}

$query = mysql_query("SELECT * FROM runs WHERE id = '" . $_POST['id'] . "'");

while ($run = mysql_fetch_array($query, MYSQL_ASSOC)) {
?>
<h3>Edit Run <?php echo $run['id']; ?></h3>
<form name="add-run" action="scripts/edit-run-exec.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="id" value="<?php echo $run['id']; ?>">
	
	<p><label name="run_name">Name<br /></label>
	<input type="text" name="run_name" value="<?php echo $run['run_name']; ?>"></p>
	
	<p><label name="gauge_min">Gauge Min<br /></label>
	<input type="number" name="gauge_min" value="<?php echo $run['gauge_min']; ?>"></p>
	<p><label name="gauge_max">Gauge Max<br /></label>
	<input type="number" name="gauge_max" value="<?php echo $run['gauge_max']; ?>"></p>
	
	<p><label name="description">Description</label><br />
	<textarea name="description"><?php echo $run['description']; ?></textarea></p>
	
	<p><input type="submit" name="submit"></p>
</form>
<?php
}
?>