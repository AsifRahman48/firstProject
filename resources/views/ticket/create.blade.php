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
   .select2-selection{
    margin-bottom: 10px;
}
.mce-item-table{
  border: 2px solid red !important;
  border-style: solid !important;
  }
  .mce-item-table td{
     border: 2px solid red !important;
  border-style: solid !important;;
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
                        <strong>Create New</strong> Request
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

                        {!! Form::open(['url'=>'request_new', 'class'=>'form-horizontal', 'role'=>'form', 'name'=>'requestAddPost', 'enctype'=>'multipart/form-data']) !!}

	                        <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }} ">
                        <div class="row form-group">
                            <div class="col-12 col-md-2">
                                {!! Html::decode(Form::label('CompanyName', 'Company Name <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                            </div>
                            <div class="col-12 col-md-10">
                                {!! Form::select('company_id', $data['CompanyName'], old('company_id'), ['id' => 'company_id', 'placeholder' => 'Select company name', 'class' => 'form-control select2', 'required' => 'required']) !!}

                                @if($errors->has('company_id'))
                                <small class="help-block form-text">{{ $errors->first('company_id') }}</small>
                                @endif
                                <!-- <small style="display: none;" class="help-block form-text" id="cat_error"></small> -->
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-12 col-md-2">
                                {!! Html::decode(Form::label('cat_id', 'Department <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                            </div>
                            <div class="col-12 col-md-10">
                                {!! Form::select('cat_id', $data['catList'], old('cat_id'), ['id' => 'select_cat', 'placeholder' => 'Select Department', 'class' => 'form-control select2', 'required' => 'required']) !!}

                                @if($errors->has('cat_id'))
                                <small class="help-block form-text">{{ $errors->first('cat_id') }}</small>
                                @endif
                                <small style="display: none;" class="help-block form-text" id="cat_error"></small>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-12 col-md-2">
                                <label for="sub_cat_id" class="form-control-label">Unit/Section <span class="mandatory-field">*</span></label>
                            </div>
                            <div class="col-12 col-md-10">
                                <select id="select_sub_cat" class="form-control select2" required="required" name="sub_cat_id" onchange="hideError()">
                                    <option selected="selected" value="">Select Department first...</option>
                                </select>
                                <small style="display: none;" class="help-block form-text" id="sub_cat_error"></small>
                            </div>
                        </div>
                            <div class="row form-group">

                            <div class="col-12 col-md-2">
                                <label for="sub_cat_id" class="form-control-label">Recommender <span class="mandatory-field">*</span></label>
                            </div>
                            <div class="col-12 col-md-10">
                              <samp id="Recommendera">
                               <select id="Recommender1" name="recommender_id[]" class="form-control selUser select2" required="1">
                                   <option value="">Select Recommender</option>

                               </select>
                           </samp>
                           <a onclick="slectUserListForForm('#Recommender')"  data-toggle="modal" data-target="#exampleModal">
                            &nbsp; &nbsp; &nbsp; <span class="fa fa-plus-square" ></span>
                        </a>

                        <div id="Recommender"></div>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-12 col-md-2">
                        <label for="sub_cat_id" class="form-control-label">Approver <span class="mandatory-field">*</span></label>
                    </div>
                    <div class="col-12 col-md-10">
                        <!-- <div class="input-group" >        -->
                            <samp id="Approvera">
                                <select name="approver_id[]" id="Approver1" class="form-control selUser select2"  style="margin-bottom: 20px;" required="1">
                                   <option value="">Select Approver</option>
                               </select>
                           </samp>
                           <!-- addNewApprover  -->
                           <a  onclick="slectUserListForForm('#Approver')"  data-toggle="modal" data-target="#exampleModal">
                            &nbsp; &nbsp; &nbsp; <span class="fa fa-plus-square" ></span>
                        </a>
                        <br>
                        <div id="Approver"></div>
                    </div>
                </div>

            <div class="row form-group">
                <div class="col-12 col-md-2">
                    {!! Html::decode(Form::label('tSubject', 'Subject <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                </div>
                <div class="col-12 col-md-10">
                    {!! Form::text('tSubject', old('tSubject'), ['class' => 'form-control', 'placeholder' => 'Subject', 'required' => 'required']) !!}

                    @if($errors->has('tSubject'))
                    <small class="help-block form-text">{{ $errors->first('tSubject') }}</small>
                    @endif
                </div>
            </div>

            <div class="row form-group">
                <div class="col-12 col-md-2">
                    {!! Html::decode(Form::label('tDescription', 'Description  <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                </div>
                <div class="col-12 col-md-10 tinymceTable">
                    {!! Form::textarea('tDescription', old('tDescription'), ['class' => 'form-control', 'rows' => 7, 'cols' => 54, 'style' => 'resize:none','id'=>'messageArea',]) !!}

                    @if($errors->has('tDescription'))
                    <small class="help-block form-text">{{ $errors->first('tDescription') }}</small>
                    @endif
                </div>
            </div>
            <div class="row form-group">
                <div class="col-12 col-md-2">

                </div>
                <div class="col-12 col-md-10 " id="fileShizeShowDiv">
                                             <!--  <div id="">

                                             </div> -->
                                         </div>
                                     </div>

                                     <div class="row form-group">
                                        <div class="col-12 col-md-2">
                                            {!! Html::decode(Form::label('tFile', 'Attachment', ['class' => 'form-control-label'])) !!}
                                        </div>
                                        <div class="col-12 col-md-10">
                                          <div class="row">
                                            <div class="col-md-5">
                                               <input type="file" id="tFile0" name="tFile[]" class="form-control-file" style='padding:0; margin-bottom:8px;' onchange="GetFileSize(this)" />
                                            </div>
                                          </div>
                                           <div class="row">
                                             <div class="col-md-12">
                                              <small class="form-control-feedback">Only pdf, doc, xls, jpg, jpeg & png formats are allowed. (Maximum attached file size below 10 MB)</small>
                                          </div>
                                           </div>
                                          <div id="uploadFile">
                                          </div>

                                          <i  class="pull-left fa fa-plus-square AddUploadFile" aria-hidden="true" style="padding: 3px; border-radius: 4px;"></i>
                                          @if($errors->has('tFile'))
                                          <small class="help-block form-text">{{ $errors->first('tFile') }}</small>
                                          @endif
                                      </div>
                                  </div>

                                  <div class="row form-group">
                                    <div class="col-12 col-md-2">
                                       <!--    {!! Html::decode(Form::label('Priority', 'Priority  <small>( Optional )</small>', ['class' => 'form-control-label'])) !!} -->
                                   </div>
                                   <div class="col-12 col-md-10">
                                    <input type="hidden" name="priority" value="2">
                                           <!-- <select name="priority" class="hide">
                                            <option value="1" selected>Low</option>
                                            <option value="2" >Medium</option>
                                            <option value="1">High</option>


                                        </select> -->
                                    </div>
                                </div>

                                <div class="form-actions form-group">
                                    <a href="{{ url('/') }}"><span class="btn btn-secondary btn-xs"><i class="fa fa-backward"></i>  Back</span></a>
                                    {!! Form::button('Submit <i class="fa fa-forward"></i>', ['type' => 'submit', 'name' => 'tStatus', 'value' => '2', 'class' => 'btn btn-success btn-xs pull-right','id'=>'save', 'style' => 'margin-left: 25px;']) !!}
                                    {!! Form::button('<i class="fa fa-cloud-upload"></i> Save as draft', ['type' => 'submit', 'name' => 'tStatus', 'value' => '1','id'=>'draft_save', 'class' => 'btn btn-info btn-xs pull-right']) !!}
                                </div>
                                {!! Form::close() !!}

                            </div>
                        </div>
                    </div>
                </div>
            </div>





        </div>


        <script src="{{ asset('lib/tinymce/tinymce.min.js') }}"></script>
        <script src="{{ asset('lib/tinymce/form-configuration.js') }}"></script>
        <!-- Script -->

        <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet" />
        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
        <script src="{{ asset('select2/dist/js/select2.min.js')}}" type='text/javascript'></script>
        <!-- <script src="//cdn.ckeditor.com/4.11.1/standard/ckeditor.js"></script> -->
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



    // CKEDITOR.on( 'messageArea', function( ev ) {
    //     var dialogName = ev.data.name;
    //     var dialogDefinition = ev.data.definition;

    //     if ( dialogName == 'table' ) {
    //         var info = dialogDefinition.getContents( 'info' );

    //         // info.get( 'txtWidth' )[ 'default' ] = '100%';       // Set default width to 100%
    //         info.get( 'txtBorder' )[ 'default' ] = '1';         // Set default border to 0
    //     }
    // });



  //   CKEDITOR.replace( 'messageArea',
  //   {
  //     customConfig : 'config.js',
  //     toolbar : 'simple',
  //     disableNativeSpellChecker: false
  // })
</script>


<!-- CSS -->
<link href="{{ asset('select2/dist/css/select2.min.css')}}" rel='stylesheet' type='text/css'>



<script type="text/javascript">
    var ImportDivId=0;

              /**
             * Setup ajax Header
             */
             $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

             function slectUserListForForm(divId){
                ImportDivId=divId;
                $('#InfoResult').empty();
                $('#sortable').empty();
                if(ImportDivId=='#Recommender'){
                    var inputarrayReconmonder=[];

                }else if(ImportDivId=='#Approver'){
                    inputarrayApprover=[];

                }


            }

    //         $("#Recommender1").click(function () {

    //             // $('#exampleModal').modal('data-target')
    //             // $('#exampleModal').modal('toggle')
    //             // $('#exampleModal').modal('show')
    //                 // alert('ok');
    //                 $('#InfoResult').empty();
    //                 $('#advancedSearchShow').empty();
    //                 $('#sortable').empty();
    // // var url = $(location).attr('href');
    // // $('#spn_url').html('<strong>' + url + '</strong>');
    // });





    $(document).ready(function(){

      $(document).on('click','.removeReconmonder', function(){
        var button_id = $(this).attr("id");
        $("#removeReconmonderRow"+button_id+"").remove();
    });
      $(document).on('click','.removeApprover', function(){
        var button_id = $(this).attr("id");
        $("#removeApproverRow"+button_id+"").remove();
    });





    // =================UploadFile========================
    $('.AddUploadFile').click(function(){
        // var loop=<?php //echo $data['userList2'] ?>;
        i++;
        var habib='';
        habib+='<div class="field-wrap" id="AddUploadFileRow'+i+'" style="margin-top: 5px;  margin-bottom:30px;">';
        habib+='<div class="col-md-5"  style="padding:0px">';
        habib+='<input type="file" id="tFile'+i+'" name="tFile[]" class="form-control-file" style="margin-bottom: 8px; margin-top: 8px;"  onchange="GetFileSize(this)">';

        habib+='</div>';
        habib+='<div class="col-md-12" style="margin-top: 5px; margin-bottom:15px; padding-left: 0;">';
        habib+='<i id="'+i+'" class=" pull-right fa fa-minus-square btn_upload_remove" aria-hidden="true" style="padding: 3px; border-radius: 4px;"></i>';
        habib+='<small class="form-control-feedback pull-left">Only pdf, doc, xls, jpg, jpeg & png formats are allowed. </small>';

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



});

    function hideError(){
      $("#cat_error").hide();
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
                        url: "{{ URL::to('/get_sub_cat')}}",
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


             $(document).ready(function(){
               $(".select2").select2();
               $(".selUser").select2({
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
          var nameInfo=account.name+'->'+account.title+'->'+account.department+'->'+account.email+'->'+account.telephonenumber+'->'+account.company_name;
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
       </script>

       @include('modal.request_create_modal')
       @endsection

