<?php
Header("content-type: application/x-javascript");

$usgs_gauges_id = array();
$usgs_gauges_code = array();
$usgs_gauges_units = array();
$gauges_current = array();
$gauges_units = array();
$gauges_title = array();

$usgs_gauges_url = 'http://waterservices.usgs.gov/nwis/iv/?sites=';

$query = mysql_query("SELECT * FROM gauges ORDER BY id");
while ($gauge = mysql_fetch_array($query, MYSQL_ASSOC)) {
	if ($gauge['type'] == 'usgs') {
		$usgs_gauges_url .= $gauge['code'] . ',';
		array_push($usgs_gauges_code, $gauge['code']);
		array_push($usgs_gauges_id, $gauge['id']);
		array_push($usgs_gauges_units, $gauge['units']);
	} else if ($gauge['type'] == 'wadoe') {
	
/* WA Department of Ecology gauges
 **********************************/
		$wadoe_gauge_url = "https://fortress.wa.gov/ecy/wrx/wrx/flows/stafiles/" . $gauge['code'] . "/" . $gauge['code'] . "_DSG_FM.txt";
		
		if ($file = @file_get_contents($wadoe_gauge_url)) {
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
			$river_name = getRiver($gauge['river']);

			$description = "<p>$value_current " . $gauge['units'] . "</p>";
			$description .= "<p><a href='https://fortress.wa.gov/ecy/wrx/wrx/flows/station.asp?sta=" . $gauge['code'] . "' target='_blank'>WA Department of Ecology Gauge link</a></p>";
			$description .= getMedia($gauge['id'], 'gauge', 'h4');
	
			new_gmaps_marker($gauge['id'], "gauge", $id, $position, "<a href='/river/" . str_replace(' ', '-', strtolower($river_name)) . "'>$river_name</a> Gauge", $description, "gauge");
			echo "gauges_array.push($id);\n";
		} else {
			echo "// Couldn't get gauge " . $gauge['id'] . " at $wadoe_gauge_url\n";
		}
	}
}
@mysql_free_result($query);



/* USGS gauges
 **************/
$usgs_gauges_url = substr($usgs_gauges_url, 0, -1);
$usgs_gauges_url .= '&period=PT4H';
// Fetch the data.
$description = file_get_contents($usgs_gauges_url);
if (!$description) {
 	echo 'Error retrieving: ' . $usgs_gauges_url;
 	exit;
}
// Remove the namespace prefix for easier parsing
$description = str_replace('ns1:','', $description);
// Load the XML returned into an object for easy parsing
$xml_tree = simplexml_load_string($description);
if ($xml_tree === FALSE) {
 	echo 'Unable to parse USGS\'s XML';
 	exit;
}
$counter = 0;
foreach ($xml_tree->timeSeries as $site_data) {
	$code = $site_data->sourceInfo->siteCode;
	if ($site_data->variable->unit->unitCode == "cfs" && $usgs_gauges_units[array_search($code, $usgs_gauges_code)] == "cfs") {
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
		$descriptionbase_id = $usgs_gauges_id[array_search($code, $usgs_gauges_code)];
		$id = "gauge_" . $descriptionbase_id;
		$position = "new google.maps.LatLng(" . $site_data->sourceInfo->geoLocation->geogLocation->latitude . ", " . $site_data->sourceInfo->geoLocation->geogLocation->longitude . ")";
		
		$description = "<p>$value_current " . $site_data->variable->unit->unitCode . " and ";
		$value_diff = ($value_current - $value_past) / 2;
		if ($value_diff > 0) {
			$description .= "rising $value_diff " . $site_data->variable->unit->unitCode . " per hour.";
		} else if ($value_diff < 0) {
			$description .= "dropping " . ($value_diff * -1) . " " . $site_data->variable->unit->unitCode . " per hour.";
		} else {
			$description .= "stable.";
		}
		$description .= "</p><p><a href='http://waterdata.usgs.gov/usa/nwis/uv?" . $code . "' target='_blank'>US Geological Survey Gauge link</a></p>";
		$description .= getMedia($gauge['id'], 'gauge', 'h4');
		
		new_gmaps_marker($descriptionbase_id, "gauge", $id, $position, ucwords(strtolower($site_data->sourceInfo->siteName)), $description, "gauge");
		echo "gauges_array.push($id);\n";
		if (!array_key_exists((integer)$descriptionbase_id, $gauges_current)) {
			$gauges_current[(integer)$descriptionbase_id] = $value_current;
		}
		if (!array_key_exists((integer)$descriptionbase_id, $gauges_units)) {
			$gauges_units[(integer)$descriptionbase_id] = $site_data->variable->unit->unitCode;
		}
		if (!array_key_exists((integer)$gauge['id'], $gauges_title)) {
			$gauges_title[(integer)$descriptionbase_id] = "USGS Gauge " . $code;
		}
		$counter++;
	} else if ($site_data->variable->unit->unitCode == "ft" && $usgs_gauges_units[array_search($code, $usgs_gauges_code)] == "ft") {
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
		$descriptionbase_id = $usgs_gauges_id[array_search($code, $usgs_gauges_code)];
		$id = "gauge_" . $descriptionbase_id;
		$position = "new google.maps.LatLng(" . $site_data->sourceInfo->geoLocation->geogLocation->latitude . ", " . $site_data->sourceInfo->geoLocation->geogLocation->longitude . ")";
		
		$description = "<p>$value_current " . $site_data->variable->unit->unitCode . " and ";
		$value_diff = ($value_current - $value_past) / 2;
		if ($value_diff > 0) {
			$description .= "rising $value_diff " . $site_data->variable->unit->unitCode . " per hour.";
		} else if ($value_diff < 0) {
			$description .= "dropping " . ($value_diff * -1) . " " . $site_data->variable->unit->unitCode . " per hour.";
		} else {
			$description .= "stable.";
		}
		$description .= "</p><p><a href='http://waterdata.usgs.gov/usa/nwis/uv?" . $code . "' target='_blank'>US Geological Survey Gauge link</a></p>";
		$description .= getMedia($gauge['id'], 'gauge', 'h4');
		
		new_gmaps_marker($descriptionbase_id, "gauge", $id, $position, ucwords(strtolower($site_data->sourceInfo->siteName)), $description, "gauge");
		echo "gauges_array.push($id);\n";
		if (!array_key_exists((integer)$descriptionbase_id, $gauges_current)) {
			$gauges_current[(integer)$descriptionbase_id] = $value_current;
		}
		if (!array_key_exists((integer)$descriptionbase_id, $gauges_units)) {
			$gauges_units[(integer)$descriptionbase_id] = $site_data->variable->unit->unitCode;
		}
		if (!array_key_exists((integer)$gauge['id'], $gauges_title)) {
			$gauges_title[(integer)$descriptionbase_id] = "USGS Gauge " . $code;
		}
		$counter++;
	}
}

?>