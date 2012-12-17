<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/scripts/add-exec-head.php");
	
	$video_url = trim(htmlspecialchars($_POST['video_url'], ENT_QUOTES));
	$associated_id = trim($_POST['associated_id']);
	$associated_type = trim($_POST['associated_type']);
	$flow = trim(htmlspecialchars($_POST['video_flow']));
	$flow_units = $_POST['flow_units'];
	$river_id = 0;
	$video_matches = array();
	
	if ($video_url == "") {
		$errmsg_arr[] = "No url provided.";
		$errflag = true;
	} else {
		if (preg_match("((https?://(vimeo.com|player.vimeo.com/video)/\d{8}(?!\w))|(https?://(www.youtube.com/|youtu.be/)((watch\?)(feature=(\w)*&)?(v=)|embed/)?\w{11}(?!\w))|(https?://www.facebook.com/(video/(editvideo.php\?v=|video.php\?v=)|v/|photo.php\?v=)\d{13}(?!\w)))", $video_url, $video_matches) > 0) {
			$video_url = $video_matches[0];
			$video_source = array();
			$video_id = array();
			preg_match("(vimeo|youtube|facebook)", $video_url, $video_source);
			switch ($video_source[0]) {
				case "vimeo":
					preg_match("(\d{8})", $video_url, $video_id);
					break;
				case "youtube":
					preg_match("(\w{11})", $video_url, $video_id);
					break;
				case "facebook":
					preg_match("(\d{13})", $video_url, $video_id);
					break;
				default:
					$errmsg_arr[] = "Invalid video source.";
					$errflag = true;
					break;
			}	
			if ($associated_id == "") {
				$errmsg_arr[] = "No element provided.";
				$errflag = true;
			} else {
				$match_video_query = mysql_query("SELECT video_id FROM videos WHERE video_id = '" . $video_id[0] . "'");	
				if (mysql_num_rows($match_video_query) > 0) {
					$errmsg_arr[] = "Video already in database.";
					$errflag = true;
				}
			}
		} else {
			$errmsg_arr[] = "Invalid URL.";
			$errflag = true;
		}
	}
	
	if ($associated_id == "") {
		$errmsg_arr[] = "No element provided.";
		$errflag = true;
	} else {
		$get_river_query = mysql_query("SELECT river FROM $associated_type" . "s WHERE id = $associated_id");	
		while ($river = mysql_fetch_array($get_river_query, MYSQL_ASSOC)) {
			$river_id = $river['river'];
		}
	}
	
	$sql_string = "INSERT INTO videos (video_id, video_type, associated_id, associated_type, river, flow, flow_units, date_added, user) VALUES ('" . $video_id[0] . "', '" . $video_source[0] . "', '$associated_id', '$associated_type', '$river_id', '$flow', '$flow_units', '$now', '" . $_SESSION['SESS_USERNAME'] . "')";
	if ($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header('Location: http://rivers.camlittle.com/');
		exit();
	} else {
		performSQL($sql_string, 'video');
	}
?>