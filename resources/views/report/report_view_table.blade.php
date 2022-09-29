<link rel="stylesheet" href="{{ asset('ElaAdmin/assets/css/lib/datatable/dataTables.bootstrap.min.css') }}">
<script src="{{ asset('ElaAdmin/assets/js/lib/data-table/datatables.min.js') }}"></script>
<script src="{{ asset('ElaAdmin/assets/js/lib/data-table/dataTables.bootstrap.min.js') }}"></script>
<style type="text/css">
    .historyDiv {
        height: 50px !important;
        overflow: hidden;
        width: 250px !important;
        -webkit-transition: all 0.5s ease;
        -moz-transition: all 0.5s ease;
        -o-transition: all 0.5s ease;
        transition: all 0.5s ease;
        max-height: 300px;
    }

    .historyDivView {
        /*width: 300px;*/
        min-height: 100px !important;
        -webkit-transition: all 0.5s ease;
        -moz-transition: all 0.5s ease;
        -o-transition: all 0.5s ease;
        transition: all 0.5s ease;
        max-height: 300px;
        overflow-x: auto;
    }

    .table-responsive {
        display: table;
    }
</style>

<div class="row">
    <div class="col-lg-3 col-md-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="stat-widget-five">
                    <div class="stat-icon dib flat-color-1">
                        <i class="fa fa-ticket"></i>
                    </div>
                    <div class="stat-content">
                        <div class="text-left dib">
                            <div class="stat-text"><span class="count">
                                        {{ $result->count() }}
                                    </span></div>
                            <div class="stat-heading">Total Request</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="stat-widget-five">
                    <div class="stat-icon dib flat-color-2">
                        <i class="fa fa-bell"></i>
                    </div>
                    <div class="stat-content">
                        <div class="text-left dib">
                            <div class="stat-text"><span class="count">
                                        {{ $result->count() - ($result->where('tStatus', 4)->count() + $result->where('tStatus', 5)->count()) }}
                                    </span></div>
                            <div class="stat-heading">Pending Request</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="stat-widget-five">
                    <div class="stat-icon dib flat-color-3">
                        <i class="fa fa-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="text-left dib">
                            <div class="stat-text"><span class="count">
                                        {{ $result->where('tStatus', 4)->count() }}
                                    </span></div>
                            <div class="stat-heading">Approved Request</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="stat-widget-five">
                    <div class="stat-icon dib flat-color-4">
                        <i class="fa fa-ban"></i>
                    </div>
                    <div class="stat-content">
                        <div class="text-left dib">
                            <div class="stat-text"><span class="count">
                                        {{ $result->where('tStatus', 5)->count() }}
                                    </span></div>
                            <div class="stat-heading">Rejected Request</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mr-0" style="padding: 0px;  overflow-y: hidden; overflow-x:scroll;">
    <table class="table table-bordered table-striped table-hover bootstrap-data-table mt-12">
        <thead>
        <tr style="font-size: 12px; text-align: center;">
            <th class="serial">Sl.no</th>
            <th>Action</th>
            <th>Reference No</th>
            <th>Initiator Name</th>
            <th>Company</th>
            <th>Department</th>
            <th>Unit/Section</th>
            <th>Subject</th>
            <th>Status</th>
            <th>History</th>
            <th>Initiation Date</th>
            <th>Received Date</th>
        </tr>
        </thead>
        <tbody>
        @php
            $sn = 1;
        @endphp
        @foreach($result as $key => $value)
            <tr style="font-size: 12px; text-align: center;">
                <td>{{$sn}}</td>
                <td><a href="{{ url('acknowledgement/'.$value->id) }}" target="_blank"
                       class="btn btn-info btn-sm">view</a></td>
                <td>{{$value->tReference_no}}</td>
                <td>{{$value->CreatorName}}</td>
                <td>{{$value->companyName}}</td>
                <td>{{$value->categorysName}}</td>
                <td>{{$value->sub_categorysName}}</td>
                <td>{{$value->tSubject}}</td>
                <td> {{ $statusResult[$value->tStatus] }}</td>
                <td id="{{$sn}}">
                    <div id="div{{$sn}}" class="historyDiv">
                        @if(!empty($value->thistory))
                            @php
                                $hasan=json_decode($value->thistory,true);
                            @endphp
                            <ul>
                                @foreach($hasan as $key => $HistoryInfo)
                                    <li>{{$key+1}} => {{$HistoryInfo["user_name"]}}-{{$HistoryInfo["user_type"]}}
                                        -{{$HistoryInfo["user_status"]}}</li>
                                @endforeach

                            </ul>
                        @endif
                    </div>
                    @if(!empty($value->thistory))
                        <button id="btn{{$sn}}" class="fa fa-eye text-success" aria-hidden="true"
                                onclick="historyFullView({{$sn}})"></button>
                        <button id="btnS{{$sn}}" class="fa fa-eye-slash text-danger hideDiv" aria-hidden="true"
                                onclick="historyFullViewClose({{$sn}})"></button>
                    @endif
                </td>
                <td><span class="name">{{ date('d-M-Y', strtotime($value->created_at)) }}</span></td>
                <td><span class="name">
                    @if(!empty($value->updated_at) && ($value->tStatus!=='2' && $value->tStatus!=='1'))
                            {{ date('d-M-Y', strtotime($value->updated_at)) }}
                        @endif
              </span></td>
            </tr>
            @php
                $sn++;
            @endphp
        @endforeach
        </tbody>
    </table>
    <br>
</div>
<script type="text/javascript">
    function historyFullView(divId) {
        $('#div' + divId).removeClass('historyDiv');
        $('#div' + divId).addClass('historyDivView', 100);
        $('#btn' + divId).hide();
        $('#btnS' + divId).show();

    }

    function historyFullViewClose(divId) {
        $('#div' + divId).removeClass('historyDivView');
        $('#div' + divId).addClass('historyDiv', 100);
        $('#btn' + divId).show();
        $('#btnS' + divId).hide();

    }

    $(document).ready(function () {
        $('.hideDiv').hide();
        $('.bootstrap-data-table').DataTable();
    });
</script>
