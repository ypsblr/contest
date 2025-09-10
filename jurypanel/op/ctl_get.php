<?php
include ("connect.php");

$sql = "SELECT user_id, jurynumber FROM ctl, pic, assignment WHERE pic.pic_id = ctl.pic_id AND assignment.section = pic.section ORDER BY jurynumber";
$qry = mysqli_query($DBCON, $sql) or die(mysqli_error($DBCON));
$ratingList = array();
for ($idx=0; $row = mysqli_fetch_array($qry); $idx++) {
	$user_id = $row['user_id'];
	$qchk = mysqli_query($DBCON, "SELECT rating, tags FROM ctl, rating WHERE rating.pic_id = ctl.pic_id AND user_id = '$user_id'") or die(mysqli_error($DBCON));
	if ($rate = mysqli_fetch_array($qchk)) {
		$ratingList[$idx] = $rate['rating'];
		if ($rate['tags'] != "")
			$ratingList[$idx] *= -1;
	}
	else
		$ratingList[$idx] = 0;
}
echo json_encode($ratingList);
?>
