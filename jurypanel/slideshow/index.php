<?php 
session_start();
//include("../op/connect.php");
function encode_string_array ($stringArray) {
    $s = strtr(base64_encode(addslashes(gzcompress(serialize($stringArray),2))), '+/=', '_,');
    
    return $s;
}	
	function decode_string_array ($stringArray) {
    $s = unserialize(gzuncompress(stripslashes(base64_decode(strtr($stringArray, '_,', '+/=')))));
    return $s;
}
function orderSuffix($i) {
						   $str = "$i";
						   $t = $i > 9 ? substr($str,-2,1) : 0;
						   $u = substr($str,-1);
						   if ($t==1) return $str . 'th';
							   else switch ($u) {
							       case 1: return $str . '<sup>st</sup>';
							       case 2: return $str . '<sup>nd</sup>';
							       case 3: return $str . '<sup>rd</sup>';
							       default: return $str . '<sup>th</sup>';
								   }
							}

// isset($_SESSION['jury_id'])&& $_SESSION['jury_type']=="Master";
$t=1;
if ($t==1) 
{
	/*
$session_id=$_SESSION['jury_id'];
$user_query = mysql_query("select * from users where id = '$session_id'")or die(mysql_error());
$user_row = mysql_fetch_array($user_query);
$jury_username = $user_row['username'];
$jury_name=$user_row['fname'];
$jury_pic=$user_row['pic'];
$user_type = $user_row['type'];
*/
	
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Youth Photography</title>
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Ultra">
<link rel="stylesheet" type="text/css" href="js/vegas/jquery.vegas.css">
<link rel="stylesheet" type="text/css" href="js/jscrollpane/jquery.jscrollpane.css">
<link rel="stylesheet" type="text/css" href="css/styles.css">
<script src="http://code.jquery.com/jquery-1.6.2.min.js"></script>
<script src="js/jquery.easing.js"></script>
<script src="js/vegas/jquery.vegas.js"></script>
<script src="js/jscrollpane/jquery.jscrollpane.min.js"></script>
<script src="js/buzz/buzz.js"></script>
<script src="js/gallery.js"></script>
<script>
$(document).ready(function() {
    $('video, audio').prop('muted', true);
});
</script>
</head>

<body>
    <div id="flash"></div>
    <div id="title">
        <h1>Rating Preview</h1>
        <p>Picture by <a href="#">Youth Photography</a></p>
    </div>
    <div id="thumbnails">
        <ul>


            <li><a href="img/01.jpg"><img src="img/01b.jpg" title="YPS007 -TITLE" data-valign="top"></a></li>
            <li><a href="img/02.jpg"><img src="img/02b.jpg" title="YPS008 -New York traffic" data-valign="bottom"></a></li>
            <li><a href="img/03.jpg"><img src="img/03b.jpg" title="YPS009 -Street dancers"></a></li>
            <li><a href="img/04.jpg"><img src="img/04b.jpg" title="YPS010 -Lights"></a></li>
            <li><a href="img/05.jpg"><img src="img/05b.jpg" title="YPS011 -NBC Studio"></a></li>
            <li><a href="img/06.jpg"><img src="img/06b.jpg" title="YPS012 -New York by night" data-valign="top"></a></li>
            <li><a href="img/07.jpg"><img src="img/07b.jpg" title="YPS013 -Empire State Building"></a></li>
            <li><a href="img/08.jpg"><img src="img/08b.jpg" title="YPS014 -New York Promenade" data-valign="top"></a></li>
            <li><a href="img/09.jpg"><img src="img/09b.jpg" title="YPS015 -Ellis Island Flag" data-valign="top"></a></li>
            <li><a href="img/10.jpg"><img src="img/10b.jpg" title="YPS016 -Central Park"></a></li>
            <li><a href="img/11.jpg"><img src="img/11b.jpg" title="YPS017 -Statue of Liberty" data-valign="top"></a></li>
            <li><a href="img/12.jpg"><img src="img/12b.jpg" title="YPS009 -Shoes"></a></li>
        </ul>
    	<div id="pointer"></div>
    </div>
    <div id="pause"><a href="#">Paused</a></div>
 
</body>
</html>
<?php
}
else
{
header("Location: ".$_SERVER['HTTP_REFERER']);
printf("<script>location.href='".$_SERVER['HTTP_REFERER']."'</script>");
}

?>
