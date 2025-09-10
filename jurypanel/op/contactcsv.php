<?php
// Set header for download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=contacts.csv');
session_start(); 
include("connect.php");

if(isset($_SESSION['jury_id']) && ($_SESSION['jury_type']=="PROJECTOR" || $_SESSION['jury_type']=="ADMIN" || $_SESSION['jury_type']=="MASTER")) {
	
	// Open output stream for data download
	$output = fopen('php://output', 'w');
	
	// Write First Line
	fputcsv($output, array('Entry#', 'Name', 'Email', 'Phone'));

	// Select Rows from ENTRY table
	$query = mysqli_query($DBCON, "SELECT * FROM entry") or die(mysqli_error($DBCON));
	while ($row = mysqli_fetch_array($query))
		fputcsv($output, array($row['entry_id'], $row['name'], $row['email'], $row['phone']));
		
}

?>		