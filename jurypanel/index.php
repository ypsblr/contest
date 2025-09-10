<?php
session_destroy();
session_start();
$_SESSION = array();
?>
<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
       <title>Youth Photographic || Jury Panel</title>


    <!-- Vendor styles -->
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="vendor/metisMenu/dist/metisMenu.css" />
    <link rel="stylesheet" href="vendor/animate.css/animate.css" />
    <link rel="stylesheet" href="vendor/bootstrap/dist/css/bootstrap.css" />

    <!-- App styles -->
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/helper.css" />
    <link rel="stylesheet" href="styles/style.css">

</head>
<body class="blank">

	<!-- Simple splash screen-->
	<div class="splash"> 
		<div class="color-line"></div>
		<div class="splash-title">
			<h1>Youth Photographic || Jury Panel</h1>
			<div class="spinner"> 
				<div class="rect1"></div> 
				<div class="rect2"></div> 
				<div class="rect3"></div> 
				<div class="rect4"></div> 
				<div class="rect5"></div> 
			</div> 
		</div> 
	</div>
	<!--[if lt IE 7]>
		<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
	<![endif]-->
	<div class="color-line"></div>
	<div class="back-link">
		<a href="../index.php" class="btn btn-primary">Back to Main Website</a>
	</div>

	<div class="login-container">
	<?php
		if(isset($_SESSION['signin_msg'])) {
	?>
		<div class="alert alert-success">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			<strong>Status:) </strong>	<?php echo $_SESSION['signin_msg']; ?>
		</div>
	<?php
			unset($_SESSION['signin_msg']);
		} 
	?>

		<div class="row">
			<div class="col-md-12">
				<div class="text-center m-b-md">
					<h3>LOGIN TO JURY ACCOUNT</h3>
					<small>Login for Judging</small>
				</div>
				<div class="hpanel">
					<div class="panel-body">
                        <form action="op/admin_login.php" id="loginForm" method="post">
                            <div class="form-group">
                                <label class="control-label" for="username">Username</label>
                                <input type="text" placeholder="username" title="Please enter you username" required 
										name="username" id="username" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="password">Password</label>
                                <input type="password" title="Please enter your password" placeholder="******" required 
										name="password" id="password" class="form-control">
                                <span class="help-block small">Your strong password</span>
                            </div>
                            <!-- <div class="checkbox" style="margin-top:10px;background-color: #fff;color:#6a6c6f;">
                                <input type="checkbox" class="i-checks">
								Remember login
                                <p class="help-block small">(if this is a private computer)</p>
                            </div> -->
                            <button class="btn btn-success btn-block form-control" type="submit" name="admin_login_check">Login</button>
                            <!-- <a class="btn btn-default btn-block" href="password_recovery.php">Password Recover</a> -->
                        </form>
						<p><?php //print($_SESSION);?></p>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-12 text-center">
				<strong>Youth Photographic Society</strong> <br/> 
				&copy; YPS 2K16 | Design & Developed by <a href="http://www.imcanny.com/" target="_blank">www.imcanny.com</a>
			</div>
		</div>
	</div>  <!-- Container -->


	<!-- Vendor scripts -->
	<script src="vendor/jquery/dist/jquery.min.js"></script>
	<script src="vendor/jquery-ui/jquery-ui.min.js"></script>
	<script src="vendor/slimScroll/jquery.slimscroll.min.js"></script>
	<script src="vendor/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="vendor/metisMenu/dist/metisMenu.min.js"></script>
	<script src="vendor/iCheck/icheck.min.js"></script>
	<script src="vendor/sparkline/index.js"></script>

	<!-- App scripts -->
	<script src="scripts/homer.js"></script>

</body>
</html>