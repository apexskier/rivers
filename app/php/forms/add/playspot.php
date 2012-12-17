<h3>Add a playspot</h3>
<form name="add-playspot" method="post" action="/app/php/actions/add/playspot.php">
	<label name="playspot_name">Name</label>
	<input type="text" name="playspot_name">
	
	<label name="gauge_id">Gauge</label>
	<div class="help-block"><span class="gauge_title">Click on a gauge on the map to select it.</span></div>
	<input type="hidden" name="gauge_id" id="gauge_id">
	<label name="gauge_min">Gauge Min</label>
	<input type="number" name="gauge_min">
	<label name="gauge_max">Gauge Max</label>
	<input type="number" name="gauge_max">

	<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/templates/rivers-options.php"); ?>
	<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/templates/pickermap.php"); ?>
	
	<label name="description">Description</label>
	<textarea name="description"></textarea>
	
	<button type="submit" name="submit" class="btn btn-primary submit">Submit</button>
</form>