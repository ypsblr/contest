<?php
session_start();
include"connect.php";
if (isset($_POST['update_password']))
{
	$uid=$_SESSION['USER_ID'];

	$opassword= $_POST['opassword']; 
	$npassword= $_POST['npassword'];

	$qchk = mysql_query("SELECT * FROM entry WHERE entry_id='$uid' AND password='$opassword'")or die(mysql_error());
	$count = mysql_num_rows($qchk);
	if ($count > 0){		 
		mysql_query("UPDATE entry SET password='$npassword' WHERE entry_id='$uid'")or die(mysql_error());
		$_SESSION['pass_update']="Password has been update successfully!";
	}
	else
	{
		$_SESSION['pass_update']="Sorry !! your Old password doesn't match!";
	}
}

header('Location:' . $_SERVER['HTTP_REFERER']); 
?>
