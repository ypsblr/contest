<?php
session_start();
include'connect.php';

if(isset($_SESSION['USER_ID']) && isset($_POST['update_profile']))
{
	$entry_id = $_SESSION['USER_ID'];
	$gender = $_POST['gender'];
	$age = $_POST['age'];
	
	$qchk = mysqli_query($DBCON, "UPDATE entry SET gender='$gender', age = '$age' WHERE entry_id = '$entry_id' ")or die(mysql_error($DBCON));

	header('Location:/user_panel.php');
}
else
{

	header('Location:' . $_SERVER['HTTP_REFERER']);
}
exit;
?>
