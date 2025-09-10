<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="shortcut icon" href="img/favicon.png">

	<title>MAHE Photography Awards</title>

	<!-- CSS Global -->
	<link href="css/bootstrap/bootstrap.min.css" rel="stylesheet">

	<!-- CSS Plugins -->
	<link href="fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link href="css/animate.css" rel="stylesheet">

	<!-- Google Fonts -->
	<link href='http://fonts.googleapis.com/css?family=Oswald:400,700,300' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,700,500,400italic,500italic,700italic' rel='stylesheet' type='text/css'>

	<!-- CSS Custom -->
	<link href="css/color-styles.css" rel="stylesheet">
	<link href="css/ui-elements.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	
	<!-- Google reCaptcha -->
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	
	<script type='text/javascript'>
		function refreshCaptcha(){
			var img = document.images['captchaimg'];
			img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
		}
	</script>
  
	<script type="text/javascript">
		function validateForm(){

			var re=/^[A-z]+$/; 
			var phoneno = /^\+?([0-9]{2})\)?[-. ]?([0-9]{4})[-. ]?([0-9]{4})$/;

			var fname=document.getElementById('fname');
			var phone=document.getElementById('phone');

			if(!re.test(fname.value) ) { 
				alert("please enter vaild first name"); 
				return false ; 
			}

			if(!phone.value.match(phoneno)) { 
				alert("please enter vaild phone no"); 
				return false ; 
			}
		}

	</script>

</head>

<body class="body-blue">

	<!-- Extra Bar -->
	<?php include"master/header.php";?>

	<!-- Wrapper -->
	<div class="wrapper">

		<!-- Topic Header -->
		<div class="topic">
			<div class="container">
				<div class="col-sm-4">
					<h3 class="primary-font">Contact Us</h3>
				</div>
				<div class="col-sm-8">
					<ol class="breadcrumb pull-right hidden-xs">
						<li><a href="index.html">Home</a></li>
						<li class="active">Contact Us</li>
					</ol>
				</div>
			</div>
		</div>

		<div class="container">
			<div class="row">
				<div class="col-sm-8 contact-us-p">
					<h2 class="headline first-child text-color">
						<span class="border-color">Contact Us</span>
					</h2>

					<!-- Alert message -->
					<?php
						if(isset($_SESSION['snd_msg'])) {
					?>
					<div class="alert alert-warning">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Status:) </strong>
					<?php echo $_SESSION['snd_msg']; ?>
					</div>
					<?php
						unset($_SESSION['snd_msg']);
						}  
					?>

					<!-- Please carefully read the README.txt file in order to setup
						the PHP contact form properly -->
					<form role="form"  method="post" action="op/contact_mail.php" onsubmit="return validateForm()" name="myForm">
						<div class="form-group">
							<label for="name">Your name</label>
							<input type="text" name="fname" class="form-control" id="name" placeholder="Full Name" data-original-title="" title="" required>
							<span class="help-block"></span>
						</div>
			  
						<div class="form-group">
							<label for="email">Your email address</label>
							<input type="email" name="email" class="form-control" id="email" placeholder="E-mail" data-original-title="" title="" required>
							<span class="help-block"></span>
						</div>
						
						<div class="form-group">
							<label for="message">Phone/Mobile Number</label>
							<input type="text" name="phone" class="form-control" required id="phone" placeholder="Phone/Mobile Number" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter your Phone/Mobile Number here." data-original-title="Phone/Mobile Number" required>
							<span class="help-block"></span>
						</div>
 
						<div class="form-group">
							<label for="message">Your message</label>
							<textarea name="message" class="form-control" rows="3" id="message" placeholder="Message" required></textarea>
							<span class="help-block"></span>
						</div>

						<!-- reCAPTCHA 
						<div class="form-group">
							<div class="row">
								<div class="col-sm-6">
									<label for="email">Validation code:</label><br>
									<img src="captcha.php?rand=<?php echo rand();?>" id='captchaimg'>
								</div>
								<div class="col-sm-6">
									<label for="email">Enter the Validation code here :</label>
									<input id="captcha_code" class="form-control" name="captcha_code" type="text" required>
									Can't read the image? click <a href='javascript: refreshCaptcha();'>here</a> to refresh
								</div>
							</div>
						</div>
						<!-- /reCAPTCHA -->
						
						<!-- Google recaptcha -->
						<div class="row">
							<div class="col-sm-6">
								<div class="g-recaptcha" data-sitekey="6LfpiFQUAAAAALdj6b1ZSgKJjh7LQkxXo_Xs2KNu"></div>
							</div>
							<div class="col-sm-3"></div>
							<div class="col-sm-3">
								<br>
								<button type="submit" class="btn btn-color btn-lg" name="btn_send">Contact Us</button>
							</div>
						</div>
					</form>
				</div>
				<div class="col-sm-4">
					<h2 class="headline first-child first-child-m text-color">
						<span class="border-color">Our Address</span>
					</h2>
					<p>
						TT Hall, 3rd Floor,<br>
						State Youth Centre of Karnataka,<br>
						Nrupathanga Road, Bengaluru - 560001.<br>
						Email: <a href="mailto:contactus@ypsbengaluru.com">contactus@ypsbengaluru.com</a>
					</p>
					<h2 class="headline text-color">
						<span class="border-color">Google Map</span>
					</h2>
					<div class="embed-responsive embed-responsive-4by3">
						<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3887.9855750024462!2d77.58515821550598!3d12.972774318360901!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bae1673793fefc5%3A0xffb51bb3af1f933a!2sYouth%20Photographic%20Society!5e0!3m2!1sen!2sin!4v1569008638389!5m2!1sen!2sin" width="600" height="450" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- / .wrapper -->

	<!-- Footer -->
	<?php include"master/footer1.php";?>

	<!-- Style Toggle -->
	<?php include "style_tool.php";?>
	<!-- JavaScript
	================================================== -->
	
	<!-- JS Global -->
	<script src="js/jquery.min.js"></script>
	<script src='../../../../www.google.com/recaptcha/api.js'></script>
	<script src="js/bootstrap/bootstrap.min.js"></script>

	<!-- JS Plugins -->
	<script src="js/scrolltopcontrol.js"></script>
	<script src="js/jquery.sticky.js"></script>
	<script src="js/contact.js"></script>

	<!-- JS Custom -->
	<script src="js/custom.js"></script>

</body>

</html>
