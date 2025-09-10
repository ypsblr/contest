<?php
session_start();
include"connect.php";
if(empty($_SESSION['captcha_code'] ) || (strcasecmp($_SESSION['captcha_code'], $_POST['captcha_code'])== 0 && $_POST['rurobot'] == 'IamHuman'))
{
	$name=$_POST['salutation'].". ". $_POST['fname']."  ".$_POST['lname'];
	$name=strtoupper($name);
	$gender = $_POST['gender'];
	$age = $_POST['age'];
	$honors= strtoupper($_POST['honors']);
	$honors=str_replace("'", " ", $honors);
	$address= strtoupper($_POST['address']);
	$address=str_replace("'", " ", $address);
	$state= strtoupper($_POST['state']);
	$city= strtoupper($_POST['city']);
	$pin=strtoupper($_POST['pin']);
	$phone= strtoupper($_POST['phone']);
	$email= $_POST['email'];
	$club= strtoupper($_POST['club']);
	$password= $_POST['password'];
	$password=str_replace("'", " ", $password);
	$edate=date('D, M jS, Y H:i:s A');
	
	$qchk = mysqli_query($DBCON, "SELECT * FROM entry WHERE email='$email'")or die(mysqli_error($DBCON));
	$count = mysqli_num_rows($qchk);
	 if ($count==0){
		mysqli_query($DBCON, "INSERT INTO entry(name,gender,age,honors,address,city,pin,state,phone,email,club,password,edate)
						VALUES('$name','$gender','$age','$honors','$address','$city','$pin','$state','$phone','$email','$club','$password','$edate')")or die(mysqli_error($DBCON));

		$rs = mysqli_query($DBCON, "SELECT entry_id FROM entry WHERE email='$email'")or die(mysqli_error($DBCON));
		$qd = mysqli_fetch_array($rs);
		$_SESSION['USER_ID']= $qd[0];

		$message="<html>
<head>
</head>
<body>
<p>
<strong>Hello ! Greetings from Youth Photographic Society !
</strong>
<br/>
<strong>Congratulations !</strong> you have been successfully registered for MAHE Photo Awards 2025.
You can now signin and upload your images. 
Stay tuned to the website for regular updates.
For any query, visit the website or feel free to contact us.
<hr/>
Your Username:  ".$email."
<br>Your Password:  ".$password." 
<hr/>
Regards,
Collaborator: Youth Photographic Society
On behalf of MAHE

</p>
</body>
</html>
";
 
		$to=$email;
		$subject="Congratulations ! you have been successfully registered in MAHE Photo Awards 2025";
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: <donotreply@ypsbengaluru.com>' . "\r\n";

		mail($to,$subject,$message,$headers);
		header('Location:../user_panel.php');
	}
	else
	{
		$_SESSION['signup_msg']="You have already registered. Signin using the previously registered password. ";
		header('Location:../sign_in.php');
	}
}
else
{
	$msg1="<span style='color:red'>The Validation code does not match!</span>";
	$_SESSION['signup_msg']=$msg1;
	header('Location:' . $_SERVER['HTTP_REFERER']); 
}
?>
