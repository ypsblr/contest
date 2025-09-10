<?php

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

include'connect.php';
session_start(); 
if (empty($_SESSION['USER_ID'])) {
	$_SESSION['upload_msg'] = ""; 
	$_SESSION['upload_error'] = "Unable to upload. Session expired. Login again and try."; 
	header('Location:' . $_SERVER['HTTP_REFERER']); 
	die();
}
$uid=$_SESSION['USER_ID'];

$entry = mysqli_query($DBCON, "SELECT name, email FROM entry WHERE entry_id='$uid'")or die(mysqli_error($DBCON));
$tr_entry=mysqli_fetch_array($entry);
$sender=$tr_entry['name'];
$email=$tr_entry['email'];

$msg_success = "";
$upload_error = "";


if (isset($_POST['upload_img']))
{
	if(isset($_FILES['file'])){
		$name = $_FILES['file']['name'];
		$tmp_name = $_FILES['file']['tmp_name'];
		$size = $_FILES['file']['size'];
		$error = $_FILES['file']['error'];
		$img_title = $_POST['img_title'];
		$location = $_POST['location'];
		$section = $_POST['selection'];
		$edate=date('D, M jS, Y H:i:s A');
		$eseq=date('siHu');
		
		debug_to_console(1);
		
		/* check if there are fie upload errors */
		if ( isset($_FILES['file']['error']) || !is_array($_FILES['file']['error']) || $_FILES['file']['error'] == UPLOAD_ERR_OK ) {
			/* check image size */
			list($img_width, $img_height, $img_typ, $attr) = getimagesize($tmp_name);
			$img_typ = image_type_to_extension($img_typ);
			$maxsize=$img_width;
			debug_to_console(2);
			if ($img_height > $img_width) 
				$maxsize = $img_height;
			debug_to_console(4);
			if ($maxsize <= 1920 && $maxsize >= 1080 && ($img_typ=='.jpeg' || $img_typ=='.jpg')) {
			    debug_to_console(4);
				$picfile = $uid . "_" . mt_rand(405,999) . ".jpg";
				if(move_uploaded_file($tmp_name, "../upload/" . $section . "/" . $picfile)){
					$query = "INSERT INTO pic(entry_id, title, section, location, submittedfile, picfile, edate, eseq) ";
					$query .= " VALUES('$uid', '$img_title', '$section', '$location', '$name', '$picfile', '$edate', '$eseq')";
					mysqli_query($DBCON, $query) or die(mysqli_error($DBCON));

					$mail_msg = "<html><head></head>";
					$mail_msg .= "<body><p>";
					$mail_msg .= "<strong>Greetings from Youth Photographic Society !</strong><br/>";
					$mail_msg .= "<strong>Congratulations !</strong> you have successfully uploaded your picture titled '$img_title' ";
					$mail_msg .= "(file: '$name') under section '$section` to MAHE Photo Awards 2025";
					$mail_msg .= "The uploaded files will be visible on the right side bar. If you need to make any changes, you can delete ";
					$mail_msg .= "the file using the trash icon and upload another file.<br><br>";
					$mail_msg .= "Regards,<br>";
					$mail_msg .= "Collaborator: Youth Photographic Society<br>";
					$mail_msg .= "on behalf of Manipal Acaemy of Higher Education (MAHE)<br>";
					$mail_msg .= "</p></body></html>";

					$subject="Upload of photo $img_title successful";
					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
					$headers .= 'From: <donotreply@ypsbengaluru.com>' . "\r\n";
					mail($email, $subject, $mail_msg, $headers);

					$msg_success="Image has been successfully uploaded !";
					
					/* Create Thumbnail */
					$pfile = "../upload/" . $section . "/". $picfile;
					$tfile = "../upload/" . $section . "/tn/". $picfile;
					createThumbnail($pfile, $tfile, 320);
				}			
				else
					$upload_error = "Error uploading file " . $name . " (File save error).";
			}
			else
				$upload_error = "Error uploading file " . $name . " (Large Picture or Incorrect picture file format)";

		}
		else
			$upload_error = "Error uploading file " . $name . " (File upload error)";
	}
	else			
		$upload_error = "Error uploading file " . $name . " (File upload error)";

	$_SESSION['upload_msg']=$msg_success; 
	$_SESSION['upload_error']=$upload_error; 
}

header('Location:' . $_SERVER['HTTP_REFERER']); 


function createThumbnail($pic, $tn, $longsize)
{
	// Content type
	// header('Content-type: image/jpeg');

	// Compute new dimensions
	list($width, $height) = getimagesize($pic);
	if ($width > $height) {
		$new_width = $longsize;
		$new_height = round($longsize / $width * $height);
	}
	else {
		$new_height = $longsize;
		$new_width = round($longsize / $height * $width);
	}

	// imagick example
	// $image = new Imagick($filename);

	// If 0 is provided as a width or height parameter,
	// aspect ratio is maintained
	// $image->thumbnailImage(140, 0);
	// echo $image;
	// Output
	// $image->writeImage('blogs/thumbnail.jpg');
	// echo "Thumbnail: <BR> <img src='blogs/thumbnail.jpg'><BR>";
	// echo "Full Size: <BR> <img src='" .$filename . "'><BR>";

	// GD Example - Resample
	$image_p = imagecreatetruecolor($new_width, $new_height);
	$image = imagecreatefromjpeg($pic);
	imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	// imagecopyresized($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

	// Sharpen the image based on two things:
	//	(1) the difference between the original size and the final size
	//	(2) the final size

	$sharpness	= sharpen($width, $new_width);

	$sharpenMatrix	= array(
		array(-1, -2, -1),
		array(-2, $sharpness + 12, -2),
		array(-1, -2, -1)
	);
	$divisor		= $sharpness;
	$offset			= 0;
	imageconvolution($image_p, $sharpenMatrix, $divisor, $offset);

	// Output to thumbnail file
	imagejpeg($image_p, $tn, 100);

}


function sharpen($orig, $final) 
{
	$final	= $final * (750.0 / $orig);
	$a		= 52;
	$b		= -0.27810650887573124;
	$c		= .00047337278106508946;

	$result = $a + $b * $final + $c * $final * $final;

	return max(round($result), 0);
}
?>