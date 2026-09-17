<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Deal Reminder</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #4a90e2;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .content {
            padding: 20px;
        }
        .content p {
            line-height: 1.6;
        }
        .task-details {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .task-details th, .task-details td {
            border: 1px solid #dddddd;
            padding: 10px 15px;
            text-align: left;
        }
        .task-details th {
            background-color: #f4f6f8;
        }
        .footer {
            background-color: #f4f6f8;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #888888;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 10px;
            background-color: #4a90e2;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Deal Reminder</h1>
        </div>

        <div class="content">
            <p>Hi {{ $user->name }},</p>
            <p>This is a reminder for your deal. Please review the details below:</p>

            <table class="task-details">
                <tr>
                    <th>Deal</th>
                    <td>{{ $deal->company ?? $deal->candidate ?? 'Deal Pipeline' }}</td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td>{{ $description }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ \Carbon\Carbon::parse($deal->created_at)->format('d M Y h:i A') }}</td>
                </tr>
            </table>

            <p>Please make sure to follow up on this deal.</p>

            <div class="btn-container">
                @if(env('APP_ENV') === 'production')
                    <a href="https://qamarhire.com/admin/dealPipeline/list?open_deal_model=true&deal_model_id={{ $deal->id }}" class="btn">
                        View Deal
                    </a>
                @else
                    <a href="https://crm.qamarhire.com/admin/dealPipeline/list?open_deal_model=true&deal_model_id={{ $deal->id }}" class="btn">
                        View Deal
                    </a>
                @endif
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>