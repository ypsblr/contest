<?php 
session_start();
include("op/connect.php");

if(isset($_SESSION['jury_id'])) {  // to prevent being run from command line and to facilitate session variables

	// Load Current Picture and related details
	$query = mysqli_query($DBCON, "SELECT * FROM ctl, pic WHERE pic.pic_id=ctl.pic_id") or die(mysqli_error($DBCON));
	$row = mysqli_fetch_array($query);
	$cur_pic_id = $row["pic_id"];
	$cur_pic_section = $row["section"];		// Section of this picture
	$cur_pic_title = $row["title"];
	$cur_pic_file = $row["picfile"];
	$cur_pic_eseq = $row["eseq"];
	list($width, $height, $type, $attr) = getimagesize("../upload/" . $cur_pic_section . "/" . $cur_pic_file);
	
	// Load Jury List and Ratings
	$query = mysqli_query($DBCON, "SELECT * FROM assignment, user WHERE assignment.user_id = user.user_id AND section = '$cur_pic_section' ORDER BY jurynumber") or die(mysqli_error($DBCON));
	$ratingList = array();
	$juryPic = array();
	$totalrating = 0;
	for ($idx = 0; $assign = mysqli_fetch_array($query); $idx++) {
		$jury_id = $assign['user_id'];
		$juryPic[$idx] = $assign['avatar'];
		$qass = mysqli_query($DBCON, "SELECT * FROM rating WHERE pic_id = '$cur_pic_id' AND user_id = '$jury_id'");
		if ($qrow = mysqli_fetch_array($qass)) {
			$ratingList[$idx] = $qrow['rating'];
			$totalrating += $qrow['rating'];
		}
		else
			$ratingList[$idx] = "X";
	}
	$numRatings = $idx;

	// Load Next Picture
	$query = mysqli_query($DBCON, "SELECT * FROM pic WHERE section = '$cur_pic_section' AND pic_id != '$cur_pic_id' AND eseq >= '$cur_pic_eseq' ORDER BY eseq ASC LIMIT 1") or die(mysqli_error($DBCON));
	if (mysqli_num_rows($query) > 0) {
		$row = mysqli_fetch_array($query);
		$next_pic_id = $row['pic_id'];
		$next_pic_title = $row['title'];
		$next_pic_file = $row['picfile'];
	}
	else {
		$next_pic_id = 0;
		$next_pic_title = '';
		$next_pic_file = '';		
	}
?>

<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!-- <meta http-equiv="refresh" content="10"> -->

    <!-- Page title -->
    <title>Youth Photographic | Display</title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!--<link rel="shortcut icon" type="image/ico" href="favicon.ico" />-->

    <!-- Vendor styles -->
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="vendor/metisMenu/dist/metisMenu.css" />
    <link rel="stylesheet" href="vendor/animate.css/animate.css" />
    <link rel="stylesheet" href="vendor/bootstrap/dist/css/bootstrap.css" />
    <link rel="stylesheet" href="vendor/blueimp-gallery/css/blueimp-gallery.min.css" />
	<link rel="stylesheet" href="vendor/bootstrap-star-rating/css/star-rating.css" />

    <!-- App styles -->
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/helper.css" />
    <link rel="stylesheet" href="styles/style.css">

	
<style type="text/css">
body {
	height: 100%;
	background-color: black;
}
#wrapper {
	height: 100%;
	border: 0;
}

#menu {
	background-color: black;
	border: 0;
)
.containerBox {
    position: relative;
    display: inline-block;
}
.text-box {
    position: absolute;    

    text-align: center;    
    width: 100%;
}
.text-box:before {
   content: '';
   display: inline-block;
   height: 100%;
   vertical-align: middle;
}
h4 {
   display: inline-block;
   font-size: 18px; 
   color: #FFF;   
}
img {
  display: block;
  max-width: 100%;
  height: 100%;
}

img.centered-image {
	padding: 0; 
	border:0; 
	display: block;
	margin-left: auto;
	margin-right: auto;
}

</style>

</head>
<body>
	<!-- Header -->
	<!-- Navigation -->
	<aside id="menu" style="top:18px; width:220px;">
		<div id="navigation">
			<div class="container alert alert-info" style="width: 200px;">
			<!-- <div class="alert alert-info" style="padding: 12px;margin-bottom: 2px; text-align:center"> -->
				<!-- Picture Information -->
				<div class="row col-lg-12 col-md-12">
					<table class="table table-condensed">
						<tbody>
							<tr><td><h4 class="text-center" id="cur-pic-section"><?php echo $cur_pic_section; ?></h4></td></tr>
							<tr><td><h4 class="text-center strong" id="cur-pic-id"><?php echo $cur_pic_id;?></h4></td></tr>
							<tr><td><span class="nav-label" id="cur-pic-title"><?php echo $cur_pic_title; ?></span></td></tr>
						</tbody>
					</table>
				</div>
				<!-- Rating for current picture -->
				<div class="row col-lg-12 col-md-12">
					<table class="table table-condensed">
						<tbody>
			<?php
				// Display Rating List
				for ($idx =0; $idx < $numRatings; $idx++) {
			?>
							<tr>
								<td><img src="images/<?php echo $juryPic[$idx];?>" class="img-rounded" style="max-width: 80px;"></td>
								<td><p id="juryrating-<?php echo $idx;?>" class="text-right strong" style="font-size:3em;"><?php echo $ratingList[$idx]; ?></p></td>
							</tr>
			<?php
				}
			?>
							<tr>
								<td colspan="2"><p id="totalrating" class="text-right strong" style="font-size:3em;"><?php echo $totalrating; ?></p></td>
							</tr>
						</tbody>
					</table>
				</div>
				
				<!-- Display previous picture -->
				<div class="row col-md-12 col-lg-12">
					<table class="table table-condensed">
						<tbody>
							<tr><td colspan="2"><h4 class="text-left">Prev Pic</h4></td></tr>
							<tr>
								<td><p class="text-warning" id="prev-pic-title"></p></td>
								<td><p id="prev-pic-rating" class="text-right small" style="font-size:2em;"></p>
							</tr>
							<tr>
								<td colspan="2"><img id="prev-pic-file" class="img-thumbnail" style="width: 180px; max-height: 200px;" ></td>
							</tr>
						</tbody>
					</table>
				</div>
				
				<!-- Display next picture -->
				<div class="row col-md-12 col-lg-12">
					<table class="table table-condensed">
						<tbody>
							<tr><td><h4 class="text-left">Next Pic</h4></td></tr>
							<tr><td><p class="text-warning" id="next-pic-title"><?php echo $next_pic_title; ?></p></td></tr>
							<tr><td><img id="next-pic-file" src="/upload/<?php echo $cur_pic_section;?>/<?php echo $next_pic_file;?>" class="img-thumbnail" style="width: 180px; max-height: 200px;"></td></tr>
						</tbody>
					</table>
				</div>
			</div>
			<ul class="nav" id="side-menu">
				<li><a href="index.php" > <span class="nav-label"><i class="fa fa-refresh"></i><small> Logout </small></span></a></li>
			</ul>
		</div>
	</aside>

	<!-- Main Wrapper -->
	<div id="wrapper" style="background-color: black; margin-left:240px;">
		<div class="transition animated fadeIn" >
			<div class="hpanel" style="border: 0; ">
				<div class="panel-body" style="padding: 0; background-color: black; border:0; ">
					<img style="display: block; margin: 0 auto; max-height: 100%" id="cur-pic-file" 
							src="../upload/<?php echo $cur_pic_section . '/' . $cur_pic_file;?>">
				</div>
			</div>
		</div>
	</div>


<!-- Vendor scripts -->
<script src="vendor/jquery/dist/jquery.min.js"></script>
<script src="vendor/slimScroll/jquery.slimscroll.min.js"></script>
<script src="vendor/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="vendor/metisMenu/dist/metisMenu.min.js"></script>
<script src="vendor/iCheck/icheck.min.js"></script>
<script src="vendor/sparkline/index.js"></script>
<script src="vendor/jquery-ui/jquery-ui.min.js"></script>
<!-- App scripts -->
<script src="scripts/homer.js"></script>
<script src="vendor/sparkline/index.js"></script>
<script src="vendor/jquery-validation/jquery.validate.min.js"></script>

<script>
var tz;

// Javascript to refresh page
function refreshPage(data, status) {
	
	if(status == "success") {
		// Update Screen
		$("#cur-pic-section").html(data.section);
		$("#cur-pic-id").html(data.pic_id);
		$("#cur-pic-title").html(data.title);
		$("#cur-pic-file").attr("src", "../upload/" + data.section + "/" + data.pic);
		var aspect = data.height / data.width;
		if ($(window).height() < data.height) {
			data.height = $(window).height();
			data.width = Math.round(data.height / aspect);
		}
		if (($(window).width()-240) < data.width) {		// Accomodate rating display
			data.width = $(window).width()- 240;
			data.height = Math.round(data.width * aspect);
		}
		var mv = 0;
		var mh = 0;
		if (data.height < $(window).height())
			mv = Math.round(($(window).height() - data.height) / 2);
		if (data.width < ($(window).width()- 240))
			mh = Math.round(($(window).width() - 240 - data.width) / 2);
		var marginattr = "margin-left: " + mh + "px; margin-right: 0px; margin-top: " + mv + "px; margin-bottom: 0px;";
		$("#cur-pic-file").attr("style", "width: " + data.width + "px; height: " + data.height + "px; display: block; " + marginattr);

		for (i in data.juryrating)
			$("#juryrating-"+i).html("<big>" + data.juryrating[i] + "</big>");
		$("#totalrating").html("<big>" + data.totalrating + "</big>");

		// Update previous and next pictures
		if (data.has_pic_changed == "YES") {
			if (data.prev_pic_id != 0){
				$("#prev-pic-title").html(data.prev_pic_title);
				$("#prev-pic-file").attr("src", "../upload/" + data.section + "/" + data.prev_pic_file);
			}
			if (data.next_pic_id != 0) {
				$("#next-pic-title").html(data.next_pic_title);
				$("#next-pic-file").attr("src", "../upload/" + data.section + "/" + data.next_pic_file);
			}
		}

	}
	clearTimeout(tz);					// Clear existig timeout
	tz = setTimeout(loadCurPic, 2000);	// Refresh every 3 seconds
}

function loadCurPic() {
	$.post("op/display_refresh.php", "", refreshPage, "json");
}
</script>
<script>

    $(function(){
		tz = setTimeout(loadCurPic, 2000);	// Refresh every 5 seconds
    });
</script>


</body>

</html>
<?php
}
else
{
header("Location: home.php");
printf("<script>location.href='home.php'</script>");
}

?>