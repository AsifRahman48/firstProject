
@extends('layouts.elaadmin')

@section('content')
<style type="text/css">
    .fa-plus-square:hover{
        color: green;
    }
    .fa-minus-square:hover{
        color: red;
    }
    .td{
         border: 1px #000 solid;
    }
</style>
<!-- <script src="//cdn.ckeditor.com/4.11.1/standard/ckeditor.js"></script> -->
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
                            {!! Form::open(['url'=>'request/update_draft_request', 'class'=>'form-horizontal', 'role'=>'form', 'name'=>'requestAddPost', 'enctype'=>'multipart/form-data']) !!}
                            	<input type="hidden" id="_token" name="_token" value="{{ csrf_token() }} ">
	                            <input type="hidden" name="id" value="{{$data['TicketInfo']->id}}">
	                            
                              <input type="hidden" name="previousUrl" value="{{$data['previousUrl']}}">
 <div class="row form-group">
                                    <div class="col col-md-2">
                                        {!! Html::decode(Form::label('CompanyName', 'Company Name <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                                    </div>
                                    <div class="col-12 col-md-10">
                                        {!! Form::select('company_id', $data['CompanyName'], $data['TicketInfo']->company_id, ['id' => 'company_id', 'placeholder' => 'Select company name', 'class' => 'form-control select2', 'required' => 'required']) !!}

                                        @if($errors->has('company_id'))
                                            <small class="help-block form-text">{{ $errors->first('company_id') }}</small>
                                        @endif
                                        <!-- <small style="display: none;" class="help-block form-text" id="cat_error"></small> -->
                                    </div>
                                </div> 
                              
                                <div class="row form-group">
                                    <div class="col col-md-2">
                                        {!! Html::decode(Form::label('cat_id', 'Department <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                                    </div>
                                    <div class="col-12 col-md-10">
                                        {!! Form::select('cat_id', $data['catList'],$data['TicketInfo']->cat_id, ['id' => 'select_cat', 'placeholder' => 'Select Department...', 'class' => 'form-control select2', 'required' => 'required']) !!}
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
                                        <select id="select_sub_cat" class="form-control select2" required="required" name="sub_cat_id">
                                            <!-- <option selected="selected" value="">Select category first...</option> -->
                                            @foreach($data['subcatList'] as $info)
                                            <option value="{{$info->id}}" @if($data['TicketInfo']->sub_cat_id==$info->id) selected @endif>{{$info->name}}</option>
                                            @endforeach
                                        </select>
                                        <small style="display: none;" class="help-block form-text" id="sub_cat_error"></small>
                                    </div>
                                </div>

                             <div class="row ">
                                 <div class="col-md-12 col-12" >
                                    <div class="row form-group">
                                    <div class="col col-md-2">
                                        <label for="sub_cat_id" class="form-control-label">Recommender <span class="mandatory-field">*</span></label>
                                    </div>
                                    <div class="col col-md-10">                       
                                    @php
                                        $slLI=1;
                                     @endphp     
                                     @foreach($data['recommenderList'] as $RecomInfo)
                      <div class="input-group" style="margin-bottom: 10px;">
                      <div id="removeReconmonder{{$slLI}}" style="width: 100%" class="d-flex">
                      <select name="recommender_id[]" id="" class="form-control select2" required="1" @php if($slLI==1){    @endphp  style="width: 95%;"   @php }    @endphp  >
                      <option value="">Select Recommender</option>
                      @foreach($data['userList2'] as $key => $value)
                      <option value="{{$value->id}}"  @if($value->id==$RecomInfo->id) selected @endif>{{$value->name}}-{{$value->title}}-{{$value->department}}</option>
                      @endforeach
                      </select>
                      @php if($slLI==1){    @endphp 
                      <a onclick="slectUserListForForm('#Recommender')"  data-toggle="modal" data-target="#exampleModal">
                      &nbsp; &nbsp; &nbsp;<span class="fa fa-plus-square" ></span>
                      </a>

                      @php }else{    @endphp  

                      &nbsp;&nbsp;&nbsp; 
                      <i id="{{$slLI}}" class="fa fa-minus-square removeReconmonderold" value="" aria-hidden="true" style="" ></i>
                      @php } @endphp           
                      </div>
                      </div>
                                               @php
$slLI++;
                                     @endphp 
                                        @endforeach    
                                           <div id="Recommender"></div>                      
                                     </div>

                         

                                  </div>
                                </div>

                                   <div class="col-md-12 col-12">
                                    <div class="row form-group">
                                    <div class="col col-md-2">
                                        <label for="sub_cat_id" class="form-control-label">Approver <span class="mandatory-field">*</span></label>
                                    </div>
                                    <div class="col col-md-10">   
                                                                 
                                          @php $RslLT=1; @endphp
                                        @foreach($data['approverList'] as $ApproInfo)
                                         <div class="input-group" style="margin-bottom: 10px;">   
                                        <!-- <input type="text" name="approver_id[]" value="{{$ApproInfo->name}}" class="form-control"> -->
                                   <!--       {!! Form::select('approver_id[]', $data['userList'],$ApproInfo->id, ['id' => 'Approver1', 'placeholder' => 'Select Approver', 'class' => 'form-control select2', 'required' => 'required','style'=>'width:90%']) !!} -->
                       
                  <div id="removeApproverold{{$RslLT}}" style="width: 100%">                
                    <select name="approver_id[]" id="" class="form-control select2" required="1" style="width: 95%;">
                                             <option value="">Select Approver</option>
                                              @foreach($data['userList2'] as $key => $value)

                                              <option value="{{$value->id}}"  @if($value->id==$ApproInfo->id) selected @endif>{{$value->name}}-{{$value->title}}-{{$value->department}}</option>
                                              @endforeach
                                         </select>
                                 @php if($RslLT==1){    @endphp         
                                     <!--     <div class="bg-success pull-right text-light  input-group-addon"  onclick="slectUserListForForm('#Approver')"  data-toggle="modal" data-target="#exampleModal">
                                                            <span class="fa fa-plus-square" ></span>
                                                        </div> -->
                                                          <a  onclick="slectUserListForForm('#Approver')"  data-toggle="modal" data-target="#exampleModal">
                                                             &nbsp; &nbsp;<span class="fa fa-plus-square" ></span>
                                                        </a>
                           @php }else{    @endphp  

                      <i id="{{$RslLT}}" class=" fa fa-minus-square removeApproverold" value="" aria-hidden="true" ></i>


                          @php } $RslLT++; @endphp 


                                          </div>
                                           </div>
                                        @endforeach                                        
                                             <div id="Approver"></div>

                                   

                                  </div>
                                </div>
                                            
                             </div>
                      </div>

                                <div class="row form-group">
                                    <div class="col col-md-2">
                                        {!! Html::decode(Form::label('tSubject', 'Subject <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                                    </div>
                                    <div class="col-12 col-md-10">
                                        {!! Form::text('tSubject',$data['TicketInfo']->tSubject, ['class' => 'form-control', 'placeholder' => 'Subject', 'required' => 'required']) !!}

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
                                        {!! Form::textarea('tDescription', $data['TicketInfo']->tDescription, ['class' => 'form-control readonly', 'rows' => 7,'id'=>'messageArea', 'cols' => 54, 'style' => 'resize:none']) !!}                                     
                                        @if($errors->has('tDescription'))
                                            <small class="help-block form-text">{{ $errors->first('tDescription') }}</small>
                                        @endif
                                    </div>
                                </div>                            
                                <br>
                                <br>
 @if(count($data['attachmentFile'])>0)
                                   <div class="row form-group">
                                    <div class="col col-md-2">
                                        {!! Html::decode(Form::label('tFile', 'Attachment', ['class' => 'form-control-label'])) !!}
                                    </div>
                                    <div class="col-12 col-md-10">
                                    @foreach($data['attachmentFile'] as $attachmentFile)
                                    <div class="input-group" id="oldF{{$attachmentFile->id}}" style="margin-bottom: 10px;">
                                        <a href="{{url('/')}}/{{$attachmentFile->folder}}/{{rawurlencode($attachmentFile->file_name)}}" target="_blank">{{$attachmentFile->file_name}}</a>
&nbsp;&nbsp;&nbsp;
                                        <i id="{{$attachmentFile->id}}" class=" pull-right fa fa-trash deleteOldFile"  aria-hidden="true" style="padding: 3px; border-radius: 4px; color:red;"></i>
                                    </div>
                                    @endforeach
                                    </div>

                                </div>
                                @endif
                        <!-- <br> -->
                              <div class="row form-group">
                                    <div class="col col-md-2">
                                       
                                    </div>
                                    <div class="col-12 col-md-10 " id="fileShizeShowDiv">
                                         <!--  <div id="">
                                              
                                          </div> -->
                                    </div>
                                    </div>
                            <div class="row form-group">
                                    <div class="col col-md-2">
                                        {!! Html::decode(Form::label('tFile', 'Attachment', ['class' => 'form-control-label'])) !!}
                                    </div>
                                    <div class="col-12 col-md-10">
                                       <input type="file" id="tFile0" name="tFile[]" class="form-control-file" style='padding:0; margin-bottom:8px;' onchange="GetFileSize(this)" />
 <div class="col-md-12">
                                  <small class="form-control-feedback">&nbsp; Only pdf, doc, xls, jpg, jpeg & png formats are allowed. (Please try to attach your file below 2 MB)</small>

                                   </div> 
                                    <!--     {!! Form::file('tFile[]', ['class' => 'form-control-file', 'style' => 'padding:0; margin-bottom:8px;']) !!} -->
                                     <!--    <small class="form-control-feedback">&nbsp; Only pdf, doc, xls, jpg, jpeg & png formats are allowed. </small>  -->
                                    <div id="uploadFile"> </div>
                                 <i  class="pull-left fa fa-plus-square AddUploadFile" aria-hidden="true" style="padding: 3px; border-radius: 4px;"></i>
                                         @if($errors->has('tFile'))
                                            <small class="help-block form-text">{{ $errors->first('tFile') }}</small>
                                        @endif
                                    </div>
                                </div>
                                <br>
                                <br>

                                <div class="form-actions form-group text-center">
                                    <a href="{{ $data['previousUrl'] }}">
                                        <span class="btn btn-secondary btn-xs pull-left" style=""><i class="fa fa-backward"></i>  Back</span>
                                    </a>
                                    <button type="submit" name='tStatus' id="save" value="2" class="btn btn-info  pull-right"  style="margin-left: 10px;">
                                        Submit <i class="fa fa-forward"></i>
                                    </button>
                                    {!! Form::button('<i class="fa fa-cloud-upload"></i> Save as draft', ['type' => 'submit', 'name' => 'tStatus', 'value' => '1', 'class' => 'btn btn-info btn-xs pull-right','style' => 'margin-left: 25px;','id'=>'draft_save']) !!}

                                 </div>
                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
<script src="{{ asset('select2/dist/js/select2.min.js')}}" type='text/javascript'></script>

<!-- CSS -->
<link href="{{ asset('select2/dist/css/select2.min.css')}}" rel='stylesheet' type='text/css'>

    <script src="{{ asset('lib/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('lib/tinymce/form-configuration.js') }}"></script>
  <script type="text/javascript">


        	function round(value, decimals) {
        	  	return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
        	}

			var input, file,totalFileSizeFinal=0;
			function GetFileSize(elem) {
				$('#fileShizeShowDiv').empty();

				var totalFileSize = 0;
				 // var input, file,total;
				var id = $(elem).attr("id");
				
				// Get total size for all
				// Get first one
				if( $('#tFile0').val() != '' ){
					var firstFileInput = document.getElementById('tFile0');
					var firstFile = firstFileInput.files[0];
					totalFileSize = (firstFile.size/1024)/1024;
				}
				
				// other children
				if( $('#uploadFile').children().length > 0 ){

					var totalFileCount = parseInt($('#uploadFile').children().last().attr('id').match(/\d+/)[0], 10);
					for( var i = 1; i <= totalFileCount; i++ ){

						if( $('#tFile'+i).length > 0 && $('#tFile'+i).val() != '' ){

							let input = document.getElementById('tFile'+i);
							let file = input.files[0];
							let inputFileSize1=(file.size/1024)/1024;

							totalFileSize+=inputFileSize1;
						}
						
					}

				}

				input = document.getElementById(id);
				file = input.files[0];
				var inputFileSize1=(file.size/1024)/1024;
				// alert(inputFileSize1);
				// totalFileSize+=inputFileSize1;
				Fhabib=''
				if( 10*1024 < 1024*totalFileSize ){

					alert('Warning! File upload size limit exceeded (Max upload size 10MB).');
					$(elem).val('');
					totalFileSize = (totalFileSize - inputFileSize1);

					Fhabib+='<p style="color:#000">Selected File Size <b style="color:green;">'+round(totalFileSize, 2)+' MB</b></p>';
					$('#fileShizeShowDiv').append(Fhabib);

				  // Fhabib+='<p style="color:#000">Selected File Size <b style="color:red;">'+round(totalFileSize, 2)+' MB</b></p>';
				  // Fhabib+='<p style="color:red">Total file size maximum limit has been exceeded</p>';
				  // document.getElementById("draft_save").disabled = true;
				  // document.getElementById("save").disabled = true;

				  //$('#fileShizeShowDiv').append(Fhabib);

				}else{
					
					Fhabib+='<p style="color:#000">Selected File Size <b style="color:green;">'+round(totalFileSize, 2)+' MB</b></p>'; 
					// document.getElementById("draft_save").disabled = false;
					// document.getElementById("save").disabled = false;

					$('#fileShizeShowDiv').append(Fhabib);
				}

				// console.log(totalFileSize);

				totalFileSizeFinal = totalFileSize;
			}

    // CKEDITOR.replace( 'messageArea',
    //      {
    //       customConfig : 'config.js',
    //       toolbar : 'simple',
    //       disableNativeSpellChecker: false
    //       })

        $(document).ready(function(){
  $(".select2").select2();
});

          $(document).on('click','.removeReconmonderold', function(){

                var button_id = $(this).attr("id");
                      // alert(button_id);
                $("#removeReconmonder"+button_id+"").remove();
            });  

         $(document).on('click','.removeReconmonder', function(){

                var button_id = $(this).attr("id");
                      // alert(button_id);
                $("#removeReconmonderRow"+button_id+"").remove();
            }); 
    $(document).on('click','.removeApproverold', function(){
                var button_id = $(this).attr("id");
                $("#removeApproverold"+button_id+"").remove();
            }); 
   $(document).on('click','.removeApprover', function(){
                var button_id = $(this).attr("id");
                $("#removeApproverRow"+button_id+"").remove();
            }); 

     function slectUserListForForm(divId){
            ImportDivId=divId;
                $('#InfoResult').empty();
                 $('#sortable').empty();
         

        }


 
   var i=1;



// =================UploadFile========================
     
         $('.AddUploadFile').click(function(){
            i++;
    var habib='';
        habib+='<div class="field-wrap" id="AddUploadFileRow'+i+'" style="margin-top: 5px;  margin-bottom:30px;">';
        habib+='<div class="col-md-12"  style="padding:0px">';
        habib+='<input type="file" id="tFile'+i+'" name="tFile[]" class="form-control-file" style="margin-bottom: 8px; margin-top: 8px;"  onchange="GetFileSize(this)">';
      
        habib+='</div>';
        habib+='<div class="col-md-12" style="margin-top: 5px; margin-bottom:15px;">';
        habib+='<i id="'+i+'" class=" pull-right fa fa-minus-square btn_upload_remove" aria-hidden="true" style="padding: 3px; border-radius: 4px; color:red;"></i>';
        habib+='<small class="form-control-feedback pull-left">&nbsp; Only pdf, doc, xls, jpg, jpeg & png formats are allowed. </small>';
   
        habib+='</div>';
        habib+='</div>';
    $('#uploadFile').append(habib);
            });

             // Remove MB
            $(document).on('click','.btn_upload_remove', function(){
                 var button_id = $(this).attr("id");
                 $('#fileShizeShowDiv').empty();   
                 input = document.getElementById('tFile'+button_id);
                 if(input.files.length == 0 ){
                     $("#AddUploadFileRow"+button_id).remove();
                     return false;
                 }
                 file = input.files[0];
                 var inputFileSize1=(file.size/1024)/1024;
                 totalFileSizeFinal = totalFileSizeFinal - inputFileSize1;
                 var  FHabib='';
         		FHabib+='<p style="color:#000">Selected File Size <b style="color:green;">'+round(totalFileSizeFinal, 2)+' MB</b></p>';

         		$('#fileShizeShowDiv').empty();
         		$('#fileShizeShowDiv').append(FHabib);
         		$("#AddUploadFileRow"+button_id+"").remove();

         	});



 /**
         * Setup ajax Header
         */
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

 $(document).on('click','.deleteOldFile', function(){
     if (confirm("Are you sure you want to delete this File ? ")) {
        var fileID = $(this).attr("id");
                  $.ajax({
                    type: 'POST',
                    url: '{{ URL::to('/request/deleteOldFile')}}',
                    data: {FileId :fileID},
                    datatype: 'json',                   
                     beforeSend: function(){
                        $('#loader').show();
                    },
                    complete: function(){
                            $('#loader').hide();
                        },
                    success: function (data) {
                        // alert('habib');
                          $("#oldF"+fileID+"").remove();
                    }
                });
    }
    return false;
              
            }); 

       

        function update(type,action,id){

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
    @include('modal.request_create_modal') 
@endsection