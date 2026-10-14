{{-- Label / value rows (stack on phones).
@include('emails.partials.details', ['rows' => [[label, value], ...]])
Rows whose value is null or '' are left out; values are escaped. Twin file in both apps. --}}
@php($T = \App\Support\EmailTheme::class)
@php($detailRows = array_values(array_filter((array) $rows, fn ($row) => is_array($row) && isset($row[1]) && $row[1] !== '' && $row[1] !== null)))
@if ($detailRows)
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:6px 0 18px 0; border:1px solid {{ $T::BORDER }}; border-radius:10px; border-collapse:separate;">
        @foreach ($detailRows as $i => [$label, $value])
            <tr>
                <td class="email-detail-label" width="38%" valign="top"
                    style="padding:10px 14px; {{ $i ? 'border-top:1px solid ' . $T::BORDER . ';' : '' }} font-family:{{ $T::FONT }}; font-size:13px; line-height:20px; color:{{ $T::MUTED }};">{{ $label }}</td>
                <td class="email-detail-value" valign="top"
                    style="padding:10px 14px; {{ $i ? 'border-top:1px solid ' . $T::BORDER . ';' : '' }} font-family:{{ $T::FONT }}; font-size:14px; line-height:20px; font-weight:bold; color:{{ $T::INK }}; word-break:break-word; overflow-wrap:anywhere;"><span dir="auto">{{ $value }}</span></td>
            </tr>
        @endforeach
    </table>
@endif
