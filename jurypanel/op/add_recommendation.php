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

if (isset($_REQUEST['pic_id']) && isset($_REQUEST['award_id']) && isset($_REQUEST['ranking'])) {
    

	$pic_id = $_REQUEST['pic_id'];
	$award_id = $_REQUEST['award_id'];
	$ranking = $_REQUEST['ranking'];

	// Determine the section from pic_id - required for selecting shortlisted pics again
	$sql = "SELECT * FROM pic WHERE pic_id = '$pic_id' ";  
	$query = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
	$row = mysqli_fetch_array($query);
	$section = $row['section'];
	
	// Check  it has already been added
	$sql = "SELECT * FROM recommendation WHERE jury_id = '$jury_id' AND award_id = '$award_id' AND pic_id = '$pic_id' ";  
	$query = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
	if (mysqli_num_rows($query) == 0) {
		$sql = "INSERT INTO recommendation (jury_id, award_id, ranking, pic_id, recommendation) VALUES ('$jury_id', '$award_id', '$ranking', '$pic_id', '') ";
		mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
	}
	
	// Return all recommended items
	$sql  = "SELECT pic.pic_id AS pic_id, title, picfile, section, ranking, gender FROM pic, recommendation, entry ";
	$sql .= "WHERE recommendation.award_id = '$award_id' ";
	$sql .= "  AND recommendation.jury_id = '$jury_id' ";
	$sql .= "  AND recommendation.pic_id = pic.pic_id ";
	$sql .= "  AND pic.section = '$section' ";
	$sql .= "  AND pic.entry_id = entry.entry_id ";
	$sql .= "ORDER BY ranking ";

	$query = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
	$tabledata = (object) array();
	$tabledata->rows = mysqli_num_rows($query);
	$rowdata = array();
	
	while($tr_pics = mysqli_fetch_array($query)) 
		$rowdata[] = $tr_pics;
	
	$tabledata->data = $rowdata;
	$tabledata->result = 'OK';
	$tabledata->errmsg = '';
	
	echo json_encode($tabledata);
}
else
	echo encodeError("Invalid Parameter");
?>