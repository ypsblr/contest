<?php
session_start();
/* clear session variables */
$user = "";
$code = "";
if (isset($_REQUEST["user"])) {
	$user = $_REQUEST["user"];
	$code = $_REQUEST["code"];
	$_SESSION["user"] = $user;
	$_SESSION["code"] = $code;
}
else if (isset($_SESSION["user"])) {
	$user = $_SESSION["user"];
	$code = $_SESSION["code"];
}

if ($user != "admin" || $code != "919667") {
	header("Location:./admin.php?error=Invalid Login");
	
}

?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
	<p>World Tourism Day Digital Photography Contest 2016</p>
	<p>Judging Administration Screen</p>
	<br><br>
	<p>Select Category<br></p>
	<a href="./display.php?cat=heritage">Karnataka Heritage</a><br><br>
	<a href="./display.php?cat=nature">Karnataka Nature</a><br>
	<br><br>
	<?php
		if (isset($_REQUEST["error"]))
			echo "Error: " . $_REQUEST["error"];
	?>
</body>
</html>