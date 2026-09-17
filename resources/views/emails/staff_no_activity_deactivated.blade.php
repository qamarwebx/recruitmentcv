<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Account Deactivated</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f5f5f5; padding:20px;">

<div style="max-width:600px; margin:0 auto; background:white; padding:25px; border-radius:8px;">

    <h2 style="color:#d9534f;">Account Deactivated</h2>

    <p>Dear {{ $user->name }},</p>

    <p>
        We noticed that there was <strong>no activity recorded today ({{ $todayDate }})</strong> 
        in your assigned tasks.
    </p>

    <p>
        As per company policy, due to inactivity your account has been temporarily 
        <strong style="color:#d9534f;">deactivated</strong>.
    </p>

    <h3 style="margin-top:20px; color:#333;">Your Assigned Tasks for Today</h3>

    @if(!empty($todoIds))
        <ul>
            @foreach ($todoIds as $taskId)
                <li style="margin-bottom:6px;">
                    @php
                        $baseUrl = env('APP_ENV') === 'production'
                            ? 'https://qamarhire.test'
                            : 'https://crm.qamarhire.com';
                    @endphp

                    <a href="{{ $baseUrl }}/admin/todo?open_todo_model=true&todo_model_task_id={{ $taskId }}"
                       style="color:#0275d8; text-decoration:none; font-weight:600;">
                        View Task #{{ $taskId }}
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <p>No tasks assigned for today.</p>
    @endif

    <p style="margin-top:25px;">
        If you believe this action is incorrect, kindly reach out to the system admin 
        to reactivate your account.
    </p>

    <p>Thank you,<br>System Automation</p>
</div>

</body>
</html>
