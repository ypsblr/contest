<?php 
session_start();
include("op/connect.php");
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

if (isset($_REQUEST['sections']) && isset($_SESSION['jury_id'])&& $_SESSION['jury_type']=="Master") 
{
	$jury_sections =$_REQUEST['sections'];
    $jury_sections=str_replace(" ", "", $jury_sections);   
    $jury_sections=decode_string_array($jury_sections);

$session_id=$_SESSION['jury_id'];
$user_query = mysqli_query($DBCON, "select * from users where id = '$session_id'")or die(mysqli_error($DBCON));
$user_row = mysqli_fetch_array($user_query);
$jury_username = $user_row['username'];
$jury_name=$user_row['fname'];
$jury_pic=$user_row['pic'];
$user_type = $user_row['type'];

$_SESSION['sections']=$jury_sections;
?>

<!DOCTYPE html>
<html>

<head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">

    
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
    <link rel="stylesheet" href="vendor/blueimp-gallery/css/blueimp-gallery.min.css" />
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

    <div class="normalheader transition animated fadeIn">
        <div class="hpanel">
            <div class="panel-body">
                <a class="small-header-action" href="#">
                    <div class="clip-header">
                        <i class="fa fa-arrow-up"></i>
                    </div>
                </a>

                <div id="hbreadcrumb" class="pull-right m-t-lg" style="margin-top:1px">
<h4>
				<a href="image_rating.php?sections=<?php echo encode_string_array($jury_sections);?>" class="" role="button">Click All <?php echo $jury_sections;?> Image</a></h4>
                    <!--ol class="hbreadcrumb breadcrumb">
						
                        <li><a href="home.php">Dashboard</a></li>
                        <li>
                            <span>Result</span>
                        </li>
                        <li class="active">
                            <span><?php echo $jury_sections;?> </span>
                        </li>
                    </ol-->
                </div>
                <h3 class="font-light m-b-xs">
                    Results of <?php echo $jury_sections;?> 
                </h3>
									         	  				
            </div>
        </div>
    </div>


<div class="content animate-panel">



    <div class="row">
        <div class="col-lg-12">
        

            <div class="hpanel">

                <div class="panel-body">
                <table id="example2" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>YPS ID</th>
                    <th>Position</th>
					
                    <th>Rating</th>
					<th>Total Image</th>
					<th>View</th>

                </tr>
                </thead>
                <tbody>
				<?php
				$t=0;
		 $pics = mysqli_query($DBCON, "SELECT DISTINCT(uid) FROM pics WHERE type='$jury_sections'")or die(mysqli_error($DBCON));
	     while($tr_pics=mysqli_fetch_array($pics))
	      {
		  $t+=1;
          $uid=$tr_pics['uid'];
		  $rating = mysqli_query($DBCON, "SELECT SUM(rate) FROM rating WHERE user_id='$uid' AND sections='$jury_sections'")or die(mysqli_error($DBCON));
          $tr_rating=mysqli_fetch_array($rating);
		  
		  $count_pics = mysqli_query($DBCON, "SELECT id FROM pics WHERE type='$jury_sections' AND uid='$uid'")or die(mysqli_error($DBCON));
		  $count=mysqli_num_rows($count_pics);

		  
              if(isset($tr_rating[0]))
			  {
		 
	  ?>
                <tr>
                    <td>YPS<?php printf("%03d", $tr_pics['uid']);?> </td>
                    <td><?php echo orderSuffix($t);?></td>
                    <td><?php echo $tr_rating[0];?></td>
                    <td><?php echo $count;?></td>


               
                    <td><a href="rating_details.php?key_id=<?php echo encode_string_array($tr_pics['uid']);?>" class="" role="button">View More</a></td>
                </tr>
				
<?php
                 }
             }

?>
                </tbody>
                </table>

                </div>
            </div>
        </div>

    </div>
    </div>

<?php 
      include("master/footer.php");
      include("master/profile_modal.php");
?>

</div>

<!-- The Gallery as lightbox dialog, should be a child element of the document body -->
<div id="blueimp-gallery" class="blueimp-gallery">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
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

<script src="vendor/blueimp-gallery/js/jquery.blueimp-gallery.min.js"></script>
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