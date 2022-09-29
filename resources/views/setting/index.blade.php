@extends('layouts.elaadmin')

@section('stylesheets')
    <style>
        .danger {
            color: red;
        }
    </style>
@endsection

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
        <div class="card">
            <div class="card-header">
                <strong>Site Settings</strong>
            </div>

            <div class="card-body" height>
                @if(Session::has('message'))
                    <p class="alert alert-info">{{ Session::get('message') }}</p>
                @endif
                <form action="{{route('setting.store')}}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row form-group">
                        <div class="col-12 col-md-6 mb-3">
                            <label>Site Title: <span class="danger">*</span></label>
                            <input type="text" class="form-control" name="site_title"
                                   placeholder="e.g. Brain Station 23" value="{{$data['setting']->site_title ?? ''}}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label>Copyright: <span class="danger">*</span></label>
                            <input name="copyright_text" type="text" class="form-control"
                                   placeholder="e.g. Copyright ©2022 BS23" value="{{$data['setting']->footer_copyright ?? ''}}" required>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-12 col-md-6">
                            <label>User manual(Admin):</label>
                            <input type="file" accept="application/pdf" name="admin_manual"
                                   class="form-control-file">
                            <small class="form-control-feedback">Only pdf files are allowed</small>
                            @if (isset($data['setting']->user_manual_admin))
                                <br><a class="text-primary" target="_blank" href="{{$data['setting']->user_manual_admin}}">user_manual(admin).pdf</a>
                            @endif
                            @if ($errors->has('admin_manual'))
                                <p class="mt-3 alert alert-danger">{{ $errors->first('admin_manual') }}</p>
                            @endif
                        </div>

                        <div class="col-12 col-md-6">
                            <label>User manual(User):</label>
                            <input type="file" accept="application/pdf" name="user_manual"
                                   class="form-control-file">
                            <small class="form-control-feedback">Only pdf files are allowed</small>
                            @if (isset($data['setting']->user_manual_user))
                                <br><a class="text-primary" target="_blank" href="{{$data['setting']->user_manual_user}}">user_manual(user).pdf</a>
                            @endif
                            @if ($errors->has('user_manual'))
                                <p class="mt-3 alert alert-danger">{{ $errors->first('user_manual') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group mt-3">
                        <div class="col-12 col-md-6 mb-3">
                            <label>Site Logo:</label>
                            <input type="file" accept="image/jpeg, image/png, image/jpg" name="logo"
                                   class="form-control-file">
                            <small class="form-control-feedback">Only jpg, jpeg, png formats are allowed. Please try to
                                attach your file below 2 MB and Dimensions( W:320px * H:120px )</small>
                            @if (isset($data['setting']->logo))
                                <img src="{{asset('storage/'.$data['setting']->logo)}}" alt="logo" height="300">
                            @endif
                            @if ($errors->has('logo'))
                                <p class="mt-3 alert alert-danger">{{ $errors->first('logo') }}</p>
                            @endif
                        </div>

                        <div class="col-12 col-md-6">
                            <label>Site Icon:</label>
                            <input type="file" accept="image/jpeg, image/png, image/jpg" name="favicon"
                                   class="form-control-file">
                            <small class="form-control-feedback">Only jpg, jpeg, png formats are allowed. Please try to
                                attach your file below 1 MB and Dimensions( W:48px * H:48px )</small>
                            @if (isset($data['setting']->icon))
                                <img src="{{asset('storage/'.$data['setting']->icon)}}" alt="logo" height="100">
                            @endif
                            @if ($errors->has('favicon'))
                                <p class="mt-3 alert alert-danger">{{ $errors->first('favicon') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="text-center mt-md-5">
                        <button class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
