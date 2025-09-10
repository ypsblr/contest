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

	// Check  it has already been added
	$sql = "SELECT * FROM shortlist WHERE jury_id = '$jury_id' AND pic_id = '$pic_id' ";  
	$query = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
	$tabledata = (object) array();
	if (mysqli_num_rows($query) > 0)
		$tabledata->data = "YES";
	else
		$tabledata->data = "NO";

	$tabledata->result = 'OK';
	$tabledata->errmsg = '';		
	echo json_encode($tabledata);
}
else
	echo encodeError("Invalid Parameter");
?>