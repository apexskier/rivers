<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/database-connect.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php");

$query = mysql_query("SELECT * FROM rapids");

$rapids = array();
$idcounter = 0;

while ($rapid = mysql_fetch_array($query)) {
	if ($rapid['rapid_name'] == "") {
		$rapid_name = null;
	} else {
		$rapid_name = $rapid['rapid_name'];
	}
	
	$data = array('id'           => $idcounter,
	              'database_id'  => intval($rapid['id']),
	              'name'         => $rapid_name,
	              'lat'			 => $rapid['lat'],
	              'lng'			 => $rapid['lng'],
	              'type'		 => "rapid",
	              'river_name'	 => getRiver($rapid['river']),
	              'river_url'    => str_replace(' ', '-', strtolower(getRiver($rapid['river']))),
	              'rating'		 => $rapid['class'],
	              'description'	 => $rapid['description'],
	              'created_user' => $rapid['user'],
	              'created_date' => $rapid['date_added'],
	              'updated_user' => $rapid['updated_by'],
	              'updated_date' => $rapid['date_modified']);
	
	array_push($rapids, $data); 
	$idcounter++;
}

mysql_free_result($query);

echo json_encode($rapids);

?>