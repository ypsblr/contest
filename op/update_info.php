<?php
session_start();
include"connect.php";
if (isset($_POST['update_info']))
{
	if(empty($_SESSION['captcha_code'] ) || strcasecmp($_SESSION['captcha_code'], $_POST['captcha_code'])== 0) {
		$uid=$_SESSION['USER_ID'];

		$name= strtoupper($_POST['name']);
		$gender = $_POST['gender'];
		$age = $_POST['age'];
		$honors= strtoupper($_POST['honors']);
		$honors=str_replace("'", " ", $honors);
		$address= strtoupper($_POST['address']);
		$address=str_replace("'", " ", $address);
		$state= strtoupper($_POST['state']);
		$city= strtoupper($_POST['city']);
		$pin = $_POST['pin'];
		$phone= $_POST['phone'];
		$club= strtoupper($_POST['club']);								

		$qchk = mysqli_query($DBCON, "SELECT * FROM entry WHERE entry_id='$uid'")or die(mysqli_error($DBCON));
		$count = mysqli_num_rows($qchk);
		if ($count > 0){		 
			mysqli_query($DBCON, "UPDATE entry SET name='$name', gender='$gender', age='$age', honors='$honors', address='$address', state='$state', city='$city', pin='$pin', phone='$phone', club='$club' WHERE entry_id='$uid'")or die(mysqli_error($DBCON));
			$_SESSION['info_update']="Personal Info has been update successfully!";
		}
		else
		{
		$_SESSION['info_update']="Sorry !! your user ID doesn't match!";
		}
	}
	else {
		$_SESSION['info_update']="Sorry !! Validation Code doesn't match!";
	}
}

header('Location:' . $_SERVER['HTTP_REFERER']); 
?>
