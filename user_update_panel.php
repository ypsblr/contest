<?php 
session_start();
include"op/connect.php";
if(isset($_SESSION['USER_ID']))
{	/*** 01 ***/
	$uid=$_SESSION['USER_ID'];
	$user = mysqli_query($DBCON, "SELECT * FROM entry WHERE entry_id='$uid'")or die(mysqli_error($DBCON));
	$tr_user=mysqli_fetch_array($user);
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
  
    <script src="js/jquery.js"></script>
    <script src="js/addtxt.js"></script>
	

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	
<style>
.img-responsive {
	min-width: 100px;
	min-height: 100px;
}
</style>
<script type='text/javascript'>
function refreshCaptcha(){
	var img = document.images['captchaimg'];
	img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
}
</script>

<script type="text/javascript">

function validateForm(){

var re=/^[A-z. ]+$/; 
var no=/^([0-9]{6})$/;
var phoneno = /^\+?[-. 0-9]*$/;
var emailstr = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;

var name=document.getElementById('name');
var phone=document.getElementById('phone');
var state=document.getElementById('state');
var city=document.getElementById('city');
var pin=document.getElementById('pin');

if(!re.test(name.value) ) {
	alert("Please enter vaild First name"); 
	return false ; 
}

if(!phone.value.match(phoneno)) {
	alert("Please enter vaild phone no"); 
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
	<?php include "master/header.php";?>
	<!-- / navbar -->

	<!-- Wrapper -->
	<div class="wrapper">
		<!-- Topic Header -->
		<div class="topic">
			<div class="container">
				<div class="row">
					<div class="col-sm-4">
						<h3 class="primary-font">Welcome <?php echo $tr_user['name'];?></h3>
					</div>
					<div class="col-sm-8">
						<ol class="breadcrumb pull-right hidden-xs">
							<li><a href="index.php">Home</a></li>
							<li class="active"><a href="op/logout.php">Log Out</a></li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<!-- End of Top Header -->

		<!-- Container Start -->
		<div class="container">
			<div class="row blog-p">
				<div class="col-sm-8">
					<div class="sign-form">
						<?php
							if(isset($_SESSION['pass_update']))
							{	/*** 02 ***/
						?>
						<div class="alert alert-success">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong>Success:) </strong><?php echo $_SESSION['pass_update']."</div>";
							unset($_SESSION['pass_update']);
							}	/*** 02 ***/
						if(isset($_SESSION['info_update']))
						{	/*** 03 ***/
						?>
						<div class="alert alert-success">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong>Success:) </strong><?php echo $_SESSION['info_update']."</div>";
							unset($_SESSION['info_update']);
						}	/*** 03 ***/
						/* if($tr_user['payment']=='')
						{*/	/*** 04 ***/
						?>
						<!--
						<div class="alert alert-warning">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong>Alerts!! </strong> You dont have any payment !!! Please <a href="user_payment.php"><i class="fa fa-money"></i> Click here</a>
						</div>
						-->
						<?php
						/* } */	/*** 04 ***/
						?>
						<!-- Update Form Start -->
						<form role="form" method="post" action="op/update_info.php" onsubmit="return validateForm()" id="form1" name="myForm">
							<div class="form-group">
								<div class="row">
									<div class="col-sm-12">
										<label class="sr-only" for="name">Name</label>
										<input type="text" name="name" class="form-control text-uppercase" id="name" value="<?php echo $tr_user['name'];?>" placeholder="Mr/Mrs." data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Mr/Mrs. First Name" data-original-title="Name" required>
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label class="sr-only" for="gender">Gender</label>
										<select class="form-control" name="gender" id="inp_gender" required>
											<option value="M" <?php echo ($tr_user['gender'] == 'M') ? "selected" : ""; ?>>Male</option>
											<option value="F" <?php echo ($tr_user['gender'] == 'F') ? "selected" : ""; ?>>Female</option>
										</select>
									</div>
									<div class="col-sm-6">
										<label class="sr-only" for="age">Age</label>
										<input type="number" name="age" class="form-control" id="club" min="1" max="125" value="<?php echo $tr_user['age'];?>" placeholder="Age in years" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter Age in years" data-original-title="Age" >
									</div>

								</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label class="sr-only" for="honors">Photographic Honors</label>
										<input type="text" name="honors" class="form-control text-uppercase" id="honors" value="<?php echo $tr_user['honors'];?>" placeholder="Photographic Honors" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Photographic Honors" data-original-title="Photographic Honors">
									</div>
									<div class="col-sm-6">
										<label class="sr-only" for="club">Club Name</label>
										<input type="text" name="club" class="form-control text-uppercase" id="club" value="<?php echo $tr_user['club'];?>" placeholder="Club Name" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter your Club Name here." data-original-title="Club" >
									</div>

								</div>
							</div>

							<div class="form-group">
								<label class="sr-only" for="address">Address</label>
								<textarea class="form-control text-uppercase" name="address" id="address" placeholder="Your Address" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter your full Address here." data-original-title="Address" required><?php echo $tr_user['address'];?></textarea>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label class="sr-only" for="password">State</label>
										<input type="text" name="state" class="form-control margin-bottom-xs text-uppercase" value="<?php echo $tr_user['state'];?>" placeholder="State" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Three symbols minimum." data-original-title="State" required>
									</div>
									<div class="col-sm-6">
										<label class="sr-only" for="repeat-password">City</label>
										<input type="City" name="city" class="form-control text-uppercase"  placeholder="City" value="<?php echo $tr_user['city'];?>" data-toggle="popover" data-trigger="focus" data-content="Make sure you still remember it." data-original-title="City" required>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label class="sr-only" for="phone">Phone/Mobile Number</label>
										<input type="text" name="phone" class="form-control text-uppercase" value="<?php echo $tr_user['phone'];?>" id="phone" placeholder="Phone/Mobile Number" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter your Phone/Mobile Number here." data-original-title="Phone/Mobile Number" required>
									</div>
									<div class="col-sm-6">
										<label class="sr-only" for="pin">PIN</label>
										<input type="number" name="pin" class="form-control text-uppercase" value="<?php echo $tr_user['pin'];?>" id="pin" placeholder="PIN Code" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter City PIN Code here." data-original-title="PIN" required>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="email">Validation code:</label><br>
										<img src="captcha.php?rand=<?php echo rand();?>" id='captchaimg'>
									</div>
									<div class="col-sm-6">
										<label for="email">Enter the Validation code here :</label>
										<input id="captcha_code" class="form-control" name="captcha_code" type="text">
										Can't read the image? click <a href='javascript: refreshCaptcha();'>here</a> to refresh
									</div>
								</div>
							</div>

							<button type="submit" class="btn btn-color" name="update_info">Update</button>
						</form>
						<!-- Update Form Ends -->
					</div>
					<div class="clearfix"></div>
				</div>
				<!-- Side Menu start -->
				<div class="col-sm-4">
					<h4 class="primary-font">Side Menu</h4>
					<ul class="blog-p-social">
						<li><a href="user_panel.php"><i class="fa fa-forward"></i> Go Back/ Home</a></li>
						<li><a href="#recover-password" data-toggle="collapse" aria-expanded="false" aria-controls="recover-password"><i class="fa fa-edit"></i> Change Password</a></li>
						<div class="collapse" id="recover-password">
							<p class="text-muted">Reset your password.</p>
							<form class="form-inline" role="form" method="post" action="op/update_password.php">
								<div class="form-group" style="padding-bottom: 10px;">
									<label class="sr-only" for="lost-password__email">Old Passowrd</label>
									<input type="password" class="form-control" name="opassword" placeholder="Enter Old Password" required>
								</div>
								<div class="form-group">
									<label class="sr-only" for="lost-password__email">New Password</label>
									<input type="password" class="form-control" name="npassword" placeholder="Enter New Password" required>
								</div>
								<button type="submit" class="btn btn-color" name="update_password">Update</button>
								<hr>
							</form>
						</div>
					</ul>

					<!-- Previous Uploads -->
					<h4 class="primary-font">Your Uploaded Photos</h4>
					<?php
						for ($tr = 0; $tr < count($section); $tr++) {
							$sec = $section[$tr];
							$res = mysqli_query($DBCON, "SELECT COUNT(*) FROM pic WHERE entry_id='$uid' AND section='$sec'") or die(mysqli_error($DBCON));
							$numpics = mysqli_fetch_array($res)[0];
					?>
					<div class="row">
						<h5 class="primary-font"><i class="fa fa-edit"></i><?php echo $sec . "(" . $numpics . "/" . $sectionMaxPics[$tr] . ")"; ?></h5>
						<div class="col-sm-12">
							<?php
								$pics = mysqli_query($DBCON, "SELECT * FROM pic WHERE entry_id='$uid' AND section='$sec'") or die(mysqli_error($DBCON));
								while($tr_pics=mysqli_fetch_array($pics))
								{
							?>
							<div class="col-sm-6 thumbnail">
								<div style="max-width:100%;">
									<a href="javascript:void(0);">
										<img src="/upload/<?php echo $sec . "/tn/" . $tr_pics['picfile'];?>" class="img-responsive" style="margin:auto; height:auto;" alt="..." >
										<div class="mask">
											<?php echo substr($tr_pics['title'], 0, 30)."..";?>
											<a href="user_panel.php?delete_id=<?php echo $tr_pics['pic_id'];?>" methods="post" onclick="return confirm('Are you sure you want to DELETE this Image ? ');"   title="Delete"><i class="ace-icon fa fa-trash"></i></a>
										</div>
									</a>
								</div>
							</div>
							<?php
								}
							?>
						</div>
					</div>
					<?php
						}
					?>
				</div>
				<!-- Side Menu End -->
			</div>
		</div>
		<!-- Container End -->
	</div> <!-- Wrapper End -->

	<?php
		if(isset($_GET['delete_id']))
		{
			$del=$_GET['delete_id'];
			mysqli_query($DBCON, "DELETE from pic WHERE pic_id='$del'")or die(mysqli_error($DBCON));
			echo "<meta http-equiv=\"refresh\" content=\"0;URL=user_update_panel.php\">";
		}
	?>              
              
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

<?php
}
else
{
header('Location:' . $_SERVER['HTTP_REFERER']); 
}

?>