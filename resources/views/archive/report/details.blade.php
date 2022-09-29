
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
                                <li class="active">{{ $data['pageTitle'] }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  <style type="text/css">
.searchIcon{
    font-size: 22px;
}
    .searchIcon:hover{
color: green;
cursor: pointer;
    }
</style>

    <div class="content">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <strong>Details view</strong> Request

                     <!--  <a href="{{ url('/archive/pdf/'.$data['TicketInfo']->id) }}" class="btn btn-secondary btn-sm  pull-right"> <i  class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i></a> -->

  <a href="{{ url('/archive/pdf/'.$data['TicketInfo']->id) }}" class="btn  btn-sm  pull-right text-danger" style="border: 1px #000 solid"> <i  class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i></a>
                         <a href="{{ url('/archive/pdf/'.$data['TicketInfo']->id.'?type=L') }}" class="btn text-danger btn-sm  pull-right" style="margin-right: 10px; border: 1px #000 solid ; font-size: 20px; font-weight: 2000; font-style: bold">  <b>L</b> </a>

                      
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
   <div class="row form-group">
                                    <div class="col col-md-2">
                                        {!! Html::decode(Form::label('CompanyName', ' Company Name <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                                    </div>
                                    <div class="col-12 col-md-10">
                                        {!! Form::select('company_id', $data['CompanyName'], $data['TicketInfo']->company_id, ['id' => 'company_id', 'placeholder' => 'Select company name', 'class' => 'form-control custom-select', 'required' => 'required','disabled'=>'disabled']) !!}

                                        @if($errors->has('company_id'))
                                            <small class="help-block form-text">{{ $errors->first('company_id') }}</small>
                                        @endif
                                      
                                    </div>
                                </div> 
                     
                                <div class="row form-group">
                                    <div class="col col-md-2">
                                        {!! Html::decode(Form::label('cat_id', 'Department <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                                    </div>
                                    <div class="col-12 col-md-10">
                                        {!! Form::select('cat_id', $data['catList'],$data['TicketInfo']->cat_id, ['id' => 'select_cat', 'placeholder' => 'Select Department...', 'class' => 'form-control custom-select', 'required' => 'required','disabled'=>'disabled']) !!}

                                        @if($errors->has('cat_id'))
                                            <small class="help-block form-text">{{ $errors->first('cat_id') }}</small>
                                        @endif
                                        <small style="display: none;" class="help-block form-text" id="cat_error"></small>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col col-md-2">
                                        <label for="sub_cat_id" class="form-control-label">Unit/Section <span class="mandatory-field">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-10">
                                        <select id="select_sub_cat" class="form-control custom-select" required="required" name="sub_cat_id" disabled='disabled'>
                                            <!-- <option selected="selected" value="">Select category first...</option> -->
                                            @foreach($data['subcatList'] as $info)
                                            <option value="{{$info->id}}" @if($data['TicketInfo']->sub_cat_id==$info->id) selected @endif>{{$info->name}}</option>
                                            @endforeach
                                        </select>
                                        <small style="display: none;" class="help-block form-text" id="sub_cat_error"></small>
                                    </div>
                                </div>
  <div class="row form-group">
                                    <div class="col col-md-2">
                                        <label for="sub_cat_id" class="form-control-label"> Initiator <span class="mandatory-field">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-10">
                                    @php
                                    $InitiID=$data['TicketInfo']->initiator_id;
$InitiatorName=DB::table('users')->select('name')->where('id',$InitiID)->first();
                                    @endphp
                                      {{$InitiatorName->name}}
                                    </div>
                                </div>

                            <div class="row ">
                                 <div class="col-md-6 col-6" >
                                    <div class="row form-group">
                                    <div class="col col-md-4">
                                        <label for="sub_cat_id" class="form-control-label">Recommender <span class="mandatory-field">*</span></label>
                                    </div>
                                    <div class="col col-md-8">                           
                                     @foreach($data['recommenderList'] as $RecomInfo)
                                    <div class="input-group" style="margin-bottom: 10px;">
                                    <input type="text" name="recommender_id[]" value="{{$RecomInfo->name}}" class="form-control"  disabled='disabled'>
                                    </div>
                                    @endforeach                                                  
                             </div>

                                  </div>
                                </div>

                                   <div class="col-6">
                                    <div class="row form-group">
                                    <div class="col col-md-4" style="text-align: right !important">
                                        <label for="sub_cat_id" class="form-control-label">Approver <span class="mandatory-field">*</span></label>
                                    </div>
                                    <div class="col col-md-8">
                                     
                                          
@foreach($data['approverList'] as $ApproInfo)
   <div class="input-group" style="margin-bottom: 10px;">
<input type="text" name="approver_id[]" value="{{$ApproInfo->name}}" class="form-control"  disabled='disabled'>
          </div>
@endforeach


                                          
                                   

                                    </div>

                                  </div>
                                </div>
                                            
                             </div>


                                <div class="row form-group">
                                    <div class="col col-md-2">
                                        {!! Html::decode(Form::label('tSubject', 'Subject <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                                    </div>
                                    <div class="col-12 col-md-10">
                                        {!! Form::text('tSubject',$data['TicketInfo']->tSubject, ['class' => 'form-control', 'placeholder' => 'Subject', 'required' => 'required','disabled'=>'disabled']) !!}

                                        @if($errors->has('tSubject'))
                                            <small class="help-block form-text">{{ $errors->first('tSubject') }}</small>
                                        @endif
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col col-md-2">
                                        {!! Html::decode(Form::label('tDescription', 'Description', ['class' => 'form-control-label'])) !!}
                                    </div>
                                    <div class="col-12 col-md-10 tinymceTable">
                                        <div class="card">
                                       <div class="card-body" style="overflow-x: scroll;">
                      
                                        <p class="card-text">{!! Html::decode($data['TicketInfo']->tDescription)!!}</p>  
                                      </div>
                                    </div>
                                   
                                        {!! Form::hidden('tDescription', $data['TicketInfo']->tDescription, ['class' => 'form-control readonly', 'rows' => 7, 'cols' => 54, 'style' => 'resize:none']) !!}

                                        @if($errors->has('tDescription'))
                                            <small class="help-block form-text">{{ $errors->first('tDescription') }}</small>
                                        @endif
                                    </div>
                                </div>


                    <div class="row form-group">
                            <div class="col col-md-2">
                                      <!--   {!! Html::decode(Form::label('Previous Comment', 'Previous Comment', ['class' => 'form-control-label'])) !!} -->
                            </div>
                     <div class="col-12 col-md-10">  
                 <div class="card">
                                          <div class="card-body table-responsive">
                                            <h5 class="card-title"> &nbsp; Previous Comment</h5> 
                                            <table class="table table table-bordered table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>SL</th>
                                                        <th>Name</th>
                                                        <th>Comment</th>
                                                        {{-- <th>Status</th> --}}
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                       @php $sl=1; @endphp 
                                                                                             @foreach($data['PreviousComment'] as $key=>$PreviousComment)
                                                    <tr>
                                                        <td>#{{$sl++}}</td>
                                                        <td>
                                            <img class="img-responsive user-photo" src="{{ asset('comment_user.png')}}" style="max-width: 20px; max-height: 20px; border-radius: 50%;">
                                             &nbsp; &nbsp;{{$PreviousComment->User_name}}        
                                                        </td>
                                                        <td>{!! Html::decode($PreviousComment->tDescription) !!}</td>
                                                        {{-- <td>{{$PreviousComment->created_at}}</td> --}}
                                                        <td> {{ date('d-M-Y H:i:s', strtotime($PreviousComment->created_at)) }}</td>
                                                    </tr>
                                                      @endforeach
                                                </tbody>
                                                
                                            </table>                                      
                                        </div>
                                        </div>
                </div>
         </div>

      <div class="row form-group">
                                    <div class="col col-md-2">
                                       <!-- History Log : -->
                                    </div>
                                    <div class="col-12 col-md-10">


    @if(!empty($data['TicketInfo']->thistory))
                                          @php
                                            $hasan=json_decode($data['TicketInfo']->thistory,true);
                                            @endphp
                                         <div class="card">
                                          <div class="card-body table-responsive">
                                            <h5 class="card-title"> &nbsp; History Log</h5> 
                                            <table class="table table table-bordered table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>SL</th>
                                                        <th>Name</th>                                                       
                                                        <th>User Type</th>
                                                        <th>Status </th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                       @php $sl=1; @endphp 
                                 @foreach($hasan as $key => $HistoryInfo)
                                                    <tr>
                                                        <td>#{{$sl++}}</td>
                                                        <td>
                                         {{$HistoryInfo["user_name"]}}
                                                        </td>
                                                        <td>{{$HistoryInfo["user_type"]}}</td>
                                                        <td>{{$HistoryInfo["user_status"]}}</td>
                                                        <td> @if(isset($HistoryInfo["date"]) && !empty($HistoryInfo["date"]))
                                                       {{date('d-M-Y H:i:s', strtotime($HistoryInfo["date"]))}}
@endif </td>
                                                    </tr>
                                                      @endforeach
                                                </tbody>
                                                
                                            </table>                                      
                                        </div>
                                        </div>
                                            @endif

                                    </div>
                                 
                                </div>
                                        <div class="row form-group">
                                    <div class="col col-md-2">
                                        {!! Html::decode(Form::label('tFile', ' Attachment', ['class' => 'form-control-label'])) !!}
                                    </div>
                                    <div class="col-12 col-md-10">

 <ol>
@foreach($data['attachmentFile'] as $attachmentFile)
   <div class="input-group" style="margin-bottom: 10px;">
   
<a href="{{url('/')}}/{{$attachmentFile->folder}}/{{rawurlencode($attachmentFile->file_name)}}" target="_blank"><li>{{$attachmentFile->file_name}}</li></a>

          </div>
@endforeach
</ol>
                                    </div>
                                 
                                </div>
<br>

                                <div class="form-actions form-group text-center">
                                    <a href="{{ url('/'.$data['PreviousUrl']) }}"><span class="btn btn-secondary btn-xs pull-left" style=""><i class="fa fa-backward"></i>  Back</span></a>
                         


                                             




                                    
                                </div>
                      

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link href="{{ asset('lib/select2-4.0.5/css/select2.min.css') }}" rel="stylesheet">
    <script src="{{ asset('lib/select2-4.0.5/js/select2.min.js') }}"></script>

    <script src="{{ asset('lib/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('lib/tinymce/form-configuration.js') }}"></script>
    <script>
           var inputFildName=null;
function checkButton(id){
    var isChecked = $('#'+id).is(':checked');
       var forward = document.getElementById(id).value;
      if (isChecked) {
        $('#'+id).prop('checked', false);
        if(forward=='404'){
            $("#ApproveAndAcknowledgementDiv").hide();
        }
        if(forward=='6'){
            $("#InfoSelectDiv").hide();
        }
      if(forward=='7'){
        $("#forwardRequestSelectDiv").hide();
        }   
  if(forward=='504'){
        $("#ApproveAndForwardDiv").hide();
 }
    }else {
        $('#'+id).prop('checked', true);

        if(forward=='404'){
            $("#ApproveAndAcknowledgementDiv").show();
        }else{
         $("#ApproveAndAcknowledgementDiv").hide();
     }  

      if(forward=='504'){
            $("#ApproveAndForwardDiv").show();
        }else{
         $("#ApproveAndForwardDiv").hide();
     }

    if(forward=='6'){
        $("#InfoSelectDiv").show();
    }else{
        $("#InfoSelectDiv").hide();
    }

    if(forward=='7'){
         $("#forwardRequestSelectDiv").show();
    }else{
        $("#forwardRequestSelectDiv").hide();
    }


    }

}

     function slectUserListForForm(divId,inputFildNameinfo){
            ImportDivId=divId;
            inputFildName=inputFildNameinfo;
            // alert(inputFildName);
            // var inputarrayApprover=[];

                $(ImportDivId).empty();
                $('#InfoResult').empty();
                 $('#sortable').empty();
                     inputarray=[];
      inputarrayApprover=[];
      inputarray.length = 0;
      inputarrayApprover.length = 0;
         

        }


// =================UploadFile========================
     var i=1;
         $('.AddUploadFile').click(function(){
            i++;
    var habib='';
        habib+='<div class="field-wrap" id="AddUploadFileRow'+i+'" style="margin-top: 5px;  margin-bottom:30px;">';
        habib+='<div class="col-md-12"  style="padding:0px">';
        habib+='<input type="file" name="tFile[]" class="form-control-file" style="margin-bottom: 8px; margin-top: 8px;">';
      
        habib+='</div>';
        habib+='<div class="col-md-12" style="margin-top: 5px; margin-bottom:15px;">';
        habib+='<i id="'+i+'" class=" pull-right fa fa-minus-square btn_upload_remove" aria-hidden="true" style="padding: 3px; border-radius: 4px; color:red;"></i>';
        habib+='<small class="form-control-feedback pull-left">&nbsp; Only pdf, doc, xls, jpg, jpeg & png formats are allowed. </small>';
   
        habib+='</div>';
        habib+='</div>';
    $('#uploadFile').append(habib);
            });

           $(document).on('click','.btn_upload_remove', function(){
                var button_id = $(this).attr("id");
                $("#AddUploadFileRow"+button_id+"").remove();
            });  


        $(document).ready(function() {
            // $('.select2').select2();
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
        var nameInfo=account.name+'-'+account.email+'-'+account.title+'-'+account.department+'-'+account.telephonenumber;

        results.push({
            id: account.id,
            text: nameInfo
        });
    });

    return {
        results: results
    };
   }
  }
 });
                $("#InfoSelectDiv").hide();
                    $("#forwardSelectDiv").hide();
                        $("#forwardRequestSelectDiv").hide();
                        $("#ApproveAndAcknowledgementDiv").hide();
                        $("#ApproveAndForwardDiv").hide();
     
        });

        /**
         * Setup ajax Header
         */
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function update(type,action,id){
    // alert(id);

       $.ajax({
                    type: 'POST',
                    url: '{{ URL::to('/request/update_status')}}',
                    data: {type :type,action:action,id:id},
                    datatype: 'json',
                    statusCode:{
                        400:function(data){
                            $("#cat_error").css({"display":"block", "color":"red"});
                            $("#cat_error").html('Please select a Department.');
                        }
                    },
                    success: function (data) {
                        alert('habib');
                    }
                });

}

        /**
         * Change Category
         */
        $("#select_cat").change(function (e) {
            e.preventDefault();
            var category = $("select[name=cat_id]").val();
            if(category === ''){
                $("#cat_error").css({"display": "block", "color": "red"});
                $("#cat_error").html('Please select a Department.');
                $('#select_sub_cat').empty();
                $('#select_sub_cat').append('<option value="">Select Department first...</option>');

            }else{
                $("#cat_error").css({"display": "block", "color": "green"});
                $("#cat_error").html('You choose a Department, Now please select a Unit/Section below.');

                $.ajax({
                    type: 'POST',
                    url: '{{ URL::to('/get_sub_cat')}}',
                    data: {cat_id : category},
                    datatype: 'json',
                    statusCode:{
                        400:function(data){
                            $("#cat_error").css({"display":"block", "color":"red"});
                            $("#cat_error").html('Please select a Department.');
                        }
                    },
                    success: function (data) {
                        $('#select_sub_cat').empty();
                        $('#select_sub_cat').append('<option value="">Select Unit/Section...</option>');
                        $.each(data.data, function(key, value){
                            $('#select_sub_cat').append('<option value="'+value.id+'">'+value.name+'</option>');
                        });
                        $("#cat_error").css({"display":"block", "color":"green"});
                        $("#cat_error").html('You choose a Department, Now please select a Unit/Section below.');
                    }
                });
            }
        });
    </script>
        @include('modal.request_update') 
@endsection