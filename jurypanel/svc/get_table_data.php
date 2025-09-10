<?php
session_start();
include ("../op/connect.php");
// var_dump($_SESSION);
if (isset($_SESSION['jury_id']) && isset($_SESSION['jury_type']) &&
			($_SESSION['jury_type'] == "ADMIN" || $_SESSION['jury_type'] == "MASTER" || $_SESSION['jury_type'] == "PROJECTOR")) {

	if (isset($_REQUEST['table']))
		$table = $_REQUEST['table'];
	else 
		die("Invalid Parameter");
	
	$query = mysql_query("SELECT * FROM " . $table) or die(mysql_error());
	// Table Name in the header
	$tabledata->table_name = $table;
	// Followed by Field Names
	$tabledata->fields = array();
	$num_fields = mysql_num_fields($query);
	for ($i = 0; $i < $num_fields; ++ $i ) {
		$fld = mysql_fetch_field($query, $i);
		$tabledata->fields[$i] = $fld->name;
	}
	// Followed by Data
	$tabledata->data = array();
	$num_rows = 0;
	while ($row = mysql_fetch_array($query)) {
		$rowdata = array();
		for ($i = 0; $i < $num_fields; ++ $i )
			$rowdata[$i] = $row[$i];
		$tabledata->data[$num_rows] = $rowdata;
		++ $num_rows;
	}
	// Footer - Number of Rows
	$tabledata->rows = $num_rows;
	
	echo json_encode($tabledata);
}
else
	echo "Not Authorised to run the service";
?>