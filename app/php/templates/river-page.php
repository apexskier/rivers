<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/database.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/functions.php");

	$river_id = $_GET['river_id'];
	$get_river_query = mysql_query("SELECT * FROM rivers WHERE id = '$river_id'");
	if ($get_river_query && mysql_num_rows($get_river_query) > 0) {
		while ($river = mysql_fetch_array($get_river_query, MYSQL_ASSOC)) {
			$river_name = $river['river_name'];
		}
	}
	
	$printable_river_name = ucwords($river_name);
	if (!strpos($printable_river_name, "River") && !strpos($printable_river_name, "Creek")) {
		$printable_river_name .= " River";
	}
?>
<?php
	echo "<h2>" . $printable_river_name . "</h2>";
	
	$gauges_query = mysql_query("SELECT * FROM gauges WHERE river = $river_id");
	echo "<h3>";
	if (mysql_num_rows($gauges_query) == 0) {
		echo "No ";
	}
	echo "Gauges</h3>";
	echo "<ul>";
	while ($gauge = mysql_fetch_array($gauges_query, MYSQL_ASSOC)) {
		echo "<li>";
		echo "<h4>" . strtoupper($gauge['type']) . " Gauge " . $gauge['code'] . "</h4>";
		echo "</li>";
	}
	echo "</ul>";
	
	$markers_query = mysql_query("SELECT * FROM markers WHERE river = $river_id");
	echo "<h3>";
	if (mysql_num_rows($markers_query) == 0) {
		echo "No ";
	}
	echo "Markers</h3>";
	echo "<ul>";
	while ($marker = mysql_fetch_array($markers_query, MYSQL_ASSOC)) {
		echo "<li>";
		if ($marker['marker_name'] != "") {
			echo "<h4>" . $marker['marker_name'] . "</h4>";
		}
		echo "<p>" . $marker['marker_type'] . "</p>";
		if ($marker['description'] != "") {
			echo "<p>" . $marker['description'] . "</p>";
		}
		echo getMedia($marker['id'], 'marker', 'h5');
		echo "</li>";
	}
	echo "</ul>";
	
	$rapids_query = mysql_query("SELECT * FROM rapids WHERE river = $river_id");
	echo "<h3>";
	if (mysql_num_rows($rapids_query) == 0) {
		echo "No ";
	}
	echo "Rapids</h3>";
	echo "<ul>";
	while ($rapid = mysql_fetch_array($rapids_query, MYSQL_ASSOC)) {
		echo "<li>";
		if ($rapid['rapid_name'] != "") {
			echo "<h4>" . $rapid['rapid_name'] . "</h4>";
		}
		echo "<p>Class " . $rapid['class'] . "</p>";
		if ($rapid['description'] != "") {
			echo "<p>" . $rapid['description'] . "</p>";
		}
		echo getMedia($rapid['id'], 'rapid', 'h5');
		echo "</li>";
	}
	echo "</ul>";
	
	$playspots_query = mysql_query("SELECT * FROM playspots WHERE river = $river_id");
	echo "<h3>";
	if (mysql_num_rows($playspots_query) == 0) {
		echo "No ";
	}
	echo "Playspots</h3>";
	echo "<ul>";
	while ($playspot = mysql_fetch_array($playspots_query, MYSQL_ASSOC)) {
		echo "<li>";
		if ($playspot['playspot_name'] != "") {
			echo "<h4>" . $playspot['playspot_name'] . "</h4>";
		}
		if ($playspot['gauge_max'] != 0 && $playspot['gauge_min'] != 0) {
			echo "<p>In from  " . $playspot['gauge_min'] . " to " . $playspot['gauge_max'] . "</p>";
		}
		if ($playspot['description'] != "") {
			echo "<p>" . $playspot['description'] . "</p>";
		}
		echo getMedia($playspot['id'], 'playspot', 'h5');
		echo "</li>";
	}
	echo "</ul>";
?>
<?php mysql_close($link); ?>