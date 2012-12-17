<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/database-connect.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php");

$query = mysql_query("SELECT * FROM rapids");

$response = array();
$rapids = array();

while ($rapid = mysql_fetch_array($query)) {
	$rapids[] = array('id'           => $rapid['id'],
	                  'name'   => $rapid['rapid_name'],
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
}

mysql_free_result($query);

$response['rapids'] = $rapids;

echo json_encode($response)



?>