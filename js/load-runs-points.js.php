<?php
Header("content-type: application/x-javascript");

require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/database-connect.php");

$cachefile = $_SERVER['DOCUMENT_ROOT'] . "/js/cache/load-runs-points-cache.js";

$updated;
$check_cache_query = mysql_query("SELECT run FROM cache_times WHERE id = 1");
while ($cache_time = mysql_fetch_array($check_cache_query, MYSQL_ASSOC)) {
	$updated = $cache_time['run'];
}
if (file_exists($cachefile) && (strtotime($updated) < filemtime($cachefile))) {
	echo "/* From cache generated " . date('g:i:s a', filemtime($cachefile)) . " */\n";
	include($cachefile);
	exit;
} else {
	echo "/* Cache generated " . date('g:i:s a') . " */\n";
}

ob_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/database-connect.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php");
?>

// run points
<?php 
$query = mysql_query("SELECT * FROM runs");
while ($run = mysql_fetch_array($query, MYSQL_ASSOC)) {
	$id = "run_" . $run['id'];

	$points_query = mysql_query("SELECT * FROM $id");
	while ($point = mysql_fetch_array($points_query, MYSQL_ASSOC)) {
		echo $id . "_points.push(new google.maps.LatLng(" . $point['lat'] . ", " . $point['lng'] . "));\n";
	}
	echo $id . ".setPath(" . $id . "_points);\n\n";
	@mysql_free_result($points_query);
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