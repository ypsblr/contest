<?php
session_start();
include("connect.php");
function encode_string_array ($stringArray) {
    $s = strtr(base64_encode(addslashes(gzcompress(serialize($stringArray),2))), '+/=', '_,');
    
    return $s;
}	
function decode_string_array ($stringArray) {
    $s = unserialize(gzuncompress(stripslashes(base64_decode(strtr($stringArray, '_,', '+/=')))));
    return $s;
}

if ((isset($_REQUEST['rating']) || isset($_REQUEST['tag']))  && isset($_SESSION['jury_id']) && isset($_SESSION['cur_pic_id'])) {

	$session_id = $_SESSION['jury_id'];
	$cur_pic_id = $_SESSION['cur_pic_id'];
	$cur_pic_uid = $_SESSION['cur_pic_uid'];
	$cur_pic_myjurynumber = $_SESSION['cur_pic_myjurynumber'];
	$cur_pic_myrating = $_SESSION['cur_pic_myrating'];
	$cur_pic_mytags = $_SESSION['cur_pic_mytags'];
	$cur_pic_section = $_SESSION['cur_pic_section'];
	$status = "OK";
	$_SESSION['msg_rate'] = "";
	
	$edate=date('D, M jS, Y H:i:s A');
	
	// Check synchronization with CTL table
	$query = mysqli_query($DBCON, "SELECT * FROM ctl") or die(mysqli_error($DBCON));
	$row = mysqli_fetch_array($query);
	$ctl_pic_id = $row['pic_id'];
	
	// If the session PIC ID is different from CTL PIC ID, do not update the rating
	if ($cur_pic_id == $ctl_pic_id) {
		// CTL field that will be updated
		if (isset($_REQUEST['tag'])) {
			//$tagop = decode_string_array(trim($_REQUEST['tag']));
			$tagop = $_REQUEST['tag'];
			if ($tagop == "TAG")
				$tagvalue = "TAGGED";
			else
				$tagvalue = "";
			// Check if a rating exists by mistake to avoid duplicate entries.
			$query = mysqli_query($DBCON, "SELECT * FROM rating WHERE pic_id = $cur_pic_id AND user_id = $session_id") or die(mysqli_error($DBCON));
			if (mysqli_num_rows($query) > 0) {
				mysqli_query($DBCON, "UPDATE rating SET tags = '$tagvalue', edate = '$edate' WHERE pic_id = $cur_pic_id AND user_id = $session_id") or die(mysqli_error($DBCON));
				$retval = array('status' => $status, 'errmsg' => $_SESSION['msg_rate'], 'tag' => $tagvalue);
			}
			else {
				$status = "ERR";
				$_SESSION['msg_rate'] = "Picture must be rated before tagging";
				$retval = array('status' => $status, 'errmsg' => $_SESSION['msg_rate']);
			}
		}
		else {
			// $new_rating = decode_string_array(trim($_REQUEST['rating']));
			$new_rating = $_REQUEST['rating'];
			// Check if a rating exists by mistake to avoid duplicate entries.
			$query = mysqli_query($DBCON, "SELECT * FROM rating WHERE pic_id = $cur_pic_id AND user_id = $session_id") or die(mysqli_error($DBCON));
			if ($row = mysqli_fetch_array($query)) {
				$cur_pic_myrating = $row["rating"];
			}
			
			if ($cur_pic_myrating != 0) {
				// Update rating only if the new rating is different from current rating
				if ($cur_pic_myrating != $new_rating) {
					$query = mysqli_query($DBCON, "UPDATE rating SET rating = $new_rating, edate = '$edate' WHERE pic_id = $cur_pic_id AND user_id = $session_id") or die(mysqli_error($DBCON));
				}
				else
					$_SESSION['msg_rate']="Not updated - No change in rating";
			}
			else { 	
				// Insert rating
				$query = mysqli_query($DBCON, "INSERT INTO rating(pic_id, user_id, rating, tags, edate) VALUES('$cur_pic_id', '$session_id', '$new_rating', '', '$edate')") or die(mysqli_error($DBCON));
			}
			$retval = array('status' => $status, 'errmsg' => $_SESSION['msg_rate'], 'rating' => $new_rating);
		}
	}
	else {
		$status = "ERR";
	    $_SESSION['msg_rate']="Not updated - Pictures on LAPTOP and PROJECTOR different.";
		$retval = array('status' => $status, 'errmsg' => $_SESSION['msg_rate']);
	}
}

echo json_encode($retval);
//header("Location: ".$_SERVER['HTTP_REFERER']);
//printf("<script>location.href='".$_SERVER['HTTP_REFERER']."'</script>");

?>
