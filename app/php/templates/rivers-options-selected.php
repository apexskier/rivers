		<p><label name="river">River<br /></label>
		<select name="river">
			<option value="0">---</option>
<?php 
	require($_SERVER['DOCUMENT_ROOT'] . "/app/php/database.php");
	
	$query = mysql_query("SELECT * FROM rivers ORDER BY river_name");
	
	while ($row = mysql_fetch_assoc($query)) {
		echo '<option value="' . $row['id'] . '">';
		echo $row['river_name'];
		echo '</option>';
	}
?>
		</select></p>