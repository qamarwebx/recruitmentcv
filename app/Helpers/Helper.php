<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

use Config;
use Illuminate\Support\Str;
use App\Models\Metawhatsapptemplate;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;
use App\Models\Metawhatsappapi;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeadAssignMail;
use App\Models\LeadActivityLog;
use App\Models\DealActivityLog;
use App\Models\TestimonialActivityLog;
use App\Models\FundAdvanceActivityLog;
use App\Models\Employer;
use App\Models\Employerplus;
use App\Models\Employercandidate;
use App\Models\Candidate;
use Illuminate\Support\Facades\Auth;

class Helper
{

    public static function assignCandidateToEmployer(array $data)
    {
        try {

            $isEmployerPlus = ($data['fromreq'] ?? '') === 'employerplus';

            $employerModel = $isEmployerPlus
                ? Employerplus::class
                : Employer::class;

            $employerColumn = $isEmployerPlus
                ? 'emp_id'
                : 'emp2_id';

            $visaEditId = $data['visaeditid'];
            $candId     = $data['cand_id'];
            $proffId    = $data['proff_id'];


            // Get employer
            $partner = $employerModel::find($visaEditId);

            if (!$partner) {
                return [
                    'status' => 0,
                    'message' => 'Employer not found.'
                ];
            }


            // Check profession exists for this employer
            $checkEmployersOpening = $employerModel::whereRaw(
                "FIND_IN_SET(?, proff_id)",
                [$proffId]
            )
            ->where('id', $visaEditId)
            ->exists();

            if (!$checkEmployersOpening) {
                return [
                    'status' => 0,
                    'message' => 'Profession is not available for this employer.'
                ];
            }


            // Get current assigned candidates
            $checkAssignEmp = Employercandidate::where(
                $employerColumn,
                $visaEditId
            )
            ->where('proff_id', $proffId)
            ->where('status', 1)
            ->count();


            // Get profession openings
            $proffIds = array_map(
                'trim',
                explode(',', $partner->proff_id)
            );

            $proffOpenings = array_map(
                'trim',
                explode(',', $partner->openings)
            );


            $openingIndex = array_search(
                (string) $proffId,
                $proffIds,
                true
            );


            if ($openingIndex === false) {
                return [
                    'status' => 0,
                    'message' => 'Profession opening not found.'
                ];
            }


            $currentOpening = (int) ($proffOpenings[$openingIndex] ?? 0);


            // Check visa slot
            // if ($currentOpening <= $checkAssignEmp) {
            //     return [
            //         'status' => 0,
            //         'message' => 'Visa slot not available for this candidate!'
            //     ];
            // }


            // Assign candidate
            $postAssign = new Employercandidate();

            $postAssign->{$employerColumn} = $visaEditId;
            $postAssign->cand_id = $candId;
            $postAssign->proff_id = $proffId;
            $postAssign->partneroffice_id = $partner->partneroffice_id;
            $postAssign->assignbystaff_id = Auth::guard('admin')->id();
            $postAssign->assignbydate = now();

            $postAssign->save();


            // Update candidate
            Candidate::where('id', $candId)->update([
                'status' => false,
                'cand_payment_status' => Candidate::where('id', $candId)
                    ->value('cand_payment_status') ?: 'Unpaid'
            ]);


            return [
                'status' => 1,
                'message' => 'Candidate successfully assigned'
            ];

        } catch (\Throwable $th) {

            Log::error(
                'Error assigning candidate to employer: ' .
                $th->getMessage(),
                [
                    'data' => $data,
                    'trace' => $th->getTraceAsString()
                ]
            );

            return [
                'status' => 0,
                'message' => $th->getMessage()
            ];
        }
    }

    public static function applClasses()
    {
        // Demo
        $fullURL = request()->fullurl();
        if (App()->environment() === 'production') {
            for ($i = 1; $i < 7; $i++) {
                $contains = Str::contains($fullURL, 'demo-' . $i);
                if ($contains === true) {
                    $data = config('custom.' . 'demo-' . $i);
                }
            }
        } else {
            $data = config('custom.custom');
        }

        // default data array
        $DefaultData = [
          'mainLayoutType' => 'vertical',
          'theme' => 'dark',
          'sidebarCollapsed' => false,
          'navbarColor' => '',
          'horizontalMenuType' => 'floating',
          'verticalMenuNavbarType' => 'floating',
          'footerType' => 'static', //footer
          'layoutWidth' => 'full',
          'showMenu' => true,
          'bodyClass' => '',
          'bodyStyle' => '',
          'pageClass' => '',
          'pageHeader' => true,
          'contentLayout' => 'default',
          'blankPage' => false,
          'defaultLanguage'=>'en',
          'direction' => env('MIX_CONTENT_DIRECTION', 'ltr'),
        ];

        // if any key missing of array from custom.php file it will be merge and set a default value from dataDefault array and store in data variable
        $data = array_merge($DefaultData, $data);

        // All options available in the template
        $allOptions = [
            'mainLayoutType' => array('vertical', 'horizontal'),
            'theme' => array('light' => 'light', 'dark' => 'dark-layout', 'bordered' => 'bordered-layout', 'semi-dark' => 'semi-dark-layout'),
            'sidebarCollapsed' => array(true, false),
            'showMenu' => array(true, false),
            'layoutWidth' => array('full', 'boxed'),
            'navbarColor' => array('bg-primary', 'bg-info', 'bg-warning', 'bg-success', 'bg-danger', 'bg-dark'),
            'horizontalMenuType' => array('floating' => 'navbar-floating', 'static' => 'navbar-static', 'sticky' => 'navbar-sticky'),
            'horizontalMenuClass' => array('static' => '', 'sticky' => 'fixed-top', 'floating' => 'floating-nav'),
            'verticalMenuNavbarType' => array('floating' => 'navbar-floating', 'static' => 'navbar-static', 'sticky' => 'navbar-sticky', 'hidden' => 'navbar-hidden'),
            'navbarClass' => array('floating' => 'floating-nav', 'static' => 'navbar-static-top', 'sticky' => 'fixed-top', 'hidden' => 'd-none'),
            'footerType' => array('static' => 'footer-static', 'sticky' => 'footer-fixed', 'hidden' => 'footer-hidden'),
            'pageHeader' => array(true, false),
            'contentLayout' => array('default', 'content-left-sidebar', 'content-right-sidebar', 'content-detached-left-sidebar', 'content-detached-right-sidebar'),
            'blankPage' => array(false, true),
            'sidebarPositionClass' => array('content-left-sidebar' => 'sidebar-left', 'content-right-sidebar' => 'sidebar-right', 'content-detached-left-sidebar' => 'sidebar-detached sidebar-left', 'content-detached-right-sidebar' => 'sidebar-detached sidebar-right', 'default' => 'default-sidebar-position'),
            'contentsidebarClass' => array('content-left-sidebar' => 'content-right', 'content-right-sidebar' => 'content-left', 'content-detached-left-sidebar' => 'content-detached content-right', 'content-detached-right-sidebar' => 'content-detached content-left', 'default' => 'default-sidebar'),
            'defaultLanguage'=>array('en'=>'en','fr'=>'fr','de'=>'de','pt'=>'pt'),
            'direction' => array('ltr', 'rtl'),
        ];

        //if mainLayoutType value empty or not match with default options in custom.php config file then set a default value
        foreach ($allOptions as $key => $value) {
            if (array_key_exists($key, $DefaultData)) {
                if (gettype($DefaultData[$key]) === gettype($data[$key])) {
                    // data key should be string
                    if (is_string($data[$key])) {
                        // data key should not be empty
                        if (isset($data[$key]) && $data[$key] !== null) {
                            // data key should not be exist inside allOptions array's sub array
                            if (!array_key_exists($data[$key], $value)) {
                                // ensure that passed value should be match with any of allOptions array value
                                $result = array_search($data[$key], $value, 'strict');
                                if (empty($result) && $result !== 0) {
                                    $data[$key] = $DefaultData[$key];
                                }
                            }
                        } else {
                            // if data key not set or
                            $data[$key] = $DefaultData[$key];
                        }
                    }
                } else {
                    $data[$key] = $DefaultData[$key];
                }
            }
        }
        // dd(App()->environment());
        //layout classes
        $layoutClasses = [
            'theme' => $data['theme'],
            'layoutTheme' => $allOptions['theme'][$data['theme']],
            'sidebarCollapsed' => $data['sidebarCollapsed'],
            'showMenu' => $data['showMenu'],
            'layoutWidth' => $data['layoutWidth'],
            'verticalMenuNavbarType' => $allOptions['verticalMenuNavbarType'][$data['verticalMenuNavbarType']],
            'navbarClass' => $allOptions['navbarClass'][$data['verticalMenuNavbarType']],
            'navbarColor' => $data['navbarColor'],
            'horizontalMenuType' => $allOptions['horizontalMenuType'][$data['horizontalMenuType']],
            'horizontalMenuClass' => $allOptions['horizontalMenuClass'][$data['horizontalMenuType']],
            'footerType' => $allOptions['footerType'][$data['footerType']],
            'sidebarClass' => 'menu-expanded',
            'bodyClass' => $data['bodyClass'],
            'bodyStyle' => $data['bodyStyle'],
            'pageClass' => $data['pageClass'],
            'pageHeader' => $data['pageHeader'],
            'blankPage' => $data['blankPage'],
            'blankPageClass' => '',
            'contentLayout' => $data['contentLayout'],
            'sidebarPositionClass' => $allOptions['sidebarPositionClass'][$data['contentLayout']],
            'contentsidebarClass' => $allOptions['contentsidebarClass'][$data['contentLayout']],
            'mainLayoutType' => $data['mainLayoutType'],
            'defaultLanguage'=>$allOptions['defaultLanguage'][$data['defaultLanguage']],
            'direction' => $data['direction'],
        ];
        // set default language if session hasn't locale value the set default language
        if(!session()->has('locale')){
            app()->setLocale($layoutClasses['defaultLanguage']);
        }

        // sidebar Collapsed
        if ($layoutClasses['sidebarCollapsed'] == 'true') {
            $layoutClasses['sidebarClass'] = "menu-collapsed";
        }

        // blank page class
        if ($layoutClasses['blankPage'] == 'true') {
            $layoutClasses['blankPageClass'] = "blank-page";
        }

        return $layoutClasses;
    }
        
    public static function updatePageConfig($pageConfigs)
    {
        $demo = 'custom';
        $fullURL = request()->fullurl();
        if (App()->environment() === 'production') {
            for ($i = 1; $i < 7; $i++) {
                $contains = Str::contains($fullURL, 'demo-' . $i);
                if ($contains === true) {
                    $demo = 'demo-' . $i;
                }
            }
        }
        if (isset($pageConfigs)) {
            if (count($pageConfigs) > 0) {
                foreach ($pageConfigs as $config => $val) {
                    Config::set('custom.' . $demo . '.' . $config, $val);
                }
            }
        }
    }

    public static function sendWhatsappAssignTemplate($lead, $autometanotifications_val)
    {
        // 2️⃣ Assigned Care Officer
        $admin = !empty($lead->leadassign_id)
            ? Admin::find($lead->leadassign_id)
            : null;

        // if($autometanotifications_val->template_name == 'job_seeker_assign_contact'){
        //     Helper::sendWhatsupMsgToUser($lead,$admin,$autometanotifications_val);
        // }

        // 1️⃣ Get WhatsApp template
        $metaTemplate = Metawhatsapptemplate::where('id', $autometanotifications_val->metatemp_id)
            ->where('template_used_for', 'auto_message')
            ->where('status', 1)
            ->first();

        if (!$metaTemplate) {
            Log::warning('WhatsApp template not found', [
                'template_name' => 'khan',
                'lead_id' => $lead->id ?? null,
            ]);
            return;
        }

      
            // 3️⃣ Base payload
        $data = [
            "template_name"     => $metaTemplate->template_name,
            "template_language" => "en",
        ];
        
        
        $country_as_per_location = '---';
        $state_as_per_location   = '---';
        $city_as_per_location    = '---';
        $licenses = [];

        if (!empty($lead->saudi_license) && $lead->saudi_license === 'yes') {
            $licenses[] = 'Saudi License';
        }

        if (!empty($lead->india_license) && $lead->india_license === 'yes') {
            $licenses[] = 'Indian License';
        }

        $country_license = !empty($licenses) ? implode(', ', $licenses) : '---';

        
            if (!empty($lead->submit_lead_from)) {

            $location = json_decode($lead->submit_lead_from, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($location)) {
                $country_as_per_location = $location['country'] ?? '';
                $state_as_per_location   = $location['region'] ?? '';
                $city_as_per_location    = $location['city'] ?? '';
            }
        }


        // 4️⃣ Dynamic meta mapping
        if (!empty($metaTemplate->meta_field_var) && !empty($metaTemplate->meta_assign_ar)) {

            $metaVars   = array_map('trim', explode(',', $metaTemplate->meta_field_var));
            $assignCols = array_map('trim', explode(',', $metaTemplate->meta_assign_ar));

            foreach ($metaVars as $i => $metaKey) {

                $colName = isset($assignCols[$i])
                    ? trim($assignCols[$i], '[]')
                    : '';

                switch ($colName) {

                    case 'Careoff Name':
                        $value = $admin->name ?? null;
                        break;

                    case 'Careoff Contact 1':
                        $value = $admin->care_no_1 ?? null;
                        break;

                    case 'Careoff Contact 2':
                        $value = $admin->care_no_2 ?? null;
                        break;

                    case 'country_as_per_location':
                        $value = $country_as_per_location ?? null;
                        break;

                    case 'state_as_per_location':
                        $value = $state_as_per_location ?? null;
                        break;

                    case 'city_as_per_location':
                        $value = $city_as_per_location ?? null;
                        break;

                    case 'license':
                        $value = $country_license ?? null;
                        break;

                    default:
                        $value = data_get($lead, $colName);
                        break;
                }

                // Replace null, empty string, or whitespace with '--'
                $data[$metaKey] = filled(trim((string) $value)) ? trim((string) $value) : '--';
            }
        }


        // 5️⃣ Get API config
        $metaAPI = Metawhatsappapi::find($metaTemplate->metaapi_id);

        if (!$metaAPI) {
            Log::error('Meta WhatsApp API config not found', [
                'template_id' => $metaTemplate->id
            ]);
            return;
        }

        $endpoint_api = $metaAPI->api_base_url . '/' .
            $metaAPI->vendor_uid . '/contact/send-template-message';

        $mobileNumbers = [];

        if ($autometanotifications_val->template_send_to == 'user') {

            $mobileNumbers = array_filter(array_unique([
                preg_replace('/\D/', '', $lead->whatsapp_no ?? ''),
                preg_replace('/\D/', '', $lead->mob_no ?? ''),
            ]));

        } elseif ($autometanotifications_val->template_send_to === 'admin') {

            $leadowner = Admin::find($lead->leadassign_id);

            $mobileNumbers = array_filter(array_unique([
                preg_replace('/\D/', '', $leadowner?->work_number ?? ''),
            ]));

        } 

        if (empty($mobileNumbers)) {
            Log::warning('No valid mobile numbers found', ['lead_id' => $lead->id]);
            return;
        }


        // 7️⃣ Send message to each number
        foreach ($mobileNumbers as $no) {

            $data['phone_number'] = $no;

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => $endpoint_api,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $metaAPI->api_access_token,
                ],
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => json_encode($data),
            ]);

            $response = curl_exec($curl);
            $error    = curl_error($curl);
            curl_close($curl);

            Log::info('WhatsApp Assign Message Sent', [
                'lead_id' => $lead->id,
                'phone'   => $no,
                'response' => $response,
                'error'   => $error,
            ]);
        }

    }

    public static function sendLeadAssignMail($lead)
    {
        $admin = Admin::find($lead->leadassign_id);

        if (!$admin || empty($admin->working_email)) {
            Log::warning('Assigned admin email not found.', [
                'lead_id' => $lead->id,
                'admin_id' => $lead->leadassign_id,
            ]);

            return;
        }

        try {

            Mail::to($admin->working_email)->send(new LeadAssignMail($lead, $admin));

            Log::info('Lead assignment mail sent.', [
                'lead_id' => $lead->id,
                'email'   => $admin->working_email,
            ]);

        } catch (\Exception $e) {

            Log::error('Lead assignment mail failed.', [
                'lead_id' => $lead->id,
                'email'   => $admin->working_email,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public static function leadActivityLog(int $leadId,array $activity,?int $adminId = null,$module = null): void {

        LeadActivityLog::create([
            'lead_id'  => $leadId,
            'activity' => $activity,
            'admin_id' => $adminId ?? null,
            'module' => $module,
        ]);
    }

    public static function dealActivityLog(int $dealId,array $activity,?int $adminId = null,$module = null): void {

        DealActivityLog::create([
            'deal_id'  => $dealId,
            'activity' => $activity,
            'admin_id' => $adminId ?? null,
            'module' => $module,
        ]);
    }

    public static function testimonialActivityLog(int $testimonialId,array $activity,?int $adminId = null,$module = null): void {

        TestimonialActivityLog::create([
            'testimonial_id' => $testimonialId,
            'activity' => $activity,
            'admin_id' => $adminId ?? null,
            'module' => $module,
        ]);
    }

    public static function fundAdvanceActivityLog(?int $transactionId, array $activity, ?int $settlementId = null, ?int $adminId = null, $module = null): void {

        FundAdvanceActivityLog::create([
            'fund_advance_transaction_id' => $transactionId,
            'fund_advance_settlement_id' => $settlementId,
            'activity' => $activity,
            'admin_id' => $adminId ?? null,
            'module' => $module,
        ]);
    }

    public static function qualificationStatuses($status = null)
    {
        $statuses = [
            null => 'Not Yet',
            1    => 'Followed Up',
            2    => 'Call Not Connected',
            3    => 'Lead Qualified',
            4    => 'Lead Not Qualified',
            5    => 'Lead Not Relevant',
        ];

        if (func_num_args() === 0) {
            return $statuses;
        }

        return $statuses[$status] ?? 'Unknown';
    }


}
