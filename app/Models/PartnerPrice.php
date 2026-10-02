<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

    /**
     * A partner's price for any Experience Type + Profession they haven't
     * saved one for (Partner Portal table + their candidate pages). Only a
     * fallback - never written to the table. From config/partner.php
     * (PARTNER_DEFAULT_SERVICE_PRICE / PARTNER_DEFAULT_DEPARTURE_DAYS,
     * default 3000 / 15).
     */
    public static function defaultCost(): float
    {
        return (float) config('partner.default_price.service_price', 3000);
    }

    public static function defaultDays(): int
    {
        return (int) config('partner.default_price.departure_days', 15);
    }

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
     * Every Experience Type + Profession combination (the same sources the
     * Price Update page always offered: Candidate::EXPERIENCE_TYPES x
     * professions) with this partner's effective price: their saved row,
     * or an unsaved defaultCost() / defaultDays() model (exists = false).
     * Saved rows for a combination no longer offered are kept at the end.
     */
    public static function effectiveForPartner(int $partnerId): Collection
    {
        $saved = static::with('profession:id,eng_name,ar_name')
            ->where('partner_id', $partnerId)
            ->get()
            ->keyBy(fn ($price) => $price->exp_type . '-' . $price->proff_id);

        $rows = collect();
        foreach (Profession::orderBy('eng_name')->get(['id', 'eng_name', 'ar_name']) as $profession) {
            foreach (array_keys(Candidate::EXPERIENCE_TYPES) as $expType) {
                $key = $expType . '-' . $profession->id;
                $price = $saved->pull($key) ?: new static([
                    'partner_id' => $partnerId,
                    'exp_type' => $expType,
                    'proff_id' => $profession->id,
                    'cost' => static::defaultCost(),
                    'days' => static::defaultDays(),
                ]);
                $price->setRelation('profession', $profession);
                $rows->push($price);
            }
        }

        return $rows->concat($saved->values());
    }

    /**
     * Service Price / Departure labels for a candidate detail page, for the
     * candidate's experience type + profession.
     *   - Partner site / Partner Portal ($partnerId set): the partner's own
     *     price, otherwise defaultCost() / defaultDays().
     *   - Apex site ($partnerId null): the global recruitmentcv.com price,
     *     otherwise the existing customercosts default ("On Request" / "---"
     *     when none exists) - unchanged.
     */
    public static function labelsFor(?int $partnerId, Candidate $candidate): array
    {
        $rule = null;
        $hasCombination = $candidate->jobtype_id && in_array((int) $candidate->gulfexperience, array_keys(Candidate::EXPERIENCE_TYPES), true);

        if ($hasCombination) {
            $rule = static::where('exp_type', (int) $candidate->gulfexperience)
                ->where('proff_id', $candidate->jobtype_id)
                ->when($partnerId, fn ($q) => $q->where('partner_id', $partnerId), fn ($q) => $q->whereNull('partner_id'))
                ->first();

            if (!$rule && $partnerId) {
                $rule = new static(['cost' => static::defaultCost(), 'days' => static::defaultDays()]);
            }
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
