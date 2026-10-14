<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Repeatable content of the About Us / How It Works / Pricing pages (trust
 * items, "Why choose us" cards, offices, setup rows, compare rows, FAQs,
 * driver categories, billing checks) - twin file, identical in the CRM and
 * RecruitmentCV. Stored like How It Works -> steps: a list beside
 * content.en / content.ar on the page's partner_page_contents row
 * (no schema change), edited in CRM -> Website and the Partner Website,
 * resolved like every list:
 *
 *   partner list -> "Recruitmentcv Partner (Global)" list (App\Support\PartnerGlobal)
 *   -> recruitmentcv.com list -> nothing (no invented items)
 *
 * (recruitmentcv.com itself: its own list only). A saved empty list =
 * deliberately none. Field kinds: text / long = per language (field_en,
 * field_ar; Arabic falls back to English); icon = a key of ICONS; bool;
 * choice:a,b,c.
 */
final class PageLists
{
    public const LISTS = [
        'about' => [
            'trust' => ['label' => 'Hero trust items', 'max' => 8, 'fields' => ['icon' => ['icon', 'Icon'], 'text' => ['text', 'Text']]],
            'features' => ['label' => 'Why choose us - cards', 'max' => 12, 'fields' => ['icon' => ['icon', 'Icon'], 'title' => ['text', 'Title'], 'text' => ['long', 'Text']]],
            'offices' => ['label' => 'Offices', 'max' => 12, 'fields' => ['city' => ['text', 'City'], 'tag' => ['text', 'Label (e.g. Head office)'], 'text' => ['long', 'Address / region'], 'highlight' => ['bool', 'Highlight (head office)']]],
        ],
        'how_it_works' => [
            'setup' => ['label' => 'Partner setup card - rows', 'max' => 8, 'fields' => ['title' => ['text', 'Title'], 'text' => ['text', 'Detail'], 'status' => ['text', 'Status label'], 'state' => ['choice:done,now,next', 'State']]],
            'compare' => ['label' => 'Compare table - rows', 'max' => 15, 'fields' => ['label' => ['text', 'Row'], 'with' => ['long', 'With RecruitmentCV'], 'without' => ['long', 'Without (manual)']]],
            'faqs' => ['label' => 'FAQs', 'max' => 15, 'fields' => ['question' => ['text', 'Question'], 'answer' => ['long', 'Answer']]],
        ],
        'pricing' => [
            'trust' => ['label' => 'Hero trust items', 'max' => 8, 'fields' => ['icon' => ['icon', 'Icon'], 'text' => ['text', 'Text']]],
            'categories' => ['label' => 'Service charge - driver categories', 'max' => 15, 'fields' => ['name' => ['text', 'Category'], 'meaning' => ['long', 'Meaning'], 'charge' => ['choice:free,paid', 'Service charge']]],
            'checks' => ['label' => 'How billing works - check list', 'max' => 8, 'fields' => ['text' => ['text', 'Text']]],
            'faqs' => ['label' => 'Payment FAQs', 'max' => 15, 'fields' => ['question' => ['text', 'Question'], 'answer' => ['long', 'Answer']]],
        ],
    ];

    /** Icon keys offered for cards / steps (drawn by worker.partials.ref-icon). */
    public const ICONS = [
        'shield' => 'Shield / certified', 'chat-off' => 'Chat crossed out', 'monitor' => 'Screen / website', 'refresh' => 'Refresh / always available',
        'document' => 'Document', 'chat' => 'Chat / support', 'target' => 'Target', 'eye' => 'Eye', 'heart' => 'Heart', 'pin' => 'Location',
        'user' => 'Person', 'search' => 'Search', 'check' => 'Check', 'send' => 'Send / travel', 'phone' => 'Phone', 'mail' => 'Mail', 'globe' => 'Globe',
        'coins' => 'Coins', 'card' => 'Card / licence', 'star' => 'Star',
    ];

    public static function definition(string $page, string $list): ?array
    {
        return self::LISTS[$page][$list] ?? null;
    }

    /** One context's saved list, or null when it has none (null partner = recruitmentcv.com). */
    public static function saved(?int $partnerId, string $page, string $list): ?array
    {
        $raw = DB::table('partner_page_contents')
            ->when($partnerId, fn ($q) => $q->where('partner_id', $partnerId), fn ($q) => $q->whereNull('partner_id'))
            ->where('page', $page)->value('content');
        $content = is_string($raw) ? json_decode($raw, true) : null;
        $items = is_array($content) ? ($content[$list] ?? null) : null;

        return is_array($items) ? array_values($items) : null;
    }

    /** The list a website shows: partner -> Partner Global -> recruitmentcv.com -> []. */
    public static function resolve(?int $partnerId, string $page, string $list): array
    {
        if ($partnerId && ($own = self::saved($partnerId, $page, $list)) !== null) {
            return $own;
        }

        return self::inherited($partnerId, $page, $list);
    }

    /** What a partner shows without its own list (null partner = recruitmentcv.com itself). */
    public static function inherited(?int $partnerId, string $page, string $list): array
    {
        if ($partnerId && ($partnerGlobal = PartnerGlobal::list($page, $list)) !== null) {
            return $partnerGlobal;
        }

        return self::saved(null, $page, $list) ?? [];
    }

    /** Every list of a page for a website: [list => items]. */
    public static function all(?int $partnerId, string $page): array
    {
        $out = [];
        foreach (array_keys(self::LISTS[$page] ?? []) as $list) {
            $out[$list] = self::resolve($partnerId, $page, $list);
        }

        return $out;
    }

    /** Validation of a submitted list. */
    public static function rules(string $page, string $list): array
    {
        $def = self::definition($page, $list);
        $rules = ['items' => 'present|array|max:' . $def['max']];
        // The first text field (in English) is what makes an item: required.
        $primary = null;
        foreach ($def['fields'] as $field => [$kind]) {
            if (in_array($kind, ['text', 'long'], true)) {
                $primary = $field;
                break;
            }
        }
        foreach ($def['fields'] as $field => [$kind]) {
            if (in_array($kind, ['text', 'long'], true)) {
                $max = $kind === 'text' ? 200 : 1000;
                $rules["items.*.{$field}_en"] = ($field === $primary ? 'required' : 'nullable') . '|string|max:' . $max;
                $rules["items.*.{$field}_ar"] = 'nullable|string|max:' . $max;
            } elseif ($kind === 'icon') {
                $rules["items.*.{$field}"] = 'nullable|in:' . implode(',', array_keys(self::ICONS));
            } elseif ($kind === 'bool') {
                $rules["items.*.{$field}"] = 'nullable|boolean';
            } elseif (str_starts_with($kind, 'choice:')) {
                $rules["items.*.{$field}"] = 'nullable|in:' . substr($kind, 7);
            }
        }

        return $rules;
    }

    /** A validated list -> stored records (only known fields, trimmed). */
    public static function clean(string $page, string $list, array $items): array
    {
        $def = self::definition($page, $list);
        $out = [];
        foreach (array_values($items) as $item) {
            $record = [];
            foreach ($def['fields'] as $field => [$kind]) {
                if (in_array($kind, ['text', 'long'], true)) {
                    $record[$field . '_en'] = trim((string) ($item[$field . '_en'] ?? ''));
                    $record[$field . '_ar'] = trim((string) ($item[$field . '_ar'] ?? ''));
                } elseif ($kind === 'bool') {
                    $record[$field] = filter_var($item[$field] ?? false, FILTER_VALIDATE_BOOLEAN);
                } else {
                    $value = trim((string) ($item[$field] ?? ''));
                    $allowed = $kind === 'icon' ? array_keys(self::ICONS) : explode(',', substr($kind, 7));
                    $record[$field] = in_array($value, $allowed, true) ? $value : ($kind === 'icon' ? '' : $allowed[0]);
                }
            }
            $out[] = $record;
        }

        return $out;
    }

    /** A record's text in a language - Arabic falls back to English. */
    public static function text(array $item, string $field, string $locale): string
    {
        return InfoPages::text($item, $field, $locale);
    }
}
