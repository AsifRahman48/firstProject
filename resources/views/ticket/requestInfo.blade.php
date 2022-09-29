
@extends('layouts.elaadmin')

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
                            <strong class="card-title" style="line-height: 30px;">All {{ $data['pageTitle'] }} listed in here.</strong>
                            <a class="pull-right" href="{{ url('/request/new') }}"><button class="btn btn-success btn-sm"> <i class="fa fa-plus"></i> Add a new request</button></a>
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
<style type="text/css">
.historyDiv{
    height: 50px !important;
    overflow: hidden;
    width: 250px !important;
     -webkit-transition: all 0.5s ease;
    -moz-transition: all 0.5s ease;
    -o-transition: all 0.5s ease;
    transition: all 0.5s ease;
    max-height: 300px;
}
.historyDivView{
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
                         <div class="table-responsive">
                            <br>
                            <table class="table table table-bordered table-striped table-hover bootstrap-data-table">
                                <thead>
                                 <tr style="font-size: 12px; text-align: center;">
                                    <th class="serial">#</th>
                                    <th>Action</th>
                                    <th>Reference Number</th>
                                    <th style="max-width: 150px;">Subject</th>
                                    <th>Department</th>
                                    <th>Unit/Section</th>
                                    <th>Recommender</th>
                                    <th>Status</th>
                                    <th>Initiation Date</th>
                                    <th>Received Date</th>

                                    <th>History</th>
                                  
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
                                                  @if($value->tStatus!=='1')
                                            <a href="{{ url('request/details/'.$value->id) }}">
                                                <button class="btn btn-primary btn-sm"><i class="fa fa-info-circle"></i> Details</button>
                                            </a>
                                            @else
                                                 <a href="{{ url('request/draft-edit/'.$value->id) }}">
                                                <button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o"></i> Edit</button>
                                            </a>
                                            @endif                                  
                                        </td>
                                        <td><span class="name">{{ $value->tReference_no }}</span></td>
                                        @php 

                                        @endphp
                                        <td style="max-width: 150px;"><span class="name">{{ substr($value->tSubject,0,100)}}</span></td>
                                        <td><span class="name">{{ $value->cat_name }}</span></td>
                                        <td><span class="name">{{ $value->sub_cat_name }}</span></td>
                                        <td><span class="name">{{ $value->user_name }}</span></td>
                                         <td>
                                            @if($value->now_ticket_at==$value->initiator_id && $value->tStatus==4)
                                            Approved
                                            @else
                                            {{ $StatusList[$value->tStatus] }}
                                            @endif
                                        </td>
                                        <td><span class="name">{{ date('d-M-Y', strtotime($value->created_at)) }}</span></td>
                                            <td><span class="name">
                                                 @if(!empty($value->updated_at) && ($value->tStatus!=='2' && $value->tStatus!=='1'))
                                            {{ date('d-M-Y', strtotime($value->updated_at)) }}                              
                                            @endif
                                            </span></td>                                    
                                       
                                          <td id="{{$sn}}">
                                            <div id="div{{$sn}}"  class="historyDiv" >                                        
                                            @if(!empty($value->thistory))
                                             @php
                                            $hasan=json_decode($value->thistory,true);
                                            @endphp
                                            <ul>
                                            @foreach($hasan as $key => $HistoryInfo)
                                                 <li>{{$key+1}} => {{$HistoryInfo["user_name"]}}-{{$HistoryInfo["user_type"]}}-{{$HistoryInfo["user_status"]}}</li>
                                                @endforeach

                                            </ul>
                                            @endif
                                </div>
<button id="btn{{$sn}}" class="fa fa-eye text-success" aria-hidden="true" onclick="historyFullView({{$sn}})"></button>
<button id="btnS{{$sn}}" class="fa fa-eye-slash text-danger hideDiv" aria-hidden="true" onclick="historyFullViewClose({{$sn}})"></button>

                                        </td>
                                      
                                    </tr>
                                    @php
                                        $sn++;
                                    @endphp
                                @endforeach
                                </tbody>
                            </table>
                          <br>
                          <br>
                          <br>
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
        function historyFullView(divId){
                   $('#div'+divId).removeClass('historyDiv');
                   $('#div'+divId).addClass('historyDivView',100);
                   $('#btn'+divId).hide();
                   $('#btnS'+divId).show();

        }  

        function historyFullViewClose(divId){
                   $('#div'+divId).removeClass('historyDivView');
                   $('#div'+divId).addClass('historyDiv',100);
                   $('#btn'+divId).show();
                   $('#btnS'+divId).hide();

        }
$(document).ready(function(){

 $('.hideDiv').hide();
  $('.bootstrap-data-table').DataTable();
});
    </script>
@endsection