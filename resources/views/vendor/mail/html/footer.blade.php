{{-- QAMAR HIRE footer for Laravel's markdown mail: the company line, then the message's own footer text. Twin file in both apps. --}}
<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center">
<p style="color: #13294b; font-size: 13px; font-weight: bold; margin-bottom: 4px;">QAMR International &middot; QAMAR HIRE</p>
{{ Illuminate\Mail\Markdown::parse($slot) }}
</td>
</tr>
</table>
</td>
</tr>
