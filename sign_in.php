<?php
session_start();
include "op/connect.php";
if(isset($_SESSION['USER_ID']))
{
	$uid=$_SESSION['USER_ID'];

	$user = mysqli_query($DBCON, "SELECT * FROM entry WHERE entry_id='$uid'") or die(mysqli_error($DBCON));
	if ($row = mysqli_fetch_array($user)) {
		$user_gender = $row['gender'];
		$user_age = $row['age'];
		if ( trim($user_gender) != "" && $user_age != 0)
			header('Location:/user_panel.php');
	}
}
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
</head>

<body class="body-blue">

	<!-- Extra Bar -->

	<?php include"master/header.php";?>

	<!-- Wrapper -->
	<div class="wrapper">

		<!-- Topic Header -->
		<div class="topic">
			<div class="container">
				<div class="row">
					<div class="col-sm-4">
						<h3 class="primary-font">Sign In</h3>
					</div>
					<div class="col-sm-8">
						<ol class="breadcrumb pull-right hidden-xs">
							<li><a href="index.php">Home</a></li>
							<li class="active">Sign In</li>
						</ol>
					</div>
				</div>
			</div>
		</div>

		<div class="container">
			<div class="row">
				<div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">

					<?php
					if(isset($_SESSION['signin_msg']))
					{
					?>
					<div class="alert alert-warning">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Warning:) </strong><?php echo $_SESSION['signin_msg']."</div>";
						unset($_SESSION['signin_msg']);
					}
					if(isset($_SESSION['rec_msg']))
					{
					?>
					<div class="alert alert-warning">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Warning:) </strong><?php echo $_SESSION['rec_msg']."</div>";
						unset($_SESSION['rec_msg']);
					}
					?>
					<?php
						$key = empty($_REQUEST['key']) ? "" : $_REQUEST['key'];
						if (date("Y-m-d") > $submissionLastDate && $key != "iamsmtheadmin")  {
					?>
					<br><br><br><br>
					<h1>Thank you for your interest in participation. The Contest is now closed for submission of pictures</h1>
					<br><br><br><br>
					<?php
						}
						else {
					?>
					<div class="sign-form">
					<?php
							if (! isset($_SESSION['USER_ID']) ) {
					?>
						<h3 class="first-child">Sign In To Your Account</h3>
						<hr>
						<form role="form" method="post" action="op/login.php">
							<label class="sr-only" for="email">Email address</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-user"></i></span>
								<input type="email" name="username" class="form-control" id="email" placeholder="Enter email">
							</div>
							<br>
							<label class="sr-only" for="exampleInputPassword1">Email address</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-lock"></i></span>
								<input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
							</div>
							<br>
							<button type="submit" class="btn btn-color" name="login_check">Submit</button>
							<hr>
						</form>

						<!-- Sign up link -->
						<p>Not registered? <a href="sign_up.php">Create an Account.</a></p>

						<!-- Lost password form -->
						<p>
							Lost your password? <a href="#lost-password__form" data-toggle="collapse" aria-expanded="false" aria-controls="lost-password__form">Click here to recover.</a>
						</p>
						<div class="collapse" id="lost-password__form">
							<p class="text-muted">
								Enter your email address below and we will send you a link to reset your password.
							</p>
							<form class="form-inline" role="form" method="post" action="op/recover_password.php">
								<div class="form-group">
									<label class="sr-only" for="lost-password__email">Email address</label>
									<input type="email" class="form-control" id="lost-password__email" name="email" placeholder="Enter email">
								</div>
								<button type="submit" class="btn btn-color" name="recover">Send</button>
							</form>
						</div> <!-- lost-password__form -->
					<?php
							}
							else {
					?>
						<h3 class="first-child">Update Additional Profile Information Required</h3>
						<hr>
						<form role="form" method="post" action="op/update_gender.php">
							<label class="sr-only" for="inp_gender">Gender</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-venus-mars"></i></span>
								<select class="form-control" name="gender" id="inp_gender" required>
									<option value="M">Male</option>
									<option value="F">Female</option>
								</select>
							</div>
							<br>
							<label class="sr-only" for="inp_age">Age</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-bars"></i></span>
								<input type="number" name="age" id="inp_age" class="form-control" min="1" max="125" required>
							</div>
							<br>
							<button type="submit" class="btn btn-color" name="update_profile">Update Profile</button>
							<hr>
						</form>
					<?php
							}
					?>
					</div>
					<?php
						}
					?>
				</div>
			</div> <!-- / .row -->
		</div> <!-- / .container -->

	</div> <!-- / .wrapper -->

	<!-- Footer -->
	<?php include "master/footer1.php"; ?>

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