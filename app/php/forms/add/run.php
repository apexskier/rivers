<h3>Add a run</h3>
<form name="add-run" action="app/php/actions/add/run.php" method="post" enctype="multipart/form-data">
	<label name="run_name">Name</label>
	<input type="text" name="run_name">
	
	<label name="gauge_id">Gauge</label>
	<div class="help-block"><span class="gauge_title">Click on a gauge on the map to select it.</span></div>
	<input type="hidden" name="gauge_id" id="gauge_id">
	<label name="gauge_min">Gauge Min</label>
	<input type="number" name="gauge_min">
	<label name="gauge_max">Gauge Max</label>
	<input type="number" name="gauge_max">
	
	<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/templates/rivers-options.php"); ?>
	
	<label>Class</label>
	<div class="run-class-slider"></div>
	<div class="help-block"><span class="run-class-slider-value-low">III</span><span class="run-class-slider-value-high">/IV</span></div>
	<input type="hidden" name="class" id="class-value" value="III/IV">
	
	<label name="description">Description</label>
	<textarea name="description"></textarea>
	
	<input type="file" name="file" id="file" />
	<span class="help-block">Submit a .gpx file</span>
	
	<button type="submit" name="submit" class="btn btn-primary submit">Submit</button>
</form>