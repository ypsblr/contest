<?php
session_start();
include("connect.php");
if (isset($_SESSION['jury_id']) && isset($_SESSION['jury_type']) && ($_SESSION['jury_type'] == "MASTER" || $_SESSION['jury_type'] == "ADMIN")) {
	// Collect Inputs
	$sections = $_REQUEST['section'];
	$jurynumbers = $_REQUEST['jurynumber'];
	$userids = $_REQUEST['user_id'];
	
	// Truncate Assignments Table
	$query = mysqli_query($DBCON, "TRUNCATE TABLE assignment") or die(mysqli_error($DBCON));
	
	// INSERT Form Data
	foreach ($sections as $key => $value){
		if ($value != "") {		// Skip the last empty row
			$section = $value;
			$jurynumber = $jurynumbers[$key];
			$user_id = $userids[$key];
			$sql = " INSERT INTO assignment (section, user_id, jurynumber) ";
			$sql .= " VALUES ('$section', '$user_id', '$jurynumber') ";
			$query = mysqli_query($DBCON, $sql) or die(mysqli_error($DBCON));
		}
	}
	header("Location: ".$_SERVER['HTTP_REFERER']);
	printf("<script>location.href='".$_SERVER['HTTP_REFERER']."'</script>");

}
else {
	header("Location: ../index.php");
	printf("<script>location.href='../index.php'</script>");
}
?>