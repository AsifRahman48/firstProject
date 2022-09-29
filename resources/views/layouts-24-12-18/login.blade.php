
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="{{ str_replace('_', '-', app()->getLocale()) }}"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="{{ str_replace('_', '-', app()->getLocale()) }}"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="{{ str_replace('_', '-', app()->getLocale()) }}"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Ams23">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ \Illuminate\Support\Facades\Config::get('app.name')  }}  Login</title>

    <link rel="apple-touch-icon" href="{{ asset('ElaAdmin/images/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('ElaAdmin/images/favicon.png') }}">

    <!-- Styles -->
    <link href="{{ asset('ElaAdmin/assets/css/normalize.css') }}" rel="stylesheet">
    <link href="{{ asset('ElaAdmin/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('ElaAdmin/assets/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('ElaAdmin/assets/css/themify-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('ElaAdmin/assets/css/pe-icon-7-filled.css') }}" rel="stylesheet">
    <link href="{{ asset('ElaAdmin/assets/css/flag-icon.min.css') }}" rel="stylesheet">
    <link href="{{ asset('ElaAdmin/assets/css/cs-skin-elastic.css') }}" rel="stylesheet">
    <link href="{{ asset('ElaAdmin/assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/myStyle.css') }}" rel="stylesheet">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
</head>
<body class="bg-dark" style="background-image: url('WhyFutureFinanceAutomation-1.jpg'); background-repeat: no-repeat, repeat; background-size: cover;  background-position: center; height: 90vh;">
    <div class="sufee-login d-flex align-content-center flex-wrap">
        <div class="container">
            <div class="login-content">
                <div class="login-logo">
                    <a class="clientName" href="{{ url('/') }}">
                        Ams23 Automation
                    </a>
                </div>
                @yield('content')
            </div>
        </div>
    </div>
    <script src="{{ asset('ElaAdmin/assets/js/vendor/jquery-2.1.4.min.js') }}"></script>
    <script src="{{ asset('ElaAdmin/assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('ElaAdmin/assets/js/plugins.js') }}"></script>
    <script src="{{ asset('ElaAdmin/assets/js/main.js') }}"></script>
</body>
</html>