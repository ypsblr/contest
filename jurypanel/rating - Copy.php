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
	// Is there a message passed back?
	if (isset($_SESSION['msg_rate'])) {
		$msg_rate = $_SESSION['msg_rate'];
		$_SESSION['msg_rate'] = "";		// Clear session variable for future screens
	}
	else
		$msg_rate = "";
	$session_id=$_SESSION['jury_id'];
	$user_query = mysql_query("SELECT * FROM user WHERE user_id = '$session_id'")or die(mysql_error());
	$user_row = mysql_fetch_array($user_query);
	$jury_login = $user_row['login'];
	$jury_name=$user_row['name'];
	$jury_pic=$user_row['avatar'];
	$user_type = $user_row['type'];
	
	// Load assignments
	$query = mysql_query("SELECT * FROM assignment WHERE user_id = $session_id") or die(mysql_error());
	$assignment = array();
	while ($row = mysql_fetch_array($query))
		$assignment[$row['section']] = $row['jurynumber'];
	
	// Load Current Picture and related details
	$my_jury_id = $session_id;
	$query = mysql_query("SELECT pic.pic_id as pic_id, entry_id, title, section, picfile FROM ctl, pic WHERE pic.pic_id=ctl.pic_id") or die(mysql_error());
	$row = mysql_fetch_array($query);
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
		$query = mysql_query("SELECT * FROM rating WHERE pic_id = $cur_pic_id AND user_id = $my_jury_id") or die(mysql_error());
		if ($row = mysql_fetch_array($query)) {
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
			<div class="alert alert-info" style="padding: 12px;margin-bottom: 2px; text-align:center">
				<p><span class="nav-label" id="cur-pic-section"><?php echo $cur_pic_section; ?></span></p>
				<hr>
				<h4><b><span id="cur-pic-id"><?php printf("%05d", $cur_pic_id);?></span></b></h4>
				<p><span class="nav-label" id="cur-pic-title"><?php echo $cur_pic_title; ?></span></p>
			<?php
				if ($cur_pic_myjurynumber == 0) {
			?>
				<hr>
				<p><span class="nav-label">You are not assigned to this section</span></p>
			<?php
				}
				if ($msg_rate != "") {
			?>
				<hr>
				<p><span class="nav-label"><?php echo $msg_rate;?></span></p>
			<?php
				}
			?>
				<hr>
			</div>
			<ul class="nav" id="side-menu" >
			<?php
				// Load Rating buttons only if I am assigned to this section
				if($cur_pic_myjurynumber != 0) {
					$border = "border: 4px dotted white;";
			?>
				<li>
					<a href="op/new_image_rating.php?rating=<?php echo encode_string_array("1");?>" style="background-color:#ED2C2D; color:#fff; <?php if($cur_pic_myrating == 1) echo $border; ?>" id="rating-1"><span class="nav-label"><i class="fa fa-star"></i> Rate -1</a></span>
				</li>
				<li>
					<a href="op/new_image_rating.php?rating=<?php echo encode_string_array("2");?>" style="background-color:#8f8e95; color:#fff; <?php if ($cur_pic_myrating == 2) echo $border; ?>" id="rating-2"><span class="nav-label"><i class="fa fa-star"></i><i class="fa fa-star"></i> Rate -2</a></span>
				</li>
				<li>
					<a href="op/new_image_rating.php?rating=<?php echo encode_string_array("3");?>" style="background-color:#FEBF00; color:#fff; <?php if ($cur_pic_myrating == 3) echo $border; ?>" id="rating-3"><span class="nav-label"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i> Rate -3</a></span>
				</li>
				<li>
					<a href="op/new_image_rating.php?rating=<?php echo encode_string_array("4");?>" style="background-color:#91CF50; color:#fff; <?php if ($cur_pic_myrating == 4) echo $border; ?>" id="rating-4"><span class="nav-label"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i> Rate -4</a></span>
				</li>
				<li>
					<a href="op/new_image_rating.php?rating=<?php echo encode_string_array("5");?>"  style="background-color:#00AFF0; color:#fff; <?php if ($cur_pic_myrating == 5) echo $border; ?>" id="rating-5"><span class="nav-label"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i> Rate -5</a></span>
				</li>
			<?php
					if ($cur_pic_myrating > 0) {
						if ($cur_pic_mytags == "") {
			?>
				<li>
					<a href="op/new_image_rating.php?tag=<?php echo encode_string_array("TAG");?>"  style="background-color:#8B008B; color:#fff; <?php if ($cur_pic_mytags != "") echo $border; ?>" id="rating-tag"><span class="nav-label"><i class="fa fa-tag"></i> TAG PICTURE</a></span>
				</li>
			<?php
						}
						else {
			?>
				<li>
					<a href="op/new_image_rating.php?tag=<?php echo encode_string_array("UNTAG");?>"  style="background-color:#8B008B; color:#fff; <?php if ($cur_pic_mytags != "") echo $border; ?>" id="rating-tag"><span class="nav-label"><i class="fa fa-times"></i> REMOVE TAG</a></span>
				</li>
			<?php
						}
					}
				}
			?>
				<li>
					<a href="rating.php" > <span class="nav-label"><i class="fa fa-refresh"></i> Load Picture </span></a>
				</li>
			</ul>
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
		<div class="normalheader transition animated fadeIn" style="padding: 0px 40px 0px 40px;">
			<div class="hpanel" style="border: 0;">
				<div class="panel-body" style="padding: 10px 10px; background-color: black; border:0;">
					<img class="img-responsive slide-content" style="max-height: 400px;" id="cur-pic-file" src="../upload/<?php echo $cur_pic_section;?>/<?php echo $cur_pic_file;?>">
				</div>
			</div>
		</div>
	</div>
	<?php 
		include("master/profile_modal.php");
	?>

</div>

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



<!-- Local styel for demo purpose - remove text shadow from oryginal plugin styles -->
<style>
    .rating-container .rating-stars:before {
        text-shadow: none;
    }
</style>
<script src="vendor/jquery-validation/jquery.validate.min.js"></script>
<script>
var tz;
// Javascript to refresh page
function refreshPage(data, status) {
	// function loadCurPic();	// Declaration for setTimeout
	if(status == "success") {
		// var ret = JSON.parse(data);	// data is already a parsed object
		// First handle errors
		if (data.status == "ERR" || data.status == "NAS") {		// In case of error, reload page to disable operations
			location.reload();
		}
		else {
			// Handle reload
			if ( ! $("#rating-1").length)	// if the rating buttons are not loaded by PHP, reload
				location.reload();
				
			// Update Screen
			$("#cur-pic-section").html(data.cur_pic_section);
			$("#cur-pic-id").html(data.cur_pic_id);
			$("#cur-pic-title").html(data.cur_pic_title);
			$("#cur-pic-file").attr("src", "../upload/" + data.cur_pic_section + "/" + data.cur_pic_file);
			if (data.cur_pic_rating == 1)
				$("#rating-1").attr("style", "background-color:#ED2C2D; color:#fff; border: 4px dotted white;");
			else
				$("#rating-1").attr("style", "background-color:#ED2C2D; color:#fff;");
			if (data.cur_pic_rating == 2)
				$("#rating-2").attr("style", "background-color:#8f8e95; color:#fff; border: 4px dotted white;");
			else
				$("#rating-2").attr("style", "background-color:#8f8e95; color:#fff;");
			if (data.cur_pic_rating == 3)
				$("#rating-3").attr("style", "background-color:#FEBF00; color:#fff; border: 4px dotted white;");
			else
				$("#rating-3").attr("style", "background-color:#FEBF00; color:#fff;");
			if (data.cur_pic_rating == 4)
				$("#rating-4").attr("style", "background-color:#91CF50; color:#fff; border: 4px dotted white;");
			else
				$("#rating-4").attr("style", "background-color:#91CF50; color:#fff;");
			if (data.cur_pic_rating == 5)
				$("#rating-5").attr("style", "background-color:#00AFF0; color:#fff; border: 4px dotted white;");
			else
				$("#rating-5").attr("style", "background-color:#00AFF0; color:#fff;");	
			if (data.cur_pic_tags == "TAGGED")
				$("#rating-tag").attr("style", "background-color:#8B008B; color:#fff; border: 4px dotted white;");
			else
				$("#rating-tag").attr("style", "background-color:#8B008B; color:#fff;");	
		}
	}

	tz = setTimeout(loadCurPic, 2000);	// Refresh every 5 seconds
}

function loadCurPic() {
	$.post("op/rating_refresh.php", "", refreshPage, "json");
}


</script>
<script>

    $(function () {
        // initialize with defaults
        $("#input-1").rating();
		
		
    });

</script>
<script>

    $(function(){
		tz = setTimeout(loadCurPic, 2000);	// Refresh every 5 seconds
		
        $("#form").validate({
            rules: {
                password: {
                    required: true,
                    minlength: 3
                },
                url: {
                    required: true,
                    url: true
                },
                number: {
                    required: true,
                    number: true
                },
                max: {
                    required: true,
                    maxlength: 4
                }
            },
            submitHandler: function(form) {
                form.submit();
            }
        });

        $("#form_2").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3
                },
                username: {
                    required: true,
                    minlength: 5
                },
                url: {
                    required: true,
                    url: true
                },
                number: {
                    required: true,
                    number: true
                },
                last_name: {
                    required: true,
                    minlength: 6
                }
            },
            messages: {
                number: {
                    required: "(Please enter your phone number)",
                    number: "(Please enter valid phone number)"
                },
                last_name: {
                    required: "This is custom message for required",
                    minlength: "This is custom message for min length"
                }
            },
            submitHandler: function(form) {
                form.submit();
            },
            errorPlacement: function(error, element) {
                $( element )
                        .closest( "form" )
                        .find( "label[for='" + element.attr( "id" ) + "']" )
                        .append( error );
            },
            errorElement: "span",
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