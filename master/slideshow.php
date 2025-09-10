		<!-- Jumbotron -->
		<div class="main-slideshow">
			<div id="main-slideshow" class="carousel slide" data-ride="carousel">
				<div class="carousel-inner">
					<!-- Slide No 1 -->
					<div class="item active">
						<div class="jumbotron first">
							<div class="container">
								<div class="row">
									<div class="col-sm-6 ">
										<h1 class="animated slideInLeft" style="color:yellow"><?php echo $contestName; ?></h1>
										<p class="lead animated slideInLeft delay-1 text-justify" style="color:yellow">
											<?php echo $contestShortDesc; ?>
										<!-- <a class="btn btn-color animated slideInLeft delay-2" href="results.php">View Results</a> -->
									</div>
									<div class="col-sm-6">
									</div>
								</div>
							</div>
						</div>
					</div> 
					<!-- Slide No 2 -->
					<div class="item">
						<div class="jumbotron second">
							<div class="container">
								<div class="row">
									<div class="col-sm-12">
										<h1 class="animated fadeInDown" style="color: yellow;">Contest Sections</h1>
										<p class="lead animated slideInLeft delay-1 text-justify" style="color: white;">
											<br><br>
											<?php
												for ($i = 0; $i < $numSections; ++$i) {
											?>
											<b> <i class="fa fa-image"></i> <?php echo $section[$i]; ?></b><br><br>
											<?php
												}
											?>
											<br><br>
											<!-- <a class="btn btn-color animated slideInLeft delay-2" href="results.php">View Results</a> -->
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Slide No 3 -->
					<div class="item">
						<div class="jumbotron third">
							<div class="container">
								<div class="row">
									<div class="col-sm-6">
									<?php
										if (date("Y-m-d") <= $submissionLastDate) {
									?>
										<h1 class="animated slideInLeft" style="color: yellow;">Contest Dates</h1>
										<p class="lead animated slideInLeft delay-1 text-justify" style="color: yellow;">
											<br><br>
											<b style="color: white;"><i class="fa fa-image"></i> Last Date for Submission : <?php echo date("M j, Y", strtotime($submissionLastDate)); ?></b><br><br>
											<!--<b><i class="fa fa-image"></i> Last Date for Submission : <?php echo date("M j, Y", strtotime($submissionLastDate)); ?></b><br><br>-->
											<b style="color: white;"><i class="fa fa-image"></i> Judging Date : <?php echo date("M j, Y", strtotime($judgingDate)); ?></b><br><br>
											<b style="color: white;"><i class="fa fa-image"></i> Results : <?php echo date("M j, Y", strtotime($resultsDate)); ?></b><br><br>
											<br><br>
											<!-- <a class="btn btn-color animated slideInLeft delay-2" href="results.php">View Results</a> -->
											<!-- <b><i class="fa fa-calendar"></i> Notification :</b> 25th September 2016<br>
											<b><i class="fa fa-calendar"></i> Award Function : </b>26th September 2016 -->
										</p>
									<?php
										}
										else {
									?>
										<h1 class="animated slideInLeft">Contest Closed</h1>
										<p class="lead animated slideInLeft delay-1 text-justify">
											<br>
											The <?php echo $contestName;?> contest is closed for submission of photos.
									<?php
											if ($resultsReady) {
									?>
											The results of the contest have been published. Check the <a href="/results.php">Results</a>.
									<?php
											}
											else {
									?>
											<br><br>
											<b><i class="fa fa-image"></i> Judging Date : <?php echo date("M j, Y", strtotime($judgingDate)); ?></b><br><br>
											<b><i class="fa fa-image"></i> Results Published : <?php echo date("M j, Y", strtotime($resultsDate)); ?></b><br><br>
									<?php
											}
									?>
											<br><br>
										</p>
									<?php
										}
									?>
									</div>
									<div class="col-sm-6">
									</div>
								</div>
							</div>
						</div>
					</div> <!-- / Slide No 3 -->
					<!-- Slide No 4 -->
				<?php
					if ($announcement != "") {
				?>
					<div class="item">
						<div class="jumbotron fourth">
							<div class="container">
								<div class="row">
									<div class="col-sm-8">
									</div>
									<div class="col-sm-4">
										<h1 class="animated slideInLeft" style="color:yellow"><!--Watch Out ! -->Results are OUT..!</h1>
										<p class="lead animated slideInLeft delay-1 text-justify" style="color:black">
											<?php 
												if ($announcement == "")
													echo "Participate and Promote Karnataka Tourism !";
												else
													echo $announcement; 
											?>
										<!-- <a class="btn btn-color animated slideInLeft delay-2" href="results.php">View Results</a> -->
									</div>
								</div>
							</div>
						</div>
					</div> <!-- / Slide No 4 -->
				<?php
					}
				?>
				</div> 
				<!-- Controls -->
				<a class="slideshow-arrow slideshow-arrow-prev bg-hover-color" href="#main-slideshow" data-slide="prev">
					<i class="fa fa-angle-left"></i>
				</a>
				<a class="slideshow-arrow slideshow-arrow-next bg-hover-color" href="#main-slideshow" data-slide="next">
					<i class="fa fa-angle-right"></i>
				</a>
			</div>
		</div> <!-- Slideshow -->
