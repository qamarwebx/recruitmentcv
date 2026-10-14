{{-- QAMAR HIRE header for Laravel's markdown mail (password reset, device notifications): the logo on a navy
band with the gold line, like emails.layouts.brand. Twin file in both apps. --}}
@props(['url'])
<tr>
<td class="header" align="center" style="padding: 28px 0 0 0;">
<table align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="width: 100%; max-width: 570px; margin: 0 auto;">
<tr>
<td align="center" bgcolor="#13294b" style="background-color: #13294b; border-radius: 14px 14px 0 0; padding: 24px;">
<a href="{{ $url }}" style="display: inline-block;">
<table align="center" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center" bgcolor="#ffffff" style="background-color: #ffffff; border-radius: 10px; padding: 12px 20px;">
<img src="{{ \App\Support\EmailTheme::logoUrl() }}" width="200" alt="QAMAR HIRE" style="display: block; width: 100%; max-width: 200px; height: auto; border: 0;">
</td>
</tr>
</table>
</a>
</td>
</tr>
<tr>
<td height="4" bgcolor="#d9a441" style="background-color: #d9a441; font-size: 0; line-height: 0;">&nbsp;</td>
</tr>
</table>
</td>
</tr>
