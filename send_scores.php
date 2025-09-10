<?php
session_start();
require("connect.php");
require('pdflib.php');

// Validate Inputs
if (isset($_REQUEST['mail_my_scores']) && (! empty($_REQUEST['participant_email'])) && (! empty($_REQUEST['captcha_code'])) && 
		strcasecmp($_SESSION['captcha_code'], $_REQUEST['captcha_code']) && $_REQUEST['rurobot'] == 'IamHuman' ) {

	$participant_email = trim($_REQUEST['participant_email']);
	// $participant_email = "srinath.narayan@me.com";
	
	// Get the required data
	$query = mysqli_query($DBCON, "SELECT * FROM contest ") or die_with_error(mysqli_error($DBCON));
	$contest = mysqli_fetch_array($query);
	
	$query = mysqli_query($DBCON, "SELECT * FROM entry WHERE email = '$participant_email' AND entry_id IN (SELECT entry_id FROM pic)") or die_with_error(mysqli_error($DBCON));
	if (mysqli_num_rows($query) == 0)
		die_with_error("Could not find contest entry for the email : '$participant_email'");
	$entry = mysqli_fetch_array($query);
	$entry_id = $entry['entry_id'];
	
	// Create a PDF Document in A5 format
	$pdf = new PDF("P", "pt", "A5", 0.5 * 72);
		
	// Set Page Layout
	$pdf->AddPage();
	
	// Write Tourism Logo & YPS Logo
	$ktdc_logo = array(
					"column_width" => 50, "content_type" => "IMAGE", "content_value" => "../img/logo.png",
					"content_align" => "LM", "image_caption" => "Karnataka Tourism", "image_link" => "http://www.karnatakatourism.org",
					"image_print_width" => 0, "image_print_height" => 20, "font_size" => 8, "font_color" => 0x0, "font_style" => ""
					);
	$yps_logo = array(
					"column_width" => 50, "content_type" => "IMAGE", "content_value" => "../img/yps.jpg",
					"content_align" => "RM", "image_caption" => "Youth Photographic Society", "image_link" => "http://www.ypsbengaluru.com",
					"image_print_width" => 0, "image_print_height" => 20, "font_size" => 8, "font_color" => 0x0, "font_style" => ""
					);
	$cols = array ( $ktdc_logo, $yps_logo );
	$pdf->PrintColumns($cols);

	// Print header image
	$header = array("column_width" => 100, "content_type" => "IMAGE", "content_value" => "../img/slide-1.jpg");
	$pdf->PrintColumns(array($header));

	// debug_dump("Co-ordinates : ", "X=". $pdf->GetX() . ", Y=" . $pdf->GetY(), __FILE__, __LINE__);
	$x = $pdf->GetX();
	$y = $pdf->GetY();	
	
	// Write Contest Name
	$pdf->PrintH2($contest["name"]);
	
	// Write Participant Name
	$pdf->PrintH3("Scores for " . $entry['name']);
	
	// Write Description
	$para  = "Please find the scores assigned to the pictures submitted by you for the above contest. The scores shown are total scores assigned ";
	$para .= "to by all the jury against a maximum total score of 15.";
	$pdf->PrintParagraph($para);

	$pdf->PrintParagraph("");
	
	// Get Picture titles & scores
	$sql  = "SELECT pic.pic_id, section, title, location, picfile, SUM(rating) AS total_rating FROM pic, rating ";
	$sql .= " WHERE entry_id = '$entry_id' ";
	$sql .= "   AND rating.pic_id = pic.pic_id ";
	$sql .= " GROUP BY pic_id, section, title, location, picfile ";
	$query = mysqli_query($DBCON, $sql) or die_with_error(mysqli_error($DBCON));

	$max_width = 72;
	$max_height = 72;

	while ($pic = mysqli_fetch_array($query)) {
		// Get Award Details if any for this Picture
		$sql  = "SELECT * FROM result, award ";
		$sql .= " WHERE result.pic_id = '" . $pic['pic_id'] . "' ";
		$sql .= "   AND award.award_id = result.award_id ";
		$result_query = mysqli_query($DBCON, $sql) or die_with_error(mysqli_error($DBCON));
		$has_result = mysqli_num_rows($result_query) > 0;
		if (mysqli_num_rows($result_query) > 0) {
			$result = mysqli_fetch_array($result_query);
		}

		// Draw a separator
		$pdf->PrintHR();
		
		// Print a row
		$img = "../upload/" . $pic['section'] . "/tn/" . $pic['picfile'];
		$thumbnail = array(
						"column_width" => 20, "content_type" => "IMAGE", "content_value" => $img,
						"content_align" => "CM", // "image_caption" => "Karnataka Tourism", "image_link" => "http://www.karnatakatourism.org",
						"image_print_width" => $max_width, "image_print_height" => $max_height  // , "font_size" => 8, "font_color" => 0x0, "font_style" => ""
						);
		$title = array(
						"text" => $pic['title'], "align" => "L", 
						"font_size" => 10, "font_color" => 0x0, "font_style" => "B",
						);
		$score = array(
						"text" => "Total Rating : " . $pic['total_rating'] . "/15", "align" => "L", 
						"font_size" => 10, "font_color" => 0x0, "font_style" => "",
						"margin_top" => 8
						);
		if ($has_result) {
			$award = array(
						"text" => "* Winner of " . $result['name'], "align" => "L", 
						"font_size" => 10, "font_color" => 0x0, "font_style" => "B",
						"margin_top" => 8
						);
		}
		$details = array(
						"column_width" => 80, "content_type" => "MULTI", "content_value" => $has_result ? array($title, $score, $award) : array($title, $score),
						"content_align" => "LT", "font_size" => 10, "font_color" => 0x0, "font_style" => ""
						);
		$pdf->PrintColumns(array($thumbnail, $details), 0, 0, 2, 2);

		
	}
	$pdf->PrintHR();

	// Create a tmp folder
	if (! file_exists("tmp"))
		mkdir("tmp");

	$attachment_name = "contest_score.pdf";
	
	// Produce output as a string and encode it for sending
	$pdfdoc = chunk_split(base64_encode($pdf->Output("S")));

	// Prepare plain text and html messages
	$plain_text  = "Dear " . $entry['name'] . ",\r\n\r\n";
	$plain_text .= "Score Card for " . $contest['name'] . "\r\n";
	$plain_text .= "===========================================================\r\n\r\n";
	$plain_text .= "Please find attached your score card for the pictures submitted by you to the contest " . $contest['name'] . "\r\n\r\n";
	$plain_text .= "Youth Photographic Society\r\n";
	$plain_text .= "Web: http://ypsbengaluru.com\r\n";

	$html  = <<<HTML
<html>
	<header>
		<style>
			.h2 {
				font-family : Arial, Helvetica, sans-serif,
				font-size : 14px, 
			}
			.p {
				font-family : Arial, Helvetica, sans-serif,
				font-size : 11px, 			
			}
		</style>
	</header>
	<body>
		<p>Dear [participant-name],</p>
		<br>
		<h2>Score Card for [contest_name]</h2>
		<p>Please find attached your score card for the pictures submitted by you to the contest [contest_name].</p>
		<br>
		<p><a href="http://ypsbengaluru.com">Youth Photographic Society</a></p>
	</body>
</html>
HTML;

	$find_text = array("[contest_name]", "[participant-name]");
	$replace_text = array($contest['name'], $entry['name']);
	$message = str_replace($find_text, $replace_text, $html);

	// Send Email
	// Note to be able to add attachment, this is being sent as multipart email.
	// Multipart emails require a boundary string to be defined and used for each section. 
	// It also requires additional headers to be passed as part of the body with proper line
	// separations (with empty lines) between header and mail content.
	// Refer RFC specification at https://www.w3.org/Protocols/rfc1341/7_2_Multipart.html for more information
	// Prepare headers to send with attachment
	$boundary_mail = md5("contest.ypsbengaluru.com/mail"); 		// define boundary with a md5 hashed value 
	$boundary_message = md5("contest.ypsbengaluru.com/message");

	// $to = $participant_email;
	$to = "webmaster@ypsbengaluru.com";
	$subject = "Scores for pictures submitted to " . $contest['name'];

	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "From: <contest@ypsbengaluru.com>\r\n";
	$headers .= "CC: <contest@ypsbengaluru.com>\r\n";
	$headers .= "Content-type:multipart/mixed;\r\n";
	$headers .= "    boundary=\"$boundary_mail\"";

	// Prepare Body Message in Plain Text as well as in HTML
	$body  = "\r\n--$boundary_mail\r\n"; 
	$body .= "Content-type: multipart/alternative;\r\n";
	$body .= "   boundary=\"$boundary_message\"\r\n\r\n";
	// Plain Text
	$body .= "--$boundary_message\r\n";
	$body .= "Content-type: text/plain; charset=UTF-8\r\n\r\n"; 
	$body .= $plain_text . "\r\n\r\n";
	// HTML Text
	$body .= "--$boundary_message\r\n";
	$body .= "Content-type: text/html; charset=UTF-8\r\n\r\n"; 
	$body .= $message . "\r\n\r\n";	
	$body .= "--$boundary_message--\r\n\r\n";
	// Attachment
	$body .= "--$boundary_mail\r\n"; 
	$body .="Content-type: application/pdf; name=" . $attachment_name . "\r\n"; 
	$body .="Content-Disposition: attachment; filename=" . $attachment_name . "\r\n"; 
	$body .="Content-Transfer-Encoding: base64\r\n"; 
	$body .="X-Attachment-Id: " . rand(1000, 99999) . "\r\n\r\n"; 
	$body .= $pdfdoc . "\r\n\r\n"; // Attaching the encoded file with email 

	$body .= "--$boundary_mail--\r\n"; 

	// Send Email
	$sentMailResult = mail($to, $subject, $body, $headers); 

	if( ! $sentMailResult) { 
		die_with_error("Error sending the email. Please try again!"); 
	} 
	
	$_SESSION['ok_msg'] = "Scores have been mailed. Wait for the email to arrive.";
	header('Location:' . $_SERVER['HTTP_REFERER']); 
}
else {
	$_SESSION['err_msg'] = "Invalid/Missing Parameters";
	header('Location:' . $_SERVER['HTTP_REFERER']); 
}

?>