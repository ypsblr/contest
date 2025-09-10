<?php 
session_start();
include("op/connect.php");



if(isset($_GET['delete_id']))
{
$del=$_GET['delete_id'];
     $sql_query="delete from entry WHERE id='$del'";
     mysqli_query($DBCON, $sql_query);

	      $sql_query="delete from rating WHERE uid='$del'";
     mysqli_query($DBCON, $sql_query);
    
	     $sql_query="delete from pics WHERE uid='$del'";
     mysqli_query($DBCON, $sql_query);

echo "<meta http-equiv=\"refresh\" content=\"0;URL=user_info.php\">";}


function orderSuffix($i) {
						   $str = "$i";
						   $t = $i > 9 ? substr($str,-2,1) : 0;
						   $u = substr($str,-1);
						   if ($t==1) return $str . 'th';
							   else switch ($u) {
							       case 1: return $str . 'st';
							       case 2: return $str . 'nd';
							       case 3: return $str . 'rd';
							       default: return $str . 'th';
								   }
							}
							function encode_string_array ($stringArray) {
    $s = strtr(base64_encode(addslashes(gzcompress(serialize($stringArray),2))), '+/=', '_,');
    
    return $s;
}	
	function decode_string_array ($stringArray) {
    $s = unserialize(gzuncompress(stripslashes(base64_decode(strtr($stringArray, '_,', '+/=')))));
    return $s;
}
							
if(isset($_SESSION['jury_id']) && $_SESSION['jury_type']=="Master")
{
		$session_id=$_SESSION['jury_id'];
	$user_query = mysqli_query($DBCON, "select * from users where id = '$session_id'")or die(mysqli_error($DBCON));
$user_row = mysqli_fetch_array($user_query);
$jury_username = $user_row['username'];
$jury_name=$user_row['fname'];
$jury_pic=$user_row['pic'];
$user_type = $user_row['type'];
$jury_sections=$user_row['sections'];
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title>Youth Photographic | Jury Panel</title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!--<link rel="shortcut icon" type="image/ico" href="favicon.ico" />-->

    <!-- Vendor styles -->
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="vendor/metisMenu/dist/metisMenu.css" />
    <link rel="stylesheet" href="vendor/animate.css/animate.css" />
    <link rel="stylesheet" href="vendor/bootstrap/dist/css/bootstrap.css" />
    <link rel="stylesheet" href="vendor/datatables.net-bs/css/dataTables.bootstrap.min.css" />

    <!-- App styles -->
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/helper.css" />
    <link rel="stylesheet" href="styles/style.css">

</head>
<body class="fixed-navbar fixed-sidebar">

<!-- Simple splash screen-->
<div class="splash"> <div class="color-line"></div><div class="splash-title"><h1>    YOUTH PHOTOGRAPHY | JURY ADMIN PANEL  </h1><p>Please Wait. </p><div class="spinner"> <div class="rect1"></div> <div class="rect2"></div> <div class="rect3"></div> <div class="rect4"></div> <div class="rect5"></div> </div> </div> </div>
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<!-- Header -->
<?php 
include"master/master_header.php";
include"master/master_sidebar.php";
?>
<!-- Main Wrapper -->
<div id="wrapper">

												   <?php
                if(isset($_SESSION['signup_msg']))
                {
                ?>
                <div class="alert alert-success">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Status:) </strong><?php echo $_SESSION['signup_msg']."</div>";
                 unset($_SESSION['signup_msg']);
                } 
				
				if(isset($_SESSION['info_update']))
                {
                ?>
                <div class="alert alert-success">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Status:) </strong><?php echo $_SESSION['info_update']."</div>";
                 unset($_SESSION['info_update']);
                } 
				?>
<div class="content animate-panel">



      <div class="row">
        <div class="col-md-5">

            <div class="hpanel">
                <div class="panel-body">
				            <div class="text-center m-b-md">
                <h3>Participate Info</h3>
            </div>
                        <form action="op/register.php" id="loginForm" method="post">
                            <div class="row">
							<div class="form-group col-lg-6">
                                <label>Name</label>
                                <input type="text" class="form-control" name="fname" value="<?php echo $fname;?>">
                            </div>
							<div class="form-group col-lg-6">
                                <label>Honors</label>
                                <input type="text"  class="form-control" name="honors" value="<?php echo $honors;?>">
                            </div>
							<div class="form-group col-lg-12">
                                <label>Address</label>
                                <input type="text"  class="form-control" name="address" value="<?php echo $address;?>">
                            </div>

							<div class="form-group col-lg-6">
                                <label>City</label>
                                <input type="text" class="form-control" name="city" value="<?php echo $city;?>">
                            </div>
							<div class="form-group col-lg-6">
                                <label>State</label>
                                <input type="text"  class="form-control" name="state" value="<?php echo $state;?>">
                            </div>
							
							<div class="form-group col-lg-6">
                                <label>Phone</label>
                                <input type="text" class="form-control" name="phone" value="<?php echo $phone;?>">
                            </div>
							<div class="form-group col-lg-6">
                                <label>Email</label>
                                <input type="text"  class="form-control" name="email" value="<?php echo $email;?>">
                            </div>


                            
                            <div class="text-center">
                                <button class="btn btn-success" type="submit" name="update">Update</button>
                                <button class="btn btn-default" type="reset">Cancel</button>
                            </div>
                            </div>
                        </form>
                </div>
            </div>
        </div>
		
		<div class="col-md-7">
		            <div class="hpanel">
               
                <div class="panel-body">
								            <div class="text-center">
                <h3>Participate Info</h3>
            </div>
                                     <p>
                <b><i class="fa fa-hand-o-right"></i> Payment Process through DD? <a href="#dd_payment" data-toggle="collapse" aria-expanded="false" aria-controls="dd_payment">Click here</a></b>
              </p>              

			  <div class="collapse" id="dd_payment">
                <p class="text-muted">
                  &nbsp;
                </p>
                <form method="post" action="op/payment_now.php"  name="form1" onsubmit="return testInputData(this);">
                 <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <label class="sr-only" for="dd">DD No</label>
                      <input type="text" name="ddno" class="form-control text-uppercase margin-bottom-xs" id="dd" placeholder="DD NO" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter a valid DD NO here." data-original-title="DD NO" required>
                    </div>
                    <div class="col-md-6">
                      <label class="sr-only" for="date">Date</label>
                      <input type="date" name="date" class="form-control text-uppercase" id="date"  placeholder="dd/mm/yyyy" data-toggle="popover" data-trigger="focus" data-content="Enter a date format(dd/mm/yyyy)." data-original-title="date" required>
                    </div>
                  </div>
                </div>
				
				<div class="form-group" >
                  <label class="sr-only" for="bank">Bank Name</label>
                  <input type="text" name="bank"  class="form-control text-uppercase" id="bank" placeholder="Enter bank name" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter a valid bank name here." data-original-title="bank" required>
                </div>
								<div class="form-group">
                  <label class="sr-only" for="amount">Total Amount*</label>
                  <input type="text" class="form-control text-uppercase" name="amount" id="amount" placeholder="Enter Total Amount" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter a Total Amount here." data-original-title="amount" required>
                </div>
                <p id="demo"></p>
                  <button type="submit" class="btn btn-color" name="payment_now_dd" onclick="validatedate(document.form1.date)">Pay Now</button>
                </form>
              </div> <!-- lost-password__form -->
			  
	              <p>
                <b><i class="fa fa-hand-o-right"></i> Payment Process through Online? <a href="#online_payment" data-toggle="collapse" aria-expanded="false" aria-controls="dd_payment">Click here</b></a>
              </p>  

	<div class="collapse" id="online_payment">
                <p class="text-muted">
                 &nbsp;
                </p>
                <form method="post" action="op/payment_now.php"  name="form" onsubmit="return testInputData(this);">
				
				<div class="form-group">
                  <label class="sr-only" for="online"> Online Transfer details </label>
                  <textarea class="form-control text-uppercase" name="online" id="online" placeholder="Your Online Transfer details" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter your Online Transfer details here." data-original-title="online" required></textarea>
                </div>
				<div class="form-group">
                  <label class="sr-only" for="amount">Total Amount*</label>
                  <input type="text" class="form-control text-uppercase" name="amount" id="amount" placeholder="Enter Total Amount" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="Enter a Total Amount here." data-original-title="amount" required>
                </div>
                <p id="demo"></p>
				

                  <button type="submit" class="btn btn-color" name="payment_now_online">Pay Now</button>
                </form>
              </div> <!-- online__form -->	  


                </div>
            </div>
		</div>
    </div></div>
    

<?php 
      include("master/footer.php");
      include("master/profile_modal.php");
?>

</div>

 

<!-- Vendor scripts -->
<script src="vendor/jquery/dist/jquery.min.js"></script>
<script src="vendor/jquery-ui/jquery-ui.min.js"></script>
<script src="vendor/slimScroll/jquery.slimscroll.min.js"></script>
<script src="vendor/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="vendor/metisMenu/dist/metisMenu.min.js"></script>
<script src="vendor/iCheck/icheck.min.js"></script>
<script src="vendor/sparkline/index.js"></script>
<!-- DataTables -->
<script src="vendor/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="vendor/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- DataTables buttons scripts -->
<script src="vendor/pdfmake/build/pdfmake.min.js"></script>
<script src="vendor/pdfmake/build/vfs_fonts.js"></script>
<script src="vendor/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="vendor/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="vendor/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="vendor/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
<!-- App scripts -->
<script src="scripts/homer.js"></script>


<script>

    $(function () {

        // Initialize Example 1
        $('#example1').dataTable( {
            "ajax": 'api/datatables.json',
            dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
            buttons: [
                {extend: 'copy',className: 'btn-sm'},
                {extend: 'csv',title: 'ExampleFile', className: 'btn-sm'},
                {extend: 'pdf', title: 'ExampleFile', className: 'btn-sm'},
                {extend: 'print',className: 'btn-sm'}
            ]
        });

        // Initialize Example 2
        $('#example2').dataTable();

    });

</script>

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