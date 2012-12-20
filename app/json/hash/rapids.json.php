<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/database.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/functions.php");

$cachefile = $_SERVER['DOCUMENT_ROOT'] . "/app/cache/json/hash/rapids.json";

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

$query = mysql_query("SELECT id, lat, lng FROM rapids");

$rapids = array();
$idcounter = 0;

while ($rapid = mysql_fetch_array($query)) {
	
	$data = array('id'          => $idcounter,
	              'database_id' => intval($rapid['id']),
	              'loc'         => array('lat' => intval($rapid['lat']),
	                                     'lng' => intval($rapid['lng'])));
	
	array_push($rapids, $data);
	$idcounter++;
}

mysql_free_result($query);

echo json_encode($rapids);

$file = fopen($cachefile, 'w'); 
fwrite($file, ob_get_contents()); 
fclose($file); 

ob_end_flush(); 

?>