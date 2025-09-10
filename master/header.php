<?php include "master/topbar.php";?>

<!-- Google Analytics -->
<?php include_once "master/ga.php"; ?>

<!-- Get contest information from DB -->
<?php include "op/get_contest.php";	?>

<?php date_default_timezone_set("Asia/Calcutta"); ?>

<?php
function print_date($ymd_date, $format = "l jS F, Y") {
	return date($format, strtotime($ymd_date));
}
?>

<div class="navbar navbar-static-top navbar-white" role="navigation">
	<div class="container">

		<!-- Navbar Header -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand1" href="index.php"> <img src="img/logo.png" style="height:74px;"></a>
			&nbsp;&nbsp;
			<a class="navbar-brand1" href="index.php"> <img src="img/yps.jpg" style="height:74px;"></a>
		</div> <!-- / Navbar Header -->

		<!-- Navbar Links -->
		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="index.php" class="bg-hover-color">Home</a></li>
				<?php
					if ($resultsReady) {
				?>
				<li><a href="results.php" class="bg-hover-color">Results</a></li>
				<?php
					}
					if (date("Y-m-d") <= $submissionLastDate) {
				?>
				<li><a href="sign_up.php" class="bg-hover-color">Register</a></li>
				<li><a href="sign_in.php" class="bg-hover-color">Upload</a></li>
				<?php
					}
				?>
				<?php
					$qchk = mysqli_query($DBCON, "SELECT * FROM assignment, section WHERE assignment.section = section.section");
					if (mysqli_num_rows($qchk) > 0) {
				?>
				<li><a href="jury.php" class="bg-hover-color">Jury</a></li>
				<?php
					}
				?>
				<li><a href="term_condition.php" class="bg-hover-color">Contest Rules</a></li>
				<!--<li><a href="archives.php" class="bg-hover-color">Hall of Fame</a></li>-->
				<li><a href="contact_us.php" class="bg-hover-color">Contact Us</a></li>
			</ul>

			<!-- Search Form (xs) -->
			<form class="navbar-form navbar-left visible-xs" role="search">
				<div class="form-group">
					<input type="text" class="form-control" placeholder="Search">
				</div>
				<button type="submit" class="btn btn-default">Go!</button>
			</form> <!-- / Search Form (xs) -->

		</div> <!-- / Navbar Links -->
	</div> <!-- / container -->
</div> <!-- / navbar -->