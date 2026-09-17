<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forget Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
</head>
<body>
    <div class="container-fluid">
        <h3>Hello!</h3>
        <p>You are receiving this email because we received a password reset request for your account.</p>
        <div class="text-canter">
            <a href="{{ route('password.reset.form',$token) }}" class="btn btn-dark">Reset Password</a>
        </div>
        <p>This password reset link will expire in 60 minutes.</p>
        <p>If you did not request a password reset, no further action is required.</p>
        <h4 class="mt-3">Regards</h4>
        <p class="mb-3">Qamar International</p>
        <hr>
        <p class="mt-3">
            If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:<a href="{{ route('password.reset.form',$token) }}">{{ route('password.reset.form',$token) }}</a>
        </p>
    </div>
</body>
</html>