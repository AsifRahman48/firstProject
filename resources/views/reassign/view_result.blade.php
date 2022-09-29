
<link href="{{ asset('select2/select2.min.css')}}" rel="stylesheet" />
<script src="{{ asset('select2/select2.min.js')}}"></script>


<div class="card " >
	<div class="card-header bg-dark text-white">
	    <strong>Pending Request</strong> 
	</div>
<div class="card-body card-block table-responsive" id="result">
@if(empty($data['pending']))
 <h2 class="text-center " style="color: #CCC">Result Not Found</h2>  
@else
		<table class="table table-bordered table-striped table-hover bootstrap-data-table text-center">
			
			<thead>
			<tr>
				<td>Sl</td>
				<td>Reference No</td>
				<td>Subject</td>
				<td>Status</td>
				<td class="action">Action</td>
			</tr>
			</thead>
		
		<tbody>
			  @php
        $sn = 1;
        @endphp
 @foreach($data['pending'] as $key => $value)
			<tr>
				<td>{{$sn}}</td>
				<td style="max-width: 200px;">{{$value->tReference_no}}</td>
				<td style="max-width: 200px;">{{$value->tSubject}}</td>
				 <td> {{ $statusResult[$value->tStatus] }}</td>
				<!-- <td> -->
							<td style="max-width: 350px;">

<form name="PR" id="PR{{$sn}}" class="d-none">
	 <div class="row form-group  ">
	 	  <div class="col-md-12 d-flex">    
	 	  	  

      <select id="PRU{{$sn}}" name="user_id" class="form-control select2" style="width: 90%; font-size:10px; " onchange="return confirm('Are you sure you want to forward this request?')?callActionFunction('PR',{{$sn}}):'';" required="1">;
                                             <option value="">Search User</option>
                                       
                                         </select>
<input type="hidden" id="PRR{{$sn}}" name="requestID" value="{{$value->id}}">
                        
                              
                                    </div>
                                    </div>

</form>

					<div id="PRB{{$sn}}">
						<button class="btn btn-sm btn-info"  onclick="viewUserList('PR',{{$sn}},{{$value->id}})">Forward</button>
					</div>
				<!-- </td> -->
				</td>
			</tr>
			 @php
        $sn++;
        @endphp
        @endforeach  
		</tbody>
		</table>

		@endif

</div>
</div>

<div class="card" >
	<div class="card-header  bg-info text-white">
	    <strong>Initiated Request </strong> 
	</div>
<div class="card-body card-block table-responsive" id="result">
@if(empty($data['Initiat']))
 <h2 class="text-center " style="color: #CCC">Result Not Found</h2>  
@else
		<table class="table table-bordered table-striped table-hover bootstrap-data-table text-center">
			
			<thead>
			<tr>
				<td>Sl</td>
				<td>Reference No</td>
				<td>Subject</td>
				<td>Status</td>
				<td class="action">Action</td>
			</tr>
			</thead>
		
		<tbody>
			  @php
        $sn = 1;
        @endphp
 @foreach($data['Initiat'] as $key => $value)
			<tr>
				<td>{{$sn}}</td>
				<td style="max-width: 200px;">{{$value->tReference_no}}</td>
				<td style="max-width: 200px;">{{$value->tSubject}}</td>
				 <td > {{ $statusResult[$value->tStatus] }}</td>
				<td style="max-width: 350px;">

<form name="IN" id="IN{{$sn}}" class="d-none">
	 <div class="row form-group  ">
	 	  <div class="col-md-12 d-flex">    
	 	  	  

      <select id="INU{{$sn}}" name="user_id" class="form-control select2" style="width: 90%; font-size:10px; " onchange="return confirm('Are you sure you want to forward this request?')?callActionFunction('IN',{{$sn}}):'';" required="1">;
                                             <option value="">Search User</option>
                                       
                                         </select>
<input type="hidden" id="INR{{$sn}}" name="requestID" value="{{$value->id}}">
                        
                              
                                    </div>
                                    </div>

</form>

					<div id="INB{{$sn}}">
						<button class="btn btn-sm btn-info"  onclick="viewUserList('IN',{{$sn}},{{$value->id}})">Forward</button>
					</div>
				</td>
			</tr>
			 @php
        $sn++;
        @endphp
        @endforeach  
		</tbody>
		</table>

		@endif

</div>
</div>



<!-- <div class="card" >
	<div class="card-header  bg-dark text-white">
	    <strong>Circle Request</strong> 
	</div>
<div class="card-body card-block table-responsive" id="result">

		<table class="table table-bordered table-striped table-hover bootstrap-data-table text-center">
			
			<thead>
			<tr>
				<td>Sl</td>
				<td>Reference No</td>
				<td>Subject</td>
				<td>Status</td>
				<td>Action</td>
			</tr>
			</thead>
		
		<tbody>
			  @php
        $sn = 1;
        @endphp
 @foreach($data['circle'] as $key => $value)
			<tr>
				<td>{{$sn}}</td>
				<td style="max-width: 200px;">{{$value->tReference_no}}</td>
				<td style="max-width: 200px;">{{$value->tSubject}}</td>
				 <td> {{ $statusResult[$value->tStatus] }}</td>
				<td style="max-width: 3500px;">
					<!-- <div id="CR{{$sn}}">
						<button class="btn btn-sm btn-info">Forward</button>
					</div> -->
				<!--</td>
			</tr>
			 @php
        $sn++;
        @endphp
        @endforeach  
		</tbody>
		</table>

</div>
</div> -->

    <script type="text/javascript">
    	       $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
      function callActionFunction(divName,divId){
            var forwardUser=$('#'+divName+'U'+divId).val();
            // alert(forwardUser);
            var forwardRequest=$('#'+divName+'R'+divId).val();
            if(divName=='IN'){
            var requestType='Initiat';
            var requestRoute="{{ URL::to('/update_assignment')}}"
            }
            if(divName=='PR'){
            var requestType='Pending';
            var requestRoute="{{ URL::to('/update_assignment')}}"
            }


              $.ajax({ 
               url: requestRoute,
               type: "POST",
               dataType: 'json',
               data:{'user':forwardUser,'request_id':forwardRequest,'requestType':requestType},
               success: function (data) {  
            // alert(data);
            if(data=='100'){
            	     $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
               $("#success-alert").slideUp(500);
                });
                searchRequestInfo();

                 
            }
               }
              });
        
            }
        
                 $(document).ready(function(){
 
				 $(".select2").select2({
				    minimumInputLength: 2,
				  ajax: { 
				   url: "{{ URL::to('/search_ad_user')}}",
				   type: "POST",
				   dataType: 'json',
				   delay: 10,
				   data: function (params) {
				    return {
				      term: params.term // search term
				    };
				   },
				   processResults: function (data) {  
				  var results = [];
				    $.each(data, function (index, account) {
				          var nameInfo=account.name+'->'+account.title+'->'+account.department+'->'+account.email+'->'+account.company_name;
if(account.id!=@php echo Auth::id() @endphp){
				        results.push({
				            id: account.id,
				            text: nameInfo
				        });
				    }
				    });

				    return {
				        results: results
				    };
				   }
				  }
				 });

});

</script>
<link href="{{ asset('select2/select2.min.css')}}" rel="stylesheet" />
<script src="{{ asset('select2/select2.min.js')}}"></script>
