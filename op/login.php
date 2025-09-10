<?php
session_start();
include'connect.php';

if(isset($_POST['login_check']))
{
	$username=$_POST['username'];
	$password=$_POST['password'];

	$qchk = mysqli_query($DBCON, "SELECT * FROM entry WHERE email='$username' AND password='$password'")or die(mysqli_error($DBCON));
	$count = mysqli_num_rows($qchk);
	$row=mysqli_fetch_array($qchk);
	$user_gender = $row['gender'];
	$user_age = $row['age'];
	
	if ($count > 0) {
		$_SESSION['USER_ID']=$row['entry_id'];
		if (trim($user_gender) == "" || $user_age == 0) {
			$_SESSION['signin_msg'] = "Your profile requires to be updated with Gender and Age information!";
			header('Location:' . $_SERVER['HTTP_REFERER']);
		}
		else
			header('Location:/user_panel.php');
	}
	else
	{
		$_SESSION['signin_msg']="Login Failed !!! Check your Email or Password";
		header('Location:' . $_SERVER['HTTP_REFERER']);
	}
}
else
{

	header('Location:' . $_SERVER['HTTP_REFERER']);
}
exit;
?>
