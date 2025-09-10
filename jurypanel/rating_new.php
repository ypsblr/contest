<?php 
session_start();
include("op/connect.php");
function encode_string_array ($stringArray) {
    $s = strtr(base64_encode(addslashes(gzcompress(serialize($stringArray),2))), '+/=', '_,');
    return $s;
}	

function decode_string_array ($stringArray) {
    $s = unserialize(gzuncompress(stripslashes(base64_decode(strtr($stringArray, '_,', '+/=')))));
    return $s;
}

if(isset($_SESSION['jury_id'])) {
	$session_id=$_SESSION['jury_id'];
	$user_query = mysqli_query($DBCON, "SELECT * FROM user WHERE user_id = '$session_id'")or die(mysqli_error($DBCON));
	$user_row = mysqli_fetch_array($user_query);
	$jury_login = $user_row['login'];
	$jury_name=$user_row['name'];
	$jury_pic=$user_row['avatar'];
	$user_type = $user_row['type'];
	
	// Load assignments
	$query = mysqli_query($DBCON, "SELECT * FROM assignment WHERE user_id = $session_id") or die(mysqli_error($DBCON));
	$assignment = array();
	while ($row = mysqli_fetch_array($query))
		$assignment[$row['section']] = $row['jurynumber'];
	
	// Load Current Picture and related details
	/***
	$my_jury_id = $session_id;
	$query = mysqli_query($DBCON, "SELECT pic.pic_id as pic_id, entry_id, title, section, picfile FROM ctl, pic WHERE pic.pic_id=ctl.pic_id") or die(mysqli_error($DBCON));
	$row = mysqli_fetch_array($query);
	$cur_pic_id = $row["pic_id"];
	$_SESSION['cur_pic_id'] = $cur_pic_id;		// set session variable for use by the ratings update PHP
	$cur_pic_uid = $row["entry_id"];			// Entry ID. Really not required
	$_SESSION['cur_pic_uid'] = $cur_pic_uid;	// Required for updating to the rating table
	$cur_pic_section = $row["section"];			// Section of this picture
	$_SESSION['cur_pic_section'] = $cur_pic_section;
	$cur_pic_title = $row["title"];
	$cur_pic_file = $row["picfile"];
	$cur_pic_myjurynumber = 0;
	$cur_pic_myrating = 0;
	$cur_pic_mytags = "";
	if (isset($assignment[$cur_pic_section])) {		// check if this is a section assigned to this jury
		$cur_pic_myjurynumber = $assignment[$cur_pic_section];
		// Load Jury's Rating for the current picture
		$query = mysqli_query($DBCON, "SELECT * FROM rating WHERE pic_id = $cur_pic_id AND user_id = $my_jury_id") or die(mysqli_error($DBCON));
		if ($row = mysqli_fetch_array($query)) {
			$cur_pic_myrating = $row["rating"];
			$cur_pic_mytags = $row["tags"];
		}
	}
	$_SESSION['cur_pic_myjurynumber'] = $cur_pic_myjurynumber;	// Set session variable
	$_SESSION['cur_pic_myrating'] = $cur_pic_myrating;
	$_SESSION['cur_pic_mytags'] = $cur_pic_mytags;
	
	list($width, $height, $type, $attr) = getimagesize("../upload/" . $cur_pic_section . "/" . $cur_pic_file);
	***/
	
?>

<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!-- <meta http-equiv="refresh" content="10"> -->

    <!-- Page title -->
    <title>Youth Photographic Society | Jury Panel</title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!--<link rel="shortcut icon" type="image/ico" href="favicon.ico" />-->

    <!-- Vendor styles -->
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.css" />
    <!-- <link rel="stylesheet" href="vendor/metisMenu/dist/metisMenu.css" /> -->
    <link rel="stylesheet" href="vendor/animate.css/animate.css" />
    <link rel="stylesheet" href="vendor/bootstrap/dist/css/bootstrap.css" />
    <!-- <link rel="stylesheet" href="vendor/blueimp-gallery/css/blueimp-gallery.min.css" /> -->
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
	margin: 0;
}

#menu {
	background-color: black;
}

#side-menu li:first-child {
	border: 0px;
}

#side-menu li {
	border: 0px;
}

#side-menu li a {
	margin: 0px;
	padding: 0px;
	background-color: black;
}

#side-menu.nav > li > a:hover, #side-menu.nav > li > a:focus {
	background-color: black;
}

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
   font-size: 18px; /*or whatever you want*/
   color: #FFF;   
}

img {
  display: block;
  max-width: 100%;
  height: auto;
}

.thumbnail {
	background-color: black;
	border-color: #202020;
}

.history-rating {
	font-family: "Arial", sans-serif;
	font-size: 1.5em;
	font-weight: bold;
	color: #a0a0a0;
}

.history-title {
	font-family: "Arial", sans-serif;
	font-size: 0.75em;
	font-weight: normal;
	color: #a0a0a0;
}

.history-thumbnail {
	width: 100%;
	background-color: black;
}
	

</style>

</head>
<body>
	<!--[if lt IE 7]>
	<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please upgrade your browser to improve your experience.</p>
	<![endif]-->

	<!-- Header -->
	
	<!-- Navigation -->
	
	<!-- Audio -->
	<!-- <embed src="/jurypanel/images/button-16.wav" autostart="false" width="0" height="0" id="click_sound" style="display: none;" enablejavascript="true"> -->

	<!-- Main Wrapper -->
	<div id="wrapper" style="background-color: black; border:0;" >
		<div class="content">
			<div class="row">	<!-- Message Row -->
				<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<img src="images/<?php echo $jury_pic; ?>" class="img-circle m-b" alt="YPS" style="margin:auto; width:30%;">
						</div>
					</div>
				</div>
				<div class="col-lg-9 col-md-9 col-sm-7 col-xs-7">
					<p class="text-muted font-extra-bold font-uppercase"><big><?php echo $jury_name;?></big></p>
					<br>
					<p id="errmsg">Hello <?php echo $jury_name; ?></p>
				</div>
				<div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
					<a href="logout.php" class="text-muted">EXIT</a>
				</div>
			</div>
			<!-- <div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<br><br>
				</div>
			</div> -->
			<div class="row">	<!-- Buttons 1, 2 & 3 -->
				<div class="col-lg-5 col-md-4 col-sm-3 col-xs-0"></div>
				<div class="panel col-lg-3 col-md-4 col-sm-6 col-xs-12" style="background-color: black;">
					<div class="panel-heading"><h3>Click on Rating</h3></div>
					<h2 id="indicator" style="font-weight: bold;"></h2>  
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"><img src="./images/sq4.png" onclick="update_rating(4)"></div>
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"><img src="./images/sq5.png" onclick="update_rating(5)"></div>
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"><img src="./images/sqtag.png" onclick="update_tag()"></div>
						</div>
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">&nbsp;</div>
						</div>
						<div class="row">
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"><img src="./images/sq1.png" onclick="update_rating(1)"></div>
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"><img src="./images/sq2.png" onclick="update_rating(2)"></div>
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"><img src="./images/sq3.png" onclick="update_rating(3)"></div>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-3 col-xs-0"></div>
			</div>
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<br><h4 class="text-muted">History of Your Ratings</h4><br>
				</div>
			</div>
			<div class="row">	<!-- History of ratings -->
				<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 containerBox">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-primary"><span id="rating-1" class="history-rating"></span></div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 thumbnail"><span id="thumbnail-1" class="history-thumbnail"></span></div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-primary"><span id="title-1" class="history-title"></span></div>
					</div>
				</div>
				<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 containerBox">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-primary"><span id="rating-2" class="history-rating"></span></div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 thumbnail"><span id="thumbnail-2" class="history-thumbnail"></span></div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-primary"><span id="title-2" class="history-title"></span></div>
					</div>
				</div>
				<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 containerBox">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-primary"><span id="rating-3" class="history-rating"></span></div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 thumbnail"><span id="thumbnail-3" class="history-thumbnail"></span></div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-primary"><span id="title-3" class="history-title"></span></div>
					</div>
				</div>
				<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 containerBox">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-primary"><span id="rating-4" class="history-rating"></span></div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 thumbnail"><span id="thumbnail-4" class="history-thumbnail"></span></div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-primary"><span id="title-4" class="history-title"></span></div>
					</div>
				</div>
				<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 containerBox">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-primary"><span id="rating-5" class="history-rating"></span></div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 thumbnail"><span id="thumbnail-5" class="history-thumbnail"></span></div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-primary"><span id="title-5" class="history-title"></span></div>
					</div>
				</div>
				<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 containerBox">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-primary"><span id="rating-6" class="history-rating"></span></div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 thumbnail"><span id="thumbnail-6" class="history-thumbnail"></span></div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-primary"><span id="title-6" class="history-title"></span></div>
					</div>
				</div>
			</div>		<!-- End of History of ratings -->
		</div>			<!-- End of content -->
	</div>
	
	<?php 
		// include("master/profile_modal.php");
	?>



<!-- Vendor scripts -->
<script src="vendor/jquery/dist/jquery.min.js"></script>
<script src="vendor/slimScroll/jquery.slimscroll.min.js"></script>
<script src="vendor/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="vendor/metisMenu/dist/metisMenu.min.js"></script>
<script src="vendor/iCheck/icheck.min.js"></script>
<script src="vendor/sparkline/index.js"></script>
<script src="vendor/blueimp-gallery/js/jquery.blueimp-gallery.min.js"></script>
<script src="vendor/jquery-ui/jquery-ui.min.js"></script>
<!-- App scripts -->
<script src="scripts/homer.js"></script>

<!-- Local style for demo purpose -->
<!--
<style>

    .lightBoxGallery {
        text-align: center;
    }

    .lightBoxGallery a {
        margin: 5px;
        display: inline-block;
    }

</style>
-->

<script src="vendor/sparkline/index.js"></script>
<!-- <script src="vendor/bootstrap-star-rating/js/star-rating.min.js"></script> -->



<!-- Local style for demo purpose - remove text shadow from oryginal plugin styles 
<style>
    .rating-container .rating-stars:before {
        text-shadow: none;
    }
</style>
-->
<script src="vendor/jquery-validation/jquery.validate.min.js"></script>

<!-- Local scripts to handle rating updates -->
<script>
// var click_sound;
/*
function PlaySound(soundObj) {
  var sound = document.getElementById(soundObj);
  sound.Play();
}
*/

// Javascript to refresh page
function updateRatingHistory(data, status) {
	if(status == "success") {
		// First handle errors
		if (data.status == "ERR" || data.status == "NAS") {		// In case of error, reload page to disable operations
			$("#errmsg").html(data.errmsg);
		}
		else {			// Update screen only if there is a change
			$("#msg_rate").html(data.errmsg);
			
			// Play Sound
			// PlaySound("click_sound");
			
			// Update Screen - History
			// Push all existing history records down
			$("#rating-6").html($("#rating-5").html());
			$("#thumbnail-6").html($("#thumbnail-5").html());
			$("#title-6").html($("#title-5").html());

			$("#rating-5").html($("#rating-4").html());
			$("#thumbnail-5").html($("#thumbnail-4").html());
			$("#title-5").html($("#title-4").html());

			$("#rating-4").html($("#rating-3").html());
			$("#thumbnail-4").html($("#thumbnail-3").html());
			$("#title-4").html($("#title-3").html());

			$("#rating-3").html($("#rating-2").html());
			$("#thumbnail-3").html($("#thumbnail-2").html());
			$("#title-3").html($("#title-2").html());

			$("#rating-2").html($("#rating-1").html());
			$("#thumbnail-2").html($("#thumbnail-1").html());
			$("#title-2").html($("#title-1").html());

			// Insert the latest rating on top
			$("#rating-1").html(data.cur_pic_rating + " " + data.cur_pic_tags);
			$("#thumbnail-1").html("<img src='../upload/" + data.cur_pic_section + "/tn/" + data.cur_pic_file + "' />");
			$("#title-1").html("[" + data.cur_pic_id +"] - " + data.cur_pic_title);
			$("#indicator").html("");
		}
	}
}

function update_rating(pic_rating) {
	$("#indicator").html("Updating");
	$.post("op/rating_update_new.php", {rating: pic_rating}, updateRatingHistory, "json");
}

function update_tag() {
	$("#indicator").html("Updating");
	$.post("op/rating_update_new.php", {tag: 'TAG'}, updateRatingHistory, "json");
}

</script>
<script>
	
	// Install Keyboard handler once the document is loaded
	$(document).ready(function(){
		$(document).keydown(function(e) {
			if(e.keyCode >= 49 && e.keyCode <= 53) 	// Handle press of 1-5 keys
				update_rating(e.keyCode - 48);
			else if (e.keyCode >= 97 && e.keyCode <= 101)	// Number 1-5 in numeric keypad
				update_rating(e.keyCode - 96);
			else if (e.keyCode == 84 || e.keyCode == 56 || e.keyCode == 106)				// Handle pressing of 't' or '8' or '*' key to TAG the picture
				update_tag();
		});
		// Load the Click Sound
		// click_sound = new Audio("/jurypanel/images/button-16.wav");
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