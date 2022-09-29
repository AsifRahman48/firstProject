
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
                            <strong class="card-title" style="line-height: 30px;"> Vacations List </strong>
                            <a class="pull-right" href="{{ url('vacations/create') }}"><button class="btn btn-success btn-sm"> <i class="fa fa-plus"></i> Set Vacation</button></a>
                        </div>

                        @if (session('success'))
                            <div class="sufee-alert alert with-close alert-success alert-dismissible fade show">
                                {{ session('success') }}
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
                                    <th class="serial">#</th>
                                    <th>Type</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Reason (if have)</th>
                                    <th style="width: 30% !important">Forward User</th>
                                    <th>Status</th>
                                    <th>Action</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data['vacations'] as $key => $value)
                                   <tr style="font-size: 14px; text-align: center;">
                                        <td class="serial">{{ $key + 1 }}</td>
                                        <td style="text-align: left">{{ $value->leaveType->name }}</td>
                                        <td>{{ date('d-M-Y', strtotime($value->from_date)) }}</td>
                                        <td>{{ date('d-M-Y', strtotime($value->to_date)) }}</td>
                                        <td>{{ $value->reason ?? 'Not Found' }}</td>
                                        <td>{{ $value->user->full_name}}</td>
                                        <td>{{ $value->status }}</td>
                                        <td>
                                            @if($value->status == "draft")
                                                <a href="{{route('vacations.edit', $value->id)}}" class="btn btn-success btn-sm">Edit</a>
                                            @endif
                                            {!! Form::open(['route' => ['vacations.destroy', $value->id], 'class' => 'd-inline']) !!}
                                                @method('delete')
                                                @csrf
                                                <button onclick="ConfirmDelete()" class="btn btn-danger btn-sm">Delete</button>
                                             {!! Form::close() !!}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <br>
                             {{ $data['vacations']->links() }}
                        </div>
                        </div>
                        <script>
                            function ConfirmDelete(){
                                return confirm('Are you sure want to delete?');
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
