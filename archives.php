<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="shortcut icon" href="img/favicon.png">

	<title>MAHE Photography Awards</title>

	<!-- CSS Global -->
	<link href="css/bootstrap/bootstrap.min.css" rel="stylesheet">

	<!-- CSS Plugins -->
	<link href="fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link href="css/animate.css" rel="stylesheet">

	<!-- Google Fonts -->
	<link href='http://fonts.googleapis.com/css?family=Oswald:400,700,300' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,700,500,400italic,500italic,700italic' rel='stylesheet' type='text/css'>

	<!-- CSS Custom -->
	<link href="css/color-styles.css" rel="stylesheet">
	<link href="css/ui-elements.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>

<body class="body-blue">

	<!-- Extra Bar -->

<?php include "master/header.php";?>

	<!-- Wrapper -->
<div class="wrapper">
	<!-- Topic Header -->
	<!-- Jumbotron -->
	<?php include "master/slideshow.php"; ?>

	<div class="color-jumbotron">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<h1 class="text-center">Mahe Photo Awards Archives</h1>
				</div>
			</div> <!-- / .row -->
		</div> <!-- / .container -->
	</div> <!-- / .color-jumbotron -->

	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-12 col-lg-12">
				<h1 class="primary-font">Congratulations to all the Winners</h1>
				<p>Karnataka Tourism Department, in association with Youth Photographic Society had been holding Photography Contests
					to promote tourism and encourage photographers. The first of the contests was held in September 2016 to celebrate
					World Tourism Day 2016. From January 2017, these contests are being held every month. The photographs from the past
					contests are available in this page. Click on the Contest to see the details of winners and to enjoy winning images.
				</p>
				<div class="panel-group" id="accordian">
					
			<?php
				$month = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
				$qcon = mysqli_query($DBCON, "SELECT * FROM ar_contest WHERE results_ready != 0 ORDER BY month DESC") or die(mysqli_error($DBCON));
				$first = true;
				while ($rcon = mysqli_fetch_array($qcon)) { // CONTEST-BEGIN
					$contestMonth = $rcon['month'];
					$contestMM = $contestMonth % 100;
					$contestYY = ($contestMonth - $contestMM) / 100;

					$contestName = $rcon['name'];
					$contestTheme = $rcon['theme'];
					$contestDescription = $rcon['description'];
					$contestNumEntries = $rcon['num_entries'];
					$contestNumPictures = $rcon['num_pictures'];
					
					if (isset($_REQUEST['month'])) {
						if ($_REQUEST['month'] == $contestMonth)
							$expand = "in";
						else
							$expand = "";
					}
					else {
						if ($first) {
							$first = false;
							$expand = "in";
						}
						else
							$expand = "";
					}
			?>
					<!-- Contest Level -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordian" href="#contest<?php echo $contestMonth;?>" >
									<h1 class="primary-font">
										<?php echo $month[$contestMM-1] . " " . $contestYY . ' - ' . $contestName; ?>
										<h6> ( <?php echo $contestNumPictures;?> pictures from <?php echo $contestNumEntries;?> participants )</h6>
									</h1>
								</a>
							</h4>
						</div>
						<div id="contest<?php echo $contestMonth;?>" class="panel-collapse collapse <?php echo $expand; ?> ">
							<div class="panel-body">
								<p><b>Theme:</b> <?php echo $contestTheme;?></p>
								<p><?php echo $contestDescription;?></p>
				<!-- Jury Information -->
								<h3 class="primary-font">Jury Panel</h3>
								<div class="row">
			<?php
					$qjury = mysqli_query($DBCON, "SELECT * FROM ar_jury WHERE month='$contestMonth' ORDER BY seq") or die(mysqli_error($DBCON));
					while ($rjury = mysqli_fetch_array($qjury)) { // JURY-BEGIN
						$juryName = $rjury['name'];
						$juryTitle = $rjury['title'];
						$juryHonors = $rjury['honors'];
						$juryProfile = $rjury['profile'];
						$juryPic = $rjury['picture'];
						$juryAvatar = $rjury['avatar'];
			?>
									<div class="col-sm-4 col-md4 col-lg-4">
										<div class="portfolio-item">
											<img src="/upload/<?php echo $contestMonth . '/jury/' . $juryPic; ?>" alt="<?php echo $juryName;?>" >
											<div class="portfolio-desc">
												<h3 class="primary-font"><?php echo $juryName;?></h3>
												<p class="text-muted"><?php echo $juryHonors; ?></p>
												<p class="text-muted"><?php echo $juryTitle; ?></p>
											</div>
										</div>
									</div>
			<?php
					} // JURY-END
			?>
								</div> <!-- row -->
								<div class="row">

					<!-- Display Awards by Section -->
			<?php
					$qsec = mysqli_query($DBCON, "SELECT * FROM ar_section WHERE month = '$contestMonth'") or die(mysqli_error($DBCON));
					while ($rsec = mysqli_fetch_array($qsec)) {
						$secSection = $rsec['section'];
						$secDescription = $rsec['description'];
						$secNumEntries = $rsec['num_entries'];
						$secNumPictures = $rsec['num_pictures'];
			?>
									<div class="col-sm-12 col-md-12 col-lg-12">
										<h2 class="primary-font">
											<?php echo $secSection; ?>
											<small><small>( <?php echo $secNumPictures;?> pictures from <?php echo $secNumEntries;?> participants )</small></small>
										</h2>
										<p><?php echo $secDescription;?></p>
									</div>
			<?php
						$qaw = mysqli_query($DBCON, "SELECT * FROM ar_award WHERE month='$contestMonth' AND section = '$secSection' ORDER BY sequence") or die(mysqli_error($DBCON));
						while ($raw = mysqli_fetch_array($qaw)) { // AWARD-BEGIN
							$aID = $raw["award_id"];
							$aName = $raw['name'];
							$aDescription = $raw['description'];
							$aNumberOfAwards = $raw['number'];
							$aCashAward = $raw['cash_award'];
			?>
									<div class="col-sm-12 col-md-12 col-lg-12">
										<table class="table">
											<tr class="info">
												<td><h3><?php echo $aName;?></h3></td>
											</tr>
										</table>
									</div>
			<?php
							$qwin = mysqli_query($DBCON, "SELECT * FROM ar_winner WHERE month='$contestMonth' AND award_id='$aID' ORDER BY slno") or die(mysqli_error($DBCON));
							while($rwin = mysqli_fetch_array($qwin)) { // WINNER-BEGIN
								$winTitle = $rwin['title'];
								$winOwner = $rwin['name'];
								$winLocation = $rwin['location'];
								$winPicFile = $rwin['picfile'];
			?>
									<div class="col-sm-12 col-md-12 col-lg-12">
										<h4 class="primary-font"><?php echo $winTitle . ' <small>by</small> ' . $winOwner; ?></h4>
										<img src="/upload/<?php echo $contestMonth . '/' . $winPicFile;?>" alt="#" width="1024" />
										<p><small><?php echo $winLocation; ?></small></p>
										<hr>
									</div>
			<?php
							} // WINNER-END
						} // AWARD-END
					} // SECTION-END 
			?>
								</div>
							</div>
						</div>
					</div>
			<?php
				} // CONTEST-END
			?>
				</div> <!-- END Accordian -->
				<hr>
				<h2>Thanks to all the Participants</h2>
			</div>
		</div> <!-- / .row -->
	</div> <!-- / .container -->
</div>	<!-- / .wrapper -->

<?php include"master/footer1.php";?>
<?php include "style_tool.php"; ?>

<!-- JavaScript
================================================== -->

<!-- JS Global -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>

<!-- JS Plugins -->
<script src="js/scrolltopcontrol.js"></script>
<script src="js/jquery.sticky.js"></script>

<!-- JS Custom -->
<script src="js/custom.js"></script>

</body>
</html>