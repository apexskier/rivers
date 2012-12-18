<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/database.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/functions.php");

$query = mysql_query("SELECT * FROM runs");

$runs = array();
$idcounter = 0;

while ($run = mysql_fetch_array($query)) {
	if ($run['run_name'] == "") {
		$run_name = null;
	} else {
		$run_name = $run['run_name'];
	}
	
	if (!file_exists($points_file_name)) {
		$run_id = "run_" . $run['id'];
		$points_query = mysql_query("SELECT * FROM $run_id");
		$points = array();
		while ($point = mysql_fetch_array($points_query)) {
			$lat_lng = array('lat' => $point['lat'],
			                 'lng' => $point['lng']);
			array_push($points, $lat_lng);
		}
		
		$points_file_name = $_SERVER['DOCUMENT_ROOT'] . "/app/json/run_points/" . $run_id . ".json";
		$points_file = fopen($points_file_name, 'w');
		fwrite($points_file, json_encode($points));
		fclose($points_file);
	
		mysql_free_result($points_query);
	}
	
	$data = array('id'           => $idcounter,
	              'database_id'  => intval($run['id']),
	              'name'         => $run_name,
	              'river_name'	 => getRiver($run['river']),
	              'river_id'     => $run['river'],
	              'description'	 => $run['description'],
	              'type'         => 'run',
	              'rating'       => $run['class'],
	              'gauge'        => array('id'  => $run['gauge_id'],
	                                      'max' => $run['gauge_max'],
	                                      'min' => $run['gauge_min']),
	              'created_user' => $run['user'],
	              'created_date' => $run['date_added'],
	              'updated_user' => $run['updated_by'],
	              'updated_date' => $run['date_modified']);
	
	array_push($runs, $data);
	$idcounter++;
}

mysql_free_result($query);

echo json_encode($runs);

?>