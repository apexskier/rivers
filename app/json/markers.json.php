<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/database-connect.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php");

$query = mysql_query("SELECT * FROM markers");

$markers = array();
$idcounter = 0;

while ($marker = mysql_fetch_array($query)) {
	if ($marker['marker_name'] == "") {
		$marker_name = null;
	} else {
		$marker_name = $marker['marker_name'];
	}
	
	$data = array('id'           => $idcounter,
	              'database_id'  => intval($marker['id']),
	              'name'         => $marker_name,
	              'lat'			 => $marker['lat'],
	              'lng'			 => $marker['lng'],
	              'type'		 => $marker['marker_type'],
	              'river_name'	 => getRiver($marker['river']),
	              'river_url'    => str_replace(' ', '-', strtolower(getRiver($marker['river']))),
	              'description'	 => $marker['description'],
	              'created_user' => $marker['user'],
	              'created_date' => $marker['date_added'],
	              'updated_user' => $marker['updated_by'],
	              'updated_date' => $marker['date_modified']);
	
	array_push($markers, $data); 
	$idcounter++;
}

mysql_free_result($query);

echo json_encode($markers);

?>