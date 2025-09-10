<?php 
session_start();
include("op/connect.php");
if(isset($_REQUEST['delete_id'])) {
	$del=$_REQUEST['delete_id'];
	$sql_query="DELETE FROM user WHERE user_id='$del'";
	mysqli_query($DBCON, $sql_query);
    
	echo "<meta http-equiv=\"refresh\" content=\"0;URL=registration.php\">";
}

if(isset($_REQUEST['edit_id'])) {
	$editUserId = $_REQUEST['edit_id'];
	$sql_query = mysqli_query($DBCON, "SELECT * FROM user WHERE user_id='$editUserId'") or die(mysqli_error($DBCON));
	$row = mysqli_fetch_array($sql_query);
	$editLogin = $row["login"];
	$editName = $row["name"];
	$editType = $row["type"];
	$editPassword = $row["password"];
	$editTitle = $row["title"];
	$editHonors = $row["honors"];
	$editProfile = $row["profile"];
	$editStatus = $row["status"];
}
else {
	$editUserId = "";
	$editLogin = "";
	$editName = "";
	$editType = "";
	$editPassword = "";
	$editTitle = "";
	$editHonors = "";
	$editProfile = "";
	$editStatus = "";
}

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
				<!-- JURY ADD/EDIT SCREEN -->
				<div class="col-md-6 col-lg-6">
					<div class="hpanel">
						<div class="panel-body">
							<div class="text-center m-b-md">
								<h3>Add Admin/Jury</h3>
							</div>
							<form action="op/register.php" id="loginForm" method="post" enctype="multipart/form-data">
								<div class="row">
								<?php
									if(isset($_REQUEST['edit_id'])) {
								?>
									<div class="form-group col-lg-12">
										<label>ID</label>
										<input type="text" class="form-control" name="user_id" value="<?php echo $editUserId;?>" readonly>
									</div>
								<?php
									}
								?>
									<div class="form-group col-lg-12">
										<label>User Type</label>
										<select class="form-control" name="type" required>
											<option value="ADMIN" <?php echo ($editType == "ADMIN") ? "selected" : "";?>>ADMIN</option>
											<option value="JURY" <?php echo ($editType == "JURY") ? "selected" : "";?>>JURY</option>
										</select>
									</div>
									<div class="form-group col-lg-12">
										<label>Name</label>
										<input type="text" class="form-control" name="name" value="<?php echo $editName;?>"required>
									</div>
									<div class="form-group col-lg-12">
										<label>Login Name</label>
										<input type="text"  class="form-control" name="login" value="<?php echo $editLogin;?>" required>
									</div>
									<div class="form-group col-lg-12">
										<label>Password</label>
										<input type="password"  class="form-control" name="password" <?php if(! isset($_REQUEST['edit_id'])){ ?> required> <?php } ?>
									</div>
									<div class="form-group col-lg-12">
										<label>Title</label>
										<input type="text"  class="form-control" name="title" value="<?php echo $editTitle;?>">
									</div>
									<div class="form-group col-lg-12">
										<label>Honors</label>
										<input type="text"  class="form-control" name="honors" value="<?php echo $editHonors;?>">
									</div>
									<div class="form-group col-lg-12">
										<label>Profile</label>
										<textarea rows="8" class="form-control html-editor" name="profile" ><?php echo $editProfile;?></textarea>
									</div>
									<div class="form-group col-lg-12">
										<label>Upload Profile Picture (180 pixels)</label>
										<input type="file" class="form-control" name="picture">
									</div>
									<!--
									<div class="form-group col-lg-12">
										<label>Upload Avatar (75 pixels)</label>
										<input type="file" class="form-control" name="avatar">
									</div>
									-->
								</div>
								<div class="text-center">
								<?php
									if(isset($_REQUEST['edit_id'])) {
								?>
									<button class="btn btn-success" type="submit" name="update_user">Update</button>
								<?php
									}
									else {
								?>
									<button class="btn btn-success" type="submit" name="new_user">Add User</button>
								<?php
									}
								?>
									<button class="btn btn-default" type="reset">Cancel</button>
								</div>
							</form>
						</div>
					</div>
				</div>		<!-- END of JURY ADD/EDIT Screen -->
		
				<!-- JURY LIST -->
				<div class="col-md-6 col-lg-6">
					<div class="hpanel">
						<div class="panel-body">
							<div class="text-center m-b-md">
								<h3>Jury/Admin Accounts</h3>
							</div>
							<table id="example2" class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th>Jury ID</th><th>Type</th><th>Name</th><th>Login</th><th>Password</th><th>Status</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$users = mysqli_query($DBCON, "SELECT * FROM user WHERE type='ADMIN' OR type='JURY'")or die(mysqli_error($DBCON));
								while($tr_users=mysqli_fetch_array($users)) {
							?>
								<tr>
									<td>
										<a href="registration.php?edit_id=<?php echo $tr_users['user_id'];?>" method="post">
											<?php echo $tr_users['user_id'] . " ";?><i class="ace-icon fa fa-edit"></i>
										</a>
									</td> 
									<td><?php echo $tr_users['type'];?></td>
									<td><?php echo $tr_users['name'];?></td>
									<td><?php echo $tr_users['login'];?></td>
									<td><?php echo $tr_users['password'];?></td>
									<td>
										<a href="registration.php?delete_id=<?php echo $tr_users['user_id'];?>" method="post" 
												onclick="return confirm('Are you sure to DELETE this Account');"   
												title="Delete">
											<?php echo $tr_users['status'] . " "; ?><i class="ace-icon fa fa-trash"></i>
										</a>
									</td>
								</tr>
							<?php
								}
							?>
							</tbody>
							</table>
						</div>
					</div>
				</div>		<!-- END OF JURY LIST -->
				
				<!-- JURY ASSIGH -->
				<div class="col-md-6 col-lg-6">
					<div class="hpanel">
						<div class="panel-body">
							<div class="text-center m-b-md">
								<h3>Assign Jury to Section</h3>
							</div>
							<form action="op/jury_assign.php" id="assignment_form" method="post" onsubmit="return validate_form()" enctype="multipart/form-data">
								<div class="form-group col-lg-12 col-md-12" id="assignments">
									<label>Jury Assignments</label>
									<div class="row">
										<div class="form-group col-lg-5 col-md-5 col-sm-5 col-xs-5"><b>Section</b></div>
										<div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-2"><b>#</b></div>
										<div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-4"><b>Jury</b></div>
										<div class="form-group col-lg-1 col-md-1 col-sm-1 col-xs-1">-</div>
									</div>
							<?php
								$idx = 0;
								// Load Current Assignments
								$query = mysqli_query($DBCON, "SELECT * FROM assignment, section, user WHERE assignment.section = section.section AND assignment.user_id = user.user_id ORDER BY assignment.section, jurynumber") or die(mysqli_error($DBCON));
								while ($row = mysqli_fetch_array($query)) {
							?>
									<div class="row" data-row-type="delete" id="assignment[<?php echo $idx;?>]">
										<!-- Hidden field to hold the value of the hidden SELECT --> 
										<input type="hidden" data-hidden="section" name="section[<?php echo $idx;?>]" value="<?php echo $row['section'];?>" >
										<input type="hidden" data-hidden="user_id" name="user_id[<?php echo $idx;?>]" value="<?php echo $row['user_id'];?>" >
										<div class="form-group col-lg-5 col-md-5 col-sm-5 col-xs-5">
											<select class="form-control" data-visible="section" name="section[<?php echo $idx;?>]" id="section[<?php echo $idx;?>]" disabled >
												<option value="<?php echo $row['section'];?>"><?php echo $row['section'];?></option>
											</select>
										</div>
										<div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-2">
											<input type="number" class="form-control" data-visible="jurynumber" name="jurynumber[<?php echo $idx;?>]" value="<?php echo $row['jurynumber'];?>" readonly>
										</div>
										<div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-4" >
											<select class="form-control" data-visible="user_id" name="user_id[<?php echo $idx;?>]" id="user_id[<?php echo $idx;?>]" disabled >
												<option value="<?php echo $row['user_id'];?>"><?php echo $row['name'];?></option>
											</select>
										</div>
										<div class="form-group col-lg-1 col-md-1 col-sm-1 col-xs-1">
											<a href="javascript:delete_assignment(<?php echo $idx;?>)" class="btn btn-danger" id="delete_assignment"><i class="glyphicon glyphicon-trash small"></i></a>
										</div>
									</div>
							<?php
									$idx++;
								}
							?>
									<!-- Add New Assignment Row -->
									<div class="row" data-row-type="add" id="assignment[<?php echo $idx;?>]">
										<!-- Hidden field to hold the value of the hidden SELECT --> 
										<input type="hidden" data-hidden="section" name="section[<?php echo $idx;?>]" value="<?php echo $row['section'];?>" >
										<input type="hidden" data-hidden="user_id" name="user_id[<?php echo $idx;?>]" value="<?php echo $row['user_id'];?>" >
										<div class="form-group col-lg-5 col-md-5 col-sm-5 col-xs-5">
											<select class="form-control" data-visible="section" name="section[<?php echo $idx;?>]" id="section[<?php echo $idx;?>]" >
												<option value="" selected>Pick Section...</option>
							<?php
								$query = mysqli_query($DBCON, "SELECT section FROM section ORDER BY section") or die(mysqli_error($DBCON));
								while ($row = mysqli_fetch_array($query)) {
							?>
												<option value="<?php echo $row['section'];?>"><?php echo $row['section'];?></option>
							<?php
								}
							?>
											</select>
										</div>
										<div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-2">
											<input type="number" class="form-control" data-visible="jurynumber" name="jurynumber[<?php echo $idx;?>]" id="jurynumber[<?php echo $idx;?>]" value="0" >
										</div>
										<div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-4" >
											<select class="form-control" data-visible="user_id" name="user_id[<?php echo $idx;?>]" id="user_id[<?php echo $idx;?>]" required>
												<option value="0" selected>Pick Jury...</option>
							<?php
								$query = mysqli_query($DBCON, "SELECT * FROM user WHERE type='JURY' ORDER BY name") or die(mysqli_error($DBCON));
								while ($row = mysqli_fetch_array($query)) {
							?>
												<option value="<?php echo $row['user_id'];?>"><?php echo $row['name'];?></option>
							<?php
								}
							?>
											</select>
										</div>
										<div class="form-group col-lg-1 col-md-1 col-sm-1 col-xs-1">
											<a href="javascript:add_assignment(<?php echo $idx;?>)" id="add_assignment" class="btn btn-primary"><i class="glyphicon glyphicon-plus small" ></i></a>
										</div>
									</div>
								</div>
								<div class="row"><br></div>
								<div class="row">
									<div class="col-lg-8 col-md-8 col-sm-6 col-xs-6"></div>
									<div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-6">
										<input class="form-control btn btn-primary" type="submit" name="submit" value="Submit">
									</div>
								</div>								
							</form>
						</div>
					</div>
				</div>		<!-- END OF JURY ASSIGN -->
				
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
	var sectionsHidden = getElementsByAttributeValue(assignments, "data-hidden", "section");
	var useridsHidden = getElementsByAttributeValue(assignments, "data-hidden", "user_id");
	var sectionsVisible = getElementsByAttributeValue(assignments, "data-visible", "section");
	var jurynumbersVisible = getElementsByAttributeValue(assignments, "data-visible", "jurynumber");
	var useridsVisible = getElementsByAttributeValue(assignments, "data-visible", "user_id");
	
	var valid_form = true;
	
	// Assign Hidden Inputs with values from Visible Selects
	var i;
	var idx;
	var prev;
	
	for (i = 0; i < sectionsVisible.length; ++i) {
		sectionsHidden[i].value = sectionsVisible[i].value;
		useridsHidden[i].value = useridsVisible[i].value;
	}
	
	// 1. Remove last line if empty. Otherwise flag error if any field is missing
	var last_row = sectionsVisible.length - 1;
	if (sectionsVisible[last_row].value == "" && jurynumbersVisible[last_row].value == "0" && useridsVisible[last_row].value == "0")  // Empty Row. Remove from validation
		-- last_row;
	else {
		// Flag Error if any fields are missing
		if (sectionsVisible[last_row].value == "") {
			alert("Section not selected in the last row");
			valid_form = false;
		}
		if (jurynumbersVisible[last_row].value == "") {
			alert("Jury Number not entered in the last row");
			valid_form = false;
		}
		if (useridsVisible[last_row].value == "") {
			alert("Jury not selected in the last row");
			valid_form = false;
		}
	}
	

	// 2. Validate Jury Number Sequence
	// Assign Values to an array and sort as they could be in any order
	idx = 0;
	jurynumbersVisible.forEach (
		function (jurynumberElement) { 
			if (idx <= last_row) {
				assign[idx] = Number(jurynumberElement.value); 
				++idx;
			}
		}
	);
	assign.sort();
	
	prev = 0;
	for (i = 0; i < assign.length && valid_form; ++i) {
		if (assign[i] == (prev + 1))
			prev = assign[i];
		else {
			alert("Jury Numbers are out of sequence");
			valid_form = false;
		}
	}
	
	// 3. Validate Duplicate Assignments
	assign = [];
	idx = 0;
	useridsVisible.forEach (
		function (useridElement) { 
			if (idx <= last_row) {
				assign[idx] = Number(useridElement.value); 
				++idx;
			}
		}
	);
	assign.sort();
	
	prev = 0;
	for (i = 0; i < assign.length && valid_form; ++ i) {
		if (assign[i] == prev) {
			alert("Jury assigned twice to the same Section");
			valid_form = false;
		}
		else
			prev = assign[i];
	}
	
	return valid_form;
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

function add_assignment(idx) {
	var row = document.getElementById("assignment[" + idx.toString() + "]");

	var section = document.getElementById("section[" + idx.toString() + "]");
	var user_id = document.getElementById("user_id[" + idx.toString() + "]");
	var jurynumber = document.getElementById("jurynumber[" + idx.toString() + "]");
	
	var valid_row = true;
	if (section.value > "" && user_id.value > 0 && jurynumber.value > 0) {
		// Validate if Jury Number is not assigned for this section
		var i;
		for (i = 0; i < idx; i++) {			// Check all previous assignments i < idx
			if (document.getElementById("section[" + i.toString() + "]")) {
				var compare_section = document.getElementById("section[" + i.toString() + "]");
				var compare_user_id = document.getElementById("user_id[" + i.toString() + "]");
				var compare_jurynumber = document.getElementById("jurynumber[" + i.toString() + "]");
				if (section.value == compare_section.value) {
					// Check if Jury Number has already been assigned
					if (compare_jurynumber.value == jurynumber.value) {	
						alert('Jury Number ' + compare_jurynumber.value.toString() + ' has already been assigned to another Jury');
						valid_row = false;
					}
					// Check if Jury has already been assigned
					if (compare_user_id.value == user_id.value) {
						alert("This Jury has already been assigned to this Section");
						valid_row = false;
					}
				}	
			}
		}
	}
	else {
		alert ("Please fill all the fields");
		valid_row = false;
	}

	if (valid_row) { 							// Add only if the data is valid
		
		var new_row = row.cloneNode(true);		// Clone the assignment

		// Change the Old Row and Replace the Plus icon with Trash Icon
		row.setAttribute("data-row-type", "delete");									// Change row-type to delete after cloning for use by initialize function
		var old_hidden_section = getElementByAttributeValue(row, "data-hidden", "section");			// Handle to the hidden input for field section
		var old_hidden_user_id = getElementByAttributeValue(row, "data-hidden", "user_id");			// Handle to the hidden input for field user_id

		old_hidden_section.value = section.value;											// Assign the SELECTED section to hidden field before disabling SELECT
		old_hidden_user_id.value = user_id.value;											// Assign the SELECTED user_id to hidden field before disabling SELECT
		
		section.setAttribute("disabled", "");												// Disable the select fields
		user_id.setAttribute("disabled", "");
		jurynumber.setAttribute("readonly", "");											// Make Jury Number field Readonly
		
		var old_button = row.getElementsByTagName("A")[0];
		old_button.innerHTML = '<i class="glyphicon glyphicon-trash small">';			// Change Old Button to Delete
		old_button.setAttribute("class", "btn btn-danger");								// Change Old Button Delete to Red
		old_button.setAttribute("href", "javascript:delete_assignment(" + idx.toString() + ")");	// Change Processing to Delete

		// Prepare the New Row Added
		new_idx = idx + 1;
		new_row.id = "assignment[" + new_idx.toString() + "]";							// change row id
		
		var new_hidden_section = getElementByAttributeValue(new_row, "data-hidden", "section");						// Hidden Field for changing name. It will have a value of 0
		var new_hidden_user_id = getElementByAttributeValue(new_row, "data-hidden", "user_id");
		new_hidden_section.setAttribute("name", "section[" + new_idx.toString() + "]");
		new_hidden_user_id.setAttribute("name", "user_id[" + new_idx.toString() + "]");
		
		var new_visible_section = getElementByAttributeValue(new_row, "data-visible", "section");
		new_visible_section.value = "";															// Set to Default
		new_visible_section.setAttribute("name", "section[" + new_idx.toString() + "]");		// change the id of the select element
		new_visible_section.setAttribute("id", "section[" + new_idx.toString() + "]");		// change the id of the select element
		
		var new_visible_user_id = getElementByAttributeValue(new_row, "data-visible", "user_id");
		new_visible_user_id.value = 0;															// Set to Default
		new_visible_user_id.setAttribute("name", "user_id[" + new_idx.toString() + "]");		// change the id of the select element
		new_visible_user_id.setAttribute("id", "user_id[" + new_idx.toString() + "]");			// change the id of the select element

		var new_visible_jurynumber = getElementByAttributeValue(new_row, "data-visible", "jurynumber");
		new_visible_jurynumber.value = 0;															// Set to Default
		new_visible_jurynumber.setAttribute("name", "jurynumber[" + new_idx.toString() + "]");		// change the id of the select element
		new_visible_jurynumber.setAttribute("id", "jurynumber[" + new_idx.toString() + "]");			// change the id of the select element

		var new_button = new_row.getElementsByTagName("A")[0];
		new_button.setAttribute("href", "javascript:add_assignment(" + new_idx.toString() + ")");	// Set processing for add button
		
		row.parentNode.appendChild(new_row);		// Add Child below
	}
}

function delete_assignment(idx) {
	var row = document.getElementById("assignment["+idx.toString()+"]");
	var parent = row.parentNode;
	parent.removeChild(row);
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