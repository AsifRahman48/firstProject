
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

                            {!! Form::open(['url'=>'request/update_status', 'class'=>'form-horizontal', 'role'=>'form', 'name'=>'requestAddPost', 'enctype'=>'multipart/form-data']) !!}
                            <input type="hidden" name="id" value="{{$data['TicketInfo']->id}}">
                            <input type="hidden" name="previousUrl" value="{{$data['previousUrl']}}">
                                <div class="row form-group">
                                    <div class="col col-md-2">
                                        {!! Html::decode(Form::label('cat_id', 'Select Department <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
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
                                        <label for="sub_cat_id" class="form-control-label">Select Unit/Section <span class="mandatory-field">*</span></label>
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
                                        {!! Html::decode(Form::label('recommender_id', 'Recommender / Approver <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                                    </div>
                                    <div class="col-12 col-md-10">
                                        {!! Form::select('recommender_id', $data['userList'], $data['TicketInfo']->recommender_id, ['placeholder' => 'Select Recommender / Approver', 'class' => 'form-control custom-select select2-select', 'required' => 'required','disabled'=>'disabled']) !!}

                                        @if($errors->has('recommender_id'))
                                            <small class="help-block form-text">{{ $errors->first('recommender_id') }}</small>
                                        @endif
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
                                        {!! Html::decode($data['TicketInfo']->tDescription)!!}
                                        {!! Form::hidden('tDescription', $data['TicketInfo']->tDescription, ['class' => 'form-control readonly', 'rows' => 7, 'cols' => 54, 'style' => 'resize:none']) !!}

                                        @if($errors->has('tDescription'))
                                            <small class="help-block form-text">{{ $errors->first('tDescription') }}</small>
                                        @endif
                                    </div>
                                </div>



           <div class="row form-group text-center recommender_details">
            <div class="col col-md-2">
 
</div>
            <div class="col-md-10 ">

<!-- <button type="button" class="btn btn-info  pull-left" onclick="checkButton('SaveAsdraft')"  style="margin-left: 25px;" >
<input type="radio" name="formAction" value="1" id="SaveAsdraft"> Save as draft
   </button> -->
   <button type="button" class="btn btn-info  pull-left" onclick="checkButton('habib')"   style="margin-left: 25px;">
<input type="radio" name="formAction" value="4" id="habib"> Approve
   </button> 

   <button type="button" class="btn btn-info  pull-left" onclick="checkButton('Reject')"  style="margin-left: 25px;" >
<input type="radio" name="formAction" value="5" id="Reject"> Reject
   </button>

   <button type="button" class="btn btn-info  pull-left" onclick="checkButton('RequestforInfo')"  style="margin-left: 25px;">
<input type="radio" name="formAction" value="6" id="RequestforInfo"> Request for Info
   </button>

   <button type="button" class="btn btn-info  pull-left" onclick="checkButton('Forward')"  style="margin-left: 25px;">
<input type="radio" name="formAction" value="7" id="Forward"> Forward
   </button>



     <button type="button" class="btn btn-info  pull-left" onclick="checkButton('ApproveAndForward')"   style="margin-left: 25px;">
<input type="radio" name="formAction" value="404" id="ApproveAndForward"> Approve and Forward
   </button>

  
<div class="col-md-2 pull-left invisible" id="forwardSelectDiv">
        
           {!! Form::select('forwardBy', $data['userList'],null, ['placeholder' => 'Select Forward Person ', 'class' => 'form-control custom-select select2-select']) !!}
  
                </div>                                         

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
                                <br>

                                   <div class="row form-group">
                                    <div class="col col-md-2">
                                        {!! Html::decode(Form::label('tFile', 'Add attachment', ['class' => 'form-control-label'])) !!}
                                    </div>
                                    <div class="col-12 col-md-10">



                                        {!! Form::file('tFile', ['class' => 'form-control', 'style' => 'padding:0;']) !!}
                                        <small class="form-control-feedback"> Only pdf, doc, xls, jpg, jpeg & png formats are allowed. </small>

                                        @if($errors->has('tFile'))
                                            <small class="help-block form-text">{{ $errors->first('tFile') }}</small>
                                        @endif
                                    </div>
                                     <div class="col col-md-2">
                                       <!-- File ::  <a target="_blank" href="{{ asset('/storage/app/public/ticket-files') }}/{{$data['TicketInfo']->tFile}}">File View</a> -->
                                    </div>
                                </div>
<br>
<br>
<br>

                                <div class="form-actions form-group text-center">
                                    <a href="{{ url('/') }}"><span class="btn btn-secondary btn-xs pull-left" style=""><i class="fa fa-backward"></i>  Back</span></a>
                                   <!--  {!! Form::button('Submit <i class="fa fa-forward"></i>', ['type' => 'submit', 'name' => 'tStatus', 'value' => '2', 'class' => 'btn btn-success btn-xs pull-right', 'style' => 'margin-left: 25px;']) !!} -->
                                  <!--   {!! Form::button('<i class="fa fa-cloud-upload"></i> Save as draft', ['type' => 'submit', 'name' => 'tStatus', 'value' => '1', 'class' => 'btn btn-info btn-xs pull-left','style' => 'margin-left: 25px;']) !!} -->

                               <!--    <button class="btn btn-sm btn-info" name="approve" type="button" onclick="update('4','approve',{{$data['TicketInfo']->id}})">Approved</button> -->


                                             




<button type="submit" class="btn btn-info  pull-right" >
Save <i class="fa fa-forward"></i>
   </button>
   

                                    
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
function checkButton(id){
    var isChecked = $('#'+id).is(':checked');
       var forward = document.getElementById(id).value;
         // alert(forward);
      if (isChecked) {

         $('#'+id).prop('checked', false);
         // $('#'+id).removeClass('btn-info').addClass('btn-default');
    if(forward=='404'){
 $('#forwardSelectDiv').removeClass('visible').addClass('invisible');

         }

            } else {
                   $('#'+id).prop('checked', true);
                   // $('#'+id).removeClass('btn-default').addClass('btn-info');
                    if(forward=='404'){
           
        $('#forwardSelectDiv').removeClass('invisible').addClass('visible');

         }else{
            $('#forwardSelectDiv').removeClass('visible').addClass('invisible'); 
         }

            }

}




        $(document).ready(function() {
            $('.select2-select').select2();
     
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
@endsection