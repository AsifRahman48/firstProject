
@extends('layouts.elaadmin')

@section('content')

<style type="text/css">
.searchIcon{
    font-size: 22px;
}
    .searchIcon:hover{
color: green;
cursor: pointer;
    }
</style>
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

    <div class="content">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <strong>Details view</strong> Request
                      
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

                            {!! Form::open(['url'=>'request/request_status_update', 'class'=>'form-horizontal', 'role'=>'form', 'name'=>'requestAddPost', 'enctype'=>'multipart/form-data']) !!}
                            <input type="hidden" name="id" value="{{$data['TicketInfo']->id}}">
                              <input type="hidden" name="previousUrl" value="{{$data['previousUrl']}}">
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
                                        {!! Html::decode(Form::label('Commentbox', 'Comment Box', ['class' => 'form-control-label'])) !!}
                                    </div>
                                    <div class="col-12 col-md-10 tinymceTable">                                        
                                        {!! Form::textarea('Commentbox', old('Commentbox'), ['class' => 'form-control', 'rows' => 3, 'cols' => 4]) !!}
                                        @if($errors->has('tDescription'))
                                            <small class="help-block form-text">{{ $errors->first('tDescription') }}</small>
                                        @endif
                                    </div>
                                </div>                              
                                <br>
                                <div class="row form-group">
                                    <div class="col col-md-2">
                                        <!-- {!! Html::decode(Form::label('Previous Comment', 'Previous Comment', ['class' => 'form-control-label'])) !!} -->
                                    </div>

                                            <div class="col-12 col-md-10"> 

 @if(count($data['PreviousComment'])>0)
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
                                                        <td> {{ date('d-M-Y h:i:s a', strtotime($PreviousComment->created_at)) }}</td>
                                                    </tr>
                                                      @endforeach
                                                </tbody>
                                                
                                            </table>                                      
                                        </div>
                                        </div>
                                        @endif
                                            </div>
                                         </div>
                                <br>
                                 @if(count($data['attachmentFile'])>0)
                                   <div class="row form-group">
                                    <div class="col col-md-2">
                                        {!! Html::decode(Form::label('tFile', 'Attachment', ['class' => 'form-control-label'])) !!}
                                    </div>
                                    <div class="col-12 col-md-10">


                                    @foreach($data['attachmentFile'] as $attachmentFile)
                                       <div class="input-group" style="margin-bottom: 10px;">
                                    <a href="{{url('/')}}/{{$attachmentFile->folder}}/{{rawurlencode($attachmentFile->file_name)}}" target="_blank">{{$attachmentFile->file_name}}</a>
                                              </div>
                                    @endforeach


                                    </div>                           
                                </div>
                                @endif
                                <br>
                                     <div class="row form-group">
                                    <div class="col col-md-2">
                                        {!! Html::decode(Form::label('tFile', 'Attachment', ['class' => 'form-control-label'])) !!}
                                    </div>
                                    <div class="col-12 col-md-10">
                                        {!! Form::file('tFile[]', ['class' => 'form-control-file', 'style' => 'padding:0; margin-bottom:8px;']) !!}
                                        <small class="form-control-feedback">&nbsp; Only pdf, doc, xls, jpg, jpeg & png formats are allowed. </small> 
                                    <div id="uploadFile"> </div>
                                 <i  class="pull-left fa fa-plus-square AddUploadFile" aria-hidden="true" style="padding: 3px; border-radius: 4px;"></i>
                                         @if($errors->has('tFile'))
                                            <small class="help-block form-text">{{ $errors->first('tFile') }}</small>
                                        @endif
                                    </div>
                                </div>


 @if($data['TicketInfo']->now_ticket_at == Auth::id() )
	<div class="row form-group text-center rerquest_in_details" style="padding: 0px;">
		<div class="col col-md-2">

		</div>
		<div class="col-md-10 " style="padding: 0px;">
		@if($data['TicketInfo']->tStatus !=='6')

		<button type="button" class="btn btn-info  pull-left" onclick="checkButton('habib')"    style="margin-left: 25px; margin-top: 5px;">
		<input type="radio" name="formAction" value="4" id="habib"> Approve
		</button> 

		<button type="button" class="btn btn-info  pull-left" onclick="checkButton('Reject')"    style="margin-left: 25px; margin-top: 5px;" >
		<input type="radio" name="formAction" value="5" id="Reject"> Reject
		</button>

		<button type="button" class="btn btn-info  pull-left" onclick="checkButton('RequestforInfo')"   style="margin-left: 25px; margin-top: 5px;">
		<input type="radio" name="formAction" value="6" id="RequestforInfo"> Request for Info
		</button>
		<div class="col-md-3 pull-left" id="InfoSelectDiv"  style="margin-top: 5px;">
		<div class="d-flex" style="padding: 0px;" >   
		<div id="InfoSelectDivAdvance">  
		{!! Form::select('requestInfoBy',$data['userList'],$data['TicketInfo']->initiator_id, ['placeholder' => 'Select Info Person ', 'class' => 'form-control  select2']) !!}
		</div>  
		<i class="fa fa-search searchIcon ml-3" aria-hidden="true"  onclick="slectUserListForForm('#InfoSelectDivAdvance','requestInfoBy')"  data-toggle="modal" data-target="#exampleModal"></i> 

		</div>  
		</div>  


		<button type="button" class="btn btn-info  pull-left" onclick="checkButton('Forward')"   style="margin-left: 25px; margin-top: 5px;">
		<input type="radio" name="formAction" value="7" id="Forward"> Forward
		</button>
		<div class="col-md-3 pull-left" id="forwardRequestSelectDiv"  style="margin-top: 5px;"> 

		<div class="d-flex" style="padding: 0px;" >   
		<div id="forwardRequestSelectDivAdvance">  
		{!! Form::select('forwardUser', $data['userList'],null, ['placeholder' => 'Select Forward Person ', 'class' => 'form-control select2','style'=>'width:100%']) !!} 
		</div>  
		<i class="fa fa-search searchIcon ml-3" aria-hidden="true"  onclick="slectUserListForForm('#forwardRequestSelectDivAdvance','forwardUser')"  data-toggle="modal" data-target="#exampleModal"></i> 
		</div>   
		</div>   


		<button type="button" class="btn btn-info  pull-left" onclick="checkButton('ApproveAndForward')"    style="margin-left: 25px; margin-top: 5px;">
		<input type="radio" name="formAction" value="504" id="ApproveAndForward"> Approve and Forward
		</button>


		<div class="col-md-3 pull-left" id="ApproveAndForwardDiv"  style="margin-top: 5px;">  
		<div class="d-flex" style="padding: 0px;" >   
		<div id="forwardSelectDivAdvance">        
		{!! Form::select('forwardBy', $data['userList'],null, ['placeholder' => 'Select Forward Person ', 'class' => 'form-control select2']) !!}  
		</div>             
		<i class="fa fa-search searchIcon ml-3" aria-hidden="true"  onclick="slectUserListForForm('#forwardSelectDivAdvance','forwardBy')"  data-toggle="modal" data-target="#exampleModal"></i> 
		</div>   
		</div> 

		<button type="button" class="btn btn-info  pull-left" onclick="checkButton('ApproveAndAcknowledgement')"   style="margin-left: 25px; margin-top: 5px;">
		<input type="radio" name="formAction" value="404" id="ApproveAndAcknowledgement"> Approve and Acknowledgement
		</button>


		<div class="col-md-3 pull-left" id="ApproveAndAcknowledgementDiv" style="margin-top: 5px;">  
		<div class="d-flex" style="padding: 0px;" >   
		<div id="AcknowledgementSelectDivAdvance">        
		{!! Form::select('AcknowledgementBy', $data['userList'],null, ['placeholder' => 'Select Forward Person ', 'class' => 'form-control select2']) !!}  
		</div>             
		<i class="fa fa-search searchIcon ml-3" aria-hidden="true"  onclick="slectUserListForForm('#forwardSelectDivAdvance','AcknowledgementBy')"  data-toggle="modal" data-target="#exampleModal"></i> 
		</div>   
		</div>    
		@else

		<input type="hidden" name="formAction" value="11">
		@endif                                  
		</div>
	</div>
@endif

	<br>
	<br>

                                <div class="form-actions form-group text-center">
                                    <a href="{{ $data['previousUrl'] }}"><span class="btn btn-secondary btn-xs pull-left" style=""><i class="fa fa-backward"></i>  Back</span></a>


                                             



  @if($data['TicketInfo']->now_ticket_at==Auth::id())
<button id="save" type="submit" class="btn btn-info  pull-right" >
Submit <i class="fa fa-forward"></i>
   </button>
   
   @endif

                                    
                                </div>
                            {!! Form::close() !!}

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
               $("#InfoSelectDiv").hide();
                    $("#forwardSelectDiv").hide();
                        $("#forwardRequestSelectDiv").hide();
                        $("#ApproveAndAcknowledgementDiv").hide();
                        $("#ApproveAndForwardDiv").hide();
     
        });



        // $(document).ready(function() {
        //     $('.select2-select').select2();

        //      $("#InfoSelectDiv").hide();
        //        $("#forwardSelectDiv").hide();
        //        $("#forwardRequestSelectDiv").hide();
     
        // });

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



// Check on final form submit forwarded or other user selected (not empty user submit)
$('#save').on('click', function(e){

	e.preventDefault();

	
	// Request for info selection
	var RequestforInfo = $('#RequestforInfo:checked').val();
	if( RequestforInfo  == 6 ){
		
		var requestInforSelectedValue =  $('#InfoSelectDivAdvance select').val();

		if( requestInforSelectedValue == '' ){
			alert('Please select a user to request information from!');
			return false;
		}

	}

	// Request for Forward selection
	var Forward = $('#Forward:checked').val();
	if( Forward  == 7 ){
		
		var forwardRequestSelectDivAdvance =  $('#forwardRequestSelectDivAdvance select').val();
		if( forwardRequestSelectDivAdvance == '' ){
			
			alert('Please select a user to forward!');
			return false;
		}

		/** User should not forward ticket to initiator **/
		ForwarduserId = parseInt(forwardRequestSelectDivAdvance, 10);

		var InitiatorId = "<?= $data['TicketInfo']->initiator_id; ?>";
		InitiatorId = parseInt(InitiatorId, 10);

		if( ForwarduserId == InitiatorId ){
			alert('Sorry! You can not forward ticket to Initiatior. Please Select other user.');
			return false;
		}

	}

	// Request for Approve and Forward selection
	var ApproveAndForward = $('#ApproveAndForward:checked').val();
	if( ApproveAndForward  == 504 ){
		
		var forwardSelectDivAdvance =  $('#forwardSelectDivAdvance select').val();
		if( forwardSelectDivAdvance == '' ){
			
			alert('Please select a user to forward for approval!');
			return false;
		}


		/** User should not forward ticket to initiator **/
		ForwarduserId = parseInt(forwardSelectDivAdvance, 10);

		var InitiatorId = "<?= $data['TicketInfo']->initiator_id; ?>";
		InitiatorId = parseInt(InitiatorId, 10);

		if( ForwarduserId == InitiatorId ){
			alert('Sorry! You can not forward ticket to Initiatior. Please Select other user.');
			return false;
		}

	}

	// Request for Approve and acknowledgement selection
	var ApproveAndAcknowledgement = $('#ApproveAndAcknowledgement:checked').val();
	if( ApproveAndAcknowledgement  == 404 ){
		
		var AcknowledgementSelectDivAdvance =  $('#AcknowledgementSelectDivAdvance select').val();
		if( AcknowledgementSelectDivAdvance == '' ){
			
			alert('Please select a user for acknowledgement!');
			return false;
		}

	}


	$(this).parents('form[name="requestAddPost"]').submit();



});


</script>
@include('modal.request_update') 
@endsection