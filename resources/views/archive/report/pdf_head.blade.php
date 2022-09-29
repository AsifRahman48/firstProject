<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Ams23 Automation</title>
</head>
<body>
	<style type="text/css" media="all">
	
	</style>
	<table style="width: 100%">
	<tr>
		<td style="width: 120px;"> <img src="{{ asset('upload/company') }}/{{$comapnyInfo->logo}}" alt="Logo" style="width: 160px; height: 80px;"></td>
		<td style="text-align: right;">
			<p style="font-size: 28px;"><b>{{$comapnyInfo->name}}</b><br>
				<sapn style="font-size: 18px; font-family: sans-serif;">
						{{$category->name}}
				</sapn><br>
				<span  style="font-size: 24px; font-family: sans-serif;">
					Office Memorandum
				</span>
			</p>
		
		</td>
	</tr>

</table>
<table style="width: 100%; border-bottom:1px #EEE solid;   border-top:1px #EEE solid">
	<tr>
		<td style="font-size: 12px; text-align: left; ">
			<b>Subject : </b> {{$TicketInfo->tSubject}}
		</td>
	</tr>
	<tr>
		
		
		<td style="font-size: 12px; text-align: left; ">
			<b>Date : </b> {{date('d M Y')}}
		</td>
		<!-- <td></td> -->

	</tr>
</table>
