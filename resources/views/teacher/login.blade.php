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
	<section id="wrapper" class="error-page" style="height: 100%;">
        <div class="error-box" style="margin-top: 10%;">
            <div class="error-body text-center">
                <h1 style="font-size: 3.5em; line-height: 20px;">UIT</h1>
                <h3 class="text-uppercase">Teacher</h3>
                <p class="text-muted m-t-30 m-b-30">Log-in with your UIT email <br>to access the system</p>
                <a href="login/google" class="btn btn-info btn-rounded waves-effect waves-light m-b-40">Sign in with google</a>
            </div>
        </div>
    </section>
    <script src="{{ asset('/material-lite/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="{{ asset('/material-lite/plugins/bootstrap/js/tether.min.js') }}"></script>
    <script src="{{ asset('/material-lite/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
</body>

</html>
