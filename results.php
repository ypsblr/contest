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
					<h1 class="text-center"><?php echo $contestName; ?></h1>
				</div>
			</div> <!-- / .row -->
		</div> <!-- / .container -->
	</div> <!-- / .color-jumbotron -->
	
	<?php 
		if ($resultsReady) { 
	?>

	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-12 col-lg-12">
				<h1 class="primary-font">Congratulations to all the Winners</h1>
			<?php
				$qtmp = mysqli_query($DBCON, "SELECT count(*) AS num_entries FROM entry WHERE entry_id IN (SELECT entry_id FROM pic)") or die(mysqli_error($DBCON));
				$rtmp = mysqli_fetch_array($qtmp);
				$contestNumEntries = $rtmp['num_entries'];
				
				$qtmp = mysqli_query($DBCON, "SELECT count(*) AS num_pictures FROM pic WHERE entry_id IN (SELECT entry_id FROM entry)") or die(mysqli_error());
				$rtmp = mysqli_fetch_array($qtmp);
				$contestNumPictures = $rtmp['num_pictures'];
			?>
				<p>Your pictures have been selected out of <?php echo $contestNumPictures;?> pictures received from <?php echo $contestNumEntries;?> participants.</p>
				<hr>
				<!-- Display CONTEST Level Awards -->
			<?php
				$qaw = mysqli_query($DBCON, "SELECT * FROM award WHERE section = 'CONTEST' ORDER BY sequence") or die(mysqli_error($DBCON));
				if (mysqli_num_rows($qaw) > 0) {
			?>
				<h2 class="primary-font">OVERALL AWARDS</h2>
			<?php
					while ($raw = mysqli_fetch_array($qaw)) {
						$aID = $raw["award_id"];
						$sql = "SELECT * FROM result, pic, entry ";
						$sql .= " WHERE result.award_id = '$aID' AND ";
						$sql .= "       result.pic_id = pic.pic_id AND ";
						$sql .= "       pic.entry_id = entry.entry_id ";
						$sql .= " ORDER BY ranking, pic.pic_id ";
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
								$picSection = $rres['section'];
								$picTitle = $rres['title'];
								$picOwner = $rres['name'];
								$picFile = $rres['picfile'];
								$honors = $rres['honors'];
			?>
				<div class="col-sm-12 col-md-12 col-lg-12">
					<h4 class="primary-font"><?php echo $picTitle . ' <small>by</small> ' . $picOwner . (($honors == "") ? "" : ('<small>, ' . $honors . '</small>')); ?></h4>
					<img src="/upload/<?php echo $picSection . '/' . $picFile;?>" alt="#" />
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
				for ($isec = 0; $isec < $numSections; $isec++) {
					$curSection = $section[$isec];
					$qaw = mysqli_query($DBCON, "SELECT * FROM award WHERE section = '$curSection' ORDER BY sequence") or die(mysqli_error($DBCON));
					if (mysqli_num_rows($qaw) > 0) {
						$qtmp = mysqli_query($DBCON, "SELECT count(*) AS num_entries FROM entry WHERE entry_id IN (SELECT entry_id FROM pic WHERE section='$curSection')") or die(mysqli_error($DBCON));
						$rtmp = mysqli_fetch_array($qtmp);
						$sectionNumEntries = $rtmp['num_entries'];
				
						$qtmp = mysqli_query($DBCON, "SELECT count(*) AS num_pictures FROM pic WHERE section = '$curSection' AND entry_id IN (SELECT entry_id FROM entry)") or die(mysqli_error($DBCON));
						$rtmp = mysqli_fetch_array($qtmp);
						$sectionNumPictures = $rtmp['num_pictures'];
			?>
				<h2 class="primary-font">
					<?php echo $curSection; ?>
					<small><small> ( <?php echo $sectionNumPictures;?> pictures from <?php echo $sectionNumEntries;?> participants )</small></small>
				</h2>
			<?php
						while ($raw = mysqli_fetch_array($qaw)) {
							$aID = $raw["award_id"];
							$sql = "SELECT * FROM result, pic, entry ";
							$sql .= " WHERE result.award_id = '$aID' AND ";
							$sql .= "       result.pic_id = pic.pic_id AND ";
							$sql .= "       pic.entry_id = entry.entry_id ";
							$sql .= " ORDER BY ranking, pic.pic_id ";
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
									$picSection = $rres['section'];
									$picTitle = $rres['title'];
									$picOwner = $rres['name'];
									$picLocation = $rres['location'];
									$picFile = $rres['picfile'];
									$honors = $rres['honors'];
			?>
				<div class="col-sm-12 col-md-12 col-lg-12" >
					<h4 class="primary-font"><?php echo $picTitle . ' <small>by</small> ' . $picOwner . (($honors == "") ? "" : ('<small>, ' . $honors . '</small>')); ?></h4>
					<img src="/upload/<?php echo $picSection . '/' . $picFile;?>" alt="#" width="1024" />
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
				<h2>Thanks to all the Participants</h2>
			</div>
		</div> <!-- / .row -->
	</div> <!-- / .container -->
	<?php
	}
	else {
		echo "<h2 style='text-align: center;'>Results are yet to be announced ! Please visit again later !</h2>";
	}
	?>
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