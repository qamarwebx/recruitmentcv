<?php

namespace App\Support;

use App\Models\Country;

/**
 * The phone country-code list used by the Partner Login/Register modal and
 * the customer account "Change Mobile" modal (worker.partials.country-code-
 * select). Same 5-country set/order as the Driver app's phone dropdown
 * (preferred pair first, then the rest), sourced from `countries` - the
 * table generateOtp2() validates country_code against.
 */
class CountryCodeOptions
{
    public const ORDER = ['966', '91', '965', '974', '971'];

    /** Flags shown in the option text (Partner Login's existing emoji flags). */
    public const FLAGS = [
        '966' => '🇸🇦',
        '91' => '🇮🇳',
        '965' => '🇰🇼',
        '974' => '🇶🇦',
        '971' => '🇦🇪',
    ];

    /**
     * code => ['label', 'flag'], in display order. Saudi Arabia uses
     * its selector-specific label ("KSA"); the others the Country's own
     * display_name.
     */
    public static function all(): array
    {
        $countries = Country::whereIn('country_code', self::ORDER)->get()->keyBy('country_code');
        $labels = ['966' => __('locale.Saudi Arabia (Country Code)')];

        $options = [];
        foreach (self::ORDER as $code) {
            if (!isset($countries[$code])) {
                continue;
            }
            $options[$code] = [
                'label' => $labels[$code] ?? trim($countries[$code]->display_name),
                'flag' => self::FLAGS[$code],
            ];
        }

        return $options;
    }
}
