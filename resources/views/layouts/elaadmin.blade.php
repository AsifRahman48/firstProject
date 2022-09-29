<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>{{ $data['pageTitle']." | ".\Illuminate\Support\Facades\Config::get('app.name')}}</title>
    <meta name="description" content="Ams23 Automation">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(\Illuminate\Support\Facades\Storage::disk('public')->has('setting/favicon.png'))
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('storage/setting/favicon.png') }}">
        <link rel="shortcut icon" sizes="114x114" href="{{ asset('storage/setting/favicon.png') }}">
    @else
        <link rel="apple-touch-icon" href="{{ asset('ElaAdmin/images/favicon.png') }}">
        <link rel="shortcut icon" href="{{ asset('ElaAdmin/images/favicon.png') }}">
    @endif

    <link href="{{ asset('ElaAdmin/assets/css/normalize.min.css')}}" rel="stylesheet">
     <link href="{{ asset('ElaAdmin/assets/css/bootstrap.min.css') }}" rel="stylesheet">
     <link href="{{ asset('ElaAdmin/assets/css/font-awesome.min.css') }}" rel="stylesheet">
     <link href="{{ asset('ElaAdmin/assets/css/themify-icons.css') }}" rel="stylesheet">
     <link href="{{ asset('ElaAdmin/assets/css/pe-icon-7-stroke.min.css') }}" rel="stylesheet">
     <link href="{{ asset('ElaAdmin/assets/css/flag-icon.min.css') }}" rel="stylesheet">
     <link href="{{ asset('ElaAdmin/assets/css/chartist.min.css') }}" rel="stylesheet">
     <link href="{{ asset('ElaAdmin/assets/css/fullcalendar.min.css') }}" rel="stylesheet">
     <link href="{{ asset('ElaAdmin/assets/css/jqvmap.min.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('ElaAdmin/assets/css/cs-skin-elastic.css')}}">
    <link rel="stylesheet" href="{{ asset('ElaAdmin/assets/css/style.css')}}">
    <link rel="stylesheet" href="{{ asset('css/v2/custom.css')}}">
 <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>

  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <!-- <link rel="stylesheet" href="/resources/demos/style.css"> -->
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
   <link rel="stylesheet" href="{{ asset('ElaAdmin/assets4/css/lib/datatable/dataTables.bootstrap.min.css') }}">

   <style>
    #weatherWidget .currentDesc {
        color: #ffffff!important;
    }
        .traffic-chart {
            min-height: 335px;
        }
        #flotPie1  {
            height: 150px;
        }
        #flotPie1 td {
            padding:3px;
        }
        #flotPie1 table {
            top: 20px!important;
            right: -10px!important;
        }
        .chart-container {
            display: table;
            min-width: 270px ;
            text-align: left;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        #flotLine5  {
             height: 105px;
        }

        #flotBarChart {
            height: 150px;
        }
        #cellPaiChart{
            height: 160px;
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

    .mandatory-field{
      color: red;
    }
    </style>
    @stack('page-css')
    <script type="text/javascript">
        $(function(){
 $('#loader').addClass('d-none');
        });
    </script>

    @yield('stylesheets')
</head>

<body>
          <div id="loader">
                <div class="centerDiv">
          <img src="{{ asset('loading.gif') }}"/>
         </div>
         </div>
@include('layouts.nav')
    <!-- /#left-panel -->
    <!-- Right Panel -->
    <div id="right-panel" class="right-panel">
        <!-- Header-->
        @include('layouts.header')
        <!-- /#header -->
        <!-- Content -->
           @yield('content')
      <!--   <div class="content" style="min-height: 700px;">

        </div> -->
        <!-- /.content -->
        <div class="clearfix"></div>
   @include('layouts.footer')


    </div>

@section('script')
 <script type="text/javascript">
        function checkInArray(arrayList,checkValus){

        }

       jQuery(document).ready(function(){
              $('#loader').hide();


        var $datepicker = $('.datepicker');
$datepicker.datepicker({ dateFormat: 'dd-mm-yy' });
$datepicker.datepicker('setDate', new Date());
$('.bootstrap-data-table').DataTable();

        });
    </script>
   @endsection
    <script src="{{ asset('ElaAdmin/assets/js/jquery.min.js')}}"></script>
    <script src="{{ asset('ElaAdmin/assets/js/popper.min.js')}}"></script>
    <script src="{{ asset('ElaAdmin/assets/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('ElaAdmin/assets/js/jquery.matchHeight.min.js')}}"></script>
    <script src="{{ asset('ElaAdmin/assets/js/Chart.bundle.min.js')}}"></script>
    <script src="{{ asset('ElaAdmin/assets/js/chartist.min.js')}}"></script>
    <script src="{{ asset('ElaAdmin/assets/js/chartist-plugin-legend.min.js')}}"></script>
    <script src="{{ asset('ElaAdmin/assets/js/jquery.flot.min.js')}}"></script>
    <script src="{{ asset('ElaAdmin/assets/js/jquery.flot.pie.min.js')}}"></script>
    <script src="{{ asset('ElaAdmin/assets/js/jquery.flot.spline.min.js')}}"></script>
    <script src="{{ asset('ElaAdmin/assets/js/moment.min.js')}}"></script>
    <script src="{{ asset('ElaAdmin/assets/js/fullcalendar.min.js')}}"></script>
    <script src="{{ asset('ElaAdmin/assets/js/init/fullcalendar-init.js')}}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>
<!-- <script src="{{ asset('ElaAdmin/assets/js/vendor/jquery-2.1.4.min.js') }}"></script> -->
 <!-- <script src="https://code.jquery.com/jquery-1.10.2.js"></script> -->
    <script src="{{ asset('ElaAdmin/assets/js/main.js')}}"></script>

      <!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->
     <!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->

<!--<script src="{{ asset('ElaAdmin/assets4/js/lib/data-table/datatables.min.js') }}"></script> -->
    <!-- <script src="{{ asset('ElaAdmin/assets4/js/lib/data-table/dataTables.bootstrap.min.js') }}"></script> -->


	@yield('add_script')


</body>
</html>
