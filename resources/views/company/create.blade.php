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
                                <li><a href="{{ url("company/list") }}">Company List</a></li>
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
                            <strong>Add</strong> Company
                        </div>
                        <div class="card-body card-block ">
                            <div class="com-md-8  mx-auto">

                            @if (session('error'))
                                <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show">
                                    <span class="badge badge-pill badge-danger">Danger</span>
                                    {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            @endif

                            {!! Form::open(['url'=>'company/store', 'class'=>'form-horizontal', 'role'=>'form', 'name'=>'categoryAddPost', 'enctype'=>'multipart/form-data']) !!}
                                <div class="row form-group">
                                    <div class="col col-md-12 d-flex">
                                        {!! Html::decode(Form::label('name', 'Company name <span class="mandatory-field">*</span>', ['class' => 'form-control-label col-md-3' ])) !!}

                                        {!! Form::text('name', old('name'), ['class' => 'form-control col-md-8', 'placeholder' => 'Company name', 'required' => 'required','id'=>'company_name']) !!}
                                       
                                    </div>
                                  @if($errors->has('name'))
                                       
                                            <small class="help-block form-text text-danger">{{ $errors->first('name') }}</small>
                                   
                                        @endif
                                </div>  
  <div class="row form-group">
                                    <div class="col col-md-12 d-flex">
                                        {!! Html::decode(Form::label('shortName', 'Short name <span class="mandatory-field">*</span>', ['class' => 'form-control-label col-md-3' ])) !!}

                                        {!! Form::text('shortName', old('shortName'), ['class' => 'form-control col-md-8', 'placeholder' => 'Company Short name', 'required' => 'required','id'=>'shortName']) !!}
                                       
                                    </div>
                                  @if($errors->has('shortName'))
                                       
                                            <small class="help-block form-text text-danger">{{ $errors->first('shortName') }}</small>
                                   
                                        @endif
                                </div> 

                              <div class="row form-group">
                                    <div class="col col-md-12 d-flex">

                                        {!! Html::decode(Form::label('active_date', 'Active Date <span class="mandatory-field">*</span>', ['class' => 'form-control-label col-md-3'])) !!}
                                        {!! Form::text('active_date',old('active_date'), ['class' => 'form-control col-md-8 datepicker', 'placeholder' => 'Active Date', 'required' => 'required']) !!}
                                         @if($errors->has('active_date'))
                                            <small class="help-block form-text">{{ $errors->first('active_date') }}</small>
                                        @endif
                                    </div>
                                   
                                </div>
                                <div class="row form-group">
                                     <div class="col-12 col-md-12  d-flex">
                                            {!! Html::decode(Form::label('deactive_date', 'Deactive Date ', ['class' => 'form-control-label col-md-3'])) !!}
                                        {!! Form::text('deactive_date', old('deactive_date'), ['class' => 'form-control col-md-8 datepicker2', 'placeholder' => 'Deactive Date']) !!}

                                        @if($errors->has('namdeactive_date'))
                                            <small class="help-block form-text">{{ $errors->first('deactive_date') }}</small>
                                        @endif
                                    </div>
                                    
                                </div>

                                   <div class="row form-group">
                                     <div class="col-12 col-md-12  d-flex">
                            {!! Html::decode(Form::label('Comapny Logo', 'Company Logo <span class="mandatory-field">*</span> ', ['class' => 'form-control-label col-md-3'])) !!}
            {!! Form::file('logo', old('logo'), ['class' => 'form-control-file col-md-8 datepicker2', 'placeholder' => 'Company Logo']) !!}
             
                                    </div>
                                    
                                </div>
                                 <div class="row">
                                    <div class="col-md-3">

                                    </div>
                                    <div class="col-md-8">
                                  <small class="form-control-feedback">&nbsp; maximum size W:160 X H:80</small>
                                  <br>
  @if($errors->has('logo'))
                                            <small class="help-block form-text text-danger">{{ $errors->first('logo') }}</small>
                                        @endif
                                        </div>
                                   </div>   

                                <div class="form-actions form-group">
                                    <a href="{{ url("company/list") }}"><span class="btn btn-secondary btn-xs"><i class="fa fa-backward"></i>  Back</span></a>
                                    {!! Form::button('Submit <i class="fa fa-forward"></i>', ['type' => 'submit', 'class' => 'btn btn-success btn-xs pull-right']) !!}
                                </div>
                            {!! Form::close() !!}
                        </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
   var $datepicker = $('.datepicker');
$datepicker.datepicker({ dateFormat: 'dd-mm-yy' });
$datepicker.datepicker('setDate', new Date());

$('.datepicker2').datepicker({ dateFormat: 'dd-mm-yy' }).val();
        });
    </script>

@endsection