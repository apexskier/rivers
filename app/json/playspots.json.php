<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/database-connect.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php");

$query = mysql_query("SELECT * FROM playspots");

$playspots = array();
$idcounter = 0;

while ($playspot = mysql_fetch_array($query)) {
	if ($playspot['playspot_name'] == "") {
		$playspot_name = null;
	} else {
		$playspot_name = $playspot['playspot_name'];
	}
	if ($playspot['gauge_max'] == 0
	 || $playspot['gauge_min'] == 0
	 || $playspot['gauge_id'] == 0
	 || $playspot['gauge_id'] == "") {
		$gauge_active = false;
	} else {
		$gauge_active = true;
	}
	
	$data = array('id'           => $idcounter,
	              'database_id'  => intval($playspot['id']),
	              'name'         => $playspot_name,
	              'river_name'	 => getRiver($playspot['river']),
	              'river_url'    => str_replace(' ', '-', strtolower(getRiver($playspot['river']))),
	              'lat'			 => $playspot['lat'],
	              'lng'			 => $playspot['lng'],
	              'type'         => "playspot",
	              'description'	 => $playspot['description'],
	              'gauge'        => array('active' => $gauge_active,
	                                      'id'     => $playspot['gauge_id'],
	                                      'max'    => $playspot['gauge_max'],
	                                      'min'    => $playspot['gauge_min']),
	              'created_user' => $playspot['user'],
	              'created_date' => $playspot['date_added'],
	              'updated_user' => $playspot['updated_by'],
	              'updated_date' => $playspot['date_modified']);
	
	array_push($playspots, $data);
	$idcounter++;
}

mysql_free_result($query);

echo json_encode($playspots);

?>