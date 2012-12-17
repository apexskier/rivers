<h3>Add a video</h3>
<form name="add-video" method="post" action="/app/php/actions/add/video.php">
	<label name="video_url">Video URL</label>
	<input type="text" name="video_url" required>
	
	<label name="associated_title">Associated map element</label>
	<span class="associated_title help-block">Click on something on the map to select it.</span>
	<input type="hidden" name="associated_id" id="associated_id">
	<input type="hidden" name="associated_type" id="associated_type">
	
	<label name="video_flow">Flow</label>
	<div class="input-append">
		<input type="text" name="video_flow">
		<select name="flow_units" class="flow_units">
			<option value="cfs">cfs</option>
			<option value="ft">ft</option>
		</select>
	</div>
	
	
	<button type="submit" name="submit" class="btn btn-primary submit">Submit</button>
</form>