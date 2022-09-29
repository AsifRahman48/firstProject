
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

                    @include('archive.report.search_form') 
                    <div class="card">

                        <div class="card-header">
                            <strong class="card-title" style="line-height: 30px;">{{ $data['pageTitle'] }} listed in here.</strong>
                            <!-- <a class="pull-right" href="{{ url('/request/new') }}"><button class="btn btn-success btn-sm"> <i class="fa fa-plus"></i> Add a new request</button></a> -->
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

                        <div class="table-responsive" id="reportView" style="padding-top: 15px;">
                            <br>
                            <br>
                         <h1 class="text-center" > Search Result </h1>
                         <br>
                            <br>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection