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
	<script type="text/javascript" src="js/bootstrap/jquery.min.js"></script>

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

<script type='text/javascript'>
function refreshCaptcha(){
	var img = document.images['captchaimg'];
	img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
}
</script>
  
<script type="text/javascript">

function validateForm(){

var re=/^[A-z ]+$/; 
var no=/^([0-9]{6})$/;
var phoneno = /^\+?[-. 0-9]*$/;
var emailstr = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;

var fname=document.getElementById('fname');
var lname=document.getElementById('lname');
var phone=document.getElementById('phone');
var email=document.getElementById('email');
var state=document.getElementById('state');
var city=document.getElementById('city');
var pin=document.getElementById('pin');

if(!re.test(fname.value) ) {
	alert("Please enter vaild First name"); 
	return false ; 
}

if(!re.test(lname.value) ) { 
	alert("Please enter vaild Last name"); 
	return false ; 
}

if(!phone.value.match(phoneno)) {
	alert("Please enter vaild phone no"); 
	return false ; 
}

if(!email.value.match(emailstr)) {
	alert("Please enter vaild email"); 
	return false ; 
}

if(!re.test(state.value) ) { 
	alert("Please enter vaild State name"); 
	return false ; 
}

if(!re.test(city.value) ) { 
	alert("Please enter vaild City name"); 
	return false ; 
}

if(!pin.value.match(no)) {
	alert("Please enter vaild PIN Code"); 
	return false ; 
}

}

</script>

</head>

<body class="body-blue">

	<!-- Extra Bar -->

	<?php include"master/header.php";?>
	<!-- / navbar -->

	<!-- Wrapper -->
	<div class="wrapper">

		<!-- Topic Header -->
		<div class="topic">
			<div class="container">
				<div class="row">
					<div class="col-sm-4">
						<h3 class="primary-font">Register</h3>
					</div>
					<div class="col-sm-8">
						<ol class="breadcrumb pull-right hidden-xs">
							<li><a href="index.html">Home</a></li>
							<li class="active">Register</li>
						</ol>
					</div>
				</div>
			</div>
		</div>

		<div class="container">
			<div class="row blog-p">
				<div class="col-sm-9">
					<?php
						if(isset($_SESSION['signup_msg']))
						{
					?>
							<div class="alert alert-warning">
								<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
								<strong>Warning:) </strong>
					<?php echo $_SESSION['signup_msg']."</div>";
							unset($_SESSION['signup_msg']);
						}  
						if(isset($msg))
						{
					?>
						<div class="alert alert-warning">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong>Warning:) </strong>
					<?php echo $msg."</div>";
						}
					?>
					<?php
						if (date("Y-m-d") > $submissionLastDate) {
					?>
						<br><br><br><br>
						<h1>Thank you for your interest in participation. The Contest is now closed for submission of pictures</h1>
						<br><br><br><br>
					<?php
						}
						else {
					?>
					
					<div class="sign-form">
						<h3 class="first-child">Registration Form</h3>
						<hr>
						<p>If you had already registered during the previous Contests, you can <a href="./sign_in.php">Sign In</a> using the
							same Email and Password. If you face any challenges <a href="./contact_us.php">Contact Us</a>.</p>
						<form role="form" method="post" action="op/register.php" id="form1" onsubmit="return validateForm()" name="myForm">
							<input type="hidden" name="rurobot" value="IamHuman">
							<div class="form-group">
								<div class="row">
									<div class="col-sm-12">
										<label>Salutation*</label>&nbsp;&nbsp;&nbsp;&nbsp;  
										<input type="radio" value="Mr" name="salutation" checked>&nbsp;&nbsp;Mr.  &nbsp;&nbsp;<input type="radio" value="Ms" name="salutation">&nbsp;&nbsp;Ms.
										<input type="radio" value="Mrs" name="salutation">&nbsp;&nbsp;Mrs.  &nbsp;&nbsp;<input type="radio" value="Dr" name="salutation">&nbsp;&nbsp;Dr.
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label class="sr-only" for="family">First Name</label>
										<input type="text" name="fname" class="form-control text-uppercase" id="fname" placeholder="Your Firstname" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Mr/Mrs. First Name" data-original-title="Name" required>
									</div>
									<div class="col-sm-6">
										<label class="sr-only" for="family">Last Name</label>
										<input type="text" name="lname" class="form-control text-uppercase" id="lname" placeholder="Your Surname" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter your Surname here." data-original-title="Name" required>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label class="sr-only" for="gender">Gender</label>
										<select class="form-control" name="gender" id="inp_gender" required>
											<option value="M">Male</option>
											<option value="F">Female</option>
										</select>

										</div>
									<div class="col-sm-6">
										<label class="sr-only" for="age">Age</label>
										<input type="number" name="age" class="form-control" id="age" placeholder="Enter Age" min="1" max="125" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter your Surname here." data-original-title="Name" required>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label class="sr-only" for="honors">Photographic Honors</label>
										<input type="text" name="honors" class="form-control text-uppercase" id="honors" placeholder="Photographic Honors" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Photographic Honors" data-original-title="Photographic Honors">
									</div>
									<div class="col-sm-6">
										<label class="sr-only" for="club">Club Name</label>
										<input type="text" name="club" class="form-control text-uppercase" id="club" placeholder="Club Name" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter your Club Name here." data-original-title="Club">
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="sr-only" for="address">Address</label>
								<textarea class="form-control text-uppercase" name="address" id="address" placeholder="Your Address" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter your full Address here." data-original-title="Address" required></textarea>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label class="sr-only" for="password">State</label>
										<input type="text" name="state" class="form-control margin-bottom-xs text-uppercase" placeholder="State" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Three symbols minimum." data-original-title="State" required>
									</div>
									<div class="col-sm-6">
										<label class="sr-only" for="repeat-password">City</label>
										<input type="City" name="city" class="form-control text-uppercase"  placeholder="City" data-toggle="popover" data-trigger="focus" data-content="Make sure you still remember it." data-original-title="City" required>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label class="sr-only" for="phone">Phone/Mobile Number</label>
										<input type="text" name="phone" class="form-control text-uppercase" id="phone" placeholder="Phone/Mobile Number" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter your Phone/Mobile Number here." data-original-title="Phone/Mobile Number" required>
									</div>
									<div class="col-sm-6">
										<label class="sr-only" for="pin">PIN</label>
										<input type="number" name="pin" class="form-control text-uppercase" id="pin" placeholder="PIN Code" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter City PIN Code here." data-original-title="PIN" required>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label class="sr-only" for="email">Email address</label>
										<input type="email" name="email" class="form-control" id="email" placeholder="Enter email" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter a valid email here." data-original-title="Email" required>
									</div>
									<div class="col-sm-6">
										<label class="sr-only" for="email">Password</label>
										<input type="password" name="password" class="form-control" id="password" placeholder="Enter Password" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter a Password here." data-original-title="Password" required>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="email">Validation code:</label><br>
										<img src="captcha.php?rand=<?php echo rand();?>" id='captchaimg'>
										<br><a href='javascript: refreshCaptcha();'>Refresh Captcha</a>
									</div>
									<div class="col-sm-6">
										<label for="email">Enter the Validation code here :</label>
										<input id="captcha_code" class="form-control" name="captcha_code" type="text">
									</div>
								</div>
							</div>
.							<div class="checkbox">
								<label>
									<input type="checkbox" required> I agree to the <a href="term_condition.php" target="_blank">Terms & Conditions</a>
								</label>
							</div>
							<button type="submit" class="btn btn-color" name="new_registration">Create Account</button>
						</form>
					</div>
					<?php
						}
					?>
					<div class="clearfix"></div>
				</div>
				<div class="col-sm-3">
					<!-- Search Form -->
					<div class="blog-p-search">
						<form class="form" role="form">
							<div class="input-group">
								<input type="text" class="form-control" placeholder="Search our blog">
								<span class="input-group-btn">
									<button class="btn btn-color" type="button">Go!</button>
								</span>
							</div>
						</form>
					</div>

					<!-- Social Links -->
					<h4 class="primary-font">Side Menu</h4>
					<ul class="blog-p-social">
						<li><a href="sign_in.php"><i class="fa fa-lock"></i> Sign In</a></li>
						<!--
						<li><a href="#"><i class="fa fa-twitter"></i> Twitter</a></li>
						<li><a href="#"><i class="fa fa-facebook"></i> Facebook</a></li>
						-->
					</ul>

					<!-- Most Popular -->
					<h4 class="primary-font">
						Participate in 3 simple steps
					</h4>
					<ul class="blog-p-popular">
						<li><a href="javascript:void(0)">Step-1 Sign-Up Participate Account</a></li>
						<!-- <li><a href="sign_in.php">Step-2 Fillup Payment Details.</a></li> -->
						<li><a href="sign_in.php">Step-2 Upload Your Image</a></li>
						<li><a href="javascript:void(0)">Step-3 Wait for Result</a></li>
					</ul>
					<hr>

				</div>
			</div> <!-- / .row -->
		</div>
	</div> <!-- / .wrapper -->

	<!-- Footer -->
	<?php include"master/footer1.php";?>

	<!-- Style Toggle -->
	<?php include "style_tool.php"; ?>

	<!-- JavaScript
	================================================== -->

	<!-- JS Global -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap/bootstrap.min.js"></script>

	<!-- JS Plugins -->
	<script src="js/scrolltopcontrol.js"></script>
	<script src="js/jquery.sticky.js"></script>

	<!-- JS Custom -->
	<script src="js/custom.js"></script>

</body>

</html>