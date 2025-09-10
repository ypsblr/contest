<?php
session_start();
include("connect.php");

function json_error($errmsg) {
	$data = Array();
	$data['status'] = "ERROR";
	$data['errmsg'] = $errmsg;
	echo json_encode($data);
	die();
}

$session_id=$_SESSION['jury_id'];
$user_query = mysqli_query($DBCON, "SELECT * FROM user where user_id = '$session_id'") or die(mysqli_error($DBCON));
$user_row = mysqli_fetch_array($user_query);
$jury_login = $user_row['login'];
$jury_name = $user_row['name'];
$user_type = $user_row['type'];

if (isset($_POST['award_id'])) {
	$award_id = $_POST['award_id'];
	
	// Check if section exists
	$query = mysqli_query($DBCON, "SELECT * FROM award WHERE award_id = '$award_id' ") or json_error("Error accessing award data (" + mysqli_error($DBCON) +")");
	if (mysqli_num_rows($query) == 0) 
		json_error("Award Not Found");

	$row = mysqli_fetch_array($query);

	$data = Array();
	$data['status'] = "OK";
	$data['errmsg'] = "";
	$data['award_id'] = $row['award_id'];
	$data['level'] = $row['level'];
	$data['section'] = $row['section'];
	$data['sequence'] = $row['sequence'];
	$data['name'] = $row['name'];
	$data['description'] = $row['description'];
	$data['number'] = $row['number'];
	$data['cash_award'] = $row['cash_award'];
	echo json_encode($data);
}
else
	json_error("Invalid Parameters");
?>
