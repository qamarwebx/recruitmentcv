<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Deal Not Updated</title>
</head>
<body>

    <h2>⚠️ Deal Not Updated</h2>

    <p>Hello,</p>

    <p>
        The following deal has not been updated for the last 6 days:
    </p>

    <p>
        <strong>Deal ID:</strong> {{ $deal->id }} <br>
        <strong>Title:</strong> 
        {{ $deal->job_title ?: $deal->job_title_other ?: $deal->deal_name ?: $deal->company ?: 'N/A' }} <br>
        <strong>Last Updated:</strong> {{ $deal->updated_at }}
    </p>

    <p>
        Please take necessary action.
    </p>

    <!-- ✅ CLICK HERE BUTTON -->
    <p style="margin-top:20px;">
        <a href="{{ url('/admin/dealPipeline/list') }}" 
           style="background:#7367f0; color:#fff; padding:10px 18px; text-decoration:none; border-radius:5px;">
           👉 Click Here to View Deals
        </a>
    </p>

    <br>

    <p>
        Regards,<br>
        <strong>System Automation</strong>
    </p>

</body>
</html>