<?php 
session_start();
include("op/connect.php");

if(isset($_SESSION['jury_id'])) {  // to prevent being run from command line and to facilitate session variables

	// Load Current Picture and related details
	$query = mysql_query("SELECT * FROM ctl, pic WHERE pic.pic_id=ctl.pic_id") or die(mysql_error());
	$row = mysql_fetch_array($query);
	$cur_pic_id = $row["pic_id"];
	$cur_pic_uid = $row["entry_id"];		// User ID. Really not required
	$cur_pic_section = $row["section"];	// Section of this picture
	$cur_pic_title = $row["title"];
	$cur_pic_file = $row["picfile"];
	list($width, $height, $type, $attr) = getimagesize("../upload/" . $cur_pic_section . "/" . $cur_pic_file);
	
	// Load Rating List
	$query = mysql_query("SELECT * FROM assignment WHERE section = '$cur_pic_section' ORDER BY jurynumber") or die(mysql_error());
	$ratingList = array();
	$totalrating = 0;
	for ($idx = 0; $assign = mysql_fetch_array($query); $idx++) {
		$jury_id = $assign['user_id'];
		$qass = mysql_query("SELECT * FROM rating WHERE pic_id = '$cur_pic_id' AND user_id = '$jury_id'");
		if ($qrow = mysql_fetch_array($qass)) {
			$ratingList[$idx] = $qrow['rating'];
			$totalrating += $qrow['rating'];
		}
		else
			$ratingList[$idx] = "X";
	}
	$numRatings = $idx;

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
	<aside id="menu" style="top:18px; width:100px;">
		<div id="navigation">
			<div class="alert alert-info" style="padding: 12px;margin-bottom: 2px; text-align:center">
				<p><span class="nav-label" id="cur-pic-section"><?php echo $cur_pic_section; ?></span></p>
				<hr>
				<h3><b><span id="cur-pic-id"><?php echo $cur_pic_id;?></span></b></h3>
				<p><span class="nav-label" id="cur-pic-title"><?php echo $cur_pic_title; ?></span></p>
				<hr>
			<?php
				// Display Rating List
				for ($idx =0; $idx < $numRatings; $idx++) {
			?>
				<p><big><h1 id="juryrating-<?php echo $idx;?>" style="text-align: center;"><?php echo $ratingList[$idx]; ?></h1></big></p>
			<?php
				}
			?>
				<hr>
				<p><big><h1 id="totalrating" style="text-align: center; font-weight: bold;"><?php echo $totalrating; ?></h1></big></p>
				<hr>
			</div>
			<ul class="nav" id="side-menu">
				<li><a href="index.php" > <span class="nav-label"><i class="fa fa-refresh"></i><small> Logout </small></span></a></li>
			</ul>
		</div>
	</aside>

	<!-- Main Wrapper -->
	<div id="wrapper" style="background-color: black; margin-left:120px;">
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
	// function loadCurPic();	// Declaration for setTimeout
	if(status == "success") {
		// var ret = JSON.parse(data);	// data is already a parsed object
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
		if (($(window).width()-120) < data.width) {		// Accomodate rating display
			data.width = $(window).width()-120;
			data.height = Math.round(data.width * aspect);
		}
		var mv = 0;
		var mh = 0;
		if (data.height < $(window).height())
			mv = Math.round(($(window).height() - data.height) / 2);
		if (data.width < ($(window).width()-120))
			mh = Math.round(($(window).width() - 120 - data.width) / 2);
		var marginattr = "margin-left: " + mh + "px; margin-right: 0px; margin-top: " + mv + "px; margin-bottom: 0px;";
		// var marginattr = "margin: 0 auto;";
		$("#cur-pic-file").attr("style", "width: " + data.width + "px; height: " + data.height + "px; display: block; " + marginattr);
		// $("#cur-pic-file").attr("style", "width: " + data.width + "px; display: block; margin: 0 auto; max-height: 100%");
		for (i in data.juryrating)
			$("#juryrating-"+i).html("<big>" + data.juryrating[i] + "</big>");
		$("#totalrating").html("<big>" + data.totalrating + "</big>");
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