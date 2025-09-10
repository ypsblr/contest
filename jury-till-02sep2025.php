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
	<?php include "master/header.php"; ?>

	<div class="wrapper">
		<?php include "master/slideshow.php"; ?>

		<!-- Jury -->
		<div class="container intro">
			<h3 class="headline text-color">
				<span class="border-color">Jury Panel</span>
			</h3>
			<div class="row portfolio">
			<?php
				$qchk = mysqli_query($DBCON, "SELECT * FROM user, assignment, section WHERE type = 'JURY' AND user.user_id = assignment.user_id AND assignment.section = section.section ORDER BY jurynumber") or die(mysqli_error($DBCON));
				while ($row = mysqli_fetch_array($qchk)) {
					$userName = $row['name'];
					$userPic = $row['picture'];
					$userAvatar = $row['avatar'];
					$userTitle = $row['title'];
					$userHonors = $row['honors'];
					$userProfile = $row['profile'];
			?>
				<div class="col-sm-4">
					<div class="portfolio-item">
						<img src="/jurypanel/images/<?php echo $userPic; ?>" alt="<?php echo $userName;?>" >
						<div class="portfolio-desc">
							<h3 class="primary-font"><?php echo $userName;?></h3>
							<p class="text-muted"><?php echo $userHonors; ?></p>
							<p class="text-muted"><?php echo $userTitle; ?></p>
							<br>
							<p class="text-muted text-justify"><?php echo $userProfile; ?></p>
						</div>
					</div>
				</div>
			<?php
				}
			?>
			</div> <!-- row -->
			<div class="divider"></div>
		</div> <!-- container -->
 		<!-- / Jury -->		
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