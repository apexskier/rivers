<?php

Header("content-type: application/x-javascript");

require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/database-connect.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php");

$cachefile = $_SERVER['DOCUMENT_ROOT'] . "/js/cache/load-markers-cache.js";

$updated;
$check_cache_query = mysql_query("SELECT marker, video, rapid, photo FROM cache_times WHERE id = 1");
while ($cache_time = mysql_fetch_array($check_cache_query, MYSQL_ASSOC)) {
	$updated = $cache_time['gauge'];
	if ($updated < $cache_time['marker']) {
		$updated = $cache_time['marker'];
	}
	if ($updated < $cache_time['video']) {
		$updated = $cache_time['video'];
	}
	if ($updated < $cache_time['photo']) {
		$updated = $cache_time['photo'];
	}
	if ($updated < $cache_time['rapid']) {
		$updated = $cache_time['rapid'];
	}
}
if (file_exists($cachefile) && (strtotime($updated) < filemtime($cachefile)) && (time() - (15 * 60) < filemtime($cachefile))) {
	echo "/* From cache generated " . date('g:i:s a', filemtime($cachefile)) . " */\n";
	include($cachefile);
	exit;
} else {
	echo "/* Cache generated " . date('g:i:s a') . " */\n";
}

ob_start();
?>

var markers_array = new Array(),
    playspots_array = new Array(),
    playspots_in_array = new Array(),
    rapids_array = new Array(),
    gauges_array = new Array(),
    runs_array = new Array();

// markers
<?php

// Load markers
$query = mysql_query("SELECT * FROM markers");
$markers = array();
while ($marker = mysql_fetch_array($query, MYSQL_ASSOC)) {
	$id = "marker_" . $marker['id'];
	$position = "new google.maps.LatLng(" . $marker['lat'] . ", " . $marker['lng'] . ")";
	$description = getDescription($marker, 'marker');
		
	new_gmaps_marker($marker['id'], "marker", $id, $position, $marker['marker_name'], $description, $marker['marker_type']);
	echo "markers_array.push($id);\n";
}
@mysql_free_result($query);

?>

// rapids
<?php

// Load rapids
$query = mysql_query("SELECT * FROM rapids");
$rapids = array();
while ($rapid = mysql_fetch_array($query, MYSQL_ASSOC)) {
	$id = "rapid_" . $rapid['id'];
	$position = "new google.maps.LatLng(" . $rapid['lat'] . ", " . $rapid['lng'] . ")";
	$description = getDescription($rapid, 'rapid');
	
	new_gmaps_marker($rapid['id'], "rapid", $id, $position, $rapid['rapid_name'], $description, $rapid['class']);
	echo "rapids_array.push($id);\n";
}
@mysql_free_result($query);

?>

<?php

// open the cache file "cache/home.html" for writing
$fp = fopen($cachefile, 'w'); 
// save the contents of output buffer to the file
fwrite($fp, ob_get_contents()); 
// close the file
fclose($fp); 
// Send the output to the browser
ob_end_flush(); 

?>