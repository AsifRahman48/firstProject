<style type="text/css">
    .btn-xss {
        padding: .25rem .4rem;
        font-size: .875rem;
        line-height: .5;
        border-radius: .2rem;
    }

    .tinymceTable table {
        border: 1px solid #888686;
    }

    .tinymceTable table td {
        border: 1px solid #888686;
    }
</style>

<script type="text/javascript">

    var BASE_URL = {!! json_encode(url('/')) !!};

</script>

<header id="header" class="header wwwwwwww">
    <div class="top-left">
        <div class="navbar-header">
            <a class="navbar-brand hidden" href="{{ url('/') }}"><img class="img-fluid"
                                                                      src="{{ asset('ElaAdmin/images/logo.png') }}"
                                                                      alt="Logo"></a>
            @if(\Illuminate\Support\Facades\Storage::disk('public')->has('setting/logo.png'))
                <a class="navbar-brand" href="{{ url('/') }}"><img class="ml-3" style="height: 50px"
                                                                   src="{{asset('storage/setting/logo.png')}}"
                                                                   alt="Logo"></a>
            @else
                <a class="navbar-brand" href="{{ url('/') }}"><img
                                                                   src="{{ asset('ElaAdmin/images/logo_2.png') }}"
                                                                   alt="Logo"></a>
        @endif
        <!-- <a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a> -->
            <style type="text/css">
                .tolsts:hover {
                    color: green;
                }
            </style>
            <div class="toggle-menu-button">
                <i class="tolsts fa fa-bars fa-2x toggle-btn"></i>
            </div>
        </div>
    </div>
    <div class="top-right">
        <div class="header-menu">
            <div class="header-left">
                <!--    <button class="search-trigger"><i class="fa fa-search"></i></button>
                   <div class="form-inline">
                       <form class="search-form">
                           <input class="form-control mr-sm-2" type="text" placeholder="Search ..." aria-label="Search">
                           <button class="search-close" type="submit"><i class="fa fa-close"></i></button>
                       </form>
                   </div> -->

                <div class="dropdown for-notification">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="notification"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bell"></i>
                        <span class="count bg-danger">
                              @php
                                  echo $ReAC=DB::table('ticket_historys')->where('tStatus','=',9)->where('action_to','=',Auth::id())->count();
                              @endphp
                        </span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="notification" style="min-width: 300px;">
                        <p class="red">You have
                            @php
                                echo $ReAC;
                            @endphp

                            Notification</p>

                        @php

                            $requestInfoAc = DB::table('ticket_historys as TH')
                                            ->join('tickets', 'TH.ticket_id', '=', 'tickets.id')
                                            ->select('TH.*', 'tickets.tSubject')
                                            ->where('TH.action_to','=',Auth::id())
                                            ->where('TH.tStatus','=',9)
                                            ->orderBy('TH.id', 'desc')
                                            ->take(5)
                                            ->get();
                        @endphp

                        @foreach($requestInfoAc as $key => $ACInfo)
                            <a class="dropdown-item media"
                               href="{{ url('acknowledgement/'.$ACInfo->ticket_id) }}?back=0">
                                <i class="fa fa-info"></i>
                                <p>{{$ACInfo->tSubject}}</p>
                            </a>
                        @endforeach
                        <br>

                        <div class="col-12 text-center">
                            <a href="{{ url('acknowledgement_list/') }}" class="btn btn-xss btn-success ">More</a>
                        </div>
                    </div>

                </div>
            </div>

            <div class="user-area dropdown float-right">
                <a href="{{ url('/') }}" class="dropdown-toggle active" data-toggle="dropdown" aria-haspopup="true"
                   aria-expanded="false">
                    <img class="user-avatar rounded-circle" src="{{ asset('profile.png') }}" alt="User Avatar">
                </a>

                <div class="user-menu dropdown-menu">
                    <a class="nav-link" href=""><i class="fa fa-user text-success"></i>{{ Auth::user()->name }}</a>

                    @if(config('custom.settings.authentication') == 'database')
                        <a class="nav-link" href="{{ route('user.edit',Auth::user()->id) }}"><i class="fa fa-key"></i>Update User Profile</a>
                    @endif

                    @if(config('custom.settings.authentication') == 'database')
                        <a class="nav-link" href="{{ route('change.password.index') }}"><i class="fa fa-key"></i>Change Password</a>
                    @endif
                    <!-- <a class="nav-link" href=""><i class="fa fa-bell-o"></i>Notifications <span class="count">13</span></a> -->
                    <a class="nav-link" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                class="fa fa-power-off text-danger"></i>{{ __('Logout') }}</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
