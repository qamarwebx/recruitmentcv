<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Booking Notifcation</title>
</head>
<body>
    <h5>{{ 'Dear '.$data['name'] }}</h5>
    <p>The OTP is {{ $data['email_code'] }} for booking reference no .{{ $data['reference'] }} and OTP will be expired on {{ $data['code_expired'] }}</p>
</body>
</html>