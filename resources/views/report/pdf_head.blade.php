<!-- TicketInfo -->
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Partex Star Automation</title>
</head>
<body >
  <style type="text/css" media="all">
  
  </style>

  <table class="headerText" style="width: 100%">
  <tr>
    <td style="width: 120px;" class="headerTexttd"> <img src="{{ asset('upload/company') }}/{{$comapnyInfo->logo}}" alt="Logo" style="width: 160px; height: 80px;"></td>
    <td style="text-align: right;"  class="headerTexttd">
      <p style="font-size: 22px;"><b>{{$comapnyInfo->name}}</b><br>
        <sapn style="font-size: 18px; font-family: sans-serif;">
            {{$category->name}}
        </sapn><br>
        <span  style="font-size: 20px; font-family: sans-serif;">
          Office Memorandum
        </span>
      </p>
    
    </td>
  </tr>

</table>
<table  style="width: 100%; border-bottom:1px #000 solid !important;   border-top:1px #000 solid;  border-left: 0px !important; border-right: 0px !important; ">
  <tr>
    <td style="font-size: 12px; text-align: left;"  class="headerTexttd">
      <br>
      <b>Subject : </b> {{$TicketInfo->tSubject}}
    </td>
  </tr>
  <tr>
    
    
    <td style="font-size: 12px; text-align: left; " class="headerTexttd">
      <p><b>Date : </b> {{date('d M Y')}} <span style="float: right;">Reference No : {{$TicketInfo->tReference_no}}</span></p>
    
    </td>
    <!-- <td></td> -->

  </tr>
</table>

