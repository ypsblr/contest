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

if (isset($_SESSION['jury_id'])&& ($_SESSION['jury_type']=="MASTER" || $_SESSION['jury_type']=="ADMIN")) {
	$session_id=$_SESSION['jury_id'];
	$user_query = mysqli_query($DBCON, "SELECT * FROM user WHERE user_id = '$session_id'")or die(mysqli_error($DBCON));
	$user_row = mysqli_fetch_array($user_query);
	$jury_username = $user_row['login'];
	$jury_name=$user_row['name'];
	$jury_pic=$user_row['avatar'];
	$user_type = $user_row['type'];

	$sectionList = array();
	$idx = 0;
	$sqry = mysqli_query($DBCON, "SELECT section FROM section ORDER BY section") or die(mysqli_error($DBCON));
	for ($idx = 0; $srow = mysqli_fetch_array($sqry); ++$idx)
		$sectionList[$idx] = $srow['section'];
	$numSections = $idx;	
	
?>

<!DOCTYPE html>
<html>

<head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">

    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title>Youth Photographic Society | Master Panel</title>

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

	<script language="javascript">
function validate()
{
var chks = document.getElementsByName('checkbox[]');
var hasChecked = false;
for (var i = 0; i < chks.length; i++)
{
if (chks[i].checked)
{
hasChecked = true;
break;
}
}
if (hasChecked == false)
{
alert("Please select at least one.");
return false;
}
return true;
}
</script>

</head>
<body class="fixed-navbar fixed-sidebar">

	<!-- Simple splash screen-->
	<div class="splash">
		<div class="color-line"></div>
		<div class="splash-title">
			<h1>    YPS MASTER PANEL  </h1>
			<p>Please Wait. </p>
			<div class="spinner"> 
				<div class="rect1"></div> 
				<div class="rect2"></div> 
				<div class="rect3"></div> 
				<div class="rect4"></div> 
				<div class="rect5"></div> 
			</div> 
		</div> 
	</div>
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

	<!-- Header -->
<?php 
include "master/master_header.php";
include "master/master_sidebar.php";
?>

	<!-- Main Wrapper -->
	<div id="wrapper">
		<div class="normalheader transition animated fadeIn">
			<div class="hpanel">
				<div class="panel-body">
					<a class="small-header-action" href="#">
						<div class="clip-header">
							<i class="fa fa-arrow-up"></i>
						</div>
					</a>
					<div id="hbreadcrumb" class="pull-right" style="margin-top:1px">
						<ol class="hbreadcrumb breadcrumb">
							<li><a href="registration.php">Dashboard</a></li>
							<li><span>Tables</span></li>
							<li class="active"><span>ALL Section Images</span></li>
						</ol>
					</div>
					<h3 class="font-light m-b-xs">
						All Uploaded Images
					</h3>
				<?php
					if(isset($_SESSION['send_err'])) {
                ?>
					<div class="alert alert-success">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Status:) </strong>All <?php echo $_SESSION['count'];?> mails sent successfully !!<?php echo $_SESSION['send_err']; ?>
					</div>
				<?php
						unset($_SESSION['send_err']);
						unset($_SESSION['count']);
					} 
				
					if(isset($_SESSION['info_update'])) {
                ?>
					<div class="alert alert-success">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Status:) </strong><?php echo $_SESSION['info_update']; ?>
					</div>
				<?php
						unset($_SESSION['info_update']);
					} 
				?>
				
				</div>
			</div>
		</div>


		<div class="content animate-panel">
			<div class="row">
				<div class="col-lg-12">
					<div class="hpanel">
						<form method="post" action="op/all_send_mail.php" onSubmit="return validate();">
							<button type="submit" value="Send Email" name="all_mail" class="btn btn-info" aria-label="icon note circle bordered">
								<i class="fa fa-forword"></i> 
								Send Mail
							</button>
							<div class="panel-body">
								<table id="example2" class="table table-striped table-bordered table-hover" style="width: 100%;">
								<thead>
									<tr>
										<th rowspan="2">ID</th>
										<th rowspan="2">Name</b></th>
										<th rowspan="2">Email, Phone</th>
										<th colspan="<?php echo $numSections; ?>" class="text-center">Sections</th>
										<!-- <th rowspan="2">Fees Info</th> -->
										<th rowspan="2">Reg.Date</th>
									</tr>
									<tr>
									<?php
										for ($idx = 0; $idx < $numSections; ++$idx) {
									?>
										<th><?php echo $sectionList[$idx]; ?></th>
									<?php
										}
									?>
									</tr>
								</thead>
								<tbody>
							<?php
								$entry = mysqli_query($DBCON, "SELECT * FROM entry ORDER BY entry_id DESC")or die(mysqli_error($DBCON));
								while($tr_entry=mysqli_fetch_array($entry)) {
							?>
									<tr>
										<td>
											<input name="checkbox[]" type="checkbox" id="checkbox[]" value="<?php echo $tr_entry['email'];?>">
											&nbsp;YPS<?php printf("%03d", $tr_entry['entry_id']);?>
										</td>
										<td><?php echo $tr_entry['name']; ?></td>
										<td><?php echo $tr_entry['email'].", ".$tr_entry['phone'];?></td>
							<?php
									$uid = $tr_entry['entry_id'];
									// $payment = $tr_entry['payment'];
									for ($idx = 0; $idx < $numSections; ++$idx) {
										$pics_m = mysqli_query($DBCON, "SELECT * FROM pic WHERE entry_id='$uid' AND section = '$sectionList[$idx]'" ) or die(mysqli_error($DBCON));
										echo "<td>" . mysqli_num_rows($pics_m) . "</td>";
									}
							?>
										<td><?php echo $tr_entry['edate'];?></td>


									</tr>
							<?php
								}
							?>
								</tbody>
								</table>
							</div>
						</form>
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
    <a class="prev">&laquo;</a>
    <a class="next">&raquo;</a>
    <a class="close">&#42;</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>

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

<script src="vendor/blueimp-gallery/js/jquery.blueimp-gallery.min.js"></script>
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