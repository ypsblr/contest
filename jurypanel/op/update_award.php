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

if (isset($_POST['award_id']) && isset($_POST['award_section']) && isset($_POST['award_name'])) {
	$award_id = $_POST['award_id'];
	$level = $_POST['award_level'];
	$section = mysqli_real_escape_string($DBCON, $_POST['award_section']);
	$sequence = $_POST['award_sequence'];
	$name = mysqli_real_escape_string($DBCON, $_POST['award_name']);
	$description = mysqli_real_escape_string($DBCON, $_POST['award_description']);
	$number = $_POST['award_number'];
	$cash_award = $_POST['award_cash'];
	
	if ($award_id > 0) {
		// UPDATE
		$sql  = "UPDATE award ";
		$sql .= "  SET level = '$level', ";
		$sql .= "      section = '$section', ";
		$sql .= "      sequence = '$sequence', ";
		$sql .= "      name = '$name', ";
		$sql .= "      description = '$description', ";
		$sql .= "      number = '$number', ";
		$sql .= "      cash_award = '$cash_award' ";
		$sql .= "  WHERE award_id = '$award_id' ";
		mysqli_query($DBCON, $sql) or json_error("Unable to update Award (" + mysqli_error($DBCON) +")");
	}
	else {
		// Find the last award_id
		$query = mysqli_query($DBCON, "SELECT MAX(award_id) AS last_award_id FROM award") or json_error("Unable to generate Award ID (" + mysqli_error($DBCON) +")");
		$row = mysqli_fetch_array($query);
		$award_id = $row['last_award_id'] + 1;
		// INSERT
		$sql  = "INSERT INTO award (award_id, level, section, sequence, name, ";
		$sql .= "                   description, number, cash_award, sponsor ) ";
		$sql .= " VALUES ('$award_id', '$level', '$section', '$sequence', '$name', ";
		$sql .= "         '$description', '$number', '$cash_award', '' ) ";
		mysqli_query($DBCON, $sql) or json_error("Unable to create award (" + mysqli_error($DBCON) +")");
	}
	
	$data = Array();
	$data['status'] = "OK";
	$data['errmsg'] = "";
	$data['award_id'] = $award_id;
	echo json_encode($data);
}
else
	json_error("Invalid Parameters");
?>
