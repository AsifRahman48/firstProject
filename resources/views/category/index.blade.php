<?php
/**
 * Created by PhpStorm.
 * User: BS108
 * Date: 10/10/2018
 * Time: 2:50 PM
 */
?>
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
                    <div class="card">

                        <div class="card-header">
                            <strong class="card-title" style="line-height: 30px;">All Department listed in here.</strong>
                            <a class="pull-right" href="{{ url('category/create') }}"><button class="btn btn-success btn-sm"> <i class="fa fa-plus"></i> Add Department</button></a>
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

                        <div class="col-md-12">
<br>
                            <table class="table table-stats table-hover table-striped">
                              
                                <tr style="font-size: 14px; text-align: center; background-color: gray ; color: #FFF;">
                                    <th class="serial">#</th>
                                    <th style="text-align: left;">Action</th>
                                    <th>Department Name</th>
                                    <th>Active Date</th>
                                    <th>Deactive Date</th>
                                   
                                </tr>
                             
                                <tbody>
                                @php
                                    $sn = 1;
                                @endphp
                                @foreach($data['listData'] as $key => $value)
                                    <tr style="font-size: 12px; text-align: center;">
                                        <td class="serial">{{ $sn }}.</td>
                                          <td style="">
                                            <a style="margin-left: 15px;" href="{{ url('category/'.$value->id.'/edit') }}">
                                                <button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o"></i> Edit</button>
                                            </a>

                                            {{ Form::open(array('url' => 'category/' . $value->id, 'class' => 'pull-left', 'onsubmit' => 'return ConfirmDelete()')) }}
                                            {{ Form::hidden('_method', 'DELETE') }}
                                            {{ Form::button('<i class="fa fa-trash"></i> Delete', ['type' => 'submit', 'class' => 'btn btn-danger btn-sm'])  }}
                                            {{ Form::close() }}
                                        </td>
                                        <td style=" text-align: center;"><span class="name">{{ $value->name }}</span></td>
                                        <td><span class="name">{{ $value->active_date }}</span></td>
                                        <td><span class="name">{{ $value->deactive_date }}</span></td>
                                      
                                    </tr>
                                    @php
                                        $sn++;
                                    @endphp
                                @endforeach
                                </tbody>
                            </table>
                            {{ $data['listData']->links() }}
                        </div>
                        <script>
                            function ConfirmDelete(){
                                return confirm('Are you sure?');
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
