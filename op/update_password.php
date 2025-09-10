<?php
session_start();
include"connect.php";
if (isset($_POST['update_password']))
{
	$uid=$_SESSION['USER_ID'];

	$opassword= $_POST['opassword']; 
	$npassword= $_POST['npassword'];

	$qchk = mysqli_query($DBCON, "SELECT * FROM entry WHERE entry_id='$uid' AND password='$opassword'")or die(mysqli_error($DBCON));
	$count = mysqli_num_rows($qchk);
	if ($count > 0){		 
		mysqli_query($DBCON, "UPDATE entry SET password='$npassword' WHERE entry_id='$uid'")or die(mysql_error($DBCON));
		$_SESSION['pass_update']="Password has been update successfully!";
	}
	else
	{
		$_SESSION['pass_update']="Sorry !! your Old password doesn't match!";
	}
}

header('Location:' . $_SERVER['HTTP_REFERER']); 
?>
