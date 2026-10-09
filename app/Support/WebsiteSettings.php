<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * CRM -> Website -> Settings values read by both apps, stored like the
 * other global Website settings: the central partner_page_contents row
 * (partner_id NULL) for page "settings" (not a public page, so never in
 * PartnerPageContent::PAGES - like the "whatsapp" settings row), content =
 * JSON key/values. Always read from the database (nothing cached across
 * requests), so a change saved in CRM applies on RecruitmentCV at once.
 * Twin file in the CRM and RecruitmentCV codebases.
 */
final class WebsiteSettings
{
    public const PAGE = 'settings';

    /** Login Session tab: how long a Pending partner's own session lasts (minutes). */
    public const PENDING_SESSION_MINUTES = 'pending_partner_session_minutes';

    public const PENDING_SESSION_DEFAULT = 30;

    public const PENDING_SESSION_MIN = 5;

    public const PENDING_SESSION_MAX = 1440;

    /** Saved values ([] when nothing is saved yet, or the row can't be read). */
    public static function all(): array
    {
        try {
            $raw = DB::table('partner_page_contents')->whereNull('partner_id')->where('page', self::PAGE)->value('content');
        } catch (\Throwable $e) {
            return [];
        }

        $data = is_string($raw) ? json_decode($raw, true) : null;

        return is_array($data) ? $data : [];
    }

    /** Pending Partner Session Duration in minutes: the saved value (kept in range), else 30. */
    public static function pendingPartnerSessionMinutes(): int
    {
        $value = self::all()[self::PENDING_SESSION_MINUTES] ?? null;

        if (!is_numeric($value)) {
            return self::PENDING_SESSION_DEFAULT;
        }

        return max(self::PENDING_SESSION_MIN, min(self::PENDING_SESSION_MAX, (int) $value));
    }

    /** Merges values into the central settings row (created on the first save). */
    public static function put(array $values): void
    {
        DB::transaction(function () use ($values) {
            $row = DB::table('partner_page_contents')->whereNull('partner_id')->where('page', self::PAGE)->lockForUpdate()->first();
            $saved = $row && is_string($row->content) ? (json_decode($row->content, true) ?: []) : [];
            $json = json_encode(array_merge($saved, $values), JSON_UNESCAPED_UNICODE);

            if ($row) {
                DB::table('partner_page_contents')->where('id', $row->id)->update(['content' => $json, 'updated_at' => now()]);
            } else {
                DB::table('partner_page_contents')->insert([
                    'partner_id' => null,
                    'page' => self::PAGE,
                    'content' => $json,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
}
