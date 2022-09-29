@extends('layouts.elaadmin')

@section('content')

    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>Auto Import</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="{{ url('/') }}">Dashboard</a></li>
                                <li class="active">Auto Import</li>
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
                            <strong class="card-title" style="line-height: 30px;">Auto Scheduler Settings</strong>
                        </div>
                        <div id="alert-import"
                             class="d-none sufee-alert alert with-close alert-success alert-dismissible fade show no-margin">
                        </div>
                        <div class="card-body">
                            <form id="import-users" class="form form-horizontal" method="post">

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label>Scheduler Time</label>
                                        <input class="form-control mb-3" type="time" id="time" value="{{ \Carbon\Carbon::parse($data['scheduler']->time)->toTimeString() }}">

                                        <label>
                                            <input type="checkbox" id="is_disable"
                                                   {{ $data['scheduler']->is_disable == 1 ? 'checked' : '' }} value="1">
                                            Disable Scheduler</label>
                                    </div>
                                    <div class="col-md-6">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button class="btn btn-primary btn-lg">Update</button>
                                </div>
                            </form>
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
                            <strong class="card-title" style="line-height: 30px;">Imported Logs</strong>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table table-bordered table-striped table-hover bootstrap-data-table">
                                <thead>
                                <tr style="font-size: 12px; text-align: center;">
                                    <th class="serial">#</th>
                                    <!-- <th>Role</th> -->
                                    <th>Imported By</th>
                                    <th>Date</th>
                                    <th>Inserted Users</th>
                                    <th>Updated Users</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data['autos'] as $key => $auto)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $auto->imported_by }}</td>
                                        <td>{{ $auto->date }}</td>
                                        <td>{{ $auto->inserted_users }}</td>
                                        <td>{{ $auto->updated_users }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $data['autos']->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-none" id="import-loader">
        <div class="center-loader">
            <h1>Importing users...</h1>
            <img src="{{ asset('loading.gif') }}"/>
        </div>
    </div>
@endsection


@section('add_script')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        $('#import-users').on('submit', function (event) {
            event.preventDefault();

            let time = $('#time').val();
            let disable = $('#is_disable').is(":checked");

            $.ajax({
                method: "POST",
                data: {time: time, is_disable: (disable ? 1 : 0)},
                url: "{{ route('ldap.auto.post') }}",
                beforeSend: function () {
                    window.onbeforeunload = function () {
                        return "Dude, are you sure you want to leave?";
                    };
                    $('#import-loader').removeClass('d-none');
                },
                complete: function () {
                    window.onbeforeunload = null;
                    $('#import-loader').addClass('d-none');
                },
                success: function (response) {
                    $('#alert-import').removeClass('d-none').text('Successfully updated!');

                    /*setTimeout(function () {
                        window.location.reload();
                    }, 1000)*/
                }
            });
        })
    </script>
@endsection
