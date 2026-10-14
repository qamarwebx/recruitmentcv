<?php

namespace App\Support;

/**
 * Candidate experience is stored as one value per experience row, comma-joined
 * ("4,2" = 4 + 2 years) and kept index-aligned with proff_id / expcountry_id /
 * expcity_id. A row left blank in the form is stored as an empty slot ("4,"),
 * which PHP 8.3's array_sum() rejects ("Addition is not supported on type
 * string"), so totals go through here. Only numeric slots are summed; blank or
 * non-numeric slots count as 0. Nothing is re-stored. Twin file, identical in
 * the CRM and RecruitmentCV.
 *
 * A slot is a number of years, possibly with a fraction for months ("2.5" =
 * 2 years 6 months, "0.5" = 6 months) - there is no separate months value.
 */
class CandidateExperience
{
    /**
     * Total years across all experience rows. Accepts the stored comma-joined
     * value or the form's experience[] array.
     */
    public static function total(string|array|null $experience): int|float
    {
        $values = is_array($experience) ? $experience : explode(',', (string) $experience);

        $numeric = array_filter(
            array_map(fn ($value) => is_string($value) ? trim($value) : $value, $values),
            'is_numeric'
        );

        return array_sum($numeric);
    }

    /**
     * The summed years for display (candidate cards): "9", "8.5" - only plain,
     * non-negative numbers count ("4", "2.5"; not "", "abc", "-2", "1e3"),
     * rounded to 2 decimals with trailing zeros dropped. Null when nothing
     * positive is left (the caller keeps its own empty state, e.g. Fresher).
     */
    public static function totalYears(string|array|null $experience): ?string
    {
        $values = is_array($experience) ? $experience : explode(',', (string) $experience);

        $total = 0.0;
        foreach ($values as $value) {
            $value = trim((string) $value);
            if (preg_match('/^\d+(\.\d+)?$/', $value)) {
                $total += (float) $value;
            }
        }

        $total = round($total, 2);
        if ($total <= 0) {
            return null;
        }

        return rtrim(rtrim(number_format($total, 2, '.', ''), '0'), '.');
    }

    /** "9 Years Experience" for all rows together, or null (no experience). */
    public static function totalLabel(string|array|null $experience): ?string
    {
        $years = self::totalYears($experience);

        return $years === null ? null : $years . ' ' . __('locale.Years Experience');
    }
}
