<?php

namespace App\Services;

class ContactPlusExportService
{
    public function exportColumnMap(): array
    {
        return [
            1  => 'Full Name',
            2  => 'Office English Name',
            3  => 'Office Arabic Name',
            4  => 'Primary Email',
            5  => 'Secondary Email',
            6  => 'Primary Contact',
            7  => 'Secondary Contact',
            8  => 'Contact No. 3',
            9  => 'Contact No. 4',
            10 => 'Lead Stage',
            11 => 'City',
            12 => 'Country',
            13 => 'Group',
            14 => 'Work',
            15 => 'Status',
        ];
    }

    public function getExportHeaders(array $columns): array
    {
        return array_values(
            array_intersect_key(
                $this->exportColumnMap(),
                array_flip($columns)
            )
        );
    }

    public function mapRow($contact, array $columns): array
    {
        $map = [
            1  => $contact->prim_concern_name ?? '---',
            2  => $contact->office_eng_name ?? '---',
            3  => $contact->office_ar_name ?? '---',
            4  => $contact->prim_email ?? '---',
            5  => $contact->sec_email ?? '---',
            6  => $contact->prim_contact ?? '---',
            7  => $contact->sec_contact ?? '---',
            8  => $contact->contact3 ?? '---',
            9  => $contact->contact4 ?? '---',

            10 => optional($contact->ls)->name ?? 'None',
            11 => optional($contact->city)->name ?? '---',
            12 => optional($contact->country)->name ?? '---',
            13 => optional($contact->group)->name ?? '---',
            14 => optional($contact->contactstatus)->name ?? 'None',

            15 => $contact->status == 1 ? 'Active' : 'Inactive',
        ];

        $row = array_intersect_key($map, array_flip($columns));

        // Excel mobile formatting
        foreach ([6,7,8,9] as $column) {

            if (isset($row[$column]) &&
                $row[$column] !== '---' &&
                $row[$column] !== '') {

                $row[$column] = '="' . $row[$column] . '"';
            } else {
                $row[$column] = '---';
            }
        }

        return $row;
    }
}