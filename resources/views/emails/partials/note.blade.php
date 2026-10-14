{{-- Tinted note box: @include('emails.partials.note', ['tone' => 'gold'|'red', 'title' => optional, 'text' => ...])
text is escaped. Twin file in both apps. --}}
@php($T = \App\Support\EmailTheme::class)
@php($noteTone = ($tone ?? 'gold') === 'red' ? ['#fef2f2', $T::RED, '#7f1d1d'] : ['#fdf6e7', $T::GOLD, '#5b4416'])
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:4px 0 16px 0;">
    <tr>
        <td bgcolor="{{ $noteTone[0] }}" style="background-color:{{ $noteTone[0] }}; border-left:4px solid {{ $noteTone[1] }}; border-radius:6px; padding:12px 14px; font-family:{{ $T::FONT }}; font-size:13px; line-height:20px; color:{{ $noteTone[2] }}; word-break:break-word;">
            @if (!empty($title))<strong>{{ $title }}</strong> @endif{{ $text }}
        </td>
    </tr>
</table>
