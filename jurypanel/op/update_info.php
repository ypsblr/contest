<?php
session_start();
include("connect.php");

$session_id=$_SESSION['jury_id'];
$user_query = mysqli_query($DBCON, "SELECT * FROM user where user_id = '$session_id'") or die(mysqli_error($DBCON));
$user_row = mysqli_fetch_array($user_query);
$jury_login = $user_row['login'];
$jury_name = $user_row['name'];
$jury_pic = $user_row['picfile'];
$user_type = $user_row['type'];

if (isset($_POST['update_profile'])) {
	$name = strtoupper($_POST['name']);
	$name = str_replace("'", " ", $name);
	$npassword = strtoupper($_POST['npassword']);
	$opassword= strtoupper($_POST['opassword']);

	$qchk = mysqli_query($DBCON, "SELECT * FROM user WHERE user_id='$session_id' AND password='$opassword'") or die(mysqli_error($DBCON));
	$count = mysqli_num_rows($qchk);
	if ($count > 0){
		mysqli_query($DBCON, "UPDATE user SET name = '$name', password = '$npassword' WHERE user_id='$session_id'")or die(mysqli_error($DBCON));
		$_SESSION['info_update']="Personal Info has been update successfully!";
	}
	else {
		$_SESSION['info_update']="Sorry !! your password doesn't match!";
	}
}
header("Location: ".$_SERVER['HTTP_REFERER']);
printf("<script>location.href='".$_SERVER['HTTP_REFERER']."'</script>");
?>
