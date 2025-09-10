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
	
	// Validate that the jury is assigned to the section
	$sql = "SELECT * FROM assignment WHERE user_id = '$jury_id' AND section='$section'";
	$qry = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
	if (mysqli_num_rows($qry) == 0)
		$_SESSION['errmsg'] = "Not assigned to $section section";
	
	// Create Award List
	$sql  = "SELECT * FROM award ";
	$sql .= "WHERE section='$section' || section='CONTEST' ";
	$sql .= "ORDER BY level, sequence ";
	$qry = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
	$awardList = array();
	
	while ($row = mysqli_fetch_array($qry)) {
		$awardList[$row['award_id']] = $row;
	}
		

	// Load Recommendation List
	$sql  = "SELECT * FROM recommendation, award ";
	$sql .= "WHERE recommendation.award_id = award.award_id ";
	$sql .= "  AND award.section = '$section' ";
	$sql .= "  AND recommendation.jury_id = '$jury_id' ";
	$qry = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
	$recommendationList = array();

	while ($row = mysqli_fetch_array($qry)) {
		$recommendationList[$row['pic_id']] = $row;
	}

	// All filtering queries are prepared here
	// All queries filter by section by default
	$query = "SELECT pic.pic_id AS pic_id, entry.entry_id as entry_id, name, gender, section, title, location, picfile, eseq ";
	$query .= " FROM pic, entry, shortlist ";
	$query .= " WHERE pic.entry_id = entry.entry_id ";
	$query .= "   AND pic.section='$section' ";
	$query .= "   AND shortlist.jury_id = '$jury_id' ";
	$query .= "   AND shortlist.pic_id = pic.pic_id ";
	$query .= " ORDER BY eseq ASC ";
		
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
				<a href="#"><img src="images/yps.jpg" class="img-circle m-b" alt="logo"></a>
				<div class="stats-label text-color">
					<span class="font-extra-bold font-uppercase">JURY RANKING</span>
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
				<li><span class="nav-label"><?php echo $section;?></span></li>
					
				<li>
					<a href="#"><span class="nav-label"><i class="fa fa-mortar-board"></i> Shortlist</span><span class="fa arrow"></span> </a>
					<ul class="nav nav-second-level">
					<?php
						for ($idx = 0; $idx < $numSections; ++ $idx) {
					?>
						<li><a href="shortlist.php?sections=<?php echo encode_string_array("$sectionList[$idx]");?> &next=0"><?php echo $sectionList[$idx];?></a></li>
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
						<li><a href="recommend.php?sections=<?php echo encode_string_array("$sectionList[$idx]");?> &next=0"><?php echo $sectionList[$idx];?></a></li>
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
				<!-- Left Panel to assign Pictures to Awards -->
				<div class="col-lg-4 col-md-4 col-sm-4">
					<!-- Collapsible Filtered Panel -->
					<div class="panel panel-default" id="shortlisted-images" >
						<div class="panel-heading"><h3>Shortlisted Pictures</h3></div>
						<div class="panel-body">
							<!-- <form method="post" action="recommend.php" class="form-inline"> -->
							<?php
								$pics = mysqli_query($DBCON, $query) or die(mysqli_error($DBCON));
								$max = mysqli_num_rows($pics);
								$idx = 0;
								while($tr_pics = mysqli_fetch_array($pics)) {
									$idx++;
									$pic_id = $tr_pics['pic_id'];
									$picfile = $tr_pics['picfile'];
									$pic_title = $tr_pics['title'];
									$pic_section = $tr_pics['section'];
									$pic_gender = $tr_pics['gender'];
							?>
								<div class="row">
									<div class="col-lg-4 col-md-4 thumbnail">
										<a href="../upload/<?php echo $pic_section . '/' . $picfile;?>" 
												title="[<?php printf("%05d", $pic_id);?>] <?php echo ucwords($tr_pics['title']) . " {" . $idx . "/" . $max . "}";?>" 
												data-lightbox="recommendation" data-title="<?php echo $pic_title;?>"
												>
											<img src="../upload/<?php echo $pic_section . '/tn/' . $picfile;?>" >
											<div class="caption">
												<p>	<?php printf("<b>%s%05d</b> %s ", ($pic_gender == "F") ? "(W) " : "", $pic_id, ucwords($tr_pics['title']));?> </p>
											</div>
										</a>
									</div>
									<div class="col-lg-8 col-md-8">
										<div class="form-group col-lg-12 col-md-12">
											<label>Award </label>
											<select class="form-control" id="award-select-<?php echo $pic_id;?>">
											<?php
												foreach ($awardList as $award) {
											?>
												<option value='<?php echo $award['award_id'];?>'><?php echo $award['name'] . ($award['number'] > 0 ? "(" . $award['number'] . ")" : "") ;?></option>
											<?php
												}
											?>
											</select>
										</div>
										<div class="form-group col-lg-12 col-md-12" >
											<label>Rank </label>
											<input type="number" value="1" name="ranking" id="award-rank-<?php echo $pic_id;?>" class="form-control" style="width:100px">
										</div>
										<div class="form-group col-lg-12 col-md-12">
											<a href="javascript: addRecommendation(<?php echo $pic_id;?>, 'award-select-<?php echo $pic_id;?>', 'award-rank-<?php echo $pic_id;?>')" name="assign" id="assign" class="btn btn-primary">Assign -></a>
										</div>
									</div>
								</div>
								<hr>
							<?php
								}
							?>
							<!-- </form> -->
						</div>
					</div>
				</div>
				<!-- Right Panel to display Pictures to Awards -->
				<div class="col-lg-8 col-md-8 col-sm-8">
					<!-- Create a Panel or each award -->
					<?php
						foreach ($awardList as $award) {
					?>
					<div class="panel panel-default" >
						<div class="panel-heading"<h3>Recommendations for <b><?php echo $award['name'] . ($award['number'] > 0 ? " (" . $award['number'] . ")" : "") ;?></b></h3></div>
						<div class="panel-body" id="recommended-<?php echo $award['award_id'];?>">
							<?php
								// Default Initial Display - Will be overwritten by 
								$sql  = "SELECT pic.pic_id AS pic_id, title, picfile, section, ranking, gender FROM pic, recommendation, entry ";
								$sql .= "WHERE recommendation.award_id = '" . $award['award_id'] ."' ";
								$sql .= "  AND recommendation.jury_id = '$jury_id' ";
								$sql .= "  AND recommendation.pic_id = pic.pic_id ";
								$sql .= "  AND section = '$section' ";
								$sql .= "  AND pic.entry_id = entry.entry_id ";
								$sql .= "ORDER BY ranking ";
								// echo $sql;
								$qry = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
								// var_dump($qry);
								while ($row = mysqli_fetch_array($qry)) {
									$pic_id = $row['pic_id'];
									$picfile = $row['picfile'];
									$pic_title = $row['title'];
									$pic_section = $row['section'];
									$ranking = $row['ranking'];
									$pic_gender = $row['gender'];
							?>
							<div class="col-lg-4 col-md-4 containerBox" >
								<a href="javascript:deleteRecommendation(<?php echo $award['award_id'];?>, <?php echo $pic_id;?>)" style="width:100%; text-align:right;" >Remove</a>
								<div class="col-lg-12 col-md-12 thumbnail">
									<a href="#" 
											title="[<?php printf("%05d", $pic_id);?>] <?php echo ucwords($pic_title);?>" >
										<img src="../upload/<?php echo $pic_section . '/tn/' . $picfile;?>" >
										<div class="caption">
											<p>	<?php printf("<b>%d</b> %s[%d] %s ", $row['ranking'], ($pic_gender == "F") ? "(W) " : "", $pic_id, ucwords($pic_title));?> </p>
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
		<a class="description" href="#" onclick="ctl_get()"><h3>-</h3></a>
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
function renderRecommendation(award_id, data, rows) {
	var idx = 0;
	var html = "";
	for (idx = 0; idx < rows; ++ idx) {
		var row = data[idx];
		html += "<div class='col-lg-4 col-md-4 containerBox' >";
		html += "<a href='javascript:deleteRecommendation(" + award_id +", " + row['pic_id'] + ")' style='width:100%; text-align:right;' >Remove</a>";
		html += "<div class='col-lg-12 col-md-12 thumbnail'>";
		html += "<a href='#' "; 
		html += "		title='" + row['pic_id'] + " " + row['title'] + "' >";
		html += "	<img src='../upload/" + row['section'] + "/tn/" + row['picfile'] + "' >";
		html += "	<div class='caption'>";
		html += "		<p><b><big>" + row['ranking'] + "</big></b> " + (row['gender'] == 'F' ? "(W)" : "") + "[" + row['pic_id'] + "] " + row['title'] + "</p>";
		html += "	</div>";
		html += "</a>";
		html += "</div>";
		html += "</div>";
	}
	$("#recommended-" + award_id).html(html);
}

function addRecommendation(pic_id, award_field, ranking_field){
	var award_id = $("#" + award_field).val();
	var ranking = $("#" + ranking_field).val();
	$.post("op/add_recommendation.php", {award_id: award_id, ranking: ranking, pic_id: pic_id}, function(result, status) {
			if(result['result'] == 'OK') {
				renderRecommendation(award_id, result["data"], result["rows"]);
			}
		 }, "json");
}

function deleteRecommendation(award_id, pic_id){
	$.post("op/delete_recommendation.php", {award_id: award_id, pic_id: pic_id}, function(result, status) {
			if(result['result'] == 'OK') {
				renderRecommendation(award_id, result["data"], result["rows"]);
			}
		 }, "json");
}
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