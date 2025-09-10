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

if (isset($_POST['section']) && isset($_POST['description']) && isset($_POST['rules'])) {
	$section = mysqli_real_escape_string($DBCON, $_POST['section']);
	$description = mysqli_real_escape_string($DBCON, $_POST['description']);
	$rules = mysqli_real_escape_string($DBCON, $_POST['rules']);

	$data = Array();
	
	// Check if section exists
	$query = mysqli_query($DBCON, "SELECT * FROM section WHERE section = '$section' ") or json_error("Error accessing section data (" + mysqli_error($DBCON) +")");
	if (mysqli_num_rows($query) > 0) {
		// UPDATE
		$sql  = "UPDATE section ";
		$sql .= "  SET description = '$description', ";
		$sql .= "      rules = '$rules' ";
		$sql .= "  WHERE section = '$section' ";
		mysqli_query($DBCON, $sql) or json_error("Unable to update section (" + mysqli_error($DBCON) +")");
		$data['operation'] = 'UPDATE';
	}
	else {
		// INSERT
		$sql  = "INSERT INTO section (section, description, rules, max_pics_per_entry) ";
		$sql .= " VALUES('$section', '$description', '$rules', 4) ";
		mysqli_query($DBCON, $sql) or json_error("Unable to create section (" + mysqli_error($DBCON) +")");
		$data['operation'] = 'INSERT';
	}
	
	// Create upload directories
	if (! is_dir("../../upload/" . $_POST['section']))
		mkdir("../../upload/" .  $_POST['section'], 0755);
	if (! is_dir("../../upload/" . $_POST['section'] . "/tn"))
		mkdir("../../upload/" .  $_POST['section'] . "/tn", 0755);
	
	$data['status'] = "OK";
	$data['errmsg'] = "";
	echo json_encode($data);
}
else
	json_error("Invalid Parameters");
?>
