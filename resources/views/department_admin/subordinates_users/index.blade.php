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
                    <div class="card">

                        <div class="card-header">
                            <strong class="card-title" style="line-height: 30px;">All Subordinate User listed in
                                here.</strong>
                        </div>

                        @if (session('status'))
                            <div
                                class="sufee-alert alert with-close alert-success alert-dismissible fade show no-margin">
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
                                <table>
                                    <tbody>
                                    <tr>
                                        <form action="">
                                            <td><input class="form-control input-sm" name="q" type="text"
                                                       value="{{ request('q') }}"></td>
                                            <td>
                                                <button style="margin-left: 10px;" class="btn btn-primary"
                                                        type="submit"><i class="fa fa-search"></i> Search
                                                </button>
                                            </td>
                                        </form>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <table class="table table-bordered table-striped">
                                <thead style="background-color: gray; color: #FFF">
                                <tr style="font-size: 12px; text-align: center;">
                                    <th class="serial">#</th>
                                    <th>Action</th>
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
                                @foreach($data['listData'] as $key => $value)
                                    <tr style="font-size: 12px; text-align: center;">
                                        <td class="serial">
                                            {{ $data['listData']->currentPage() <= 1 ? $key + 1 : ($data['listData']->perPage() * ($data['listData']->currentPage() - 1)) + ($key + 1) }}
                                        </td>
                                        <td>
                                            <a style="margin-right: 15px;" href="#">
                                                <button class="btn btn-secondary btn-sm subordinate-users-modal"
                                                        data-id="{{ $value->id }}" data-toggle="modal"
                                                        data-target="#addSubordinateUsers">
                                                    <i class="fa fa-users"></i> Add Subordinates
                                                </button>
                                            </a>
                                        </td>
                                        <td>{{ $value->name }}</td>
                                        <td>
                                            @if($value->user_type==0) User
                                            @elseif($value->user_type==1) Admin
                                            @elseif($value->user_type==2) Audit
                                            @else
                                                Department Admin
                                            @endif
                                        </td>
                                        <td>{{ $value->email }}</td>
                                        <td>{{ $value->title }}</td>
                                        <td>{{ $value->department }}</td>
                                        <td>{{ $value->telephonenumber }}</td>
                                        <td>{{ $value->company_name }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div>
                                {{ $data['listData']->appends(['q' => request('q')])->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--+++++++++++++++++++++++++++++  Add Subordinate Users Modal  +++++++++++++++++++++++++++++-->
    @include('department_admin.partial_views.subordinate_modal')


    <script>
        $(document).on("click", ".subordinate-users-modal", function () {
            $('#loader').show();
            let userId = $(this).data('id');
            $("#parentUserID").val(userId);

            let url = `{{ route('subordinate.users.list','') }}`;
            $.get(url + '/' + userId, [], function (data) {

                let subordinateSelect = $(".subordinate-select2");
                if(data.users.length > 0){
                    data.users.map(function (e){
                        subordinateSelect.append(new Option(e.name + ' -> ' + e.email, e.id, true, true)).trigger('change');
                    })
                } else {
                    subordinateSelect.val(null).trigger('change');
                }

                $('#loader').hide();
            });
        });
    </script>
@endsection
