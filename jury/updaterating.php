<?php
include "../op/connect.php";
/** Update Ratings */
$err = false;
$id = 0;
$disqualified = false;
$reason = "";
$jury1_rating = 0;
$jury2_rating = 0;
$jury3_rating = 0;
$jury4_rating = 0;
$jury5_rating = 0;

if (isset($_REQUEST["id"]))
	$id=$_REQUEST["id"];
else
	$err = true;

if (isset($_REQUEST["operation"]))
	$operation=$_REQUEST["operation"];
else
	$err = true;

if (isset($_REQUEST["disqualify"]))
	$disqualified=true;
else
	$disqualified=false;

if (isset($_REQUEST["reason"]))
	$reason=$_REQUEST["reason"];
else
	$err = true;

if (isset($_REQUEST["jury1_rating"]))
	$jury1_rating=$_REQUEST["jury1_rating"];

if (isset($_REQUEST["jury2_rating"]))
	$jury2_rating=$_REQUEST["jury2_rating"];

if (isset($_REQUEST["jury3_rating"]))
	$jury3_rating=$_REQUEST["jury3_rating"];

if (isset($_REQUEST["jury4_rating"]))
	$jury4_rating=$_REQUEST["jury4_rating"];

if (isset($_REQUEST["jury5_rating"]))
	$jury5_rating=$_REQUEST["jury5_rating"];

if ($operation == "insert") {
	$query = "INSERT INTO rating (pic_id, disqualified, reason, jury1_rating, jury2_rating, jury3_rating, jury4_rating, jury5_rating, edate)";
	$query .= " VALUES (" . $id . "," . ($disqualified ? 1 : 0) . ",'" . $reason . "'," . $jury1_rating . "," . $jury2_rating . "," . $jury3_rating . "," . $jury4_rating . "," . $jury5_rating . ",'" . date('D, M jS, Y H:i:s A') . "')";
}
else {
	$query = "UPDATE rating SET";
	$query .= " disqualified=" . ($disqualified ? 1 : 0) . ",";
	$query .= " reason='" . $reason . "'," ;
	$query .= " jury1_rating=" . $jury1_rating . ",";
	$query .= " jury2_rating=" . $jury2_rating . ",";
	$query .= " jury3_rating=" . $jury3_rating . ",";
	$query .= " jury4_rating=" . $jury4_rating . ",";
	$query .= " jury5_rating=" . $jury5_rating ;
	$query .= " WHERE pic_id=" . $id;
}
// echo $query;
mysql_query($query) or die(mysql_error());

header("Location:" . $_SERVER['HTTP_REFERER']);

?>