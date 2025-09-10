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
$jury_pic = $user_row['picfile'];
$user_type = $user_row['type'];

if (isset($_POST['section'])) {
	$section = mysqli_real_escape_string($DBCON, $_POST['section']);
	
	// Check if section exists
	$query = mysqli_query($DBCON, "SELECT * FROM section WHERE section = '$section' ") or json_error("Error accessing section data (" + mysqli_error($DBCON) +")");
	if (mysqli_num_rows($query) == 0) 
		json_error("Section Not Found");

	$row = mysqli_fetch_array($query);

	$data = Array();
	$data['status'] = "OK";
	$data['errmsg'] = "";
	$data['description'] = $row['description'];
	$data['rules'] = $row['rules'];
	echo json_encode($data);
}
else
	json_error("Invalid Parameters");
?>
