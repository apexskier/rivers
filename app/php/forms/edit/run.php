<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/database.php");

if (!$logged_in) {
	echo "<h4>Not logged in</h4>";
	exit;
}
?>
<h3>Edit Run<?php
                if ($_GET['name'] != "") {
                    $name = ": " . $_GET['name'];
                } else {
                    $name = " " . $_GET['database_id'];
                }
                echo $name;
                ?></h3>
<form name="add-run" method="post" action="/app/php/actions/edit/run.php">
	<input type="hidden" name="id" value="<?php echo $_GET['database_id']; ?>">
	
	<label name="run_name">Name</label>
	<input type="text" name="run_name" value="<?php echo $_GET['name']; ?>">
	
	<label name="gauge_min">Gauge Min</label>
	<input type="number" name="gauge_min" value="<?php echo $_GET['gauge']['min']; ?>">
	<label name="gauge_max">Gauge Max</label>
	<input type="number" name="gauge_max" value="<?php echo $_GET['gauge']['max']; ?>">
	
	<label name="description">Description</label>
	<textarea name="description"><?php echo $_GET['description']; ?></textarea>
	
	<button type="submit" class="btn submit">Submit</button>
</form>