<!-- Header -->
<div id="header">
    <div class="color-line">
    </div>
    <div id="logo" class="light-version">
        <span>
               Youth Photographic Society
        </span>
    </div>
    <nav role="navigation">
        <div class="header-link hide-menu"><i class="fa fa-bars"></i></div>
        <div class="small-logo"> <!-- Shows up only in small screens -->
            <span class="text-primary">Admin/Jury Module</span>
        </div>
        <form role="search" class="navbar-form-custom" method="post" action="#">
			<!-- Search control -->
            <div class="form-group">
                <input type="text" placeholder="Search something special" class="form-control" name="search">
            </div>
        </form>
        <div class="mobile-menu"> <!-- Shows up only in Mobile -->
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
        <div class="navbar-right">
            <ul class="nav navbar-nav no-borders">
                <li class="dropdown">
                    <a class="dropdown-toggle" href="#" data-toggle="dropdown">
                        <i class="pe-7s-keypad"></i>
                    </a>

                    <div class="dropdown-menu hdropdown bigmenu animated flipInX">
                        <table>
                            <tbody>
                            <tr>
                                <td>
                                    <a data-toggle="modal" href="record.php">
                                        <i class="pe pe-7s-note2 text-info"></i>
                                        <h5>Result</h5>
                                    </a>
                                </td>
                                <td>
                                    <a href="rating.php">
                                        <i class="pe pe-7s-camera text-warning"></i>
                                        <h5>Image Rating</h5>
                                    </a>
                                </td>
                                <td>
                                    <a href="#" id="sidebar" class="right-sidebar-toggle">
                                        <i class="pe pe-7s-users text-success"></i>
                                        <h5>Profile</h5>
                                    </a>
                                </td>
                            </tr>
                           </tbody>
                        </table>
                    </div>
                </li>
                <li>
                    <a href="#" id="sidebar" class="right-sidebar-toggle">
                        <i class="pe-7s-upload pe-7s-note"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="index.php">
                        <i class="pe-7s-upload pe-rotate-90"></i>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>
