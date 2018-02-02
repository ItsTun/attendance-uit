<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/material-lite/images/favicon.png') }}">
    <title>@yield('title')</title>
    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('/material-lite/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- chartist CSS -->
    <link href="{{ asset('/material-lite/plugins/chartist-js/dist/chartist.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/material-lite/plugins/chartist-js/dist/chartist-init.css') }}" rel="stylesheet">
    <link href="{{ asset('/material-lite/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css') }}" rel="stylesheet">
    <!--This page css - Morris CSS -->
    <link href="{{ asset('/material-lite/plugins/c3-master/c3.min.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('/material-lite/css/style.css') }}" rel="stylesheet">
    <!-- You can change the theme colors from here -->
    <link href="{{ asset('/material-lite/css/colors/blue.css') }}" id="theme" rel="stylesheet">

    @yield('styles')
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body class="fix-header fix-sidebar card-no-border">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar">
            <nav class="navbar top-navbar navbar-toggleable-sm navbar-light">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                <div class="navbar-header">
                    
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav mr-auto mt-md-0">
                        <!-- This is  -->
                        <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="mdi mdi-menu"></i></a> </li>
                    </ul>
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav my-lg-0">
                        <!-- ============================================================== -->
                        <!-- Profile -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Add user's name here</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        {{--<li> <a class="waves-effect waves-dark" href="{{ route('admin-dashboard') }}" aria-expanded="false"><i class="mdi mdi-gauge"></i><span class="hide-menu">Dashboard</span></a>--}}
                        {{--</li>--}}
                        <li> <a class="waves-effect waves-dark" href="{{ route('admin-students') }}" aria-expanded="false"><i class="mdi mdi-account"></i><span class="hide-menu">Students</span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="{{ route('admin-teachers') }}" aria-expanded="false"><i class="mdi mdi-account-multiple"></i><span class="hide-menu">Teachers</span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="{{ route('admin-years') }}" aria-expanded="false"><i class="mdi mdi-calendar"></i><span class="hide-menu">Years</span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="{{ route('admin-departments') }}" aria-expanded="false"><i class="mdi mdi-home-modern"></i><span class="hide-menu">Departments</span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="{{ route('admin-classes') }}" aria-expanded="false"><i class="mdi mdi-home"></i><span class="hide-menu">Classes</span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="{{ route('admin-subjects') }}" aria-expanded="false"><i class="mdi mdi-library-books"></i><span class="hide-menu">Subjects</span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="{{ route('admin-timetables') }}" aria-expanded="false"><i class="mdi mdi-table"></i><span class="hide-menu">Timetables</span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="{{ route('admin-attendance') }}" aria-expanded="false"><i class="mdi mdi-account-check"></i><span class="hide-menu">Attendance</span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="{{ route('admin-admins') }}" aria-expanded="false"><i class="mdi mdi-account-star"></i><span class="hide-menu">Admins</span></a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="{{ route('admin-medical-leave') }}" aria-expanded="false"><i class="mdi mdi-account-star"></i><span class="hide-menu">Medical Leave</span></a>
                        </li>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
            <!-- Bottom points-->
            <div class="sidebar-footer">
                
            </div>
            <!-- End Bottom points-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            @yield('content')
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="{{ asset('/material-lite/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="{{ asset('/material-lite/plugins/bootstrap/js/tether.min.js') }}"></script>
    <script src="{{ asset('/material-lite/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="{{ asset('/material-lite/js/jquery.slimscroll.js') }}"></script>
    <!--Wave Effects -->
    <script src="{{ asset('/material-lite/js/waves.js') }}"></script>
    <!--Menu sidebar -->
    <script src="{{ asset('/material-lite/js/sidebarmenu.js') }}"></script>
    <!--stickey kit -->
    <script src="{{ asset('/material-lite/plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
    <!--Custom JavaScript -->
    <script src="{{ asset('/material-lite/js/custom.min.js') }}"></script>
    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->
    <!-- chartist chart -->
    <script src="{{ asset('/material-lite/plugins/chartist-js/dist/chartist.min.js') }}"></script>
    <script src="{{ asset('/material-lite/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.min.js') }}"></script>
    <!--c3 JavaScript -->
    <script src="{{ asset('/material-lite/plugins/d3/d3.min.js') }}"></script>
    <script src="{{ asset('/material-lite/plugins/c3-master/c3.min.js') }}"></script>
    @yield('scripts')
</body>

</html>
