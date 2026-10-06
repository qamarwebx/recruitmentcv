{{-- Partner Activity alert to CRM staff (App\Mail\PartnerActivityAlert). Inline styles for email clients. Twin file in both apps. --}}
@php
    $accent = ['candidate_viewed' => '#2563eb', 'hire_now' => '#16a34a', 'download_cv' => '#7c3aed'][$alert['event']] ?? '#2563eb';
    $heading = [
        'candidate_viewed' => 'A partner is viewing a candidate',
        'hire_now' => 'A partner wants to hire a candidate',
        'download_cv' => 'A partner downloaded a candidate CV',
    ][$alert['event']] ?? 'Partner Activity Alert';
    $rows = array_filter([
        'Partner' => $alert['partner_name'],
        'Mobile' => $alert['partner_mobile'],
        'City' => $alert['partner_city'],
        'Team Member' => $alert['team_member'] ?: null,
        'Candidate' => $alert['candidate_name'],
        'Candidate ID' => $alert['candidate_ref'],
        'Activity' => $alert['activity'],
        'Date / Time' => $alert['occurred_at'],
    ], fn ($value) => $value !== null);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $alert['subject'] }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f1f5f9;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e2e8f0;">
                    <tr>
                        <td style="background:#0f172a;padding:18px 24px;">
                            <span style="font-size:18px;font-weight:bold;color:#ffffff;">RecruitmentCV</span>
                            <span style="font-size:12px;color:#94a3b8;"> &middot; QAMR International</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 24px 8px;">
                            <div style="display:inline-block;background:{{ $accent }};color:#ffffff;font-size:12px;font-weight:bold;padding:4px 10px;border-radius:999px;letter-spacing:0.3px;">PARTNER ACTIVITY ALERT</div>
                            <h1 style="margin:14px 0 4px;font-size:20px;line-height:1.3;color:#0f172a;">{{ $heading }}</h1>
                            <p style="margin:0;font-size:14px;color:#475569;">{{ $alert['activity'] }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-size:14px;">
                                @foreach ($rows as $label => $value)
                                    <tr>
                                        <td style="padding:9px 12px;border-bottom:1px solid #e2e8f0;color:#64748b;width:38%;vertical-align:top;">{{ $label }}</td>
                                        <td style="padding:9px 12px;border-bottom:1px solid #e2e8f0;font-weight:bold;color:#0f172a;word-break:break-word;">{{ $value }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 24px 28px;">
                            <a href="{{ $alert['candidate_url'] }}" style="display:inline-block;background:{{ $accent }};color:#ffffff;text-decoration:none;font-weight:bold;font-size:14px;padding:12px 22px;border-radius:8px;">View Candidate</a>
                            <p style="margin:12px 0 0;font-size:12px;color:#64748b;word-break:break-all;">{{ $alert['candidate_url'] }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f8fafc;padding:14px 24px;font-size:12px;color:#94a3b8;border-top:1px solid #e2e8f0;">
                            You receive this because you are a Partner Notification recipient (CRM &rarr; Website &rarr; Partner Notification).
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
