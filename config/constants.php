<?php

return [

    // Template For → Actual DB Table Name
    'template_for_map' => [
        'contactp'    => 'contactpluses',
        'allcontact'  => 'allcontacts',
        'associate'   => 'associates',
        'partner'     => 'partners',
        'employers'   => 'employers',
        'leads'       => 'leads',
        'todos'       => 'todos',
        'qamarhire'  => [

            // =========================
            // Users Module
            // =========================
            'customer name' => 'users.name',
            'customer mobile number' => 'users.mobile_no',
            'careof whatsup link' => 'partners.partner_careoff_id',


            // =========================
            // Partner Module
            // =========================
            'partner office name (eng)' => 'partners.rec_off_name',
            'partner office name (Arabic)' => 'partners.rec_office_arname',
            'partner Consern Person Name Daynamic As Per Mobile Number' => 'partner Consern Person Name Daynamic As Per Mobile Number',
            'partner Consern Person Name1' => 'partners.portal_consern_person_name1',
            'partner Consern Person Name2' => 'partners.portal_consern_person_name2',
            'Partner office address' => 'partners.portal_add_for_cust',
            'partner Consern Person mobile1' => 'partners.portal_mobile_no_1_text',
            'partner Consern Person mobile2' => 'partners.portal_mobile_no_2_text',
            'partner calling number' => 'partners.partner_calling_number',
            'partner whatsup number' => 'partners.partner_whatsapp_number',


            // =========================
            // Candidate Module
            // =========================
            'candidate name' => 'candidates.cand_name',
            'passport number' => 'candidates.pass_no',
            'candidate cv' => 'candidates.cv_file',

            // =========================
            // Order Reference
            // =========================
            'Order Reference' => 'bookings.reference_no',
            'Date of order' => 'bookings.created_at',
            'work location city' => 'bookings.worklocation',
            'Order detail pdf' => '',


        ],
    ],

];


