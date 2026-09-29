<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Service Price + Departure Days for one Experience Type + Profession, in
 * one of two contexts: partner_id set = that partner's own price (Partner
 * Portal -> Price Update, unique per partner + exp_type + proff_id);
 * partner_id NULL = the global recruitmentcv.com price (CRM -> Website ->
 * Price Update, unique per exp_type + proff_id via central_key).
 */
class PartnerPrice extends Model
{
    protected $fillable = [
        'partner_id',
        'exp_type',
        'proff_id',
        'cost',
        'days',
    ];

    /**
     * Service Price / Departure Days rules - shared by the Partner Portal's
     * Price Update and CRM -> Website -> Price Update (the CRM's twin of
     * this model carries the same rules).
     */
    public const VALUE_RULES = [
        'cost' => ['required', 'numeric', 'min:0', 'max:9999999'],
        'days' => ['required', 'integer', 'min:0', 'max:3650'],
    ];

    protected $casts = [
        'exp_type' => 'integer',
        'proff_id' => 'integer',
        'cost' => 'decimal:2',
        'days' => 'integer',
    ];

    public function profession()
    {
        return $this->belongsTo(Profession::class, 'proff_id');
    }

    /**
     * Service Price / Departure labels for a candidate detail page, for the
     * candidate's experience type + profession: the partner's own price
     * when there is one, otherwise the global recruitmentcv.com price,
     * otherwise the existing customercosts default (unchanged: "On Request"
     * / "---" when none exists). $partnerId null = the apex site, which
     * starts at the global price. One query - the partner row sorts first.
     */
    public static function labelsFor(?int $partnerId, Candidate $candidate): array
    {
        $rule = null;

        if ($candidate->jobtype_id && in_array((int) $candidate->gulfexperience, array_keys(Candidate::EXPERIENCE_TYPES), true)) {
            $rule = static::where('exp_type', (int) $candidate->gulfexperience)
                ->where('proff_id', $candidate->jobtype_id)
                ->where(fn ($q) => $q->whereNull('partner_id')->when($partnerId, fn ($q) => $q->orWhere('partner_id', $partnerId)))
                ->orderByRaw('partner_id IS NULL')
                ->first();
        }

        $rule = $rule ?: Customercost::where('proff_id', $candidate->jobtype_id)
            ->where('exp_type', $candidate->gulfexperience)
            ->where('status', 1)
            ->first();

        if (!$rule) {
            return [__('locale.On Request'), '---'];
        }

        return [
            round($rule->cost, 0) == 0 ? __('locale.Free') : round($rule->cost, 0) . ' SAR',
            $rule->days . ' ' . __('locale.Days'),
        ];
    }
}
