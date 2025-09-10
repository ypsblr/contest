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

if(isset($_SESSION['jury_id']))
{
	$session_id=$_SESSION['jury_id'];
	$user_query = mysqli_query($DBCON, "SELECT * FROM user WHERE user_id = '$session_id'")or die(mysqli_error($DBCON));
	$user_row = mysqli_fetch_array($user_query);
	$jury_login = $user_row['login'];
	$jury_name=$user_row['name'];
	$jury_pic=$user_row['avatar'];
	$user_type = $user_row['type'];

	$jury_id=$_SESSION['jury_id'];
	$user = mysqli_query($DBCON, "SELECT * FROM user WHERE user_id='$jury_id'")or die(mysqli_error($DBCON));
	$tr_user=mysqli_fetch_array($user);
	
	// Get a list of sections assigned
	$sectionList = array();
	$sql = "SELECT assignment.section AS section, COUNT(*) as pending ";
	$sql .= " FROM assignment, pic, entry ";
	$sql .= " WHERE assignment.user_id = '$jury_id' ";
	$sql .= "   AND assignment.section = pic.section ";
	$sql .= "   AND pic.entry_id = entry.entry_id ";
	$sql .= "   AND pic.pic_id NOT IN ";
	$sql .= "          (SELECT pic_id FROM rating WHERE user_id = '$jury_id') ";
	$sql .= " GROUP BY section ";
	$query = mysqli_query($DBCON, $sql) or die(mysqli_error($DBCON));
	while ($row = mysqli_fetch_array($query)) 
		$sectionList[$row['section']] = $row['pending'];
?>

<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
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

    <!-- App styles -->
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/helper.css" />
    <link rel="stylesheet" href="styles/style.css">

</head>
<body class="fixed-navbar fixed-sidebar">

	<!-- Simple splash screen-->
	<div class="splash">
		<div class="color-line"></div>
		<div class="splash-title">
			<h1>    YOUTH PHOTOGRAPHIC SOCIETY | JURY PANEL  </h1>
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

<?php 
	include("master/header.php");
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
							<small class="text-muted">Jury / Admin<b class="caret"></b></small>
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
				<li class="active">
					<a href="home.php"> <span class="nav-label"><i class="fa fa-home"></i> Dashboard </span></a>
				</li>
				<li>
					<a href="rating.php"> <span class="nav-label"><i class="fa fa-star"></i> Rating </span></a>
				</li>		
				<li>
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

	<!-- Main Wrapper Dash Board -->
	<div id="wrapper">
		<div class="content animate-panel">
			<div class="row">
				<div class="col-lg-12 text-center m-t-md">
					<h2>Welcome to <?php echo $jury_name;?></h2>
					<p>Jury</p>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div class="hpanel">
						<div class="panel-heading">
							<div class="panel-tools">
								<a class="showhide"><i class="fa fa-chevron-up"></i></a>
								<a class="closebox"><i class="fa fa-times"></i></a>
							</div>
							Dashboard of Sections Assigned
						</div>
						<div class="panel-body">
							<div class="row">
							<?php
								foreach ($sectionList as $sectionName => $pendingPics) {
							?>
								<div class="col-lg-3">
									<div class="hpanel">
										<div class="panel-body text-center h-200">
											<i class="fa fa-4x fa-photo"></i>
											<h3><?php echo $sectionName;?></h3>
											<h3 class="m-xs font-extra-bold no-margins text-success"> <?php echo $pendingPics;?></h3>
											<small>Rating Pending</small>
										</div>
										<div class="panel-footer text-center">
											<a href="rating.php" class="btn btn-info btn-sm">Start Rating</a>
										</div>
									</div>
								</div>
							<?php
								}
							?>
							</div>
						</div>
						<div class="panel-footer">
							<span class="pull-right">
								Youth Photographic Society
							</span>
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

<!-- Vendor scripts -->
<script src="vendor/jquery/dist/jquery.min.js"></script>
<script src="vendor/jquery-ui/jquery-ui.min.js"></script>
<script src="vendor/slimScroll/jquery.slimscroll.min.js"></script>
<script src="vendor/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="vendor/jquery-flot/jquery.flot.js"></script>
<script src="vendor/jquery-flot/jquery.flot.resize.js"></script>
<script src="vendor/jquery-flot/jquery.flot.pie.js"></script>
<script src="vendor/flot.curvedlines/curvedLines.js"></script>
<script src="vendor/jquery.flot.spline/index.js"></script>
<script src="vendor/metisMenu/dist/metisMenu.min.js"></script>
<script src="vendor/iCheck/icheck.min.js"></script>
<script src="vendor/peity/jquery.peity.min.js"></script>
<script src="vendor/sparkline/index.js"></script>

<!-- App scripts -->
<script src="scripts/homer.js"></script>
<script src="scripts/charts.js"></script>

<script>

    $(function () {

        /**
         * Flot charts data and options
         */
        var data1 = [ [0, 55], [1, 48], [2, 40], [3, 36], [4, 40], [5, 60], [6, 50], [7, 51] ];
        var data2 = [ [0, 56], [1, 49], [2, 41], [3, 38], [4, 46], [5, 67], [6, 57], [7, 59] ];

        var chartUsersOptions = {
            series: {
                splines: {
                    show: true,
                    tension: 0.4,
                    lineWidth: 1,
                    fill: 0.4
                },
            },
            grid: {
                tickColor: "#f0f0f0",
                borderWidth: 1,
                borderColor: 'f0f0f0',
                color: '#6a6c6f'
            },
            colors: [ "#62cb31", "#efefef"],
        };

        $.plot($("#flot-line-chart"), [data1, data2], chartUsersOptions);

        /**
         * Flot charts 2 data and options
         */
        var chartIncomeData = [
            {
                label: "line",
                data: [ [1, 10], [2, 26], [3, 16], [4, 36], [5, 32], [6, 51] ]
            }
        ];

        var chartIncomeOptions = {
            series: {
                lines: {
                    show: true,
                    lineWidth: 0,
                    fill: true,
                    fillColor: "#64cc34"

                }
            },
            colors: ["#62cb31"],
            grid: {
                show: false
            },
            legend: {
                show: false
            }
        };

        $.plot($("#flot-income-chart"), chartIncomeData, chartIncomeOptions);



    });

</script>


</body>

</html>

<?php
	$img_count=0;
	$sql = "SELECT * FROM pic, section ";
	$sql .= " WHERE pic.section = section.section AND ";
	$sql .= "       '$session_id' IN (jury1, jury2, jury3, jury4, jury5)";
	$pics = mysqli_query($DBCON, $sql)or die(mysqli_error($DBCON));
	while($tr_pics=mysqli_fetch_array($pics)) {
		$pic_id=$tr_pics['pic_id'];
		$rating = mysqli_query($DBCON, "SELECT * FROM rating WHERE user_id='$session_id' AND pic_id='$pic_id'")or die(mysqli_error($DBCON));
		$count=mysqli_num_rows($rating);
		if($count==0)
			$img_count+=1;
	}
	$_SESSION['total_remaining_img']=$img_count;
}
else
{
	header("Location: ".$_SERVER['HTTP_REFERER']);
	//printf("<script>location.href='".$_SERVER['HTTP_REFERER']."'</script>");
}

?>