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
                                <li><a href="{{ url("category") }}">Department List</a></li>
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
                            <strong>Update</strong> Department
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

                            {!! Form::model($data['editData'], array('route' => array('category.update', $data['editData']->id), 'method' => 'PUT', 'class'=>'form-horizontal', 'role'=>'form', 'name'=>'categoryUpdatePost', 'enctype'=>'multipart/form-data')) !!}
                               <div class="row form-group">
                                    <div class="col col-md-12 d-flex">
                                        {!! Html::decode(Form::label('name', 'Department name <span class="mandatory-field">*</span>', ['class' => 'form-control-label col-md-3' ])) !!}

                                        {!! Form::text('name', old('name'), ['class' => 'form-control col-md-8', 'placeholder' => 'Department name', 'required' => 'required']) !!}

                                        @if($errors->has('name'))
                                            <small class="help-block form-text">{{ $errors->first('name') }}</small>
                                        @endif
                                    </div>
                                 
                                </div>  
                              <div class="row form-group">
                                    <div class="col col-md-12 d-flex">

                                        {!! Html::decode(Form::label('active_date', 'Active Date <span class="mandatory-field">*</span>', ['class' => 'form-control-label col-md-3'])) !!}
                                        {!! Form::text('active_date',old('active_date'), ['class' => 'form-control col-md-8 datepicker2', 'placeholder' => 'Active Date', 'required' => 'required']) !!}
                                         @if($errors->has('name'))
                                            <small class="help-block form-text">{{ $errors->first('name') }}</small>
                                        @endif
                                    </div>
                                   
                                </div>
                                <div class="row form-group">
                                     <div class="col-12 col-md-12  d-flex">
                                            {!! Html::decode(Form::label('deactive_date', 'Deactive Date ', ['class' => 'form-control-label col-md-3'])) !!}
                                        {!! Form::text('deactive_date', old('deactive_date'), ['class' => 'form-control col-md-8 datepicker2', 'placeholder' => 'Deactive Date']) !!}

                                        @if($errors->has('name'))
                                            <small class="help-block form-text">{{ $errors->first('name') }}</small>
                                        @endif
                                    </div>
                                    
                                </div>

                          




                            <div class="form-actions form-group">
                                <a href="{{ url("category") }}"><span class="btn btn-secondary btn-xs"><i class="fa fa-backward"></i>  Back</span></a>
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