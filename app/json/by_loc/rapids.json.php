<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/database.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/functions.php");

$lat = intval($_GET['lat']);
$lng = intval($_GET['lng']);
	
if (isset($_GET['lat']) && $lat >= -90 && $lat <= 90
 && isset($_GET['lng']) && $lng >= -180 && $lng <= 180) {

	
	$cachefile = $_SERVER['DOCUMENT_ROOT'] . "/app/cache/json/by_loc/rapids/" . $lat . "_" . $lng . ".json";
	
	$updated;
	$check_cache_query = mysql_query("SELECT rapid FROM cache_times WHERE id = 1");
	while ($cache_time = mysql_fetch_array($check_cache_query, MYSQL_ASSOC)) {
		$updated = $cache_time['rapid'];
	}
	if (file_exists($cachefile)
	&& (strtotime($updated) < filemtime($cachefile))
	&& (time() - (15 * 60) < filemtime($cachefile))) {
		include($cachefile);
		exit;
	}
	
	ob_start();

	$query = mysql_query("SELECT * FROM rapids WHERE lat LIKE '$lat.%' AND lng LIKE '$lng.%'");
	
	$rapids = array();
	$idcounter = 0;
	
	while ($rapid = mysql_fetch_array($query)) {
		if ($rapid['rapid_name'] == "") {
			$rapid_name = null;
		} else {
			$rapid_name = $rapid['rapid_name'];
		}
		
		$data = array('id'           => $idcounter,
		              'database_id'  => intval($rapid['id']),
		              'name'         => $rapid_name,
		              'lat'			 => $rapid['lat'],
		              'lng'			 => $rapid['lng'],
		              'type'		 => "rapid",
		              'river_name'	 => getRiver($rapid['river']),
		              'river_id'     => $rapid['river'],
		              'rating'		 => $rapid['class'],
		              'description'	 => $rapid['description'],
		              'created_user' => $rapid['user'],
		              'created_date' => $rapid['date_added'],
		              'updated_user' => $rapid['updated_by'],
		              'updated_date' => $rapid['date_modified']);
		
		array_push($rapids, $data); 
		$idcounter++;
	}
	
	mysql_free_result($query);

	echo json_encode($rapids);

	$file = fopen($cachefile, 'w'); 
	fwrite($file, ob_get_contents()); 
	fclose($file); 
	
	ob_end_flush(); 
} else {
	echo "invalid params";
}

?>