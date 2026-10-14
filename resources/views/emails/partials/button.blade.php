{{-- Email button (works in Outlook too: the colour sits on the table cell).
@include('emails.partials.button', ['url' => ..., 'label' => ..., 'color' => optional, 'showLink' => optional bool])
showLink adds the URL under the button for clients that block buttons. Twin file in both apps. --}}
@php($T = \App\Support\EmailTheme::class)
@php($buttonColor = $color ?? $T::NAVY)
<table role="presentation" cellpadding="0" cellspacing="0" border="0" class="email-btn" style="margin:6px 0 4px 0;">
    <tr>
        <td align="center" bgcolor="{{ $buttonColor }}" style="background-color:{{ $buttonColor }}; border-radius:8px;">
            <a href="{{ $url }}" target="_blank" rel="noopener"
               style="display:inline-block; padding:12px 24px; font-family:{{ $T::FONT }}; font-size:14px; line-height:18px; font-weight:bold; color:#ffffff; text-decoration:none; border-radius:8px;">{{ $label }}</a>
        </td>
    </tr>
</table>
@if (!empty($showLink))
    <p style="{{ $T::SMALL }}">If the button does not work, open this link: <a href="{{ $url }}" style="color:{{ $T::MUTED }};">{{ $url }}</a></p>
@endif
