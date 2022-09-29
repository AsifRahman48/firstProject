@extends('layouts.elaadmin')
@section('content')
    <style>
        .custom-tab-css {
            padding: 15px 30px;
        }

        .codes p {
            margin: 0 0 0 20px;
        }
    </style>

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
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">{{ $data['pageTitle'] }}</strong>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td>Date & Time</td>
                                    <td>{{ $data['auditLog']->created_at->format('M d, Y H:i:s A') }}
                                        ({{ $data['auditLog']->created_at->diffForHumans() }})
                                    </td>
                                </tr>
                                <tr>
                                    <td>Initiator Name</td>
                                    <td>{{ optional($data['auditLog']->causer)->name }}</td>
                                </tr>
                                <tr>
                                    <td>Activity</td>
                                    <td>{{ ucwords($data['auditLog']->menu_journey) }}</td>
                                </tr>
                                <tr>
                                    <td>Action</td>
                                    <td>{{ ucfirst($data['auditLog']->activity_name)." ". ucfirst($data['auditLog']->activity_type) }}</td>
                                </tr>
                                <tr>
                                    <td>Description</td>
                                    <td>{{ ucwords($data['auditLog']->description) }}</td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>Success</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="custom-tab">
                        <nav>
                            <div class="nav nav-tabs bg-white" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active custom-tab-css" data-toggle="tab"
                                   href="#custom-initiator-home" role="tab">Initiator Info</a>
                                <a class="nav-item nav-link custom-tab-css" data-toggle="tab"
                                   href="#custom-ip-home" role="tab">Initiator IP Info</a>
                            </div>
                        </nav>
                        <div class="tab-content pt-2" id="nav-tabContent">
                            <div class="tab-pane fade show active bg-white" id="custom-initiator-home" role="tabpanel">
                                <div class="codes mx-3 py-3">
                                    @if(!is_null($data['auditLog']->causer))
                                        <table class="table table-bordered">
                                            <tbody>
                                            @foreach($data['auditLog']->causer->toArray() as $key => $val)
                                                <tr>
                                                    <td>{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                                    <td>{{ $val ?? 'null' }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p class="text-center">No Data Found!</p>
                                    @endif
                                </div>
                            </div>

                            <div class="tab-pane fade bg-white" id="custom-ip-home" role="tabpanel">
                                <div class="codes mx-3 py-3">
                                    @if($data['ipInfo'] != false)
                                        <table class="table table-bordered">
                                            <tbody>
                                            @foreach($data['ipInfo'] as $key => $val)
                                                @if($key != 'driver' && !in_array($val, [null,'null']))
                                                    <tr>
                                                        <td>{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                                        <td>{{ $val ?? 'null' }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p class="text-center">No Data Found!</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
