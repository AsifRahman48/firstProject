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
                                <li><a href="{{ url('/') }}">Vacation</a></li>
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
                            <strong>Edit</strong> Vacation
                        </div>
                        <div class="card-body card-block ">
                            <div class="com-md-8  mx-auto">
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

                                {!! Form::open(['route' => ['vacations.update', $data['vacation']->id], 'class' => 'form-horizontal']) !!}
                                @method('put')
                                @csrf
                                <div class="row form-group">
                                    <div class="col col-md-12 d-flex">
                                        {!! Html::decode(Form::label('leave_type_id', 'Leave Type <span class="mandatory-field">*</span>', ['class' => 'form-control-label col-md-3'])) !!}

                                        {!! Form::select('leave_type_id', $data['leaves'], $data['vacation']->leave_type_id, [
                                            'class' => 'form-control col-md-8 leaves', 
                                            'placeholder' => 'Select Type', 
                                            'id' => 'leave_type', 
                                            'onchange' => 'showReasonInput()']) !!}
                                    </div>
                                   
                                    @if ($errors->has('leave_type_id'))
                                        <small
                                            class="help-block form-text text-danger offset-md-3">{{ $errors->first('leave_type_id') }}</small>
                                    @endif
                                </div>
                                <div class="row form-group reason" style="display:none">
                                    <div class="col col-md-12 d-flex">
                                        {!! Html::decode(Form::label('reason', 'Reason <span class="mandatory-field">*</span>', ['class' => 'form-control-label col-md-3'])) !!}

                                        {!! Form::text('reason', $data['vacation']->reason, ['class' => 'form-control col-md-8', 'placeholder' => 'Reason']) !!}
                                    </div>
                                   
                                    @if ($errors->has('reason'))
                                        <small
                                            class="help-block form-text text-danger offset-md-3">{{ $errors->first('reason') }}</small>
                                    @endif
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-11 d-flex">
                                        <label style="margin-right: 24px" class="col-md-3">Forward User <span class="mandatory-field">*</span></label>
                                        {!! Form::select('forward_user_id', $data['users'], $data['vacation']->forward_user_id, ['class' => 'form-control col-md-7 select2', 'placeholder' => 'Select User']) !!}
                                    </div>
                                    
                                    @if ($errors->has('forward_user_id'))
                                        <small class="help-block form-text text-danger offset-md-3">{{ $errors->first('forward_user_id') }}</small>
                                    @endif
                                </div>
                               

                                <div class="row form-group">
                                    <div class="col col-md-12 d-flex">
                                        {!! Html::decode(Form::label('from_date', 'From Date <span class="mandatory-field">*</span>', ['class' => 'form-control-label col-md-3'])) !!}
                                        {!! Form::text('from_date', date('d-m-Y', strtotime($data['vacation']->from_date)), ['class' => 'form-control col-md-8 datepicker', 'placeholder' => 'From Date', 'required' => 'required']) !!}
                                        
                                        @if ($errors->has('from_date'))
                                            <small class="help-block form-text">{{ $errors->first('from_date') }}</small>
                                        @endif
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-12 col-md-12  d-flex">
                                        {!! Html::decode(Form::label('to_date', 'To Date <span class="mandatory-field">*</span>', ['class' => 'form-control-label col-md-3'])) !!}
                                        {!! Form::text('to_date', date('d-m-Y', strtotime($data['vacation']->to_date)), ['class' => 'form-control col-md-8 datepicker', 'placeholder' => 'To Date']) !!}

                                        @if ($errors->has('to_date'))
                                            <small class="help-block form-text">{{ $errors->first('to_date') }}</small>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-actions form-group">
                                    <a href="{{ route('vacations.index') }}"><span class="btn btn-secondary btn-xs"><i
                                                class="fa fa-backward"></i> Back</span></a>
                                                {!! Form::button('Submit <i class="fa fa-forward"></i>', ['type' => 'submit', 'class' => 'btn btn-success btn-xs pull-right', 'name' => 'status', 'value' => 'submitted']) !!}
                                                {!! Form::button('Draft <i class="fa fa-forward"></i>', ['type' => 'submit', 'class' => 'btn btn-primary btn-xs mr-1 pull-right', 'name' => 'status', 'value' => 'draft']) !!}
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link href="{{ asset('select2/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('select2/select2.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            showReasonInput();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $(document).ready(function() {
                $("#success-alert").hide();

                $(".select2").select2({
                    minimumInputLength: 2,
                    ajax: {
                        url: "{{ URL::to('/search_ad_user') }}",
                        type: "POST",
                        dataType: 'json',
                        delay: 10,
                        data: function(params) {
                            return {
                                term: params.term // search term
                            };
                        },
                        processResults: function(data) {
                            var results = [];
                            $.each(data, function(index, account) {
                                var nameInfo = account.name + '->' + account.title +
                                    '->' + account.department + '->' + account.email +
                                    '->' + account.company_name;
                                if (account.id != @php echo Auth::id() @endphp) {
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
            });


            let dateToday = new Date();
            let $datepicker = $('.datepicker');
            $datepicker.datepicker({
                dateFormat: 'dd-mm-yy',
                minDate: dateToday
            });

           
        });

        function showReasonInput() {
                let value = $(".leaves option:selected").text();
                
                value == "Others" ? $(".reason").show() : $(".reason").hide();
            }

    </script>
@endsection
