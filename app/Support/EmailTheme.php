<?php

namespace App\Support;

/**
 * The one QAMAR HIRE email look - palette, the logo and the inline styles
 * shared by the email layout (resources/views/emails/layouts/brand), its
 * partials (emails/partials/*) and the emails built on them. Email clients
 * ignore stylesheets, so styles are inline strings; keeping them here means
 * every email uses the same values. Twin class in both apps (CRM and
 * RecruitmentCV) - keep them identical.
 */
final class EmailTheme
{
    public const NAVY = '#13294b';
    public const GOLD = '#d9a441';
    public const INK = '#0f172a';
    public const TEXT = '#334155';
    public const MUTED = '#64748b';
    public const BG = '#eef1f6';
    public const BORDER = '#dfe4ee';
    public const RED = '#b91c1c';
    public const FONT = 'Arial, Helvetica, sans-serif';

    public const H1 = 'margin:0 0 18px 0; font-family:' . self::FONT . '; font-size:22px; line-height:30px; font-weight:bold; color:' . self::NAVY . '; text-align:center;';
    public const H2 = 'margin:22px 0 10px 0; font-family:' . self::FONT . '; font-size:16px; line-height:22px; font-weight:bold; color:' . self::NAVY . ';';
    public const P = 'margin:0 0 14px 0; font-family:' . self::FONT . '; font-size:15px; line-height:24px; color:' . self::TEXT . '; word-break:break-word; overflow-wrap:anywhere;';
    public const SMALL = 'margin:14px 0 0 0; font-family:' . self::FONT . '; font-size:12px; line-height:18px; color:' . self::MUTED . '; word-break:break-all;';
    public const STRONG = 'font-weight:bold; color:' . self::NAVY . ';';
    public const LINK = 'color:' . self::NAVY . '; font-weight:bold; text-decoration:underline;';

    /**
     * QAMAR HIRE / QAMR International logo: the email-safe copy (artwork on
     * white, no transparency, PNG - renders the same in Gmail, Outlook and
     * image proxies) on the main RecruitmentCV domain; both apps read the
     * same setting.
     */
    public static function logoUrl(): string
    {
        $host = strtolower(trim((string) config('services.hostinger.recruitmentcv_domain'), " \t\n\r\0\x0B.")) ?: 'recruitmentcv.com';

        return 'https://' . $host . '/user/img/logo/Logo_eng_dark_email.png';
    }
}
