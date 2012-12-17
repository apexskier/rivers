<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/scripts/add-exec-head.php");
	
	$run_name = trim(htmlspecialchars($_POST['run_name'], ENT_QUOTES));
	$river = $_POST['river'];
	$gauge_id = $_POST['gauge_id'];
	$gauge_max = trim($_POST['gauge_max']);
	$gauge_min = trim($_POST['gauge_min']);
	$class = $_POST['class'];
	$description = nl2br(trim(htmlspecialchars($_POST['description'], ENT_QUOTES)));
	
	if ($class == "") {
		$errmsg_arr[] = "No class entered.";
		$errflag = true;
	}
	if ($river == 0) {
		$errmsg_arr[] = "No river entered.";
		$errflag = true;
	}
	
	$allowedExts = array("gpx", "GPX");
	$extension = end(explode(".", $_FILES["file"]["name"]));
	
	if (in_array($extension, $allowedExts)) {
		if ($_FILES["file"]["error"] > 0) {
			$errmsg_arr[] = "Error: " . $_FILES["file"]["error"] . "<br />";
			$errflag = true;
		} else {
			// get contents of file
			$contents = file_get_contents($_FILES['file']['tmp_name']);
			
			// read lat/lng values into arrays
			$lat_array = array();
			$lng_array = array();
			preg_match_all("((?<=<trkpt lat=\")((-?(|\.|\d)*)(?=\" lon=\"(-?(|\.|\d)*)\">)))", $contents, $lat_array);
			preg_match_all("((?<=(<trkpt lat=\"(\d\d)\.(\d\d\d\d\d)\" lon=\"))(-?(\d*)\.(\d*))(?=\">))", $contents, $lng_array);
			
			//   ((?<=(<trkpt lat=\"))((((-?|\.|\d)*\" lon=\")(-?(|\.|\d)*))(?=(\">)))), then (?<=()\"\slon=")(-?(\d*)[.](\d)*)
			
			// add run to array
			if (mysql_query("INSERT INTO runs (run_name, river, gauge_id, gauge_max, gauge_min, class, description, date_added, user) VALUES ('$run_name', '$river', '$gauge_id', '$gauge_max', '$gauge_min', '$class', '$description', '$now', '" . $_SESSION['SESS_USERNAME'] . "')")) {
				
				// get run id
				$query = mysql_query("SELECT * FROM runs WHERE date_added = '$now'");
				$run = mysql_fetch_array($query, MYSQL_ASSOC);
				$run_id = "run_" . $run['id'];
				
				// add run points table
				mysql_query("CREATE TABLE $run_id(id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), lat DECIMAL(13,10), lng DECIMAL(13,10))") or die(mysql_error());
				foreach ($lat_array[0] as $key => $value) {
					if (!mysql_query("INSERT INTO $run_id (lat, lng) VALUES ('$value', '" . $lng_array[0][$key] . "')")) {
						$errmsg_arr[] = "Error pushing $key: $value to table. <br>" . mysql_error();
						$errflag = true;
					}
				}
				
				mysql_query("UPDATE users SET contributions = contributions + 5 WHERE username = '" . $_SESSION['SESS_USERNAME'] . "'");
				$cache_query = mysql_query("UPDATE cache_times SET run = '" . date("Y-m-d H:i:s") . "' WHERE id = 1");
				if ($cache_query) {
					$_SESSION['SUCCESS'] = "Successfully added run.";
					session_write_close();
					mysql_close($link);
					header('Location: http://rivers.camlittle.com/');
					exit();
				} else {
					$errmsg_arr[] = "Error updating cache times in database: " . mysql_error();
				}
			} else {
				$errmsg_arr[] = "Error: " . mysql_error();
				$errflag = true;
			}	
		}
	} else {
		$errmsg_arr[] = "Invalid file type.";
		$errflag = true;
	}
	
	onError();

?>