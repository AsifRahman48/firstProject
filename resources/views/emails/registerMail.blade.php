<!doctype html>
<html lang="en">
<head>
    <title>Temporary Password</title>
</head>
<body>
<h4>Hi {{ $name }},</h4>
<p>Your account has been created successfully. Please find your login details below.</p>
<p><b>URL:</b> <a href="{{ route('login') }}" target="_blank">{{ route('login') }}</a></p>
<p><b>Username:</b> {{ $username }}</p>
<p><b>Password:</b> {{ $password }}</p>
</body>
</html>
