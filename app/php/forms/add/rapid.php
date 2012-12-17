<h3>Add a rapid</h3>
<form name="add-rapid" method="post" action="/app/php/actions/add/rapid.php">
	<label name="rapid_name">Name</label>
	<input type="text" name="rapid_name">
	
	<label name="class">Class</label>
	<div class="class-slider"></div>
	<div class="help-block"><span class="class-slider-value">I</span></div>
	<input type="hidden" name="class" id="class-value" value="I">

	<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/templates/rivers-options.php"); ?>
	<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/templates/pickermap.php"); ?>
	
	<label name="description">Description</label>
	<textarea name="description"></textarea>
	
	<button type="submit" name="submit" class="btn btn-primary submit">Submit</button>
</form>