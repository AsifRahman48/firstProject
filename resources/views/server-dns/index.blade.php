
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
                            <h1>Server DNS Name</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="{{ url('/') }}">Dashboard</a></li>
                                <li class="active">Server DNS Name</li>
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
                    <div class="card" style="min-height: 400px;">
                    <div class="card-body">
                        @if (Session::has('success'))
                            <div class="sufee-alert alert with-close alert-success alert-dismissible fade show">
                                <span>{{ Session::get('success') }}</span>
                            </div>
                        @endif

                        @if (Session::has('error'))
                                <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show">
                                    <span>{{ Session::get('error') }}</span>
                                </div>
                            @endif
                        <div class="table-responsive">
                            <!-- <br> -->
                            <table class="table table table-bordered table-striped table-hover">
                                <thead style="background-color: gray; color: #FFF">
                                <tr style="font-size: 12px; text-align: center;">
                                    <th>DNS Name</th>
                                    <th>IP Address</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $data['server_data']->dns_name }}</td>
                                        <td>{{ $data['server_data']->ip_address }}</td>
                                        <td><button class="btn btn-primary btn-sm edit-btn"><i class="fa fa-pencil-square-o"></i> Edit</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="edit-div" style="display: none">
                          {!! Form::open(['url'=>['server-dns', $data['server_data']->id], 'method' => 'post']) !!}
                            @method('put')
                            @csrf
                          <div class="row mt-3">
                              <div class="col-md-4">
                                  <div class="form-group">
                                      <label for="">DNS Name</label>
                                      <input class="form-control" name="dns_name" placeholder="DNS Name" value="{{ $data['server_data']->dns_name }}"/>
                                  </div>
                              </div>
                              <div class="col-md-4">
                                  <div class="form-group">
                                      <label for="">Ip Address</label>
                                      <input class="form-control" name="ip_address" placeholder="Ip Address" value="{{ $data['server_data']->ip_address }}"/>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <button type="submit" class="btn btn-success">Update</button>
                              </div>
                              {!! Form::close() !!}
                          </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
     <script type="text/javascript">
         $('.edit-btn').on('click', function () {
             $('.edit-div').slideToggle();
         })
     </script>
@endsection
