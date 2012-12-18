<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/database.php");

if (!$logged_in) {
	echo "<h4>Not logged in</h4>";
	exit;
}
?>
<h3>Edit Marker<?php
                if ($_GET['name'] != "") {
                    $name = ": " . $_GET['name'];
                } else {
                    $name = " " . $_GET['database_id'];
                }
                echo $name;
                ?></h3>
<form name="add-marker" method="post" action="/app/php/actions/edit/marker.php">
	<input type="hidden" name="id" value="<?php echo $_GET['database_id']; ?>">
	
	<label name="marker_name">Name</label>
	<input type="text" name="marker_name" value="<?php echo $_GET['name']; ?>">
	
	<label name="description">Description</label>
	<textarea name="description"><?php echo $_GET['description']; ?></textarea>
	
	<button type="submit" class="btn submit">Submit</button>
</form>