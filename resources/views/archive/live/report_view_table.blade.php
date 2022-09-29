 <link rel="stylesheet" href="{{ asset('ElaAdmin/assets/css/lib/datatable/dataTables.bootstrap.min.css') }}">
    <script src="{{ asset('ElaAdmin/assets/js/lib/data-table/datatables.min.js') }}"></script>
    <script src="{{ asset('ElaAdmin/assets/js/lib/data-table/dataTables.bootstrap.min.js') }}"></script>
    <!-- <script src="{{ asset('ElaAdmin/assets/js/lib/data-table/dataTables.buttons.min.js') }}"></script> -->
    <!-- <script src="{{ asset('ElaAdmin/assets/js/lib/data-table/buttons.bootstrap.min.js') }}"></script> -->
    <!-- <script src="{{ asset('ElaAdmin/assets/js/lib/data-table/jszip.min.js') }}"></script> -->
    <!-- <script src="{{ asset('ElaAdmin/assets/js/lib/data-table/pdfmake.min.js') }}"></script> -->
    <!-- <script src="{{ asset('ElaAdmin/assets/js/lib/data-table/vfs_fonts.js') }}"></script> -->
    <!-- <script src="{{ asset('ElaAdmin/assets/js/lib/data-table/buttons.html5.min.js') }}"></script> -->
    <!-- <script src="{{ asset('ElaAdmin/assets/js/lib/data-table/buttons.print.min.js') }}"></script> -->
    <!-- <script src="{{ asset('ElaAdmin/assets/js/lib/data-table/buttons.colVis.min.js') }}"></script> -->
    <!-- <script src="{{ asset('ElaAdmin/assets/js/lib/data-table/datatables-init.js') }}"></script> -->
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
    

/*    .table-responsive {
    display: table;
}*/
</style>
<div  class="mr-0" style="padding: 0px; overflow-x:scroll;" >
<table class="table table-bordered table-striped table-hover bootstrap-data-table">
    <thead>
          <tr style="font-size: 12px; text-align: center;">
            <th class="serial"><input type="checkbox" class="select_all"></th>
            <th>Action</th>
            <th>Reference No</th>
            <th>Initiator Name</th>
            <th>Department</th>
            <th>Unit/Section</th>
            <th>Subject</th>
            <!-- <th>Description</th> -->
            <!-- <th>history</th>                                   -->
            <th>Status</th>
            <!-- <th>Date</th> -->
      
          
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
            <td>{{$sn}}  &nbsp;<br>&nbsp;<br><input type="checkbox" name="arc_id[]" value="{{$value->id}}" class="checkbox"></td>
              <td style="min-width: 140px;">
              <div class="Infobutton">

                                        <a href="{{ url('archive/archive/'.$value->id) }}">
                                                <button type="button" class="btn btn-success btn-sm"> Archive</button>
                                            </a>   
                                            <a href="{{ url('/archive/live_acknowledgement_view/'.$value->id) }}">
                                                <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-info-circle"></i> Details</button>
                                            </a>
                                           
                                   
</div>

                                  
                                        </td>
            <td>{{$value->tReference_no}}</td>
            <td>{{$value->CreatorName}}</td>
            <td>{{$value->categorysName}}</td>
            <td>{{$value->sub_categorysName}}</td>
            <td>{{$value->tSubject}}</td>
              <!-- <td></td> -->
           <!--  <td>

         </td> -->
         
             <td> {{ $statusResult[$value->tStatus] }}</td>
             <!-- <td>{{date('d-M-y', strtotime($value->created_at))}}</td> -->
           
                <td id="{{$sn}}">
              <div id="div{{$sn}}"  class="historyDiv" >
                @if(!empty($value->thistory))
                 @php $hasan=json_decode($value->thistory,true); @endphp
                   <ul>
                     @foreach($hasan as $key => $HistoryInfo)
                    <li>{{$key+1}} => {{$HistoryInfo["user_name"]}}-{{$HistoryInfo["user_type"]}}-{{$HistoryInfo["user_status"]}}</li>
                       @endforeach

                     </ul>
                    @endif
                 </div>
            @if(!empty($value->thistory))
<button id="btn{{$sn}}" class="fa fa-eye text-success" aria-hidden="true" onclick="historyFullView({{$sn}})"></button>
<button id="btnS{{$sn}}" class="fa fa-eye-slash text-danger hideDiv" aria-hidden="true" onclick="historyFullViewClose({{$sn}})"></button>
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
<br>
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
     $(':input[name="save"]').prop('disabled', true);
 $('.select_all').on('click',function(){
        if(this.checked){
            $('.checkbox').each(function(){
                this.checked = true;
                 $(':input[name="save"]').prop('disabled', false);
                  $('.Infobutton').hide();

            });
        }else{
             $('.checkbox').each(function(){
                this.checked = false;
                 $(':input[name="save"]').prop('disabled', true);
                       $('.Infobutton').show();
            });
        }
    });
    
    $('.checkbox').on('click',function(){
        if($('.checkbox:checked').length == $('.checkbox').length){
            $('.select_all').prop('checked',true);
             $(':input[name="save"]').prop('disabled', false);
              $('.Infobutton').hide();
        }else{
            $('.select_all').prop('checked',false);
             $(':input[name="save"]').prop('disabled', true);
        }
    });


 $('.hideDiv').hide();
  $('.bootstrap-data-table').DataTable();

});
    </script>