<!DOCTYPE html>
<html>
<head>
	<title>Rewquest Notification</title>
	

	<style type="text/css">
	.bodyClass{
/*background-color: #efeff5;*/
min-height: 300px;
/*text-align: center;*/
padding: 20px;
	}
	.textCot{
		font-size: 16px; 
		color: #000000
	


		}
	</style>

	
</head>
<body>
	<div class="bodyClass">
		<p class="textCot">Dear {{$name}},<br>
		Your request for {{$subject}} has been rejected <br><br>
		Please visit the below link to view details for necessary action
		</p>

	<a href="{{ url($URL) }}">{{ url($URL) }}</a>
	<br>
	<br>
	<b>Regards</b>
	<br>
	<h2>AMS Notification</h2>
	<!-- <h2>Approval Management System</h2> -->
	<!-- <img src="http://ams.psgbd.com/ElaAdmin/images/logo.png" alt="Logo" style="height: 90px; width: 250px;"> -->
 <!-- <img src="{{ asset('ElaAdmin/images/logo.png') }}" alt="Logo" style="height: 90px; width: 250px;"> -->
	</div>

</body>
</html>