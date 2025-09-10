<?php
	session_start();
	function debug_dump($name, $value, $phpfile, $phpline) {
		file_put_contents("debug.txt", date("Y-m-d H:i") .": Dump of '$name' requested in line $phpline of '$phpfile'" . chr(13) . chr(10), FILE_APPEND);
		file_put_contents("debug.txt", print_r($value, true) . chr(13) . chr(10), FILE_APPEND);	
	}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="shortcut icon" href="img/favicon.png">

	<title>MAHE Photoghaphy Awards</title>

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
	
	<style>
		.valid-error{
			color:red;
		}
	</style>
	
	<script type='text/javascript'>
		function refreshCaptcha(){
			var img = document.images['captchaimg'];
			img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
		}
	</script>

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
						<?php
							if ($resultsReady) {
						?>
						<div class="panel-heading">
							<h4 class="text-danger">RESULTS HAVE BEEN ANNOUNCED</h4>
						</div>
						<div class="panel-body">
							<p>
								The results of <?=$contestName;?> have been announced based on the judging of the pictures held on <?=print_date($judgingDate);?>.
								We thank the <?=$arcNumEntries;?> participants who have submitted <?=$arcNumPictures;?> pictures for the contest.
								Check <a href="/results.php">Results Page</a> for the winning images and participants.
								YPS Congratulates all the winners of the contest. Please wait for further instructions from YPS about distribution of prizes.
							</p>
							<?php
								if ($jury_mode == "PROJECT") {
							?>
							<div class="row">
								<div class="col-sm-12">
									<form role="form" method="post" action="op/send_scores.php" id="send_score_form" name="send_score_form">
										<input type="hidden" name="rurobot" value="IamHuman">
										<div class="row">
											<div class="col-sm-12">
												<h4>Mail My Scores</h4>
												<p>
													Participants can now get the total scores awarded by the jury panel for each picture submitted by them.
													Type the email using which you had registered for the <?=$contestName;?> and 
													your scores will be sent to the email. 
												</p>
											</div>
										</div>
										<div class="form-group">
											<div class="row">
												<div class="col-sm-12">
													<label for="participant_email">My Email</label>
													<input type="text" name="participant_email" class="form-control" id="participant_email" placeholder="Email" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter Email Registered for this contest" data-original-title="Email" required>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="row">
												<div class="col-sm-6">
													<label for="captcha_code">Validation code:</label><br>
													<img src="captcha.php?rand=<?php echo rand();?>" id='captchaimg'>
													<br><a href='javascript: refreshCaptcha();'>Refresh Captcha</a>
												</div>
												<div class="col-sm-6">
													<label for="email">Enter the Validation code here :</label>
													<input id="captcha_code" class="form-control" name="captcha_code" type="text">
												</div>
											</div>
										</div>
			.							<div class="form-group">
											<div class="row">
												<div class="col-sm-11">
													<button type="submit" class="btn btn-color pull-right" name="mail_my_scores">Mail My Scores</button>
												</div>
											</div>
										</div>
									</form>
									<?php
										debug_dump("SESSION", $_SESSION, __FILE__, __LINE__);
										if (isset($_SESSION['err_msg'])) {
									?>
									<div class="row">
										<div class="col-sm-12">
											<div class="text text-danger"><small><?=$_SESSION['err_msg'];?></small></div>
										</div>
									</div>
									<?php
											unset($_SESSION['err_msg']);
										}
									?>
									<?php
										if (isset($_SESSION['ok_msg'])) {
									?>
									<div class="row">
										<div class="col-sm-12">
											<div class="text text-info"><small><?=$_SESSION['ok_msg'];?></small></div>
										</div>
									</div>
									<?php
											unset($_SESSION['ok_msg']);
										}
									?>
								</div>
							</div>
							<?php
								}
							?>
						</div>
						<?php
							}
							else if (date("Y-m-d") > $submissionLastDate) {
						?>
						<div class="panel-heading">
							<h4 class="text-danger">SUBMISSION CLOSED</h4>
						</div>
						<div class="panel-body">
							We thank you for visiting the YPS Contest Page. The current contest is closed for registration and submission of pictures.
							If you have already submitted the pictures, please wait for the announcement of results.
							If not, please join us in the next contest.
						</div>
						<?php
							}
							else {
						?>
						
						<!-- Added by Gopinath -->
						<!--div class="panel-body" style="border-style: none !important;">
    						<p class="text-muted text-justify">
    						    <?=$contestShortDesc;?>
    					    </p>
    						<p class="text-muted text-justify">
    						    <?=$contestDescription;?>
    					    </p>
                        </div-->
                        
						<!--<div class="panel-heading">
							<h4 class="text-danger">IMPORTANT INSTRUCTIONS</h4>
						</div>
						<div class="panel-body">
							<ol>
								<li>Picture size requirements:
									<ul>
										<li>The <b>Maximum Size</b> for the longer side is <b>1920 pixels</b>.</li>
										<li>The <b>Minimum Size</b> for the longer side is <b>1080 pixels</b>. Any picture less than this size will be rejected.</li>
									</ul>
								</li>
								<li>Any Travel picture clicked in DSLR/Mirrorless camera only.</li>
								<li>At the time of uploading, specify the location in the box provided.</li>
								<li>Editing techniques must be in compliance with the commonly accepted Travel photography guidelines.</li>
								<li>The contest is open to photo enthusiasts from all over India.</li>
								<li>If your photo is shortlisted, digital photo with a minimum size of 12 inches x 18 inches need to be sent by 
								email before the announcement of results. Before uploading your pictures in this contest, please ensure that 
								this requirement is met.
								</li>
							</ol>
						</div>-->
						<?php
							}
						?>
					</div>
					<h3 class="text-color"><?=$contestName;?></h3>
					
					<p class="text-muted text-justify"><?=$contestShortDesc;?></p>
					<p class="text-muted text-justify"><?=$contestDescription;?></p>
					
					<!--<p class="text-muted text-justify">-->
						<!--<a href="http://www.karnatakatourism.org" target="_blank"><strong>KAPA</strong></a>--><!--<strong style="color: #2790B0;">KAPA</strong>,
					    in association with
						<a href="http://ypsbengaluru.com" target="_blank"><strong>Youth Photographic Society</strong>&reg;</a>, 
						is pleased to invite you to participate in the <strong><?=$contestName;?></strong>.
						The contest is being organized to pay tributes to our dear Kalyan da by recognizing photographers who can bring out the glory his photography.
					</p>
					<p class="text-muted text-justify">
						Pictures not only speak for themselves but offer inspiration to explore, appreciate, cherish and treasure our travel.
					</p>-->
					
					<div class="panel panel-default">
					<div class="panel-heading">
							<h4 class="text-danger">IMPORTANT INSTRUCTIONS</h4>
						</div>
						<div class="panel-body">
							<ol>
								<li>Picture size requirements:
									<ul>
										<li>The <b>Maximum Size</b> for the longer side is <b>1920 pixels</b>.</li>
										<li>The <b>Minimum Size</b> for the longer side is <b>1080 pixels</b>. Any picture less than this size will be rejected.</li>
									</ul>
								</li>
								<li>Any Travel picture clicked on <!--DSLR/Mirrorless--> any camera and not AI Generated.</li>
								<li>At the time of uploading, specify the location in the box provided.</li>
								<li>Editing techniques must be in compliance with the commonly accepted Travel photography guidelines.</li>
								<li>The contest is open to photo enthusiasts from all over India.</li>
								<li>If your photo is shortlisted, digital photo with a minimum size of 12 inches x 18 inches need to be sent by 
								email before the announcement of results. Before uploading your pictures in this contest, please ensure that 
								this requirement is met.
								</li>
							</ol>
						</div>
				   </div class="panel panel-default">
					</p>
					<h3 class="text-color">Last date to submit pictures is <?php echo date("M j, Y", strtotime($submissionLastDate)); ?></h3>
					<p><?php echo $termsConditions; ?></p>
					<hr>
					<!--<p>The contest is hosted by <a href="http://ypsbengaluru.com" target="_blank">Youth Photographic Society (YPS) Bengaluru</a>. <br><br>-->
						<!--<a href="http://www.ypsbengaluru.com" target="_blank">www.ypsbengaluru.com</a><br>-->
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
											echo "<p><strong>Cash Award: Rs." . $awardCash[$j] . " " . ( $awardNumber[$j] > 1 ? "each" : "" ) . "</strong></p>";
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
					<!--<br>
					<h3 class="text-color">Past Contests</h3>-->
					<ul>
					<?php
						$month = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
						$qcon = mysqli_query($DBCON, "SELECT * FROM ar_contest WHERE results_ready = 1 ORDER BY month DESC") or die(mysqli_error($DBCON));
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
					<!--<p><small>* Click the links see the results</small></p>-->
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
	<script src="js/validate/js/jquery.validate.min.js"></script>

	<!-- JS Custom -->
	<script src="js/custom.js"></script>
	
	<!-- Form Validation -->
	<script>
		$('#send_score_form').validate({
			rules: {
				participant_email : { 
					required : true, 
					email : true, 
					remote : {
						url : 'op/validate_participant_email.php',
						type : 'post',
						data : {
							email : function() { return $('#participant_email').val() },
						},
					},
				},
				captcha_code : {
					required : true,
				},
			},
			messages : {
				participant_email : {
					required : "Enter the email used to register",
					email : "Invalid Email",
					remote : "Email not registered for this contest",
				},
				captcha_code : {
					required : "Enter the verification code displayed on the left"
				}
			},
			errorElement:"div",
			errorClass:"valid-error",
			submitHandler:function(form){
				form.submit();
			},	
		});
	</script>

</body>

</html>