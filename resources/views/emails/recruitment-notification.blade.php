{{-- RecruitmentCV notification email (App\Mail\RecruitmentNotificationMail). Inline styles for email clients. Twin file in both apps. --}}
@php
    $brand = $n['brand'] ?? ['name' => 'RecruitmentCV', 'logo' => null, 'site_url' => 'https://recruitmentcv.com'];
    $lines = array_values(array_filter((array) ($n['lines'] ?? []), fn ($l) => is_array($l) && isset($l[1]) && $l[1] !== '' && $l[1] !== null));
    $action = $n['action'] ?? null;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subjectLine }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f1f5f9;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e2e8f0;">
                    <tr>
                        <td style="background:#0f172a;padding:18px 24px;">
                            <span style="font-size:18px;font-weight:bold;color:#ffffff;">{{ $brand['name'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 24px 8px;">
                            <h1 style="margin:0 0 10px;font-size:20px;line-height:1.3;color:#0f172a;">{{ $n['title'] ?? $subjectLine }}</h1>
                            @if (!empty($n['recipient_name']))
                                <p style="margin:0 0 8px;font-size:14px;color:#334155;">Hello {{ $n['recipient_name'] }},</p>
                            @endif
                            @if (!empty($n['message']))
                                <p style="margin:0;font-size:14px;line-height:1.6;color:#475569;">{{ $n['message'] }}</p>
                            @endif
                        </td>
                    </tr>
                    @if ($lines)
                        <tr>
                            <td style="padding:16px 24px;">
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-size:14px;">
                                    @foreach ($lines as [$label, $value])
                                        <tr>
                                            <td style="padding:9px 12px;border-bottom:1px solid #e2e8f0;color:#64748b;width:38%;vertical-align:top;">{{ $label }}</td>
                                            <td style="padding:9px 12px;border-bottom:1px solid #e2e8f0;font-weight:bold;color:#0f172a;word-break:break-word;">{{ $value }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    @endif
                    @if ($action && !empty($action[1]))
                        <tr>
                            <td style="padding:8px 24px 20px;">
                                <a href="{{ $action[1] }}" style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;font-weight:bold;font-size:14px;padding:12px 22px;border-radius:8px;">{{ $action[0] }}</a>
                                <p style="margin:12px 0 0;font-size:12px;color:#64748b;word-break:break-all;">{{ $action[1] }}</p>
                            </td>
                        </tr>
                    @endif
                    @if (!empty($n['note']))
                        <tr>
                            <td style="padding:0 24px 20px;font-size:13px;line-height:1.5;color:#b45309;">{{ $n['note'] }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="background:#f8fafc;padding:14px 24px;font-size:12px;color:#94a3b8;border-top:1px solid #e2e8f0;">
                            {{ $n['occurred_at'] ?? '' }} &middot; <a href="{{ $brand['site_url'] }}" style="color:#64748b;">{{ preg_replace('#^https?://#', '', $brand['site_url']) }}</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
