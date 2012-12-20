<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/database.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/functions.php");

$cachefile = $_SERVER['DOCUMENT_ROOT'] . "/app/cache/json/hash/markers.json";

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

$query = mysql_query("SELECT id, lat, lng FROM markers");

$markers = array();
$idcounter = 0;

while ($marker = mysql_fetch_array($query)) {
	
	$data = array('id'          => $idcounter,
	              'database_id' => intval($marker['id']),
	              'loc'         => array('lat' => intval($marker['lat']),
	                                     'lng' => intval($marker['lng'])));
	
	array_push($markers, $data);
	$idcounter++;
}

mysql_free_result($query);

echo json_encode($markers);

$file = fopen($cachefile, 'w'); 
fwrite($file, ob_get_contents()); 
fclose($file); 

ob_end_flush(); 

?>