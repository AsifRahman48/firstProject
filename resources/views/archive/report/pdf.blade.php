    @include('report.pdf_head') 



<p style="font-size: 12px; text-align: justify; "> {!! Html::decode($TicketInfo->tDescription)!!} </p>
<table style="width: 100%; border-top:1px #EEE solid">
	<tr>
		
		<td style="font-size: 12px; text-align: left; ">
			<br>
			<br>
			<h4><blockquote><b  style="border:1px #000 solid; padding: 15px;">Recommender List</b></blockquote></h4>			
	<table class="recomander">
		<tr style="background-color: gray">
			<th>Name</th>
			<th>Status</th>
		</tr>
			@foreach ($recommenderList as $key => $value)
			<tr><td class="tbltb">{{ $value->name}} </td><td class="tbltb"> @if( $value->action==1)Approved @else  Pending @endif </td></tr>
			@endforeach
			</table>
		</td>
		<td style="font-size: 12px; text-align: left; ">
			<br>
			<br>
			<h4><blockquote><b  style="border:1px #000 solid; padding: 15px;">Approver List</b></blockquote></h4>
	<style type="text/css">
		.recomander,table{
 		border-collapse: collapse;
 		min-width: 300px;
		}
		.recomander,th{
  		border: 1px solid black;
  		padding: 5px;
		}
	.tbltb{
 	 border: 1px solid black;
 	 padding: 5px;
	}

.thistory{
	 border-collapse: collapse;
 width: 100%;
}
	</style>

	<table class="recomander">
		<tr style="background-color: gray">
			<th>Name</th>
			<th>Status</th>
		</tr>
			@foreach ($approverList as $key => $value)
			<tr><td class="tbltb">{{ $value->name}} </td><td class="tbltb"> @if( $value->action==1)Approved @else  Pending @endif </td></tr>
			@endforeach
			</table>
		</td>

	</tr>
</table>
	<h3>Approval Sequence</h3>	
 @if(!empty($TicketInfo->thistory))
                                          @php
                                            $hasan=json_decode($TicketInfo->thistory,true);
                                            @endphp


                                            <table class="thistory">
                                            <tr style="background-color: gray">
                                                        <th>SL</th>
                                                        <th>Name</th>                                                     
                                                       <th>Status</th>
                                                        <th>Date</th>
                                                    </tr>
                                           <tbody>
                                                       @php $sl=1; @endphp 
                                 @foreach($hasan as $key => $HistoryInfo)
                                                    <tr>
                                                        <td class="tbltb">#{{$sl++}}</td>
                                                        <td class="tbltb">
                                         {{$HistoryInfo["user_name"]}}
                                                        </td>
                                                      
                                                        <td class="tbltb">{{$HistoryInfo["user_status"]}}</td>
                                                             <td class="tbltb"> @if(isset($HistoryInfo["date"]) && !empty($HistoryInfo["date"]))
                                                       {{date('d-M-Y H:i:s', strtotime($HistoryInfo["date"]))}}
@endif</td>
                                                    </tr>
                                                      @endforeach
                                                </tbody>
                                                
                                            </table>                                      
                                       
                                          
                                            @endif

    @include('report.pdf_footer') 