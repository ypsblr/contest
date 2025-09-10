<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="shortcut icon" href="img/favicon.png">

	<title>MAHE Photo Awards</title>

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

<?php include"master/header.php";?>
<?php include "master/slideshow.php"; ?>
	<!-- Wrapper -->
<div class="wrapper">
	<!-- Topic Header -->
	<div class="color-jumbotron">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<h1 class="text-center">MAHE Photography Awards</h1>
				</div>
			</div> <!-- / .row -->
		</div> <!-- / .container -->
	</div> <!-- / .color-jumbotron -->

	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<h3 class="primary-font">Terms &amp; Conditions for participation</h3>
				<hr>
				<ol style="line-height:1.8; text-align:justify">
					<li>The Contest is open to all photographers, whether amateurs or professionals or tourists, from India. Members of the 
							jury cannot participate.</li>
					<li>There is no entry fee.</li>
					<li>The Categories for the Contest and associated Terms and conditions can be found below.</li>
					<li>There is a maximum limit to the number of photographs that can be uploaded under each section. Please check the upload 
							page for information. This number will be enforced.</li>
					<li>Each Photographer can participate only once and submit only one set of photographs. Please do not login under different 
							emails and submit pictures under each email.</li>
					<li>Photographs should be in JPEG format. The Photograph uploaded should be resized <b>not to exceed 1920 pixels on the longer 
							side</b>. Also photographs with <b>less than 1024 pixels on the longer side will not be allowed</b>. The maximum file size allowed is 4MB. 
							Images will be presented to the jury in the same resolution in which the image was uploaded.</li>
					<li>Since the selected photograph(s) could be displayed as large size hoardings, coffee books, brochures and other media, the Photographer 
									of selected picture will be required to submit a full resolution picture of not less than 4500 pixels on the long side, 
									preferably in 16 Bit TIFF format. Before submitting the picture, please ensure that this requirement can be met.</li>
					<li>Uploads with your name in the Picture or Title or File Name will not be considered.</li>
					<li>If the picture submitted has recognizable faces in prominence, the entrant submitting the picture should have the 
						concurrence of the person(s) in the photograph for use in promotional material. The entrant agrees to indemnify
						MAHE against any claims from such persons.</li>
					<li>The Photograph should not contain Title or Name or Watermark or any other form of identification on the face of the image.</li>
					<li>It is the responsibility of the participants to upload the photographs under the correct section. Photographs not matching 
							the section under which they are uploaded will not be considered for the contest. If in doubt, please refer to the 
							definition of the categories provided below.</li>
					<li>Entries must originate as photographs (image-captures of objects via light sensitivity) captured by the entrant on 
							photographic emulsion or acquired digitally.</li> 
					<li>By virtue of submitting an entry, the entrant certifies the work as his own (aliases are not permitted). The entrant indemnifies 
							MAHE against all claims arising from originality and ownership.</li>
					<li>The entrant permits the organizer MAHE and Youth Photographic Society to reproduce all or part of any of the photographs entered
					for the contest free of charge for publication and/or display in media related activities which are of Non-Comercial.</li>
					<li>All matters related to usage of photographs submitted through this website for the contest, must be taken up 
							with MAHE. Youth Photographic Society, as hosts for the contest,
							cannot be held responsible for any matters related to usage of the photographs by anyone.</li>
					<li>Only entries submitted through this website will be considered for the contest. Photographs received through emails 
							or other means will not be considered for the contest.</li>
					<li>While filling up the entry form, enter full Image Titles and your Name and Surname as you want them to appear in the 
							publications, cheques and certificates. Titles like "Untitled" or "No Title" or File name of the image or just 
							numerals are not allowed and may lead to disqualification. It is also mandatory to provide valid contact number.</li>
					<li>There are no restrictions as to subject matter except as outlined in the rules and definitions of specific sections, 
							or specified by law in the country where the exhibition is held.</li>
					<li>The only permissible alterations to the photographs uploaded are by way of resizing, cropping, selective lighting 
							or darkening, sharpening, and any technique for restoration of original color of the scene. Any adjustments must 
							appear natural.</li>
					<li>Judging results will be published on this web-site. Individual emails will not be sent to the participants.</li>
					<li>Photos from this competition will not be used or sold anywhere without prior consent of the contestants.</li>
					<li>Participants are requested to upload their entries well ahead of time to avoid last minute rush. Late entries will not 
							be entertained.</li>
					<li>The decision of the Panel of Judges is final and binding, no further appeal will be entertained.</li>
				</ol>
			<!--	<h3 class="primary-font">Sections &amp; Rules</h3>-->
				<hr>
				<!--The subject for images entered in all sections must satisfy the definitions and rules given below:-->
				<!--<br><br>-->
				<ul class="nav nav-pills">
				<?php
					for ($i = 0; $i < $numSections; $i++) {
				?>
					<li <?php if ($i == 0) echo "class='active'"; ?>>
						<a data-toggle="pill" href="#<?php echo 'section' . $i; ?>"><?php echo $section[$i]; ?></a>
					</li>
				<?php
					}
				?>
				</ul>
				<div class="tab-content">
				<?php
					for ($i = 0; $i < $numSections; $i++) {
				?>
					<div id="<?php echo 'section' . $i; ?>" class="tab-pane fade in <?php if ($i == 0) echo "active"; ?>">
						<!--<h4><?php echo $section[$i]; ?></h4>-->
						<p><strong>Max Pictures under this section : <?php echo $sectionMaxPics[$i]; ?></strong></p>
						<p><?php echo $sectionDescription[$i]; ?></p>
						<p><?php echo $sectionRules[$i]; ?></p>
						<hr>
					</div>
				<?php
					}
				?>
				</div>
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