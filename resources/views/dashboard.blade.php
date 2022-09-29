@extends('layouts.elaadmin')

@section('content')
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
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content" style="min-height: 700px;">

    <div class="content pb-0">

            <!-- Widgets  -->
            <div class="row">

            	<div class="col-sm-12">
            		<div class="card1">
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
            		</div>
            	</div>
               
                <div class="col-lg-3 col-md-6">
                    <div class="card">
						
                        <div class="card-body">
                             <a href="{{ url('/request/inbox') }}">
                            <div class="stat-widget-five">
                                <div class="stat-icon dib flat-color-1">
                                    <i class="fa fa-paper-plane"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="text-left dib"> 
                                        <div class="stat-text"><span class="count">

                                            @php
echo $totalInbox=DB::table('tickets as t')->where([['t.initiator_id', '=', Auth::id()], ['t.tStatus', '!=', 1], ['t.is_delete', '=', 0] ])->orWhere([['t.now_ticket_at', '=', Auth::id()], ['t.tStatus', '!=', 1], ['t.is_delete', '=', 0] ])->count();
                                            @endphp
                                            
                                        </span></div>
                                        <div class="stat-heading">Inbox Request</div>
                                    </div>
                                </div>
                            </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ url('/request/pending') }}">    
                            <div class="stat-widget-five">
                                <div class="stat-icon dib flat-color-2">
                                    <i class="fa fa-bell"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="text-left dib">
                                        <div class="stat-text"><span class="count">
                                                  @php
echo $to=DB::table('tickets as t')->where('tStatus','=',2)->where('t.initiator_id', '=',Auth::id())->count();
                                            @endphp
                                        </span></div>
                                        <div class="stat-heading">Pending Request</div> 
                                    </div>
                                </div>
                            </div>
                        </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                             <a href="{{ url('/archive/archive_search') }}">    
                            <div class="stat-widget-five">
                                <div class="stat-icon dib flat-color-3">
                                    <i class="fa fa-briefcase"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="text-left dib"> 
                                        <div class="stat-text"><span class="count">
                                            @php
                                            if(Auth::user()->user_type!==1){
 echo $totalarc=DB::table('arc_ticket as t')->where('t.initiator_id', '=',Auth::id())->count();
}else{
echo $totalarc=DB::table('arc_ticket as t')->count();
}

                                            @endphp
                                        </span></div>
                                        <div class="stat-heading">Total Archive</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        </div>
                    </div>
                </div>
@if(Auth::user()->user_type==1)
                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                              <a href="{{ url('/users') }}">  
                            <div class="stat-widget-five">
                                <div class="stat-icon dib">
                                    <i class="fa fa-users text-success"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="text-left dib"> 
                                        <div class="stat-text"><span class="count">
                                             @php
echo $tot=DB::table('users')->count();
                                            @endphp
                                        </span></div>
                                        <div class="stat-heading">Total Users</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        </div>
                    </div>
                </div>

              @endif
            </div> 
            <!-- Widgets End -->


        </div>


     <!--    <div class="animated fadeIn">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">

                        <div class="card-header">
                            <strong class="card-title">Custom Table</strong>
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

                        <div class="table-stats order-table ov-h">
                            <table class="table ">
                                <thead>
                                <tr>
                                    <th class="serial">#</th>
                                    <th class="avatar">Avatar</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="serial">1.</td>
                                    <td class="avatar">
                                        <div class="round-img">
                                            <a href="#"><img class="rounded-circle" src="{{ asset('ElaAdmin/images/avatar/1.jpg') }}" alt=""></a>
                                        </div>
                                    </td>
                                    <td> #5469 </td>
                                    <td>  <span class="name">Louis Stanley</span> </td>
                                    <td> <span class="product">iMax</span> </td>
                                    <td><span class="count">231</span></td>
                                    <td>
                                        <span class="badge badge-complete">Complete</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="serial">2.</td>
                                    <td class="avatar">
                                        <div class="round-img">
                                            <a href="#"><img class="rounded-circle" src="{{ asset('ElaAdmin/images/avatar/2.jpg') }}" alt=""></a>
                                        </div>
                                    </td>
                                    <td> #5468 </td>
                                    <td>  <span class="name">Gregory Dixon</span> </td>
                                    <td> <span class="product">iPad</span> </td>
                                    <td><span class="count">250</span></td>
                                    <td>
                                        <span class="badge badge-complete">Complete</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="serial">3.</td>
                                    <td class="avatar">
                                        <div class="round-img">
                                            <a href="#"><img class="rounded-circle" src="{{ asset('ElaAdmin/images/avatar/3.jpg') }}" alt=""></a>
                                        </div>
                                    </td>
                                    <td> #5467 </td>
                                    <td>  <span class="name">Catherine Dixon</span> </td>
                                    <td> <span class="product">SSD</span> </td>
                                    <td><span class="count">250</span></td>
                                    <td>
                                        <span class="badge badge-complete">Complete</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="serial">4.</td>
                                    <td class="avatar">
                                        <div class="round-img">
                                            <a href="#"><img class="rounded-circle" src="{{ asset('ElaAdmin/images/avatar/4.jpg') }}" alt=""></a>
                                        </div>
                                    </td>
                                    <td> #5466 </td>
                                    <td>  <span class="name">Mary Silva</span> </td>
                                    <td> <span class="product">Magic Mouse</span> </td>
                                    <td><span class="count">250</span></td>
                                    <td>
                                        <span class="badge badge-pending">Pending</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div> -->
    </div>
@endsection
