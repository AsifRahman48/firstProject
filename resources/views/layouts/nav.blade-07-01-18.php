
<aside id="left-panel" class="left-panel">
    <style type="text/css">
  
        .active a{
    color: green !important;
}
    </style>
    <nav class="navbar navbar-expand-sm navbar-default">
        <div id="main-menu" class="main-menu collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="{{ url('/') }}"><i class="menu-icon fa fa-laptop"></i>Dashboard </a></li>

                @php
                    $ctrl = $data['ctrlName'] ?? '';
                    $mthd = $data['mthdName'] ?? '';
                    if(($ctrl == 'category') || ($ctrl == 'sub_category') || ($ctrl == 'user') || ($ctrl == 'companyName')){
                        $manageShow   = 'show'; $manageArea   = 'true';
                    }else{
                        $manageShow   = '';     $manageArea   = 'false';
                    }

                    if($ctrl == 'ticket'){
                        $manageShow_1   = 'show'; $manageArea_1   = 'true';
                    }else{
                        $manageShow_1   = '';     $manageArea_1   = 'false';
                    }

                     if($ctrl =='Archive'){
                        $manageShow_2   = 'show'; $manageArea_2   = 'true';
                    }else{
                        $manageShow_2   = '';     $manageArea_2   = 'false';
                    }
                @endphp

               
                <!-- <li class="menu-title">Initiator</li> -->
                <li class="menu-item-has-children dropdown {{ $manageShow_1 }}">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="{{ $manageArea_1 }}"> <i class="menu-icon fa fa-file-text-o"></i>Requests</a>
                    <ul class="sub-menu children dropdown-menu {{ $manageShow_1 }}">
                        <li @if($mthd =='inbox') class="active" @endif><i class="fa fa-inbox"></i><a href="{{ url('request/inbox') }}">Inbox
                          

                            <span class="badge bg-secondary text-light br-5 rounded-circle"> 
                             @php
                        echo DB::table('tickets')->where('initiator_id', '=',Auth::id())->orWhere('now_ticket_at','=',Auth::id())->count();
                             @endphp
                         </span>
                        </a></li>
                        <li @if($mthd =='new') class="active" @endif><i class="fa fa-plus"></i><a href="{{ url('request/new') }}">New
                            <!-- <span class="badge bg-secondary text-light br-5 rounded-circle">42</span> -->
                        </a></li>
                        <li @if($mthd =='pending') class="active" @endif><i class="fa fa-balance-scale"></i><a href="{{ url('request/pending') }}">My request Status 
                            <span class="badge bg-secondary text-light br-5 rounded-circle">  @php
                        echo DB::table('tickets')->whereIn('tStatus',[2,6,11,7,8,10])->where('initiator_id', '=',Auth::id())->count();
                             @endphp
                         </span></a></li>
                        <li @if($mthd =='draft') class="active" @endif><i class="fa fa-file"></i><a href="{{ url('request/drafts') }}">Drafts
                            <span class="badge bg-secondary text-light br-5 rounded-circle"> @php
                        echo DB::table('tickets')->where('tStatus', '=', 1)->where('initiator_id', '=',Auth::id())->count();
                             @endphp</span>
                        </a></li>
                        <li @if($mthd =='request_info') class="active" @endif><i class="fa fa-id-card-o"></i><a href="{{ url('request/request_info') }}">Request for Info
                            <span class="badge bg-secondary text-light br-5 rounded-circle"> @php
                        echo DB::table('tickets')->whereIn('tStatus',[6,11])->Where('now_ticket_at','=',Auth::id())->count();
                             @endphp</span>
                        </a></li>
                        <li @if($mthd =='approved') class="active" @endif><i class="fa fa-check-square-o"></i><a href="{{ url('request/approved') }}">Approved
                            <span class="badge bg-secondary text-light br-5 rounded-circle"> @php
                        echo DB::table('tickets')->where('tStatus', '=', 4)->where('initiator_id', '=',Auth::id())->count();
                             @endphp</span>
                        </a></li>
                        <li @if($mthd =='rejected') class="active" @endif><i class="fa fa-times"></i><a href="{{ url('request/rejected') }}">Rejected
                            <span class="badge bg-secondary text-light br-5 rounded-circle"> @php
                        echo DB::table('tickets')->where('tStatus', '=', 5)->where('initiator_id', '=',Auth::id())->count();
                             @endphp</span>
                        </a></li>
                    </ul>
                </li>
                @if(Auth::user()->user_type==1)
 <li class="menu-item-has-children dropdown {{ $manageShow }}">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="{{ $manageArea }}"> <i class="menu-icon fa fa-cogs"></i>Manage</a>
                    <ul class="sub-menu children dropdown-menu {{ $manageShow }}">
  <li @if($ctrl =='companyName') class="active" @endif><i class="menu-icon fa fa-angle-double-right"></i><a href="{{ url('company/list') }}">Company Name
                         <span class="badge bg-secondary text-light br-5 rounded-circle">
                             @php
                        echo DB::table('company_name')->count();
                             @endphp
                         </span>
                        </a></li>
  <li @if($mthd =='reassign') class="active" @endif ><i class="menu-icon fa fa-angle-double-right"></i><a href="{{ url('reassign') }}">Reassign 
                    
                        </a></li>

                        <li @if($ctrl =='category') class="active" @endif><i class="menu-icon fa fa-angle-double-right"></i><a href="{{ url('category') }}">Category
                         <span class="badge bg-secondary text-light br-5 rounded-circle">
                             @php
                        echo DB::table('categorys')->count();
                             @endphp
                         </span>
                        </a></li>
                        <li @if($ctrl =='sub_category') class="active" @endif><i class="menu-icon fa fa-angle-double-right"></i><a href="{{ url('sub-category') }}">Sub-category
                         <span class="badge bg-secondary text-light br-5 rounded-circle">
                                 @php
                        echo DB::table('sub_categorys')->count();
                             @endphp
                         </span>
                        </a></li>
                        <li @if($mthd =='user') class="active" @endif><i class="menu-icon fa fa-angle-double-right"></i><a href="{{ url('users') }}">Users
                             <span class="badge bg-secondary text-light br-5 rounded-circle">
                                    @php
                            echo DB::table('users')->count();
                                 @endphp

                             </span>
                        </a></li>
                    </ul>
                </li>
                @endif
                
                <li @if($mthd =='report') class="active" @endif><a href="{{ url('report_search') }}"> <i class="menu-icon fa fa-pie-chart"></i>Reports </a></li> 


  
              
  <li class="menu-item-has-children dropdown {{ $manageShow_2 }}">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="{{ $manageShow_2 }}"> <i class="menu-icon fa fa-cogs"></i>Archive</a>
                    <ul class="sub-menu children dropdown-menu {{ $manageShow_2 }}">
  <li @if($mthd =='LocalArchive') class="active" @endif><i class="menu-icon fa fa-angle-double-right"></i><a href="{{ url('archive/index') }}">Live
                         <span class="badge bg-secondary text-light br-5 rounded-circle">
                              @php
                        echo DB::table('tickets')->where('tStatus', '=', 4)->where('initiator_id', '=',Auth::id())->count();
                             @endphp
                         </span>
                        </a></li>

     <!--    <li @if($mthd =='Archive') class="active" @endif><i class="menu-icon fa fa-angle-double-right"></i><a href="{{ url('archive/archive_list') }}">Archive to Local 
                         <span class="badge bg-secondary text-light br-5 rounded-circle">
                              @php
                        echo DB::table('arc_ticket')->where('tStatus', '=', 4)->where('initiator_id', '=',Auth::id())->count();
                             @endphp
                         </span>
                        </a></li> -->
  <li @if($mthd =='ArchiveReport') class="active" @endif><i class="menu-icon fa fa-angle-double-right"></i><a href="{{ url('archive/archive_search') }}">Archive 
                     <span class="badge bg-secondary text-light br-5 rounded-circle">
                              @php
                        echo DB::table('arc_ticket')->where('tStatus', '=', 4)->where('initiator_id', '=',Auth::id())->count();
                             @endphp
                         </span>     
                        </a></li>

                      
                    </ul>
                </li>
   @if(Auth::user()->user_type==1)
  <li @if($mthd =='reportstt') class="active" @endif><a href="{{ url('ldap_info') }}" onclick="importUser()"> <i class="menu-icon fa fa-pie-chart"></i>Import All AD User </a></li>
@endif
            </ul>
        </div>
    </nav>
    <script type="text/javascript">
        function importUser(){
              $('#loader').show();
        }
    </script>
</aside>
