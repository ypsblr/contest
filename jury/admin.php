<?php
session_start();
/* clear session variables */
$_SESSION["user"]="";
$_SESSION["code"]="";
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
	<p>World Tourism Day Digital Photography Contest 2016</p>
	<p>Judging Administration Screen</p>
	<br><br>
	<?php if (isset($_REQUEST["error"])) 
			echo "Error : " . $_REQUEST["error"] . "<br><br>";
	?>
	<p>Admin Login<br></p>
	<form action="./judging.php" method="post">
		<p>User Name :
			<input type="text" name="user">
		</p>
		<p>Access Code :
			<input type="password" name="code">
			<br>
		</p>
		<input type="submit" value="Login">
	</form>
</body>
