<?php

function new_gmaps_marker($id, $item_type, $name, $position, $title, $data, $type) {
	echo "$name = createMarker($id, \"$item_type\", $position, \"$title\", \"$data\", \"$type\");\n";
}

function getRiver($river_id) {
	if (isset($river_id) || $river_id != 0 || $river_id != '0' || $river_id != '') { // make sure a valid id is being passed
		// get the name from the database
		$rivername_query = mysql_query("SELECT * FROM rivers WHERE id = " . $river_id);
		while ($river = mysql_fetch_array($rivername_query, MYSQL_ASSOC)) {
			$river_name = $river['river_name'];
		}
		@mysql_free_result($rivername_query);
		
		// convert river name to printable form (look for creek or river in the name and add river if not found
		$river_name = ucwords($river_name);
		if (!strpos($river_name, "River") && !strpos($river_name, "Creek")) {
			$river_name .= " River";
		}
		return $river_name;
	} else {
		return false;
	}
}

function getDescription($data, $type) {
	$river_name = getRiver($data['river']);
	
	if ($type != 'playspot' && $type !== 'run') {
		// meta
		$description = "<p class='meta'>";
		if ($river_name) {
			$description .= "<strong><a href='/river/" . str_replace(' ', '-', strtolower($river_name)) . "'>$river_name</a></strong>";
		}
		switch ($type) {
			case 'marker':
				$description .= " - " . $data['marker_type'] . "";
				break;
			case 'rapid':
				$description .= " - Class " . $data['class'] . " rapid";
			case 'playspot':
		}
		$description .= "</p>";
	}
	
	// description
	if ($data['description'] !== "") {
		$description .= "<p>" . $data['description'] . "</p>";
	}
	
	// media
	$description .= getMedia($data['id'], $type, 'h4');
	
	// added/updated
	$description .= "<p><small>Added by <a href='/user/" . $data['user'] . "'>" . $data['user'] . "</a> on " . date("F j, Y g:i a", strtotime($run['date_added'])) . ".";
	if (!is_null($marker['updated_by'])) {
		$description .= " Updated by <a href='/user/" . $data['updated_by'] . "'>" . $data['updated_by'] . "</a> on " . date("F j, Y g:i a", strtotime($data['date_modified'])) . ".";
	}
	$description .= "</small></p>";
	
	// edit button
	$description .= loadEditButton($data['id'], $type);
	
	return $description;
}

function loadEditButton($id, $type) {
	$string = "<div class='edit'><form method='POST' action='edit-$type.php'>";
	$string .= "	<input type='submit' value='Edit $type'>";
	$string .= "	<input type='hidden' name='id' value='$id'>";
	$string .= "</div>";
	return $string;
}

function getMedia($id, $type, $header_level) {
	$videos = array();
	$video_query = mysql_query("SELECT * FROM videos WHERE associated_id = $id AND associated_type = '$type'");
	if ($video_query) {
		while ($video = mysql_fetch_array($video_query, MYSQL_ASSOC)) {
			$video_html = "";
			switch ($video['video_type']) {
				case 'vimeo':
					$video_html .= "<iframe src='http://player.vimeo.com/video/" . $video['video_id'] . "' width='560' height='315' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";
					break;
				case 'youtube':
					$video_html .= "<iframe width='560' height='315' src='http://www.youtube.com/embed/" . $video['video_id'] . "' frameborder='0' allowfullscreen></iframe>";
					break;
				case 'facebook':
					$video_html .= "<object width='560' height='315'><param name='allowfullscreen' value='true'></param><param name='movie' value='https://www.facebook.com/v/" . $video['video_id'] . "'></param><embed src='https://www.facebook.com/v/" . $video['video_id'] . "' type='application/x-shockwave-flash' allowfullscreen='1' width='560' height='315'></embed></object>";
					break;
				default:
					null;
					break;
			}
			$video_html .= "<p><small>Video added by <a href='/user/" . $video['user'] . "'>" . $video['user'] . "</a> on " . date("F j, Y g:i a", strtotime($video['date_added']));
			if ($video['flow'] != "" && $video['flow'] != "0") {
				$video_html .= " and taken at " . $video['flow'] . " " . $video['flow_units'];
			}
			$video_html .= ".</small></p>";
			array_push($videos, $video_html);
		}
	}
	@mysql_free_result($video_query);
	
	$photos = array();
	$photo_query = mysql_query("SELECT * FROM photos WHERE associated_id = $id AND associated_type = '$type'");
	if ($photo_query) {
		while ($photo = mysql_fetch_array($photo_query, MYSQL_ASSOC)) {
			$photo_html = "<div class='photo' data-photo-id='" . $photo['id'] . "." . $photo['file_type'] . "' data-title='" . $photo['title'] . "' data-description='<p>";
			if ($photo['description'] != "") {
				$photo_html .= $photo['description'] . "</p><p>";
			}
			$photo_html .= "<small>Photo added by <a href=&quot;/user/" . $photo['user'] . "&quot;>" . $photo['user'] . "</a> on " . date("F j, Y g:i a", strtotime($photo['date_added']));
				if ($photo['flow'] != "" && $photo['flow'] != "0") {
					$photo_html .= " and taken at " . $photo['flow'] . " " . $photo['flow_units'];
				}
				$photo_html .= ".</small>'>";
			
				$imgdata = getimagesize($_SERVER['DOCUMENT_ROOT'] . "/img/user/uploaded/thumb/" . $photo['id'] . "." . $photo['file_type']);
				$photo_html .= "<img src='/img/user/uploaded/thumb/" . $photo['id'] . "." . $photo['file_type'] . "' height='150' width='" . $imgdata[0] . "'>";
				if ($photo['flow'] != "") {
					$photo_html .= "<span class='meta'>" . $photo['flow'] . " " . $photo['flow_units'] . "</span>";
				}
			$photo_html .= "</div>";
			array_push($photos, $photo_html);
		}
	}
	@mysql_free_result($photo_query);
	
	$output = "";
	if (count($photos) > 0) {
		$output .= "<$header_level>Photos</$header_level><div class='photos clearfix'>";
		foreach ($photos as $photo) {
			$output .= $photo;
		}
		$output .= "</div>";
	}
	if (count($videos) > 0) {
		$output .= "<$header_level>Videos</$header_level>";
		foreach ($videos as $video) {
			$output .= $video;
		}
	}
	
	return $output;
}

?>