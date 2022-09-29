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

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <strong>Update</strong> User
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

                            {!! Form::model($data['editData'], array('route' => array('users.update', $data['editData']->id), 'method' => 'PUT', 'class'=>'form-horizontal', 'role'=>'form', 'name'=>'userUpdatePost', 'enctype'=>'multipart/form-data')) !!}

                                <div class="row form-group">
                                    <div class="col col-md-2">
                                        <label>Name <span class="mandatory-field">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-10">
                                        <input type="text" name="name" value="{{ $data['editData']->name }}" class="form-control" placeholder="Name" required>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col col-md-2">
                                        <label>Username</label>
                                    </div>
                                    <div class="col-12 col-md-10">
                                        <input type="text" value="{{ $data['editData']->user_name }}" class="form-control" readonly>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col col-md-2">
                                        <label>Email <span class="mandatory-field">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-10">
                                        <input type="email" name="email" value="{{ $data['editData']->email }}" class="form-control" placeholder="Email" required>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col col-md-2">
                                        <label>Phone Number <span class="mandatory-field">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-10">
                                        <input type="text" name="telephonenumber" value="{{ $data['editData']->telephonenumber }}" class="form-control" placeholder="Phone Number" required>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col col-md-2">
                                        <label>Designation <span class="mandatory-field">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-10">
                                        <input type="text" name="title" value="{{ $data['editData']->title }}" class="form-control" placeholder="Designation" required>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col col-md-2">
                                        <label>Department <span class="mandatory-field">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-10">
                                        <input type="text" name="department" value="{{ $data['editData']->department }}" class="form-control" placeholder="Department" required>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col col-md-2">
                                        <label>Company <span class="mandatory-field">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-10">
                                        <input type="text" name="company_name" value="{{ $data['editData']->company_name }}" class="form-control" placeholder="Company" required>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col col-md-2">
                                        <label>Role <span class="mandatory-field">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-10">
                                        <select id="user_type" name="user_type" required>
                                            <option value="0" @if($data['editData']->user_type == 0) {{ 'selected' }} @endif>User</option>
                                            <option value="1" @if($data['editData']->user_type == 1) {{ 'selected' }} @endif>Admin</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-actions form-group">
                                    <a href="{{ url("users") }}"><span class="btn btn-secondary btn-xs"><i class="fa fa-backward"></i>  Back</span></a>
                                    {!! Form::button('Update <i class="fa fa-forward"></i>', ['type' => 'submit', 'class' => 'btn btn-success btn-xs pull-right']) !!}
                                </div>
                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
