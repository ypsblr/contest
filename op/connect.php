<?php
// mysql_select_db('yps_contestdb',mysql_connect('localhost','ypsdbadmin','yps!@#$12'))or die(mysql_error());
// $db = mysqli_connect('localhost','ypsdbadmin','yps!@#$12', 'yps_contestdb') or die(mysql_error());
$DBCON = mysqli_connect("localhost", "ypsdbadmin", "yps!@#$12", "yps_mahedb");
if (mysqli_connect_errno())
	die("DB Connection failed: " . mysqli_connect_error());

?>
