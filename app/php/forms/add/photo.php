<h3>Add a photo</h3>
<form name="add-photo" method="post" action="scripts/add-photo-exec.php" enctype="multipart/form-data">
	<label name="photo_name">Name</label>
	<input type="text" name="photo_name">
	
	<label name="description">Description</label>
	<textarea name="description"></textarea>
	<span class="help-block">Describe the photo. If of a rapid, try to explain what direction the camera was facing in respect to the rapid.</span>

	<label name="associated_title">Associated map element</label>
	<span class="associated_title help-block">Click on something on the map to select it.</span>
	<input type="hidden" name="associated_id" id="associated_id" required>
	<input type="hidden" name="associated_type" id="associated_type">
	
	<label name="photo_flow">Flow</label>
	<div class="input-append">
		<input type="text" name="photo_flow">
		<select name="flow_units" class="flow_units">
			<option value="cfs">cfs</option>
			<option value="ft">ft</option>
		</select>
	</div>
	
	
	<input type="file" name="file" id="file" />
	<span class="help-block">Submit a .jpg file</span>
	
	<button type="submit" name="submit" class="btn btn-primary submit">Submit</button>
</form>