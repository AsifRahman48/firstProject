<?php
/**
 * Created by PhpStorm.
 * User: BS108
 * Date: 10/10/2018
 * Time: 2:50 PM
 */
?>
@extends('layouts.elaadmin')

@section('add_script')
	<script src="{{ asset('ElaAdmin/assets/js/lib/data-table/datatables.min.js') }}"></script>
	<script src="{{ asset('ElaAdmin/assets/js/lib/data-table/dataTables.bootstrap.min.js') }}"></script>
@endsection

@section('content')
	{{-- <link rel="stylesheet" href="{{ asset('ElaAdmin/assets/css/lib/datatable/dataTables.bootstrap.min.css') }}"> --}}
	

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
                            <strong class="card-title" style="line-height: 30px;">All Unit/Section listed in here.</strong>
                            <a class="pull-right" href="{{ url('sub-category/create') }}"><button class="btn btn-success btn-sm"> <i class="fa fa-plus"></i> Add Unit/Section</button></a>
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

                        <div class="table-stats order-table ov-h">
                            <table class="table bootstrap-data-table">
                                <thead>
                                 <tr style="font-size: 12px; text-align: center;">
                                    <th class="serial">#</th>
                                     <th>Action</th>
                                    <th>Department Name</th>
                                    <th>Unit/Section Name</th>
                                     <th>Active Date</th>
                                    <th>Deactive Date</th>
                                   
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $sn = 1;
                                @endphp
                                @foreach($data['listData'] as $key => $value)
                                     <tr style="font-size: 12px; text-align: center;">
                                        <td class="serial">{{ $sn }}.</td>
                                         <td>
                                            <a style="margin-left: 15px;" href="{{ url('sub-category/'.$value->id.'/edit') }}">
                                                <button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o"></i> Edit</button>
                                            </a>

                                            {{ Form::open(array('url' => 'sub-category/' . $value->id, 'class' => 'pull-left', 'onsubmit' => 'return ConfirmDelete()')) }}
                                            {{ Form::hidden('_method', 'DELETE') }}
                                            {{ Form::button('<i class="fa fa-trash"></i> Delete', ['type' => 'submit', 'class' => 'btn btn-danger btn-sm'])  }}
                                            {{ Form::close() }}
                                        </td>
                                        <td><span class="name">{{ $value->category->name }}</span></td>
                                        <td><span class="name">{{ $value->name }}</span></td>
                                        <td><span class="name">{{ $value->active_date }}</span></td>
                                        <td><span class="name">{{ $value->deactive_date }}</span></td>
                                       
                                    </tr>
                                    @php
                                        $sn++;
                                    @endphp
                                @endforeach
                                </tbody>
                            </table>
                            {{-- {{ $data['listData']->links() }} --}}
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


	
	
	<script type="text/javascript">
		
		$(document).ready(function(){

		 	// $('.hideDiv').hide();
		  	$('.bootstrap-data-table').DataTable();
		});

	</script>



@endsection






