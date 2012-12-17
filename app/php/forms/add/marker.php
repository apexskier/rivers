<h3>Add a marker</h3>
<form name="add-marker" method="post" action="/app/php/actions/add/marker.php">
	<label name="marker_name">Name</label>
	<input type="text" name="marker_name">
	
	<label name="marker_type">Type</label>
	<select name="marker_type">
		<option value="River Access">River Access (Put In/Take Out)</option>
		<option value="Fork">River Fork</option>
	</select>

	<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/templates/rivers-options.php"); ?>
	<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/templates/pickermap.php"); ?>
	
	<label name="description">Description</label>
	<textarea name="description"></textarea>
	
	<button type="submit" name="submit" class="btn btn-primary submit">Submit</button>
</form>