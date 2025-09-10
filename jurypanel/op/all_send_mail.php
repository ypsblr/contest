<?php
session_start(); 
include("connect.php");

$err="";
$count=$counterr=0;
if (isset($_POST['all_mail'])) {
	if(isset($_POST['checkbox'])) {
		$value=$_POST['checkbox'];
		foreach($value as $email) {
			$count++;
			$entry = mysqli_query($DBCON, "SELECT * FROM entry WHERE email='$email'")or die(mysqli_error());
			$row = mysqli_fetch_array($entry);
			$part_name=$row['name'];

	  		include("mastermail_msg.php");
			
			$to = $email;
			$subject = "You have not completed all the uploads for MAHE Photo Awards".$part_name;
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: <contactus@ybsbengaluru.com>' . "\r\n";
			$headers .= 'Cc: <contactus@ypsbengaluru.com>' . "\r\n";
			if(!mail($to,$subject,$message,$headers))
			{
				$err .= $email.", ";	
				$counterr++;
			}

		}

	if($err=="")
		$_SESSION['send_err']="";
	else
		$_SESSION['send_err']="Some email faild-:  ".$err;
	
	$_SESSION['count']=$count-$counterr;

   }
}

header("Location: ".$_SERVER['HTTP_REFERER']);
printf("<script>location.href='".$_SERVER['HTTP_REFERER']."'</script>");
						
?>		