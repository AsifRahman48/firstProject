@extends('layouts.elaadmin')
@section('content')
    <div class="row m-0">
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
            <div class="row">
                <div class="col-md-12 col-sm-12 col-lg-12">
                    @include('department_admin.partial_views.search', ['subordinate_users_list' => $data['userList']])
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title" style="line-height: 30px;">{{ $data['pageTitle'] }} listed in here.</strong>
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
                        </div>
                        <div class="card-body">
                            <div id="reportView" style="padding-top: 15px;">
                                @include('report.report_view_table', ['result' => $data['tickets'], 'statusResult' => $data['ticketStatus']])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
