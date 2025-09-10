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
</head>

<body class="body-blue">

	<div class="wrapper">
		<!-- Jumbotron -->
		<!-- Intro Text -->
		<div class="container intro">
			<div class="row">
				<div class="col-sm-7">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="text-danger">COMING SOON ...</h4>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</body>

</html>