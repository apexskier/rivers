<?php
Header("content-type: application/x-javascript");

require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/database-connect.php");

$cachefile = $_SERVER['DOCUMENT_ROOT'] . "/js/cache/load-runs-cache.js";

$updated;
$check_cache_query = mysql_query("SELECT gauge, playspot, run, video, photo FROM cache_times WHERE id = 1");
while ($cache_time = mysql_fetch_array($check_cache_query, MYSQL_ASSOC)) {
	$updated = $cache_time['gauge'];
	if ($updated < $cache_time['playspot']) {
		$updated = $cache_time['playspot'];
	}
	if ($updated < $cache_time['run']) {
		$updated = $cache_time['run'];
	}
	if ($updated < $cache_time['video']) {
		$updated = $cache_time['video'];
	}
	if ($updated < $cache_time['photo']) {
		$updated = $cache_time['photo'];
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
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php");
?>

// gauges
<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/js/load-gauges.js.php"); ?>

// playspots
<?php

// Load playspots
$query = mysql_query("SELECT * FROM playspots");
$playspots = array();
while ($playspot = mysql_fetch_array($query, MYSQL_ASSOC)) {
	$id = "playspot_" . $playspot['id'];
	$position = "new google.maps.LatLng(" . $playspot['lat'] . ", " . $playspot['lng'] . ")";
	$level_max = $playspot['gauge_max'];
	$level_min = $playspot['gauge_min'];
	
	$river_name = getRiver($playspot['river']);
	
	$description = "<p class='meta'><strong><a href='/river/" . str_replace(' ', '-', strtolower($river_name)) . "'>$river_name</a></strong>";
	if ($level_max != 0 && $level_min != 0) {
		$description .= ", In at $level_min to $level_max " . $gauges_units[$playspot['gauge_id']] . " (currently " . $gauges_current[$playspot['gauge_id']] . ") on the ";
		$description .= "<a onclick='gotoMarker(gauge_" . $playspot['gauge_id'] . ")'>";
		$description .= $gauges_title[$playspot['gauge_id']];
		$description .= "</a>";
	}
	$description .= "</p>";
	
	$description .= getDescription($playspot, 'playspot');
		
	if ($gauges_current[$playspot['gauge_id']] >= $level_min && $gauges_current[$playspot['gauge_id']] <= $level_max) {
		new_gmaps_marker($playspot['id'], "playspot", $id, $position, $playspot['playspot_name'], $description, "playspot_in");
		echo "playspots_in_array.push($id);\n";
	} else {
		new_gmaps_marker($playspot['id'], "playspot", $id, $position, $playspot['playspot_name'], $description, "playspot");
		echo "playspots_array.push($id);\n";
	}
}
@mysql_free_result($query);

?>

// runs
<?php
$query = mysql_query("SELECT * FROM runs");
while ($run = mysql_fetch_array($query, MYSQL_ASSOC)) {
	$id = "run_" . $run['id'];
	$level_max = $run['gauge_max'];
	$level_min = $run['gauge_min'];
	$gauge_id = $run['gauge_id'];
	$current_flow = $gauges_current[$gauge_id];
	$river_name = getRiver($run['river']);
	$description = "<p class='meta'><strong><a href='/river/" . str_replace(' ', '-', strtolower($river_name)) . "'>$river_name</a></strong>, Class " . $run['class'];
	if ($level_max != 0 && $level_min != 0) {
		$description .= ", Runnable at $level_min to $level_max " . $gauges_units[$gauge_id] . " (currently " . $current_flow . ") on the ";
		$description .= "<a onclick='gotoMarker(gauge_" . $gauge_id . ")'>";
		$description .= $gauges_title[$gauge_id];
		$description .= "</a>";
	}
	$description .= "</p>";
	
	$description .= getDescription($run, 'run');
	
	echo "var $id" . "_points = new Array();\n";
	echo "var $id = createRun(" . $run['id'] . ", \"run\", \"" . $run['run_name'] . "\", \"$description\");\n";
	echo "runs_array.push($id);\n";
	
	// run color
	echo "run_" . $run['id'] . ".setOptions({ strokeColor: '";
	if ($level_min == 0 || $level_max == 0) {
		echo "hsl(0, 0%, 0%)"; // black
	} else if ($current_flow < $level_min) {
		echo "hsl(0, 80%, 50%)"; // red
	} else if ($current_flow > $level_max) {
		echo "hsl(240, 80%, 50%)"; // blue
	}  else if ($current_flow >= $level_min && $current_flow <= $level_max) {
		$hue = $current_flow * 240 / $level_max;
		echo "hsl($hue, 80%, 50%)";
	} else {
		echo "hsl(0, 0%, 0%)";
	}
	echo "' });\n";
	
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