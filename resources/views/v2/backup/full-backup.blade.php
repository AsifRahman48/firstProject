@extends('layouts.elaadmin')

@section('content')
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>Full Backup</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="{{ url('/') }}">Dashboard</a></li>
                                <li class="active">Full Backup</li>
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
                            <form id="form-submit" class="form form-horizontal" method="post">

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label>Everyday</label>
                                        <input class="form-control mb-3" type="time" id="time" value="{{ \Carbon\Carbon::parse($data['scheduler']->time)->toTimeString() }}">

                                        <label class="d-block">
                                            <input type="checkbox" id="is_disable"
                                                   {{ $data['scheduler']->is_disable == 1 ? 'checked' : '' }} value="1">
                                            Disable Scheduler
                                        </label>

                                        <label>
                                            <input type="checkbox" id="is_delete"
                                                   {{ $data['scheduler']->is_delete == 1 ? 'checked' : '' }} value="1">
                                            Auto Delete
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="auto-delete" class="mb-3 d-none">
                                            <label>Select Auto Delete Day</label>
                                            <select class="form-control" name="delete_after_days" id="delete_after_days">
                                                <option value="15" {{ $data['scheduler']->delete_after_days == 15 ? 'selected' : '' }}>15 days</option>
                                                <option value="30" {{ $data['scheduler']->delete_after_days == 30 ? 'selected' : '' }}>30 days</option>
                                                <option value="40" {{ $data['scheduler']->delete_after_days == 40 ? 'selected' : '' }}>40 days</option>
                                                <option value="50" {{ $data['scheduler']->delete_after_days == 50 ? 'selected' : '' }}>50 days</option>
                                                <option value="60" {{ $data['scheduler']->delete_after_days == 60 ? 'selected' : '' }}>60 days</option>
                                                <option value="70" {{ $data['scheduler']->delete_after_days == 70 ? 'selected' : '' }}>70 days</option>
                                                <option value="80" {{ $data['scheduler']->delete_after_days == 80 ? 'selected' : '' }}>80 days</option>
                                            </select>
                                        </div>
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
                            <strong class="card-title" style="line-height: 30px;">Backups History</strong>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table table-bordered table-striped table-hover bootstrap-data-table">
                                <thead>
                                <tr style="font-size: 12px; text-align: center;">
                                    <th class="serial">#</th>
                                    <th>Name</th>
                                    <th>Size</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data['backups'] as $key => $backup)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $backup->name }}</td>
                                        <td>{{ $backup->size }}</td>
                                        <td>
                                            <a href="{{ url('/') }}/storage/full_backup/{{ $backup->path }}" class="btn btn-sm btn-info"><i class="fa fa-cloud-download" aria-hidden="true"></i> Download</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $data['backups']->links() }}
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

        $(document).ready(function () {
            if($('#is_delete').is(":checked")) {
                $('#auto-delete').removeClass('d-none');
            } else {
                $('#auto-delete').addClass('d-none');
            }
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        $('#is_delete').on('click', function () {
            if($('#is_delete').is(":checked")) {
                $('#auto-delete').removeClass('d-none');
            } else {
                $('#auto-delete').addClass('d-none');
            }
        });

        $('#form-submit').on('submit', function (event) {
            event.preventDefault();

            let time = $('#time').val();
            let disable = $('#is_disable').is(":checked");
            let deleteCheck = $('#is_delete').is(":checked");
            let afterDays = $('#delete_after_days option:selected').val();

            $.ajax({
                method: "POST",
                data: {
                    time: time,
                    is_disable: (disable ? 1 : 0),
                    is_delete: (deleteCheck ? 1 : 0),
                    delete_after_days: afterDays
                },
                url: "{{ route('scheduler.fullbackup.post') }}",
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

                    setTimeout(function () {
                        $('#alert-import').addClass('d-none');
                    }, 2000);
                }
            });
        })
    </script>
@endsection
