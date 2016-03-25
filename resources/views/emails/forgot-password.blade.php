<html>
<head>
	<title>HTML email</title>
</head>
<body>
	<p>We received a request to reset the password for your account. If you made this request, click the link below. If you didn't make this request, you can ignore this email.</p>
	<p>Reset your password</p>	

	<p> <a href="{{URL::to('/')}}/api/password-reset/{{ $token }}">{{ URL::to('/') }}/api/password-reset/{{ $token }}</a> </p>
</body>
</html>