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
                            <strong class="card-title" style="line-height: 30px;">Database Backup File List.</strong>

                            <a href="{{ url('/only_db') }}" class="btn btn-sm btn-success pull-right"
                               style="margin-left:20px;" onclick=" return load();"> <i class="fa fa-database"
                                                                                       aria-hidden="true"></i>&nbsp;&nbsp;
                                Get Backup Only DB</a>

                            <a href="{{ url('/db_file') }}" class="btn btn-sm btn-info pull-right"
                               onclick=" return load();"> <i class="fa fa-database" aria-hidden="true"></i>&nbsp;&nbsp;
                                Get Full Backup</a>
                            <!-- <button>Show</button> -->
                        </div>

                        @if (session('status'))
                            <div
                                class="sufee-alert alert with-close alert-success alert-dismissible fade show no-margin">
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
                        <div class="card-body table-responsive">
                            @php
                                $totalFile=count($data['file']);
                                $j=1;
                            @endphp
                            <table class="table table table-bordered table-striped table-hover bootstrap-data-table">
                                <thead>
                                <tr style="font-size: 12px; text-align: center;">
                                    <th class="serial">#</th>
                                    <!-- <th>Role</th> -->
                                    <th>File Name</th>
                                    <th>File Size</th>
                                    <th>Action</th>


                                </tr>
                                </thead>
                                <tbody>
                                @for($i=$totalFile-1;$j<=$totalFile;$i--)
                                    <tr>
                                        <td>{{$j}}</td>
                                        <td>


                                            {{ str_replace('-only-db', '', $data['file'][$i]) }}

                                            @php


                                                $precision = 2;
                                                $size=Storage::size($data['file'][$i]);

                                                  if ($size > 0) {
                                                            $size = (int) $size;
                                                         $base = log($size) / log(1024);
                                                         // dd( $base );
                                                            $suffixes = array(' bytes', ' Only DB', ' Full Backup', ' Full Backup', ' Full Backup');
                                                            $color = array('primary', 'success', 'info', 'warning', 'danger');

                                                            if( strpos($data['file'][$i],  '-only-db') !== false ){

                                                                echo '<b class="text-'.$color[1].'">';
                                                                       echo  $suffixes[1];
                                                                echo '</b>';

                                                            }
                                                            else{
                                                                echo '<b class="text-'.$color[2].'">';
                                                                       echo  $suffixes[2];
                                                                echo '</b>';
                                                            }

                                                        }

                                            @endphp


                                        </td>

                                        <td>
                                            @php
                                                $precision = 2;
                                                $size=Storage::size($data['file'][$i]);
                                                  if ($size > 0) {
                                                            $size = (int) $size;
                                                             $base = log($size) / log(1024);
                                                            $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');
                                                            $color = array('primary', 'success', 'info', 'warning', 'danger');


                                                            if( strpos($data['file'][$i],  '-only-db') !== false ){
                                                                echo '<b class="text-'.$color[1].'">';
                                                            }
                                                            else{
                                                                echo '<b class="text-'.$color[2].'">';
                                                            }


                                                            echo  round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
                                                            echo '</b>';
                                                        }

                                            @endphp

                                        </td>
                                        <td><a href="{{ url('/') }}/DB_BACKUP/{{$data['file'][$i]}}"
                                               class="btn btn-sm btn-info"><i class="fa fa-cloud-download"
                                                                              aria-hidden="true"></i> Download</a>
                                            <a href="{{ url('/backupDelete') }}/{{$data['file'][$i]}}"
                                               class="btn btn-sm btn-danger" onclick=" return load();"><i
                                                    class="fa fa-trash-o" aria-hidden="true"></i> Delete</a>
                                        </td>

                                    </tr>

                                    @php
                                        $j++;
                                    @endphp
                                @endfor
                                </tbody>
                            </table>


                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function load() {
            // alert('habib');
            $('#loader').show();
            $('#loader').removeClass('d-none');

            return true;

        }

    </script>
@endsection
