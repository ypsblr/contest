<?php 
session_start();
include("op/connect.php");
function orderSuffix($i) {
	$str = "$i";
	$t = $i > 9 ? substr($str,-2,1) : 0;
	$u = substr($str,-1);
	if ($t==1) 
		return $str . 'th';
	else 
		switch ($u) {
			case 1: return $str . 'st';
			case 2: return $str . 'nd';
			case 3: return $str . 'rd';
			default: return $str . 'th';
		}
}
							
if(isset($_SESSION['jury_id'])) {
	$session_id=$_SESSION['jury_id'];
	$user_query = mysqli_query($DBCON, "SELECT * FROM user WHERE user_id = '$session_id'")or die(mysqli_error($DBCON));
	$user_row = mysqli_fetch_array($user_query);
	$jury_login = $user_row['login'];
	$jury_name = $user_row['name'];
	$jury_pic = $user_row['avatar'];
	$user_type = $user_row['type'];

	if(isset($_GET['delete_id'])) {
		unset($_SESSION['next_pics_id']);
		$del=$_GET['delete_id'];
		$sql_query="DELETE FROM rating WHERE pic_id='$del' AND user_id='$session_id'";
		mysqli_query($DBCON, $sql_query);    
	}
?>

<!DOCTYPE html>
<html>

<head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">

    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title>Youth Photographic Society | Jury Panel</title>

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
	<div class="splash"> 
		<div class="color-line"></div>
		<div class="splash-title">
			<h1>    YOUTH PHOTOGRAPHIC SOCIETY | JURY ADMIN PANEL  </h1>
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
		include"master/header.php";
	?>
	<!-- Navigation -->
	<aside id="menu">
		<div id="navigation">
			<div class="profile-picture">
				<a href="home.php">
					<img src="images/<?php echo $jury_pic;?>" class="img-circle m-b" alt="logo">
				</a>
	            <div class="stats-label text-color">
					<span class="font-extra-bold font-uppercase"><?php echo $jury_name;?></span>
					<div class="dropdown">
						<a class="dropdown-toggle" href="#" data-toggle="dropdown">
							<small class="text-muted">Jury Member<b class="caret"></b></small>
						</a>
						<ul class="dropdown-menu animated flipInX m-t-xs">
							<li><a href="#" id="sidebar" class="right-sidebar-toggle">Profile</a></li>
							<li class="divider"></li>
							<li><a href="index.php">Logout</a></li>
						</ul>
					</div>
					<div id="sparkline1" class="small-chart m-t-sm"></div>
				</div>
			</div>
			<ul class="nav" id="side-menu">
				<li>
					<a href="home.php"> <span class="nav-label"><i class="fa fa-home"></i> Dashboard </span></a>
				</li>
				<li>
					<a href="rating.php"> <span class="nav-label"><i class="fa fa-star"></i> Rating </span></a>
				</li>
				<li class="active">
					<a href="all_record.php"> <span class="nav-label"><i class="fa fa-support"></i> All Score </span></a>
				</li>
				<li>
					<a href="#" id="sidebar" class="right-sidebar-toggle"><span class="nav-label"><i class="fa fa-user"></i> Profile </span></a>
				</li>
				<li>
					<a href="index.php"> <span class="nav-label"><i class="fa fa-unlock-alt"></i> Logout</span>  </a>
				</li>
			</ul>
		</div>
	</aside>
	
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
							<li><a href="home.php">Dashboard</a></li>
							<li><span>Tables</span></li>
							<li class="active"><span>Your Rating Ratio</span></li>
						</ol>
					</div>
					<h3 class="font-light m-b-xs">
						Your Rating Ratio 
					</h3>
				<?php
					if(isset($_SESSION['msg_rate'])) {
                ?>
					<div class="alert alert-success">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Status:) </strong><?php echo $_SESSION['msg_rate'];?>
					</div>
				<?php
						unset($_SESSION['msg_rate']);
					} 
					if(isset($_SESSION['info_update'])) {
                ?>
					<div class="alert alert-success">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Status:) </strong><?php echo $_SESSION['info_update'];?>
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
						<div class="panel-body">
							<table id="example2" class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th>ID</th>
									<th>Rating</th>
									<th>Image</th>
									<th>Title</th>
									<th>Rating Date</th>
									<th>Delete?</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$t=0;
								$pics = mysqli_query($DBCON, "SELECT * FROM pic, assignment WHERE pic.section = assignment.section AND assignment.user_id='$session_id'")or die(mysqli_error($DBCON));
								while($tr_pics=mysqli_fetch_array($pics)) {
									$t+=1;
									$pic_id = $tr_pics['pic_id'];
									$rating = mysqli_query($DBCON, "SELECT * FROM rating WHERE pic_id='$pic_id' AND user_id='$session_id'")or die(mysqli_error($DBCON));
									$count1 = mysqli_num_rows($rating);
									$tr_rating=mysqli_fetch_array($rating);
									if($count1>0) {
							?>
								<tr>
									<td>YPS<?php printf("%04d", $tr_pics['entry_id']);?> </td>
									<td><?php echo $tr_rating['rating'];?></td>
									<td> 
										<div class="lightBoxGallery">
											<a href="../upload/<?php echo $tr_pics['section'] . "/" . $tr_pics['picfile'];?>" 
													title=" User ID: YPS<?php printf("%04d", $tr_pics['entry_id']);?> & Title: <?php echo $tr_pics['title'];?>" 
													data-gallery="">
												<img class="img-responsive" src="../upload/<?php echo $tr_pics['section'] . "/tn/" . $tr_pics['picfile'];?>" 
													style="width:100px; height:60px">
											</a>					
										</div>
									</td>
									<td><?php echo $tr_pics['title'];?></td>
									<td><?php echo $tr_rating['edate'];?></td>
									<td> 
										<a href="all_record.php?delete_id=<?php echo $tr_rating['pic_id'];?>" methods="post" 
												onclick="return confirm('Are you sure');"   title="Delete">
												<i class="ace-icon fa fa-trash"></i> Delete
										</a>
									</td>
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