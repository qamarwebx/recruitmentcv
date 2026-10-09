<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * On/off per notification event and channel (CRM -> Website -> Settings),
 * stored like the other Website settings in partner_page_contents, page
 * "notifications": the central row (partner_id NULL) = Global RecruitmentCV,
 * a partner's own row = that partner's overrides. content =
 * {"event": {"email": true|false, "whatsapp": true|false}} - a value missing
 * from a partner row means "use Global".
 *
 * Resolution: partner override -> Global -> on (default). Required events
 * (NotificationEvents "required") are always on. Read from the database
 * with a memo of a few seconds only (MEMO_SECONDS - the queue worker is a
 * long-running process), so a change in CRM applies at once in both apps.
 * Twin file in both apps.
 */
final class NotificationSettings
{
    public const PAGE = 'notifications';

    /** How long one read is reused within a process (a request, or a queue worker's jobs). */
    private const MEMO_SECONDS = 5;

    /** context => [read at, values] */
    private static array $memo = [];

    /** Saved values of one context (null = Global). */
    public static function saved(?int $partnerId): array
    {
        $key = (int) $partnerId;
        if (isset(self::$memo[$key]) && microtime(true) - self::$memo[$key][0] < self::MEMO_SECONDS) {
            return self::$memo[$key][1];
        }

        try {
            $raw = DB::table('partner_page_contents')->where('page', self::PAGE)
                ->when($partnerId, fn ($q) => $q->where('partner_id', $partnerId), fn ($q) => $q->whereNull('partner_id'))
                ->value('content');
        } catch (\Throwable $e) {
            return [];
        }
        $data = is_string($raw) ? json_decode($raw, true) : null;
        $data = is_array($data) ? $data : [];
        self::$memo[$key] = [microtime(true), $data];

        return $data;
    }

    /** Is this channel of this event on for this partner (null = Global / main site)? */
    public static function enabled(string $event, string $channel, ?int $partnerId = null): bool
    {
        if (!NotificationEvents::hasChannel($event, $channel)) {
            return false;
        }
        if (NotificationEvents::isRequired($event)) {
            return true;
        }

        if ($partnerId) {
            $own = self::saved($partnerId)[$event][$channel] ?? null;
            if (is_bool($own)) {
                return $own;
            }
        }
        $global = self::saved(null)[$event][$channel] ?? null;

        return is_bool($global) ? $global : true;
    }

    /** A partner's own value: true / false, or null = uses Global. */
    public static function override(int $partnerId, string $event, string $channel): ?bool
    {
        $value = self::saved($partnerId)[$event][$channel] ?? null;

        return is_bool($value) ? $value : null;
    }

    /**
     * Saves values for one context. $values = [event => [channel => true|false|null]]
     * (null on a partner = back to Global; ignored for Global). Only known
     * events/channels are kept; required events are never stored.
     */
    public static function put(?int $partnerId, array $values): void
    {
        DB::transaction(function () use ($partnerId, $values) {
            $row = DB::table('partner_page_contents')->where('page', self::PAGE)
                ->when($partnerId, fn ($q) => $q->where('partner_id', $partnerId), fn ($q) => $q->whereNull('partner_id'))
                ->lockForUpdate()->first();
            $content = $row && is_string($row->content) ? (json_decode($row->content, true) ?: []) : [];

            foreach ($values as $event => $channels) {
                $event = (string) $event;
                if (!NotificationEvents::get($event) || NotificationEvents::isRequired($event) || !is_array($channels)) {
                    continue;
                }
                foreach (['email', 'whatsapp'] as $channel) {
                    if (!array_key_exists($channel, $channels) || !NotificationEvents::hasChannel($event, $channel)) {
                        continue;
                    }
                    $value = $channels[$channel];
                    if ($value === null) {
                        if ($partnerId) {
                            unset($content[$event][$channel]);
                        }
                    } else {
                        $content[$event][$channel] = (bool) $value;
                    }
                }
                if (isset($content[$event]) && $content[$event] === []) {
                    unset($content[$event]);
                }
            }

            $json = json_encode((object) $content, JSON_UNESCAPED_UNICODE);
            if ($row) {
                DB::table('partner_page_contents')->where('id', $row->id)->update(['content' => $json, 'updated_at' => now()]);
            } else {
                DB::table('partner_page_contents')->insert(['partner_id' => $partnerId, 'page' => self::PAGE, 'content' => $json, 'created_at' => now(), 'updated_at' => now()]);
            }
        });

        self::$memo = [];
    }

    public static function forget(): void
    {
        self::$memo = [];
    }
}
