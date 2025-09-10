<?php
include 'connect.php';

// Fetch 
$query = mysqli_query($DBCON, "SELECT * FROM contest")or die(mysqli_error($DBCON));
$row=mysqli_fetch_array($query);
$contestName = $row["name"];
$submissionStartDate = $row["submission_start_date"];
$submissionLastDate = $row["submission_last_date"];
$judgingDate = $row["judging_date"];
$resultsDate = $row["results_date"];
$resultsReady = $row["results_ready"];
$contestMaxPics = $row["max_pics_per_entry"];
$contestTheme = $row["theme"];
$contestShortDesc = $row["short_description"];
$contestDescription = $row["description"];
$termsConditions = $row["terms_conditions"];
$announcement = $row["announcement"];

// Fetch Sections
$query = mysqli_query($DBCON, "SELECT * FROM section")or die(mysqli_error($DBCON));
$section = array();
$sectionDescription = array();
$sectionRules = array();
$sectionMaxPics = array();
$i = 0;
while ($row = mysqli_fetch_array($query)) {
	$section[$i] = $row["section"];
	$sectionDescription[$i] = $row["description"];
	$sectionRules[$i] = $row["rules"];
	$sectionMaxPics[$i] = $row["max_pics_per_entry"];
	$i += 1;
}
$numSections = $i;

// Fetch Awards
$query = mysqli_query($DBCON, "SELECT * FROM award")or die(mysqli_error($DBCON));
$awardId = array();
$awardSection = array();
$awardName = array();
$awardDescription = array();
$awardNumber = array();
$awardCash = array();
$awardSponsor = array();
$i = 0;
$thereisOverallAward = false;
while ($row = mysqli_fetch_array($query)) {
	$awardId[$i] = $row["award_id"];
	$awardSection[$i] = $row["section"];
	if ($row["section"] == "CONTEST")
		$thereisOverallAward = true;
	$awardName[$i] = $row["name"];
	$awardDescription[$i] = $row["description"];
	$awardNumber[$i] = $row["number"];
	$awardCash[$i] = $row["cash_award"];
	$awardSponsor[$i] = $row["sponsor"];
	$i += 1;
}
$numAwards = $i;

?>