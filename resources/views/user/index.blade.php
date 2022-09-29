@extends('layouts.elaadmin')
@section('content')
    <link rel="stylesheet" href="{{ asset('ElaAdmin/assets/css/lib/datatable/dataTables.bootstrap.min.css') }}">
    <script src="{{ asset('ElaAdmin/assets/js/lib/data-table/datatables.min.js') }}"></script>
    <script src="{{ asset('ElaAdmin/assets/js/lib/data-table/dataTables.bootstrap.min.js') }}"></script>
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
                    @if(config('custom.settings.authentication') == 'database')
                        <div class="mb-4 text-right text-white">
                            <a href="{{ route('users.create') }}" class="btn btn-success">Add New User</a>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title" style="line-height: 30px;">All User listed in here.</strong>
                        </div>

                        @if (session('status'))
                            <div class="sufee-alert alert with-close alert-success alert-dismissible fade show no-margin">
                                <span class="badge badge-pill badge-success">Success</span>
                                {{ session('status') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show">
                                <span class="badge badge-pill badge-danger">Danger</span>
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                        @endif

                        <div class="table-responsive" style="padding: 20px;">
                            <div style="float: right; margin-bottom: 20px;">

                                {!! Form::open(['url'=>'users', 'method'=>'get', 'class'=>'form-horizontal', 'role'=>'form', 'name'=>'userSearchPost']) !!}
                                <table>
                                    <tbody>
                                    <tr>
                                        @php
                                            if(isset($data['searchTerm']) && ($data['searchTerm'] != '') && ($data['searchTerm'] != null)){
                                               $searchValue = $data['searchTerm'];

                                            }else{
                                                $searchValue = "";
                                            }
                                        @endphp
                                        <td>
                                            <select name="role" class="form-control">
                                                <option value="">Select Type</option>
                                                <option value="4" @if(request('role') == 4) {{ 'selected' }} @endif>User</option>
                                                <option value="1" @if(request('role') == 1) {{ 'selected' }} @endif>Admin</option>
                                                <option value="2" @if(request('role') == 2) {{ 'selected' }} @endif>Audit</option>
                                                <option value="3" @if(request('role') == 3) {{ 'selected' }} @endif>Department Admin</option>
                                            </select>
                                        </td>
                                        <td><input class="form-control input-sm" name="searchTerm" type="text" value="{{ $searchValue }}"></td>
                                        <td><button style="margin-left: 10px;" class="btn btn-primary" type="submit" ><i class="fa fa-search"></i> Search</button></td>
                                    </tr>
                                    </tbody>
                                </table>
                                {!! Form::close() !!}

                            </div>
                            <table class="table table-bordered table-striped">
                                <thead style="background-color: gray; color: #FFF">
                                <tr style="font-size: 12px; text-align: center;">
                                    <th class="serial">#</th>
                                    <th>Action</th>
                                    <th>Is Active</th>
                                    <th>Full Name</th>
                                    <th>Type</th>
                                    <th>Email</th>
                                    <th>Designation</th>
                                    <th>Department</th>
                                    <th>Phone No</th>
                                    <th>Company</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                if(isset($_GET['page'])){
                                    $page=$_GET['page'];
                                    if($page==1){
                                        $sn = 1;

                                    }else{
                                        $pages=$page*10-10+1;
                                        $sn=$pages;
                                    }

                                }else{
                                    $sn = 1;
                                }
                                @endphp
                                @foreach($data['listData'] as $key => $value)
                                    <tr style="font-size: 12px; text-align: center;">
                                        <td class="serial">{{ $sn }}.</td>
                                        <td>
                                            @if($value->user_type==0)
                                                <a style="margin-right: 15px;" href="{{ url('users/makeadmin/'.$value->id.'/1') }}">
                                                    <button class="btn btn-primary btn-sm"><i class="fa fa-users"></i> Make Admin</button>
                                                </a>
                                                <a style="margin-right: 15px;" href="#">
                                                    <button class="btn btn-info btn-sm open-auditSelectCompany" data-id="{{ $value->id }}" data-toggle="modal" data-target="#auditSelectCompany"><i class="fa fa-users"></i> Make Audit</button>
                                                </a>
                                                <a style="margin-right: 15px;" href="#">
                                                        <button class="btn btn-secondary btn-sm open-Category" data-id="{{ $value->id }}" data-toggle="modal" data-target="#categoryAdmin"><i class="fa fa-users"></i> Make DA</button>
                                                    </a>
                                            @elseif($value->user_type==1)
                                                <a style="margin-right: 15px;" href="{{ url('users/makeadmin/'.$value->id.'/0') }}">
                                                    <button class="btn btn-success btn-sm"><i class="fa fa-users"></i> Make User</button>
                                                </a>
                                                <a style="margin-right: 15px;" href="#">
                                                    <button class="btn btn-info btn-sm open-auditSelectCompany" data-id="{{ $value->id }}" data-toggle="modal" data-target="#auditSelectCompany"><i class="fa fa-users"></i> Make Audit</button>
                                                </a>
                                                <a style="margin-right: 15px;" href="#">
                                                    <button class="btn btn-secondary btn-sm open-Category" data-id="{{ $value->id }}" data-toggle="modal" data-target="#categoryAdmin"><i class="fa fa-users"></i> Make DA</button>
                                                </a>
                                            @elseif($value->user_type==2)
                                                <a style="margin-right: 15px;" href="{{ url('users/makeadmin/'.$value->id.'/0') }}">
                                                    <button class="btn btn-success btn-sm"><i class="fa fa-users"></i> Make User</button>
                                                </a>
                                                <a style="margin-right: 15px;" href="{{ url('users/makeadmin/'.$value->id.'/1') }}">
                                                    <button class="btn btn-primary btn-sm"><i class="fa fa-users"></i> Make Admin</button>
                                                </a>
                                                <a style="margin-right: 15px;" href="#">
                                                    <button class="btn btn-secondary btn-sm open-Category" data-id="{{ $value->id }}" data-toggle="modal" data-target="#categoryAdmin"><i class="fa fa-users"></i> Make DA</button>
                                                </a>
                                                <a style="margin-right: 15px;" href="#" class="open-com-modal">
                                                    <button class="btn btn-info btn-sm open-auditSelectCompany" data-id="{{ $value->id }}" data-toggle="modal" data-target="#auditSelectCompany"><i class="fa fa-edit"></i> Edit</button>
                                                </a>
                                            @else
                                                <a style="margin-right: 15px;" href="{{ url('users/makeadmin/'.$value->id.'/0') }}">
                                                    <button class="btn btn-success btn-sm"><i class="fa fa-users"></i> Make User</button>
                                                </a>
                                                <a style="margin-right: 15px;" href="{{ url('users/makeadmin/'.$value->id.'/1') }}">
                                                    <button class="btn btn-primary btn-sm"><i class="fa fa-users"></i> Make Admin</button>
                                                </a>
                                                <a style="margin-right: 15px;" href="#">
                                                    <button class="btn btn-info btn-sm open-auditSelectCompany" data-id="{{ $value->id }}" data-toggle="modal" data-target="#auditSelectCompany"><i class="fa fa-users"></i> Make Audit</button>
                                                </a>
                                                <a style="margin-right: 15px;" href="#" class="open-cat-modal">
                                                    <button class="btn btn-secondary btn-sm open-Category" data-id="{{ $value->id }}" data-toggle="modal" data-target="#categoryAdmin"><i class="fa fa-edit"></i> Edit</button>
                                                </a>
                                            @endif

                                                <a href="{{ route('users.edit', $value->id) }}" class="btn btn-dark btn-sm mt-1"><i class="fa fa-edit"></i> Edit User Info</a>
                                        </td>
                                        <td>
                                            @if(auth()->id() != $value->id)
                                                @if($value->is_active)
                                                    <form method="POST" action="{{ route('users.update', $value->id) }}">
                                                        @csrf @method("PATCH")
                                                        <button onclick="return confirm('Are you sure to deactivate this user?')" class="btn btn-danger btn-sm" name="is_active" value="0">
                                                            <i class="fa fa-info-circle"></i> Deactivate
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('users.update', $value->id) }}">
                                                        @csrf @method("PATCH")
                                                        <button onclick="return confirm('Are you sure to activate this user?')" class="btn btn-success btn-sm" name="is_active" value="1">
                                                            <i class="fa fa-info-circle"></i> Activate
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <span class="badge badge-success">Not eligible</span>
                                            @endif
                                        </td>
                                        <td><span class="name">{{ $value->name }}</span></td>
                                        <td>
                                            @if($value->user_type==0) User
                                            @elseif($value->user_type==1) Admin
                                            @elseif($value->user_type==2) Audit
                                            @else
                                            Department Admin
                                            @endif
                                        </td>
                                        <td><span class="name">{{ $value->email }}</span></td>
                                        <td><span class="name">{{ $value->title }}</span></td>
                                        <td><span class="name">{{ $value->department }}</span></td>
                                        <td><span class="name">{{ $value->telephonenumber }}</span></td>
                                        <td>{{ $value->company_name }}</td>
                                    </tr>
                                    @php
                                        $sn++;
                                    @endphp
                                @endforeach
                                </tbody>
                            </table>
                            <div>
                                {{ $data['listData']->appends(request()->all())->links() }}
                            </div>
                        </div>

                        <!-- Modal -->
                        @include('modal.request_audit_company_modal')
                        @include('modal.category_modal')

                        <script>
                            function ConfirmDelete(){
                                return confirm('Are you sure?');
                            }

                            $(document).on('click', '.open-cat-modal', function() {
                                let userId = $(this).find('.open-Category').attr('data-id');
                                let data = { userId: userId }
                                let url = `{{ url('user_department_list') }}`;

                                $.get(url + '/' + userId, data, function (data) {
                                    $('.category-select2').val(data).trigger('change');
                                });
                            })

                            $(document).on('click', '.open-com-modal', function() {
                                let userId = $(this).find('.open-auditSelectCompany').attr('data-id');
                                let data = { userId: userId }
                                let url = `{{ url('user_company_list') }}`;

                                $.get(url + '/' + userId, data, function (data) {
                                    console.log(data);
                                    $('.audit-company-select2').val(data).trigger('change');
                                });
                            })


                            /*
                            $(document).ready(function(){
                                $('.bootstrap-data-table').DataTable();
                            });
                            */

                            $(document).on("click", ".open-auditSelectCompany", function () {
                                var userId = $(this).data('id');
                                $("#auditSelectCompany #auditCompanyUserID").val(userId);

                                $.ajax({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    async: false,
                                    url: "{{ URL::to('/user_company_list')}}",
                                    type: "POST",
                                    data:  {"userId":userId},
                                    beforeSend: function(){
                                        $('#loader').show();
                                    },
                                    success: function(data){
                                        $(".audit-company-select2").select2("val", data);
                                        $('#loader').hide();
                                    }
                                });
                            });

                            //for category admin
                            $(document).on("click", ".open-Category", function () {
                                var userId = $(this).data('id');
                                $("#categoryAdmin #categoryUserID").val(userId);

                                $.ajax({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    async: false,
                                    url: "{{ URL::to('/category_list')}}",
                                    type: "POST",
                                    data:  {"userId":userId},
                                    beforeSend: function(){
                                        $('#loader').show();
                                    },
                                    success: function(data){
                                        $(".category-select2").select2("val", data);
                                        $('#loader').hide();
                                    }
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
