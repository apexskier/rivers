 <?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/database.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/functions.php");

$query = mysql_query("SELECT * FROM gauges");

$gauges = array();
$idcounter = 0;

$gauge_codes;
while ($gauge = mysql_fetch_array($query)) {
	if ($gauge['type'] == "usgs") {
		$gauge_codes .= $gauge['code'] . ",";
	}
}
$usgs_gauges_url = 'http://waterservices.usgs.gov/nwis/iv/?sites=' . $gauge_codes;
$usgs_gauges_url = substr($usgs_gauges_url, 0, -1);
$usgs_gauges_url .= '&period=PT8H';
$usgs_gauge_data = array();

// Fetch the data.
$usgs_xml_raw = file_get_contents($usgs_gauges_url);
if ($usgs_xml_raw) {
	$usgs_xml_raw = str_replace('ns1:','', $usgs_xml_raw);
	$usgs_xml = simplexml_load_string($usgs_xml_raw);
	
	$counter = 0;
	foreach ($usgs_xml->timeSeries as $site_data) {
		$code = (string)$site_data->sourceInfo->siteCode;
		
		// get flow from two hours ago
		if ($site_data->values->value == '') {
			$past_flow = null;
		} else if ($site_data->values->value == -999999) {
			$past_flow = null;
		} else {
			$past_flow = $site_data->values->value;
		}
		
		// get current flow
		foreach ($site_data->values->value as $value) {
			if ($value == '') {
				$current_flow = '-';
			} else if ($value == -999999) {
				$current_flow = 'UNKNOWN';
			} else {
				$current_flow = $value;
			}
		}
		
		$units = (string)$site_data->variable->unit->unitCode;
		
		$flow_difference = ($current_flow - $past_flow) / 2;
		if ($flow_difference > 0) {
			$flow_difference = "rising $flow_difference $units per hour";
		} else if ($flow_difference < 0) {
			$flow_difference = "dropping " . ($flow_difference * -1) . " $units per hour";
		} else {
			$flow_difference = "stable";
		}
		
		$link = "http://waterdata.usgs.gov/usa/nwis/uv?$code";
		
		$name = ucwords(strtolower($site_data->sourceInfo->siteName));
		
		$data = array('code'            => $code,
		              'units'           => $units,
		              'past_flow'       => (string)$past_flow,
		              'current_flow'    => (string)$current_flow,
		              'flow_difference' => (string)$flow_difference,
		              'gauge_url'       => $link,
		              'name'            => $name,
		              'lat'             => (string)$site_data->sourceInfo->geoLocation->geogLocation->latitude,
		              'lng'             => (string)$site_data->sourceInfo->geoLocation->geogLocation->longitude);
		array_push($usgs_gauge_data, $data);
	}
}

mysql_data_seek($query, 0); // reset pointer to first result
while ($gauge = mysql_fetch_array($query)) {
	$active = false;
	$link = null;
	$current_flow = null;
	$flow_difference = "";
	
	if ($gauge['type'] == 'usgs') {
		foreach ($usgs_gauge_data as $gauge_data) {
			if ($gauge_data['code'] == $gauge['code']
			 && $gauge_data['units'] == $gauge['units']) {
				$current_flow = $gauge_data['current_flow'];
				$active = true;
				$link = $gauge_data['gauge_url'];
				$name = $gauge_data['name'];
				$flow_difference = $gauge_data['flow_difference'];
				$lat = $gauge_data['lat'];
				$lng = $gauge_data['lng'];
			}
		}
	} elseif ($gauge['type'] == 'wadoe') {
		$wadoe_gauge_url = "https://fortress.wa.gov/ecy/wrx/wrx/flows/stafiles/";
		$wadoe_gauge_url .= $gauge['code'] . "/" . $gauge['code'] . "_" . date("Y") . "_DSG_FM.txt";
//		https://fortress.wa.gov/ecy/wrx/wrx/flows/stafiles/20A070/20A070_2012_DSG_FM.txt
//		https://fortress.wa.gov/ecy/wrx/wrx/flows/stafiles/20A070/20A070_2012_STG_DV.txt
		if ($file = @file_get_contents($wadoe_gauge_url)) {
			$flow_date = trim(substr($file, 0, strrpos($file, "  ")));
			$current_flow = trim(substr($flow_date, strrpos($current_flow, "  ")));
			
			$current_flow = trim(substr($file, 0, strrpos($file, "  ")));
			$current_flow = trim(substr($current_flow, strrpos($current_flow, "  ")));
			$active = false; // all of these are false
			$name = "WA DOE Gauge on the " . getRiver($gauge['river']);
			$lat = $gauge['lat'];
			$lng = $gauge['lng'];
		}
		
		$link = "https://fortress.wa.gov/ecy/wrx/wrx/flows/station.asp?sta=" . $gauge['code'];
	}
	
	if ($active) {
		$data = array('id'              => $idcounter,
		              'database_id'     => intval($gauge['id']),
		              'name'            => $name,
		              'link'            => $link,
		              'lat'		        => $lat,
		              'lng'		        => $lng,
		              'source'	        => $gauge['type'],
		              'type'            => "gauge",
		              'river_name'      => getRiver($gauge['river']),
		              'river_url'       => str_replace(' ', '-', strtolower(getRiver($gauge['river']))),
		              'units'	        => $gauge['units'],
		              'code'            => $gauge['code'],
		              'active'          => $active,
		              'current_flow'    => (string)$current_flow,
		              'flow_difference' => $flow_difference);
		
		array_push($gauges, $data);
		$idcounter++;
	}
}

mysql_free_result($query);

echo json_encode($gauges);

?>