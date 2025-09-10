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
    $sections = $_REQUEST['sections'];
    $sections = str_replace(" ", "", $sections);   
    $sections = decode_string_array($sections);
	
	// Generate a List of Sections
	$sql = mysqli_query($DBCON, "SELECT * FROM section") or die(mysqli_error($DBCON));
	$sectionList = array();
	for ($idx = 0; $row = mysqli_fetch_array($sql); ++ $idx)
		$sectionList[$idx] = $row['section'];
	$numSections = $idx;

	// Generate a List of Jury Assignments
	$sql = mysqli_query($DBCON, "SELECT * FROM section, assignment WHERE section.section = assignment.section AND section.section='$sections'") or die(mysqli_error($DBCON));
	$juryList = array();
	for ($idx = 0; $row = mysqli_fetch_array($sql); $idx++)
		$juryList[$idx] = $row["user_id"];
	$numJuries = $idx;
	
	// Prepare Temp table
	mysqli_query($DBCON, "TRUNCATE TABLE temp ") or die(mysqli_error($DBCON));
	$sql = " INSERT INTO temp (pic_id, rating_count) SELECT pic_id, COUNT(*) AS rc FROM rating GROUP BY pic_id HAVING rc = '$numJuries' ";
	mysqli_query($DBCON, $sql) or die(mysqli_error($DBCON));
		
	// Get number of pictures left to be scored
	$qck = mysqli_query($DBCON, "SELECT COUNT(*) AS not_rated FROM pic, entry WHERE section='$sections' AND pic.entry_id = entry.entry_id AND pic_id NOT IN (SELECT pic_id FROM temp)") or die(mysqli_error($DBCON));
	$row = mysqli_fetch_array($qck);
	$pics_left = $row["not_rated"];
	
	// Get Award List
	$qchk = mysqli_query($DBCON, "SELECT * FROM award WHERE section='$sections' OR section='CONTEST'");
	$awardList = array();
	while ($row = mysqli_fetch_array($qchk)) {
		$awardName = $row["name"] . "-" . $row["number"];
		if ($row["section"] == "CONTEST")
				$awardName .= " (OVERALL)";
		$awardList[$awardName] = $row["award_id"];
	}
	
	// Get Result List
	$qchk = mysqli_query($DBCON, "SELECT * FROM result, award WHERE award.award_id = result.award_id AND (section='$sections' OR section='CONTEST')") or die(mysqli_error($DBCON));
	$resultList = array();
	while ($row = mysqli_fetch_array($qchk)) {
		$awardName = $row["name"] . "-" . $row["number"];
		if ($row["section"] == "CONTEST")
			$awardName .= " (OVERALL)";
		if(isset($resultList[$awardName])) 
			array_push($resultList[$awardName], $row["pic_id"]);
		else 
			$resultList[$awardName] = array($row["pic_id"]);
	}
	
	
	// Generate Bucket List
	if (isset($_SESSION["buckets"]) && $_SESSION["buckets"]["section"] == $sections) {
		// Restore Bucket List from Session Variable
		$bucketList = $_SESSION["buckets"];
	}
	else {
		$bucketList = array();
		$bucketList["section"] = $sections;
		$bucketList["current_list"] = "";
		$bucketList["Bucket 1"] = array();
		$bucketList["Bucket 2"] = array();
		$bucketList["Bucket 3"] = array();
		$bucketList["Bucket 4"] = array();
		$bucketList["Bucket 5"] = array();
		foreach($awardList as $award => $award_id) {
			$bucketList[$award] = array();
			if (isset($resultList[$award]))
				$bucketList[$award] = $resultList[$award];
		}
		$_SESSION["buckets"] = $bucketList;
	}
		
	if (isset($_REQUEST['image-assign'])) {  // Assign Operation
		$bucketList["current_list"] = $_REQUEST['selected_bucket'];
		$currentBucket = $bucketList["current_list"];
		if (isset($_REQUEST['checkbox'])) {	// Empty array causes the entire operation to return NULL
			$bucketList[$currentBucket] = array_unique(array_merge($bucketList[$currentBucket], $_REQUEST['checkbox']));
			if (isset($awardList[$currentBucket])) 	 // If it is one of the Award Buckets, reset Result List as well
				$resultList[$currentBucket] =  $bucketList[$currentBucket];
		}
		$_SESSION["buckets"] = $bucketList;
		$request = $_SESSION["saved-request"];
	}
	else if (isset($_REQUEST['remove-assign'])) {  // Remove from  Bucket Operation
		$bucketList["current_list"] = $_REQUEST['selected_bucket'];
		$currentBucket = $bucketList["current_list"];
		if (isset($_REQUEST['checkbox'])) {
			$bucketList[$currentBucket] = array_diff($bucketList[$currentBucket], $_REQUEST['checkbox']);
			if (isset($awardList[$currentBucket])) 	 // If it is one of the Award Buckets, reset the Result List as well
				$resultList[$currentBucket] =  $bucketList[$currentBucket];
		}
		$_SESSION["buckets"] = $bucketList;
		$request = $_SESSION["saved-request"];
	}
	/******* Now saving of awards is done automatically. Hence this is not used *********
	else if (isset($_REQUEST['save-award'])) {
		foreach($bucketList as $bucket => $picList) {
			if (isset($awardList[$bucket])) {
				$award_id = $awardList[$bucket];
				$qchk = mysqli_query($DBCON, "DELETE FROM result WHERE award_id = '$award_id'") or die(mysqli_error($DBCON));
				foreach ($picList as $pic_id)
					$qchk = mysqli_query($DBCON, "INSERT INTO result (award_id, pic_id, ranking) VALUES ('$award_id', '$pic_id', '0')") or die(mysqli_error($DBCON));
			}
		}
		$request = $_SESSION["saved-request"];
	}
	**********/
	else {
		$request = $_REQUEST;
		$_SESSION["saved-request"] = $_REQUEST;
	}

	// Auto save pictures assigned to Award Buckets
	foreach($bucketList as $bucket => $picList) {
		if (isset($awardList[$bucket])) {	// If the bucket is part of Award and Picture List is not empty
			$award_id = $awardList[$bucket];
			// Retrieve existing rankings to be applied back if there is no new ranking
			$qchk = mysqli_query($DBCON, "SELECT * FROM result WHERE award_id = '$award_id'") or die(mysqli_error($DBCON));
			$rankList = array();
			while ($row = mysqli_fetch_array($qchk))
				$rankList[$row['pic_id']] = $row['ranking'];
			// Clear results for Award
			$qchk = mysqli_query($DBCON, "DELETE FROM result WHERE award_id = '$award_id'") or die(mysqli_error($DBCON));
			// Insert new result rows for each award
			foreach ($picList as $pic_id) {
				// Find the ranking
				if (isset($_REQUEST["ranking"]) && isset($_REQUEST["ranking"][$pic_id])) 		// If ranking has been assigned use it
					$pic_ranking = $_REQUEST["ranking"][$pic_id];
				else if (isset($rankList[$pic_id]))		// Use previous ranking if available
					$pic_ranking = $rankList[$pic_id];
				else									// Default Ranking is 0
					$pic_ranking = 0;
				$qchk = mysqli_query($DBCON, "INSERT INTO result (award_id, pic_id, ranking) VALUES ('$award_id', '$pic_id', '$pic_ranking')") or die(mysqli_error($DBCON));
			}
		}
	}
	
	$filterText = "";
	// All filtering queries are prepared here
	// All queries filter by section by default
	// Case 1 - 'next' filter is set with a picture id
	if (isset($request['next'])) {
		$next = $request['next'];	// Start display of 28 pictures from
		$filterText = "No Filter";
		$query = "SELECT pic_id, entry.entry_id as entry_id, name, gender, section, title, location, picfile, eseq ";
		$query .= " FROM pic, entry ";
		$query .= " WHERE pic.entry_id = entry.entry_id AND ";
		$query .= "       pic.section='$sections' AND ";
		$query .= " 	  pic.pic_id>='$next' ";
		$query .= " ORDER BY eseq ASC ";
	}
	// Case 2 - 'title' filter
	if (isset($request['title'])) {
		$title = $request['title'];
		$filterText = "Filter by Title/ID (" .$title . ")";
		$query = "SELECT pic_id, entry.entry_id as entry_id, name, gender, section, title, location, picfile, eseq ";
		$query .= " FROM pic, entry ";
		$query .= " WHERE pic.entry_id = entry.entry_id AND ";
		$query .= "       pic.section='$sections' AND ";
		$query .= " 	  (pic.title LIKE '%$title%' OR ";
		$query .= "        pic.location LIKE '%$title%' OR ";
		$query .= "        pic.pic_id = '$title') ";
		$query .= " ORDER BY eseq ASC ";
	}
	// Case 3 - 'score' filter
	if (isset($request['score'])) {
		$score = $request['score'];
		if ($score == "inconsistent") {
			// Find pictures with difference of more than 2 marks between minimum and maximum
			$filterText = "Filtered for ratings with a difference of 2 or more";
			$query = "SELECT pic.pic_id as pic_id, entry.entry_id as entry_id, name, gender, section, title, location, picfile, eseq, MIN(rating) AS min_score, MAX(rating) AS max_score ";
			$query .= " FROM pic, entry, rating ";
			$query .= " WHERE pic.entry_id = entry.entry_id AND ";
			$query .= "       pic.pic_id = rating.pic_id AND ";
			$query .= "       pic.section='$sections' ";
			$query .= " GROUP BY pic_id ";
			$query .= " HAVING (max_score - min_score) > 2 ";
			$query .= " ORDER BY eseq ASC ";
		}
		else if ($score == "tagged") {
			// Find pictures with a total score equal to the score entered
			$score = trim($score);
			$filterText = "Tagged Images";
			$query = "SELECT pic.pic_id as pic_id, entry.entry_id as entry_id, name, gender, section, title, location, picfile, eseq, SUM(rating) AS total_score ";
			$query .= " FROM pic, entry, rating ";
			$query .= " WHERE pic.entry_id = entry.entry_id AND ";
			$query .= "       pic.pic_id = rating.pic_id AND ";
			$query .= "       pic.section='$sections' AND ";
			$query .= "       pic.pic_id IN (SELECT pic_id FROM rating WHERE tags != '') ";
			$query .= " GROUP BY pic_id ";
			$query .= " ORDER BY eseq ASC ";
		}
		else {
			// Find pictures with a total score equal to the score entered
			$score = trim($score);
			$filterText = "Filter by Score " . $score;
			$query = "SELECT pic.pic_id as pic_id, entry.entry_id as entry_id, name, gender, section, title, location, picfile, eseq, SUM(rating) AS total_score ";
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
	if (isset($request['unscored'])) {
		$filterText = "Pictures with no rating";
		$query = "SELECT pic.pic_id as pic_id, entry.entry_id as entry_id, name, gender, section, title, location, picfile, eseq ";
		$query .= " FROM pic, entry ";
		$query .= " WHERE pic.entry_id = entry.entry_id AND ";
		$query .= "       pic.section='$sections' AND ";
		$query .= "       pic.pic_id NOT IN (SELECT pic_id FROM temp ) ";
		$query .= " ORDER BY eseq ASC ";
		
	}
	
	// Case 5 - View Assigned
	if (isset($request['view-assign'])) {
		$filterText = "By selected Bucket/Award";
		$bucketList["current_list"] = $request['selected_bucket'];
		$currentBucket = $bucketList["current_list"];
		if (isset($awardList[$currentBucket])) { // If this relates to Awards - order by ranking assigned
			$query = "SELECT pic.pic_id AS pic_id, entry.entry_id as entry_id, name, gender, section, title, location, picfile, eseq, ranking ";
			$query .= " FROM pic, entry, result ";
			$query .= " WHERE pic.entry_id = entry.entry_id AND ";
			$query .= "       result.award_id = '" . $awardList[$currentBucket] . "' AND ";
			$query .= "       result.pic_id = pic.pic_id ";
			$query .= " ORDER BY ranking, pic_id ";
		}
		else {
			$picList = $bucketList[$currentBucket];
			$query = "SELECT pic_id, entry.entry_id as entry_id, name, gender, section, title, location, picfile, eseq ";
			$query .= " FROM pic, entry ";
			$query .= " WHERE pic.entry_id = entry.entry_id AND ";
			$query .= "       pic.pic_id IN (";
			if (count($picList) > 0)
				$query .= implode(",", $picList);
			else
				$query .= "0";    // Nonexistent picture id
			$query .= ") ";
			$query .= " ORDER BY eseq ASC ";
		}
	}
	
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
						<li><a class="" href="logout.php">Logout</a></li>
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
				<a href="#"><img src="images/yps.jpg" class="img-circle m-b" alt="logo"></a>
				<div class="stats-label text-color">
					<span class="font-extra-bold font-uppercase">Projection Master</span>
					<div class="dropdown">
						<a class="dropdown-toggle" href="#" data-toggle="dropdown">
							<small class="text-muted">Projection Master <b class="caret"></b></small>
						</a>
						<ul class="dropdown-menu animated flipInX m-t-xs">
							<li><a href="#" id="sidebar" class="right-sidebar-toggle">Profile</a></li>
							<li class="divider"></li>
							<li><a href="logout.php">Logout</a></li>
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
				<li class="active">
					<a href="op/email_result.php"> <span class="nav-label"><i class="fa fa-home"></i> Mail Result </span></a>
				</li>            				
			</ul>
		</div>
	</aside>
	<!-- Main Wrapper -->
	<div id="wrapper">
		<div class="content animate-panel">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12">
					<!-- Filters Row -->
            		<div class="row">
						<div class="col-lg-3 col-sm-3 col-md-3">
							<form method="post" action="projector-view.php">
								<input type="hidden" name="sections" value="<?php echo encode_string_array($sections);?>" >
								<input type="hidden" name="tn_per_row" value="<?php echo $tn_per_row;?>" class="tn_per_row">	<!-- Will be set in Javascript -->
								<label>By Title/ID</label>
								<div class="input-group">
									<span class="input-group-btn">
										<button class="btn btn-info" type="submit" name="image-search"><i class="glyphicon glyphicon-filter small"></i></button>
									</span>
									<input type="text" class="form-control" placeholder="By Title/ID" name="title" >
								</div>
							</form>
						</div>
						<div class="col-lg-3 col-sm-3 col-md-3">
							<form method="post" action="projector-view.php">
								<input type="hidden" name="sections" value="<?php echo encode_string_array($sections);?>" >
								<input type="hidden" name="tn_per_row" value="<?php echo $tn_per_row;?>" class="tn_per_row">	<!-- Will be set in Javascript -->
								<label>By Score[+]</label>
								<div class="input-group">
									<span class="input-group-btn">
										<button class="btn btn-info" type="submit" name="image-search"><i class="glyphicon glyphicon-filter small"></i></button>
									</span>
									<input type="text" class="form-control" placeholder="By Total Score" name="score" >
								</div>
							</form>
						</div>
						<div class="col-lg-2 col-sm-2 col-md-2">
							<form method="post" action="projector-view.php">
								<input type="hidden" name="sections" value="<?php echo encode_string_array($sections);?>" >
								<input type="hidden" name="score" value="inconsistent" >
								<input type="hidden" name="tn_per_row" value="<?php echo $tn_per_row;?>" class="tn_per_row">	<!-- Will be set in Javascript -->
								<label>Ratings</label>
								<div class="input-group">
									<span class="input-group-btn">
										<button class="btn btn-info" type="submit" name="image-search"><i class="glyphicon glyphicon-filter small"></i> Variations</button>
									</span>
								</div>
							</form>     
						</div>

						<div class="col-lg-2 col-sm-2 col-md-2">
							<form method="post" action="projector-view.php">
								<input type="hidden" name="sections" value="<?php echo encode_string_array($sections);?>" >
								<input type="hidden" name="score" value="tagged" >
								<input type="hidden" name="tn_per_row" value="<?php echo $tn_per_row;?>" class="tn_per_row">	<!-- Will be set in Javascript -->
								<label>Tagged</label>
								<div class="input-group">
									<span class="input-group-btn">
										<button class="btn btn-info" type="submit" name="image-search"><i class="glyphicon glyphicon-filter small"></i> Tagged</button>
									</span>
								</div>
							</form>     
						</div>
						<div class="col-lg-2 col-sm-2 col-md-2">
							<form method="post" action="projector-view.php">
								<input type="hidden" name="sections" value="<?php echo encode_string_array($sections);?>" >
								<input type="hidden" name="next" value="0" >
								<input type="hidden" name="tn_per_row" value="<?php echo $tn_per_row;?>" class="tn_per_row">	<!-- Will be set in Javascript -->
								<label>Reset Filters</label>
								<div class="input-group">
									<span class="input-group-btn">
										<button class="btn btn-info" type="submit" name="image-search">No Filter</button>
									</span>
								</div>
							</form>     
						</div>
					</div>  
					<!-- end of Filter Row -->
					
				
					<div class="hpanel">
						<h5>Filter Criteria : <?php echo $filterText; ?></h5>
						<p><b>Select pictures and Assign/Unassign to Bucket or View a Bucket</b></p>
						<form method="post" action="projector-view.php" class="form-inline">
							<input type="hidden" name="sections" value="<?php echo encode_string_array($sections);?>" >
							<input type="hidden" name="assign" value="assign">  <!-- to identify this form -->
							<div class="form-group">
								<select class="form-control input-group-select" placeholder="Select a Bucket" name="selected_bucket" >
								<?php
									foreach($bucketList as $bucket => $value) {
										if($bucket != "section" && $bucket != "current_list") {
								?>
									<option value="<?php echo $bucket;?>" <?php if($bucket == $bucketList['current_list']) echo 'selected';?>>
										<?php echo $bucket; ?>
									</option>
								<?php
										}
									}
								?>
								</select>
							</div>
							<div class="input-group">
								&nbsp;&nbsp;&nbsp;&nbsp;
								<span class="input-group-btn">
									<button class="btn btn-info" type="submit" name="image-assign"><i class="glyphicon glyphicon-ok-circle small"></i> Assign</button>
								</span>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<span class="input-group-btn">
									<button class="btn btn-info" type="submit" name="remove-assign"><i class="glyphicon glyphicon-ok-circle small"></i> Remove</button>
								</span>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<span class="input-group-btn">
									<button class="btn btn-info" type="submit" name="view-assign"><i class="glyphicon glyphicon-filter small"></i> View</button>
								</span>
								<!--
								&nbsp;&nbsp;&nbsp;&nbsp;
								<span class="input-group-btn">
									<button class="btn btn-info" type="submit" name="save-award"><i class="glyphicon glyphicon-filter small"></i>Email Result</button>
								</span>
								-->
							</div>
							<div class="form-group">
								<b style="white-space:pre;">                                Set Zoom Level : </b> 
								<select class="form-control input-group-select" name="tn_per_row" id="tn_per_row">
									<option value="6" <?php echo ($tn_per_row == 6) ? "selected" : "";?> >6 per row</option>
									<option value="4" <?php echo ($tn_per_row == 4) ? "selected" : "";?> >4 per row</option>
									<option value="3" <?php echo ($tn_per_row == 3) ? "selected" : "";?> >3 per row</option>
									<option value="2" <?php echo ($tn_per_row == 2) ? "selected" : "";?> >2 per row</option>
								</select>
							</div>
							
							<div class="panel-body">
								<p>
									<a href="#" style="color: blue;" onclick="check_all()">Check ALL</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" style="color: red;" onclick="uncheck_all()">Uncheck ALL</a>
								</p>
								<div class="lightBoxGallery" id="gallery" style="text-align:left"> 
								<?php
									// var_dump($_REQUEST);
									// var_dump($rankList);
									// var_dump($awardList);
									// var_dump($resultList);
									// var_dump($bucketList);
									// print_r($_SESSION);
									$pics = mysqli_query($DBCON, $query)or die(mysqli_error($DBCON));
									$max = mysqli_num_rows($pics);
									echo "<p>Number of Pictures: " . $max . "</p>";
									$idx = 0;
									while($tr_pics=mysqli_fetch_array($pics)) {
										$idx++;
										$pic_id=$tr_pics['pic_id'];
										$picfile = $tr_pics['picfile'];
										$pic_section = $tr_pics['section'];
										$pic_alert = $tr_pics['name'] . ' (' .  $tr_pics['entry_id'] . ')' ;
								?>
									<div class="<?php echo $tn_classes;?> containerBox" >
										<input name="checkbox[]" type="checkbox" id="checkbox[]" value="<?php echo $pic_id;?>">
									<?php
										echo ($tr_pics['gender'] == "F") ? "&nbsp;&nbsp;<b>W</b>" : "";
									?>
									<?php
										if(isset($_REQUEST['view-assign'])) {		// Display button to view entry details
											if(isset($awardList[$currentBucket])) {		// Show ranking options
												$pic_ranking = $tr_pics["ranking"];
									?>
										&nbsp;&nbsp;&nbsp;&nbsp;
										<a href="#" style="margin:0; padding: 0;" onclick="increment('ranking[<?php echo $pic_id;?>]')"><i class="glyphicon glyphicon-plus small"></i></a>
										<input type="text" size="2" readonly style="text-align: center; border: 0; font-weight: bold;" name="ranking[<?php echo $pic_id;?>]" id="ranking[<?php echo $pic_id;?>]" value="<?php echo $pic_ranking;?>" />
										<a href="#" style="margin:0; padding: 0;" onclick="decrement('ranking[<?php echo $pic_id;?>]')"><i class="glyphicon glyphicon-minus small"></i></a>
									<?php
											}
									?>
										&nbsp;&nbsp;&nbsp;&nbsp;
										<a href="#" onclick="alert('Submitted By: <?php echo $pic_alert; ?> ')"><i class="glyphicon glyphicon-user small"></i></a>
									<?php
										}
									?>
										<div class="col-md-12 col-lg-12 col-xs-12 thumbnail" >
											<a href="../upload/<?php echo $pic_section . '/' . $picfile;?>" 
													title="[<?php printf("%05d", $pic_id);?>] <?php echo ucwords($tr_pics['title']) . " (" . $tr_pics['location'] . ") {" . $idx . "/" . $max . "}";?>" 
													data-description="Space for Rating" data-gallery="">
												<img src="../upload/<?php echo $pic_section . '/tn/' . $picfile;?>" >
												<div class="caption">
													<p>	
														<?php printf("<b>%05d</b> %s (%s)", $pic_id, ucwords($tr_pics['title']), $tr_pics['location']);?>
													</p>
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
						</form>
						<!-- 
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
							
							<div class="col-lg-4 col-md-3 col-sm-4">
								<a href="projector-view.php?sections=<?php //echo encode_string_array($sections);?> &next=<?php //echo $pic_id+1;?>" class="btn btn-info btn-md" role="button">Next&nbsp;&raquo;</a>
							</div>
							

							<i class="fa fa-picture-o"> </i> 28 images 
						</div> -->
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

<!-- Handle Spinner -->
<script>
function increment(field) {
	// var rankings = document.getElementsByName('ranking[]');
	// var rankingValue = Number(rankings[index].value);
	var ranking = document.getElementById(field);
	var rankingValue = Number(ranking.value);
	if (rankingValue < 99)
		rankingValue = rankingValue + 1;
	// rankings[index].value = rankingValue;
	ranking.value = rankingValue;
}

function decrement(field) {
	// var rankings = document.getElementsByName('ranking[]');
	// var rankingValue = Number(rankings[index].value);
	var ranking = document.getElementById(field);
	var rankingValue = Number(ranking.value);
	if (rankingValue > 0)
		rankingValue = rankingValue - 1;
	// rankings[index].value = rankingValue;
	ranking.value = rankingValue;
}

</script>


<script>
// Keeps tab on ratings being assigned by Juries and displays status
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
// Handle Check ALL and Uncheck ALL operations
function check_all() {
	$("#gallery :checkbox").each(function () { $(this).prop("checked", true) });
}

function uncheck_all() {
	$("#gallery :checkbox").each(function () { $(this).prop("checked", false) });
}

</script>

<script>

    $(function(){

		$("#blueimp-gallery").on("slideend", function (event, index, slide) {ctl_update(index);});   // Initiate ctl table update on navigation to next slide
		$("#blueimp-gallery").on("close", function (event) {clearTimeout(timeout_var);});		// Reset any timer in progress

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