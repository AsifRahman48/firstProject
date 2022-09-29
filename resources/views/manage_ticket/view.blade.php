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
                            <div class="row form-group">
                                <div class="col col-md-12">
                                    {!! Html::decode(Form::label('tDescription', 'Description', ['class' => 'form-control-label'])) !!}
                                </div>
                                <div class="col-12 col-md-12 tinymceTable">
                                    <div class="card">
                                   <div class="card-body" style="overflow-x: scroll;">
                                    <p class="card-text">{!! Html::decode($data['ticketInfo']->description)!!}</p>
                                  </div>
                                </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col col-md-12">
                                    {!! Html::decode(Form::label('Attachments', 'Attachments', ['class' => 'form-control-label'])) !!}
                                </div>
                                <div class="col-12 col-md-12 ml-1">
                                    <ol>
                                        @if(!empty(json_decode($data['ticketInfo']->files)))
                                            @foreach(json_decode($data['ticketInfo']->files) as $attachmentFile)
                                                <div class="input-group" style="margin-bottom: 10px;">
                                                    <a href="{{url('/')}}/{{$attachmentFile->folder}}/{{$attachmentFile->file_name}}"
                                                       target="_blank">
                                                        <li>{{$attachmentFile->file_name}}</li>
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            <p>No Attachments Available</p>
                                        @endif
                                    </ol>
                                </div>
                            </div>

                            <br>

                            <div class="form-actions form-group text-center">
                                <a href="{{ route('manage_ticket_edit', $data['ticketInfo']->ticket_id) }}"><span
                                        class="btn btn-secondary btn-xs pull-left" style=""><i
                                            class="fa fa-backward"></i>  Back</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
