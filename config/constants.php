<?php

// Order-automation variables (customer, partner, candidate, booking) shared by
// the website template sets: qamarhire.com and recruitmentcv.com use the same
// booking data, so both keys point at this one map.
$websiteOrderMap = [

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
];

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
        'qamarhire'     => $websiteOrderMap,
        'recruitmentcv' => $websiteOrderMap,
        // Partner Activity alerts to CRM staff (App\Support\PartnerActivity):
        // values come from the alert itself ("activity.<context key>").
        'partner_activity' => [
            'partner name'   => 'activity.partner_name',
            'partner mobile' => 'activity.partner_mobile',
            'partner city'   => 'activity.partner_city',
            'candidate name' => 'activity.candidate_name',
            'candidate id'   => 'activity.candidate_ref',
            'date time'      => 'activity.occurred_at',
            'candidate url'  => 'activity.candidate_url',
            'activity'       => 'activity.activity',
            'team member'    => 'activity.team_member',
        ],
        // RecruitmentCV notification events (App\Support\NotificationEvents,
        // CRM -> Website -> Settings); values set by NotificationCenter.
        'recruitmentcv_events' => [
            'recipient name' => 'event.recipient_name',
            'partner name'   => 'event.partner_name',
            'title'          => 'event.title',
            'details'        => 'event.details',
            'link'           => 'event.link',
            'date time'      => 'event.occurred_at',
        ],
    ],

    // Website template sets offered in the Template / Meta Automation
    // "Template For" dropdowns (key = template_for / automation key).
    // recruitmentcv.com orders use their own set, never qamarhire's.
    'website_template_for' => [
        'qamarhire'     => 'Qamarhire',
        'recruitmentcv' => 'recruitmentcv.com',
    ],

    // Partner Activity alerts (CRM -> Website -> Partner Notification) in the
    // same Template / Meta Automation "Template For" dropdowns.
    'partner_activity_template_for' => [
        'partner_activity' => 'Partner Activity',
        // Partner / order / team / security events (Website -> Settings).
        'recruitmentcv_events' => 'RecruitmentCV Notifications',
    ],

];


