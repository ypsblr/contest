<?php
session_start();

include_once("connect.php");

//if(isset($_POST["captcha_code"])) {                  
//	if((empty($_SESSION['captcha_code']) || strcasecmp($_SESSION['captcha_code'], $_POST['captcha_code'])== 0))
//	{
//		echo json_encode(true);
//	}
//	else{
//		echo json_encode(false);
//	}
//}

if(isset($_REQUEST["email"])) { 
	$email=  trim($_REQUEST['email']);
	$sql = "SELECT * FROM entry, pic WHERE email ='$email' AND pic.entry_id = entry.entry_id ";
	$qchk = mysqli_query($DBCON, $sql) or die(json_encode(false));
	if(mysqli_num_rows($qchk) > 0)
		echo json_encode(true);	
	else
		echo json_encode(false);
}

?>
