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
					if(isset($_SESSION['signin_msg'])) {
					?>
						<div class="alert alert-warning">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong>Warning:) </strong>
							<?php echo $_SESSION['signin_msg']; ?>
						</div>
					<?php
						unset($_SESSION['signin_msg']);
					}
					?>
					<?php
					if(isset($_SESSION['rec_msg'])) {
					?>
						<div class="alert alert-warning">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong>Warning:) </strong>
							<?php echo $_SESSION['rec_msg']; ?>
						</div>
					<?php
						unset($_SESSION['rec_msg']);
					}  
					?>
					<div class="sign-form">
						<h3 class="first-child">Sign In To Admin Account</h3>
						<hr>
						<form role="form" method="post" action="op/admin_login.php">
							<label class="sr-only" for="email">Username</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-user"></i></span>
								<input type="text" name="username" class="form-control" placeholder="Enter Username" required>
							</div>
							<br>
							<label class="sr-only" for="exampleInputPassword1">Password</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-lock"></i></span>
								<input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password" required>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox"> Remember me
								</label>
							</div>
							<button type="submit" class="btn btn-color" name="admin_login_check">Submit</button>
							<hr>
						</form>

						<!-- Lost password form -->
						<p>
							Lost your password? <a href="#lost-password__form" data-toggle="collapse" aria-expanded="false" aria-controls="lost-password__form">Click here to recover.</a>
						</p>
						<div class="collapse" id="lost-password__form">
							<p class="text-muted">
								Enter your email address & username below and we will send you a link to reset your password.
							</p>
							<form class="form-inline" role="form" method="post" action="op/recover_password.php">
								<div class="form-group">
									<label class="sr-only" for="lost-password__email">User Nname</label>
									<input type="text" class="form-control"  name="username" placeholder="Enter username">
								</div>
								<div class="form-group">
									<label class="sr-only" for="lost-password__email">Email address</label>
									<input type="email" class="form-control" id="lost-password__email" name="email" placeholder="Enter email">
								</div>
								<button type="submit" class="btn btn-color" name="recover">Send</button>
							</form>
						</div> <!-- lost-password__form -->
					</div>
				</div>
			</div> <!-- / .row -->
		</div> <!-- / .container -->
	</div> <!-- / .wrapper -->

    <!-- Footer -->
	<?php include"master/footer1.php"; ?>

    <!-- Style Toggle -->
    <div class="style-toggle text-left hidden-xs">
		<i class="fa fa-gears style-toggle-btn"></i>
		<div class="style-toggle-header text-center">
			Navbar Type
		</div>
		<!-- Navbar Type -->
		<div class="style-toggle-body text-left">
			<div class="radio">
				<label>
					<input type="radio" name="navbar" id="opt-navbar-dark" value="opt-navbar-dark" checked>
					Navbar Dark (default)
				</label>
			</div>
			<div class="radio">
				<label>
					<input type="radio" name="navbar" id="opt-navbar-white" value="opt-navbar-white">
					Navbar White
				</label>
			</div>
			<div class="radio">
				<label>
					<input type="radio" name="navbar" id="opt-navbar-mixed" value="opt-navbar-mixed">
					Navbar Mixed
				</label>
			</div>
		</div>
		<!-- Color Styles-->
		<div class="style-toggle-header text-center">
			Color Styles
		</div>
		<div class="style-toggle-body text-center">
			<ul class="colors list-inline">
				<li class="green"></li>
				<li class="blue"></li>
				<li class="orange"></li>
				<li class="red"></li>
			</ul>
		</div>
		<!-- Footer Type -->
		<div class="style-toggle-header text-center">
			Footer Type
		</div>
		<div class="style-toggle-body text-left">
			<div class="radio">
				<label>
					<input type="radio" name="footer" id="opt-footer-dark" value="opt-footer-dark" checked>
					Footer Dark (default)
				</label>
			</div>
			<div class="radio">
				<label>
					<input type="radio" name="footer" id="opt-footer-white" value="opt-footer-white">
					Footer White
				</label>
			</div>
			<hr>
			<a href="#"><i class="fa fa-question-circle"></i> Using color schemes on a production site.</a>
		</div>
	</div>

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