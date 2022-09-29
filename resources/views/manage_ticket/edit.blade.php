@extends('layouts.elaadmin')

@push('page-css')
    <style type="text/css">
        .searchIcon {
            font-size: 22px;
        }

        .searchIcon:hover {
            color: green;
            cursor: pointer;
        }
    </style>
@endpush

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
                    <div class="card">
                        <div class="card-body card-block">
                            @if (session('error'))
                                <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show">
                                    <span class="badge badge-pill badge-danger">Danger</span>
                                    {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            @endif

                            @if($data['page'] == "edit")
                                {!! Form::open(['route' => ['manage_ticket_update', $data['ticketInfo']->id], 'enctype'=>'multipart/form-data']) !!}
                            @endif
                            <div class="row form-group">
                                <div class="col col-md-2">
                                    {!! Html::decode(Form::label('CompanyName', ' Company Name <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                                </div>
                                <div class="col-12 col-md-10">
                                    {!! Form::text('company_id', $data['ticketInfo']->company ? $data['ticketInfo']->company->name : 'Not Found', ['class' => 'form-control custom-select', 'required' => 'required','disabled'=>'disabled']) !!}
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-2">
                                    {!! Html::decode(Form::label('cat_id', 'Department <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                                </div>
                                <div class="col-12 col-md-10">
                                    {!! Form::text('cat_id', $data['ticketInfo']->category ? $data['ticketInfo']->category->name : 'Not Found', ['class' => 'form-control custom-select','disabled'=>'disabled']) !!}
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-2">
                                    <label for="sub_cat_id" class="form-control-label">Unit/Section <span
                                            class="mandatory-field">*</span></label>
                                </div>
                                <div class="col-12 col-md-10">
                                    {!! Form::text('cat_id', $data['ticketInfo']->sub_category ? $data['ticketInfo']->sub_category->name : 'Not Found', ['class' => 'form-control custom-select','disabled'=>'disabled']) !!}
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-2">
                                    <label for="sub_cat_id" class="form-control-label"> Initiator <span
                                            class="mandatory-field">*</span></label>
                                </div>
                                <div class="col-12 col-md-10">
                                    {{ $data['ticketInfo']->user ? $data['ticketInfo']->user->user_name : 'Name Not Found'}}
                                </div>
                            </div>

                            <div class="row ">
                                <div class="col-md-6 col-6">
                                    <div class="row form-group">
                                        <div class="col col-md-4">
                                            <label for="sub_cat_id" class="form-control-label">Recommender <span
                                                    class="mandatory-field">*</span></label>
                                        </div>
                                        <div class="col col-md-8">
                                            @foreach($data['recommenderList'] as $RecomInfo)
                                                <div class="input-group" style="margin-bottom: 10px;">
                                                    <input type="text" name="recommender_id[]"
                                                           value="{{$RecomInfo->name}}" class="form-control"
                                                           disabled='disabled'>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="row form-group">
                                        <div class="col col-md-4" style="text-align: right !important">
                                            <label for="sub_cat_id" class="form-control-label">Approver <span
                                                    class="mandatory-field">*</span></label>
                                        </div>
                                        <div class="col col-md-8">
                                            @foreach($data['approverList'] as $ApproInfo)
                                                <div class="input-group" style="margin-bottom: 10px;">
                                                    <input type="text" name="approver_id[]" value="{{$ApproInfo->name}}"
                                                           class="form-control" disabled='disabled'>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row form-group">
                                <div class="col col-md-2">
                                    {!! Html::decode(Form::label('tSubject', 'Subject <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                                </div>
                                <div class="col-12 col-md-10">
                                    {!! Form::text('tSubject',$data['ticketInfo']->tSubject, ['class' => 'form-control', 'placeholder' => 'Subject', 'required' => 'required','disabled'=>'disabled']) !!}

                                    @if($errors->has('tSubject'))
                                        <small class="help-block form-text">{{ $errors->first('tSubject') }}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-2">
                                    {!! Html::decode(Form::label('tDescription', 'Description', ['class' => 'form-control-label'])) !!}
                                </div>
                                <div class="col-12 col-md-10 tinymceTable">
                                    <div class="card">
                                        <div class="card-body" style="overflow-x: scroll;">
                                            @if($data['page'] == "edit")
                                                <p class="card-text">{!! Form::textarea('description', $data['ticketInfo']->tDescription, ['class' => 'form-control', 'required', 'rows' => 7, 'cols' => 54, 'style' => 'resize:none','id'=>'messageArea',]) !!}</p>
                                                @if($errors->has('description'))
                                                    <small class="help-block form-text">{{ $errors->first('description') }}</small>
                                                @endif
                                            @else
                                                <p class="card-text">{!! Html::decode($data['ticketInfo']->tDescription)!!}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-2">

                                </div>
                                <div class="col-12 col-md-10">
                                    <div class="card">
                                        <div class="card-body table-responsive">
                                            <h5 class="card-title"> &nbsp; Previous Comment</h5>
                                            <table class="table table table-bordered table-striped table-hover">
                                                <thead>
                                                <tr>
                                                    <th>SL</th>
                                                    <th>Name</th>
                                                    <th>Comment</th>
                                                    {{-- <th>Status</th> --}}
                                                    <th>Date</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php $sl=1; @endphp
                                                @foreach($data['previousComment'] as $key=>$PreviousComment)
                                                    <tr>
                                                        <td>#{{$sl++}}</td>
                                                        <td>
                                                            <img class="img-responsive user-photo"
                                                                 src="{{ asset('comment_user.png')}}"
                                                                 style="max-width: 20px; max-height: 20px; border-radius: 50%;">
                                                            &nbsp; &nbsp;{{$PreviousComment->User_name}}
                                                        </td>
                                                        <td>{!! Html::decode($PreviousComment->tDescription) !!}</td>
                                                        {{-- <td>{{$PreviousComment->created_at}}</td> --}}
                                                        <td> {{ date('d-M-Y H:i:s', strtotime($PreviousComment->created_at)) }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-2">
                                    <!-- History Log : -->
                                </div>
                                <div class="col-12 col-md-10">


                                    @if(!empty($data['ticketInfo']->thistory))
                                        @php
                                            $hasan=json_decode($data['ticketInfo']->thistory,true);
                                        @endphp

                                        <div class="card">
                                            <div class="card-body table-responsive">
                                                <h5 class="card-title"> &nbsp; History Log</h5>
                                                <table class="table table table-bordered table-striped table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th>SL</th>
                                                        <th>Name</th>
                                                        <th>User Type</th>
                                                        <th>Status</th>
                                                        <th>Date</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @php $sl=1; @endphp
                                                    @foreach($hasan as $key => $HistoryInfo)
                                                        <tr>
                                                            <td>#{{$sl++}}</td>
                                                            <td>
                                                                {{$HistoryInfo["user_name"]}}
                                                            </td>
                                                            <td>{{$HistoryInfo["user_type"]}}</td>
                                                            <td>{{$HistoryInfo["user_status"]}}</td>
                                                            <td>
                                                                @if(isset($HistoryInfo["date"]) && !empty($HistoryInfo["date"]))
                                                                    {{date('d-M-Y H:i:s', strtotime($HistoryInfo["date"]))}}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>


                                    @endif

                                </div>

                            </div>

                            @if(!($data['ticketInfo']->ticketEditHistories->isEmpty()))
                            <div class="row form-group">
                                <div class="col col-md-2">
                                    <!-- Ticket Edit History : -->
                                </div>
                                <div class="col-12 col-md-10">
                                        <div class="card">
                                            <div class="card-body table-responsive">
                                                <h5 class="card-title"> &nbsp; Ticket Edit History Log</h5>
                                                <table class="table table table-bordered table-striped table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th>SL</th>
                                                        <th>Edited By</th>
                                                        <th>Edited Date</th>
                                                        <th>View</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($data['ticketInfo']->ticketEditHistories as $key => $val)
                                                        <tr>
                                                            <td>#{{$key++}}</td>
                                                            <td>
                                                                {{$val->user ? $val->user->getFullNameAttribute() : 'Name Not Found'}}
                                                            </td>
                                                            <td>{{date('d-M-Y H:i:s', strtotime($val->created_at))}}</td>
                                                            <td>
                                                                <a class="btn btn-info btn-sm mt-1" href="{{ route('ticket_edit_view', $val->id)}}">
                                                                        <i class="fa fa-eye"></i>View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            @endif

                            <div class="row form-group">
                                <div class="col col-md-2">
                                    {!! Html::decode(Form::label('tFile', ' Attachments', ['class' => 'form-control-label'])) !!}
                                </div>
                                <div class="col-12 col-md-10">

                                    <ol>
                                        @foreach($data['attachmentFile'] as $attachmentFile)
                                            <div class="input-group" style="margin-bottom: 10px;">

                                                <a href="{{url('/')}}/{{$attachmentFile->folder}}/{{rawurlencode($attachmentFile->file_name)}}"
                                                   target="_blank">
                                                    <li>{{$attachmentFile->file_name}}</li>
                                                </a>

                                            </div>
                                        @endforeach
                                    </ol>
                                </div>
                                <div class="col col-md-2">
                                    {!! Html::decode(Form::label('tFile', ' Upload Attachments', ['class' => 'form-control-label'])) !!}
                                </div>
                                <div class="col-md-5">
                                    <input type="file" id="tFile0" name="tFile[]" class="form-control-file" multiple style='padding:0; margin-bottom:8px;'/>
                                </div>
                            </div>

                            <br>

                            <div class="form-actions form-group text-center">

                                <!-- Form Submit Button -->
                                @if($data['page'] == "edit")
                                    {!! Form::button('Submit <i class="fa fa-forward"></i>', ['type' => 'submit', 'name' => 'tStatus', 'value' => '2', 'class' => 'btn btn-success btn-xs','id'=>'save', 'style' => 'margin-left: 25px;']) !!}
                                {!! Form::close() !!}
                                @endif

                                <!-- Back Button -->
                                <a href="{{ route('get_manage_tickets') }}"><span
                                        class="btn btn-secondary btn-xs" style=""><i
                                            class="fa fa-backward"></i>  Back</span>
                                </a>

                                <!-- Edit & View Toggle Button -->
                                @if ($data['page'] != "edit")
                                    @if ($data['ticketInfo']->tStatus == 2)
                                        <a href="{{ route('manage_ticket_edit', $data['ticketInfo']->id) }}">
                                            <button class="btn btn-info btn-xs ml-1"><i
                                                    class="fa fa-pencil"></i>
                                                Edit</button>
                                        </a>
                                    @else
                                        @if ($data['ticketInfo']->tStatus == 5)
                                        {!! Form::open(['url' => ['update_view_status', $data['ticketInfo']->id]]) !!}
                                        @method('put')
                                        @csrf
                                        @if ($data['ticketInfo']->is_viewed == 0)
                                            <button
                                            onclick="return confirm('Are you sure to hide this ticket from user panel?')"
                                            class="btn btn-success btn-sm" name="is_viewed" value="1"><i
                                                class="fa fa-info-circle"></i>
                                            Hide Ticket</button>
                                        @else
                                                <button
                                                onclick="return confirm('Are you sure to show this ticket in user panel?')"
                                                class="btn btn-success btn-sm" name="is_viewed" value="0"><i
                                                    class="fa fa-info-circle"></i>
                                                Show Ticket</button>
                                        @endif
                                        {!! Form::close() !!}
                                        @else
                                            <span class="badge badge-success">Not eligible</span>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('lib/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('lib/tinymce/form-configuration.js') }}"></script>
@endsection
