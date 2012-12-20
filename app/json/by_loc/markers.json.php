<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/database.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/functions.php");

$lat = intval($_GET['lat']);
$lng = intval($_GET['lng']);
	
if (isset($_GET['lat']) && $lat >= -90 && $lat <= 90
 && isset($_GET['lng']) && $lng >= -180 && $lng <= 180) {

	
	$cachefile = $_SERVER['DOCUMENT_ROOT'] . "/app/cache/json/by_loc/markers/" . $lat . "_" . $lng . ".json";
	
	$updated;
	$check_cache_query = mysql_query("SELECT marker FROM cache_times WHERE id = 1");
	while ($cache_time = mysql_fetch_array($check_cache_query, MYSQL_ASSOC)) {
		$updated = $cache_time['marker'];
	}
	if (file_exists($cachefile)
	&& (strtotime($updated) < filemtime($cachefile))
	&& (time() - (15 * 60) < filemtime($cachefile))) {
		include($cachefile);
		exit;
	}
	
	ob_start();

	$query = mysql_query("SELECT * FROM markers WHERE lat LIKE '$lat.%' AND lng LIKE '$lng.%'");
	
	$markers = array();
	$idcounter = 0;
	
	while ($marker = mysql_fetch_array($query)) {
		if ($marker['marker_name'] == "") {
			$marker_name = null;
		} else {
			$marker_name = $marker['marker_name'];
		}
		
		$data = array('id'           => $idcounter,
		              'database_id'  => intval($marker['id']),
		              'name'         => $marker_name,
		              'lat'			 => $marker['lat'],
		              'lng'			 => $marker['lng'],
		              'type'		 => $marker['marker_type'],
		              'river_name'	 => getRiver($marker['river']),
		              'river_id'     => $marker['river'],
		              'description'	 => $marker['description'],
		              'created_user' => $marker['user'],
		              'created_date' => $marker['date_added'],
		              'updated_user' => $marker['updated_by'],
		              'updated_date' => $marker['date_modified']);
		
		array_push($markers, $data); 
		$idcounter++;
	}
	
	mysql_free_result($query);

	echo json_encode($markers);

	$file = fopen($cachefile, 'w'); 
	fwrite($file, ob_get_contents()); 
	fclose($file); 
	
	ob_end_flush(); 
} else {
	echo "invalid params";
}

?>