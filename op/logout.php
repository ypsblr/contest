<?php
session_start();
unset($_SESSION['USER_ID']);
session_destroy();
header('Location:/sign_in.php'); 
?>

