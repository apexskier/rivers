<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/actions/add/head.php");
	
	//function createThumb() {
		
	//}
	
	$photo_name = trim(htmlspecialchars($_POST['photo_name'], ENT_QUOTES));
	$description = nl2br(trim(htmlspecialchars($_POST['description'], ENT_QUOTES)));
	$associated_id = trim($_POST['associated_id']);
	$associated_type = trim($_POST['associated_type']);
	$flow = trim(htmlspecialchars($_POST['photo_flow']));
	$flow_units = $_POST['flow_units'];
	$river_id = 0;
	
	$web_max = 1024;
	$thumb_height = 150;
	
	if ($associated_id == "") {
		$errmsg_arr[] = "No element provided.";
		$errflag = true;
	} else {
		$get_river_query = mysql_query("SELECT river FROM $associated_type" . "s WHERE id = $associated_id");	
		while ($river = mysql_fetch_array($get_river_query, MYSQL_ASSOC)) {
			$river_id = $river['river'];
		}
	}
	
	$allowedExts = array("jpg", "jpeg");
	$maxFileSize = 3000000; // 3MB
	$extension = strtolower(end(explode(".", $_FILES["file"]["name"])));
	
	if (!$errflag) {
		if ((($_FILES["file"]["type"] == "image/jpeg")
		  || ($_FILES["file"]["type"] == "image/pjpeg"))
		  && in_array($extension, $allowedExts)) {
			if ($_FILES["file"]["size"] < $maxFileSize) {
				if ($_FILES["file"]["error"] > 0) {
					$errmsg_arr[] = "Error: " . $_FILES["file"]["error"] . "<br />";
					$errflag = true;
				} else {
					if (mysql_query("INSERT INTO photos (title, description, file_type, associated_id, associated_type, river, flow, flow_units, date_added, user) VALUES ('$photo_name', '$description', '$extension', '$associated_id', '$associated_type', '$river_id', '$flow', '$flow_units', '$now', '" . $_SESSION['SESS_USERNAME'] . "')")) {
						$id = mysql_insert_id();
						$file = $_FILES["file"]["tmp_name"];
						$original_size = getimagesize($file);
						$original_img = imagecreatefromjpeg($file);

						// store original image
						move_uploaded_file($file, $_SERVER['DOCUMENT_ROOT'] . "/img/user/uploaded/original/$id." . $extension);
						
						// generate and store thumbnail
						$thumb_width = ($thumb_height * $original_size[0]) / $original_size[1];
						$thumb_img = imagecreatetruecolor($thumb_width, $thumb_height);
						if ($thumb_img) {
							if (imagecopyresampled($thumb_img, $original_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $original_size[0], $original_size[1])) {
								imagejpeg($thumb_img, $_SERVER['DOCUMENT_ROOT'] . "/img/user/uploaded/thumb/$id." . $extension, 100);
							} else {
								$text_color = imagecolorallocate($thumb_img, 233, 14, 91);
								imagestring($thumb_img, 1, 5, 5,  'Image error', $text_color);
							}
						} else {
							$errmsg_arr[] = "Something went wrong generating the thumbnail";
							$errflag = true;
						}
						
						// generate and store web image
						if ($original_size[0] > $original_size[1]) { // width greater than height
							$web_width = $web_max;
							$web_height = ($web_width * $original_size[1]) / $original_size[0];
						} else { // height greater than width
							$web_height = $web_max;
							$web_width = ($web_height * $original_size[0]) / $original_size[1];
						}
						if ($web_width > $original_size[0]) { // if original was smaller than web size
							if (!copy($_SERVER['DOCUMENT_ROOT'] . "/img/user/uploaded/original/$id." . $extension, $_SERVER['DOCUMENT_ROOT'] . "/img/user/uploaded/web/$id." . $extension)) {
								$errmsg_arr[] = "Something went wrong generating the web sized image";
								$errflag = true;
							}
						} else {
							$web_img = imagecreatetruecolor($web_width, $web_height);
							if ($web_img) {
								if (imagecopyresampled($web_img, $original_img, 0, 0, 0, 0, $web_width, $web_height, $original_size[0], $original_size[1])) {
									imagejpeg($web_img, $_SERVER['DOCUMENT_ROOT'] . "/img/user/uploaded/web/$id." . $extension, 100);
								} else {
									$errmsg_arr[] = "Something went wrong generating the web sized image";
									$errflag = true;
								}
							} else {
								$errmsg_arr[] = "Something went wrong generating the web sized image";
								$errflag = true;
							}
						}
						
						if (!$errflag) {
							$_SESSION['SUCCESS'] = "Successfully uploaded photo.";
						}
						session_write_close();
						mysql_close($link);
						if (isset($_SERVER['HTTP_REFERER'])) {
							header('Location: ' . $_SERVER['HTTP_REFERER']);
						} else {
							header('Location: http://rivers.camlittle.com/');
						}
						exit();
					} else {
						$errmsg_arr[] = "Error: " . mysql_error();
						$errflag = true;
					}
				}
			} else {
				$errmsg_arr[] = "Invalid file: too large.";
				$errflag = true;
			}
		} else {
			$errmsg_arr[] = "Invalid file type.";
			$errflag = true;
		}
	}
	$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
	session_write_close();
	header('Location: http://rivers.camlittle.com/');
?>