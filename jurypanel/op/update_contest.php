<?php
session_start();
include 'connect.php';

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

function upload_slide($stub) {
	$name = $_FILES[$stub]['name'];
	$tmp_name = $_FILES[$stub]['tmp_name'];
	$size = $_FILES[$stub]['size'];
	if (file_exists($tmp_name) && is_uploaded_file($tmp_name)) {
		// echo "File Uploaded<br>";
		/* check image size */
		list($img_width, $img_height, $img_typ, $attr) = getimagesize($tmp_name);
		$img_typ = image_type_to_extension($img_typ);
		$maxsize=$img_width;
		// echo "Height: " . $img_height . "    Width: " . $img_width . "<br>";
		if ($img_height == 500 && $img_width == 1920) 
			move_uploaded_file($tmp_name, "../../img/" . $stub . ".jpg");
	}
}

$session_id=$_SESSION['jury_id'];
$user_query = mysqli_query($DBCON, "SELECT * FROM user where user_id = '$session_id'") or die(mysqli_error($DBCON));
$user_row = mysqli_fetch_array($user_query);
$jury_login = $user_row['login'];
$jury_name = $user_row['name'];
$user_type = $user_row['type'];

// var_dump($_FILES);

if (isset($_REQUEST['update_contest'])) {
    debug_to_console('updating contest');
	// Retrieve all values
	$contestName = mysqli_real_escape_string($DBCON, $_REQUEST["name"]);
	//$contestName = 'Test';
	debug_to_console($contestName);

	$submissionStartDate = $_REQUEST["submission_start_date"];
	debug_to_console($submissionStartDate);

	$submissionLastDate = $_REQUEST["submission_last_date"];
	debug_to_console($submissionLastDate);
	
	$judgingDate = $_REQUEST["judging_date"];
	debug_to_console($judgingDate);
	
	$resultsDate = $_REQUEST["results_date"];
	debug_to_console($resultsDate);
	
	$resultsReady = 0;
	debug_to_console($resultsReady);
	
	$contestMaxPics = 4;
	debug_to_console($contestMaxPics);
	
	$contestTheme = mysqli_real_escape_string($DBCON, $_REQUEST["theme"]);
	debug_to_console($contestTheme);
	
	$contestShortDesc = mysqli_real_escape_string($DBCON, $_REQUEST["short_description"]);
	debug_to_console($contestShortDesc);
	
	$contestDescription = mysqli_real_escape_string($DBCON, $_REQUEST["description"]);
	debug_to_console($contestDescription);
	
	$termsConditions = mysqli_real_escape_string($DBCON, $_REQUEST["terms_conditions"]);
	debug_to_console($termsConditions);
	
	$announcement = mysqli_real_escape_string($DBCON, $_REQUEST["announcement"]);
	debug_to_console($announcement);
	
	$jury_mode = $_REQUEST["jury_mode"];
	debug_to_console($jury_mode);
	
	debug_to_console('done with variable initialization');
	
	// Pictures
	upload_slide('slide-1');
	upload_slide('slide-2');
	upload_slide('slide-3');
	upload_slide('slide-4');
    debug_to_console('done with uploading pictures');
    
	// Update Contest
	$sql  = "UPDATE contest ";
	$sql .= "   SET name = '$contestName', ";
	$sql .= "       submission_start_date = '$submissionStartDate', ";
	$sql .= "       submission_last_date = '$submissionLastDate', ";
	$sql .= "       judging_date = '$judgingDate', ";
	$sql .= "       results_date = '$resultsDate', ";
	$sql .= "       results_ready = '$resultsReady', ";
	$sql .= "       max_pics_per_entry = '$contestMaxPics', ";
	$sql .= "       theme = '$contestTheme', ";
	$sql .= "       short_description = '$contestShortDesc', ";
	$sql .= "       description = '$contestDescription', ";
	$sql .= "       terms_conditions = '$termsConditions', ";
	$sql .= "       announcement = '$announcement', ";
	$sql .= "       jury_mode = '$jury_mode' ";
	// debug_to_console($sql);
	mysqli_query($DBCON, $sql) or die(mysqli_error($DBCON));
	$_SESSION['msg'] = "Contest updated";
	debug_to_console('finished updating contest');
}
if (isset($_REQUEST['init_contest'])) {
    debug_to_console('initializing contest');
	// Initialize assignment, pic, rating, recommendation, result, shortlist
	mysqli_query($DBCON, "TRUNCATE TABLE assignment") or die(mysqli_error($DBCON));
	mysqli_query($DBCON, "TRUNCATE TABLE pic") or die(mysqli_error($DBCON));
	mysqli_query($DBCON, "TRUNCATE TABLE rating") or die(mysqli_error($DBCON));
	mysqli_query($DBCON, "TRUNCATE TABLE recommendation") or die(mysqli_error($DBCON));
	mysqli_query($DBCON, "TRUNCATE TABLE result") or die(mysqli_error($DBCON));
	mysqli_query($DBCON, "TRUNCATE TABLE shortlist") or die(mysqli_error($DBCON));
	$_SESSION['msg'] = "All data initialized";
}

header("Location: ".$_SERVER['HTTP_REFERER']);
printf("<script>location.href='".$_SERVER['HTTP_REFERER']."'</script>");	

?>