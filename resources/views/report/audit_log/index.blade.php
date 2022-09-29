@extends('layouts.elaadmin')

@section('stylesheets')
    <link href="{{ asset('select2/select2.min.css')}}" rel="stylesheet"/>
    <script src="{{ asset('select2/select2.min.js')}}"></script>
@endsection

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
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">{{ $data['pageTitle'] }}</strong>
                        </div>
                        <div class="card-body">
                            <form id="searchData" novalidate="novalidate">

                                <div class="row" style="margin-bottom: 10px;">
                                    <div class="col-md-6 ">
                                        <div class="form-group d-flex">
                                            {!! Html::decode(Form::label('Start Date', 'Start Date', ['class' => 'form-control-label col-md-4'])) !!}
                                            <input id="start_date" name="start_date" type="text"
                                                   class="form-control datepicker" autocomplete="off"
                                                   placeholder="Start Date">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group d-flex">
                                            {!! Html::decode(Form::label('End Date', 'End Date', ['class' => 'form-control-label col-md-4'])) !!}
                                            <input id="end_date" name="end_date" type="text"
                                                   class="form-control datepicker" autocomplete="off"
                                                   placeholder="End Date">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group d-flex">
                                            {!! Html::decode(Form::label('Search by Activity', 'Search by Activity', ['class' => 'form-control-label col-md-4'])) !!}
                                            <select name="action_type" class="form-control">
                                                <option value="">Search by Activity</option>
                                                @foreach($data['actionTypes'] as $val)
                                                    <option value="{{ $val }}">{{ ucwords($val) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group d-flex">
                                            {!! Html::decode(Form::label('Search By User', 'Search By User', ['class' => 'form-control-label col-md-4'])) !!}
                                            <select id="user_id" name="user_id" class="form-control select2" required>
                                                <option value="">Search User</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="col-md-2 pull-right">
                                            <button type="submit" class="btn btn-md btn-info">
                                                <i class="fa fa-search fa-lg"></i>
                                                <span id="payment-button-amount">Search</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title" style="line-height: 30px;">{{ $data['pageTitle'] }} listed in
                                here.</strong>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-striped table-hover mt-12">
                                <thead>
                                <tr style="font-size: 12px; text-align: center;">
                                    <th class="serial">Sl.no</th>
                                    <th>Date & Time</th>
                                    <th>Initiator Name</th>
                                    <th>IP</th>
                                    <th>Activity</th>
                                    <th>Action</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($data['auditLogs']) > 0)
                                @foreach($data['auditLogs'] as $index => $log)
                                    <tr>
                                        <td>{{ $data['auditLogs']->currentPage() <= 1 ? $index + 1 : ($data['auditLogs']->perPage() * ($data['auditLogs']->currentPage() - 1)) + ($index + 1) }}</td>
                                        <td>
                                            {{ $log->created_at->format('M d, Y H:i:s A') }}
                                            ({{ $log->created_at->diffForHumans() }})
                                        </td>
                                        <td>{{ optional($log->causer)->name }}</td>
                                        <td>{{ $log->ip }}</td>
                                        <td>{{ ucwords($log->menu_journey) }}</td>
                                        <td>{{ ucfirst($log->activity_name)." ". ucfirst($log->activity_type) }}</td>
                                        <td>Success</td>
                                        <td>
                                            <a style="margin-right: 15px;"
                                               href="{{ route('auditLogs.show', $log->id) }}">
                                                <button class="btn btn-primary btn-sm"><i class="fa fa-eye p-1"></i>Details
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                @else
                                    <tr>
                                        <td colspan="12">
                                            <p class="text-center mt-3">No Data Found!</p>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>

                            <div class="row">
                                <div class="col-md-6">
                                    <p>Showing {{ $data['auditLogs']->currentPage() ?? 0 }}
                                        to {{ $data['auditLogs']->lastPage() ?? 0 }}
                                        of {{ $data['auditLogs']->total() ?? 0 }} entries</p>
                                </div>

                                <div class="col-md-6">
                                    <div class="float-right">
                                        {{ $data['auditLogs']->appends(request()->all())->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('add_script')
    <script>
        var $datepicker = $('.datepicker');
        $datepicker.datepicker({
            dateFormat: 'dd-mm-yy', changeMonth: true,
            changeYear: true
        });

        $('.datepicker2').datepicker({dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true}).val();

        $(".select2").select2({
            minimumInputLength: 2,
            ajax: {
                url: "{{ URL::to('/search_ad_user')}}",
                type: "POST",
                dataType: 'json',
                delay: 10,
                data: function (params) {
                    return {
                        term: params.term,
                        '_token': $('meta[name="csrf-token"]').attr('content')
                    };
                },
                processResults: function (data) {
                    var results = [];
                    $.each(data, function (index, account) {
                        var nameInfo = account.name + '->' + account.title + '->' + account.department + '->' + account.email + '->' + account.company_name;
                        if (account.id != "{{ auth()->id() }}") {
                            results.push({
                                id: account.id,
                                text: nameInfo
                            });
                        }
                    });

                    return {
                        results: results
                    };
                }
            }
        });
    </script>
@endsection
