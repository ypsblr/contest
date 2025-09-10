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
$sectionCount = array();
if(isset($_SESSION['jury_id']) && $_SESSION['jury_type']=="PROJECTOR")
{
     $query = mysqli_query($DBCON, "SELECT section, COUNT(*) as count FROM pic, entry WHERE pic.entry_id = entry.entry_id GROUP BY section ") or die(mysqli_error($DBCON));
	 while ($row = mysqli_fetch_array($query)) 
		$sectionCount[$row['section']] = $row['count'];
?>



<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
       <title>Youth Photographic Society | Projection Panel</title>
	   
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
			<h1>    YOUTH PHOTOGRAPHIC SOCIETY | PROJECTION PANEL  </h1>
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
	<div id="header">
		<div class="color-line"></div>
		<div id="logo" class="light-version">
			<span>Youth Photographic Society</span>
		</div>
		<nav role="navigation">
			<div class="header-link hide-menu"><i class="fa fa-bars"></i></div>
			<div class="small-logo">
				<span class="text-primary">PROJECTION APP</span>
			</div>
			<form role="search" class="navbar-form-custom" method="post" action="#">
				<div class="form-group">
					<input type="text" placeholder="Search something special" class="form-control" name="search">
				</div>
			</form>
			<div class="mobile-menu">
				<button type="button" class="navbar-toggle mobile-menu-toggle" data-toggle="collapse" data-target="#mobile-collapse">
					<i class="fa fa-edit"></i>
				</button>
				<div class="collapse mobile-navbar" id="mobile-collapse">
					<ul class="nav navbar-nav">
						<li>
							<a class="" href="index.php">Logout</a>
						</li>
						<li>
							<a data-toggle="modal" data-target="#myModal">Profile</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
	</div>
	
	<!-- Navigation -->
	<aside id="menu">
		<div id="navigation">
			<div class="profile-picture">
				<a href="#"><img src="images/yps.jpg" class="img-circle m-b" alt="logo"></a>
				<div class="stats-label text-color">
					<span class="font-extra-bold font-uppercase">Projection Master</span>
					<div class="dropdown">
						<a class="dropdown-toggle" href="#" data-toggle="dropdown">
							<small class="text-muted">Projection Control<b class="caret"></b></small>
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
					<a href="projector_home.php"> <span class="nav-label"><i class="fa fa-home"></i> Dashboard </span></a>
				</li>            
				<li>
					<a href="#"><span class="nav-label"><i class="fa fa-mortar-board"></i> Projector</span><span class="fa arrow"></span> </a>
					<ul class="nav nav-second-level">
					<?php
						foreach ($sectionCount as $section => $count) {
					?>
						<li><a href="projector-view.php?sections=<?php echo encode_string_array("<?php echo $section;?>");?> &next=0"><?php echo $section;?></a></li>
					<?php
						}
					?>
					</ul>
				</li>
			</ul>
		</div>
	</aside>



	<div id="wrapper">
		<div class="content animate-panel">
			<div class="row">
				<div class="col-lg-12 text-center m-t-md">
					<h2>Welcome to Projection View</h2>
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
							Picture Submission Dashboard
						</div>
						<div class="panel-body">
							<div class="row">
							<?php
								foreach($sectionCount as $section => $count) {
							?>
								<div class="col-lg-3">
									<div class="hpanel">
										<div class="panel-body text-center h-200">
											<i class="fa fa-4x fa-photo"></i>
											<h3><?php echo $section;?></h3>
											<h3 class="m-xs font-extra-bold no-margins text-success"> <?php echo $count;?></h3>
											<small>Total Image</small>
										</div>
										<div class="panel-footer text-center">
											<a href="projector-view.php?sections=<?php echo encode_string_array("$section");?> &next=0" class="btn btn-info btn-sm">Project</a>
										</div>
									</div>
								</div>
							<?php
								}
							?>
							</div>
						</div>
						<div class="panel-footer">
							<p id="msg-line">Have a Good Day</p>
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

<!-- Simple Javascript to mediate results upload -->
<script>
// Global Variable
var server_url = "http://salon.localhost/jurypanel/svc/put_table_data.php";

// Since $.post executes async queries, results may not arrive on the next line. Hence this function is called when $.post data is received
function send_data_to_server(tableref, table_data) {
	
	// As simple as this -> just send data to the server
	$("#msg-line").html("Uploading " + tableref + " Data");
	$.post (server_url, 
			{ "data" : table_data, "user" : "projector", "auth" : "projector!@#" }, 
			function (data, status) {
				if (status == "success") alert(data);
				$("#msg-line").html("Finshed Uploading " + tableref + " Data");
				alert("Finshed Uploading " + tableref + " Data");
			}
	);

	
}

function isValidJSON(str)
{
	try {
		JSON.parse(str);
	}
	catch(e) {
		return false;
	}
	return true;
}

function upload_rating() {
	// Simple Results Uploader
	// Calls a local script to assemble data
	// Then calls a generic server script to load data into the target table
	
	// Retrieve & upload rating data
	$("#msg-line").html("Assembling Rating Data");
	$.post (
		"svc/get_table_data.php",
		{ "table" : "rating" },
		function (data, status) {
			if (status == "success") {
				if (isValidJSON(data))
					send_data_to_server("Rating", data);	// Send Data to Server when ready with valid data
				else
					alert(data);	// Probably error message
			}
			else
				alert(status);
		},
		"text"
	);
}

function upload_result() {
	// Simple Results Uploader
	// Calls a local script to assemble data
	// Then calls a generic server script to load data into the target table
	
	// Retrieve & upload result data
	$("#msg-line").html("Assembling Result Data");
	$.post (
		"svc/get_table_data.php",
		{ "table" : "result" },
		function (data, status) {
			if (status == "success") {
				if (isValidJSON(data))
					send_data_to_server("Result", data);	// Send Data to Server when ready with valid data
				else
					alert(data);	// Probably error message
			}
			else
				alert(status);
		},
		"text"
	);
}


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