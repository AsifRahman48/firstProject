<!DOCTYPE html>
<html>
<head>

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

  <title>Partex Star Automation</title>

   	<style type="text/css" media="all">
        /**
        * Set the margins of the PDF to 0
        * so the background image will cover the entire page.
        **/
        @page {
        	/*size: auto;*/
            margin: 4.8cm 0.5cm 1.5cm 0.5cm;
			padding: 0cm 0cm;
			header: page-header;
			footer: page-footer;
        }

        /**
        * Define the real margins of the content of your PDF
        * Here you will fix the margins of the header and footer
        * Of your background image.
        **/
        body {
        	/*padding: 2cm 0.5cm 2cm 0.5cm;*/
            font-family: "bangla", sans-serif;
        }


		#pagehead_wrapper{
			width: 100%;
			/*margin-bottom: 3px;*/
			padding-top: 5px;
			margin-bottom: 15px;
			/*padding-bottom: 30px;*/
		}

		#pagehead{
		  	/*position: fixed;*/

		  	/*height: 1cm;*/
		   	/*left:     0px;*/
		  	/*border: 1px gray solid;*/
		  	/*float: left;*/
		  	/*z-index:  -1000;*/
		  	/*margin-top: 30px;*/
		  	/*padding-bottom: 40px;*/
		  	margin-bottom: 10px;
		  	padding-bottom: 0px;
		  	/*line-height: auto;*/
		  	/*height:auto;*/

		}
        #HeaderImage{
          width: 30%;
           height: auto;
          /*border: 1px blue solid;*/
          float: left ;
        }

         #HeaderContent{
          width: 70%;
          /*height: 3cm;*/
          /*border: 1px blue solid;*/
          float: right;
          text-align: right;
        }

        #subjectContent{
          /*position: fixed;*/
          width: 100%;
          /*height: 3cm;*/
          /*left:     0px;*/
          border-top: 1px solid #000000;
          border-bottom: 1px solid #000000;
          /*float: left;*/
          /*z-index:  -1000;*/
          margin-top: 0px;
          margin-bottom: 3px;
          padding-top: 10px;
          padding-bottom: 10px;
          font-size: 12px;
        }

        #page_wrapper{
        	/*padding: 0px 0px 50px 0px;*/
        	/*margin: 2cm 0cm 2cm 0cm;*/
			/*margin-top:    1cm;*/
			/*margin-bottom: 2cm;
			margin-left:   0.8cm;
			margin-right:  0.8cm;*/
        }

        /**
        * Define the width, height, margins and position of the watermark.
        **/
        #watermark {
            /*position: relative;*/
            /*bottom:   0px;*/
            /*left:     0px;*/
            /** The width and height may change
                according to the dimensions of your letterhead
            **/
            width:    100%;
            /*min-height:   1cm;*/
            /*border: 1px red solid;*/
          /*max-width: 20cm;*/


            /** Your watermark should be behind every content**/

        }




		table{
			border-collapse: collapse;
			min-width: 300px;
			border: 1px solid #000000;
			width: 100%;
		}
		th{
			border: 1px solid black;
			padding: 5px;
		}
		td{
			border: 1px solid #00000;
			/*width: 100px !important;*/
		}
		.tbltb{
			border: 1px solid black;
			padding: 5px;
		}

		.thistory{
			border-collapse: collapse;
			width: 100%;
			font-size: 14px;
		}

		.reference_no{
			float: right;
			text-align: right;
			width: 50%;
			display: block;
		}

		.ref_date{
			float: left;
			text-align: left;
			width: 50%;
			display: block;
		}

		.page_footer{
			/*padding-left: 20px;*/
			/*padding-right: 20px;*/
		}

		.tinymceTable table{
			width: 100% !important;
			max-width: 100% !important;
		}

		.tinymceTable table td{
			padding: 1px;
		}

	</style>

<style>

.page-break {
    page-break-after: always;

}
</style>


</head>

<body>

<htmlpageheader name="page-header">

    <div id="pagehead_wrapper">
    	<div id="pagehead">
    		<div id="HeaderImage" style="float: left;">
    			<img src="{{ asset('upload/company') }}/{{$comapnyInfo->logo}}" height="80px" width="160px" />
    		</div>
    		<div id="HeaderContent" style="float: right;">
    		 	<p style="font-size: 17px;margin-bottom:0px;"><b>{{$comapnyInfo->name}}</b><br>
    		        <sapn style="font-size: 15px; font-family: sans-serif;">
    		            {{$category->name}}
    		        </sapn><br>
    		        <span  style="font-size: 14px; font-family: sans-serif;">
    		          	Office Memorandum
    		        </span>
    		    </p>
    		</div>
    	</div>

    	<div id="subjectContent">
    	  	<p style="margin-bottom:2px;margin-top:0px;"><b>Subject : </b> {{$TicketInfo->tSubject}}</p>
    	   	<div style="float: left; width: 50%;"><b>Reference No : </b>{{$TicketInfo->tReference_no}}</div>
    	   	<div style="float: right; width: 50%; text-align:right;">Date : {{date('d M Y')}}</div>
    	</div>
    </div>



</htmlpageheader>

<htmlpagefooter name="page-footer">
	<div class="page_footer">
		<div class="" style="float: left;width: 60%;">
			<span style="color:#f02;font-size: 14px;">Computer Generated Approval Note. No Signature Required.</span>
		</div>
		<div class="" style="float: right; text-align: right;width: 40%;">
			<span style="font-size: 14px;">Page {PAGENO} of {nbpg}</span>
		</div>

	</div>
	<br>
</htmlpagefooter>


<watermarktext content="{{$TicketInfo->tReference_no}}" alpha="0.2" />

<div id="page_wrapper">

	<div id="page_content">
	    <p style="font-size: 12px; text-align: justify;"> <?php echo  Html::decode(str_replace("&nbsp;", '', $TicketInfo->tDescription)); ?>
	     </p>
	</div>


</div>



</body>
