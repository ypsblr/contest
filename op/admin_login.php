<?php
session_start();
include 'connect.php';
if(isset($_POST['admin_login_check']))
{
	$username=$_POST['username'];
    $password=$_POST['password'];
           
        $qchk = mysql_query("SELECT * FROM user WHERE login='$username' AND password='$password'")or die(mysql_error());
		$count = mysql_num_rows($qchk);
		$row=mysql_fetch_array($qchk);
		
   if ($count > 0) {
         $_SESSION['user_id']=$row['user_id'];
         header('Location:panel/index.php'); 
                    }			 
	else
	   {
	   $_SESSION['signin_msg']="Login Failed !!! Check your Email or Password";
	 header('Location:' . $_SERVER['HTTP_REFERER']); 	
	    }
}
else
{
  
 header('Location:' . $_SERVER['HTTP_REFERER']); 
 }
?>