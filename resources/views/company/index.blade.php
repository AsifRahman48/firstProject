
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
                            <strong class="card-title" style="line-height: 30px;">All Company listed in here.</strong>
                            <a class="pull-right" href="{{ url('company/create') }}"><button class="btn btn-success btn-sm"> <i class="fa fa-plus"></i> Add Company</button></a>
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
                    <div class="card-body">
                        <div class="table-responsive">
                            <!-- <br> -->
                            <table class="table table table-bordered table-striped table-hover">
                                <thead style="background-color: gray; color: #FFF">
                                <tr style="font-size: 12px; text-align: center;">
                                    <!-- <th class="serial">#</th> -->
                                       <th>Action</th>
                                    <th>Company Name</th>
                                    <th>Short Name</th>
                                    <th>Logo</th>
                                    <th>Active Date</th>
                                    <th>Deactive Date</th>

                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $sn = 1;
                                @endphp
                                @foreach($data['listData'] as $key => $value)
                                   <tr style="font-size: 14px; text-align: center;">
                                        <!-- <td class="serial">#.</td> -->
                                          <td>
                                            <a style="margin-right: 15px;" href="{{ url('company/edit/'.$value->id) }}">
                                                <button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o"></i> Edit</button>
                                            </a>
                                        </td>
                                        <td style="text-align: left">{{ $value->name }}</td>
                                        <td>{{ $value->short_name }}</td>
                                        <td><img src="{{ asset('upload/company')}}/{{$value->logo}}" style="height: 80; width: 120px;"></td>
                                        <td>{{ date('d-M-Y', strtotime($value->active_date)) }}</td>
                                        <td>
                                            @if(!empty($value->deactive_date))
                                                   {{ date('d-M-Y', strtotime($value->deactive_date)) }}
                                            @endif
                                        </td>

                                    </tr>
                                    @php
                                        $sn++;
                                    @endphp
                                @endforeach
                                </tbody>
                            </table>
                            <br>
                            {{ $data['listData']->links() }}
                        </div>
                        </div>
                        <script>
                            function ConfirmDelete(){
                                return confirm('Are you sure?');
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">

$(document).ready(function(){

 $('.hideDiv').hide();
  $('.bootstrap-data-table').DataTable();
});

    </script>
@endsection
