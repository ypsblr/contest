<?php
session_start();
/* clear session variables */
if (isset($_SESSION["user"])) {
	$user = $_SESSION["user"];
	$code = $_SESSION["code"];
}
if ($user != "admin" || $code != "919667") {
	header("Location:./admin.php?error=Invalid Login");
	exit();
}

/* Process Picture Request */
if(isset($_REQUEST["cat"])) {
	$cat = $_REQUEST["cat"];
	$_SESSION["cat"] = $cat;
}
$cat = $_SESSION["cat"];

if ($cat == "heritage")
	$pic_type = "Karnataka Heritage";
else if ($cat == "nature")
	$pic_type = "Karnataka Nature";
else
		header("Location:./judging.php?err=Invalid Parameters");


if (isset($_REQUEST["start"]))
	$start = $_REQUEST["start"];
else
	$start = 0;

if (isset($_REQUEST["nav"]))
	$nav = $_REQUEST["nav"];
else
	$nav = "";

include "../op/connect.php";

/* Find Pic id based on parameters */
$pic_id = $start;

$req = mysql_query("SELECT MAX(PID) FROM allpics WHERE PID < " . $start . " AND Category ='" . $pic_type . "'");
$ret = mysql_fetch_array($req);
$prev = $ret[0];
if ($prev == "") {
	$req = mysql_query("SELECT MIN(PID) FROM allpics WHERE Category ='" . $pic_type . "'");
	$ret = mysql_fetch_array($req);
	$prev = $ret[0];
}

$req = mysql_query("SELECT MIN(PID) FROM allpics WHERE PID > " . $start . " AND Category ='" . $pic_type ."' ");
$ret = mysql_fetch_array($req);
$next = $ret[0];
if ($next == "") {
	$req = mysql_query("SELECT MAX(PID) FROM allpics WHERE Category ='" . $pic_type . "'");
	$ret = mysql_fetch_array($req);
	$next = $ret[0];
}

$req = mysql_query("SELECT * FROM allpics WHERE allpics.PID = " . $start . " AND Category='" . $pic_type ."' ") ;
if (mysql_num_rows($req) == 0)   // Start ID does not exist
	$pic_id = $next;

if ($nav == "prev")
	$pic_id = $prev;

if ($nav == "next")
	$pic_id = $next;

	
/* Fetch Picture Data */
$query = "SELECT PID, EiD, Category, img_title, location, pic FROM allpics WHERE allpics.PID = " . $pic_id;
$req = mysql_query($query);
$ret = mysql_fetch_array($req);
$id = $ret["PID"];
$uid = $ret["EID"];
$category = $ret["Category"];
//$uid = $ret["uid"];
$img_title = $ret["img_title"];
$location = $ret["location"];
$pic = $ret["pic"];

/* Fetch Rating Data */
$query = "SELECT pic_id, disqualified, reason, jury1_rating, jury2_rating, jury3_rating, jury4_rating, jury5_rating FROM rating WHERE pic_id = " . $id;
$req = mysql_query($query) or die(mysql_error());
$ret = mysql_fetch_array($req);
if (mysql_num_rows($req) == 0) {
	$rating_new = true;
	$disqualified = false;
	$reason = "";
	$jury1_rating = 0;
	$jury2_rating = 0;
	$jury3_rating = 0;
	$jury4_rating = 0;
	$jury5_rating = 0;
}
else {
	$rating_new = false;
	$disqualified = $ret["disqualified"];
	$reason = $ret["reason"];
	$jury1_rating = $ret["jury1_rating"];
	$jury2_rating = $ret["jury2_rating"];
	$jury3_rating = $ret["jury3_rating"];
	$jury4_rating = $ret["jury4_rating"];
	$jury5_rating = $ret["jury5_rating"];
}

?>
<!DOCTYPE html>
<html>
<head>
</head>
<body style="background-color: black; color: white; font-family: Arial, Trebuchet, sans-serif" >
	<!-- First display the picture -->
	<p><?php echo "<img src='/img/upload/" . $pic . "' />"; ?></p>
	<h2><?php echo $img_title . "  [" . $location . "]"; ?></h2>
	<br>
	<p><?php echo "Category : " . $category;?>         Switch to : <a href="./display.php?cat=heritage">Karnataka Heritage</a> / <a href="./display.php?cat=nature">Karnataka Nature</a></p>
	<p>
		<?php echo "<a href='./display.php?start=" . $pic_id . "&amp;nav=prev' >&lt;&lt;&lt; PREV &lt;&lt;&lt;</a>"; ?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<?php echo "<a href='./display.php?start=" . $pic_id . "&amp;nav=next' >&gt;&gt;&gt; NEXT &gt;&gt;&gt;</a>"; ?>
		<form action='./display.php' method='post'>
			<input type='text' name='start' <?php echo"value='" . $pic_id . "'"; ?> >
			&nbsp;&nbsp;
			<input type='submit' value=" Go To " >
		</form>	
	</p>
	<br>
	<p><b>Enter Rating</b></p>
	<form action="./updaterating.php" method="post">
		<p>Picture ID : <input type="number" name="id" readonly <?php echo 'value="' . $id . '"' ; ?> >
		<p>Disqualify ? <input type="checkbox" name="disqualify" value="disqualify" <?php if ($disqualified) echo "checked"; ?> >Picture Disqualified</p>
		<p>Reason for disqualification : <input type="text" name="reason" <?php echo 'value="' . $reason . '"'; ?> >
		<p>Ratings</p>
		<h3>Jury 1 : <input type="number" min=0 max=5 name="jury1_rating" <?php echo 'value="' . $jury1_rating . '"'; ?> ></h3>
		<h3>Jury 2 : <input type="number" min=0 max=5 name="jury2_rating" <?php echo 'value="' . $jury2_rating . '"'; ?> ></h3>
		<h3>Jury 3 : <input type="number" min=0 max=5 name="jury3_rating" <?php echo 'value="' . $jury3_rating . '"'; ?> ></h3>
		<h3>Jury 4 : <input type="number" min=0 max=5 name="jury4_rating" <?php echo 'value="' . $jury4_rating . '"'; ?> ></h3>
		<h3>Jury 5 : <input type="number" min=0 max=5 name="jury5_rating" <?php echo 'value="' . $jury5_rating . '"'; ?> ></h3>
		<input type="hidden" name="operation" <?php echo 'value="'; if ($rating_new) echo 'insert"'; else echo 'update"'; ?> >
		<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="submit" value="Update" >
		</p>
	</form>
	<br><br>
	<p>World Tourism Day Digital Photography Contest 2016</p>
	<p>Judging Administration Screen</p>
	<br><br>
	
</body>