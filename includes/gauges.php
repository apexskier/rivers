<?php

/* USGS gauges
 **************/
$query = mysql_query("SELECT * FROM gauges ORDER BY id");

$usgs_gauges_url = 'http://waterservices.usgs.gov/nwis/iv/?sites=';

$usgs_gauges_id = array();
$usgs_gauges_code = array();
$gauges_link = array();
$usgs_gauges_units = array();
$gauges_lat = array();
$gauges_lng = array();
$gauges_current = array();
$gauges_units = array();
$gauges_title = array();
//$gauges_type = array();

while ($gauge = mysql_fetch_array($query, MYSQL_ASSOC)) {
	if ($gauge['type'] == 'usgs') {
		$usgs_gauges_url .= $gauge['code'] . ',';
		array_push($usgs_gauges_code, $gauge['code']);
		array_push($usgs_gauges_id, $gauge['id']);
		array_push($usgs_gauges_units, $gauge['units']);
	} else if ($gauge['type'] == 'wadoe') {
		$wadoe_gauge_url = "https://fortress.wa.gov/ecy/wrx/wrx/flows/stafiles/" . $gauge['code'] . "/" . $gauge['code'] . "_DSG_FM.txt";
		$file = file_get_contents($wadoe_gauge_url);
		
		$value_current = trim(substr($file, 0, strrpos($file, "  ")));
		$value_current = trim(substr($value_current, strrpos($value_current, "  ")));
		
		if (!array_key_exists((integer)$gauge['id'], $gauges_current)) {
			$gauges_current[(integer)$gauge['id']] = $value_current;
		}
		if (!array_key_exists((integer)$gauge['id'], $gauges_units)) {
			$gauges_units[(integer)$gauge['id']] = $gauge['units'];
		}
		if (!array_key_exists((integer)$gauge['id'], $gauges_title)) {
			$gauges_title[(integer)$gauge['id']] = "WADOE Gauge " . $gauge['code'];
		}
		
		$position = "new google.maps.LatLng(" . $gauge['lat'] . ", " . $gauge['lng'] . ")";
		$id = "gauge_" . $gauge['id'];
		$rivername_query = mysql_query("SELECT * FROM rivers WHERE id = " . $gauge['river']);
		while ($river = mysql_fetch_array($rivername_query, MYSQL_ASSOC)) {
			$river_name = $river['river_name'];
		}
		@mysql_free_result($rivername_query);
		
		$data = "<p>$value_current " . $gauge['units'] . "</p>";
		$data .= "<p><a href='https://fortress.wa.gov/ecy/wrx/wrx/flows/station.asp?sta=" . $gauge['code'] . "' target='_blank'>WA Department of Ecology Gauge link</a></p>";

		new_gmaps_marker($gauge['id'], $id, $position, "$river_name Gauge", $data, "gauge");
		echo "gauges_array.push($id);\n";
	}
	//$gauges_type[(integer)$usgs_gauges_id] = $gauge['type'];
}
@mysql_free_result($query);

$usgs_gauges_url = substr($usgs_gauges_url, 0, -1);
$usgs_gauges_url .= '&period=PT4H';
// Fetch the data.
$data = file_get_contents($usgs_gauges_url);
if (!$data) {
 	echo 'Error retrieving: ' . $usgs_gauges_url;
 	exit;
}
// Remove the namespace prefix for easier parsing
$data = str_replace('ns1:','', $data);
// Load the XML returned into an object for easy parsing
$xml_tree = simplexml_load_string($data);
if ($xml_tree === FALSE) {
 	echo 'Unable to parse USGS\'s XML';
 	exit;
}

$counter = 0;
foreach ($xml_tree->timeSeries as $site_data) {
	if ($site_data->variable->unit->unitCode == "cfs") {
		$code = $site_data->sourceInfo->siteCode;
		// get flow from two hours ago
		if ($site_data->values->value == '') {
			$value_past = '-';
		} else if ($site_data->values->value == -999999) {
			$value_past = 'UNKNOWN';
		} else {
			$value_past = $site_data->values->value;
		}
		// get current flow
		foreach ($site_data->values->value as $value) {
			if ($value == '') {
				$value_current = '-';
			} else if ($value == -999999) {
				$value_current = 'UNKNOWN';
			} else {
				$value_current = $value;
			}
		}
		$database_id = $usgs_gauges_id[array_search($code, $usgs_gauges_code)];
		$id = "gauge_" . $database_id;
		$position = "new google.maps.LatLng(" . $site_data->sourceInfo->geoLocation->geogLocation->latitude . ", " . $site_data->sourceInfo->geoLocation->geogLocation->longitude . ")";
		
		$data = "<p>$value_current " . $site_data->variable->unit->unitCode . " and ";
		$value_diff = ($value_current - $value_past) / 2;
		if ($value_diff > 0) {
			$data .= "rising $value_diff " . $site_data->variable->unit->unitCode . " per hour.";
		} else if ($value_diff < 0) {
			$data .= "dropping " . ($value_diff * -1) . " " . $site_data->variable->unit->unitCode . " per hour.";
		} else {
			$data .= "stable.";
		}
		$data .= "</p><p><a href='http://waterdata.usgs.gov/usa/nwis/uv?" . $code . "' target='_blank'>US Geological Survey Gauge link</a></p>";
		
		new_gmaps_marker($database_id, $id, $position, ucwords(strtolower($site_data->sourceInfo->siteName)), $data, "gauge");
		echo "gauges_array.push($id);\n";
		if (!array_key_exists((integer)$database_id, $gauges_current)) {
			$gauges_current[(integer)$database_id] = $value_current;
		}
		if (!array_key_exists((integer)$database_id, $gauges_units)) {
			$gauges_units[(integer)$database_id] = $site_data->variable->unit->unitCode;
		}
		if (!array_key_exists((integer)$gauge['id'], $gauges_title)) {
			$gauges_title[(integer)$database_id] = "USGS Gauge " . $code;
		}
		$counter++;
	}
}

/* WA Department of Ecology gauges
 **********************************/


?>