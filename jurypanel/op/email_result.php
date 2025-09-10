<?php
session_start(); 
include("connect.php");

// Formats results related to a section in the form o HTML table with header and separator
function tabulate_awards ($sel_month, $section) {
	global $DBCON;
	$m = "";
	$qaw = mysqli_query($DBCON, "SELECT * FROM ar_award WHERE month = '$sel_month' AND section = '$section' ORDER BY sequence") or die(mysqli_error($DBCON));
	if (mysqli_num_rows($qaw) > 0) {
		if ($section == "CONTEST")
			$m .= "<h2>OVEALL AWARDS</h2>";
		else
			$m .= "<h2>$section</h2>";
		while ($raw = mysqli_fetch_array($qaw)) {
			$aID = $raw["award_id"];
			$awardName = $raw["name"];
			$m .= "<h3>" . $awardName . " (" . $raw['number'] . ")</h3>";
		
			// Retrieve Results or the Award
			$sql = "SELECT * FROM ar_winner ";
			$sql .= " WHERE award_id = '$aID' AND ";
			$sql .= "       month = '$sel_month' "; 
			$sql .= " ORDER BY slno ";
		
			$qres = mysqli_query($DBCON, $sql) or die(mysqli_error($DBCON));
			if (mysqli_num_rows($qres) > 0) {
				// Start Results Table
				$m .= "<table>";
				$m .= "<tr>";
				$m .= "<th align='left'>#</th>";
				$m .= "<th align='left'>Name</th>";
				$m .= "<th align='left'>Email</th>";
				$m .= "<th align='left'>Phone</th>";
				$m .= "<th align='left'>Pic Title</th>";
				$m .= "</tr>";
			
				// Fill Results
				while($rres = mysqli_fetch_array($qres)) {
					$picRanking = $rres['slno'];
					$picOwner = $rres['name'];
					$picEmail = $rres['email'];
					$picPhone = $rres['phone'];
					$picTitle = $rres['title'];
					$picFile = $rres['picfile'];
					$picLocation = $rres['location'];
				
					// Write Result Row
					$m .= "<tr>";
					$m .= "<td>" . $picRanking . "</td>";
					$m .= "<td>" . $picOwner . "</td>";
					$m .= "<td>" . $picEmail . "</td>";
					$m .= "<td>" . $picPhone . "</td>";
					$m .= "<td>" . $picTitle . "</td>";
					$m .= "</tr>";
					$m .= "<tr>";
					$m .= "<td colspan='5'><img src='" . str_replace(" ", "%20", "http://" . $_SERVER['SERVER_NAME'] . "/upload/" . $sel_month . "/" . $picFile) . "'><br><p>" . $picLocation . "</p></td>";
					$m .= "</tr>"; 
				}
				$m .= "</table>";
			}
			$m .= "<br><hr><br>";
		}
	}
	return $m;
}

if(isset($_SESSION['jury_id']) && ($_SESSION['jury_type']=="PROJECTOR" || $_SESSION['jury_type']=="ADMIN" || $_SESSION['jury_type']=="MASTER")) {

	$sel_month = $_REQUEST['sel_month'];
	$err = "";

	/*** Retrieve Results **/
	$msg = "<html>";
	$msg .= "<head>";
	$msg .= "</head>";
	$msg .= "<body>";

	// Print Contest Name
	$query = mysqli_query($DBCON, "SELECT * FROM ar_contest WHERE month = '$sel_month' ") or die(mysqli_error($DBCON));
	$crow = mysqli_fetch_array($query);
	$contest_name = $crow["name"];

	$msg .= "<h1>" . (($crow['results_ready'] == 0) ? "Interim" : "Published") . " Results for the " . $contest_name . " Contest</h1>";

	// First collect Overall Awards
	$msg .= tabulate_awards($sel_month, "CONTEST");

	// Collect section-wise awards
	$qsec = mysqli_query($DBCON, "SELECT * FROM ar_section WHERE month = '$sel_month' ") or die(mysqli_error($DBCON));
	while($rsec = mysqli_fetch_array($qsec))
		$msg .= tabulate_awards($sel_month, $rsec['section']);

	$msg .= "</body>";

	// echo $msg;

	/**** Mailing Function ******/
	if (isset($_REQUEST['email']))
		$to = $_REQUEST['email'];
	else
		$to = "mettursmurali@gmail.com";
	$subject = "Interim Results of " . $contest_name . " Contest";
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: <donotreply@ybsbengaluru.com>' . "\r\n";
	//$headers .= 'Cc: <contactus@ypsbengaluru.com>' . "\r\n";

	if(!mail($to,$subject,$msg,$headers)) 
		$err = "Email Send Failed";

	$_SESSION['send_err']=$err;


	header("Location: " . $_SERVER['HTTP_REFERER']);
	printf("<script>location.href='" . $_SERVER['HTTP_REFERER']."'</script>");
	/***********/	
}
else {
	header("Location: http://" . $_SERVER['SERVER_NAME'] . "/jurypanel/index.php");
	printf("<script>location.href='http://" . $_SERVER['SERVER_NAME'] . "/jurypanel/index.php'</script>");
}	
?>		