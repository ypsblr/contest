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
function encode_string_array ($stringArray) {
	$s = strtr(base64_encode(addslashes(gzcompress(serialize($stringArray),2))), '+/=', '_,');
   
	return $s;
}	
function decode_string_array ($stringArray) {
	$s = unserialize(gzuncompress(stripslashes(base64_decode(strtr($stringArray, '_,', '+/=')))));
	return $s;
}
							
if(isset($_SESSION['jury_id']) && ($_SESSION['jury_type']=="MASTER" || $_SESSION['jury_type']=="ADMIN")) {
	$session_id=$_SESSION['jury_id'];
	$user_query = mysqli_query($DBCON, "SELECT * FROM user WHERE user_id = '$session_id'") or die(mysqli_error($DBCON));
	$user_row = mysqli_fetch_array($user_query);
	$jury_login = $user_row['login'];
	$jury_name = $user_row['name'];
	$jury_pic = $user_row['avatar'];
	$user_type = $user_row['type'];
	
	// Get Contest Information
	$query = mysqli_query($DBCON, "SELECT * FROM contest") or die(mysqli_error($DBCON));
	$contest = mysqli_fetch_array($query);
	
	
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
    <link rel="stylesheet" href="vendor/datatables.net-bs/css/dataTables.bootstrap.min.css" />

    <!-- App styles -->
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/helper.css" />
    <link rel="stylesheet" href="styles/style.css">
	
	<!-- Scripts -->
	<script src='vendor/tinymce/tinymce.min.js'></script>
	<script src='vendor/tinymce/plugins/link/plugin.min.js'></script>
	<script src='vendor/tinymce/plugins/lists/plugin.min.js'></script>

</head>
<body class="fixed-navbar fixed-sidebar">

	<!-- Simple splash screen-->
	<div class="splash"> 
		<div class="color-line"></div>
		<div class="splash-title">
			<h1>    YOUTH PHOTOGRAPHIC SOCIETY | JURY/ADMIN PANEL  </h1>
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
		include"master/master_header.php";
		include"master/master_sidebar.php";
	?>
	<!-- Main Wrapper -->
	<div id="wrapper">
		<?php
			if(isset($_SESSION['signup_msg'])) {
		?>
		<div class="alert alert-success">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			<strong>Status:) </strong>	<?php echo $_SESSION['signup_msg']; ?>
		</div>
		<?php
				unset($_SESSION['signup_msg']);
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
		<div class="content animate-panel">
			<div class="row">
				<!-- EDIT CONTEST -->
				<div class="col-md-6 col-lg-6">
					<div class="hpanel">
						<div class="panel-body">
							<div class="text-center m-b-md">
								<h3>Edit Contest Information</h3>
							</div>
							<form action="op/update_contest.php" id="loginForm" method="post" onsubmit="return validate_contest()" enctype="multipart/form-data">
								<div class="row">
									<p class="text text-danger" id="ct_err"></p>
									<div class="form-group col-lg-12">
										<label>Contest Name</label>
										<input type="text" class="form-control" name="name" id="ct_name" value="<?php echo $contest['name'];?>" >
									</div>
								</div>
								<div class="row">
									<div class="form-group col-lg-6 col-md-6">
										<label>Start Date</label>
										<input type="date" class="form-control" name="submission_start_date" id="ct_submission_start_date" value="<?php echo $contest['submission_start_date'];?>" >
									</div>
									<div class="form-group col-lg-6 col-md-6">
										<label>End Date</label>
										<input type="date" class="form-control" name="submission_last_date" id="ct_submission_last_date" value="<?php echo $contest['submission_last_date'];?>" >
									</div>
									<div class="form-group col-lg-6 col-md-6">
										<label>Judging Date</label>
										<input type="date" class="form-control" name="judging_date" id="ct_judging_date" value="<?php echo $contest['judging_date'];?>" >
									</div>
									<div class="form-group col-lg-6 col-md-6">
										<label>Results Date</label>
										<input type="date" class="form-control" name="results_date" id="ct_results_date" value="<?php echo $contest['results_date'];?>" >
									</div>
								</div>
								<div classs="row">
									<div class="form-group col-lg-12">
										<label>Theme Definition (max 255 characters)</label>
										<input type="text" class="form-control" maxlength="255" name="theme" id="ct_theme" value="<?php echo $contest['theme'];?>"required>
									</div>
								</div>
								<div classs="row">
									<div class="form-group col-lg-12">
										<label>Short Description (max 500 characters)</label>
										<textarea rows="5" class="form-control html-editor" maxlength="500" name="short_description" id="ct_short_description" ><?php echo $contest['short_description'];?></textarea>
									</div>
								</div>
								<div classs="row">
									<div class="form-group col-lg-12">
										<label>Full Description (max 5000 characters)</label>
										<textarea rows="10" class="form-control html-editor" maxlength="5000" name="description" id="ct_description" ><?php echo $contest['description'];?></textarea>
									</div>
								</div>
								<div classs="row">
									<div class="form-group col-lg-12">
										<label>Terms and Conditions (max 5000 characters)</label>
										<textarea rows="10" class="form-control html-editor" maxlength="5000" name="terms_conditions" id="ct_terms_conditions" ><?php echo $contest['terms_conditions'];?></textarea>
									</div>
								</div>
								<div classs="row">
									<div class="form-group col-lg-12">
										<label>Announcement Text (max 255 characters)</label>
										<textarea rows="3" class="form-control html-editor" maxlength="255" name="announcement" id="ct_announcement" ><?php echo $contest['announcement'];?></textarea>
									</div>
								</div>
								<div classs="row">
									<div class="form-group col-lg-12">
										<label>Judging Type</label>
										<select class="form-control" name="jury_mode" required>
											<option value="PROJECT" <?php echo ($contest['jury_mode'] == "PROJECT") ? "selected" : "";?>>Project &amp; Score</option>
											<option value="REMOTE" <?php echo ($contest['jury_mode'] == "REMOTE") ? "selected" : "";?>>Remote Recommend &amp; Finalize</option>
										</select>
									</div>
								</div>
								<div classs="row">
									<div class="form-group col-lg-12">
										<label>Slider 1 (1920 x 500 pixels)</label>
										<img src="/img/slide-1.jpg" style="max-width: 100%">
										<input type="file" accept=".jpg, .jpeg" width="1920" height="500" class="form-control" name="slide-1">
									</div>
									<div class="form-group col-lg-12">
										<label>Slider 2 (1920 x 500 pixels)</label>
										<img src="/img/slide-2.jpg"  style="max-width: 100%" >
										<input type="file"  accept=".jpg, .jpeg" width="1920" height="500" class="form-control" name="slide-2">
									</div>
									<div class="form-group col-lg-12">
										<label>Slider 3 (1920 x 500 pixels)</label>
										<img src="/img/slide-3.jpg"  style="max-width: 100%">
										<input type="file"  accept=".jpg, .jpeg" width="1920" height="500" class="form-control" name="slide-3">
									</div>
									<div class="form-group col-lg-12">
										<label>Slider 4 (1920 x 500 pixels)</label>
										<img src="/img/slide-4.jpg"  style="max-width: 100%">
										<input type="file"  accept=".jpg, .jpeg" width="1920" height="500" class="form-control" name="slide-4">
									</div>
								</div>
								<div class="row pull-right">
									<button class="btn btn-success" type="submit" name="update_contest">Update / Create</button>
									<button class="btn btn-warning" type="submit" name="init_contest">Initialize Data</button>
								</div>
							</form>
						</div>
					</div>
				</div>		<!-- END of JURY ADD/EDIT Screen -->
		
				<!-- ADD SECTIONS -->
				<div class="col-md-6 col-lg-6">
					<div class="hpanel">
						<div class="panel-body">
							<div class="text-center m-b-md">
								<h3>Edit Section</h3>
							</div>
							<form id="section-list-form" >
								<div class="form-group col-lg-12 col-md-12" id="section-list">
									<label>Section List</label>
									<?php
										$idx = 0;
										// Load Current Sections
										$query = mysqli_query($DBCON, "SELECT * FROM section") or die(mysqli_error($DBCON));
										while ($row = mysqli_fetch_array($query)) {
									?>
									<div class="row" data-row-type="delete" id="section_list[<?php echo $idx;?>]">
										<div class="form-group col-lg-10 col-md-10 col-sm-10 col-xs-10">
											<input type="text" maxlength="255" value="<?php echo $row['section'];?>" class="form-control" data-visible="section" name="section[<?php echo $idx;?>]" id="section[<?php echo $idx;?>]" readonly >
										</div>
										<div class="form-group col-lg-1 col-md-1 col-sm-1 col-xs-1">
											<a href="javascript:edit_section(<?php echo $idx;?>)" class="btn btn-warning" ><i class="glyphicon glyphicon-edit small"></i></a>
										</div>
										<div class="form-group col-lg-1 col-md-1 col-sm-1 col-xs-1">
											<a href="javascript:delete_section(<?php echo $idx;?>)" class="btn btn-danger" ><i class="glyphicon glyphicon-trash small"></i></a>
										</div>
									</div>
									<?php
											$idx++;
										}
									?>
								</div>
							</form>
							<hr>
							<!-- Add Section Row -->
							<form id="section-edit-form" >
								<div class="row" data-row-type="add" id="section_list[<?php echo $idx;?>]">
									<input type="hidden" name="last_index" id="last_index" value="<?php echo $idx;?>" >
									<p class="text text-danger" id="section_err"></p>
									<div class="form-group col-lg-11 col-md-11 col-sm-11 col-xs-11">
										<label>Add/Edit Section</label>
										<input type="text" maxlength="255" value="" class="form-control" data-visible="section" name="section" id="section" >
									</div>
									<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<label>Description (Max 255 characters)</label>
										<input type="text" maxlength="255" class="form-control" data-visible="description" name="description"  id="description" value="" >
									</div>
									<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<label>Rules (Max 5000 characters)</label>
										<textarea rows="5" class="form-control html-editor" maxlength="5000" name="rules" id="rules" data-visible="rules" ></textarea>
									</div>
								</div>
								<div class="row"><br></div>
								<div class="row">
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"></div>
									<div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-4">
										<a href="javascript:void(0)" class="form-control btn btn-primary" name="update_section" id="update_section" >Submit</a>
									</div>
									<div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-4">
										<a href="javascript:void(0)" class="form-control btn btn-primary" name="clear_section" id="clear_section" >Clear</a>
									</div>
								</div>								
							</form>
						</div>
					</div>
				</div>		<!-- END OF SECTION -->

				<!-- ADD AWARDS -->
				<div class="col-md-6 col-lg-6">
					<div class="hpanel">
						<div class="panel-body">
							<div class="text-center m-b-md">
								<h3>Edit Awards</h3>
							</div>
							<form id="awared-list-form" >
								<div class="form-group col-lg-12 col-md-12" id="award-list">
									<label>Award List</label>
									<?php
										$idx = 0;
										// Load Current Sections
										$query = mysqli_query($DBCON, "SELECT * FROM award ORDER BY section, level, sequence") or die(mysqli_error($DBCON));
										while ($row = mysqli_fetch_array($query)) {
									?>
									<div class="row" data-row-type="delete" id="award_list[<?php echo $idx;?>]">
										<input type="hidden" name="award_id[<?php echo $idx;?>]" id="award_id[<?php echo $idx;?>]" value="<?php echo $row['award_id'];?>">
										<div class="form-group col-lg-1 col-md-1 col-sm-1 col-xs-1">
											<input type="text" name="award_level[<?php echo $idx;?>]" id="award_level[<?php echo $idx;?>]" value="<?php echo $row['level'];?>" class="form-control" readonly >
										</div>
										<div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="text" name="award_section[<?php echo $idx;?>]" id="award_section[<?php echo $idx;?>]" value="<?php echo $row['section'];?>" class="form-control" readonly >
										</div>
										<div class="form-group col-lg-5 col-md-5 col-sm-5 col-xs-5">
											<input type="text" name="award_name[<?php echo $idx;?>]" id="award_name[<?php echo $idx;?>]" value="<?php echo $row['name'];?>" class="form-control" readonly >
										</div>
										<div class="form-group col-lg-1 col-md-1 col-sm-1 col-xs-1">
											<a href="javascript:edit_award(<?php echo $idx;?>)" class="btn btn-warning" ><i class="glyphicon glyphicon-edit small"></i></a>
										</div>
										<div class="form-group col-lg-1 col-md-1 col-sm-1 col-xs-1">
											<a href="javascript:delete_award(<?php echo $idx;?>)" class="btn btn-danger" ><i class="glyphicon glyphicon-trash small"></i></a>
										</div>
									</div>
									<?php
											$idx++;
										}
									?>
								</div>
							</form>
							<hr>
							<!-- Add Award Row -->
							<form id="award-edit-form" >
								<div class="row">
									<input type="hidden" name="last_award_index" id="last_award_index" value="<?php echo $idx;?>" >
									<input type="hidden" name="award_id" id="award_id" value="0">
									<p class="text text-danger" id="award_err"></p>
									<div class="form-group col-lg-11 col-md-11 col-sm-11 col-xs-11">
										<label>Section</label>
										<select class="form-control" name="award_section" id="award_section">
										<?php
											$qq = mysqli_query($DBCON, "SELECT * FROM section") or die(mysqli_error($DBCON));
											while ($rr = mysqli_fetch_array($qq)) {
										?>
											<option value="<?php echo $rr['section'];?>" ><?php echo $rr['section'];?></option>
										<?php
											}
										?>
										</select>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<label>Award Name (Max 255 characters)</label>
										<input type="text" maxlength="255" class="form-control" name="award_name" id="award_name" value="" >
									</div>
								</div>
								<div class="row">
									<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<label>Award Description (Max 500 characters)</label>
										<textarea rows="4" class="form-control" maxlength="500" name="award_description" id="award_description" ></textarea>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-3">
										<label>Level</label>
										<input type="number" class="form-control" name="award_level"  id="award_level" value="0" >
									</div>
									<div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-3">
										<label>Sequence</label>
										<input type="number" class="form-control" name="award_sequence"  id="award_sequence" value="0" >
									</div>
									<div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-3">
										<label># Awards</label>
										<input type="number" class="form-control" name="award_number"  id="award_number" value="0" >
									</div>
									<div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-3">
										<label>Cash</label>
										<input type="number" class="form-control" name="award_cash"  id="award_cash" value="0" >
									</div>
								</div>
								
								<div class="row"><br></div>
								<div class="row">
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"></div>
									<div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-4">
										<a href="javascript:void(0)" class="form-control btn btn-primary" name="update_award" id="update_award" >Submit</a>
									</div>
									<div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-4">
										<a href="javascript:void(0)" class="form-control btn btn-primary" name="clear_award" id="clear_award" >Clear</a>
									</div>
								</div>								
							</form>
						</div>
					</div>
				</div>		<!-- END OF SECTION -->
				
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

<script>
// TinyMCE
$(document).ready(function(){
	tinymce.init({
		selector: '.html-editor',
		plugins : 'link lists',
		menubar: false,
		toolbar: 'formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link '
	});
});

</script>

<script>

// Validate Assignments Form
function validate_form() {
	
	var assign = [];
	var assignments = document.getElementById("assignments");
	
	// Gather all values
	var sectionsVisible = getElementsByAttributeValue(assignments, "data-visible", "section");
	var descriptionsVisible = getElementsByAttributeValue(assignments, "data-visible", "description");
	var rulessVisible = getElementsByAttributeValue(assignments, "data-visible", "rules");
	
	var valid_form = true;
	
	
	// 1. Remove last line if empty. Otherwise flag error if any field is missing
	var last_row = sectionsVisible.length - 1;
	if (sectionsVisible[last_row].value == "" && descriptionsVisible[last_row].value == "" && rulesVisible[last_row].value == "")  // Empty Row. Remove from validation
		-- last_row;
	else {
		// Flag Error if any fields are missing
		if (sectionsVisible[last_row].value == "") {
			alert("Section not entered in the last row");
			valid_form = false;
		}
		if (descriptionsVisible[last_row].value == "") {
			alert("Description not entered in the last row");
			valid_form = false;
		}
		if (rulesVisible[last_row].value == "") {
			alert("Rules not entered in the last row");
			valid_form = false;
		}
	}
		
	return valid_form;
}
</script>

<script>
// Validate values of contest form
function longer(field_id) {
	return ($("#" + field_id).val().length > $("#" + field_id).attr("maxlength"));
}

function validate_contest() {
	
	tinyMCE.triggerSave();
	if (longer("ct_name")) {
		$("#ct_err").html("Name exceeds maximum length");
		return false;
	}
	if (longer("ct_theme")) {
		$("#ct_err").html("Theme exceeds maximum length");
		return false;
	}
	if (longer("ct_short_description")) {
		$("#ct_err").html("Short Description exceeds maximum length");
		return false;
	}
	if (longer("ct_description")) {
		$("#ct_err").html("Description exceeds maximum length");
		return false;
	}
	if (longer("ct_terms_conditions")) {
		$("#ct_err").html("Terms and Conditions exceeds maximum length");
		return false;
	}
	if (longer("ct_announcement")) {
		$("#ct_err").html("Announcement exceeds maximum length");
		return false;
	}	
	
}
</script>

<!-- Scripts to handle assignment rows -->
<script>
// Return an Array of Elements that match attribute-value pair
function getElementsByAttributeValue(groupElement, attribute, attributeValue) {
	var i;
	var childElements = [];
	var childElement;
	
	for (i = 0; i < groupElement.children.length; ++i) {
		childElement = groupElement.children[i];
		if (childElement.getAttribute(attribute) == attributeValue)
			childElements.push(childElement);
		else if (childElement.children.length > 0)   	// Recursively check at the next level
			childElements = childElements.concat(getElementsByAttributeValue(childElement, attribute, attributeValue));
	}
	return childElements;
}

// Returns the first Element that matches attribute-value pair
function getElementByAttributeValue(groupElement, attribute, attributeValue) {
	var i;
	var childElement;
	for (i = 0; i < groupElement.children.length; ++i) {
		childElement = groupElement.children[i];
		if (childElement.getAttribute(attribute) == attributeValue)
			return childElement;
		else if (childElement.children.length > 0)  	// Recursively check at the next level
			if (childElement = getElementByAttributeValue(childElement, attribute, attributeValue))
				return childElement;
	}
	return null;
}

$("#clear_section").click(function() {
	$("#section").val("");
	$("#section").removeAttr("readonly");
	$("#description").val("");
	$("#rules").val("");
	tinyMCE.get("rules").load();
});

// Handle Update Section
$("#update_section").click(function(){
	$("#section_err").html("");
	var idx = $("#last_index").val() * 1;
	
	// Validate inputs
	var section = $("#section").val();
	var description = $("#description").val();
	tinyMCE.get("rules").save();
	var rules = $("#rules").val();

	var valid_row = true;
	if (section > "" && description > "" && rules > "") {
		// Validate if the section already exists
		var i;
		for (i = 0; i < idx; i++) {			// Check all previous sections i < idx
			if (document.getElementById("section[" + i.toString() + "]")) {
				var compare_section = document.getElementById("section[" + i.toString() + "]");
				if (section.value == compare_section.value) {
					$("#section_err").html('Section ' + compare_section.value + ' has already been created');
					valid_row = false;
				}	
			}
		}
	}
	else {
		$("#section_err").html("Please fill all the fields");
		valid_row = false;
	}

	if (valid_row) { 							// Add only if the data is valid	
		// Save the Section
		$.post(
			"op/update_section.php",
			{
				section: section,
				description: description,
				rules: rules
			},
			function(json) {
				var data = JSON.parse(json);
				if (data["status"] == "OK") {
					if (data['operation'] == 'INSERT') {
						// Add section to the section list
						var html = "";
						html += '<div class="row" data-row-type="delete" id="section_list[' + idx + ']"> ';
						html += '	<div class="form-group col-lg-10 col-md-10 col-sm-10 col-xs-10"> ';
						html += '		<input type="text" maxlength="255" value="' + section + '" class="form-control" data-visible="section" name="section[' + idx + ']" id="section[' + idx + ']" readonly > ';
						html += '	</div> ';
						html += '	<div class="form-group col-lg-1 col-md-1 col-sm-1 col-xs-1"> ';
						html += '		<a href="javascript:edit_section(' + idx + ')" class="btn btn-warning"><i class="glyphicon glyphicon-edit small"></i></a> ';
						html += '	</div> ';
						html += '	<div class="form-group col-lg-1 col-md-1 col-sm-1 col-xs-1"> ';
						html += '		<a href="javascript:delete_section(' + idx + ')" class="btn btn-danger" ><i class="glyphicon glyphicon-trash small"></i></a> ';
						html += ' 	</div> ';
						html += '</div> ';
						$("#section-list").append(html);
						$("#last_index").val(idx + 1);
					}
					
					// Clear Fields
					$("#section").val("");
					$("#section").removeAttr("readonly");
					$("#description").val("");
					$("#rules").val("");
					tinyMCE.get("rules").load();
				}
				else {
					$("#section_err").val(data["errmsg"]);
				}
			},
			"text"
		);
	}
});

function edit_section(idx) {
	var section = document.getElementById("section[" + idx.toString() + "]");
	$.post(
		"op/get_section.php",
		{	section: section.value	},
		function(json) {
			var data = JSON.parse(json);
			if (data["status"] == "OK") {
				// Add section to the section list
				$("#section").val(section.value);
				$("#description").val(data["description"]);
				$("#rules").val(data["rules"]);
				tinyMCE.get("rules").load();
				$("#section").attr("readonly","");
			}
			else
				$("#section_err").html(data['errmsg']);
		},
		"text"
	);
	
}

function delete_section(idx) {
	var section = document.getElementById("section[" + idx.toString() + "]");
	$.post(
		"op/del_section.php",
		{	section: section.value	},
		function(json) {
			var data = JSON.parse(json);
			if (data["status"] == "OK") {
				// Remove section from the section list
				var row = document.getElementById("section_list["+idx.toString()+"]");
				var parent = row.parentNode;
				parent.removeChild(row);
			}
			else
				$("#section_err").html(data['errmsg']);
		},
		"text"
	);
}

</script>

<!-- AWARDS -->
<script>
$("#clear_award").click(function() {
	$("#award_id").val("0");
	$("#award_level").val("0");
	$("#section").val("");
	$("#award_name").val("");
	$("#award_description").val("");
	$("#award_sequence").val("0");
	$("#award_number").val("0");
	$("#award_cash").val("0");
	$("award_err").html("");
});

// Handle Update Award
$("#update_award").click(function(){
	$("#award_err").html("");
	var idx = $("#last_award_index").val() * 1;
	
	// Validate inputs
	var award_id = $("#award_id").val();
	var award_level = $("#award_level").val();
	var award_section = $("#award_section").val();
	var award_name = $("#award_name").val();
	var award_sequence = $("#award_sequence").val();
	var award_description = $("#award_description").val();
	var award_number = $("#award_number").val();
	var award_cash = $("#award_cash").val();

	if (award_level > "0" && award_name > "" && award_sequence > "0" && award_description > "" && award_number > "0") {
		// Save the Award
		$.post(
			"op/update_award.php",
			{
				award_id: award_id,
				award_level: award_level,
				award_section: award_section,
				award_name: award_name,
				award_sequence: award_sequence,
				award_description: award_description,
				award_number: award_number,
				award_cash: award_cash
			},
			function(json) {
				var data = JSON.parse(json);
				if (data["status"] == "OK") {
					if (award_id == data['award_id']) {
						// Case of update. Let us update the list
						// Find the index
						var i = 0;
						for (i = 0; i < idx; ++i) {
							if ($("#award_id\\[" + i + "\\]").val() == award_id) {
								$("#award_level\\[" + i + "\\]").val(award_level);
								$("#award_section\\[" + i + "\\]").val(award_section);
								$("#award_name\\[" + i + "\\]").val(award_name);
							}
						}
					}
					else {
						// Add section to the section list
						var html = "";
						html += '<div class="row" data-row-type="delete" id="award_list[' + idx + ']"> ';
						html += '	<input type="hidden" name="award_id[' + idx + ']" id="award_id[' + idx + ']" value="' + data['award_id'] + '"> ';
						html += '	<div class="form-group col-lg-1 col-md-1 col-sm-1 col-xs-1"> ';
						html += '		<input type="text" name="award_level[' + idx + '] id="award_level[' + idx + '] value="' + award_level + '" class="form-control" readonly > ';
						html += '	</div> ';
						html += '	<div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-4"> ';
						html += '		<input type="text" name="award_section[' + idx + '] id="award_section[' + idx + '] value="' + award_section + '" class="form-control" readonly > ';
						html += '	</div> ';
						html += '	<div class="form-group col-lg-5 col-md-5 col-sm-5 col-xs-5"> ';
						html += '		<input type="text" name="award_name[' + idx + '] id="award_name[' + idx + '] value="' + award_name + '" class="form-control" readonly > ';
						html += '	</div> ';
						html += '	<div class="form-group col-lg-1 col-md-1 col-sm-1 col-xs-1"> ';
						html += '		<a href="javascript:edit_award(' + idx + ')" class="btn btn-warning" ><i class="glyphicon glyphicon-edit small"></i></a> ';
						html += '	</div> ';
						html += '	<div class="form-group col-lg-1 col-md-1 col-sm-1 col-xs-1"> ';
						html += '		<a href="javascript:delete_award(' + idx + ')" class="btn btn-danger" ><i class="glyphicon glyphicon-trash small"></i></a> ';
						html += '	</div> ';
						html += '</div> ';

						$("#award-list").append(html);
						$("#last_award_index").val(idx + 1);
					}
					
					// Clear Fields
					$("#award_id").val("0");
					$("#award_level").val("0");
					$("#award_section").val("");
					$("#award_name").val("");
					$("#award_description").val("");
					$("#award_sequence").val("0");
					$("#award_number").val("0");
					$("#award_cash").val("0");
					$("#award_err").html("");

				}
				else {
					$("#award_err").html(data["errmsg"]);
				}
			},
			"text"
		);
	}
	else
		$("#award_err").html("Please complete all fields");
});

function edit_award(idx) {
	var award_id = document.getElementById("award_id[" + idx.toString() + "]");
	$.post(
		"op/get_award.php",
		{	award_id: award_id.value	},
		function(json) {
			var data = JSON.parse(json);
			if (data["status"] == "OK") {
				// Add section to the section list
				$("#award_id").val(data['award_id']);
				$("#award_level").val(data['level']);
				$("#award_section").val(data['section']);
				$("#award_sequence").val(data['sequence']);
				$("#award_name").val(data["name"]);
				$("#award_description").val(data["description"]);
				$("#award_number").val(data['number']);
				$("#award_cash").val(data['cash_award']);
				$("#award_err").html('');
			}
			else
				$("#award_err").html(data['errmsg']);
		},
		"text"
	);
	
}

function delete_award(idx) {
	var award_id = document.getElementById("award_id[" + idx.toString() + "]");
	$.post(
		"op/del_award.php",
		{	award_id: award_id.value	},
		function(json) {
			var data = JSON.parse(json);
			if (data["status"] == "OK") {
				// Remove section from the section list
				var row = document.getElementById("award_list["+idx.toString()+"]");
				var parent = row.parentNode;
				parent.removeChild(row);
			}
			else
				$("#award_err").html(data['errmsg']);
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
// header("Location: /jurypanel/index.php");
$_SESSION['signin_msg'] = "Use ID with require permission not found !";
printf("<script>location.href='/jurypanel/index.php'</script>");
}

?>