<?php
	$link = mysql_connect('mysql.camlittle.com', 'camlittle', 'KayaKing@010');
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db('riverdatabase') or die('Unable to select database');
?>