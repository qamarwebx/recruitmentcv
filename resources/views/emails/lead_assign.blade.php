<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Lead Assigned</title>
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
        .lead-details {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .lead-details th,
        .lead-details td {
            border: 1px solid #dddddd;
            padding: 10px 15px;
            text-align: left;
        }
        .lead-details th {
            background-color: #f4f6f8;
            width: 35%;
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
            margin-top: 15px;
            background-color: #4a90e2;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .btn-container{
            text-align:center;
            margin-top:20px;
        }
    </style>
</head>
<body>

<div class="email-container">

    <div class="header">
        <h1>New Lead Assigned</h1>
    </div>

    <div class="content">

        <p>Hi {{ $admin->name }},</p>

        <p>A new lead has been assigned to you. Please review the details below.</p>

        <table class="lead-details">

            <tr>
                <th>Candidate Name</th>
                <td>{{ $lead->cand_name ?? '--' }}</td>
            </tr>

            <tr>
                <th>Mobile Number</th>
                <td>{{ $lead->mob_no ?? '--' }}</td>
            </tr>

            <tr>
                <th>WhatsApp Number</th>
                <td>{{ $lead->whatsapp_no ?? '--' }}</td>
            </tr>

            <tr>
                <th>Job Title</th>
                <td>{{ $lead->required_service ?? '--' }}</td>
            </tr>

            <tr>
                <th>Experience</th>
                <td>{{ $lead->experience ?? '--' }}</td>
            </tr>

            <tr>
                <th>Expected Country</th>
                <td>{{ $lead->country ?? '--' }}</td>
            </tr>

            <tr>
                <th>Driving License</th>
                <td>
                    @php
                        $licenses = [];

                        if(($lead->saudi_license ?? '') == 'yes'){
                            $licenses[] = 'Saudi License';
                        }

                        if(($lead->india_license ?? '') == 'yes'){
                            $licenses[] = 'Indian License';
                        }
                    @endphp

                    {{ count($licenses) ? implode(', ', $licenses) : '--' }}
                </td>
            </tr>

            <tr>
                <th>Lead Date</th>
                <td>{{ optional($lead->lead_date)->format('d M Y h:i A') ?? '--' }}</td>
            </tr>

            @if(!empty($lead->message))
            <tr>
                <th>Message</th>
                <td>{{ $lead->message }}</td>
            </tr>
            @endif

        </table>

        <p>Please contact the candidate as soon as possible and update the CRM accordingly.</p>

    </div>

    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>

</div>

</body>
</html>