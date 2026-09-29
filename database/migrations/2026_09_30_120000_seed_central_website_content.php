<?php

use App\Models\PartnerPageContent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Copies what recruitmentcv.com currently shows into its central
 * partner_page_contents rows (partner_id NULL), so CRM -> Website opens
 * pre-filled with the live content instead of blank fields.
 *
 * Built from the same defaults the public pages fall back to
 * (PartnerPageContent::defaultContent(), defaultBranches(),
 * defaultWhatsapp()), so the live site renders exactly as before.
 * Additive and idempotent: only missing keys are filled, any value already
 * saved on a central row wins, and partner rows are never read or written.
 * The WhatsApp label stays unset on purpose - its default is translated
 * per language ("Customer Support" / Arabic), which a stored value would
 * freeze to one language.
 */
return new class extends Migration
{
    public function up()
    {
        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();

        foreach (PartnerPageContent::PAGES as $page) {
            $row = PartnerPageContent::firstOrNew(['partner_id' => null, 'page' => $page]);
            $content = $row->content ?? [];

            foreach (['en', 'ar'] as $locale) {
                $content[$locale] = array_replace_recursive(
                    PartnerPageContent::defaultContent($page, $locale, $frontwebsite),
                    (array) ($content[$locale] ?? [])
                );
            }

            if ($page === 'contact' && !is_array($content['branches'] ?? null)) {
                $content['branches'] = PartnerPageContent::defaultBranches();
            }

            $row->content = $content;
            $row->save();
        }

        $row = PartnerPageContent::firstOrNew(['partner_id' => null, 'page' => PartnerPageContent::WHATSAPP]);
        $saved = array_filter($row->content ?? [], fn ($value) => $value !== null && $value !== '');
        $row->content = array_merge(PartnerPageContent::defaultWhatsapp(), $saved);
        $row->save();
    }

    public function down()
    {
        // Intentionally no-op: after this runs the central rows are edited
        // from CRM -> Website, and rolling back must never delete that content.
    }
};
