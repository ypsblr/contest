<?php
session_start();
include("connect.php");

if(isset($_POST['admin_login_check'])) {
	$username=$_POST['username'];
    $password=$_POST['password'];
	
	$qchk = mysqli_query($DBCON, "SELECT * FROM user WHERE login='$username' AND password='$password'")or die(mysqli_error($DBCON));
	$count = mysqli_num_rows($qchk);
	$row=mysqli_fetch_array($qchk);
	if ($count > 0) {
		$_SESSION['jury_id']=$row['user_id'];
		$_SESSION['jury_type']=$row['type'];
		/* Special Logins */
		if($_SESSION['jury_type']=="PROJECTOR")
			printf("<script>location.href='/jurypanel/projector_home.php'</script>");
		elseif ($_SESSION['jury_type']=="DISPLAY")
			printf("<script>location.href='/jurypanel/display.php'</script>");
		elseif ($_SESSION['jury_type']=="FINALIZE")
			printf("<script>location.href='/jurypanel/finalize.php'</script>");
		elseif($_SESSION['jury_type']=="MASTER" || $_SESSION['jury_type']=="ADMIN")
			printf("<script>location.href='/jurypanel/registration.php'</script>");
		else {
			$query = mysqli_query($DBCON, "SELECT * FROM contest");
			$row = mysqli_fetch_array($query);
			if ($row['jury_mode'] == 'PROJECT')
				printf("<script>location.href='/jurypanel/rating_new.php'</script>"); // Jury Login for scoring
			else
				printf("<script>location.href='/jurypanel/shortlist.php'</script>"); // Jury Login for scoring
		}
	}
	else {
		$_SESSION['signin_msg']="Login Failed !!! Check your User Name $username or Password";
		// echo print_r($_POST);
		printf("<script>location.href='/jurypanel/index.php'</script>");
		printf("<script>location.href='".$_SERVER['HTTP_REFERER']."'</script>");	
	}
}
else {
	echo print_r($_POST);
	$_SESSION['signin_msg']="amin_login_check not set";
	printf("<script>location.href='".$_SERVER['HTTP_REFERER']."'</script>");	
	printf("<script>location.href='/jurypanel/index.php'</script>");
	header("Location: ".$_SERVER['HTTP_REFERER']);
}
?>