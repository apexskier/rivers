<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/database.php");

if (!$logged_in) {
	echo "<h4>Not logged in</h4>";
	exit;
}
?>
<h3>Edit Rapid<?php
                if ($_GET['name'] != "") {
                    $name = ": " . $_GET['name'];
                } else {
                    $name = " " . $_GET['database_id'];
                }
                echo $name;
                ?></h3>
<form name="add-rapid" method="post" action="/app/php/actions/edit/rapid.php">
	<input type="hidden" name="id" value="<?php echo $_GET['database_id']; ?>">
	
	<label name="rapid_name">Name</label>
	<input type="text" name="rapid_name" value="<?php echo $_GET['name']; ?>">
	
	<label name="class">Class</label>
	<div class="class-slider"></div><span class="class-slider-value help-block"><?php echo $_GET['rating']; ?></span>
	<input type="hidden" name="class" id="class-value" value="<?php echo $_GET['rating']; ?>">
	
	<label name="description">Description</label>
	<textarea name="description"><?php echo $_GET['description']; ?></textarea>
	
	<button type="submit" class="btn submit">Submit</button>
</form>