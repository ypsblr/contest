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
function sql_error($sql, $errmsg, $phpfile, $phpline) {
	file_put_contents("sqldump.txt", "SQL operation failed with message '$errmsg' in line $phpline of '$phpfile'" . chr(13) . chr(10), FILE_APPEND);
	file_put_contents("sqldump.txt", "SQL: " . $sql . chr(13) . chr(10), FILE_APPEND);
	$_SESSION['errmsg'] = "Server Error evaluating Jury assignment";
}
    
if(isset($_SESSION['jury_id']) && $_SESSION['jury_type']=="JURY" || $_SESSION['jury_type'] == "FINALIZE" )
{
	$jury_id = $_SESSION['jury_id'];
	
	// Prepare list of sections
	$sql = "SELECT * FROM section";
	$query = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
	$sectionList = array();
	$numSections = 0;
	while ($row = mysqli_fetch_array($query)) {
		$sectionList[$numSections] = $row['section'];
		$numSections++;
	}
	
	// Ssection to be judged is passed on as a parameter
	if (isset($_REQUEST['section']) && $_REQUEST['section'] != "") 
		$section = decode_string_array($_REQUEST['section']);
	else
		$section = $sectionList[0];
	
	// Get User Information
	$sql = "SELECT * FROM user WHERE user_id = '$jury_id'";
	$qry = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
	$jury = mysqli_fetch_array($qry);
	$jury_name = $jury['name'];
	$jury_avatar = $jury['avatar'];
	
	// Validate that the jury is assigned to the section
	$sql = "SELECT * FROM assignment WHERE user_id = '$jury_id' AND section='$section'";
	$qry = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
	if (mysqli_num_rows($qry) == 0)
		$_SESSION['errmsg'] = "Not assigned to $section section";
	
	$filterText = "";
	// All filtering queries are prepared here
	// All queries filter by section by default
	$filterText = "No Filter";
	$query = "SELECT pic_id, entry.entry_id AS entry_id, name, gender, pic.section AS section, title, location, picfile, eseq ";
	$query .= " FROM pic, entry, assignment ";
	$query .= " WHERE pic.entry_id = entry.entry_id ";
	$query .= "   AND pic.section = '$section' ";
	$query .= "   AND assignment.section = pic.section ";
	$query .= "   AND assignment.user_id = '$jury_id' ";
	$query .= " ORDER BY eseq ASC ";
	
	// Set variable to control Thumbnails per row
	if (isset($_REQUEST['tn_per_row']))
		$tn_per_row = $_REQUEST['tn_per_row'];
	else
		$tn_per_row = 6;		// Default thumbnail display per row
	
	switch ($tn_per_row) {
		case 6 : 	$tn_classes = 'col-lg-2 col-md-2';
					break;
		case 4 : 	$tn_classes = 'col-lg-3 col-md-3';
					break;
		case 3 :	$tn_classes = 'col-lg-4 col-md-4';
					break;
		case 2 :	$tn_classes = 'col-lg-6 col-md-6';
					break;
		default :	$tn_classes = 'col-lg-2 col-md-2';
					break;
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
	<link rel="stylesheet" href="vendor/lightbox/css/lightbox.css" >


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

.blueimp-gallery > .title {
	font-size: 12px;
	width: 25%;
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

.blueimp-gallery-controls > .prev {
	display: none;
}

.blueimp-gallery-controls > .next {
	display: none;
}

.blueimp-gallery-controls > .close {
	display: none;
}


#shortlistmsg {
    visibility: hidden; /* Hidden by default. Visible on click */
    min-width: 250px; /* Set a default minimum width */
    margin-left: -125px; /* Divide value of min-width by 2 */
    background-color: #333; /* Black background color */
    color: #fff; /* White text color */
    text-align: center; /* Centered text */
    border-radius: 2px; /* Rounded borders */
    padding: 16px; /* Padding */
    position: fixed; /* Sit on top of the screen */
    z-index: 1000000; /* Add a z-index if needed */
    left: 50%; /* Center the snackbar */
    bottom: 200px; /* 30px from the bottom */
}

/* Show the snackbar when clicking on a button (class added with JavaScript) */
#shortlistmsg.show {
    visibility: visible; /* Show the snackbar */

/* Add animation: Take 0.2 seconds to fade in and out the snackbar.
However, delay the fade out process for 2.5 seconds */
    -webkit-animation: fadein 0.2s, fadeout 0.2s 1s;
    animation: fadein 0.2s, fadeout 0.2s 1s;
}

/* Animations to fade the snackbar in and out */
@-webkit-keyframes fadein {
    from {bottom: 0; opacity: 0;}
    to {bottom: 30px; opacity: 1;}
}

@keyframes fadein {
    from {bottom: 0; opacity: 0;}
    to {bottom: 30px; opacity: 1;}
}

@-webkit-keyframes fadeout {
    from {bottom: 30px; opacity: 1;}
    to {bottom: 0; opacity: 0;}
}

@keyframes fadeout {
    from {bottom: 30px; opacity: 1;}
    to {bottom: 0; opacity: 0;}
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
			<form role="search" class="navbar-form-custom" method="post" action="#">
				<div class="form-group">
					<input type="text" placeholder="Filter by Title" class="form-control" name="filter" id="filter" >
				</div>
			</form>
		</nav>
	</div>
	<!-- Navigation -->
	<aside id="menu">
		<div id="navigation">
			<div class="profile-picture">
				<a href="#"><img src="images/<?php echo ($jury_avatar != "") ? $jury_avatar : "yps.jpg";?>" class="img-circle m-b" alt="logo"></a>
				<div class="stats-label text-color">
					<span class="font-extra-bold font-uppercase"><?php echo $jury_name;?></span>
					<div class="dropdown">
						<a class="dropdown-toggle" href="#" data-toggle="dropdown">
							<small class="text-muted">Menu<b class="caret"></b></small>
						</a>
						<ul class="dropdown-menu animated flipInX m-t-xs">
							<li><a href="logout.php">Logout</a></li>
						</ul>
					</div>
					<div id="sparkline1" class="small-chart m-t-sm"></div>
					<br>
				</div>
			</div>
			
			<ul class="nav" id="side-menu">
				<li style="text-align: center; padding-top: 4px; padding-bottom: 4px;"><span class="font-extra-bold text-color" ><?php echo $section;?></span></li>
					
				<li>
					<a href="#"><span class="nav-label"><i class="fa fa-mortar-board"></i> Shortlist</span><span class="fa arrow"></span> </a>
					<ul class="nav nav-second-level">
					<?php
						for ($idx = 0; $idx < $numSections; ++ $idx) {
					?>
						<li><a href="shortlist.php?section=<?php echo encode_string_array("$sectionList[$idx]");?> &next=0"><?php echo $sectionList[$idx];?></a></li>
					<?php
						}
					?>
					</ul>
				</li>
				<li>
					<a href="#"><span class="nav-label"><i class="fa fa-mortar-board"></i> Recommend</span><span class="fa arrow"></span> </a>
					<ul class="nav nav-second-level">
					<?php
						for ($idx = 0; $idx < $numSections; ++ $idx) {
					?>
						<li><a href="recommend.php?section=<?php echo encode_string_array("$sectionList[$idx]");?> &next=0"><?php echo $sectionList[$idx];?></a></li>
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
				<?php
					if (isset($_SESSION['errmsg'])) {
				?>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="bg-danger">
						<h3 style="padding-top: 8px; padding-bottom: 8px;"><?php echo "WARNING: " . $_SESSION['errmsg'];?></h3>
					</div>
				</div>
				<?php
						unset($_SESSION['errmsg']);
					}
				?>
				<div class="col-lg-12 col-md-12 col-sm-12">
					<h3 class="bg-info" style="padding-top: 8px; padding-bottom: 8px;"><a href="#" data-toggle="collapse" data-target="#shortlisted-images" class="primary">Shortlisted Images <small><small>Click to toggle</small></small></a></h3>
					<!-- Collapsible Filtered Panel -->
					<div class="hpanel panel-collapse in" id="shortlisted-images" >
						<div class="panel-body">
							<div id="shortlisted">
							<?php
								$sql  = "SELECT pic.pic_id, entry.entry_id as entry_id, name, gender, section, title, location, picfile, eseq ";
								$sql .= " FROM pic, entry, shortlist ";
								$sql .= " WHERE pic.entry_id = entry.entry_id ";
								$sql .= "   AND pic.section='$section' ";
								$sql .= "   AND shortlist.pic_id = pic.pic_id ";
								$sql .= "   AND shortlist.jury_id = '$jury_id' ";
								$sql .= " ORDER BY eseq ASC ";

								$shortlisted = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
								$max = mysqli_num_rows($shortlisted);
								$sl_per_row = 6;
								echo "<p>Number of Pictures: " . $max . "</p>";
								$idx = 0;
								while($tr_pics=mysqli_fetch_array($shortlisted)) {
									$idx++;
									$pic_id=$tr_pics['pic_id'];
									$picfile = $tr_pics['picfile'];
									$pic_section = $tr_pics['section'];
									$pic_gender = $tr_pics['gender'];
									$pic_alert = $tr_pics['name'] . ' (' .  $tr_pics['entry_id'] . ')' ;
							?>
								<div class="col-lg-2 col-md-2 containerBox" >
									<a href="javascript:deleteFromShortlist(<?php echo $pic_id;?>)" style="width:100%; text-align:right;" >(-) Shortlist</a>
									<div class="col-md-12 col-lg-12 col-xs-12 thumbnail" >
										<a href="../upload/<?php echo $pic_section . '/' . $picfile;?>"
											title="[<?php echo $pic_id;?>] <?php echo $tr_pics['title'];?>" 
											data-lightbox="<?php echo $pic_section;?>" 
											data-title="<?php echo $tr_pics['title'];?>" >
											<img src="../upload/<?php echo $pic_section . '/tn/' . $picfile;?>" >
											<div class="caption">
												<p>	<?php printf("<b>%s%05d</b> %s ", ($pic_gender == "F") ? "(W) " : "", $pic_id, ucwords($tr_pics['title']));?> </p>
											</div>
										</a>
									</div>
								</div>
							<?php
									if ($idx % $sl_per_row == 0) {
							?>
										<div class="clearfix"></div>
							<?php
									}
								}
							?>
								
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12">
					<div class="hpanel">
						<form method="post" action="projector-view.php" class="form-inline">
							<input type="hidden" name="section" value="<?php echo encode_string_array($section);?>" >
							<input type="hidden" name="assign" value="assign">  <!-- to identify this form -->
							<div class="form-group">
								<b style="white-space:pre;">Set Zoom Level : </b> 
								<select class="form-control input-group-select" name="tn_per_row" id="tn_per_row">
									<option value="6" <?php echo ($tn_per_row == 6) ? "selected" : "";?> >6 per row</option>
									<option value="4" <?php echo ($tn_per_row == 4) ? "selected" : "";?> >4 per row</option>
									<option value="3" <?php echo ($tn_per_row == 3) ? "selected" : "";?> >3 per row</option>
									<option value="2" <?php echo ($tn_per_row == 2) ? "selected" : "";?> >2 per row</option>
								</select>
							</div>
						</form>
							
						<div class="panel-body">
							<div class="lightBoxGallery" id="gallery" style="text-align:left"> 
							<?php
								$pics = mysqli_query($DBCON, $query)or die(mysqli_error($DBCON));
								$max = mysqli_num_rows($pics);
								echo "<p>Number of Pictures: " . $max . "</p>";
								$idx = 0;
								while($tr_pics=mysqli_fetch_array($pics)) {
									$idx++;
									$pic_id=$tr_pics['pic_id'];
									$picfile = $tr_pics['picfile'];
									$pic_section = $tr_pics['section'];
									$pic_gender = $tr_pics['gender'];
							?>
								<div class="<?php echo $tn_classes;?> containerBox" >
									<a href="javascript: addToShortlist(<?php echo $pic_id;?>)" name="add_to_shortlist" style="width:100%; text-align:right;" value="<?php echo $pic_id;?>" >(+) Shortlist</a>
									<div class="col-md-12 col-lg-12 col-xs-12 thumbnail" >
										<a href="../upload/<?php echo $pic_section . '/' . $picfile;?>" 
											title="[<?php printf("%05d", $pic_id);?>] <?php echo ucwords($tr_pics['title']) . " {" . $idx . "/" . $max . "}";?>" 
											data-description="Space for Rating" data-gallery="">
											<img src="../upload/<?php echo $pic_section . '/tn/' . $picfile;?>" >
											<div class="caption">
												<p>	<?php printf("<b>%s%03d</b> %s", ($pic_gender == "F") ? "(W) " : "", $pic_id, ucwords($tr_pics['title']));?> </p>
											</div>
										</a>
									</div>
								</div>
							<?php
									if ($idx % $tn_per_row == 0) {
							?>
										<div class="clearfix"></div>
							<?php
									}
								}
							?>
							</div>
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
	
	<div id="shortlistmsg">Shortlisted</div>
	<script>
	function showShortlistMsg() {
		// Get the snackbar DIV
		var x = document.getElementById("shortlistmsg")

		// Add the "show" class to DIV
		x.className = "show";

		// After 3 seconds, remove the show class from DIV
		setTimeout(function(){ x.className = x.className.replace("show", ""); }, 1500);
	} 
	</script>
	
	
	<!-- The Gallery as lightbox dialog, should be a child element of the document body -->
	<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-continuous="false" data-toggle-slideshow-on-space="false" data-full-screen="true" data-toggle-controls-on-slide-click="false" >
		<div class="slides"></div>
		<h3 class="title"></h3> 
		<a class="prev">&laquo;</a>
		<a class="next">&raquo;</a>
		<a class="close">&#42;</a>
		<a class="description" href="#" ></a>
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
<script src="vendor/lightbox/js/lightbox.min.js"></script>

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

function showDescription(msg) {
	var gallery = $('#blueimp-gallery').data('gallery');		// Get Gallery Object
	var node = gallery.container.find('.description');
	node[0].innerHTML = "<h3>" + msg + "</h3>";
}

function renderShortlistStatus(pic_id) {
	
	$.post("op/get_shortlist_status.php", {pic_id: pic_id}, function(result, status) {
		var gallery = $('#blueimp-gallery').data('gallery');		// Get Gallery Object
		var node = gallery.container.find('.description');
		if(result['result'] == 'OK' && result['data'] == "YES")
			node[0].innerHTML = "<h3>SHORTLISTED</h3>";
		else
			node[0].innerHTML = "";
	 }, "json");
}

function renderShortlisted(data, rows) {
	var idx = 0;
	var html = "";
	for (idx = 0; idx < rows; ++ idx) {
		if(idx != 0 && idx % 6 == 0)
			html += "<div class='clearfix'></div>";
		var row = data[idx];
		html += "<div class='col-lg-2 col-md-2 col-sm-12 containerBox' >";
		html += "<a href='javascript:deleteFromShortlist(" + row["pic_id"] + ")' style='width:100%; text-align:right;'  >(-) Shortlist</a>";
		html += "<div class='col-md-12 col-lg-12 col-xs-12 thumbnail' >";
		html += "<a class='lightbox-link' href='../upload/" + row["section"] + "/" + row["picfile"] +"' title='[" + row["pic_id"] + "] " + row["title"] + "' ";
		html += "data-lightbox='" + row["section"] + "'"; 
		html += "data-title='" + row["title"] + "' >";
		html += "<img src='../upload/" + row["section"] + "/tn/" + row["picfile"] + "' >";
		html += "<div class='caption'>";
		html += "<p><b>" + (row["gender"] == "F" ? "(W) " : "") + "[" + row["pic_id"] + "]</b> " + row["title"] + "</p>";
		html += "</div>";
		html += "</a>";
		html += "</div>";
		html += "</div>";
		
	}
	$("#shortlisted").html(html);
}

function addToShortlist(pic_id){
	$.post("op/add_to_shortlist.php", {pic_id: pic_id}, function(result, status) {
	   // alert(result['data']);
			if(result['result'] == 'OK') {
				renderShortlisted(result["data"], result["rows"]);
			}
		 }, "json");
}

function deleteFromShortlist(pic_id){
	$.post("op/delete_from_shortlist.php", {pic_id: pic_id}, function(result, status) {
			if(result['result'] == 'OK') {
				renderShortlisted(result["data"], result["rows"]);
			}
		 }, "json");
}
</script>

<script>
	// Install Keypress handler to short list the image on display 
    $(function(){
		$("#blueimp-gallery").on("opened", function (event, index, slide) {
			// Start monitoring key press once the sldeshow starts
			// Plus Keycodes: 187 or 43
			$(document).keydown(function(e) {
				if(e.keyCode == 83 || e.keyCode == 76) { 	// Key S=83 or key L=76
					var gallery = $('#blueimp-gallery').data('gallery');		// Gallery Object
					var index = gallery.getIndex();								// Curren slide inde
					var text = gallery.list[index].getAttribute('title');		// Get title text
					var pic_id = Number(text.substring(1,6));							// Title starts with [nnnnn] containing the picture id
					addToShortlist(pic_id);
					showDescription("S");
					//showShortlistMsg();
					//renderShortlistStatus(pic_id);
				}
			});
		});

		$("#blueimp-gallery").on("close", function (event) {
			// Stop processing keypress
			$(document).keydown(function(e) {
				// No action
			});
		});
		
		$("#blueimp-gallery").on("slide", function (event) {
			showDescription("");
		});

		// Handle Status display on slide change
		/*****
		$("#blueimp-gallery").on("slide", function (event) {
			var gallery = $('#blueimp-gallery').data('gallery');		// Gallery Object
			var index = gallery.getIndex();								// Curren slide inde
			var text = gallery.list[index].getAttribute('title');		// Get title text
			var pic_id = Number(text.substring(1,6));					// Title starts with [nnnnn] containing the picture id
			renderShortlistStatus(pic_id);
		});
		*****/
		
	});

</script>


<script>
$("#tn_per_row").change(function() {
	var tn_per_row = $(this).val();
	$(".tn_per_row").each(function() {
		$(this).val(tn_per_row);
	});
});

</script>

</body>
</html>
<?php
}
else
{
// header("Location: ".$_SERVER['HTTP_REFERER']);
// printf("<script>location.href='".$_SERVER['HTTP_REFERER']."'</script>");

	// Go back to login screen in case of unauthorized access
	header("Location: http://" . $_SERVER['SERVER_NAME'] . "/jurypanel/index.php");
	printf("<script>location.href='http://" . $_SERVER['SERVER_NAME'] . "/jurypanel/index.php'</script>");

}

?>