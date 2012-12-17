<?php require($_SERVER['DOCUMENT_ROOT'] . "/app/php/database.php"); ?>
<?php

if (!$logged_in) {
	$errmsg_arr[] = "Not logged in.";
	$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
	session_write_close();
	header('Location: http://rivers.camlittle.com/');
	exit();
}

$query = mysql_query("SELECT * FROM rapids WHERE id = '" . $_POST['id'] . "'");

while ($rapid = mysql_fetch_array($query, MYSQL_ASSOC)) {
?>
<h3>Edit Rapid <?php echo $rapid['id']; ?></h3>
<form name="add-rapid" method="post" action="scripts/edit-rapid-exec.php">
	<input type="hidden" name="id" value="<?php echo $rapid['id']; ?>">
	
	<p><label name="rapid_name">Name<br /></label>
	<input type="text" name="rapid_name" value="<?php echo $rapid['rapid_name']; ?>"></p>
	
	<p><label name="class">Class</label><br />
	<div class="class-slider"></div><span class="class-slider-value"><?php echo $rapid['class']; ?></span></p>
	<input type="hidden" name="class" id="class-value" value="<?php echo $rapid['class']; ?>">
	
	<p><label name="description">Description</label><br />
	<textarea name="description"><?php echo $rapid['description']; ?></textarea></p>
	
	<p><input type="submit" name="submit"></p>
</form>
<?php
}
?>