<?php
session_start();
include"connect.php";

function generate_password($length = 8, $complex=3) {
	$min = "abcdefghijklmnopqrstuvwxyz";
	$num = "0123456789";
	$maj = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$symb = "!@#$%^&*()_-=+;:,.?";
	$chars = $min;
	if ($complex >= 2) { $chars .= $num; }
	if ($complex >= 3) { $chars .= $maj; }
	if ($complex >= 4) { $chars .= $symb; }
	$password = substr( str_shuffle( $chars ), 0, $length );
	return $password;
}

if (isset($_POST['recover']))
{

	$email= $_POST['email'];

	$edate=date('D, M jS, Y H:i:s A');
	$qchk = mysqli_query($DBCON, "SELECT * FROM entry WHERE email='$email'")or die(mysqli_error($DBCON));
	$count = mysqli_num_rows($qchk);
	$row=mysqli_fetch_array($qchk);

	if ($count > 0) {
		$password=generate_password(8);

		mysqli_query($DBCON, "update entry set password='$password' WHERE email='$email'")or die(mysqli_error($DBCON));

		$to = $email;
		$subject = "Recover Your MAHE Photo Awards Account";
		$txt = "<html><body><b>Username:- </b>" . $email . "<br><b>Your New Password:- </b>" . $password . "</body></html>";
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: <contest@ypsbengaluru.com>' . "\r\n";
		$headers .= 'Cc: <contest@ypsbengaluru.com>' . "\r\n";

		if(mail($to, $subject, $txt, $headers))
			$msg="password has been send in your registered email id<" . $email . ">";


	}
	else
	{
		$msg= "Your Email ID doesn't match";
	}
	$_SESSION['rec_msg']= $msg;
}
header('Location:' . $_SERVER['HTTP_REFERER']); 
?>
