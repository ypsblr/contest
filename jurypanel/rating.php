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
	
?>

<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!-- <meta http-equiv="refresh" content="10"> -->

    <!-- Page title -->
    <title>Youth Photographic | Jury Panel</title>

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

.rating-button {
	margin: 0px;
	padding: 0px;
	background-color: black;
}

</style>

</head>
<body>
	<!--[if lt IE 7]>
	<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
	<![endif]-->

	<!-- Header -->
	<!-- Navigation -->
	<aside id="menu" style="top:18px">
		<div id="navigation">
		    <div class="profile-picture" id="jury" style="padding:5px; ">
				<a href="home.php">
					<img src="images/<?php echo $jury_pic; ?>" class="img-circle m-b" alt="logo" style="margin:auto; width:50%;">
				</a>
				<div class="stats-label text-color">
					<div class="dropdown">
						<a class="dropdown-toggle" href="#" data-toggle="dropdown">
							<small class="text-muted font-extra-bold font-uppercase"><?php echo $jury_name;?><b class="caret"></b></small>
						</a>
						<ul class="dropdown-menu animated flipInX m-t-xs">
							<li><a href="#" id="sidebar" class="right-sidebar-toggle">Profile</a></li>
							<li class="divider"></li>
							<li><a href="index.php">Logout</a></li>
						</ul>
					</div>
					<a class=" font-extra-bold" href="index.php">Logout</a>
				</div>
			</div>
			<?php
				// Load Rating buttons only if I am assigned to this section
				if($cur_pic_myjurynumber != 0) {
					$border = "border: 4px dotted white;";
			?>
			<ul class="nav" id="side-menu" style="margin: 0; padding: 0; spacing:0;">
				<li>
					<a href="#" id="rating-1" class="rating-button" onclick="update_rating(1)"><img src="./images/<?php echo ($cur_pic_myrating == 1) ? '1P.png' : '1U.png'; ?>"></a>
				</li>
				<li>
					<a href="#" id="rating-2" class="rating-button" onclick="update_rating(2)"><img src="./images/<?php echo ($cur_pic_myrating == 2) ? '2P.png' : '2U.png'; ?>"></a>
				</li>
				<li>
					<a href="#" id="rating-3" class="rating-button" onclick="update_rating(3)"><img src="./images/<?php echo ($cur_pic_myrating == 3) ? '3P.png' : '3U.png'; ?>"></a>
				</li>
				<li>
					<a href="#" id="rating-4" class="rating-button" onclick="update_rating(4)"><img src="./images/<?php echo ($cur_pic_myrating == 4) ? '4P.png' : '4U.png'; ?>"></a>
				</li>
				<li>
					<a href="#" id="rating-5" class="rating-button" onclick="update_rating(5)"><img src="./images/<?php echo ($cur_pic_myrating == 5) ? '5P.png' : '5U.png'; ?>"></a>
				</li>
				<!-- Show tags only when there is a rating -->
			<?php
					if ($cur_pic_myrating != 0) {
			?>
				<li>
			<?php
						if ($cur_pic_mytags == "") {
			?>
					<a href="#" id="rating-tag" class="rating-button" onclick="update_tag('TAG')"><img src="./images/tag.png"></a>
			<?php
						} else {
			?>
					<a href="#" id="rating-tag" class="rating-button" onclick="update_tag('UNTAG')"><img src="./images/untag.png"></a>
			<?php
						}
			?>
				</li>
			<?php
					}
			?>
				<li>
					<a href="rating.php" style="padding: 10px;">&nbsp;&nbsp;<span class="nav-label"><i class="fa fa-refresh"></i> Load Picture </span></a>
				</li>
			</ul>
			<?php
				}
			?>
			<ul class="nav" id="side-menu-1">
				<li>
					<a href="home.php"> <span class="nav-label"><i class="fa fa-forward"></i> Go Back </span></a>
				</li>
				<!-- <li>
					<a href="javascript:void(0)"> <span class="nav-label">Remaining Img:  <?php //echo $_SESSION['total_remaining_img'];?></span></a>
				</li> -->
			</ul>
		</div>
	</aside>

	<!-- Main Wrapper -->
	<div id="wrapper" style="background-color: black; border:0;">
		<div style="padding: 12px;margin-bottom: 2px;">
			<h4 class="text-muted"><span class="nav-label" id="cur-pic-section"><?php echo $cur_pic_section; ?></span></h4>
			<br>
			<h5 class="text-muted"><b><span id="cur-pic-id"><?php printf("[%d] - %s", $cur_pic_id, $cur_pic_title);?></span></b></h5>
		<?php
			if ($cur_pic_myjurynumber == 0) {
		?>
			<br>
			<p><span class="text-warning">You are not assigned to this section</span></p>
		<?php
			}
		?>
			<br>
			<p><span class="nav-label" id="msg_rate"><?php echo $_SESSION['msg_rate'];?></span></p>
			<br>
		</div>
		<div class="normalheader transition animated fadeIn" style="padding: 0px 40px 0px 40px;">
			<div class="hpanel" style="border: 0;">
				<div class="panel-body" style="padding: 10px 10px; background-color: black; border:0;">
					<img class="img-responsive slide-content" style="max-height: 300px;" id="cur-pic-file" src="../upload/<?php echo $cur_pic_section;?>/tn/<?php echo $cur_pic_file;?>">
				</div>
			</div>
		</div>
	</div>
	<?php 
		include("master/profile_modal.php");
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
<style>

    .lightBoxGallery {
        text-align: center;
    }

    .lightBoxGallery a {
        margin: 5px;
        display: inline-block;
    }

</style>

<script src="vendor/sparkline/index.js"></script>
<script src="vendor/bootstrap-star-rating/js/star-rating.min.js"></script>



<!-- Local style for demo purpose - remove text shadow from oryginal plugin styles -->
<style>
    .rating-container .rating-stars:before {
        text-shadow: none;
    }
</style>
<script src="vendor/jquery-validation/jquery.validate.min.js"></script>

<!-- Local scripts to handle rating updates -->
<script>
var tz;
// Javascript to refresh page
function refreshPage(data, status) {
	// function loadCurPic();	// Declaration for setTimeout
	if(status == "success") {
		// var ret = JSON.parse(data);	// data is already a parsed object
		// First handle errors
		if (data.status == "ERR" || data.status == "NAS") {		// In case of error, reload page to disable operations
			$("#msg_rate").html(data.errmsg);
			// location.reload();
		}
		else if (data.status != "NOC") {			// Update screen only if there is a change
			$("#msg_rate").html(data.errmsg);
			// Handle reload
			if ( ! $("#rating-1").length)	// if the rating buttons are not loaded by PHP, reload
				location.reload();
				
			// Update Screen
			$("#cur-pic-section").html(data.cur_pic_section);
			$("#cur-pic-id").html("[" + data.cur_pic_id +"] - " + data.cur_pic_title);
			$("#cur-pic-file").attr("src", "../upload/" + data.cur_pic_section + "/tn/" + data.cur_pic_file);
			if (data.cur_pic_rating == 1)
				$("#rating-1").html("<img src='./images/1P.png'>");
			else
				$("#rating-1").html("<img src='./images/1U.png'>");
			if (data.cur_pic_rating == 2)
				$("#rating-2").html("<img src='./images/2P.png'>");
			else
				$("#rating-2").html("<img src='./images/2U.png'>");
			if (data.cur_pic_rating == 3)
				$("#rating-3").html("<img src='./images/3P.png'>");
			else
				$("#rating-3").html("<img src='./images/3U.png'>");
			if (data.cur_pic_rating == 4)
				$("#rating-4").html("<img src='./images/4P.png'>");
			else
				$("#rating-4").html("<img src='./images/4U.png'>");
			if (data.cur_pic_rating == 5)
				$("#rating-5").html("<img src='./images/5P.png'>");
			else
				$("#rating-5").html("<img src='./images/5U.png'>");
			if (data.cur_pic_tags == "TAGGED") {
				$("#rating-tag").attr("onclick", "update_tag('UNTAG')"); 
				$("#rating-tag").html("<img src='./images/untag.png'>");
			}
			else {
				$("#rating-tag").attr("onclick", "update_tag('TAG')");
				$("#rating-tag").html("<img src='./images/tag.png'>");
			}
		}
	}

	tz = setTimeout(loadCurPic, 2000);	// Refresh every 5 seconds
}

function loadCurPic() {
	$.post("op/rating_refresh.php", "", refreshPage, "json");
}

function refreshRating(data, status) {
	if(status == "success") {
		// var ret = JSON.parse(data);	// data is already a parsed object
		// First handle errors
		if (data.status == "ERR") {		// In case of error, reload page to disable operations
			$("#msg_rate").html(data.errmsg);
			// location.reload();
		}
		else {
			$("#msg_rate").html(data.errmsg);
			// Handle reload
			if ( ! $("#rating-1").length)	// if the rating buttons are not loaded by PHP, reload
				location.reload();
				
			// Update Screen
			if (data.rating == 1)
				$("#rating-1").html("<img src='./images/1P.png'>");
			else
				$("#rating-1").html("<img src='./images/1U.png'>");
			if (data.rating == 2)
				$("#rating-2").html("<img src='./images/2P.png'>");
			else
				$("#rating-2").html("<img src='./images/2U.png'>");
			if (data.rating == 3)
				$("#rating-3").html("<img src='./images/3P.png'>");
			else
				$("#rating-3").html("<img src='./images/3U.png'>");
			if (data.rating == 4)
				$("#rating-4").html("<img src='./images/4P.png'>");
			else
				$("#rating-4").html("<img src='./images/4U.png'>");
			if (data.rating == 5)
				$("#rating-5").html("<img src='./images/5P.png'>");
			else
				$("#rating-5").html("<img src='./images/5U.png'>");
		}
	}

	tz = setTimeout(loadCurPic, 2000);	// Refresh every 5 seconds
	
}

function update_rating(pic_rating) {
	clearTimeout(tz);	// Stop any Timeout in progress
	$.post("op/new_image_rating.php", {rating: pic_rating}, refreshRating, "json");
}

function refreshTag(data, status) {
	if(status == "success") {
		// var ret = JSON.parse(data);	// data is already a parsed object
		// First handle errors
		if (data.status == "ERR") {		// In case of error, reload page to disable operations
			$("#msg_rate").html(data.errmsg);
			// location.reload();
		}
		else {
			$("#msg_rate").html(data.errmsg);
			// Handle reload
			if ( ! $("#rating-1").length)	// if the rating buttons are not loaded by PHP, reload
				location.reload();
				
			// Update Screen
			if (data.tag == "TAGGED") {
				$("#rating-tag").attr("onclick", "update_tag('UNTAG')");
				$("#rating-tag").html("<img src='./images/untag.png'>");
			}
			else {
				$("#rating-tag").attr("onclick", "update_tag('TAG')");
				$("#rating-tag").html("<img src='./images/tag.png'>");
			}

		}
	}

	tz = setTimeout(loadCurPic, 2000);	// Refresh every 5 seconds
	
}

function update_tag(pic_tagop) {
	clearTimeout(tz);	// Stop any Timeout in progress
	$.post("op/new_image_rating.php", {tag: pic_tagop}, refreshTag, "json");
}

</script>
<script>
	// Initiate periodic update on load
    $(function(){
		tz = setTimeout(loadCurPic, 2000);	// Refresh every 5 seconds
    });
	
	// Install Kyboard handler once the document is loaded
	$(document).ready(function(){
		$(document).keydown(function(e) {
			if(e.keyCode >= 49 && e.keyCode <= 53) 	// Handle press of 1-5 keys
				update_rating(e.keyCode - 48);
			else if (e.keyCode == 84)				// Handle pressing of 't' key to TAG the picture
				update_tag('TAG');
			else if (e.keyCode == 88)				// Handle pressing of 'x' key to UNTAG the picture
				update_tag('UNTAG');
		});
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