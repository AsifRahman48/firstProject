
@extends('layouts.elaadmin')

@section('content')
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>{{ $data['pageTitle'] }}</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="{{ url('/') }}">Dashboard</a></li>
                                <li><a href="{{ url("users") }}">Manage</a></li>
                                <li class="active">{{ $data['pageTitle'] }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <strong>Search</strong> Request
                        </div>
                        <div class="card-body card-block">

                            @if (session('error'))
                                <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show">
                                    <span class="badge badge-pill badge-danger">Danger</span>
                                    {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            @endif
<!-- <form class="form-horizontal"> -->
      <div class="row form-group  ">
                        <div class="col-md-8 offset-md-2 d-flex">              
                                    <div class="col-md-8 d-flex" style="padding-right: 0px;">

      <select id="user_id" name="user_id" class="form-control select2" style="width: 90%; font-size: " required="1">px;
                                             <option value="">Search User</option>
                                       
                                         </select>&nbsp;
 <button class="btn btn-success btn-sm" onclick="searchRequestInfo()">View</button>
                                   
                                    </div>
<!-- 
                                     <div class="col col-md-1 " style="padding-left: 0px;">
                                       
                                    </div> -->
                                </div>
                                </div>
    
<!-- </form> -->
               <div class="alert alert-success" id="success-alert">
    <button type="button" class="close" data-dismiss="alert">x</button>
    <strong>Success! </strong> Request reassign 
</div>           

                        </div>
                    </div>

     <div class="card" >
                        <div class="card-header">
                            <strong>Result</strong> 
                        </div>
                        <div class="card-body card-block" id="result">

  <h2 class="text-center " style="color: #CCC">Result Not Found</h2>   

                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

<!-- <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script> -->
<link href="{{ asset('select2/select2.min.css')}}" rel="stylesheet" />
<script src="{{ asset('select2/select2.min.js')}}"></script>
  

    <script type="text/javascript">
               $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
                 $(document).ready(function(){
                     $("#success-alert").hide();
 
 $(".select2").select2({
    minimumInputLength: 2,
  ajax: { 
   url: "{{ URL::to('/search_ad_user')}}",
   type: "POST",
   dataType: 'json',
   delay: 10,
   data: function (params) {
    return {
      term: params.term // search term
    };
   },
   processResults: function (data) {  
  var results = [];
    $.each(data, function (index, account) {
          var nameInfo=account.name+'->'+account.title+'->'+account.department+'->'+account.email+'->'+account.company_name;
if(account.id!=@php echo Auth::id() @endphp){
        results.push({
            id: account.id,
            text: nameInfo
        });
      }
    });

    return {
        results: results
    };
   }
  }
 });

});

function searchRequestInfo(){
    var userId=$('#user_id').val();
    // alert(userId);
var csrf = $('meta[name="csrf-token"]').attr('content');
      $.ajax({ 
   url: "{{ URL::to('/search_assignment')}}",
   type: 'POST',
   // dataType: 'json',
   // delay: 10,
   data:{'userId':userId,'_token': csrf},
   success: function (data) {  
 // console.log(data);
 $('#result').empty();
$('#result').append(data);
    
   }
  });

   }


   function viewUserList(divName,divId,requestId){
    // alert();
    console.log(divName,divId,requestId);
    $('#'+divName+divId).removeClass('d-none');
    $('#'+divName+'B'+divId).addClass('d-none');
    $(".action").css('width', '35%');

    // var divInfo='';
    // divInfo+='<form>'
    // divInfo+='<inpu type="text" name="request_id" value="'+requestId+'">'

    //  divInfo+='<select id="user_id" name="user_id" class="form-control select2" style="width: 90%; font-size: 10px">';
    //  divInfo+='<option value="">Search User</option>';
    //  divInfo+='</select>';
    // divInfo+='</form>'
    // $('#'+divName+''+divId).append(divInfo);

   }




    </script>


@endsection