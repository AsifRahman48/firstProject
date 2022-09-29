<div id="left-panelw">
<style type="text/css">
  .nav-side-menu {
  overflow: auto;
  font-family: sans-serif;
  /*font-family: Times New Roman;*/
  font-size: 14px;
  font-weight: 400;
  background-color: #FFF;
  position: fixed;
  top: 0px;
  width: 300px;
  height: 100%;
  color: #000;
  z-index: 9 !important;
  min-width: 280px;
   -webkit-transition: all .5s;
  -moz-transition: all .5s;
  -o-transition: all .5s;
  transition: all .5s;
}
@media only screen and (min-width: 768px)  {
  .nav-side-menu.menu-collapsed {
    width: 60px;
    min-width: 60px;
}
.right-panel.full-width {
  margin-left: 60px;
}
}
.nav-side-menu .brand {
  background-color: #FFF;
  line-height: 50px;
  display: block;
  text-align: center;
  font-size: 14px;
}
.nav-side-menu .toggle-btn {
  display: none;
}
.nav-side-menu ul,
.nav-side-menu li {
  list-style: none;
  padding: 0px;
  margin: 0px;
  line-height: 35px;
  cursor: pointer;
  /*
    .collapsed{
       .arrow:before{
                 font-family: FontAwesome;
                 content: "\f053";
                 display: inline-block;
                 padding-left:10px;
                 padding-right: 10px;
                 vertical-align: middle;
                 float:right;
            }
     }
*/
}
@media only screen and (min-width: 768px)  {
  .nav-side-menu.menu-collapsed ul :not(collapsed) .arrow:before,
.nav-side-menu.menu-collapsed li :not(collapsed) .arrow:before {
    font-size: 12px;
    position: absolute;
    right: -3px;
    color: #6b6b6b;
    -webkit-transform: rotate(-90deg);
    -ms-transform: rotate(-90deg);
    transform: rotate(-90deg);
}
}
@media only screen and (min-width: 768px)  {
  .nav-side-menu.menu-collapsed ul li.show ~ ul.sub-menu, .nav-side-menu.menu-collapsed ul li ~ ul.sub-menu.show.collapse.in, .nav-side-menu.menu-collapsed ul li ~ ul.sub-menu.collapse, .nav-side-menu.menu-collapsed ul li ~ ul.sub-menu.collapsing  {
  position: fixed;
  left: 60px;
  width: 270px;
  margin-top: -41px;
  box-shadow: 0 0 20px #d8d8d8;
}
}
.nav-side-menu.menu-collapsed ul .sub-menu li {
  line-height: 34px;
   padding: 4px 15px;
}
.nav-side-menu.menu-collapsed ul .sub-menu li a .badge {
  margin-top: 15px;
}
.nav-side-menu.menu-collapsed ul .sub-menu li:before {
  display: none;
}
.nav-side-menu ul :not(collapsed) .arrow:before,
.nav-side-menu li :not(collapsed) .arrow:before {
  font-family: sans-serif;
  font-family: FontAwesome;
  content: "\f078";
  display: inline-block;
  padding-left: 10px;
  padding-right: 10px;
  vertical-align: middle;
  float: right;
  -webkit-transition: all .5s;
  -moz-transition: all .5s;
  -o-transition: all .5s;
  transition: all .5s;
}
.nav-side-menu ul .active,
.nav-side-menu li .active {
  /*border-left: 3px solid #d19b3d;*/
  /*background-color: #CCC;*/
}
.nav-side-menu ul .sub-menu li.active,
.nav-side-menu li .sub-menu li.active {
    color: #000;
    /*background-color: #CCC;*/
}

.menu-active {
    background-color: #CCC !important;
}

.nav-side-menu ul .sub-menu li.active a,
.nav-side-menu li .sub-menu li.active a,
 {
  color: green;
}
.nav-side-menu ul .sub-menu li,
.nav-side-menu li .sub-menu li {
  background-color: #FFF;
  border: none;
  line-height: 28px;
  /*border-bottom: 1px solid #23282e;*/
  margin-left: 0px;
}
.nav-side-menu li {
  padding-left: 0px;
  /*border-left: 3px solid #2e353d;*/
  /*border-bottom: 1px solid #23282e;*/
  padding: 3px;
}
.nav-side-menu ul .sub-menu li:hover,
.nav-side-menu li .sub-menu li:hover {
  background-color: #CCC;
}
.nav-side-menu ul .sub-menu li:before,
.nav-side-menu li .sub-menu li:before {
  font-family: FontAwesome;
  content: "\f105";
  display: inline-block;
  padding-left: 30px;
  padding-right: 10px;
  vertical-align: middle;
  padding-top: 0px;
  padding-bottom: 0px;
}

.nav-side-menu li a {
  text-decoration: none;
  color: #000;
}
@media only screen and (min-width: 768px)  {
.nav-side-menu.menu-collapsed ul li a span.active-menu-span {
  position: absolute;
  opacity: 0;
}
.nav-side-menu.menu-collapsed ul li a i {
    color: #6b6b6b
}
}
.nav-side-menu li a i {
  padding-left: 10px;
  width: 20px;
  padding-right: 20px;
}
.nav-side-menu li:hover {
  border-left: 3px solid #d19b3d;
  background-color: #FFF;
  -webkit-transition: all 1s ease;
  -moz-transition: all 1s ease;
  -o-transition: all 1s ease;
  -ms-transition: all 1s ease;
  transition: all 1s ease;
}
@media (max-width: 767px) {
  .nav-side-menu {
    position: fixed;
    width: 280px;
    margin-bottom: 10px;
    height: 100vh;
    overflow-x: auto;
    margin-top: 50px;

    left: -280px;
    -webkit-transition: all .5s;
  -moz-transition: all .5s;
  -o-transition: all .5s;
  -ms-transition:all .5s;
  transition: all .5s;
  }
  .nav-side-menu.menu-collapsed {
    left: 0;
  }
  .nav-side-menu .toggle-btn {
    display: block;
    cursor: pointer;
    position: absolute;
    right: 10px;
    /*top: 10px;*/
    z-index: 10 !important;
    padding: 3px;
    background-color: #ffffff;
    color: #000;
    width: 40px;
    text-align: center;
  }
  .brand {
    text-align: left !important;
    font-size: 22px;
    padding-left: 20px;
    line-height: 50px !important;
  }
}
@media (min-width: 767px) {
  .nav-side-menu .menu-list .menu-content {
    display: block;
    margin-top: 50px;
  }
}
.badge{
  text-align: right;
  margin-right: 20px;
}
/*body {
  margin: 0px;
  padding: 0px;
}*/

</style>
@php
                    $ctrl = $data['ctrlName'] ?? '';
                    $mthd = $data['mthdName'] ?? '';
                    if(($ctrl == 'category') || ($ctrl == 'sub_category') || ($ctrl == 'user') || ($ctrl == 'subordinates') || ($ctrl == 'companyName') || ($ctrl == 'ticketManage')){
                        $manageShow   = 'active show'; $manageArea   = 'true'; $manageCollapse_in='in show';
                    }else{
                        $manageShow   = '';     $manageArea   = 'false';  $manageCollapse_in='';
                    }

                    if($ctrl == 'ticket'){
                        $manageShow_1   = 'active show'; $manageArea_1   = 'true'; $collapse_in='in show';
                    }else{
                        $manageShow_1   = '';     $manageArea_1   = 'false'; $collapse_in='';
                    }

                     if($ctrl =='Archive'){
                        $manageShow_2   = 'active show'; $manageArea_2   = 'true';  $ArchiveCollapse_in='in show';
                    }else{
                        $manageShow_2   = '';     $manageArea_2   = 'false'; $ArchiveCollapse_in='';
                    }

      if($mthd =='report' || $mthd == 'subordinate_user_tickets_report'){
                        $manageShow_3  = 'active show'; $manageArea_3   = 'true';  $ReportCollapse_in='in show';
                    }else{
                        $manageShow_3   = '';     $manageArea_3   = 'false'; $ReportCollapse_in='';
                    }


                @endphp

<div class="nav-side-menu">
    <!-- <div class="brand">Brand Logo</div> -->
    <!-- <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i> -->
  <!-- <br> -->
        <div class="menu-list">

            <ul id="menu-content" class="menu-content">
                <li @if(request()->is('/')) class="menu-active" @endif style="padding-top: 20px;">
                  <a href="{{ url('/') }}"><i class="menu-icon fa fa-tachometer"></i> <span class="active-menu-span">Dashboard</span></a>
                </li>

                <li  data-toggle="collapse" data-target="#products" class="" aria-expanded="">
                  <a href="#"><i class="menu-icon fa fa-paper-plane"></i><span class="active-menu-span">Requests </span><span class="arrow"></span></a>
                </li>
                <ul class="sub-menu collapse @if(request()->is('request/*')) {{ "in show" }} @endif" id="products">
                    <li @if(request()->is('request/inbox')) class="menu-active" @endif> <i class="fa fa-inbox"></i>
                        <a href="{{ url('request/inbox') }}"> Inbox
                            <span class="badge bg-secondary text-light br-5 rounded-circle pull-right">
                                @php
                                    echo DB::table('tickets')->where([['initiator_id', '=', Auth::id()], ['tStatus', '!=', 1], ['is_delete', '=', 0], ['is_viewed', 0] ])->orWhere([['now_ticket_at', '=', Auth::id()], ['tStatus', '!=', 1], ['is_delete', '=', 0], ['is_viewed', 0] ])->count();
                                @endphp
                            </span>
                        </a>
                    </li>

                    <li @if(request()->is('request/new')) class="menu-active" @endif> <i class="fa fa-plus"></i> <a href="{{ url('request/new') }}"> New</a></li>

                     <li @if(request()->is('request/pending')) class="menu-active" @endif><i class="fa fa-balance-scale"></i>
                         <a href="{{ url('request/pending') }}">My request Status
                            <span class="badge bg-secondary text-light br-5 rounded-circle pull-right">
                                @php
                                    echo DB::table('tickets')->whereIn('tStatus',[2,6,11,7,8,10])->where('initiator_id', '=',Auth::id())->count();
                                @endphp
                            </span>
                         </a>
                     </li>

                    <li @if(request()->is('request/drafts')) class="menu-active" @endif><i class="fa fa-file"></i>
                        <a href="{{ url('request/drafts') }}">Drafts
                            <span class="badge bg-secondary text-light br-5 rounded-circle pull-right">
                                @php
                                    echo DB::table('tickets')->where('tStatus', '=', 1)->where('initiator_id', '=',Auth::id())->count();
                                @endphp
                            </span>
                        </a>
                    </li>

                    <li @if(request()->is('request/request_info')) class="menu-active" @endif><i class="fa fa-id-card-o"></i>
                        <a href="{{ url('request/request_info') }}">Request for Info
                            <span class="badge bg-secondary text-light br-5 rounded-circle pull-right">
                                @php
                                    echo DB::table('tickets')->whereIn('tStatus',[6,11])->Where('now_ticket_at','=',Auth::id())->count();
                                @endphp
                            </span>
                        </a>
                    </li>

                    <li @if(request()->is('request/approved')) class="menu-active" @endif><i class="fa fa-check-square-o"></i>
                        <a href="{{ url('request/approved') }}">Approved
                            <span class="badge bg-secondary text-light br-5 rounded-circle pull-right">
                                @php
                                    echo DB::table('tickets')->where('tStatus', '=', 4)->where('initiator_id', '=',Auth::id())->count();
                                @endphp
                            </span>
                        </a>
                    </li>

                    <li @if(request()->is('request/rejected')) class="menu-active" @endif><i class="fa fa-times"></i>
                        <a href="{{ url('request/rejected') }}">Rejected
                            <span class="badge bg-secondary text-light br-5 rounded-circle pull-right">
                                @php
                                    echo DB::table('tickets')->where([['tStatus', '=', 5], ['is_viewed', 0]])->where('initiator_id', '=',Auth::id())->count();
                                @endphp
                            </span>
                        </a>
                    </li>
                </ul>

                <li @if(request()->segment(1) == 'vacations') class="menu-active" @endif>
                    <a href="{{ route('vacations.index') }}">
                        <i class="menu-icon fa fa-fire"></i> <span class="active-menu-span">Vacation</span>
                    </a>
                </li>

                @if(auth()->user()->user_type == 1)
                    <li data-toggle="collapse" data-target="#service" class="" aria-expanded="">
                      <a href="#"><i class="menu-icon fa fa-cogs"></i><span class="active-menu-span"> Manage </span> <span class="arrow"></span></a>
                    </li>
                    <ul class="sub-menu collapse  @if(request()->is('get_manage_tickets') || request()->segment(1) == 'company' || request()->is('reassign') || request()->segment(1) == 'category' || request()->segment(1) == 'sub-category' || request()->segment(1) == 'users' || request()->is('subordinate-users')) {{ "in show" }} @endif" id="service">
                        <li @if(request()->is('get_manage_tickets')) class="menu-active" @endif><a href="{{ route('get_manage_tickets') }}">Tickets</a></li>
                        <li @if(request()->segment(1) == 'company') class="menu-active" @endif>  <a href="{{ url('company/list') }}">Company
                             <span class="badge bg-secondary text-light br-5 rounded-circle pull-right">
                                 @php
                            echo DB::table('company_name')->count();
                                 @endphp
                             </span>
                            </a></li>
                        <li @if(request()->is('reassign')) class="menu-active" @endif>  <a href="{{ url('reassign') }}">Reassign

                                    </a></li>

{{--                        <li @if($mthd =='dns') class="active" @endif >  <a href="{{ url('server-dns') }}">DNS</a></li>--}}

                        <li @if(request()->segment(1) == 'category') class="menu-active" @endif>  <a href="{{ url('category') }}">Department
                         <span class="badge bg-secondary text-light br-5 rounded-circle pull-right">
                             @php
                        echo DB::table('categorys')->count();
                             @endphp
                         </span>
                        </a></li>
                        <li @if(request()->segment(1) == 'sub-category') class="menu-active" @endif>  <a href="{{ url('sub-category') }}">Unit/Section
                         <span class="badge bg-secondary text-light br-5 rounded-circle pull-right">
                                 @php
                        echo DB::table('sub_categorys')->count();
                             @endphp
                         </span>
                        </a></li>
                        <li @if(request()->segment(1) == 'users') class="menu-active" @endif>  <a href="{{ url('users') }}">Users
                             <span class="badge bg-secondary text-light br-5 rounded-circle pull-right">
                                    @php
                            echo DB::table('users')->count();
                                 @endphp

                             </span>
                        </a></li>
                        <li @if(request()->is('subordinate-users')) class="menu-active" @endif>
                            <a href="{{ route('subordinate.users.index') }}">
                                Subordinate Users
                            </a>
                        </li>
                    </ul>
                @endif

                <li data-toggle="collapse" data-target="#new" class=""  aria-expanded="">
                  <a href="#"><i class="menu-icon fa fa-archive  "></i><span class="active-menu-span">Archive</span> <span class="arrow"></span></a>
                </li>
                <ul class="sub-menu collapse @if(request()->is('archive/*')) {{ 'in show' }} @endif" id="new">
                    <li @if(request()->is('archive/index')) class="menu-active" @endif>
                        <a href="{{ url('archive/index') }}">Live
                            <span class="badge bg-secondary text-light br-5 rounded-circle pull-right">
                                @php
                                    echo DB::table('tickets')->where('tStatus', '=', 4)->where('initiator_id', '=',Auth::id())->count();
                                @endphp
                            </span>
                        </a>
                    </li>

                   <li @if(request()->is('archive/archive_search')) class="menu-active" @endif>
                       <a href="{{ url('archive/archive_search') }}">Archive
                           <span class="badge bg-secondary text-light br-5 rounded-circle pull-right">
                               @if(Auth::user()->user_type==1)
                                   @php
                                       echo DB::table('arc_ticket')->where('tStatus', '=', 4)->count();
                                   @endphp
                               @else
                                   @php
                                       echo DB::table('arc_ticket')->where('tStatus', '=', 4)->where('initiator_id', '=',Auth::id())->count();
                                   @endphp
                               @endif
                           </span>
                        </a>
                   </li>
                </ul>


                <li data-toggle="collapse" data-target="#report">
                    <a href="#"><i class="menu-icon fa fa-book"></i><span class="active-menu-span"> Report </span> <span class="arrow"></span></a>
                </li>
                <ul class="sub-menu collapse @if(request()->is('initiat_report_search') || request()->is('report_search') || request()->is('audit_report') || request()->is('department_report') || request()->is('subordinate-user-tickets')) {{ 'in show' }} @endif" id="report">
                    <li @if(request()->is('initiat_report_search')) class="menu-active" @endif><a href="{{ url('initiat_report_search') }}"> Initiated Request Report</a></li>
                    <li @if(request()->is('report_search')) class="menu-active" @endif><a href="{{ url('report_search') }}"> Recommendation/Approval</a></li>

                    @if((Auth::user()->user_type == 1) || (Auth::user()->user_type == 2))
                        <li @if(request()->is('audit_report')) class="menu-active" @endif><a href="{{ url('audit_report') }}"> Audit Report</a></li>
                    @endif

                    @if((Auth::user()->user_type == 3))
                        <li @if(request()->is('department_report')) class="menu-active" @endif><a href="{{ url('department_report') }}">Audit Report</a></li>
                    @endif

                    <li @if(request()->is('subordinate-user-tickets')) class="menu-active" @endif><a href="{{ route('subordinate.users.tickets') }}">Subordinate Users Report</a></li>
                </ul>

                @if(auth()->user()->user_type == 1)
                    @if(config('custom.settings.authentication') == 'ldap')
                        <li data-toggle="collapse" data-target="#ldap" class=" {{ $manageShow_3 }}" aria-expanded="{{$manageShow_3}}">
                            <a href="#"><i class="menu-icon fa fa-bar-chart"></i><span class="active-menu-span"> AD Users Import </span> <span class="arrow"></span></a>
                        </li>
                        <ul class="sub-menu collapse @if(request()->is('ldap/*')) {{ 'in show' }} @endif" id="ldap">
                            <li @if(request()->is('ldap/manual-import')) class="menu-active" @endif>
                                <a href="{{ route('ldap.manual') }}">
                                    <i class="menu-icon fa fa-users"></i>
                                    <span class="active-menu-span">Import Manual</span>
                                </a>
                            </li>
                            <li @if(request()->is('ldap/auto-import')) class="menu-active" @endif>
                                <a href="{{ route('ldap.auto') }}">
                                    <i class="menu-icon fa fa-users"></i>
                                    <span class="active-menu-span">Import Automatically</span>
                                </a>
                            </li>
                        </ul>
                    @endif

                    <li class="@if(str_contains(request()->url(), '/audit_logs')) menu-active @endif">
                        <a href="{{ route('auditLogs.index') }}">
                            <i class="menu-icon fa fa-history"></i> <span class="active-menu-span">Audit Logs</span>
                        </a>
                    </li>

                    <li class="@if(request()->is('setting')) menu-active @endif">
                        <a href="{{ route('setting.index') }}">
                            <i class="menu-icon fa fa-gear"></i> <span class="active-menu-span">Settings</span>
                        </a>
                    </li>

                    <li data-toggle="collapse" data-target="#backups">
                        <a href="#"><i class="menu-icon fa fa-database"></i><span class="active-menu-span"> Backups </span> <span class="arrow"></span></a>
                    </li>
                    <ul class="sub-menu collapse @if(request()->is('scheduler/*') || request()->is('backup_db')) {{ 'in show' }} @endif" id="backups">
                        <li @if(request()->is('scheduler/full-backup')) class="menu-active" @endif><a href="{{ route('scheduler.fullbackup') }}"> <i class="menu-icon fa fa-database "></i><span class="active-menu-span">Auto Full Backup</span></a></li>
                        <li @if(request()->is('scheduler/db-backup')) class="menu-active" @endif><a href="{{ route('scheduler.dbbackup') }}"> <i class="menu-icon fa fa-database "></i><span class="active-menu-span">Auto DB Backup</span></a></li>
                        <li @if(request()->is('backup_db')) class="menu-active" @endif><a href="{{ url('backup_db') }}"> <i class="menu-icon fa fa-database "></i><span class="active-menu-span">Manual Backups</span></a></li>
                    </ul>
                @endif


				<li data-toggle="collapse" data-target="#user_manual">
				  <a href="#"><i class="menu-icon fa fa-archive  "></i><span class="active-menu-span">User Manual</span> <span class="arrow"></span></a>
				</li>
				<ul class="sub-menu collapse" id="user_manual">
					@if(Auth::user()->user_type==1)
                        @if(\Illuminate\Support\Facades\Storage::disk('public')->has('setting/user_manual(admin).pdf'))
                            <li @if($mthd =='report1') class="menu-active" @endif><a target="_blank" href="{{ url('/storage/setting/user_manual(admin).pdf') }}"> User Manual (Admin)</a></li>
                        @endif
					@endif
                        @if(\Illuminate\Support\Facades\Storage::disk('public')->has('setting/user_manual(user).pdf'))
                            <li @if($mthd =='report1') class="menu-active" @endif><a target="_blank" href="{{ url('/storage/setting/user_manual(user).pdf') }}"> User Manual (User)</a></li>
                        @endif
				</ul>

              <!--    <li>
                  <a href="#">
                  <i class="fa fa-users fa-lg"></i> Users
                  </a>
                </li> -->

                {{--@if(config('custom.settings.authentication') == 'database')
                    <li @if(request()->is('change-password')) class="menu-active" @endif><a href="{{ route('change.password.index') }}"> <i class="menu-icon fa fa-lock "></i>Change Password</a></li>
                @endif--}}
            </ul>
     </div>

 <script type="text/javascript">
        function importUser(){
              $('#loader').show();
        }
    </script>

</div>

</div>
