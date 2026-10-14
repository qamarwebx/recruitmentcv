<?php

namespace App\Support;

/**
 * Expected Salary currency (candidates.exp_sal_currency next to exp_sal) -
 * twin file, identical in the CRM and RecruitmentCV. The one list of
 * currencies and the one way a salary is shown everywhere.
 *
 * Display only: amounts are never converted, and exp_sal (free text) is
 * never rewritten. A candidate without a currency (all records saved before
 * it existed) shows the amount exactly as stored - no currency is assumed.
 */
final class SalaryCurrency
{
    /** Code => country, in the order of the selector. */
    public const OPTIONS = [
        'SAR' => 'Saudi Arabia',
        'INR' => 'India',
        'QAR' => 'Qatar',
        'AED' => 'United Arab Emirates',
        'KWD' => 'Kuwait',
        'BHD' => 'Bahrain',
        'OMR' => 'Oman',
    ];

    /** Currency of a new candidate saved without one (Candidate model creating hook). */
    public const DEFAULT = 'SAR';

    /** Former Expected Salary choices (the CRM Edit Personal dropdown, now a numeric box - amountError()). */
    public const AMOUNTS = ['500', '1000', '1500', '2000', '2500', '3000', '3500'];

    /** Expected Salary typed in the CRM Edit Personal form: a whole number in this range. */
    public const MIN_AMOUNT = 0;
    public const MAX_AMOUNT = 5000;

    /** A currency word / code already typed into an old amount ("1800 RIYAL", "1800/- riyal"). */
    private const TRAILING_CURRENCY = '#[\s/\-]*(?:riyals?|rials?|dirhams?|dinars?|rupees?|rs\.?|sar|inr|qar|aed|kwd|bhd|omr|ريال|درهم|دينار)\s*$#iu';

    public static function codes(): array
    {
        return array_keys(self::OPTIONS);
    }

    public static function isValid(?string $code): bool
    {
        return is_string($code) && array_key_exists($code, self::OPTIONS);
    }

    /**
     * The salary dropdown for one record, [value => label]: the standard
     * AMOUNTS, plus - first - the record's own stored value when it is not one
     * of them (older records, e.g. "1800 RIYAL"), so it stays selected and
     * saving the form never clears or replaces it.
     */
    public static function amountOptions(?string $current): array
    {
        $options = [];
        $current = (string) $current;
        if ($current !== '' && !in_array($current, self::AMOUNTS, true)) {
            $options[] = ['value' => $current, 'label' => $current . ' (current)'];
        }
        foreach (self::AMOUNTS as $amount) {
            $options[] = ['value' => $amount, 'label' => $amount];
        }

        return $options;
    }

    /** A salary the form may save: a standard amount, the record's own stored value, or none. */
    public static function isAllowedAmount(?string $amount, ?string $current): bool
    {
        $amount = (string) $amount;

        return $amount === '' || in_array($amount, self::AMOUNTS, true) || $amount === (string) $current;
    }

    /**
     * Why an Expected Salary from the CRM Edit Personal form cannot be saved
     * (null = it can): a whole number MIN_AMOUNT..MAX_AMOUNT, digits only (no
     * sign, decimals, letters or spaces); empty (no salary, as before); or the
     * record's own stored value unchanged - older free text such as "1800
     * RIYAL" stays exactly as it is until a new amount is entered.
     */
    public static function amountError($amount, ?string $current): ?string
    {
        if ($amount === null || $amount === '') {
            return null;
        }
        if (!is_string($amount) && !is_int($amount)) {
            return 'Expected Salary must be a whole number (digits only).';
        }
        $amount = (string) $amount;
        if ($current !== null && $current !== '' && $amount === $current) {
            return null;
        }
        if (!preg_match('/^[0-9]+$/', $amount)) {
            return 'Expected Salary must be a whole number (digits only - no letters, decimals or minus sign).';
        }
        $number = ltrim($amount, '0');
        if (strlen($number) > strlen((string) self::MAX_AMOUNT) || (int) $number > self::MAX_AMOUNT) {
            return 'Expected Salary cannot be more than ' . self::MAX_AMOUNT . '.';
        }

        return (int) $number < self::MIN_AMOUNT ? 'Expected Salary cannot be less than ' . self::MIN_AMOUNT . '.' : null;
    }

    /**
     * The value to store for an amount that passed amountError(): the stored
     * value itself when unchanged, a number without leading zeros ("0500" ->
     * "500"), or null / '' as given (no salary).
     */
    public static function normalizeAmount($amount, ?string $current)
    {
        if ($amount === null || $amount === '') {
            return $amount;
        }
        $amount = (string) $amount;
        if ($current !== null && $current !== '' && $amount === $current) {
            return $current;
        }

        return (string) (int) $amount;
    }

    /** "Saudi Arabia (SAR)" - the selector's option text. */
    public static function label(string $code): string
    {
        return (self::OPTIONS[$code] ?? $code) . ' (' . $code . ')';
    }

    /**
     * "2000 SAR" - the amount with its currency code; the amount as stored
     * when no (valid) currency is set; $empty when there is no amount. With a
     * currency, a currency word already typed into an old amount is left out
     * of the display ("1800 RIYAL" + SAR -> "1800 SAR"), never "1800 RIYAL SAR".
     */
    public static function format($amount, ?string $currency, string $empty = ''): string
    {
        $amount = trim((string) $amount);
        if ($amount === '') {
            return $empty;
        }
        if (!self::isValid($currency)) {
            return $amount;
        }

        $number = trim(preg_replace('#[\s/\-]+$#', '', preg_replace(self::TRAILING_CURRENCY, '', $amount)));

        return ($number !== '' ? $number : $amount) . ' ' . $currency;
    }
}
