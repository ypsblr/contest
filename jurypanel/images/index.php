<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="shortcut icon" href="img/favicon.png">

	<title>Karnataka Tourism Photo Contest</title>

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
	<?php include "master/header.php"; ?>

	<div class="wrapper">
		<!-- Jumbotron -->
		<?php include "master/slideshow.php"; ?>

		<!-- Intro Text -->
		<div class="container intro">
			<div class="row">
				<div class="col-sm-7">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="text-danger">IMPORTANT INSTRUCTIONS</h4>
						</div>
						<div class="panel-body">
							<ol>
								<li>Picture size requirements:<li>
								<ul>
									<li>The <b>Maximum Size</b> for the longer side is <b>1920 pixels</b>.</li>
									<li>The <b>Minimum Size</b> for the longer side is <b>1024 pixels</b>. Any picture less than this size will be rejected.</li>
								</ul>
								<li>Only pictures of heritage monuments in Karnataka would be accepted. At the time of uploading, specify the location of the monument.</li>
								<li>Photographer from any place in India can participate in the contest.</li>
								<li>Since the selected photograph(s) could be displayed as large size hoardings, coffee books, brochures and other media, the Photographer 
									of selected picture will be required to submit a full resolution picture of not less than 4500 pixels on the long side, 
									preferably in 16 Bit TIFF format. Before submitting the picture, please ensure that this requirement can be met.
								</li>
							</ol>
						</div>
					</div>
					<h3 class="text-color"><?=$contestName;?></h3>
					<p class="text-muted text-justify">
						<a href="http://www.karnatakatourism.org" target="_blank"><strong>Department of Tourism</strong></a>, 
						Government of Karnataka in association with
						<a href="http://ypsbengaluru.com" target="_blank"><strong>Youth Photographic Society</strong>&reg;</a>, 
						is pleased to invite you to participate in the <strong><?=$contestName;?></strong>.
						The contest is being organized to celebrate World Tourism Day by recognizing photographers who could bring out the glory of heritage monuments in Karnataka.
					</p>
					<p class="text-muted text-justify">
						Pictures not only speak for themselves but offers inspiration to explore, appreciate, cherish and treasure our rich heritage. 
						Your photographs will show the world, the rich heritage of Karnataka.
					</p>
					<p class="text-muted text-justify">
						Karnataka is home to many of the most spectacular, natural landscapes,  exceptionally marvellous waterfalls, ancient caves and 
						temples and uniquely rich cultural heritage. Along with the natural beauty in the form of landscape, ancient dynasties have 
						added to its beauty with magnificent palaces, architecturally rich temples, majestic dams, monolithic statues, forts and buildings.
					</p>
					<p class="text-muted text-justify">
						The focus of this contest is <strong>Heritage Monuments of Karnataka</strong>. Here are some of the monuments:
					</p>
					<ul>
						<li>Hampi - Vijaya Vittala Temple, Zanana Enclosures...</li>
						<li>Ancient Monuments of Pattadakkal</li>
						<li>Rock Cut Caves at Badami</li>
						<li>Hoysala Architecture at its Best at Belur, Halebidu and Somnathpur</li>
						<li>Places of worship and other monuments of other architectures like Talakkadu</li>
						<li>Massive Jain temples of Shravana Belagola and others places</li>
						<li>Mohal architectural splendors like Ibrahim Rouza and Gol Gumbaz</li>
						<li>Heritage Churches like St. Philomenas Cathedral at Mysore</li>
						<li>Forts that have survived years of battles like Chitradurga Fort and Tippu Fort</li>
						<li>Palaces like Mysore Palace</li>
					</ul>
					<p class="text-muted text-justify">
						The list is not limited to this. The Photographs can cover <strong>any heritage monument so long as it is in Karnataka</strong>.
					</p>
					<h3 class="text-color">Last date to submit pictures is <?php echo date("M j, Y", strtotime($submissionLastDate)); ?></h3>
					<p><?php echo $termsConditions; ?></p>
					<hr>
					<p>The contest is hosted by <a href="http://ypsbengaluru.com" target="_blank">Youth Photographic Society (YPS) Bengaluru</a>. <br><br>
						<a href="http://www.karnatakatourism.org" target="_blank">www.karnatakatourism.org</a><br>
						<a href="http://www.karnatakaholidays.net" target="_blank">www.karnatakaholidays.net</a><br>
						<a href="http://www.junglelodges.com" target="_blank">www.junglelodges.com</a><br>
						<a href="http://www.ypsbengaluru.com" target="_blank">www.ypsbengaluru.com</a><br>
					</p>
				</div>
				<div class="col-sm-5">
					<table class="table table-bordered">
					<thead>
						<tr>
							<th colspan=2 class="text-center"><h3 class="text-color">Awards</h3></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td align="center"><h4 class="text-color">Section</h4></td>
							<td align="center"><h4 class="text-color">Awards</h4></td>
						</tr>
					<?php
						for ($i = 0; $i < $numSections; ++$i) {
					?>
						<tr>
							<td align="center"><strong><?php echo $section[$i]; ?></strong></td>
							<td align="left">
							<?php
								for ($j = 0; $j < $numAwards; $j++)
									if ($awardSection[$j] == $section[$i] && $awardNumber[$j] > 0) {
										echo "<p><strong>" . $awardName[$j] . "</strong> <span class='badge'>" . $awardNumber[$j] . "</span></p>";
										if($awardCash[$j] > 0)
											echo "<p><strong>Cash Award: Rs." . $awardCash[$j] . " each</strong></p>";
										echo "<p>" . $awardDescription[$j] . "</p>";
										echo "<hr>";
									}
							?>
							</td>
						</tr>
					<?php
						}
						$isThereOverallAward = false;
						for ($j = 0; $j < $numAwards; $j++) {
							if ($awardSection[$j] == "CONTEST") {
								$isThereOverallAward = true;
								break;
							}
						}
						if ($isThereOverallAward) {
					?>
						<tr>
							<td align="center">Overall Awards</td>
							<td align="left">
								<ul>
							<?php
								for ($j = 0; $j < $numAwards; $j++)
									if ($awardSection[$j] == "CONTEST")
										echo "<li>" . $awardName[$j] . ($awardCash[$j] > 0 ? " (Rs." . $awardCash[$j] . ")" : "") . "</li>";
										if($awardCash[$j] > 0)
											echo "<p><strong>Cash Award: Rs." . $awardCash[$j] . " each</strong></p>";
										echo "<p>" . $awardDescription[$j] . "</p>";
										echo "<hr>";
							?>
								</ul>
							</td>
						</tr>
					<?php
						}
					?>
					</tbody>
					</table>
					<br>
					<h3 class="text-color">Simple Steps to Participate</h3>
					<p><b>Step 1</b> - <a href="./sign_up.php">Register</a> your details if you are participating in the contest for the first time. 
						If you are already registered, you can directly go for <a href="./sign_in.php">Upload</a>. </p>
					<p><b>Step 2</b> - <a href="./sign_in.php">Upload</a> your images. Enter your registered Email and Password, if requested, to login.
						You can upload one picture at a time. Thumbnails of your uploaded pictures will be shown on the right side. After submitting the 
						maximum allowed number of pictures, you will not be allowed to upload any pictures.</p>
					<p><b>Substituting an Uploaded Picture</b> - If you want to change any picture already submitted, click on the <i class="ace-icon fa fa-trash"></i> 
						icon below the picture that you want to substitute. This will remove that picture from your list and you will be allowed to upload another picture.</p>
					<br>
					<h3 class="text-color">Past Contests</h3>
					<ul>
					<?php
						$month = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
						$qcon = mysqli_query($DBCON, "SELECT * FROM ar_contest ORDER BY month DESC") or die(mysqli_error($DBCON));
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
					?>
						<li><a href="/archives.php?month=<?php echo $contestMonth;?>"><?php echo $month[$contestMM-1] . " " . $contestYY . ' - ' . $contestName; ?></a></li>
					<?php
						}
					?>
					</ul>
					<p><small>* Click the links see the results</small></p>
				</div>
			</div> <!-- / .row -->
			<div class="divider"></div>
		</div>
	</div>
	<!-- Footer -->

	<?php include"master/footer1.php"; ?>
	<!-- Style Toggle -->
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