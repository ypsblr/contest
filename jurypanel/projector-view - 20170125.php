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
    
if(isset($_SESSION['jury_id']) && $_SESSION['jury_type']=="PROJECTOR")
{

    $sections =$_REQUEST['sections'];
    $sections=str_replace(" ", "", $sections);   
    $sections=decode_string_array($sections);
	
	// Generate a List of Sections
	$sql = mysql_query("SELECT * FROM section") or die(mysql_error());
	$sectionList = array();
	for ($idx = 0; $row = mysql_fetch_array($sql); ++ $idx)
		$sectionList[$idx] = $row['section'];
	$numSections = $idx;

	$sql = mysql_query("SELECT * FROM section, assignment WHERE section.section = assignment.section AND section.section='$sections'") or die(mysql_error());
	
	$juryList = array();
	for ($idx = 0; $row = mysql_fetch_array($sql); $idx++)
		$juryList[$idx] = $row["user_id"];
	$numJuries = $idx;
	
	// Prepare Temp table
	mysql_query("TRUNCATE TABLE temp ") or die(mysql_error());
	$sql = " INSERT INTO temp (pic_id, rating_count) SELECT pic_id, COUNT(*) AS rc FROM rating GROUP BY pic_id HAVING rc = '$numJuries' ";
	mysql_query($sql) or die(mysql_error());
		
	// Get number of pictures left to be scored
	$qck = mysql_query("SELECT COUNT(*) AS not_rated FROM pic WHERE section='$sections' AND pic_id NOT IN (SELECT pic_id FROM temp)") or die(mysql_error());
	$row = mysql_fetch_array($qck);
	$pics_left = $row["not_rated"];
	
	// All filtering queries are prepared here
	// All queries filter by section by default
	// Case 1 - 'next' filter is set with a picture id
	if (isset($_REQUEST['next'])) {
		$next = $_REQUEST['next'];	// Start display of 28 pictures from 
		$query = "SELECT pic_id, entry.entry_id as entry_id, section, title, picfile, eseq ";
		$query .= " FROM pic, entry ";
		$query .= " WHERE pic.entry_id = entry.entry_id AND ";
		$query .= "       pic.section='$sections' AND ";
		$query .= " 	  pic.pic_id>='$next' ";
		$query .= " ORDER BY eseq ASC ";
	}
	// Case 2 - 'title' filter
	if (isset($_REQUEST['title'])) {
		$title = $_REQUEST['title'];
		$query = "SELECT pic_id, entry.entry_id as entry_id, section, title, picfile, eseq ";
		$query .= " FROM pic, entry ";
		$query .= " WHERE pic.entry_id = entry.entry_id AND ";
		$query .= "       pic.section='$sections' AND ";
		$query .= " 	  pics.title LIKE '%$title%' ";
		$query .= " ORDER BY eseq ASC ";
	}
	// Case 3 - 'score' filter
	if (isset($_REQUEST['score'])) {
		$score = $_REQUEST['score'];
		if ($score == "inconsistent") {
			// Find pictures with difference of more than 2 marks between minimum and maximum
			$query = "SELECT pic.pic_id as pic_id, entry.entry_id as entry_id, section, title, picfile, eseq, MIN(rate) AS min_score, MAX(rate) AS max_score ";
			$query .= " FROM pic, entry, rating ";
			$query .= " WHERE pic.entry_id = entry.entry_id AND ";
			$query .= "       pic.pic_id = rating.pic_id AND ";
			$query .= "       pic.section='$sections' ";
			$query .= " GROUP BY pic_id ";
			$query .= " HAVING (max_score - min_score) > 2 ";
			$query .= " ORDER BY eseq ASC ";
		}
		else {
			// Find pictures with a total score equal to the score entered
			$score = trim($score);
			$query = "SELECT pic.pic_id as pic_id, entry.entry_id as entry_id, section, title, picfile, eseq, SUM(rate) AS total_score ";
			$query .= " FROM pic, entry, rating ";
			$query .= " WHERE pic.entry_id = entry.entry_id AND ";
			$query .= "       pic.pic_id = rating.pic_id AND ";
			$query .= "       pic.section='$sections' ";
			$query .= " GROUP BY pic_id ";
			if (substr($score, -1) == "+") {
				$score = substr($score, 0, strlen($score) - 1);
				$query .= " HAVING total_score >= $score ";
			}
			else
				$query .= " HAVING total_score = $score ";
			$query .= " ORDER BY eseq ASC ";
		}
	}
	
	// Case 4 - Unscored
	if (isset($_REQUEST['unscored'])) {
		$query = "SELECT pic.pic_id as pic_id, entry.entry_id as entry_id, section, title, picfile, eseq ";
		$query .= " FROM pic, entry ";
		$query .= " WHERE pic.entry_id = entry.entry_id AND ";
		$query .= "       pic.section='$sections' AND ";
		$query .= "       pic.pic_id NOT IN (SELECT pic_id FROM temp ) ";
	}
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title>Youth Photographic Society | Projection Panel</title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!--<link rel="shortcut icon" type="image/ico" href="favicon.ico" />-->

    <!-- Vendor styles -->
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="vendor/metisMenu/dist/metisMenu.css" />
    <link rel="stylesheet" href="vendor/animate.css/animate.css" />
    <link rel="stylesheet" href="vendor/bootstrap/dist/css/bootstrap.css" />
    <link rel="stylesheet" href="vendor/blueimp-gallery/css/blueimp-gallery.min.css" />

    <!-- App styles -->
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/helper.css" />
    <link rel="stylesheet" href="styles/style.css">

	
<style type="text/css">
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

.blueimp-gallery > .description {
	position: fixed;
	bottom: 0px;
	right: 0px;
	border: 2px solid #a0a0a0;
	padding: 2px;
	color: #fff;
	display: none;
}
.blueimp-gallery-controls > .description {
	display: block;
}

</style>


</head>
<body class="fixed-navbar fixed-sidebar">

	<!-- Simple splash screen-->
	<div class="splash">
		<div class="color-line"></div>
		<div class="splash-title">
			<h1>    YOUTH PHOTOGRAPHIC SOCIETY | PROJECTION PANEL  </h1>
			<p>Please Wait. </p>
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
		<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
	<![endif]-->

	<!-- Header -->
	<!-- Header -->
	<div id="header">
		<div class="color-line"></div>
		<div id="logo" class="light-version">
			<span>Youth Photographic Society</span>
		</div>
		<nav role="navigation">
			<div class="header-link hide-menu"><i class="fa fa-bars"></i></div>
			<div class="small-logo"><span class="text-primary">POJECTION APP</span></div>
			<form role="search" class="navbar-form-custom" method="post" action="#">
				<div class="form-group">
					<input type="text" placeholder="Search something special" class="form-control" name="search">
				</div>
			</form>
			<div class="mobile-menu">
				<button type="button" class="navbar-toggle mobile-menu-toggle" data-toggle="collapse" data-target="#mobile-collapse">
					<i class="fa fa-edit"></i>
				</button>
				<div class="collapse mobile-navbar" id="mobile-collapse">
					<ul class="nav navbar-nav">
						<li><a class="" href="index.php">Logout</a></li>
						<li><a data-toggle="modal" data-target="#myModal">Profile</a></li>
					</ul>
				</div>
			</div>
		</nav>
	</div>
	<!-- Navigation -->
	<aside id="menu">
		<div id="navigation">
			<div class="profile-picture">
				<a href="#"><img src="images/user.jpg" class="img-circle m-b" alt="logo"></a>
				<div class="stats-label text-color">
					<span class="font-extra-bold font-uppercase">Projection Master</span>
					<div class="dropdown">
						<a class="dropdown-toggle" href="#" data-toggle="dropdown">
							<small class="text-muted">Projection Master <b class="caret"></b></small>
						</a>
						<ul class="dropdown-menu animated flipInX m-t-xs">
							<li><a href="#" id="sidebar" class="right-sidebar-toggle">Profile</a></li>
							<li class="divider"></li>
							<li><a href="index.php">Logout</a></li>
						</ul>
					</div>
					<div id="sparkline1" class="small-chart m-t-sm"></div>
					<br>
				<?php
					if ($pics_left > 0) {
				?>
					<a href="projector-view.php?unscored=y&sections=<?php echo encode_string_array($sections);?>">
						<span class="font-extra-bold font-uppercase"><?php echo $pics_left; ?> Left</span>
					</a>
				<?php
					}
					else {
				?>
					<span class="font-extra-bold font-uppercase">All Scored</span>
				<?php
					}
				?>
				</div>
			</div>
			<ul class="nav" id="side-menu">
				<li class="active">
					<a href="projector_home.php"> <span class="nav-label"><i class="fa fa-home"></i> Dashboard </span></a>
				</li>            
				<li>
					<a href="#"><span class="nav-label"><i class="fa fa-mortar-board"></i> Projector</span><span class="fa arrow"></span> </a>
					<ul class="nav nav-second-level">
					<?php
						for ($idx = 0; $idx < $numSections; ++ $idx) {
					?>
						<li><a href="projector-view.php?sections=<?php echo encode_string_array("$sectionList[$idx]");?> &next=0"><?php echo $sectionList[$idx];?></a></li>
					<?php
						}
					?>
					</ul>
				</li>
			</ul>
		</div>
	</aside>
	<!-- Main Wrapper -->
	<div id="wrapper">
		<div class="content animate-panel">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12">
            		<div class="row">
						<div class="col-lg-4 col-sm-4 col-md-4">
							<form method="post" action="projector-view.php">
								<input type="hidden" name="sections" value="<?php echo encode_string_array($sections);?>" >
								<div class="input-group">
									<span class="input-group-btn">
										<button class="btn btn-info" type="submit" name="image-search"><i class="glyphicon glyphicon-plus small"></i> Filter</button>
									</span>
									<input type="text" class="form-control" placeholder="By Title" name="title" >
								</div>
							</form>
						</div>
						<div class="col-lg-3 col-sm-3 col-md-3">
							<form method="post" action="projector-view.php">
								<input type="hidden" name="sections" value="<?php echo encode_string_array($sections);?>" >
								<div class="input-group">
									<span class="input-group-btn">
										<button class="btn btn-info" type="submit" name="image-search"><i class="glyphicon glyphicon-plus small"></i> Filter</button>
									</span>
									<input type="text" class="form-control" placeholder="By Total Score" name="score" >
								</div>
							</form>
						</div>
						<div class="col-lg-3 col-sm-3 col-md-3">
							<form method="post" action="projector-view.php">
								<input type="hidden" name="sections" value="<?php echo encode_string_array($sections);?>" >
								<input type="hidden" name="score" value="inconsistent" >
								<div class="input-group">
									<span class="input-group-btn">
										<button class="btn btn-info" type="submit" name="image-search"><i class="glyphicon glyphicon-plus small"></i> Pictures with wide variation</button>
									</span>
								</div>
							</form>     
						</div>
						<div class="col-lg-2 col-sm-2 col-md-2">
							<form method="post" action="projector-view.php">
								<input type="hidden" name="sections" value="<?php echo encode_string_array($sections);?>" >
								<input type="hidden" name="next" value="0" >
								<div class="input-group">
									<span class="input-group-btn">
										<button class="btn btn-info" type="submit" name="image-search">Reset</button>
									</span>
								</div>
							</form>     
						</div>

					</div>
					<div class="hpanel">
						<div class="panel-body">
							<div class="lightBoxGallery" style="text-align:left"> 
							<?php
								$pics = mysql_query($query)or die(mysql_error());
								while($tr_pics=mysql_fetch_array($pics)) {
									$pic_id=$tr_pics['pic_id'];
									$picfile = $tr_pics['picfile'];
									$pic_section = $tr_pics['section'];
							?>
								<div class="col-md-3 col-lg-3 containerBox" >
									<div class="thumbnail">
										<a href="../upload/<?php echo $tr_pics['section'] . '/' . $picfile;?>" 
												title="[<?php printf("%05d", $pic_id);?>] <?php echo ucwords($tr_pics['title']);?>" 
												data-description="Space for Rating" data-gallery="">
											<img src="../upload/<?php echo $pic_section . '/tn/' . $picfile;?>" style="width: 100%;">
											<div class="caption">
												<h5><?php printf("%05d", $pic_id);?></h5>
											</div>
										</a>
									</div>
								</div>
							<?php
								}
							?>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-6 col-md-3 col-sm-6">
								<form method="post" action="projector-view.php" style="text-align:center" >
									<div class="input-group">
										<input type="hidden" name="sections" value="<?php echo encode_string_array($sections);?>" >
										<input type="text" class="form-control" placeholder="Start From PIC ID" name="next" width=15 required>
										<span class="input-group-btn">
											<button class="btn btn-info" type="submit" name="image-search">
												<i class="glyphicon glyphicon-plus small"></i> Go To
											</button>
										</span>
									</div>
								</form>
							</div>
							<!--
							<div class="col-lg-4 col-md-3 col-sm-4">
								<a href="projector-view.php?sections=<?php //echo encode_string_array($sections);?> &next=<?php //echo $pic_id+1;?>" class="btn btn-info btn-md" role="button">Next&nbsp;&raquo;</a>
							</div>
							-->

							<!-- <i class="fa fa-picture-o"> </i> 28 images -->   
						</div> 
					</div>
				</div>
			</div>
		</div>

		<!-- Right sidebar -->
		<!-- Footer-->
<?php 
include("master/footer.php");
include("master/profile_modal.php");
?>
	</div>  <!-- / Main Wrapper -->

	<!-- The Gallery as lightbox dialog, should be a child element of the document body -->
	<div id="blueimp-gallery" class="blueimp-gallery" data-continuous="false" data-toggle-slideshow-on-space="false">
		<div class="slides"></div>
		<h3 class="title"></h3>
		<a class="prev">&laquo;</a>
		<a class="next">&raquo;</a>
		<a class="close">&#42;</a>
		<a class="description" href="#" onclick="ctl_get()"><h3>Refresh Rating</h3></a>
		<!-- <a class="play-pause"></a> -->
		<!-- <ol class="indicator"></ol> -->
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
<script src="vendor/bootstrap-star-rating/js/star-rating.min.js"></script>
<script src="vendor/jquery-validation/jquery.validate.min.js"></script>
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

<!-- Local styel for demo purpose - remove text shadow from original plugin styles -->
<style>
    .rating-container .rating-stars:before {
        text-shadow: none;
    }
</style>

<script>
    $(function () {
        // initialize with defaults
        $("#input-1").rating();

    });
</script>

<script>
var timeout_var;

// call back function for $.post to update the description element with update on rating
function refresh_rating (data, status) {
	if (status == "success") {
		var gallery = $('#blueimp-gallery').data('gallery');		// Get Gallery Object
		// data contains ratings in 3 digits with colons in between representing ratings by Jury 1 to 3
		var ratings = JSON.parse(data);
		var desc="";
		var rt;
		var jury = 0;
		for (x in ratings) {
			jury = jury + 1;
			if (ratings[x] == 0)
				desc = desc + jury.toString() + "-N  ";
			else if (ratings[x] > 0)
				desc = desc + jury.toString() + "-Y  ";
			else
				desc = desc + jury.toString() + "-Y*  ";     // Tagged
		}
		// var desc = gallery.list[index].getAttribute('data-description');
		var node = gallery.container.find('.description');
		// node.empty();
		if (desc)
			node[0].innerHTML = "<h3>" + desc + "</h3>";
			// node[0].appendChild(document.createTextNode(desc));
	}
}

// function to get ratings from CTL for screen update
function ctl_get() {
	$.post("op/ctl_get.php", {"refresh":"y"}, refresh_rating, "text");	// call php to update the picture ID. It returns ratings passed on to a call back function
}

// function to get ratings from CTL for screen update
function ctl_auto_get() {
	$.post("op/ctl_get.php", "", refresh_rating, "text");	// call php to update the picture ID. It returns ratings passed on to a call back function
	timeout_var = setTimeout(ctl_auto_get, 2000);
}


// function added by Murali to update ctl table
function ctl_update(index) {
	// alert("slide change");
	var gallery = $('#blueimp-gallery').data('gallery');		// Get Gallery Object
	var text = gallery.list[index].getAttribute('title');		// Get title text
	var pic_id = text.substring(1,6);							// Title starts with [nnnnn] containing the picture id
	// alert(pic_id);
    if (pic_id) {
        $.post("op/ctl_set_current.php", {"pic_id":pic_id}, refresh_rating, "text");	// call php to update the picture ID. It returns ratings passed on to a call back function
    }
	clearTimeout(timeout_var);		// Remove any timeouts in progress
	timeout_var = setTimeout(ctl_auto_get, 2000);
}
</script>

<script>

    $(function(){

		$("#blueimp-gallery").on("slideend", function (event, index, slide) {ctl_update(index);});   // Initiate ctl table update on navigation to next slide
		$("#blueimp-gallery").on("close", function (event) {clearTimeout(timeout_var);});		// Reset any timer in progress
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
header("Location: ".$_SERVER['HTTP_REFERER']);
printf("<script>location.href='".$_SERVER['HTTP_REFERER']."'</script>");
}

?>