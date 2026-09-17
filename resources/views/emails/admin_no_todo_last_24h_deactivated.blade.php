<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Account Deactivated</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f5f5f5; padding:20px;">

<div style="max-width:600px; margin:0 auto; background:white; padding:25px; border-radius:8px;">

    <h2 style="color:#d9534f;">Account Deactivated</h2>

    <p>Dear {{ $admin->name ?? 'Admin' }},</p>

    <p>
        We noticed that there was <strong>no todo Created in the last 24 hours</strong>
        ({{ $date }}).
    </p>

    <p>
        As per company policy, your account has been temporarily
        <strong style="color:#d9534f;">deactivated</strong>.
    </p>

    <p style="margin-top:25px;">
        If you believe this is a mistake, please contact the system administrator.
    </p>

    <p>
        Regards,<br>
        <strong>System Automation</strong>
    </p>

</div>

</body>
</html>
