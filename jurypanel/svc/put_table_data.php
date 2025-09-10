<?php
include ("../op/connect.php");

if (isset($_REQUEST['user']) && isset($_REQUEST['auth']) &&
			$_REQUEST['user'] == "projector" && $_REQUEST['auth'] == "projector!@#" ) {

	if (isset($_REQUEST['data'])) {
		if (($data = json_decode($_REQUEST['data'])) == NULL)
			die("Data Decode Error");
	}
	else 
		die("Invalid Parameter");
	
	$table = $data->table_name;
	
	// Test if the table exists
	$query = mysql_query("SELECT * FROM " . $table) or die(mysql_error());
	
	// Take the bold step: TRUNCATE the Table
	if (mysql_num_rows($query) > 0) 
		$query = mysql_query("TRUNCATE TABLE " . $table) or die(mysql_error());
	
	// Build INSERT statement
	$sql_insert = "INSERT INTO " . $table . " (" . explode(",", $data->fields) . ") ";
	
	$nr = 0;	// Used to optimize INSERT requests by bunching 10 records at a time
	$sql_values = "";
	for ($i = 0; $i < count($data->data); ++ $i) {
		$row = $data->data[$i];
		// Wrap all values with Single Quotes to be safe
		for ($x = 0; $x < count($row); ++ $x)
			$row[$x] = "'" . $row[$x] . "'";
		if ($sql_values == "")
			$sql_values = " VALUES ";	// start with the VALUES keyword
		else
			$sql_values .= ", ";		// Add a comma before adding the next row of values
		$sql_values .= "(" . explode($row) . ")";
		
		// Optimize INSERT
		++ $nr;
		if (($nr % 10) == 0) {
			echo $sql_insert . $sql_values;
			// INSERT 10 records into table
			$query = mysql_query($sql_insert . $sql_values) or die(mysql_error());
			$sql_values = "";
		}	
	}
	echo $sql_insert . $sql_values;
	// INSERT any residual data
	if ($sql_values != "")
		$query = mysql_query($sql_insert . $sql_values) or die(mysql_error());
	
	if ($data->rows != $nr)
		die("Number of rows of data counted (" . $nr . ") does not match row count in data sent (" . $data->rows . ")");
	
}
else
	echo "Not Authorised to run the service";
?>