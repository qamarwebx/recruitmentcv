<?php

namespace App\Services;

class ContactExportService
{
    /**
     * Exportable column map.
     */
    public function exportColumnMap(): array
    {
        return [
            1 => 'Name',
            2 => 'Company',
            3 => 'Business',
            4 => 'Stage',
            5 => 'Priority',
            6 => 'OPT IN',
            7 => 'Group',
            8 => 'Careoff',
            9 => 'Primary No',
            10 => 'Secondory No',
            11 => 'Mobile 1',
            12 => 'Mobile 2',
            13 => 'Mobile 3',
        ];
    }

    /**
     * Get CSV headers.
     */
    public function getExportHeaders(array $columns): array
    {
        return array_values(
            array_intersect_key(
                $this->exportColumnMap(),
                array_flip($columns)
            )
        );
    }

    /**
     * Map a contact to an export row.
     */
    public function mapRow($contact, array $columns): array
    {
        $map = [
            1 => $contact->full_name ?? '---',
            2 => $contact->company_name ?? '---',
            3 => $contact->lead_type ?? '---',
            4 => optional($contact->ls)->name ?? '---',
            5 => $contact->lead_prority ?? '---',
            6 => isset($contact->optinout)
                ? ($contact->optinout == 1 ? 'Opt In' : 'Opt Out')
                : '---',
            7 => optional($contact->group)->name ?? '---',
            8 => optional($contact->careoff)->name ?? '---',
            9 => $contact->primary_no_wsp ?? '---',
            10 => $contact->secondary_no_wsp ?? '---',
            11 => $contact->mobile_no1_wsp ?? '---',
            12 => $contact->mobile_no2_wsp ?? '---',
            13 => $contact->mobile_no3_wsp ?? '---',
        ];

        // Return only selected columns
        return array_intersect_key($map, array_flip($columns));
    }
}