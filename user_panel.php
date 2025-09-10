<?php
session_start();
include "op/connect.php";
if(isset($_SESSION['USER_ID']))
{
	$uid=$_SESSION['USER_ID'];

	$user = mysqli_query($DBCON, "SELECT * FROM entry WHERE entry_id='$uid'") or die(mysqli_error($DBCON));
	$tr_user=mysqli_fetch_array($user);
	$total=0;

	$res = mysqli_query($DBCON, "SELECT section, max_pics_per_entry FROM section") or die(mysqli_error($DBCON));;
	$contestSection = array();
	$maxPics = array();
	for ($tr=0; ($row = mysqli_fetch_array($res)); $tr++) {
			$contestSection[$tr] = $row["section"];
			$maxPics[$row["section"]] = $row["max_pics_per_entry"];
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

	<script src="js/jquery.js"></script>
	<script src="js/addtxt.js"></script>


	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

<style type="text/css">
#div1,#div2,#div3 {
display: none
}
.img-responsive {
min-width: 100px;
min-height: 100px;
}
</style>

<script type="text/javascript">
function showHide(elem) {
	document.getElementById('div1').style.display = 'block';
}

window.onload=function() {
	//get the divs to show/hide
	divsO = document.getElementById("frmMyform").getElementsByTagName('div');
}
</script>

<script src="http://code.jquery.com/jquery-1.5.js"></script>

<script>
function countChar(val) {
	var len = val.value.length;
	if (len >= 30) {
		val.value = val.value.substring(0, 30);
	} else {
		$('#charNum').text(30 - len);
	}
};
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

		<div class="container">
			<div class="row blog-p">
				<div class="col-sm-8">
					<div class="sign-form">
						<?php
							if(isset($_SESSION['pass_update']))
							{
						?>
						<div class="alert alert-success">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong>Success:) </strong><?php echo $_SESSION['pass_update']."</div>";
							unset($_SESSION['pass_update']);
							}
							if(isset($_SESSION['upload_msg']))
							{
						?>
						<div class="alert alert-info">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong>Status:) </strong><?php echo $_SESSION['upload_msg']."</div>";
							unset($_SESSION['upload_msg']);
							}
							if(isset($_SESSION['upload_error']) && $_SESSION['upload_error']!="")
							{
						?>
						<div class="alert alert-warning">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong>Warning:)</strong> Image does not conform to the size requirements. The following photos are not uploaded : <b><?php echo $_SESSION['upload_error']."</b></div>";
							unset($_SESSION['upload_error']);
							}
						?>
						<table class="table table-bordered">
							<tr>
								<td><strong>ID: YPS<?php printf("%04d", $tr_user['entry_id']);?></strong></td>
								<td>Club: <?php echo $tr_user['club'];?></td>
								<td>Honors: <?php echo $tr_user['honors'];?></td>
							</tr>
						</table>

						<div class="alert alert-info">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong style="color:red;">READ BEFORE UPLOADING</strong> 
							<ol>
								<li>Images must be in JPG/JPEG format. Size on the long side must <b>not exceed 1920 pixels</b> and long side should <b>not be less than 1080 pixels</b>. Maximum file size 4 MB.</li>
								<!--<li>Since the selected photograph(s) would be displayed as large hoardings, the Photographer of selected picture will be 
										required to submit a full resolution picture of not less than 4500 pixels on the long side, 
										preferably in 16 Bit TIFF format. Before submitting the picture, please ensure that this requirement can be met.</li>
								<li>Pictures that have already won Awards or Certificates in the earlier Karnataka Tourism Contests hosted since Sep 2016 by 
										YPS are not eligible to compete.</li>-->
								<li>Uploads with your name in the Picture or Title or File Name will not be considered.</li>
								<!--<li>Only pictures taken in Karnataka should be uploaded.</li>-->
								<li>Please see on the side panel for the maximum number of files you can upload under each category.</li>
							</ol>
						</div>

						<form role="form" method="post" action="op/upload_img.php" enctype="multipart/form-data" id="frmMyform">
							<div class="form-group">
								<label for="honors">Select Section</label>
								<select class="form-control" name="selection" onchange="showHide(this)" required>
									<?php
										$sectionsAvailable = 0;
										for($tr = 0; $tr < count($contestSection); $tr++) {
											$sec = $contestSection[$tr];
											$rs = mysqli_query($DBCON, "SELECT COUNT(*) FROM pic WHERE entry_id='$uid' AND section='$sec'")or die(mysqli_error($DBCON));
											$count = mysqli_fetch_array($rs)[0];
											if($count < $maxPics[$sec]) {
												++$sectionsAvailable;
									?>
									<option value="<?php echo $sec; ?>"><?php echo $sec; ?></option>
									<?php
											}
										}	
									?>
								</select>
							</div>
							<?php
								if ($sectionsAvailable == 0){
							?>
							<div class="alert alert-info">
								<p>Congratulations ! You have uploaded the maximum number of uploads possible across all sections. 
									You can replace an already uploaded picture
									by deleting it (by clicking on the <i class="ace-icon fa fa-trash"></i> icon against the image) and upload a different image under
									that section.
								</p>
							</div>
							<?php
								}
								else {
							?>
							<div class="form-group">
								<div class="row">
									<label class="col-sm-3 control-label">Select Picture*</label>
									<div class="col-sm-9">
										<input type="file" name="file" required/>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-12">
										<input type="text" class="form-control" name="img_title" placeholder="Image Title*" onkeyup="countChar(this)" required>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-12">
										<input type="text" class="form-control" name="location" placeholder="Location where this was shot*" onkeyup="countChar(this)" required>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<input type="checkbox" name="confirm" value="confirm" required> I confirm the correctness of the location*
									</div>
								</div>
							</div>
							<hr>

							<button type="submit" class="btn btn-color" name="upload_img">Upload Photo</button>
							<?php
								}
							?>
						</form>

					</div>

					<div class="clearfix"></div>
				</div>

				<div class="col-sm-4">
					<!-- Search Form -->
					<!-- Social Links -->
					<h4 class="primary-font">Side Menu</h4>
					<ul class="blog-p-social">
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
						<li><a href="user_update_panel.php"><i class="fa fa-edit"></i> Update Information</a></li>
					</ul>

					<!-- Previous Uploads -->
					<h4 class="primary-font">Your Uploaded Photos</h4>
					<?php
						for ($tr = 0; $tr < count($contestSection); $tr++) {
							$sec = $contestSection[$tr];
							$res = mysqli_query($DBCON, "SELECT COUNT(*) FROM pic WHERE entry_id='$uid' AND section='$sec'") or die(mysqli_error($DBCON));
							$numpics = mysqli_fetch_array($res)[0];
					?>
					<div class="row">
						<h5 class="primary-font"><i class="fa fa-edit"></i><?php echo $sec . "(" . $numpics . "/" . $maxPics[$sec] . ")"; ?></h5>
						<div class="col-sm-12">
							<?php
								$pics = mysqli_query($DBCON, "SELECT * FROM pic WHERE entry_id='$uid' AND section='$sec'") or die(mysqli_error($DBCON));
								while($tr_pics=mysqli_fetch_array($pics))
								{
							?>
							<div class="col-sm-6 thumbnail">
								<div style="max-width:100%; ">
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
				</div> <!-- / .row -->
			</div>
		</div> <!-- Container -->
	</div> <!-- / .wrapper -->

	<?php
		if(isset($_GET['delete_id']))
		{
			$del=$_GET['delete_id'];
			$sql_query="DELETE FROM pic WHERE pic_id='$del'";
			mysqli_query($DBCON, $sql_query);
			echo "<meta http-equiv=\"refresh\" content=\"0;URL=user_panel.php\">";
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

