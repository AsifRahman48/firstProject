<?php
/**
 * Created by PhpStorm.
 * User: BS108
 * Date: 10/11/2018
 * Time: 5:59 PM
 */
?>
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
                            <strong>Update</strong> Company Name
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
 {!! Form::open(['url'=>['company/update',$data['editData']->id], 'class'=>'form-horizontal', 'role'=>'form', 'name'=>'categoryAddPost', 'enctype'=>'multipart/form-data']) !!}
                       
                               <div class="row form-group">
                                    <div class="col col-md-12 d-flex">
                                        {!! Html::decode(Form::label('name', 'Company name <span class="mandatory-field">*</span>', ['class' => 'form-control-label col-md-3' ])) !!}

                                        {!! Form::text('name',$data['editData']->name, ['class' => 'form-control col-md-8', 'placeholder' => 'Company name', 'required' => 'required']) !!}

                                        @if($errors->has('name'))
                                            <small class="help-block form-text">{{ $errors->first('name') }}</small>
                                        @endif
                                    </div>
                                 
                                </div>  

                                <div class="row form-group">
                                    <div class="col col-md-12 d-flex">
                                        {!! Html::decode(Form::label('shortName', 'Short name <span class="mandatory-field">*</span>', ['class' => 'form-control-label col-md-3' ])) !!}

                                        {!! Form::text('shortName', $data['editData']->short_name, ['class' => 'form-control col-md-8', 'placeholder' => 'Company Short name', 'required' => 'required','id'=>'shortName']) !!}
                                       
                                    </div>
                                  @if($errors->has('shortName'))
                                       
                                            <small class="help-block form-text text-danger">{{ $errors->first('shortName') }}</small>
                                   
                                        @endif
                                </div> 
                              <div class="row form-group">
                                    <div class="col col-md-12 d-flex">

                                        {!! Html::decode(Form::label('active_date', 'Active Date <span class="mandatory-field">*</span>', ['class' => 'form-control-label col-md-3'])) !!}
                                        {!! Form::text('active_date',$data['editData']->active_date, ['class' => 'form-control col-md-8 datepicker2', 'placeholder' => 'Active Date', 'required' => 'required']) !!}
                                         @if($errors->has('active_date'))
                                            <small class="help-block form-text">{{ $errors->first('active_date') }}</small>
                                        @endif
                                    </div>
                                   
                                </div>
                                <div class="row form-group">
                                     <div class="col-12 col-md-12  d-flex">
                                            {!! Html::decode(Form::label('deactive_date', 'Deactive Date ', ['class' => 'form-control-label col-md-3'])) !!}
                                        {!! Form::text('deactive_date',$data['editData']->deactive_date, ['class' => 'form-control col-md-8 datepicker2', 'placeholder' => 'Deactive Date']) !!}

                                        @if($errors->has('deactive_date'))
                                            <small class="help-block form-text">{{ $errors->first('deactive_date') }}</small>
                                        @endif
                                    </div>
                                    
                                </div>
           <div class="row form-group">
                                     <div class="col-12 col-md-12  d-flex">
                            {!! Html::decode(Form::label('Comapny Logo', 'Company Logo <span class="mandatory-field">*</span>', ['class' => 'form-control-label col-md-3'])) !!}
            {!! Form::file('logo', old('logo'), ['class' => 'form-control-file col-md-8 datepicker2', 'placeholder' => 'Company Logo']) !!}

                                        @if($errors->has('logo'))
                                            <small class="help-block form-text text-danger">{{ $errors->first('logo') }}</small>
                                        @endif
                                    </div>
                                 <div class="col-12 col-md-12  d-flex">
                                     
                                    <div class="col-md-8 col-sm-12" style="text-align: right;">
   <small class="form-control-feedback">&nbsp; maximum size W:160 X H:80</small>
                                  <br>
  @if($errors->has('logo'))
                                            <small class="help-block form-text text-danger">{{ $errors->first('logo') }}</small>
                                        @endif
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                  <img src="{{ asset('upload/company')}}/{{$data['editData']->logo}}" style="width: 200px; height: 100px;">
                                        </div>
                                
                        
                                    </div>     
                                </div>
                          




                            <div class="form-actions form-group">
                                <a href="{{ url("company/list") }}"><span class="btn btn-secondary btn-xs"><i class="fa fa-backward"></i>  Back</span></a>
                                {!! Form::button('Update <i class="fa fa-forward"></i>', ['type' => 'submit', 'class' => 'btn btn-success btn-xs pull-right']) !!}
                            </div>
                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <script type="text/javascript">
        $(document).ready(function(){
// $('.datepicker').datepicker();
$('.datepicker').datepicker({ dateFormat: 'dd-mm-yy' }).val();
$('.datepicker2').datepicker({ dateFormat: 'dd-mm-yy' }).val();
        });
    </script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
     <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endsection