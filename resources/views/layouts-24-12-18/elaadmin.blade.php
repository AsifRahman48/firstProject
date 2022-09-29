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

    <title>{{ $data['pageTitle'] }}</title>

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

    <!-- Scripts -->
    <script src="{{ asset('ElaAdmin/assets/js/vendor/jquery-2.1.4.min.js') }}"></script>

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
    <style type="text/css">
    .field-wrap{
        margin-top: 5px;
        margin-bottom: 5px;
    }
    .mandatory-field{
        color: red !important;
    }

  #loader
    {
        position: fixed;
        z-index: 9999;
        height: 100%;
        width: 100%;
        top: 0;
        left: 0;
        background-color: Black;
        filter: alpha(opacity=60);
        opacity: 0.4;
        -moz-opacity: 0.4;
    }
    .centerDiv
    {
        z-index: 1000;
        margin: 300px auto;
        padding: 10px;
        width: 130px;
        /*background-color: White;*/
        border-radius: 10px;
        filter: alpha(opacity=100);
        opacity: 1;
        -moz-opacity: 1;
    }
    .centerDiv img
    {
        height: 128px;
        width: 128px;
    }

</style>
<script type="text/javascript">
    $(document).ready(function(){
    $('#loader').hide();

    });
</script>

</head>
<body>
@include('layouts.nav')
<div id="right-panel" class="right-panel">

    @include('layouts.header')
   

        <div id="loader">
                <div class="centerDiv">
          <img src="{{ asset('loading.gif') }}"/>
         </div>
         </div>

    @yield('content')
    @include('layouts.footer')

    <script type="text/javascript">
        function checkInArray(arrayList,checkValus){

        }
    </script>

        <script type="text/javascript">
        $(document).ready(function(){
// $('.datepicker').datepicker();
// $('.datepicker').datepicker({ dateFormat: 'dd-mm-yy' }).val();
        var $datepicker = $('.datepicker');
$datepicker.datepicker({ dateFormat: 'dd-mm-yy' });
$datepicker.datepicker('setDate', new Date());

        });
    </script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
     <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</div>
<script src="{{ asset('ElaAdmin/assets/js/vendor/jquery-2.1.4.min.js') }}"></script>
<script src="{{ asset('ElaAdmin/assets/js/popper.min.js') }}"></script>
<script src="{{ asset('ElaAdmin/assets/js/plugins.js') }}"></script>
<script src="{{ asset('ElaAdmin/assets/js/main.js') }}"></script>
</body>
</html>