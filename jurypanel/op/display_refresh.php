<?php 
session_start();
include("connect.php");

if (isset($_SESSION['jury_id'])) {
	// Load Current Picture and related details
	$query = mysqli_query($DBCON, "SELECT * FROM ctl, pic WHERE pic.pic_id=ctl.pic_id") or die(mysqli_error($DBCON));
	$row = mysqli_fetch_array($query);
	$section = $row['section'];
	$pic_id = $row['pic_id'];
	$pic_title = $row['title'];
	$picfile = $row['picfile'];
	$cur_pic_eseq = $row['eseq'];
	list($width, $height, $type, $attr) = getimagesize("../../upload/" . $row["section"] . "/" . $row["picfile"]);
	
	// Set session variables to identify change
	if (! isset($_SESSION['dr_last_pic_id']))
		$_SESSION['dr_last_pic_id'] = $pic_id;
	
	$pic_changed = false;
	if ($_SESSION['dr_last_pic_id'] != $pic_id)	{	// PIC_ID has changed in ctl table
		$_SESSION['dr_prev_pic_id'] = $_SESSION['dr_last_pic_id'];
		$_SESSION['dr_last_pic_id'] = $pic_id;
		$pic_changed = true;
	}

	// Load Rating List for current picture
	$query = mysqli_query($DBCON, "SELECT * FROM assignment WHERE section = '$section' ORDER BY jurynumber") or die(mysqli_error($DBCON));
	$ratingList = array();
	$totalrating = 0;
	for ($idx = 0; $assign = mysqli_fetch_array($query); $idx++) {
		$jury_id = $assign['user_id'];
		$qass = mysqli_query($DBCON, "SELECT * FROM rating WHERE pic_id = '$pic_id' AND user_id = '$jury_id'");
		if ($qrow = mysqli_fetch_array($qass)) {
			$ratingList[$idx] = $qrow['rating'];
			$totalrating += $qrow['rating'];
		}
		else
			$ratingList[$idx] = "-";
	}
	
	// Load Rating for prev picture
	if (isset($_SESSION['dr_prev_pic_id'])) {
		$prev_pic_id = $_SESSION['dr_prev_pic_id'];
		$query = mysqli_query($DBCON, "SELECT * FROM pic WHERE pic_id = '$prev_pic_id' ") or die(mysqli_error($DBCON));
		$row = mysqli_fetch_array($query);
		$prev_pic_title = $row['title'];
		$prev_pic_file = $row['picfile'];
		$query = mysqli_query($DBCON, "SELECT SUM(rating) as rating FROM rating WHERE pic_id = '$prev_pic_id' ") or die(mysqli_error($DBCON));
		if (mysqli_num_rows($query) > 0) {
			$row = mysqli_fetch_array($query);
			$prev_pic_rating = $row['rating'];
		}
		else
			$prev_pic_rating = 0;
	}
	else {
		$prev_pic_id = 0;
		$prev_pic_title = "";
		$prev_pic_file = "";
		$prev_pic_rating = 0;
	}
	
	// Load Next Picture
	$query = mysqli_query($DBCON, "SELECT * FROM pic WHERE section = '$section' AND pic_id != '$pic_id' AND eseq >= '$cur_pic_eseq' ORDER BY eseq ASC LIMIT 1") or die(mysqli_error($DBCON));
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

}
$retval = array ("pic_id" => $pic_id, 
				"section" => $section, 
				"title" => $pic_title, 
				"pic" => $picfile, 
				"juryrating" => $ratingList, 
				"totalrating" => $totalrating, 
				"height" => $height, 
				"width" => $width,
				"has_pic_changed" => ($pic_changed) ? "YES" : "NO",
				"prev_pic_id" => $prev_pic_id,
				"prev_pic_title" => $prev_pic_title,
				"prev_pic_file" => $prev_pic_file,
				"prev_pic_rating" => $prev_pic_rating,
				"next_pic_id" => $next_pic_id,
				"next_pic_title" => $next_pic_title,
				"next_pic_file" => $next_pic_file);
echo json_encode($retval);

?>
