
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
                        <small class="text-muted">Master Panel of YPS 2016 <b class="caret"></b></small>
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
			<li class="<?php echo (basename($_SERVER['PHP_SELF']) == "registration.php") ? 'active': '';?>">
                <a href="registration.php"> <span class="nav-label"><i class="glyphicon glyphicon-user"></i> Jury Account </span></a>
            </li>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == "dashboard.php") ? 'active': '';?>">
                <a href="dashboard.php"> <span class="nav-label"><i class="glyphicon glyphicon-home"></i> Dashboard </span></a>
            </li>
			<li class="<?php echo (basename($_SERVER['PHP_SELF']) == "all_participate.php") ? 'active': '';?>">
                <a href="all_participate.php"> <span class="nav-label"><i class="glyphicon glyphicon-th-list"></i> Participants </span></a>
            </li>
			<li class="<?php echo (basename($_SERVER['PHP_SELF']) == "results.php") ? 'active': '';?>">
				<a href="#"><span class="nav-label"><i class="fa fa-mortar-board"></i> Results </span></a>
				<ul class="nav nav-second-level">
					<li>
						<a href="results.php?archive=true"><span class="nav-label"><i class="fa fa-mortar-board"></i> Archive Results </span></a>
					</li>
					<li>
						<a href="results.php"><span class="nav-label"><i class="fa fa-mortar-board"></i> View Results </span></a>
					</li>
				</ul>
            </li>
			<li class="contest">
				<a href="contest.php"><span class="nav-label"><i class="glyphicon glyphicon-edit"></i> Initialize Contest </span></a>
			</li>
			<li >
				<a href="op/contactcsv.php" target="_blank"><span class="nav-label"><i class="glyphicon glyphicon-book"></i> Download Contacts </span></a>
			</li>
        </ul>
    </div>
</aside>
