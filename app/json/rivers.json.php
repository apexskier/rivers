<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/database.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/app/php/functions.php");

$query = mysql_query("SELECT * FROM rivers");

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
	              'name'	     => $rapid['river_name'],
	              'type'         => "rapid",
	              'river_url'    => str_replace(' ', '-', strtolower($rapid['river_name'])),
	              'created_user' => $rapid['user'],
	              'created_date' => $rapid['date_added']);
	
	array_push($rapids, $data); 
	$idcounter++;
}

mysql_free_result($query);

echo json_encode($rapids);

?>