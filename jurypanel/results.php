<?php 
session_start();
include("op/connect.php");

function orderSuffix($i) {
   $str = "$i";
   $t = $i > 9 ? substr($str,-2,1) : 0;
   $u = substr($str,-1);
   if ($t==1) 
	   return $str . 'th';
   else 
		switch ($u) {
			case 1: return $str . 'st';
			case 2: return $str . 'nd';
			case 3: return $str . 'rd';
			default: return $str . 'th';
		}
}
function encode_string_array ($stringArray) {
	$s = strtr(base64_encode(addslashes(gzcompress(serialize($stringArray),2))), '+/=', '_,');
   
	return $s;
}	
function decode_string_array ($stringArray) {
	$s = unserialize(gzuncompress(stripslashes(base64_decode(strtr($stringArray, '_,', '+/=')))));
	return $s;
}

function sql_error($sql, $errmsg, $phpfile, $phpline) {
	file_put_contents("sqldump.txt", "SQL operation failed with message '$errmsg' in line $phpline of '$phpfile'" . chr(13) . chr(10), FILE_APPEND);
	file_put_contents("sqldump.txt", "SQL: " . $sql . chr(13) . chr(10), FILE_APPEND);
	die("SQL Error: ". $errmsg);
}

							
if(isset($_SESSION['jury_id']) && ($_SESSION['jury_type']=="MASTER" || $_SESSION['jury_type']=="ADMIN")) {
	$session_id=$_SESSION['jury_id'];
	$user_query = mysqli_query($DBCON, "SELECT * FROM user WHERE user_id = '$session_id'") or die(mysqli_error($DBCON));
	$user_row = mysqli_fetch_array($user_query);
	$jury_login = $user_row['login'];
	$jury_name = $user_row['name'];
	$jury_pic = $user_row['avatar'];
	$user_type = $user_row['type'];

	if (isset($_REQUEST['archive']) && $_REQUEST['archive'] == 'true') {
		// Archiving Results
		// 
		$query = mysqli_query($DBCON, "SELECT * FROM contest") or die(mysqli_error($DBCON));
		$contest = mysqli_fetch_array($query);
		$archive_month = substr($contest['submission_start_date'], 0, 4) . substr($contest['submission_start_date'], 5, 2);
		
		// Check if meaningful results are available
		$query = mysqli_query($DBCON, "SELECT * FROM result, pic, entry WHERE result.pic_id = pic.pic_id AND pic.entry_id = entry.entry_id");
		if (mysqli_num_rows($query) > 0) {
			// Delete existing archive records
			$sql = "SELECT * FROM ar_contest WHERE month = '$archive_month' ";
			$query = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
			if (mysqli_num_rows($query) > 0) {
				$sql = "DELETE FROM ar_contest WHERE month = '$archive_month' ";
				mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
				$sql = "DELETE FROM ar_section WHERE month = '$archive_month' ";
				mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
				$sql = "DELETE FROM ar_award WHERE month = '$archive_month' ";
				mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
				$sql = "DELETE FROM ar_jury WHERE month = '$archive_month' ";
				mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
				$sql = "DELETE FROM ar_winner WHERE month = '$archive_month' ";
				mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
			}

			// Create archives
			$sql = "SELECT count(*) AS num_entries FROM entry WHERE entry_id IN (SELECT entry_id FROM pic)";
			$query = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
			$res = mysqli_fetch_array($query);
			$num_entries = $res['num_entries'];
			
			$sql = "SELECT count(*) AS num_pictures FROM pic WHERE entry_id IN (SELECT entry_id FROM entry)";
			$query = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
			$res = mysqli_fetch_array($query);
			$num_pictures = $res['num_pictures'];
			
			// ar_contest
			$sql  = "INSERT INTO ar_contest (month, name, submission_start_date, submission_last_date, judging_date, results_date, results_ready, theme, description, num_entries, num_pictures) ";
			$sql .= " SELECT '$archive_month', name, submission_start_date, submission_last_date, judging_date, results_date, 0, theme, description, '$num_entries', '$num_pictures' FROM contest ";
			mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
			
			// ar_section
			$sql  = "INSERT INTO ar_section (month, section, description, num_entries, num_pictures) ";
			$sql .= "SELECT '$archive_month', section, description, 0, 0 FROM section ";
			mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
			
			$sql = "SELECT * FROM ar_section WHERE month = '$archive_month' ";
			$query = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
			while ($res = mysqli_fetch_array($query)) {
				$this_section = $res['section'];
				$sql = "SELECT count(*) as num_entries FROM entry WHERE entry_id IN (SELECT DISTINCT entry_id FROM pic WHERE section = '$this_section') ";
				$qq = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
				$rr = mysqli_fetch_array($qq);
				$num_entries = $rr['num_entries'];
				
				$sql = "SELECT COUNT(*) AS num_pictures FROM pic, entry WHERE section = '$this_section' AND pic.entry_id = entry.entry_id ";
				$qq = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
				$rr = mysqli_fetch_array($qq);
				$num_pictures = $rr['num_pictures'];
				
				$sql = "UPDATE ar_section SET num_entries = '$num_entries', num_pictures = '$num_pictures' WHERE section = '$this_section' ";
				mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
			}
			
			//ar_award
			$sql  = "INSERT INTO ar_award (month, award_id, section, sequence, name, description, number, cash_award, sponsor) ";
			$sql .= "SELECT '$archive_month', award_id, section, sequence, name, description, number, cash_award, sponsor FROM award ";
			mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
			
			//ar_jury
			$sql  = "INSERT INTO ar_jury (month, seq, name, picture, avatar, title, honors, profile) ";
			$sql .= "   SELECT '$archive_month', jurynumber, name, picture, avatar, title, honors, profile ";
			$sql .= "   FROM assignment, user ";
			$sql .= "   WHERE assignment.user_id = user.user_id ";
			mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);

			// ar_winner
			$sql  = "INSERT INTO ar_winner (month, award_id, slno, name, honors, phone, email, title, location, picfile) ";
			$sql .= "   SELECT '$archive_month', award_id, ranking, name, honors, phone, email, title, location, picfile ";
			$sql .= "   FROM result, pic, entry ";
			$sql .= "   WHERE result.pic_id = pic.pic_id ";
			$sql .= "     AND pic.entry_id = entry.entry_id ";
			mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);

			// Create Directories
			if (! is_dir("../upload/" . $archive_month))
				mkdir("../upload/" . $archive_month, 0755);
			if (! is_dir("../upload/" . $archive_month . "/tn"))
				mkdir("../upload/" . $archive_month . "/tn", 0755);
			if (! is_dir("../upload/" . $archive_month . "/jury"))
				mkdir("../upload/" . $archive_month . "/jury", 0755);
			
			// Copy Jury Profiles
			$sql = "SELECT * FROM ar_jury WHERE month = '$archive_month' ";
			$query = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
			while ($res = mysqli_fetch_array($query)) {
				$src = "./images/" . $res['picture'];
				$dst = "../upload/" . $archive_month . "/jury/" . $res['picture'];
				copy($src, $dst);
				$src = "./images/" . $res['avatar'];
				$dst = "../upload/" . $archive_month . "/jury/" . $res['avatar'];
				copy($src, $dst);
			}
			
			// Copy Winning Pictures
			$sql  = "SELECT * FROM ar_winner, ar_award ";
			$sql .= " WHERE ar_winner.month = '$archive_month' ";
			$sql .= "   AND ar_award.month = '$archive_month' ";
			$sql .= "   AND ar_winner.award_id = ar_award.award_id ";
			$query = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
			while ($res = mysqli_fetch_array($query)) {
				$src = "../upload/" . $res['section'] . "/" . $res['picfile'];
				$dst = "../upload/" . $archive_month . "/" . $res['picfile'];
				copy($src, $dst);
				$src = "../upload/" . $res['section'] . "/tn/" . $res['picfile'];
				$dst = "../upload/" . $archive_month . "/tn/" . $res['picfile'];
				copy($src, $dst);
			}

			// END
		}
		
	} // Archive Ends

	// Publish Results
	if(isset($_REQUEST['publish_result']) && isset($_REQUEST['sel_month'])) {
		$sql = "UPDATE ar_contest SET results_ready = '1' WHERE month = '" . $_REQUEST['sel_month'] . "' ";
		mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
		$sql  = "UPDATE contest, ar_contest ";
		$sql .= "SET contest.results_ready = 1 ";
		$sql .= "WHERE ar_contest.month = '" . $_REQUEST['sel_month'] . "' ";
		$sql .= "  AND contest.name = ar_contest.name ";
		mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
	}
	
	
	if (isset($_REQUEST['sel_month'])) {
		// Show Results for the month passed as parameter
		$sel_month = $_REQUEST['sel_month'];
		$sql = "SELECT * FROM ar_contest WHERE month = '$sel_month'";
		$qtmp = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
		$rtmp = mysqli_fetch_array($qtmp);
		$contest_name = $rtmp['name'];
		$contest_theme = $rtmp['theme'];
		$contest_num_entries = $rtmp['num_entries'];
		$contest_num_pictures = $rtmp['num_pictures'];
		$results_ready = $rtmp['results_ready'];
	}
	else {
		// Show Latest Results
		$sql = "SELECT * FROM ar_contest WHERE month = (SELECT MAX(month) FROM ar_contest)";
		$qtmp = mysqli_query($DBCON, $sql) or sql_error($sql, mysqli_error($DBCON), __FILE__, __LINE__);
		$rtmp = mysqli_fetch_array($qtmp);
		$sel_month = $rtmp['month'];
		$contest_name = $rtmp['name'];
		$contest_theme = $rtmp['theme'];
		$contest_num_entries = $rtmp['num_entries'];
		$contest_num_pictures = $rtmp['num_pictures'];
		$results_ready = $rtmp['results_ready'];
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title>Youth Photographic Society | Jury Panel</title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!--<link rel="shortcut icon" type="image/ico" href="favicon.ico" />-->

    <!-- Vendor styles -->
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="vendor/metisMenu/dist/metisMenu.css" />
    <link rel="stylesheet" href="vendor/animate.css/animate.css" />
    <link rel="stylesheet" href="vendor/bootstrap/dist/css/bootstrap.css" />
    <link rel="stylesheet" href="vendor/datatables.net-bs/css/dataTables.bootstrap.min.css" />

    <!-- App styles -->
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/helper.css" />
    <link rel="stylesheet" href="styles/style.css">
</head>

<body class="fixed-navbar fixed-sidebar">

	<!-- Simple splash screen-->
	<div class="splash"> 
		<div class="color-line"></div>
		<div class="splash-title">
			<h1>    YOUTH PHOTOGRAPHIC SOCIETY | JURY/ADMIN PANEL  </h1>
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

	<!-- Header -->
	<?php 
		include "master/master_header.php";
		include "master/master_sidebar.php";
	?>

	<!-- Wrapper -->
	<div id="wrapper">

		<!-- Page Header -->
		<div class="content animate-panel">
			<div class="row">
				<div class="col-lg-12 text-center m-t-md">
					<h2>
						<?php echo ($results_ready == 0) ? "Interim" : "Published";?> Results
					</h2>
				</div>
			</div>
			<div class="row">
				<div class="hpanel">
					<div class="panel-body">
						<!-- Email results -->
						<form action="results.php" id="loginForm" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-lg-4">
									<label>Select Month</label>
									<select class="form-control" name="sel_month" id="sel_month" required>
								<?php
									$qtmp = mysqli_query($DBCON, "SELECT * FROM ar_contest ORDER BY month") or die(mysqli_error($DBCON));
									while ($rtmp = mysqli_fetch_array($qtmp)) {
								?>
										<option value="<?php echo $rtmp['month'];?>" <?php echo ($rtmp['month'] == $sel_month) ? "selected" : ""; ?>><?php echo $rtmp['month'] . ": " . $rtmp['name'];?></option>
								<?php
									}
								?>
									</select>
								</div>
								<div class="form-group col-lg-4">
									<button class="btn btn-success" type="submit" name="show_result">Show Results</button>
								</div>
							</div>
						</form>
						<form action="op/email_result.php" id="loginForm" method="post" enctype="multipart/form-data">
							<input type="hidden" name="sel_month" value="<?php echo $sel_month;?>" >
							<div class="row">
								<div class="form-group col-lg-8">
									<input type="email" class="form-control" name="email" id="email" placeholder="Enter Email..." required>
								</div>
								<div class="form-group col-lg-4">
									<button class="btn btn-success" type="submit" name="send_result">Email Results for <?php echo $sel_month;?></button>
								</div>
							</div>
						</form>
						<?php
							if($results_ready == 0) {
						?>
						<form action="results.php" id="loginForm" method="post" enctype="multipart/form-data">
							<input type="hidden" name="sel_month" value="<?php echo $sel_month;?>" >
							<div class="row">
								<div class="form-group col-lg-4">
									<button class="btn btn-success" type="submit" name="publish_result">Publish Results for <?php echo $sel_month;?></button>
								</div>
							</div>
						</form>
						<?php
							}
						?>
					</div>
				</div>
			</div>

			<!-- Award List -->
			<div class="row">
				<div class="col-sm-12 col-md-12 col-lg-12">
					<div class="hpanel">
						<div class="panel-body">
							<!-- Overall Statistics -->
							<table class="table">
								<tr class="info"><td><h2><?php echo $sel_month . " - " . $contest_name;?></h2></td></tr>
								<tr class="info"><td><?php echo $contest_theme;?></td></tr>
								<tr class="info"><td><?php echo $contest_num_pictures;?> Pictures from <?php echo $contest_num_entries;?> Participants</td></tr>
							</table>
							<!-- Display CONTEST Level Awards -->
							<?php
								$qaw = mysqli_query($DBCON, "SELECT * FROM ar_award WHERE month = '$sel_month' AND section = 'CONTEST' ORDER BY sequence") or die(mysqli_error($DBCON));
								if (mysqli_num_rows($qaw) > 0) {
							?>
							<h2 class="primary-font">OVERALL AWARDS</h2>
							<?php
								while ($raw = mysqli_fetch_array($qaw)) {
									$aID = $raw["award_id"];
									$sql = "SELECT * FROM ar_winner ";
									$sql .= " WHERE award_id = '$aID' ";
									$sql .= "   AND month = '$sel_month' ";
									$sql .= " ORDER BY slno ";
									$qres = mysqli_query($DBCON, $sql) or die(mysqli_error($DBCON));
									if (mysqli_num_rows($qres) > 0) {
							?>
							<table class="table">
								<tr class="info">
									<td><h3><?php echo $raw["name"];?></h3></td>
								</tr>
							</table>
							<?php
									while($rres = mysqli_fetch_array($qres)) {
										$picTitle = $rres['title'];
										$picLocation = $rres['location'];
										$picOwner = $rres['name'];
										$picFile = $rres['picfile'];
										$honors = $rres['honors'];
										$email = $rres['email'];
										$phone = $rres['phone'];
										$picRanking = $rres['slno'];
							?>
							<div class="col-sm-12 col-md-12 col-lg-12">
								<h4 class="primary-font"><?php echo "[" . $picRanking . "] " . $picTitle . ' <small>by</small> ' . $picOwner . (($honors == "") ? "" : ('<small>, ' . $honors . '</small>')); ?></h4>
								<p class="primary-font">Email: <?php echo $email;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Phone: <?php echo $phone; ?></p>
								<img class="img-responsive" src="/upload/<?php echo $sel_month . '/' . $picFile;?>" alt="#" />
								<hr>
							</div>
							<?php
											}
										}
									}
								}
							?>

						<!-- Display Awards by Section -->
							<?php
								$qsec = mysqli_query($DBCON, "SELECT * FROM ar_section WHERE month = '$sel_month' ") or die(mysqli_error($DBCON));
								while ($rsec = mysqli_fetch_array($qsec)) {
									$curSection = $rsec['section'];
									$sectionNumEntries = $rsec['num_entries'];
									$sectionNumPictures = $rsec['num_pictures'];
									$qaw = mysqli_query($DBCON, "SELECT * FROM ar_award WHERE month='$sel_month' AND section = '$curSection' ORDER BY sequence") or die(mysqli_error($DBCON));
									if (mysqli_num_rows($qaw) > 0) {
							?>
							<h2 class="primary-font">
								<?php echo $curSection; ?>
								<small><small> ( <?php echo $sectionNumPictures;?> pictures from <?php echo $sectionNumEntries;?> participants )</small></small>
							</h2>
							<?php
										while ($raw = mysqli_fetch_array($qaw)) {
											$aID = $raw["award_id"];
											$sql = "SELECT * FROM ar_winner ";
											$sql .= " WHERE award_id = '$aID' AND ";
											$sql .= "       month = '$sel_month' ";
											$sql .= " ORDER BY slno ";
											$qres = mysqli_query($DBCON, $sql) or die(mysqli_error($DBCON));
											if (mysqli_num_rows($qres) > 0) {
							?>
							<table class="table">
								<tr class="info">
									<td><h3><?php echo $raw["name"];?></h3></td>
								</tr>
							</table>
							<?php
												while($rres = mysqli_fetch_array($qres)) {
													$picTitle = $rres['title'];
													$picOwner = $rres['name'];
													$picLocation = $rres['location'];
													$picFile = $rres['picfile'];
													$honors = $rres['honors'];
													$email = $rres['email'];
													$phone = $rres['phone'];
													$picRanking = $rres['slno'];
							?>
							<div class="col-sm-12 col-md-12 col-lg-12">
								<h4 class="primary-font"><?php echo "[" . $picRanking . "] " . $picTitle . ' <small>by</small> ' . $picOwner . (($honors == "") ? "" : ('<small>, ' . $honors . '</small>')); ?></h4>
								<p>Email: <?php echo $email;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Phone: <?php echo $phone;?> </p>
								<img class="img-responsive" src="/upload/<?php echo $sel_month . '/' . $picFile;?>" alt="#" />
								<p><small><?php echo $picLocation; ?></small></p>
								<hr>
							</div>
							<?php
												}
											}
										}
									}
								}
							?>
							<hr>
						</div>
					</div>
				</div>
			</div> <!-- / .row -->
		</div> <!-- / .container -->
	<?php 
		include("master/footer.php");
		include("master/profile_modal.php");
	?>		
	</div>	<!-- / .wrapper -->

<!-- JavaScript
================================================== -->

<!-- Vendor scripts -->
<script src="vendor/jquery/dist/jquery.min.js"></script>
<script src="vendor/jquery-ui/jquery-ui.min.js"></script>
<script src="vendor/slimScroll/jquery.slimscroll.min.js"></script>
<script src="vendor/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="vendor/metisMenu/dist/metisMenu.min.js"></script>
<script src="vendor/iCheck/icheck.min.js"></script>
<script src="vendor/sparkline/index.js"></script>
<!-- DataTables -->
<script src="vendor/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="vendor/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- DataTables buttons scripts -->
<script src="vendor/pdfmake/build/pdfmake.min.js"></script>
<script src="vendor/pdfmake/build/vfs_fonts.js"></script>
<script src="vendor/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="vendor/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="vendor/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="vendor/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
<!-- App scripts -->
<script src="scripts/homer.js"></script>


<script>

    $(function () {

        // Initialize Example 1
        $('#example1').dataTable( {
            "ajax": 'api/datatables.json',
            dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
            buttons: [
                {extend: 'copy',className: 'btn-sm'},
                {extend: 'csv',title: 'ExampleFile', className: 'btn-sm'},
                {extend: 'pdf', title: 'ExampleFile', className: 'btn-sm'},
                {extend: 'print',className: 'btn-sm'}
            ]
        });

        // Initialize Example 2
        $('#example2').dataTable();

    });

</script>

</body>
</html>
<?php
}
else
{
$_SESSION['signin_msg'] = "Use ID with require permission not found !";
header("Location: /jurypanel/index.php");
printf("<script>location.href='/jurypanel/index.php'</script>");
}

?>