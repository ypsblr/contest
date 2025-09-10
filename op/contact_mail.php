<?php
session_start();

function captcha_check($token) {
	// Call Google reCaptcha verification API
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	$post_fields = array();
	$post_fields["secret"] = "6LfpiFQUAAAAANvKz1nrcTHOGLJ8Bymz4QOKLKRQ";
	$post_fields["response"] = $token;
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
	
	$result = json_decode(curl_exec($ch), true);
	// print_r($result);

	return $result['success'];
}

if(isset($_POST['btn_send']) && isset($_REQUEST['g-recaptcha-response'])) {
	if(captcha_check($_REQUEST['g-recaptcha-response'])){
		$full_name = $_POST['fname'];
		$query = $_POST['message'];
		$phone = $_POST['phone'];
		$emailid = $_POST['email'];

		$email = $emailid;
		$subject = "Contact Enquiry";
			
        include "../master/contact_msg.php";
		   
		$to = "<contactus@ypsbengaluru.com>";
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: <donotreply@ypsbengaluru.com>' . "\r\n";
		$headers .= 'Cc: <' . $email . '>' . "\r\n";
					
		mail($to,$subject,$message,$headers);

		$snd_msg = "An email has been sent successfully !)";
	}
	else
		$snd_msg = 'The Validation code does not match.';				 
			
}		

$_SESSION['snd_msg']=$snd_msg;
//echo "<meta http-equiv=\"refresh\" content=\"0;URL=/contact_us.php\">";
header("Location: /contactus.php");
printf("<script>location.href='/contact_us.php'</script>");
?>


