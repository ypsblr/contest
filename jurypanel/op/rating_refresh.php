<?php
session_start();

include("connect.php");

if(isset($_SESSION['jury_id'])) {
	$session_id=$_SESSION['jury_id'];
	
	// Load assignments
	$query = mysqli_query($DBCON, "SELECT * FROM assignment WHERE user_id = $session_id") or die(mysqli_error($DBCON));
	$assignment = array();
	while ($row = mysqli_fetch_array($query))
		$assignment[$row['section']] = $row['jurynumber'];
	
	// Load Current Picture and related details
	$my_jury_id = $session_id;
	$query = mysqli_query($DBCON, "SELECT pic.pic_id AS pic_id, entry_id, section, title, picfile FROM ctl, pic WHERE pic.pic_id=ctl.pic_id") or die(mysqli_error($DBCON));
	$row = mysqli_fetch_array($query);
	$cur_pic_id = $row["pic_id"];
	if ($cur_pic_id != $_SESSION['cur_pic_id']) {	// Get rating details only if PIC_ID has changed
		$_SESSION['cur_pic_id'] = $cur_pic_id;		// set session variable for use by the ratings update PHP
		$cur_pic_uid = $row["entry_id"];			// User ID. Really not required
		$_SESSION['cur_pic_uid'] = $cur_pic_uid;	// Required for updating to the rating table
		$cur_pic_section = $row["section"];		// Section of this picture
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
			$_SESSION['cur_pic_myjurynumber'] = $cur_pic_myjurynumber;	// Set session variable
			$_SESSION['cur_pic_myrating'] = $cur_pic_myrating;
			$_SESSION['cur_pic_mytags'] = $cur_pic_mytags;
		
			$retval = array('status' => 'OK', 'cur_pic_section' => $cur_pic_section, 'cur_pic_id' => $cur_pic_id, 'cur_pic_title' => $cur_pic_title, 'cur_pic_file' => $cur_pic_file, 'cur_pic_rating' => $cur_pic_myrating, 'cur_pic_tags' => $cur_pic_mytags);
		}
		else
			$retval = array('status' => 'NAS', 'errmsg' => 'You are not assigned to this section');
	}
	else
		$retval = array('status' => 'NOC', 'errmsg' => 'Nothing has changed');
}
else {
	$retval = array('status' => 'ERR', 'errmsg' => 'Login required');
}
echo json_encode($retval);
?>
