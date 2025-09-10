<?php
include("../op/connect.php");

include_once('../PHPImageWorkshop/ImageWorkshop.php');

use PHPImageWorkshop\ImageWorkshop as ImageWorkshop;

function assemble_honors($list, $maxlen) {
	$text = "";
	$first = true;
	foreach($list as $item) {
		if (strlen($text . $item) <= $maxlen)
			$text .= ($first ? "" : ", ") . $item;
		$first = false;
	}
	return $text . "...";
}


// define("TEXT_COLOR", "0xf34508"); Text Color for 35th All India Salon
define("TEXT_COLOR", 0x318ba2);
define("GRAY_TEXT_COLOR", 0x808080);
define("GRAY_BACKGROUND_COLOR", 0x808080);
define("SS_WIDTH", 1920);
define("SS_HEIGHT", 1080);
define("SS_BGCOLOR", "202020");
define("SS_BGLAYER_IMG", "WTD2019_SS_BACKDROP.jpg");
define("SS_TITLE_BGCOLOR", "202020");		// Black Layer


if (! empty($_REQUEST['top']) ) {
	
    // Generate Slideshow of images for all specific section or all sections
	$top = $_REQUEST['top'];
	$cutoff = empty($_REQUEST['cutoff']) ? 9 : $_REQUEST['cutoff'];
    
	// Check if already running
	if (file_exists("slideshow_lock.txt")) {
		echo "slideshow already running. Quitting";
		die;
	}
	
	// Create lock file
	$file = fopen("slideshow_lock.txt","w");
	echo fputs($file, "Slideshow Running for " . $top . " top pictures.");
	fclose($file);
	
    // Create Slideshow Folder if it does not exist
    $slideshow_folder = "../slideshow";
    if (! is_dir($slideshow_folder))
        mkdir($slideshow_folder);
	
	// Remove execution time limit for PHP
	set_time_limit(0);
	
	$sql = "SELECT * FROM contest";
	$query = mysqli_query($DBCON, $sql) or die(mysqli_error($DBCON));
	$contest = mysqli_fetch_array($query);
	$section = $contest['name'];
	
	//
	// List of Top scorers that have not been awarded
	//
	$sql  = "SELECT pic_id, SUM(rating) AS total_rating FROM rating ";
	$sql .= " WHERE pic_id NOT IN (SELECT pic_id FROM result) ";
	$sql .= " GROUP BY pic_id ";
	$sql .= "HAVING total_rating >= '$cutoff' ";
	$sql .= " ORDER BY total_rating DESC ";
	$query = mysqli_query($DBCON, $sql) or die(mysqli_error($DBCON));
	
	$idx = 0;
	for ($count = 0; $count < $top && $row = mysqli_fetch_array($query); ++ $count) {
		$pic_id = $row['pic_id'];
		$sql  = "SELECT name, honors, city, state, ";
		$sql .= "       title, section, location, picfile ";
		$sql .= "  FROM entry, pic ";
		$sql .= " WHERE pic_id = '$pic_id' ";
		$sql .= "   AND entry.entry_id = pic.entry_id ";
		$pic_query = mysqli_query($DBCON, $sql) or die(mysqli_error($DBCON));
		
		$pic = mysqli_fetch_array($pic_query);
		$name = $pic['name'];
		$honors = trim($pic['honors']);
		$title = $pic['title'];
		$city = $pic['city'];
		$state = $pic['state'];
		$section = $pic['section'];
		$picfile = $pic['picfile'];
		$location = $pic['location'];
		$award_name = "TOP PICKS";
		
		echo "<br>" . $title . "(" . $pic_id . ")";
		
		$fontPath = "../PHPImageWorkshop/font/PetitaBold.ttf";
		$fontColor = "C0C0C0";
		$textRotation = 0;
		$fontSize = 72;

		//
		// STEP 1 - Create a layer with just images & template
		// ===================================================
		//
		// Create an empty Layer
		//
		$image = ImageWorkshop::initVirginLayer(SS_WIDTH, SS_HEIGHT, SS_BGCOLOR);

		//
		// Add Background Layer
		//
		$background_layer = ImageWorkshop::initFromPath(SS_BGLAYER_IMG);
		$image->addLayerOnTop($background_layer, 0, 0, "LT");

		//
		// Add Text Layer for Section Name
		//
		// $awardFontPath = "../PHPImageWorkshop/font/SIFONN_BASIC.otf";
		//$awardFontPath = "../../PHPImageWorkshop/font/PetitaBold.ttf";
		//$awardFontColor = "D9D9D9";
		//$awardTextRotation = 90;
		//$awardFontSize = 20;
		//$awardText = $section . " - " . $award_name;
		//$section_title_layer = ImageWorkshop::initTextLayer($awardText, $awardFontPath, $awardFontSize, $awardFontColor, $awardTextRotation);
		//$image->addLayerOnTop($section_title_layer, 80, 25, "LB");

		//
		// Add Picture as Layer in the remaining space
		//
		$pic_layer = ImageWorkshop::initFromPath("../upload/$section/$picfile");
		$pic_width = $pic_layer->getWidth();
		$pic_height = $pic_layer->getHeight();

		// Resize and Add to the blank page
		$available_width = SS_WIDTH - 172; 	// 172 pixels occupied by the template text on the left
		$available_height = SS_HEIGHT;		// Use the entire height of the screen

		// Determine resize width & height
		$output_width = $pic_width;
		$output_height = $pic_height;
		if ($output_width > $available_width || $output_height > $available_height) {
			// Proportionately adjust height & width
			if ($output_width > $available_width) {
				$output_height = $output_height * $available_width / $output_width;
				$output_width = $available_width;
				if ($output_height > $available_height) {
					$output_width = $output_width * $available_height / $output_height;
					$output_height = $available_height;
				}
			}
			elseif ($output_height > $available_height) {
				$output_width = $output_width * $available_height / $output_height;
				$output_height = $available_height;
				if ($output_width > $available_width) {
					$output_height = $output_height * $available_width / $output_width;
					$output_width = $available_width;
				}
			}				
			$pic_layer->resizeInPixel($output_width, $output_height, false);
		}

		$pic_x = 172 + ($available_width / 2);
		$pic_y = ($available_height / 2);

		$image->addLayerOnTop($pic_layer, 172/2, 0, 'MM');
		
		// Add Tourism Logo on top
		$logo_layer = ImageWorkshop::initFromPath("ktd_logo.jpg");
		$image->addLayerOnTop($logo_layer, 20, 20, 'RT');
		

		// Write the first picture
		++ $idx;
		$folder = $slideshow_folder;
		$savefile = "WTD2019-" . sprintf("%03d-A", $idx) . ".jpg";
		$image->save($folder, $savefile, true, null, $imageQuality = 85, $interlace = false);

		//
		// Step 2 - Create a second image with Author and Picture details
		//          Will be displayed from Right Bottom
		//
		$details = ImageWorkshop::initVirginLayer($available_width, 140, SS_TITLE_BGCOLOR);
		$details->opacity(65);
		// debug_dump("Band", "Created Details Band", __FILE__, __LINE__);

		// Print Title - centered
		$text = $title . ", " . $location;
		$textRotation = 0;
		$fontSize = 24;
		$title_layer = ImageWorkshop::initTextLayer($text, $fontPath, $fontSize, $fontColor, $textRotation);
		$details->addLayerOnTop($title_layer, 0, 20, "MT");
		// debug_dump("Title", "Added Title", __FILE__, __LINE__);

		$output_x = 80;
		// Print Avatar
		// Open Avatar Layer and resize to 100 x 100
		// if (!empty($avatar) && $avatar != 'user.jpg' && file_exists("../../img/avatar/$avatar")) {
		//	$avatar_layer = ImageWorkshop::initFromPath("../../img/avatar/$avatar");
		//	$avatar_layer->resizeInPixel(100, 100, true, 0, 0, "LT");

			// Add Avatar Layer to the image
			// $details->addLayerOnTop($avatar_layer, $output_x, 20, "RT");
			// debug_dump("Avatar", "Added Avatar", __FILE__, __LINE__);
			// $output_x += 100 + 20;
		//}

		// Print Honors
		$maxlen = 125;
		if (! empty($honors)) {
			$text = $honors;
			if (strlen($honors) > $maxlen) {
				$honors_list = explode(",", $honors);
				if (sizeof($honors_list) <= 1) {
					$honors_list = explode(" ", $honors);
					if (sizeof($honors_list) <= 1)
						$text = substr($honors, 0, $maxlen) . "...";
					else
						$text = assemble_honors($honors_list, $maxlen);
				}
				else
					$text = assemble_honors($honors_list, $maxlen);
			} 
			$textRotation = 0;
			$fontSize = 16;
			$honors_layer = ImageWorkshop::initTextLayer($text, $fontPath, $fontSize, $fontColor, $textRotation);
			$details->addLayerOnTop($honors_layer, $output_x, 72, "RT");
		}

		// Print Name
		$text = $name;
		$textRotation = 0;
		$fontSize = 24;
		$name_layer = ImageWorkshop::initTextLayer($text, $fontPath, $fontSize, $fontColor, $textRotation);
		$details->addLayerOnTop($name_layer, $output_x, 96, "RT");


		// Print City & State on the left edge
		$text = trim($city) . ", " . $state;
		$textRotation = 0;
		$fontSize = 24;
		$country_layer = ImageWorkshop::initTextLayer($text, $fontPath, $fontSize, $fontColor, $textRotation);
		$details->addLayerOnTop($country_layer, 120, 96, "LT");

		// Add the detail group to image
		$image->addLayerOnTop($details, 0, 20, "RB");

		// Write second image
		$folder = $slideshow_folder;
		$savefile = "WTD2019-" . sprintf("%03d-B", $idx) . ".jpg";
		$image->save($folder, $savefile, true, null, $imageQuality = 85, $interlace = false);
	}
	unlink("slideshow_lock.txt");
}
else
	echo "Invalid Parameters";

?>