<?php
session_start();
include ("connect.php");

function encodeError($errmsg) {
	$tabledata = (object) array();
	$tabledata->result = 'FAIL';
	$tabledata->errmsg = $errmsg;
	return json_encode($tabledata);
}

function sql_error($sql, $errmsg, $phpfile, $phpline) {
	file_put_contents("sqldump.txt", "SQL operation failed with message '$errmsg' in line $phpline of '$phpfile'" . chr(13) . chr(10), FILE_APPEND);
	file_put_contents("sqldump.txt", "SQL: " . $sql . chr(13) . chr(10), FILE_APPEND);
	echo encodeError("Server Error");
	exit();
}

if(isset($_SESSION['jury_id']) && $_SESSION['jury_id'] != "")
	$jury_id = $_SESSION['jury_id'];
else {
	echo encodeError("Unauthorized access");
	exit;
}

if (isset($_REQUEST['pic_id'])) {

	$pic_id = $_REQUEST['pic_id'];

	// Determine the section from pic_id - required for selecting shortlisted pics again
	$sql = "SELECT * FROM pic WHERE pic_id = '$pic_id' ";  
	$query = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
	$row = mysqli_fetch_array($query);
	$section = $row['section'];
	
	// Check  it has already been added
	$sql = "SELECT * FROM shortlist WHERE jury_id = '$jury_id' AND pic_id = '$pic_id' ";  
	$query = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
	if (mysqli_num_rows($query) == 0) {
		$sql = "INSERT INTO shortlist (jury_id, pic_id) VALUES ('$jury_id', '$pic_id') ";
		mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
	}
	
	// Return all shortlisted items
	$sql  = "SELECT pic.pic_id, entry.entry_id as entry_id, name, gender, section, title, location, picfile, eseq ";
	$sql .= " FROM pic, entry, shortlist ";
	$sql .= " WHERE pic.entry_id = entry.entry_id ";
	$sql .= "   AND pic.section='$section' ";
	$sql .= "   AND shortlist.pic_id = pic.pic_id ";
	$sql .= "   AND shortlist.jury_id = '$jury_id' ";
	$sql .= " ORDER BY eseq ASC ";

	$shortlisted = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
	$tabledata = (object) array();
	$tabledata->rows = mysqli_num_rows($shortlisted);
	$rowdata = array();
	
	while($tr_pics = mysqli_fetch_array($shortlisted)) 
		$rowdata[] = $tr_pics;
	
	$tabledata->data = $rowdata;
	$tabledata->result = 'OK';
	$tabledata->errmsg = '';
	
	echo json_encode($tabledata);
}
else
	echo encodeError("Invalid Parameter");
?>