<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Groupallc;
use App\Models\Groupm;
use App\Models\Country;
use App\Models\Admin;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignResponse;
use App\Models\EmailTemplate;
use App\Models\EmailSmtp;
use App\Models\Adminpermission;
use App\Models\Allcontact;
use App\Models\Associates;
use App\Models\User;
use App\Models\Partner;
use App\Models\Contactplus;
use App\Mail\GenericCampaignMail;
use App\Models\Basepathstatus;

class EmailCampaignController extends Controller
{
    /**
     * Display all email campaigns.
     */
    public function index(Request $request){

        $careoff = "";
        $group = "";
        $country = "";
        $leadtype = "";
        $allcontsubs = "";
        $allcontacttype = "4";
        $allconmobcountry = "91";

        // Convert comma-separated values to arrays
        $group = $group ? explode(',', $group) : [];
        $careoff = $careoff ? explode(',', $careoff) : [];
        $country = $country ? explode(',', $country) : [];
        $leadtype = $leadtype ? explode(',', $leadtype) : [];
        $allcontsubs = $allcontsubs ? explode(',', $allcontsubs) : [];
        $allconmobcountry = $allconmobcountry ? explode(',', $allconmobcountry) : [];

        $data_posts = Allcontact::where(function ($query) use ($careoff, $group, $country, $leadtype, $allcontsubs, $allconmobcountry){

            if (!empty($group)) {
                $query->whereIn('group_id', $group);
            }

            if (!empty($careoff)) {
                $query->whereIn('careoff_id', $careoff);
            }

            if (!empty($country)) {
                $query->whereIn('country_id', $country);
            }

            if (!empty($leadtype)) {
                $query->whereIn('lead_type', $leadtype);
            }

            if (!empty($allcontsubs)) {
                $query->whereIn('optinout', $allcontsubs);
            }

            if (!empty($allconmobcountry)) {
                 $query->where(function($q) use($allconmobcountry) {

                    foreach ($allconmobcountry as $allconmobcountry2) {
                       $q->orWhere('primary_no_wsp', 'like', $allconmobcountry2 . '%')
                        ->orWhere('secondary_no_wsp', 'like', $allconmobcountry2 . '%')
                        ->orWhere('mobile_no1_wsp', 'like', $allconmobcountry2 . '%');
                    }

                 });
            }

        })->get();

        $total_contact = 0;

        foreach ($data_posts as $data_post) {
            switch ($allcontacttype) {
                case '2':
                    if (!empty($data_post->secondary_no_wsp)) {
                        if (!empty($allconmobcountry)) {
                            foreach ($allconmobcountry as $country_code_no) {
                                if (strpos($data_post->secondary_no_wsp,$country_code_no) === 0) {
                                    $total_contact++;
                                }
                            }
                        }else{
                            $total_contact++;
                        }

                    }
                    break;

                case '3':
                    if (!empty($data_post->primary_no_wsp)) {

                        if (!empty($allconmobcountry)) {
                            foreach ($allconmobcountry as $country_code_no) {
                                if (strpos($data_post->primary_no_wsp,$country_code_no) === 0) {
                                    $total_contact++;
                                }
                            }
                        }else{
                            $total_contact++;
                        }

                    }
                    break;

                case '4':
                    if (!empty($data_post->mobile_no1_wsp)) {

                        if (!empty($allconmobcountry)) {
                            foreach ($allconmobcountry as $country_code_no) {
                                if (strpos($data_post->mobile_no1_wsp,$country_code_no) === 0) {
                                    $total_contact++;
                                }
                            }
                        }else{
                            $total_contact++;
                        }

                    }
                    break;

                default:

                    if (!empty($data_post->secondary_no_wsp)) {

                        if (!empty($allconmobcountry)) {
                            foreach ($allconmobcountry as $country_code_no_1) {
                                if (strpos($data_post->secondary_no_wsp,$country_code_no_1) === 0) {
                                    $total_contact++;
                                }
                            }
                        }else{
                            $total_contact++;
                        }


                    }
                    if (!empty($data_post->primary_no_wsp)) {
                        if (!empty($allconmobcountry)) {
                            foreach ($allconmobcountry as $country_code_no_2) {
                                if (strpos($data_post->primary_no_wsp,$country_code_no_2) === 0) {
                                    $total_contact++;
                                }
                            }
                        }else{
                            $total_contact++;
                        }

                    }
                    if (!empty($data_post->mobile_no1_wsp)) {
                        if (!empty($allconmobcountry)) {
                            foreach ($allconmobcountry as $country_code_no_3) {
                                if (strpos($data_post->mobile_no1_wsp,$country_code_no_3) === 0) {
                                    $total_contact++;
                                }
                            }
                        }else{
                            $total_contact++;
                        }
                    }
                    break;
            }
        }

        $userpermission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        $userID = Auth::guard('admin')->user()->id;

        $groupms = Groupm::orderBy('name','ASC')->get();
        $groupallcs = Groupallc::orderBy('name')->get();
        $businesstypes = DB::table('businesstypes')->orderBy('name')->get();
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        $careoffs = Admin::where('status',1)->orderBy('name')->get();
        $countries = Country::orderBy('name')->get();

        $otp = 'otp'; // Assuming a single value, modify accordingly
        $exceptional = 'campaign,otp';

        $smtpLists = EmailSmtp::where('status',1)->get();

        // Message Records
        $total_send_campaign = 10;
        $total_faild_campaign = 10;
        $total_success_campaign = 10;

        $percent_faild = ($total_faild_campaign / $total_send_campaign) * 100;
        $percent_success = ($total_success_campaign / $total_send_campaign) * 100;

        $campaig_rate = [
            'send' => $total_send_campaign,
            'failed' => $total_faild_campaign,
            'success' => $total_success_campaign,
            'faild_ratio' => $percent_faild,
            'success_ratio' => $percent_success
        ];

        $post = EmailCampaign::with(['smtp', 'emailTemplate', 'admin']);

        if ((Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && $permission->full_access == 1)) {

            if ($request->ajax()) {
                $post->FilterSearchText($request->search_text);

                $posts = $post->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

                return view('admin.email_campaign.load',compact('posts','permission'));
            }

            $posts = $post->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

            return view('admin.email_campaign.index',compact('posts','groupms','smtpLists','campaig_rate','countries','careoffs','groupallcs','businesstypes','permission'));

        }elseif (isset($permission) && $permission->full_access == 0) {
        
                if ($request->ajax()) {
                    $post->FilterSearchText($request->search_text);

                    $posts = $post->where('admin','=', $userID)->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

                    return view('admin.email_campaign.load',compact('posts','permission'));
                }

                $posts = $post->where('admin_id','=',$userID)->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

                return view('admin.email_campaign.index',compact('posts','groupms','smtpLists','campaig_rate','countries','careoffs','groupallcs','businesstypes','permission'));

        }


    }

    /**
     * Get email templates by audience or smtp_id
     */
    public function getListTemp(Request $request)
    {
        $audience = $request->audience;
        $smtp_id = $request->smtp_id;
        $user = Auth::guard('admin')->user();

        $templates = EmailTemplate::query()
            ->when($smtp_id, fn($q) => $q->where('smtp_id', $smtp_id))
            // ->when($audience, fn($q) => $q->where('template_for', $audience))
            ->where('status', 1)
            ->get();

        $options = '<option></option>';
        foreach ($templates as $t) {
            if ($user->user_type == 1 || $t->public == 1 || $t->careoff_id == $user->id) {
                $options .= "<option value='{$t->id}'>{$t->template_name}</option>";
            }
        }

        return response()->json(['res' => $options]);
    }

    /**
     * Get Template By ID
     */
    public function getListTempID(Request $request)
    {

        $template = EmailTemplate::where('id', $request->id)->where('status', 1)->first();
    
        if ($template && $template->attachment) {
            // Generate full file path based on your structure
            $template->attachment_url = asset('admin/assets/images/email-template/' . $template->attachment);
        } else {
            $template->attachment_url = null;
        }
    
        return response()->json($template);
    }

    /**
     * Store new Email Campaign (Send Now or Schedule)
     */
    public function store(Request $request)
    {    
        try {
            $smtp = EmailSmtp::find($request->smtp_id);
            $template = EmailTemplate::find($request->email_template_id);
            $adminId = Auth::guard('admin')->id();
    
            // 🔹 Determine subject & body
            $subject = $template?->subject ?? 'No Subject';
            $body =  $request->email_body ?? $template?->email_body ?? '';
            $emailBg = $request->email_body_bg ?? '#FFFFFF';

            $body = "<div style='background: {$emailBg}; padding:20px;'>" . $body . "</div>";

            $body = str_replace("<table", "<table style='border-collapse: collapse; width: 100%; border: 2px solid #000000;'", $body);
            $body = str_replace("<th", "<th style='border:1px solid #000000; padding:8px; background:#000000; text-align:left;'", $body);
            $body = str_replace("<td", "<td style='border:1px solid #000000; padding:8px;'", $body);

    
            // 🔹 Determine send time
            $send_time = $request->sch_type === 'Scheduled'
                ? Carbon::parse($request->date_and_time)
                : now();
                
    
            // 🔹 Create Campaign
            $campaign = new EmailCampaign();
            $campaign->campaign_name = $request->campaign_name;
            $campaign->audience = $request->audience;
            $campaign->smtp_id = $request->smtp_id;
            $campaign->email_template_id = $request->email_template_id;
            $campaign->email_subject = $subject;
            $campaign->email_body = $body;
            $campaign->email_body_bg = $emailBg;
            $campaign->attachment = $request->attachments;
            $campaign->admin_id = $adminId;
            $campaign->schedule_type = $request->sch_type;
            $campaign->schedule_datetime = $send_time->format('Y-m-d H:i:s');
            $campaign->raw_request_data = json_encode($request->except('_token'));
            $campaign->email_status = $request->sch_type === 'Scheduled' ? 'Scheduled' : 'Pending';
                    $campaign->save();

    
            // 🔹 If Scheduled
            if ($request->sch_type === 'Scheduled') {
                $campaign->update([
                    'email_status' => 'Scheduled',
                    'email_response' => 'Email campaign scheduled successfully.'
                ]);
    
                return redirect()->back()->with('success', 'Email campaign scheduled successfully.');
            }
    
            // 🔹 Collect recipients
            $emails = $this->collectCampaignEmails($request, $request->audience);
    
            if (empty($emails)) {
                $campaign->update([
                    'email_status' => 'Failed',
                    'email_response' => 'No valid email recipients found.'
                ]);
    
                EmailCampaignResponse::create([
                    'email_campaign_id' => $campaign->id,
                    'status' => 'Failed',
                    'response_message' => 'No valid emails found.'
                ]);
    
                return redirect()->back()->with('error', 'No valid recipients found.');
            }
    
            // 🔹 Send immediately
            $result = $this->sendBulkEmails($smtp, $emails, $subject, $body, $campaign->id, $campaign->attachment);
            
            // Make sure values exist
            $success = $result['success'] ?? 0;
            $failed  = $result['failed'] ?? 0;

            $campaign->update([
                'email_status'   => $success > 0 ? 'Success' : 'Failed',
                'email_response' => "{$success} emails sent successfully, {$failed} failed."
            ]);

            return redirect()->back()->with('success', 'Email campaign created successfully and will be sent shortly.');
    
        } catch (\Throwable $th) {
            Log::error('Email Campaign Store Error', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
    
            if (isset($campaign)) {
                $campaign->update([
                    'email_status' => 'Failed',
                    'email_response' => 'Exception: ' . $th->getMessage()
                ]);
            }
    
            return redirect()->back()->with('error', 'Something went wrong: ' . $th->getMessage());
        }
    }

    /**
     * Collect emails based on audience
     */
    public function collectCampaignEmails(Request $request, $audience)
    {
        $finalEmails = [];

        /* ----------------------------------------------------------------------
        LEADS
        ---------------------------------------------------------------------- */
        if ($audience == 'Leads') {


            $lead_is_qualified = $request->lead_is_qualified;
            $leadassign_id     = $request->leadassign_id;

            $posts = DB::table('leads')
                ->when($lead_is_qualified, fn($q) => $q->whereIn('is_qualified', (array) $lead_is_qualified))
                ->when($leadassign_id, fn($q) => $q->whereIn('leadassign_id', (array) $leadassign_id))
                ->whereNotNull('email')->where('email', '!=', '')
                ->pluck('email')
                ->toArray();

            return $this->filterEmails($posts);
        }

        /* ----------------------------------------------------------------------
        ALLCONTACT
        ---------------------------------------------------------------------- */
        if ($audience == 'allcontact') {

            $careoff = $request->careoff_id2;
            $group = $request->groupmallc;
            $country = $request->country_id;
            $leadtype = $request->lead_type;
            $allcontsubs = $request->allcontact_subscribe;
            $allcontacttype = $request->allcontact_contact_type;

            $posts = Allcontact::where(function ($query) use ($careoff, $group, $country, $leadtype, $allcontsubs) {
                if ($group)    $query->whereIn('group_id', (array) $group);
                if ($careoff)  $query->whereIn('careoff_id', (array) $careoff);
                if ($country)  $query->whereIn('country_id', (array) $country);
                if ($leadtype) $query->whereIn('lead_type', (array) $leadtype);
                if ($allcontsubs) $query->whereIn('optinout', (array) $allcontsubs);
            })->get();

            $emailFields = [
                '1' => ['email', 'email0', 'email1', 'email2'],
                '2' => ['email'],
                '3' => ['email0'],
                '4' => ['email1'],
                '5' => ['email2'],
            ];

            $selectedFields = $emailFields[$allcontacttype] ?? ['email'];

            foreach ($posts as $row) {
                foreach ($selectedFields as $field) {
                    if (!empty($row->$field)) {
                        $finalEmails[] = $row->$field;
                    }
                }
            }

            return $this->filterEmails($finalEmails);
        }

        /* ----------------------------------------------------------------------
        CONTACT PLUS
        ---------------------------------------------------------------------- */
        if ($audience == 'contactp') {

            $lad = $request->business_type_contact;
            $sub = $request->contactp_subscribe;
            $grp = $request->contact_group;
            $care = $request->careoff_id;
            $type = $request->contactp_contact_type;

            $posts = Contactplus::where(function($q) use ($lad, $sub, $grp, $care) {
                if ($lad)  $q->whereIn('businesstype_id', (array) $lad);
                if ($sub)  $q->whereIn('subscribe', (array) $sub);
                if ($grp)  $q->whereIn('group_id', (array) $grp);
                if ($care) $q->whereIn('careoff_id', (array) $care);
            })->get();

            $emailFields = [
                '1' => ['office_email', 'prim_email', 'sec_email', 'owenr_email'],
                '2' => ['office_email'],
                '3' => ['prim_email'],
                '4' => ['sec_email'],
                '5' => ['owenr_email'],
            ];

            $selectedFields = $emailFields[$type] ?? ['office_email'];

            foreach ($posts as $row) {
                foreach ($selectedFields as $field) {
                    if (!empty($row->$field)) {
                        $finalEmails[] = $row->$field;
                    }
                }
            }

            return $this->filterEmails($finalEmails);
        }

        /* ----------------------------------------------------------------------
        ASSOCIATES
        ---------------------------------------------------------------------- */
        if ($audience == 'associate') {

            $status = $request->assoc_status;

            $emails = Associates::when($status, fn($q) => $q->whereIn('status', (array) $status))
                ->whereNotNull('pty_email')->where('pty_email', '!=', '')
                ->pluck('pty_email')
                ->toArray();

            return $this->filterEmails($emails);
        }

        /* ----------------------------------------------------------------------
        CLIENT
        ---------------------------------------------------------------------- */
        if ($audience == 'client') {

            $status = $request->client_status;

            $emails = User::when($status, fn($q) => $q->whereIn('status', (array) $status))
                ->whereNotNull('email')->where('email', '!=', '')
                ->pluck('email')
                ->toArray();

            return $this->filterEmails($emails);
        }

        /* ----------------------------------------------------------------------
        PARTNER
        ---------------------------------------------------------------------- */
        if ($audience == 'partner') {

            $status = $request->partner_status;
            $type   = $request->partner_contact_type;

            $posts = Partner::when($status, fn($q) => $q->whereIn('status', (array) $status))
                ->get();

            $emailFields = [
                '1' => ['email', 'primary_email', 'secondary_email', 'portal_email'], // all
                '2' => ['email'],
                '3' => ['primary_email'],
                '4' => ['secondary_email'],
                '5' => ['portal_email'],
            ];

            $selectedFields = $emailFields[$type] ?? ['email'];

            foreach ($posts as $row) {
                foreach ($selectedFields as $field) {
                    if (!empty($row->$field)) {
                        $finalEmails[] = $row->$field;
                    }
                }
            }

            return $this->filterEmails($finalEmails);
        }

        return [];
    }

    public function filterEmails($emails)
    {
        return array_values(
            array_unique(
                array_filter($emails, function ($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                })
            )
        );
    }

    /**
     * Send emails using selected SMTP
     */
    public function sendBulkEmails($smtp, $emails, $subject, $body, $campaignId, $attachment = null)
    {
        $success = 0;
        $failed = 0;

        try {
            // SMTP config
            config([
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => $smtp->mail_host,
                'mail.mailers.smtp.port' => $smtp->mail_port,
                'mail.mailers.smtp.username' => $smtp->mail_username,
                'mail.mailers.smtp.password' => $smtp->mail_password,
                'mail.mailers.smtp.encryption' => $smtp->mail_encryption,
                'mail.from.address' => $smtp->from_address,
                'mail.from.name' => $smtp->from_name,
            ]);

            // A campaign sends through the SMTP chosen for it (not the
            // default failover mailer): a fresh "smtp" mailer with the
            // settings above.
            Mail::purge('smtp');
            $campaignMailer = Mail::mailer('smtp');

            foreach ($emails as $email) {
                try {

                    // IMPORTANT FIX → send directly to string email
                    $campaignMailer->to($email)->send(
                        new GenericCampaignMail($subject, $body, $attachment)
                    );

                    EmailCampaignResponse::create([
                        'email_campaign_id' => $campaignId,
                        'email' => $email,
                        'status' => 'Success',
                        'response_message' => 'Email sent successfully'
                    ]);

                    $success++;

                } catch (\Throwable $e) {

                    EmailCampaignResponse::create([
                        'email_campaign_id' => $campaignId,
                        'email' => $email,
                        'status' => 'Failed',
                        'response_message' => $e->getMessage()
                    ]);

                    $failed++;
                }
            }

        } catch (\Throwable $th) {

            $campaign = EmailCampaign::find($campaignId);

            $campaign->update([
                'email_status'   => $success > 0 ? 'Success' : 'Failed',
                'email_response' => "{$success} emails sent successfully, {$failed} failed."
            ]);

            Log::error('SMTP Send Error: '.$th->getMessage());
        }

        return ['success' => $success, 'failed' => $failed];
    }


    /**
     * Show campaign details and responses
     */
    public function show($id)
    {
        $campaign = EmailCampaign::with(['smtp', 'emailTemplate', 'admin'])->find($id);

        if (!$campaign) {
            return redirect()->back()->with('error', 'Email campaign not found.');
        }

        $responses = EmailCampaignResponse::where('email_campaign_id', $id)->orderByDesc('id')->get();

        return view('admin.email_campaign.show', compact('campaign', 'responses'));
    }

    public function getsendList(Request $request) {

        // ----------------------------------------------------------------------
        // EMAIL FILTER FUNCTION (INSIDE YOUR METHOD)
        // ----------------------------------------------------------------------
        $filterEmails = function($emails) {
            return array_values(
                array_unique(
                    array_filter($emails, function ($email) {
                        return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
                    })
                )
            );
        };
        // ----------------------------------------------------------------------
    
        $audience = $request->audience;
    
        /* ============================================================
           LEADS
        ============================================================ */
        if ($audience == 'Leads') {
    
            $lead_is_qualified = $request->lead_is_qualified;
            $leadassign_id     = $request->leadassign_id;
        
            $emails = DB::table('leads')
                ->when($lead_is_qualified, fn($q) => $q->whereIn('is_qualified', (array) $lead_is_qualified))
                ->when($leadassign_id, fn($q) => $q->whereIn('leadassign_id', (array) $leadassign_id))
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->pluck('email')
                ->toArray();
    
            $valid = $filterEmails($emails);
    
            return response()->json([
                "result" => count($valid) > 0 ? count($valid)." leads found and ready to send" : "No leads found"
            ]);
        }
    
        /* ============================================================
           ALLCONTACT
        ============================================================ */
        elseif ($audience == 'allcontact') {
    
            $careoff = $request->careoff;
            $group = $request->group;
            $country = $request->country;
            $leadtype = $request->leadtype;
            $allcontsubs = $request->allcontsubs;
            $allcontacttype = $request->allcontacttype;

    
            $posts = Allcontact::where(function ($query) use($careoff,$group,$country,$leadtype,$allcontsubs){
                if ($group)    $query->whereIn('group_id', (array) $group);
                if ($careoff)  $query->whereIn('careoff_id', (array) $careoff);
                if ($country)  $query->whereIn('country_id', (array) $country);
                if ($leadtype) $query->whereIn('lead_type', (array) $leadtype);
                if ($allcontsubs) $query->whereIn('optinout', (array) $allcontsubs);
            })->get();
    
            $emailFields = [
                '1' => ['email', 'email0', 'email1', 'email2'],
                '2' => ['email'],
                '3' => ['email0'],
                '4' => ['email1'],
                '5' => ['email2'],
            ];
    
            $selected = $emailFields[$allcontacttype] ?? ['email'];
    
            $emails = [];
            foreach ($posts as $row) {
                foreach ($selected as $field) {
                    if (!empty($row->$field)) {
                        $emails[] = $row->$field;
                    }
                }
            }
    
            $valid = $filterEmails($emails);
    
            return response()->json([
                "result" => count($valid) > 0 ? count($valid)." Emails are ready to be send" : "No data Record Found"
            ]);
        }
    
        /* ============================================================
           CONTACT PLUS
        ============================================================ */
        elseif ($audience == 'contactp') {
    
            $lad = $request->ladtypecontactp;
            $sub = $request->subscribecontactp;
            $grp = $request->groupcontactp;
            $care = $request->careoffcontactp;
            $type = $request->contactpconttype;
    
            $posts = Contactplus::where(function($q) use ($lad,$sub,$grp,$care){
                if ($lad)  $q->whereIn('businesstype_id', (array) $lad);
                if ($sub)  $q->whereIn('subscribe', (array) $sub);
                if ($grp)  $q->whereIn('group_id', (array) $grp);
                if ($care) $q->whereIn('careoff_id', (array) $care);
            })->get();
    
            $emailFields = [
                '1' => ['office_email', 'prim_email', 'sec_email', 'owenr_email'],
                '2' => ['office_email'],
                '3' => ['prim_email'],
                '4' => ['sec_email'],
                '5' => ['owenr_email'],
            ];
    
            $selected = $emailFields[$type] ?? ['office_email'];
    
            $emails = [];
            foreach ($posts as $row) {
                foreach ($selected as $field) {
                    if (!empty($row->$field)) {
                        $emails[] = $row->$field;
                    }
                }
            }
    
            $valid = $filterEmails($emails);
    
            return response()->json([
                "result" => count($valid) > 0 ? count($valid)." Emails are ready to be send" : "No data Record Found"
            ]);
        }
    
        /* ============================================================
           ASSOCIATE
        ============================================================ */
        elseif ($audience == 'associate') {
    
            $status = $request->add_status_assoc;
    
            $emails = Associates::when($status, fn($q) => $q->whereIn('status', (array) $status))
                ->whereNotNull('pty_email')
                ->where('pty_email','!=','')
                ->pluck('pty_email')
                ->toArray();
    
            $valid = $filterEmails($emails);
    
            return response()->json([
                "result" => count($valid)." Emails are ready to be send"
            ]);
        }
    
        /* ============================================================
           CLIENT
        ============================================================ */
        elseif ($audience == 'client') {
    
            $status = $request->add_status_client;
    
            $emails = User::when($status, fn($q) => $q->whereIn('status', (array) $status))
                ->whereNotNull('email')->where('email','!=','')
                ->pluck('email')
                ->toArray();
    
            $valid = $filterEmails($emails);
    
            return response()->json([
                "result" => count($valid)." Emails are ready to be send"
            ]);
        }
    
        /* ============================================================
           PARTNER
        ============================================================ */
        elseif ($audience == 'partner') {
    
            $status = $request->add_status_partner;
            $type   = $request->add_contact_type_partner;
    
            $posts = Partner::when($status, fn($q) => $q->whereIn('status', (array) $status))
                ->get();
    
            $emailFields = [
                '1' => ['email', 'primary_email', 'secondary_email','portal_email'],
                '2' => ['email'],
                '3' => ['primary_email'],
                '4' => ['secondary_email'],
                '5' => ['portal_email'],
            ];
    
            $selected = $emailFields[$type] ?? ['email'];
    
            $emails = [];
            foreach ($posts as $row) {
                foreach ($selected as $field) {
                    if (!empty($row->$field)) {
                        $emails[] = $row->$field;
                    }
                }
            }
    
            $valid = $filterEmails($emails);
    
            return response()->json([
                "result" => count($valid)." Emails are ready to be send"
            ]);
        }
    
        return response()->json(["result" => "No data Record Found"]);
    }
    
    // public function getsendList(Request $request) {

        //     $audience = $request->audience;

        //     if ($audience == 'Leads') {

        //         $lead_is_qualified = $request->lead_is_qualified;
        //         $leadassign_id     = $request->leadassign_id;
            
        //         // ✅ Build base query dynamically
        //         $query = DB::table('leads')
        //             ->when($lead_is_qualified, function ($q, $lead_is_qualified) {
        //                 $q->whereIn('is_qualified', (array) $lead_is_qualified);
        //             })
        //             ->when($leadassign_id, function ($q, $leadassign_id) {
        //                 $q->whereIn('leadassign_id', (array) $leadassign_id);
        //             });

        //         $query->where(function ($sub) {
        //             $sub->whereNotNull('email')->where('email', '!=', '');
        //         });
            
        //         // ✅ Get count directly (no need for ->get()->count())
        //         $leadCount = $query->count();
            
        //         // ✅ Prepare response
        //         if ($leadCount > 0) {
        //             $data = [
        //                 "result" => "{$leadCount} leads found and ready to send",
        //                 "leads"  => $leadCount
        //             ];
        //         } else {
        //             $data = [
        //                 "result" => "No leads found matching your filters."
        //             ];
        //         }
            
        //         return response()->json($data);
        //     }  
        //     elseif ($audience == 'allcontact') {

        //         $careoff = $request->careoff;
        //         $group = $request->group;
        //         $country = $request->country;
        //         $leadtype = $request->leadtype;
        //         $allcontsubs = $request->allcontsubs;
        //         $allcontacttype = $request->allcontacttype;
        //         $allconmobcountry = $request->allconmobcountry;
        //         $foremail = true;

        //         $posts = Allcontact::where(function ($query) use($careoff,$group,$country,$leadtype,$allcontsubs,$allconmobcountry,$foremail){
        //             if ($group != '') {
        //                 $query->whereIn('group_id', (array) $group);
        //             }

        //             if ($careoff != '') {
        //                 $query->whereIn('careoff_id', (array) $careoff);
        //             }

        //             if ($country != '') {
        //                 $query->whereIn('country_id', (array) $country);
        //             }

        //             if ($leadtype != '') {
        //                 $query->whereIn('lead_type', (array) $leadtype);
        //             }

        //             if ($allcontsubs != '') {
        //                 $query->whereIn('optinout', (array) $allcontsubs);
        //             }

        //             if ($allconmobcountry != '') {
        //                 $query->where(function($q) use($allconmobcountry) {

        //                     foreach ((array) $allconmobcountry as $allconmobcountry2) {
        //                         $q->orWhere('primary_no_wsp', 'like', $allconmobcountry2 . '%')
        //                         ->orWhere('secondary_no_wsp', 'like', $allconmobcountry2 . '%')
        //                         ->orWhere('mobile_no1_wsp', 'like', $allconmobcountry2 . '%');
        //                     }   
        //                 });
        //             }

        //         })->get();


        //         if ($posts) {

        //             $emailFields = [
        //                 '1' => ['email', 'email0', 'email1', 'email2'],
        //                 '2' => ['email'],
        //                 '3' => ['email0'],
        //                 '4' => ['email1'],
        //                 '5' => ['email2'],
        //             ];
                    
        //             $selectedFields = $emailFields[$allcontacttype] ?? ['email'];

        //             $post = 0;
                    
        //             foreach ($posts as $data_post) {
        //                 foreach ($selectedFields as $field) {
        //                     if (!empty($data_post->$field) && filter_var($data_post->$field, FILTER_VALIDATE_EMAIL) && $data_post->$field != '') {
        //                         $post++;
        //                     }
        //                 }
        //             }
                    

        //         }else{
        //             $post = 0;
        //         }

        //         if ($post > 0) {
        //             $data = [
        //                 "result" => $post."  Emails are ready to be send"
        //             ];
        //         }else{
        //             $data = [
        //                 "result" => "No data Record Found"
        //             ];
        //         }


        //     }
        //     elseif ($audience == 'contactp') {

        //         $ladtypecontactp = $request->ladtypecontactp;
        //         $subscribecontactp = $request->subscribecontactp;
        //         $groupcontactp = $request->groupcontactp;
        //         $careoffcontactp = $request->careoffcontactp;
        //         $contactpconttype = $request->contactpconttype;

        //         $posts = Contactplus::where(function($query) use($ladtypecontactp,$subscribecontactp,$groupcontactp,$careoffcontactp){
        //             if ($ladtypecontactp != '') {
        //                 $query->whereIn('businesstype_id', (array) $ladtypecontactp);
        //             }
        //             if ($subscribecontactp != '') {
        //                 $query->whereIn('subscribe', (array) $subscribecontactp);
        //             }

        //             if ($groupcontactp != '') {
        //                 $query->whereIn('group_id', (array) $groupcontactp);
        //             }

        //             if ($careoffcontactp != '') {
        //                 $query->whereIn('careoff_id', (array) $careoffcontactp);
        //             }
        //         })->get();

        //         if ($posts) {

        //             $emailFields = [
        //                 '1' => ['office_email', 'prim_email', 'sec_email', 'owenr_email'],
        //                 '2' => ['office_email'],
        //                 '3' => ['prim_email'],
        //                 '4' => ['sec_email'],
        //                 '5' => ['owenr_email'],
        //             ];
                    
        //             $selectedFields = $emailFields[$contactpconttype] ?? ['office_email'];

        //             $post = 0;
                    
        //             foreach ($posts as $data_post) {
        //                 foreach ($selectedFields as $field) {
        //                     if (!empty($data_post->$field) && filter_var($data_post->$field, FILTER_VALIDATE_EMAIL) && $data_post->$field != '') {
        //                         $post++;
        //                     }
        //                 }
        //             }
                    

        //         }else{
        //             $post = 0;
        //         }

        //         if ($post > 0) {
        //             $data = [
        //                 "result" => $post."  Emails are ready to be send"
        //             ];
        //         }else{
        //             $data = [
        //                 "result" => "No data Record Found"
        //             ];
        //         }

        //     }
        //     elseif ($audience == 'associate') {

        //         $add_status_assoc = $request->add_status_assoc;

        //         $post = Associates::where(function($query) use($add_status_assoc){
        //             if ($add_status_assoc != '') {
        //                 $query->whereIn('status', (array) $add_status_assoc);
        //             }
        //         })->whereNotNull('pty_email')->where('pty_email', '!=', '')->get()->count();

        //         if ($post > 0) {
        //             $data = [
        //                 "result" => $post."  Emails are ready to be send"
        //             ];
        //         }else{
        //             $data = [
        //                 "result" => "No data Record Found"
        //             ];
        //         }
        //     }
        //     elseif ($audience == 'client') {


        //         $add_status_client = $request->add_status_client;

        //         $post = User::where(function($query) use($add_status_client){
        //             if ($add_status_client != '') {
        //                 $query->whereIn('status', (array) $add_status_client);
        //             }
        //         })->whereNotNull('email')->where('email', '!=', '')->get()->count();

        //         if ($post > 0) {
        //             $data = [
        //                 "result" => $post."  Emails are ready to be send"
        //             ];
        //         }else{
        //             $data = [
        //                 "result" => "No data Record Found"
        //             ];
        //         }
        //     }
        //     elseif ($audience == 'partner') {

        //         $partner_status = $request->add_status_partner;
        //         $partner_contact_type = $request->add_contact_type_partner;
        //         $post = 0;
        //         // Get all partner data
        //         $posts = Partner::where(function($query) use($partner_status){
        //             if ($partner_status != '') {
        //                 $query->whereIn('status', (array) $partner_status);
        //             }
        //         })->get();

        //         $post = $posts->count();
            
        //         if ($posts && $partner_contact_type) {
            
        //             // Email fields mapping
        //             $emailFields = [
        //                 '1' => ['email', 'primary_email', 'secondary_email','portal_email'], // All
        //                 '2' => ['email'],                                     // Default email
        //                 '3' => ['primary_email'],                             // Primary
        //                 '4' => ['secondary_email'],    //Secondary
        //                 '5' => ['portal_email']                       // portal
        //             ];
            
        //             $selectedFields = $emailFields[$partner_contact_type] ?? ['email'];
            
                    
            
        //             foreach ($posts as $data_post) {
        //                 foreach ($selectedFields as $field) {
        //                     if (!empty($data_post->$field) && filter_var($data_post->$field, FILTER_VALIDATE_EMAIL)) 
        //                     {
        //                         $post++;
        //                     }
        //                 }
        //             }
            
        //         } else {
        //             $post = 0;
        //         }
            
        //         if ($post > 0) {
        //             $data = [
        //                 "result" => $post . " Emails are ready to be send"
        //             ];
        //         } else {
        //             $data = [
        //                 "result" => "No data Record Found"
        //             ];
        //         }

        //     }else{
        //         $data = [
        //             "result" => "No data Record Found"
        //         ];
        //     }

        //     return response()->json($data);
    // }

    // private function collectCampaignContacts(Request $request, $audience) {
        //     $contacts = [];
        
        //     /*
        //     |--------------------------------------------------------------------------
        //     | LEADS
        //     |--------------------------------------------------------------------------
        //     */
        //     if ($audience == 'Leads') {
        //         $lead_is_qualified = $request->lead_is_qualified;
        //         $leadassign_id     = $request->leadassign_id;
        //         $lead_contact_type = $request->lead_contact_type;
        
        //         $query = DB::table('leads')
        //             ->when($lead_is_qualified, fn($q) => $q->whereIn('is_qualified', (array) $lead_is_qualified))
        //             ->when($leadassign_id, fn($q) => $q->whereIn('leadassign_id', (array) $leadassign_id));
        
        //         if (empty($lead_contact_type)) {
        //             $query->where(function ($sub) {
        //                 $sub->whereNotNull('email')->where('email', '!=', '');
        //             });
        //         } elseif ($lead_contact_type == 'mob_no') {
        //             $query->whereNotNull('mob_no')->where('mob_no', '!=', '');
        //         } elseif ($lead_contact_type == 'whatsapp_no') {
        //             $query->whereNotNull('whatsapp_no')->where('whatsapp_no', '!=', '');
        //         }
        
        //         $leads = $query->get(['id', 'cand_name', 'mob_no', 'whatsapp_no']);
        
        //         foreach ($leads as $lead) {
        //             $name = $lead->cand_name ?? null;
        //             if ($lead_contact_type == 'mob_no' && $lead->mob_no) {
        //                 $contacts[] = ['id' => $lead->id, 'name' => $name, 'mobile' => $lead->mob_no];
        //             } elseif ($lead_contact_type == 'whatsapp_no' && $lead->whatsapp_no) {
        //                 $contacts[] = ['id' => $lead->id, 'name' => $name, 'mobile' => $lead->whatsapp_no];
        //             } else {
        //                 if ($lead->mob_no) $contacts[] = ['id' => $lead->id, 'name' => $name, 'mobile' => $lead->mob_no];
        //                 if ($lead->whatsapp_no) $contacts[] = ['id' => $lead->id, 'name' => $name, 'mobile' => $lead->whatsapp_no];
        //             }
        //         }
        //     }
        
        //     /*
        //     |--------------------------------------------------------------------------
        //     | ALLCONTACT
        //     |--------------------------------------------------------------------------
        //     */
        //     elseif ($audience == 'allcontact') {
        //         $group        = $request->groupmallc;
        //         $careoff      = $request->careoff_id2;
        //         $country      = $request->country_id;
        //         $leadtype     = $request->lead_type;
        //         $optinout     = $request->allcontact_subscribe;
        //         $country_code = $request->country_code;
        //         $contact_type = $request->allcontact_contact_type;
        
        //         $query = Allcontact::query();
        
        //         if ($group) $query->whereIn('group_id', $group);
        //         if ($careoff) $query->whereIn('careoff_id', $careoff);
        //         if ($country) $query->whereIn('country_id', $country);
        //         if ($leadtype) $query->whereIn('lead_type', $leadtype);
        //         if ($optinout) $query->whereIn('optinout', $optinout);
        
        //         if ($country_code) {
        //             $query->where(function ($q) use ($country_code) {
        //                 foreach ($country_code as $code) {
        //                     $q->orWhere('primary_no_wsp', 'like', $code.'%')
        //                       ->orWhere('secondary_no_wsp', 'like', $code.'%')
        //                       ->orWhere('mobile_no1_wsp', 'like', $code.'%');
        //                 }
        //             });
        //         }
        
        //         $allcontacts = $query->get(['id', 'full_name', 'mobile_no1_wsp', 'primary_no_wsp', 'secondary_no_wsp']);
        
        //         foreach ($allcontacts as $c) {
        //             $name = $c->full_name ?? null;
        //             if ($contact_type == '4' && $c->mobile_no1_wsp)
        //                 $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->mobile_no1_wsp];
        //             elseif ($contact_type == '3' && $c->primary_no_wsp)
        //                 $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->primary_no_wsp];
        //             elseif ($contact_type == '2' && $c->secondary_no_wsp)
        //                 $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->secondary_no_wsp];
        //             else {
        //                 if ($c->mobile_no1_wsp) $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->mobile_no1_wsp];
        //                 if ($c->primary_no_wsp) $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->primary_no_wsp];
        //                 if ($c->secondary_no_wsp) $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->secondary_no_wsp];
        //             }
        //         }
        //     }
        
        //     /*
        //     |--------------------------------------------------------------------------
        //     | CONTACT+
        //     |--------------------------------------------------------------------------
        //     */
        //     elseif ($audience == 'contactp') {
        //         $group     = $request->contact_group;
        //         $business  = $request->business_type_contact;
        //         $careoff   = $request->careoff_id;
        //         $subscribe = $request->contactp_subscribe;
        //         $type      = $request->contactp_contact_type;
        
        //         $query = Contactplus::query();
        //         if ($group) $query->whereIn('group_id', $group);
        //         if ($business) $query->whereIn('businesstype_id', $business);
        //         if ($careoff) $query->whereIn('careoff_id', $careoff);
        //         if ($subscribe) $query->whereIn('subscribe', $subscribe);
        
        //         $contactsPlus = $query->get(['id', 'office_eng_name', 'owner_contact', 'prim_contact', 'sec_contact']);
        
        //         foreach ($contactsPlus as $c) {
        //             $name = $c->office_eng_name ?? null;
        //             if ($type == '4' && $c->sec_contact)
        //                 $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->sec_contact];
        //             elseif ($type == '3' && $c->prim_contact)
        //                 $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->prim_contact];
        //             elseif ($type == '2' && $c->owner_contact)
        //                 $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->owner_contact];
        //             else {
        //                 if ($c->owner_contact) $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->owner_contact];
        //                 if ($c->prim_contact)  $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->prim_contact];
        //                 if ($c->sec_contact)   $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->sec_contact];
        //             }
        //         }
        //     }
        
        //     /*
        //     |--------------------------------------------------------------------------
        //     | ASSOCIATES, CLIENTS, PARTNERS
        //     |--------------------------------------------------------------------------
        //     */
        //     elseif ($audience == 'associate') {
        //         $list = Associates::get(['id', 'pty_full_name', 'mobile_no']);
        //         foreach ($list as $c) {
        //             if ($c->mobile_no)
        //                 $contacts[] = ['id' => $c->id, 'name' => $c->pty_full_name, 'mobile' => $c->mobile_no];
        //         }
        //     } elseif ($audience == 'client') {
        //         $list = User::get(['id', 'name', 'mobile_no']);
        //         foreach ($list as $c) {
        //             if ($c->mobile_no)
        //                 $contacts[] = ['id' => $c->id, 'name' => $c->name, 'mobile' => $c->mobile_no];
        //         }
        //     } elseif ($audience == 'partner') {
        //         $list = Partner::get(['id', 'owner_name', 'mobile_no']);
        //         foreach ($list as $c) {
        //             if ($c->mobile_no)
        //                 $contacts[] = ['id' => $c->id, 'name' => $c->owner_name, 'mobile' => $c->mobile_no];
        //         }
        //     }
        
        //     /*
        //     |--------------------------------------------------------------------------
        //     | CLEAN + UNIQUE
        //     |--------------------------------------------------------------------------
        //     */
        //     $contacts = collect($contacts)
        //         ->filter(fn($c) => !empty($c['mobile']))
        //         ->unique('mobile')
        //         ->values()
        //         ->toArray();
        
        //     return $contacts;
    // }

    // private function sendBulkEmails($smtp, $emails, $subject, $body, $campaignId)
    // {
    //     $success = 0;
    //     $failed = 0;

    //     try {
    //         // ✅ Configure SMTP dynamically
    //         config([
    //             'mail.mailers.smtp.transport' => 'smtp',
    //             'mail.mailers.smtp.host' => $smtp->smtp_host,
    //             'mail.mailers.smtp.port' => $smtp->smtp_port,
    //             'mail.mailers.smtp.username' => $smtp->smtp_user,
    //             'mail.mailers.smtp.password' => $smtp->smtp_pass,
    //             'mail.mailers.smtp.encryption' => $smtp->smtp_encryption,
    //             'mail.from.address' => $smtp->from_email,
    //             'mail.from.name' => $smtp->from_name,
    //         ]);

    //         foreach ($emails as $email) {
    //             try {
    //                 Mail::to($email['email'])->send(new GenericCampaignMail($subject, $body));
    //                 EmailCampaignResponse::create([
    //                     'email_campaign_id' => $campaignId,
    //                     'email' => $email['email'],
    //                     'status' => 'Success',
    //                     'response_message' => 'Email sent successfully'
    //                 ]);
    //                 $success++;
    //             } catch (\Throwable $e) {
    //                 EmailCampaignResponse::create([
    //                     'email_campaign_id' => $campaignId,
    //                     'email' => $email['email'],
    //                     'status' => 'Failed',
    //                     'response_message' => $e->getMessage()
    //                 ]);
    //                 $failed++;
    //             }
    //         }

    //     } catch (\Throwable $th) {
    //         Log::error('SMTP Send Error: ' . $th->getMessage());
    //     }

    //     return ['success' => $success, 'failed' => $failed];
    // }

}
