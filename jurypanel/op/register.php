<?php
session_start();
include("connect.php");
$session_id=$_SESSION['jury_id'];
$user_query = mysqli_query($DBCON, "SELECT * FROM user WHERE user_id = '$session_id'") or die(mysqli_error($DBCON));
$user_row = mysqli_fetch_array($user_query);
$jury_name = $user_row['name'];
$jury_login = $user_row['login'];
$jury_pic = $user_row['avatar'];
$user_type = $user_row['type'];

if (isset($_POST['new_user'])) {
	$type = $_POST["type"];
	$name = $_POST['name'];
	$name = strtoupper($name);
	$login = $_POST['login'];
	$password = $_POST['password'];
	$title = mysqli_real_escape_string($DBCON, $_POST['title']);
	$honors = mysqli_real_escape_string($DBCON, $_POST['honors']);
	$profile = mysqli_real_escape_string($DBCON, $_POST['profile']);
	if (isset($_FILES['picture'])) {
		$filename = $_FILES['picture']['name'];
		$tmp_name = $_FILES['picture']['tmp_name'];
		$profilePic = "userpic_" . mt_rand(500, 999) . ".jpg";
		createThumbnail($tmp_name, "../images/" . $profilePic, 180);
	}
	else
		$pic = "userpic.png";
	if (isset($_FILES['avatar'])) {
		$filename = $_FILES['avatar']['name'];
		$tmp_name = $_FILES['avatar']['tmp_name'];
		$pic = "user_" . mt_rand(500, 999) . ".jpg";
		createThumbnail($tmp_name, "../images/" . $pic, 75);
	}
	else
		$pic = "user.png";
	$status = "ACTIVE";
	$qchk = mysqli_query($DBCON, "SELECT * FROM user WHERE login='$login'") or die(mysqli_error($DBCON));
	$count = mysqli_num_rows($qchk);
	if ($count==0){
		mysqli_query($DBCON, "INSERT INTO user(name, login, type, picture, avatar, password, title, honors, profile, status)
						VALUES('$name','$login','$type','$profilePic','$pic','$password','$title','$honors','$profile','$status')") or die(mysqli_error($DBCON));
		$_SESSION['signup_msg'] = "Successfully Registered :)";
		header("Location: ../registration.php");
		printf("<script>location.href='../registration.php'</script>");
	}
	else {
		$_SESSION['signup_msg'] = "User already exists !";
		header("Location: ../registration.php");
		printf("<script>location.href='../registration.php'</script>");
	}
}
else if(isset($_POST['update_user'])) {
	$user_id = $_POST["user_id"];
	$type = $_POST["type"];
	$name = $_POST['name'];
	$name = strtoupper($name);
	$login = $_POST['login'];
	$password = $_POST['password'];
	$title = mysqli_real_escape_string($DBCON, $_POST['title']);
	$honors = mysqli_real_escape_string($DBCON, $_POST['honors']);
	$profile = mysqli_real_escape_string($DBCON, $_POST['profile']);
	if (isset($_FILES['picture']) && getimagesize($_FILES['picture']['tmp_name']) != false) {
		$filename = $_FILES['picture']['name'];
		$tmp_name = $_FILES['picture']['tmp_name'];
		$profilePic = "userpic_" . mt_rand(500, 999) . ".jpg";
		createThumbnail($tmp_name, "../images/" . $profilePic, 180);
		$pic = "user_" . mt_rand(500, 999) . ".jpg";
		createThumbnail($tmp_name, "../images/" . $pic, 75);
	}
	else {
		$profilePic = "";
		$pic = "";
	}
	$status = "ACTIVE";

	$sql = "UPDATE user ";
	$sql .= "SET name = '$name', ";
	$sql .= "    login = '$login', ";
	$sql .= "    type = '$type', ";
	if ($password != "")
		$sql .= "    password = '$password', ";
	if ($profilePic != "")
		$sql .= "    picture = '$profilePic', ";
	if ($pic != "")
		$sql .= "    avatar = '$pic', ";
	$sql .= "    title = '$title', ";
	$sql .= "    honors = '$honors', ";
	$sql .= "    profile = '$profile' ";
	$sql .= "WHERE user_id = '$user_id' ";
	mysqli_query($DBCON, $sql) or die(mysqli_error($DBCON));
	$_SESSION['signup_msg'] = "Successfully Updated :)";
	header("Location: ../registration.php");
	printf("<script>location.href='../registration.php'</script>");
}
else {     
	header("Location: ".$_SERVER['HTTP_REFERER']);
	printf("<script>location.href='".$_SERVER['HTTP_REFERER']."'</script>");
}

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