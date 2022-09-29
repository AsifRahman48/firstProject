@extends('layouts.elaadmin')
@push('page-css')
    <style>
        .historyDiv {
            height: 50px !important;
            overflow: hidden;
            width: 250px !important;
            -webkit-transition: all 0.5s ease;
            -moz-transition: all 0.5s ease;
            -o-transition: all 0.5s ease;
            transition: all 0.5s ease;
            max-height: 300px;
        }

        .historyDivView {
            /*width: 300px;*/
            min-height: 100px !important;
            -webkit-transition: all 0.5s ease;
            -moz-transition: all 0.5s ease;
            -o-transition: all 0.5s ease;
            transition: all 0.5s ease;
            max-height: 300px;
            overflow-x: auto;
        }

    </style>
@endpush
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
                            <!-- <h1>{{ $data['pageTitle'] }}</h1> -->
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
                            <strong class="card-title" style="line-height: 30px;">All {{ $data['pageTitle'] }} listed in
                                here.</strong>
                        </div>

                        @if (session('success'))
                            <div class="sufee-alert alert with-close alert-success alert-dismissible fade show no-margin">
                                <span class="badge badge-pill badge-success">Success</span>
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

                        <div class="row" style="margin-top:30px;">
                            <div class="col-md-10 offset-md-2 d-flex">
                                <form action="{{route('get_manage_tickets')}}" method="get" class="w-100">
                                <div class="col-md-8 d-flex" style="padding-right: 0px;">
                                    <input name="searchValue" class="form-control" placeholder="Reference No / Ticket ID" value=""/>
                                    <button class="btn btn-success btn-sm ml-1">Search</button>
                                </div>
                            </form>
                            </div>
                           
                        </div>

                        <div class="table-responsive" style="margin-top:30px;">
                            <table class="table table-bordered table-striped table-hover bootstrap-data-table">
                                <thead>
                                    <tr style="font-size: 14px; text-align: center;">
                                        <th class="serial">#</th>
                                        <th>Action</th>
                                        <th>Is Viewed?</th>
                                        <th>Reference Number</th>
                                        <th style="max-width: 150px;">Subject</th>
                                        <th>Department</th>
                                        <th>Unit/Section</th>
                                        <th>Initiated By</th>
                                        <th>Status</th>
                                        <th>Initiation Date</th>
                                        <th>History</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $sn = 1;
                                    @endphp
                                    @foreach ($data['tickets'] as $key => $value)
                                        <tr style="font-size: 12px; text-align: center;">
                                            <td class="serial">{{ $sn }}.</td>
                                            <td>
                                                <a href="{{ route('manage_ticket_view', $value->id) }}">
                                                    <button class="btn btn-primary btn-sm"><i class="fa fa-info-circle"></i>
                                                        View</button>
                                                </a>
                                                @if ($value->tStatus == 2)
                                                    <a href="{{ route('manage_ticket_edit', $value->id) }}">
                                                        <button class="btn btn-info btn-sm mt-1"><i
                                                                class="fa fa-info-circle"></i>
                                                            Edit</button>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($value->tStatus == 5)
                                                    {!! Form::open(['url' => ['update_view_status', $value->id]]) !!}
                                                    @method('put')
                                                    @csrf
                                                    @if ($value->is_viewed == 0)
                                                        <button
                                                        onclick="return confirm('Are you sure to hide this ticket from user panel?')"
                                                        class="btn btn-success btn-sm" name="is_viewed" value="1"><i
                                                            class="fa fa-info-circle"></i>
                                                        Yes</button>
                                                    @else
                                                            <button
                                                            onclick="return confirm('Are you sure to show this ticket in user panel?')"
                                                            class="btn btn-success btn-sm" name="is_viewed" value="0"><i
                                                                class="fa fa-info-circle"></i>
                                                            No</button>
                                                    @endif
                                                    {!! Form::close() !!}
                                                @else
                                                    <span class="badge badge-success">Not eligible</span>
                                                @endif
                                            </td>
                                            <td><span class="name">{{ $value->tReference_no }}</span></td>
                                            <td style="max-width: 150px;"><span
                                                    class="name">{{ substr($value->tSubject, 0, 100) }}</span></td>
                                            <td><span class="name">{{ $value->category->name }}</span></td>
                                            <td><span class="name">{{ $value->sub_category->name }}</span></td>
                                            <td><span
                                                    class="name">{{ $value->user ? $value->user->username : 'Name Not Found' }}</span>
                                            </td>
                                            <td>
                                                @if ($value->now_ticket_at == $value->initiator_id && $value->tStatus == 4)
                                                    Approved
                                                @else
                                                    {{ $statusList[$value->tStatus] }}
                                                @endif
                                            </td>
                                            <td><span
                                                    class="name">{{ date('d-M-Y', strtotime($value->created_at)) }}</span>
                                            </td>
                                            <td id="{{ $sn }}">
                                                <div id="div{{ $sn }}" class="historyDiv">

                                                    @if (!empty($value->thistory))
                                                        @php
                                                            $hasan = json_decode($value->thistory, true);
                                                        @endphp
                                                        <ul>
                                                            @foreach ($hasan as $key => $HistoryInfo)

                                                                <li>{{ $key + 1 }} =>
                                                                    {{ $HistoryInfo['user_name'] }}-{{ $HistoryInfo['user_type'] }}-{{ $HistoryInfo['user_status'] }}
                                                                </li>
                                                            @endforeach

                                                        </ul>
                                                    @endif
                                                </div>
                                                @if (!empty($value->thistory))
                                                    <button id="btn{{ $sn }}" class="fa fa-eye text-success"
                                                        aria-hidden="true"
                                                        onclick="historyFullView({{ $sn }})"></button>
                                                    <button id="btnS{{ $sn }}"
                                                        class="fa fa-eye-slash text-danger hideDiv" aria-hidden="true"
                                                        onclick="historyFullViewClose({{ $sn }})"></button>
                                                @endif

                                            </td>

                                        </tr>
                                        @php
                                            $sn++;
                                        @endphp
                                    @endforeach
                                </tbody>
                            </table>
                            <div style="float: right">
                                {{ $data['tickets']->links() }}
                            </div>
                            <br>
                            <br>
                            <br>
                        </div>
                        <script>
                            function ConfirmDelete() {
                                return confirm('Are you sure?');
                            }

                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function historyFullView(divId) {
            $('#div' + divId).removeClass('historyDiv');
            $('#div' + divId).addClass('historyDivView', 100);
            $('#btn' + divId).hide();
            $('#btnS' + divId).show();

        }

        function historyFullViewClose(divId) {
            $('#div' + divId).removeClass('historyDivView');
            $('#div' + divId).addClass('historyDiv', 100);
            $('#btn' + divId).show();
            $('#btnS' + divId).hide();

        }
        // $(document).ready(function() {

        //     $('.hideDiv').hide();
        //     $('.bootstrap-data-table').DataTable({
        //         "paging" : false
        //     });
        // });

    </script>
@endsection
