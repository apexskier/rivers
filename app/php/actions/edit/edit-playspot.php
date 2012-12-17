<?php require($_SERVER['DOCUMENT_ROOT'] . "/includes/database-connect.php"); ?>
<?php

if (!$logged_in) {
	$errmsg_arr[] = "Not logged in.";
	$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
	session_write_close();
	header('Location: http://rivers.camlittle.com/');
	exit();
}

$query = mysql_query("SELECT * FROM playspots WHERE id = '" . $_POST['id'] . "'");

while ($playspot = mysql_fetch_array($query, MYSQL_ASSOC)) {
?>
<h3>Edit Playspot <?php echo $playspot['id']; ?></h3>
<form name="add-playspot" method="post" action="scripts/edit-playspot-exec.php">
	<input type="hidden" name="id" value="<?php echo $playspot['id']; ?>">

	<p><label name="playspot_name">Name<br /></label>
	<input type="text" name="playspot_name" value="<?php echo $playspot['playspot_name']; ?>"></p>
	
	<p><label name="gauge_min">Gauge Min<br /></label>
	<input type="number" name="gauge_min" value="<?php echo $playspot['gauge_min']; ?>"></p>
	<p><label name="gauge_max">Gauge Max<br /></label>
	<input type="number" name="gauge_max" value="<?php echo $playspot['gauge_max']; ?>"></p>
	
	<p><label name="description">Description</label><br />
	<textarea name="description"><?php echo $playspot['description']; ?></textarea></p>
	
	<p><input type="submit" name="submit"></p>
</form>
<?php
}
?>