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

if(isset($_POST['picid']))
$_SESSION['picid']=$_POST['picid'];


if(isset($_SESSION['jury_id']) && isset($_SESSION['picid']))
{

     $picid =$_SESSION['picid'];
	 

  
     $pics = mysqli_query($DBCON, "SELECT * FROM pics WHERE id='$picid' or img_title LIKE '%$picid%'")or die(mysqli_error($DBCON));
     $tr_pics=mysqli_fetch_array($pics);
	 $uid=$tr_pics['uid'];

	 $entry = mysqli_query($DBCON, "SELECT * FROM entry WHERE id='$uid'")or die(mysqli_error($DBCON));
     $tr_entry=mysqli_fetch_array($entry);

	 $rate = mysqli_query($DBCON, "SELECT SUM(rate) FROM rating WHERE pic_id='$uid'")or die(mysqli_error($DBCON));
     $tr_rate=mysqli_fetch_array($rate);


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
    <link rel="stylesheet" href="vendor/blueimp-gallery/css/blueimp-gallery.min.css" />

    <!-- App styles -->
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/helper.css" />
    <link rel="stylesheet" href="styles/style.css">

	
<style type="text/css">
.containerBox {
    position: relative;
    display: inline-block;
}
.text-box {
    position: absolute;    

    text-align: center;    
    width: 100%;
}
.text-box:before {
   content: '';
   display: inline-block;
   height: 100%;
   vertical-align: middle;
}
h4 {
   display: inline-block;
   font-size: 18px; /*or whatever you want*/
   color: #FFF;   
}
img {
  display: block;
  max-width: 100%;
  height: auto;
}
</style>


</head>
<body class="fixed-navbar fixed-sidebar">

<!-- Simple splash screen-->
<div class="splash"> <div class="color-line"></div><div class="splash-title"><h1>    YOUTH PHOTOGRAPHY | JURY ADMIN PANEL  </h1><p>Please Wait. </p><div class="spinner"> <div class="rect1"></div> <div class="rect2"></div> <div class="rect3"></div> <div class="rect4"></div> <div class="rect5"></div> </div> </div> </div>
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<!-- Header -->
<!-- Header -->
<div id="header">
    <div class="color-line">
    </div>
    <div id="logo" class="light-version">
        <span>
               Youth Photographic
        </span>
    </div>
    <nav role="navigation">
        <div class="header-link hide-menu"><i class="fa fa-bars"></i></div>
        <div class="small-logo">
            <span class="text-primary">HOMER APP</span>
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
            <a href="#">
                <img src="images/jury/user.jpg" class="img-circle m-b" alt="logo">
            </a>

            <div class="stats-label text-color">
                <span class="font-extra-bold font-uppercase">Project master</span>

                <div class="dropdown">
                    <a class="dropdown-toggle" href="#" data-toggle="dropdown">
                        <small class="text-muted">Jury Member of YPS 2k16 <b class="caret"></b></small>
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
                    <li><a href="projector-view.php?sections=<?php echo encode_string_array("Monochrome_Digital");?> &next=0">Monochrome</a></li>
                    <li><a href="projector-view.php?sections=<?php echo encode_string_array("Colour_Digital");?>&next=0">Color</a></li>
                    <li><a href="projector-view.php?sections=<?php echo encode_string_array("Nature_Digital");?>&next=0">Nature-Wildlife</a></li>
					<li><a href="projector-view.php?sections=<?php echo encode_string_array("Travel_Digital");?>&next=0">Photo Travel Digital</a></li>
					<li><a href="projector-view.php?sections=<?php echo encode_string_array("Photo_Journalism");?>&next=0">Photo Journalism Digital</a></li>

                </ul>
            </li>
        </ul>
    </div>
</aside>
<!-- Main Wrapper -->
<div id="wrapper">
   <div class="content animate-panel">
    
	    <div class="row">
        
        <div class="col-md-12">
            <div class="hpanel">

              					<form method="post" action="image-search.php">
					 <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search Pic id or title" name="picid" required>
                           <span class="input-group-btn">
                                <button class="btn btn-info" type="submit" name="image-search"><i class="glyphicon glyphicon-plus small"></i>Search </button>
                           </span>
                    </div>
</form>     

<div class="panel-body">
                    <div class="tab-content">
                        <div id="note1" class="tab-pane active">
                            <div class="note-content">
							<div class="row">
							<div class="col-md-4 col-lg-4">
								 <div class="lightBoxGallery" style="text-align: unset;">
								 
                                     	 <a href="../img/upload/<?php echo $tr_pics['pic'];?>" title="<?php echo $tr_pics['id'];?> & Title: <?php echo ucwords($tr_pics['img_title']);?>" data-gallery=""><img class="img-responsive" src="../img/upload/<?php echo $tr_pics['pic'];?>" style="width:240px; height:160px"></a>
                                  </div>                        
								</div>
                      
					  <div class="col-md-6 col-lg-6">
					  								<table class="table table-bordered table-striped" cellspacing="1" cellpadding="1">

													<tr><td><b> Image ID: </b>  <?php echo $tr_pics['id'];?></td></tr>
								<tr><td><b> YPS ID: </b> YPS<?php printf("%03d", $tr_pics['uid']);?></td></tr>
								<tr><td><b> Title </b> <?php echo $tr_pics['img_title'];?></td></tr>
								<tr><td><b> Name: </b> <?php echo $tr_entry['name'];?></td></tr>
								<tr><td><b> Sections: </b> <?php echo $tr_pics['type'];?></td></tr>
								<tr><td><b> Total Rating: </b> <?php echo $tr_rate['rate'];?></td></tr>
								 </table>
								 </div>
								 </div>
								
								</p>
                            </div>
                        </div>

                   </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Right sidebar -->


    <!-- Footer-->
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



<!-- Vendor scripts -->
<script src="vendor/jquery/dist/jquery.min.js"></script>
<script src="vendor/slimScroll/jquery.slimscroll.min.js"></script>
<script src="vendor/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="vendor/metisMenu/dist/metisMenu.min.js"></script>
<script src="vendor/iCheck/icheck.min.js"></script>
<script src="vendor/sparkline/index.js"></script>
<script src="vendor/blueimp-gallery/js/jquery.blueimp-gallery.min.js"></script>
<script src="vendor/jquery-ui/jquery-ui.min.js"></script>
<!-- App scripts -->
<script src="scripts/homer.js"></script>

<!-- Local style for demo purpose -->
<style>

    .lightBoxGallery {
        text-align: center;
    }

    .lightBoxGallery a {
        margin: 5px;
        display: inline-block;
    }

</style>

<script src="vendor/sparkline/index.js"></script>
<script src="vendor/bootstrap-star-rating/js/star-rating.min.js"></script>



<!-- Local styel for demo purpose - remove text shadow from oryginal plugin styles -->
<style>
    .rating-container .rating-stars:before {
        text-shadow: none;
    }
</style>
<script src="vendor/jquery-validation/jquery.validate.min.js"></script>

<script>

    $(function () {
        // initialize with defaults
        $("#input-1").rating();

    });

</script>
<script>

    $(function(){

        $("#form").validate({
            rules: {
                password: {
                    required: true,
                    minlength: 3
                },
                url: {
                    required: true,
                    url: true
                },
                number: {
                    required: true,
                    number: true
                },
                max: {
                    required: true,
                    maxlength: 4
                }
            },
            submitHandler: function(form) {
                form.submit();
            }
        });

        $("#form_2").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3
                },
                username: {
                    required: true,
                    minlength: 5
                },
                url: {
                    required: true,
                    url: true
                },
                number: {
                    required: true,
                    number: true
                },
                last_name: {
                    required: true,
                    minlength: 6
                }
            },
            messages: {
                number: {
                    required: "(Please enter your phone number)",
                    number: "(Please enter valid phone number)"
                },
                last_name: {
                    required: "This is custom message for required",
                    minlength: "This is custom message for min length"
                }
            },
            submitHandler: function(form) {
                form.submit();
            },
            errorPlacement: function(error, element) {
                $( element )
                        .closest( "form" )
                        .find( "label[for='" + element.attr( "id" ) + "']" )
                        .append( error );
            },
            errorElement: "span",
        });


    });
</script>

</body>
</html>
<?php
}
else
{
header("Location: projector_home.php");
printf("<script>location.href='projector_home.php'</script>");
}

?>