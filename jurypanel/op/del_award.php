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
	mysqli_query($DBCON, "DELETE FROM award WHERE award_id = '$award_id' ") or json_error("Error removing award (" + mysqli_error($DBCON) +")");

	$data = Array();
	$data['status'] = "OK";
	$data['errmsg'] = "";
	echo json_encode($data);
}
else
	json_error("Invalid Parameters");
?>
