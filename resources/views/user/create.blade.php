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
                                <li><a href="{{ url("users") }}">User List</a></li>
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
                            <strong>Add</strong> User
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

                            {!! Form::open(['url'=>'users', 'class'=>'form-horizontal', 'role'=>'form', 'name'=>'userAddPost', 'enctype'=>'multipart/form-data']) !!}


                            <div class="row form-group">
                                <div class="col col-md-2">
                                    {!! Html::decode(Form::label('name', 'Name <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                                </div>
                                <div class="col-12 col-md-10">
                                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required']) !!}

                                    @if($errors->has('name'))
                                        <small class="help-block form-text">{{ $errors->first('name') }}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-2">
                                    {!! Html::decode(Form::label('user_name', 'Username <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                                </div>
                                <div class="col-12 col-md-10">
                                    {!! Form::text('user_name', old('user_name'), ['class' => 'form-control', 'placeholder' => 'e.g. BS***', 'required' => 'required', 'autocomplete' => 'off']) !!}

                                    @if($errors->has('user_name'))
                                        <small
                                            class="help-block form-text">{{ $errors->first('user_name') }}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-2">
                                    {!! Html::decode(Form::label('email', 'Email <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                                </div>
                                <div class="col-12 col-md-10">
                                    {!! Form::email('email', old('email'), ['class' => 'form-control', 'placeholder' => 'Email', 'required' => 'required', 'autocomplete' => 'off']) !!}

                                    @if($errors->has('email'))
                                        <small class="help-block form-text">{{ $errors->first('email') }}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-2">
                                    {!! Html::decode(Form::label('telephonenumber', 'Phone Number <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                                </div>
                                <div class="col-12 col-md-10">
                                    {!! Form::text('telephonenumber', old('telephonenumber'), ['class' => 'form-control', 'placeholder' => 'e.g. +88016********', 'required' => 'required', 'autocomplete' => 'off']) !!}

                                    @if($errors->has('telephonenumber'))
                                        <small
                                            class="help-block form-text">{{ $errors->first('telephonenumber') }}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-2">
                                    {!! Html::decode(Form::label('title', 'Designation <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                                </div>
                                <div class="col-12 col-md-10">
                                    {!! Form::text('title', old('title'), ['class' => 'form-control', 'placeholder' => 'Designation', 'required' => 'required', 'autocomplete' => 'off']) !!}

                                    @if($errors->has('title'))
                                        <small class="help-block form-text">{{ $errors->first('title') }}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-2">
                                    {!! Html::decode(Form::label('department', 'Department <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                                </div>
                                <div class="col-12 col-md-10">
                                    {!! Form::text('department', old('department'), ['class' => 'form-control', 'placeholder' => 'e.g. Development', 'required' => 'required', 'autocomplete' => 'off']) !!}

                                    @if($errors->has('department'))
                                        <small
                                            class="help-block form-text">{{ $errors->first('department') }}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-2">
                                    {!! Html::decode(Form::label('company_name', 'Company <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                                </div>
                                <div class="col-12 col-md-10">
                                    {!! Form::text('company_name', old('company_name'), ['class' => 'form-control', 'placeholder' => 'e.g. BS23', 'required' => 'required', 'autocomplete' => 'off']) !!}

                                    @if($errors->has('company_name'))
                                        <small
                                            class="help-block form-text">{{ $errors->first('company_name') }}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-2">
                                    {!! Html::decode(Form::label('user_type', 'Role <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                                </div>
                                <div class="col-12 col-md-10">
                                    {!! Form::select('user_type', [0 => "User", 1 => "Admin"], ['class' => 'form-control', 'required' => 'required']) !!}

                                    @if($errors->has('user_type'))
                                        <small class="help-block form-text">{{ $errors->first('user_type') }}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="form-actions form-group">
                                <a href="{{ url("users") }}"><span class="btn btn-secondary btn-xs"><i
                                            class="fa fa-backward"></i>  Back</span></a>
                                {!! Form::button('Submit <i class="fa fa-forward"></i>', ['type' => 'submit', 'class' => 'btn btn-success btn-xs pull-right']) !!}
                            </div>
                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
