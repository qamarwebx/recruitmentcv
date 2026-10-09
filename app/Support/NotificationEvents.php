<?php

namespace App\Support;

/**
 * Every RecruitmentCV notification in one registry - the events the CRM
 * Website -> Settings tabs configure (App\Support\NotificationSettings) and
 * App\Support\NotificationCenter gates, sends and logs. Twin file in both
 * apps.
 *
 * Per event: tab (group), label, description, who receives it, the email
 * template, the WhatsApp template source (Meta Automation "Template For" +
 * trigger - the existing template system), where it comes from and whether
 * it existed before this registry ("existing") or was added with it ("new").
 * "required" events (sign-in / verification codes) can't be switched off -
 * that would lock users out - but are logged like the rest.
 */
final class NotificationEvents
{
    public const GROUPS = [
        'partner' => 'Partner Notification',
        'customer' => 'Customer Notifications',
        'orders' => 'Order & Hiring',
        'recruitment' => 'Candidate/Recruitment',
        'team' => 'Team Member',
        'security' => 'Security',
    ];

    /** Meta Automation "Template For" of the events added with this registry. */
    public const WHATSAPP_GROUP = 'recruitmentcv_events';

    public const RECIPIENTS = [
        'staff' => 'CRM staff - Partner Notification recipients (Work Email / Work Mobile No CRM Notification)',
        'partner' => 'The partner (account email / primary mobile)',
        'customer' => 'The customer (account email / mobile)',
        'team_member' => 'The team member (email / mobile)',
        'previous_email' => 'The partner\'s previous email address',
        'user' => 'The person signing in / verifying',
    ];

    public const ALL = [
        // ---- Partner account (tab: Partner Notification) -------------------------
        'partner_registered' => [
            'group' => 'partner', 'label' => 'Partner Registration Submitted', 'recipient' => 'staff', 'source' => 'new',
            'description' => 'A new partner has registered and is waiting for approval in CRM.',
            'email' => 'Partner registration submitted', 'whatsapp' => ['recruitmentcv_events', 'partner_registered'],
            'trigger' => 'RecruitmentCV partner registration (PartnerAuthController)',
        ],
        'partner_approved' => [
            'group' => 'partner', 'label' => 'Partner Approved', 'recipient' => 'partner', 'source' => 'new',
            'description' => 'CRM sets the partner\'s Registration Request to Approved - Hire Now and Download CV become available.',
            'email' => 'Your partner account is approved', 'whatsapp' => ['recruitmentcv_events', 'partner_approved'],
            'trigger' => 'CRM Partner -> Registration Status (PartnerController)',
        ],
        'partner_rejected' => [
            'group' => 'partner', 'label' => 'Partner Rejected', 'recipient' => 'partner', 'source' => 'new',
            'description' => 'CRM sets the partner\'s Registration Request to Rejected.',
            'email' => 'Your partner registration was not approved', 'whatsapp' => ['recruitmentcv_events', 'partner_rejected'],
            'trigger' => 'CRM Partner -> Registration Status (PartnerController)',
        ],
        'partner_domain_created' => [
            'group' => 'partner', 'label' => 'Partner Website Live', 'recipient' => 'partner', 'source' => 'new',
            'description' => 'The partner\'s {name}.recruitmentcv.com subdomain is created on Hostinger.',
            'email' => 'Your RecruitmentCV website is live', 'whatsapp' => ['recruitmentcv_events', 'partner_domain_created'],
            'trigger' => 'CRM Partner -> Website -> Domain -> Save Domain (DomainController)',
        ],
        'partner_domain_removed' => [
            'group' => 'partner', 'label' => 'Partner Website Removed', 'recipient' => 'partner', 'source' => 'new',
            'description' => 'The partner\'s subdomain is removed and its RecruitmentCV portal access revoked.',
            'email' => 'Your RecruitmentCV website was removed', 'whatsapp' => ['recruitmentcv_events', 'partner_domain_removed'],
            'trigger' => 'CRM Partner -> Website -> Domain -> Remove Subdomain (DomainController)',
        ],

        // ---- Partner candidate activity (tab: Candidate/Recruitment) -------------
        'partner_candidate_viewed' => [
            'group' => 'recruitment', 'label' => 'Candidate Viewed (over 1 minute)', 'recipient' => 'staff', 'source' => 'existing',
            'description' => 'A partner stays on a Candidate Detail page for more than one minute.',
            'email' => 'Partner Activity alert', 'whatsapp' => ['partner_activity', 'partner_candidate_viewed'],
            'trigger' => 'Partner Portal candidate page presence (PartnerPortalController)',
        ],
        'partner_hire_now_clicked' => [
            'group' => 'recruitment', 'label' => 'Hire Now Clicked', 'recipient' => 'staff', 'source' => 'existing',
            'description' => 'A partner clicks Hire Now on a candidate.',
            'email' => 'Partner Activity alert', 'whatsapp' => ['partner_activity', 'partner_hire_now_clicked'],
            'trigger' => 'Partner Portal Hire Now (PartnerPortalController)',
        ],
        'partner_download_cv_clicked' => [
            'group' => 'recruitment', 'label' => 'Candidate Download CV', 'recipient' => 'staff', 'source' => 'existing',
            'description' => 'A partner downloads (or asks to download) a candidate CV.',
            'email' => 'Partner Activity alert', 'whatsapp' => ['partner_activity', 'partner_download_cv_clicked'],
            'trigger' => 'CV download endpoint (PartnerPortalController::candidateCv)',
        ],

        // ---- Orders (tab: Order & Hiring) -----------------------------------------
        'partner_new_customer_order' => [
            'group' => 'orders', 'label' => 'New Customer Order (to Partner)', 'recipient' => 'partner', 'source' => 'existing',
            'description' => 'A customer hires a candidate on the partner\'s website - the partner is told about the new order.',
            'email' => 'New customer order', 'whatsapp' => ['recruitmentcv', 'order_to_partner'],
            'trigger' => 'Customer Hire Now (CustomerHireController -> BookingController::store4)',
        ],
        'partner_order_placed' => [
            'group' => 'orders', 'label' => 'Partner Order Placed', 'recipient' => 'staff', 'source' => 'new',
            'description' => 'A partner completes Hire Now - a new order for one of its employers.',
            'email' => 'Partner order placed', 'whatsapp' => ['recruitmentcv_events', 'partner_order_placed'],
            'trigger' => 'Partner Portal Hire Now submit (PartnerHireController::hire)',
        ],
        'order_cancelled_staff' => [
            'group' => 'orders', 'label' => 'Order Cancelled by Partner', 'recipient' => 'staff', 'source' => 'new',
            'description' => 'A partner cancels an order in its portal.',
            'email' => 'Order cancelled', 'whatsapp' => ['recruitmentcv_events', 'order_cancelled'],
            'trigger' => 'Partner Portal Orders -> Cancel (PartnerHireController::cancel)',
        ],

        // ---- Customer (tab: Customer Notifications) --------------------------------
        'customer_order_created' => [
            'group' => 'customer', 'label' => 'Customer Order Confirmation', 'recipient' => 'customer', 'source' => 'existing',
            'description' => 'The customer gets a confirmation for the candidate they hired.',
            'email' => 'Booking confirmation', 'whatsapp' => ['recruitmentcv', 'order'],
            'trigger' => 'Customer Hire Now (CustomerHireController -> BookingController::store4)',
        ],
        'customer_order_cancelled' => [
            'group' => 'customer', 'label' => 'Customer Order Cancelled', 'recipient' => 'customer', 'source' => 'new',
            'description' => 'The customer is told when their order is cancelled by the partner.',
            'email' => 'Your order was cancelled', 'whatsapp' => ['recruitmentcv', 'cancel_order'],
            'trigger' => 'Partner Portal Orders -> Cancel of a customer order (PartnerHireController::cancel)',
        ],
        'customer_email_changed' => [
            'group' => 'customer', 'label' => 'Customer Email Changed', 'recipient' => 'customer', 'source' => 'existing',
            'description' => 'Confirmation sent to the customer\'s new email address after a verified change.',
            'email' => 'Email changed', 'whatsapp' => null,
            'trigger' => 'Customer account -> email change (DashboardController)',
        ],

        // ---- Team members (tab: Team Member) ---------------------------------------
        'team_member_created' => [
            'group' => 'team', 'label' => 'Team Member Added', 'recipient' => 'team_member', 'source' => 'new',
            'description' => 'Welcome message to a new team member with where and how to sign in (never the password).',
            'email' => 'You were added as a team member', 'whatsapp' => ['recruitmentcv_events', 'team_member_created'],
            'trigger' => 'Partner Portal Team Members -> Add (PartnerTeamMemberController)',
        ],

        // ---- Security (tab: Security) ----------------------------------------------
        'partner_password_changed' => [
            'group' => 'security', 'label' => 'Partner Password Changed', 'recipient' => 'partner', 'source' => 'new',
            'description' => 'Security notice to the partner after its password is changed.',
            'email' => 'Your password was changed', 'whatsapp' => ['recruitmentcv_events', 'partner_password_changed'],
            'trigger' => 'Partner Portal Account -> Password (PartnerPortalController)',
        ],
        'partner_email_changed' => [
            'group' => 'security', 'label' => 'Partner Email Changed', 'recipient' => 'previous_email', 'source' => 'new',
            'description' => 'Security notice to the previous address after the partner\'s email is changed.',
            'email' => 'Your email address was changed', 'whatsapp' => null,
            'trigger' => 'Partner Portal Account -> email change (PartnerPortalController)',
        ],
        'partner_login_code' => [
            'group' => 'security', 'label' => 'Partner Sign-in / Reset Code', 'recipient' => 'partner', 'source' => 'existing', 'required' => true,
            'description' => 'One-time code emailed for Login with OTP and Forgot Password.',
            'email' => 'Partner email verification code', 'whatsapp' => null,
            'trigger' => 'Partner Login (PartnerAuthController)',
        ],
        'partner_email_change_code' => [
            'group' => 'security', 'label' => 'Partner Email Verification Code', 'recipient' => 'partner', 'source' => 'existing', 'required' => true,
            'description' => 'Code that verifies a new partner email address.',
            'email' => 'Partner email verification code', 'whatsapp' => null,
            'trigger' => 'Partner Portal Account -> email change (PartnerPortalController)',
        ],
        'customer_email_change_code' => [
            'group' => 'security', 'label' => 'Customer Email Verification Code', 'recipient' => 'customer', 'source' => 'existing', 'required' => true,
            'description' => 'Code that verifies a new customer email address.',
            'email' => 'OTP verification', 'whatsapp' => null,
            'trigger' => 'Customer account -> email change (DashboardController)',
        ],
        'mobile_otp' => [
            'group' => 'security', 'label' => 'Mobile OTP (WhatsApp)', 'recipient' => 'user', 'source' => 'existing', 'required' => true,
            'description' => 'Sign-in, registration and mobile verification codes sent on WhatsApp.',
            'email' => null, 'whatsapp' => ['otp', 'otp'],
            'trigger' => 'Partner / customer mobile OTP (WhatsappOtpController, DashboardController)',
        ],
    ];

    public static function get(string $event): ?array
    {
        return self::ALL[$event] ?? null;
    }

    public static function inGroup(string $group): array
    {
        return array_filter(self::ALL, fn ($e) => $e['group'] === $group);
    }

    public static function hasChannel(string $event, string $channel): bool
    {
        $e = self::ALL[$event] ?? null;

        return $e !== null && !empty($e[$channel]);
    }

    public static function isRequired(string $event): bool
    {
        return !empty(self::ALL[$event]['required']);
    }

    /** Triggers of the "RecruitmentCV Notifications" Meta Automation group: trigger => label. */
    public static function whatsappTriggers(): array
    {
        $out = [];
        foreach (self::ALL as $event) {
            if (($event['whatsapp'][0] ?? null) === self::WHATSAPP_GROUP) {
                $out[$event['whatsapp'][1]] = 'RecruitmentCV - ' . $event['label'];
            }
        }

        return $out;
    }
}
