<?php
session_start();
// Insert a rating or tag into ratings table

include("connect.php");

if((isset($_REQUEST['rating']) || isset($_REQUEST['tag']))  && isset($_SESSION['jury_id'])) {
	$session_id=$_SESSION['jury_id'];
	$edate=date('D, M jS, Y H:i:s A');
	$retval = "";

	// Load Current Picture and related details
	$query = mysqli_query($DBCON, "SELECT pic.pic_id AS pic_id, entry_id, section, title, picfile FROM ctl, pic WHERE pic.pic_id=ctl.pic_id") or die(mysqli_error($DBCON));
	$row = mysqli_fetch_array($query);
	$cur_pic_id = $row["pic_id"];
	$cur_pic_uid = $row["entry_id"];			// User ID. Really not required
	$cur_pic_section = $row["section"];		// Section of this picture
	$cur_pic_title = $row["title"];
	$cur_pic_file = $row["picfile"];

	// Check if Jury is assigned to this section
	$query = mysqli_query($DBCON, "SELECT * FROM assignment WHERE section = '$cur_pic_section' AND user_id = $session_id") or die(mysqli_error($DBCON));
	if (mysqli_num_rows($query) == 0)		// Jury not assigned to this section and hence cannot provide rating
		$retval = array('status' => 'NAS', 'errmsg' => 'You are not assigned to this section');
	else {
		// Check if there is a rating record
		$query = mysqli_query($DBCON, "SELECT * FROM rating WHERE pic_id = $cur_pic_id AND user_id = $session_id") or die(mysqli_error($DBCON));
		if (mysqli_num_rows($query) == 0) {	// Insert New Rating
			if (isset($_REQUEST['tag']))	// cannot tag image without a rating
				$retval = array('status' => 'ERR', 'errmsg' => 'Picture must be rated before it is tagged');
			else {
				$new_rating = $_REQUEST['rating'];
				$query = mysqli_query($DBCON, "INSERT INTO rating(pic_id, user_id, rating, tags, edate) VALUES('$cur_pic_id', '$session_id', '$new_rating', '', '$edate')") or die(mysqli_error($DBCON));
				$cur_pic_rating = $new_rating;
				$cur_pic_tags = "";
			}
		}
		else {	// Update rating or tag
			$row = mysqli_fetch_array($query);
			$cur_pic_rating = $row['rating'];
			$cur_pic_tags = $row['tags'];
			$sql = "UPDATE rating ";
			if (isset($_REQUEST['tag'])) {
				$sql .= "SET tags='TAGGED', ";
				$cur_pic_tags = 'TAGGED';
			}
			else {
				$sql .= "SET rating = " . $_REQUEST['rating'] . ", ";
				$cur_pic_rating = $_REQUEST['rating'];
			}
			$sql .= " edate = '$edate' ";
			$sql .= " WHERE pic_id = $cur_pic_id AND user_id = $session_id ";
			mysqli_query($DBCON, $sql) or die(mysqli_error($DBCON));
		}
		if ($retval == "")	// No errors
			$retval = array('status' => 'OK', 
							'cur_pic_section' => $cur_pic_section, 
							'cur_pic_id' => $cur_pic_id, 
							'cur_pic_title' => $cur_pic_title, 
							'cur_pic_file' => $cur_pic_file, 
							'cur_pic_rating' => $cur_pic_rating, 
							'cur_pic_tags' => $cur_pic_tags);
	}
}
else {
	$retval = array('status' => 'ERR', 'errmsg' => 'Login required');
}
echo json_encode($retval);
?>
