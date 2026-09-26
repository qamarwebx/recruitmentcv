<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Partner Website Config override row - see the create_partner_page_contents
 * migration for the `content` JSON shape (locale => field => value, one row
 * per partner+page). Never holds a full copy of a page's content, only the
 * fields a Partner has actually chosen to override.
 */
class PartnerPageContent extends Model
{
    protected $fillable = [
        'partner_id',
        'page',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public const PAGES = ['home', 'about', 'contact', 'privacy', 'terms'];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * The given partner's saved overrides for one page/locale, or an empty
     * array (never null) so every call site can safely do
     * `$content['field'] ?? $existingDefault` without an extra null check.
     */
    public static function contentFor(?int $partnerId, string $page, string $locale): array
    {
        if (!$partnerId) {
            return [];
        }

        $row = static::where('partner_id', $partnerId)->where('page', $page)->first();

        return $row->content[$locale] ?? [];
    }

    /**
     * Contact Us "Branches/Locations" - stored under content.branches (a
     * sibling of content.en/content.ar, not nested inside either one),
     * since each branch pairs an English name + Arabic name + ONE shared
     * image in a single record - splitting that across two locale buckets
     * would need extra id-matching logic for no benefit. A list, not a
     * flat field map, so it's override-the-whole-list, never a per-branch
     * merge with the default (there's no sane way to "merge" a reordered/
     * added/removed list against a different one).
     *
     * The exact same 5 branches (photo + label) worker.contact's own
     * hardcoded $branches array used to define - relocated here, not
     * duplicated, so this is still the only place that list is written.
     */
    public static function defaultBranches(): array
    {
        $defaults = [
            ['image' => 'user/img/ksa.png', 'key' => 'Al Riyadh and Al Qassim'],
            ['image' => 'user/img/Mumbai.png', 'key' => 'Mumbai'],
            ['image' => 'user/img/delhi.png', 'key' => 'New Delhi'],
            ['image' => 'user/img/lucknow.png', 'key' => 'Lucknow'],
            ['image' => 'user/img/hydr.png', 'key' => 'Hyderabad'],
        ];

        return collect($defaults)->map(fn ($branch) => [
            'name_en' => __('locale.' . $branch['key'], [], 'en'),
            'name_ar' => __('locale.' . $branch['key'], [], 'ar'),
            'image' => asset($branch['image']),
        ])->values()->all();
    }

    /**
     * The branches to actually render/prefill for this partner (or the
     * apex/no-partner case) - this partner's saved list if they've ever
     * saved one (even a shorter/reordered one), otherwise the default
     * list above. A saved empty array is a deliberate "no branches" and
     * is honored as such, not treated as "unset".
     */
    public static function effectiveBranches(?int $partnerId): array
    {
        if ($partnerId) {
            $row = static::where('partner_id', $partnerId)->where('page', 'contact')->first();
            $branches = $row->content['branches'] ?? null;

            if (is_array($branches)) {
                return $branches;
            }
        }

        return static::defaultBranches();
    }
}
