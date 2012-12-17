<?php require($_SERVER['DOCUMENT_ROOT'] . "/app/php/database.php"); ?>
<?php

if (!$logged_in) {
	$errmsg_arr[] = "Not logged in.";
	$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
	session_write_close();
	header('Location: http://rivers.camlittle.com/');
	exit();
}

$query = mysql_query("SELECT * FROM markers WHERE id = '" . $_POST['id'] . "'");

while ($marker = mysql_fetch_array($query, MYSQL_ASSOC)) {
?>
<h3>Edit Marker <?php echo $marker['id']; ?></h3>
<form name="add-marker" method="post" action="scripts/edit-marker-exec.php">
	<input type="hidden" name="id" value="<?php echo $marker['id']; ?>">
	
	<p><label name="marker_name">Name<br /></label>
	<input type="text" name="marker_name" value="<?php echo $marker['marker_name']; ?>"></p>
	
	<p><label name="description">Description</label><br />
	<textarea name="description"><?php echo $marker['description']; ?></textarea></p>
	
	<p><input type="submit" name="submit"></p>
</form>
<?php
}
?>