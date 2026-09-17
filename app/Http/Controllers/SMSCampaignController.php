<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Associates;
use App\Models\City;
use App\Models\Contactp;
use App\Models\Contactplus;
use App\Models\Country;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SmsApi;
use App\Models\Groupm;
use App\Models\Contactsendtag;
use Illuminate\Support\Str;
use App\Models\Unsubscribedata;
use App\Models\Adminpermission;
use App\Models\Allcontact;
use App\Models\Campaignlist;
use App\Models\Groupallc;
use App\Models\Whatsappchaturl;
use App\Models\SmsCampaign;
use App\Models\SmsTemplate;
use App\Models\SmsSendLog;
use Illuminate\Support\Facades\Http;
use App\Models\SmsCampaignResponse;

use Carbon\Carbon;

class SMSCampaignController  extends Controller
{
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

        $smsApiLists = SmsApi::orderBy('api_name')->where('status',1)->get();

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

        $post = SmsCampaign::with(['admin','smstemp']);

        if ((Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && $permission->full_access == 1)) {

            if ($request->ajax()) {
                $post->FilterSearchText($request->search_text);

                $posts = $post->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

                return view('admin.sms_campaign.load',compact('posts','permission'));
            }

            $posts = $post->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

            return view('admin.sms_campaign.index',compact('posts','groupms','smsApiLists','campaig_rate','countries','careoffs','groupallcs','businesstypes','permission'));

        }elseif (isset($permission) && $permission->full_access == 0) {
        
                if ($request->ajax()) {
                    $post->FilterSearchText($request->search_text);

                    $posts = $post->where('admin','=', $userID)->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

                    return view('admin.sms_campaign.load',compact('posts','permission'));
                }

                $posts = $post->where('admin_id','=',$userID)->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

                return view('admin.sms_campaign.index',compact('posts','groupms','smsApiLists','campaig_rate','countries','careoffs','groupallcs','businesstypes','permission'));

        }


    }

    public function getListTemp(Request $request){
       
        $audience = $request->audience;
        $sms_api_id = $request->sms_api_id;

        $userD = Auth::guard('admin')->user();
        $userpermission = Adminpermission::where('staff_id','=',$userD->id)->first();

        if ($sms_api_id != '') {
            $tempLists = SMSTemplate::where('template_for','=',$audience)->where('sms_api_id','=',$sms_api_id)->where(function($query){
                $query->where('template_used_for', '!=', 'auto_message')
                  ->orWhereNull('template_used_for');
            })->get();
        }else{
            $tempLists = SMSTemplate::where('template_for','=',$audience)->where(function($query){
                $query->where('template_used_for', '!=', 'auto_message')
                  ->orWhereNull('template_used_for');
            })->get();
        }

        $res = '<option></option>';
        if($tempLists->count() > 0){
            foreach($tempLists as $tempList){
                if ($userD->user_type == 1 || $tempList->public == 1 || $tempList->careoff_id == $userD->id) {
                    $res .= "<option value=".$tempList->id.">".$tempList->template_name."</option>";
                }
             }
        }

        $data['res'] = $res;

        return response()->json($data);
    }

    public function getListTempID(Request $request){
        $post = SmsTemplate::where('id','=',$request->id)->where('status','=',1)->first();

        return response()->json($post);
    }

    public function getsendList(Request $request) {
        $audience = $request->audience;

        if ($audience == 'Leads') {

            $lead_is_qualified = $request->lead_is_qualified;
            $leadassign_id     = $request->leadassign_id;
            $lead_contact_type = $request->lead_contact_type;
        
            // ✅ Build base query dynamically
            $query = DB::table('leads')
                ->when($lead_is_qualified, function ($q, $lead_is_qualified) {
                    $q->whereIn('is_qualified', (array) $lead_is_qualified);
                })
                ->when($leadassign_id, function ($q, $leadassign_id) {
                    $q->whereIn('leadassign_id', (array) $leadassign_id);
                });
        
            // ✅ Apply contact type condition
            if ($lead_contact_type == null) {
                // Count all with either mobile or WhatsApp
                $query->where(function ($sub) {
                    $sub->whereNotNull('mob_no')->where('mob_no', '!=', '')
                        ->orWhere(function ($s) {
                            $s->whereNotNull('whatsapp_no')->where('whatsapp_no', '!=', '');
                        });
                });
            } elseif ($lead_contact_type == 'mob_no') {
                // Count only leads with mobile number
                $query->whereNotNull('mob_no')->where('mob_no', '!=', '');
            } elseif ($lead_contact_type == 'whatsapp_no') {
                // Count only leads with WhatsApp number
                $query->whereNotNull('whatsapp_no')->where('whatsapp_no', '!=', '');
            }
        
            // ✅ Get count directly (no need for ->get()->count())
            $leadCount = $query->count();
        
            // ✅ Prepare response
            if ($leadCount > 0) {
                $data = [
                    "result" => "{$leadCount} leads found and ready to send",
                    "leads"  => $leadCount
                ];
            } else {
                $data = [
                    "result" => "No leads found matching your filters."
                ];
            }
        
            return response()->json($data);
        }        
        elseif ($audience == 'allcontact') {

            $careoff = $request->careoff;
            $group = $request->group;
            $country = $request->country;
            $leadtype = $request->leadtype;
            $allcontsubs = $request->allcontsubs;
            $allcontacttype = $request->allcontacttype;
            $allconmobcountry = $request->allconmobcountry;



            $posts = Allcontact::where(function ($query) use($careoff,$group,$country,$leadtype,$allcontsubs,$allconmobcountry){
                if ($group != '') {
                    $query->whereIn('group_id', (array) $group);
                }

                if ($careoff != '') {
                    $query->whereIn('careoff_id', (array) $careoff);
                }

                if ($country != '') {
                    $query->whereIn('country_id', (array) $country);
                }

                if ($leadtype != '') {
                    $query->whereIn('lead_type', (array) $leadtype);
                }

                if ($allcontsubs != '') {
                    $query->whereIn('optinout', (array) $allcontsubs);
                }

                if ($allconmobcountry != '') {
                    $query->where(function($q) use($allconmobcountry) {

                        foreach ((array) $allconmobcountry as $allconmobcountry2) {
                        $q->orWhere('primary_no_wsp', 'like', $allconmobcountry2 . '%')
                        ->orWhere('secondary_no_wsp', 'like', $allconmobcountry2 . '%')
                        ->orWhere('mobile_no1_wsp', 'like', $allconmobcountry2 . '%');
                }


                });
            }

            })->get();


            if ($posts) {
                $post = 0;

                foreach ($posts as $data_post) {
                    if ($allcontacttype == '4') {
                        if ($data_post->mobile_no1_wsp !='') {
                            if ($allconmobcountry != '') {
                                foreach ((array) $allconmobcountry as $country_code_no_s1) {
                                    if (strpos($data_post->mobile_no1_wsp,$country_code_no_s1) == 0) {
                                        $post++;
                                    }
                                }
                            }else{
                                $post++;
                            }
                        }
                    }elseif ($allcontacttype == '3') {
                        if ($data_post->primary_no_wsp !='') {
                            if ($allconmobcountry != '') {
                                foreach ((array) $allconmobcountry as $country_code_no_s2) {
                                    if (strpos($data_post->primary_no_wsp,$country_code_no_s2) == 0) {
                                        $post++;
                                    }
                                }
                            }else{
                                $post++;
                            }
                        }

                    }elseif ($allcontacttype == '2') {
                        if ($data_post->secondary_no_wsp !='') {

                            if ($allconmobcountry != '') {
                                foreach ((array) $allconmobcountry as $country_code_no_s3) {
                                    if (strpos($data_post->secondary_no_wsp,$country_code_no_s3) == 0) {
                                        $post++;
                                    }
                                }
                            }else{
                                $post++;
                            }
                        }
                    }else{
                        if ($data_post->mobile_no1_wsp !='') {

                            if ($allconmobcountry != '') {
                                foreach ((array) $allconmobcountry as $country_code_no_s1) {
                                    if (strpos($data_post->mobile_no1_wsp,$country_code_no_s1) == 0) {
                                        $post++;
                                    }
                                }
                            }else{
                                $post++;
                            }

                        }

                        if ($data_post->primary_no_wsp !='') {

                            if ($allconmobcountry != '') {
                                foreach ((array) $allconmobcountry as $country_code_no_s2) {
                                    if (strpos($data_post->primary_no_wsp,$country_code_no_s2) == 0) {
                                        $post++;
                                    }
                                }
                            }else{
                                $post++;
                            }

                        }

                        if ($data_post->secondary_no_wsp !='') {

                            if ($allconmobcountry != '') {
                                foreach ((array) $allconmobcountry as $country_code_no_s3) {
                                    if (strpos($data_post->secondary_no_wsp,$country_code_no_s3) == 0) {
                                        $post++;
                                    }
                                }
                            }else{
                                $post++;
                            }

                        }
                    }
                }

            }else{
                $post = 0;
            }

            if ($post > 0) {
                $data = [
                    "result" => $post."  Contacts are ready to be send"
                ];
            }else{
                $data = [
                    "result" => "No data Record Found"
                ];
            }


        }elseif ($audience == 'contactp') {

            $ladtypecontactp = $request->ladtypecontactp;
            $subscribecontactp = $request->subscribecontactp;
            $groupcontactp = $request->groupcontactp;
            $careoffcontactp = $request->careoffcontactp;
            $contactpconttype = $request->contactpconttype;

            $posts = Contactplus::where(function($query) use($ladtypecontactp,$subscribecontactp,$groupcontactp,$careoffcontactp){
                if ($ladtypecontactp != '') {
                    $query->whereIn('businesstype_id', (array) $ladtypecontactp);
                }
                if ($subscribecontactp != '') {
                    $query->whereIn('subscribe', (array) $subscribecontactp);
                }

                if ($groupcontactp != '') {
                    $query->whereIn('group_id', (array) $groupcontactp);
                }

                if ($careoffcontactp != '') {
                    $query->whereIn('careoff_id', (array) $careoffcontactp);
                }
            })->get();

            if ($posts) {
                $post = 0;
                foreach ($posts as $data_post) {
                    if ($contactpconttype == '4') {
                        if ($data_post->sec_contact !='') {
                            $post++;
                        }
                    }elseif ($contactpconttype == '3') {
                        if ($data_post->prim_contact !='') {
                            $post++;
                        }

                    }elseif ($contactpconttype == '2') {
                        if ($data_post->owner_contact !='') {
                            $post++;
                        }
                    }else{
                        if ($data_post->sec_contact !='') {
                            $post++;
                        }

                        if ($data_post->prim_contact !='') {
                            $post++;
                        }

                        if ($data_post->owner_contact !='') {
                            $post++;
                        }
                    }
                }
            } else {
                $post = 0;
            }

            if ($post > 0) {
                $data = [
                    "result" => $post."  Contacts are ready to be send"
                ];
            }else{
                $data = [
                    "result" => "No data Record Found"
                ];
            }

        }elseif ($audience == 'associate') {
            $post = Associates::count();

            if ($post > 0) {
                $data = [
                    "result" => $post."  Contacts are ready to be send"
                ];
            }else{
                $data = [
                    "result" => "No data Record Found"
                ];
            }

        }elseif ($audience == 'client') {
            $post = User::count();

            if ($post > 0) {
                $data = [
                    "result" => $post."  Contacts are ready to be send"
                ];
            }else{
                $data = [
                    "result" => "No data Record Found"
                ];
            }


        }elseif ($audience == 'partner') {
            $post = Partner::count();

            if ($post > 0) {
                $data = [
                    "result" => $post."  Contacts are ready to be send"
                ];
            }else{
                $data = [
                    "result" => "No data Record Found"
                ];
            }

        }else{
            $data = [
                "result" => "No data Record Found"
            ];
        }

        return response()->json($data);
    }

    public function store(Request $request) {
        try {
            $smsAPI      = SmsApi::find($request->sms_api_id);
            $smsTemplate = SmsTemplate::find($request->sms_template_id);
            $audience    = $request->audience;
            $message     = trim($request->sms_msg);
            $send_time   = $request->send_time ?? now();
            $adminId     = Auth::guard('admin')->user()->id;

            // 🔹 Create campaign entry
            $campaign = new SmsCampaign();
            $campaign->campaign_name = $request->campaign_name;
            $campaign->audience      = $audience;
            $campaign->msg_body      = $message;
            $campaign->admin_id      = $adminId;
            $campaign->sms_temp_id   = $request->sms_template_id;
            $campaign->sms_api_id    = $request->sms_api_id;
            $campaign->sch_type      = $request->sch_type;
            $campaign->date_and_time = ($request->sch_type == 'Scheduled') ? Carbon::parse($request->date_and_time)->format('Y-m-d H:i:s') : Carbon::parse($send_time)->format('Y-m-d H:i:s');
            $campaign->save();

            // 🔹 Handle Scheduled
            if ($request->sch_type === 'Scheduled') {

                $campaign->update([
                    'raw_request_data' => json_encode($request->all()),
                    'message_status'          => 'Scheduled',
                    'message_text'            => 'Message scheduled for later sending.',
                ]);

                return redirect()->back()->with('success', 'Campaign scheduled successfully.');
            }

           
            // 🔹 Collect contacts (simplified)
            $mobileNumbers = $this->collectCampaignContacts($request, $audience);

            if (empty($mobileNumbers)) {
                $campaign->update([
                    'message_status' => 'Failed',
                    'message_text'   => 'No valid contacts found'
                ]);

                SmsCampaignResponse::create([
                    'message_status'  => 'Failed',
                    'message_text'    => 'No valid contacts found',
                    'sms_campaign_id' => $campaign->id,
                ]);

                return redirect()->back()->with('error', 'No valid contacts found to send SMS');
            }

            // ✅ Send & Log Each Response
            $responseData = $this->sendSmsViaApi($smsAPI, $message, $mobileNumbers, $campaign->id, $audience);

          
            // ✅ Update campaign summary
            $campaign->update([
                'message_status' => $responseData['totalSent'] > 0 ? 'Success' : 'Failed',
                'message_text'   => "SMS sent to {$responseData['totalSent']} of " . count($mobileNumbers) . " contacts",
            ]);


            return redirect()->back()->with('success', "{$responseData['totalSent']} SMS sent successfully!");

        } catch (\Throwable $th) {
            \Log::error('SMS Campaign Error', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            if (isset($campaign)) {
                  
                $campaign->update([
                    'message_status' => 'Failed',
                    'message_text'   => 'Exception: ' . $th->getMessage(),
                ]);

                SmsCampaignResponse::create([
                    'message_status'  => 'failed',
                    'message_text'    => 'Exception: ' . $th->getMessage(),
                    'sms_campaign_id' => $campaign->id,
                    'main_response'   => $th->getTraceAsString(),
                ]);
            }

            return redirect()->back()->with('error', 'Something went wrong: ' . $th->getMessage());
        }
    }

    private function collectCampaignContacts(Request $request, $audience) {
        $contacts = [];
    
        /*
        |--------------------------------------------------------------------------
        | LEADS
        |--------------------------------------------------------------------------
        */
        if ($audience == 'Leads') {
            $lead_is_qualified = $request->lead_is_qualified;
            $leadassign_id     = $request->leadassign_id;
            $lead_contact_type = $request->lead_contact_type;
    
            $query = DB::table('leads')
                ->when($lead_is_qualified, fn($q) => $q->whereIn('is_qualified', (array) $lead_is_qualified))
                ->when($leadassign_id, fn($q) => $q->whereIn('leadassign_id', (array) $leadassign_id));
    
            if (empty($lead_contact_type)) {
                $query->where(function ($sub) {
                    $sub->whereNotNull('mob_no')->where('mob_no', '!=', '')
                        ->orWhereNotNull('whatsapp_no')->where('whatsapp_no', '!=', '');
                });
            } elseif ($lead_contact_type == 'mob_no') {
                $query->whereNotNull('mob_no')->where('mob_no', '!=', '');
            } elseif ($lead_contact_type == 'whatsapp_no') {
                $query->whereNotNull('whatsapp_no')->where('whatsapp_no', '!=', '');
            }
    
            $leads = $query->get(['id', 'cand_name', 'mob_no', 'whatsapp_no']);
    
            foreach ($leads as $lead) {
                $name = $lead->cand_name ?? null;
                if ($lead_contact_type == 'mob_no' && $lead->mob_no) {
                    $contacts[] = ['id' => $lead->id, 'name' => $name, 'mobile' => $lead->mob_no];
                } elseif ($lead_contact_type == 'whatsapp_no' && $lead->whatsapp_no) {
                    $contacts[] = ['id' => $lead->id, 'name' => $name, 'mobile' => $lead->whatsapp_no];
                } else {
                    if ($lead->mob_no) $contacts[] = ['id' => $lead->id, 'name' => $name, 'mobile' => $lead->mob_no];
                    if ($lead->whatsapp_no) $contacts[] = ['id' => $lead->id, 'name' => $name, 'mobile' => $lead->whatsapp_no];
                }
            }
        }
    
        /*
        |--------------------------------------------------------------------------
        | ALLCONTACT
        |--------------------------------------------------------------------------
        */
        elseif ($audience == 'allcontact') {
            $group        = $request->groupmallc;
            $careoff      = $request->careoff_id2;
            $country      = $request->country_id;
            $leadtype     = $request->lead_type;
            $optinout     = $request->allcontact_subscribe;
            $country_code = $request->country_code;
            $contact_type = $request->allcontact_contact_type;
    
            $query = Allcontact::query();
    
            if ($group) $query->whereIn('group_id', $group);
            if ($careoff) $query->whereIn('careoff_id', $careoff);
            if ($country) $query->whereIn('country_id', $country);
            if ($leadtype) $query->whereIn('lead_type', $leadtype);
            if ($optinout) $query->whereIn('optinout', $optinout);
    
            if ($country_code) {
                $query->where(function ($q) use ($country_code) {
                    foreach ($country_code as $code) {
                        $q->orWhere('primary_no_wsp', 'like', $code.'%')
                          ->orWhere('secondary_no_wsp', 'like', $code.'%')
                          ->orWhere('mobile_no1_wsp', 'like', $code.'%');
                    }
                });
            }
    
            $allcontacts = $query->get(['id', 'full_name', 'mobile_no1_wsp', 'primary_no_wsp', 'secondary_no_wsp']);
    
            foreach ($allcontacts as $c) {
                $name = $c->full_name ?? null;
                if ($contact_type == '4' && $c->mobile_no1_wsp)
                    $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->mobile_no1_wsp];
                elseif ($contact_type == '3' && $c->primary_no_wsp)
                    $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->primary_no_wsp];
                elseif ($contact_type == '2' && $c->secondary_no_wsp)
                    $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->secondary_no_wsp];
                else {
                    if ($c->mobile_no1_wsp) $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->mobile_no1_wsp];
                    if ($c->primary_no_wsp) $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->primary_no_wsp];
                    if ($c->secondary_no_wsp) $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->secondary_no_wsp];
                }
            }
        }
    
        /*
        |--------------------------------------------------------------------------
        | CONTACT+
        |--------------------------------------------------------------------------
        */
        elseif ($audience == 'contactp') {
            $group     = $request->contact_group;
            $business  = $request->business_type_contact;
            $careoff   = $request->careoff_id;
            $subscribe = $request->contactp_subscribe;
            $type      = $request->contactp_contact_type;
    
            $query = Contactplus::query();
            if ($group) $query->whereIn('group_id', $group);
            if ($business) $query->whereIn('businesstype_id', $business);
            if ($careoff) $query->whereIn('careoff_id', $careoff);
            if ($subscribe) $query->whereIn('subscribe', $subscribe);
    
            $contactsPlus = $query->get(['id', 'office_eng_name', 'owner_contact', 'prim_contact', 'sec_contact']);
    
            foreach ($contactsPlus as $c) {
                $name = $c->office_eng_name ?? null;
                if ($type == '4' && $c->sec_contact)
                    $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->sec_contact];
                elseif ($type == '3' && $c->prim_contact)
                    $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->prim_contact];
                elseif ($type == '2' && $c->owner_contact)
                    $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->owner_contact];
                else {
                    if ($c->owner_contact) $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->owner_contact];
                    if ($c->prim_contact)  $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->prim_contact];
                    if ($c->sec_contact)   $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->sec_contact];
                }
            }
        }
    
        /*
        |--------------------------------------------------------------------------
        | ASSOCIATES, CLIENTS, PARTNERS
        |--------------------------------------------------------------------------
        */
        elseif ($audience == 'associate') {
            $list = Associates::get(['id', 'pty_full_name', 'mobile_no']);
            foreach ($list as $c) {
                if ($c->mobile_no)
                    $contacts[] = ['id' => $c->id, 'name' => $c->pty_full_name, 'mobile' => $c->mobile_no];
            }
        } elseif ($audience == 'client') {
            $list = User::get(['id', 'name', 'mobile_no']);
            foreach ($list as $c) {
                if ($c->mobile_no)
                    $contacts[] = ['id' => $c->id, 'name' => $c->name, 'mobile' => $c->mobile_no];
            }
        } elseif ($audience == 'partner') {
            $list = Partner::get(['id', 'owner_name', 'mobile_no']);
            foreach ($list as $c) {
                if ($c->mobile_no)
                    $contacts[] = ['id' => $c->id, 'name' => $c->owner_name, 'mobile' => $c->mobile_no];
            }
        }
    
        /*
        |--------------------------------------------------------------------------
        | CLEAN + UNIQUE
        |--------------------------------------------------------------------------
        */
        $contacts = collect($contacts)
            ->filter(fn($c) => !empty($c['mobile']))
            ->unique('mobile')
            ->values()
            ->toArray();
    
        return $contacts;
    }

    private function sendSmsViaApi($smsAPI, $message, $mobileNumbers = [], $campaignId = null, $audience = null){
        $totalSent = 0;
    
        try {
            $base_url = $smsAPI->api_base_url ?? "https://qlogin.zapim.com";
            $send_url = rtrim($base_url, '/') . '/api/v2/SendSMS';
    
            // ✅ Clean & validate numbers
            $cleanNumbers = [];
            foreach ($mobileNumbers as $num) {
                if (is_array($num)) $num = $num['mobile'] ?? '';
    
                if (!is_scalar($num) || empty($num)) continue;
    
                $num = trim((string) $num);
                $num = preg_replace(
                    '/^\+?91|^\+?971|^\+?966|^\+?1|^\+?44|^\+?880|^\+?94|^\+?92|^\+?60|^\+?65|^\+?81|^\+?20/',
                    '',
                    $num
                );
                $num = preg_replace('/\D/', '', $num);
    
                if (strlen($num) >= 8) $cleanNumbers[] = $num;
            }
    
            $cleanNumbers = array_unique($cleanNumbers);
            if (empty($cleanNumbers)) {
                \Log::warning('No valid mobile numbers found for SMS sending.');
                return ['totalSent' => 0];
            }
    
            $chunks = array_chunk($cleanNumbers, 100);
    
            foreach ($chunks as $batch) {
                $payload = [
                    "senderId" => $smsAPI->sender_id ?? "QAMR",
                    "is_Unicode" => false,
                    "is_Flash" => false,
                    "isRegisteredForDelivery" => true,
                    "validityPeriod" => 1440,
                    "dataCoding" => 0,
                    "schedTime" => "",
                    "groupId" => "7",
                    "message" => $message,
                    "mobileNumbers" => implode(',', $batch),
                    "principleEntityId" => $smsAPI->entity_id ?? "1101647460000048498",
                    "templateId" => $smsAPI->template_id ?? "1107176190670884363",
                    "apiKey" => $smsAPI->api_key ?? "+5vVPV8PYZ4UEU/4MbBDUl3ci/wal1pRnsg2/cBixfk=",
                    "clientId" => $smsAPI->client_id ?? "e4b5a2f4-08cd-4159-8807-cb917e74666f"
                ];
    
                try {
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ])->timeout(25)->post($send_url, $payload);
    
                    $data = $response->json();
    
                    if ($response->successful() && isset($data['ErrorCode'])) {
                        $errorCode = $data['ErrorCode'];
                        $errorDesc = $data['ErrorDescription'];
    
                        // ✅ Success case: ErrorCode 0
                        if ($errorCode == 0 && !empty($data['Data'])) {
                            foreach ($data['Data'] as $item) {
                                $msgStatus = ($item['MessageErrorCode'] == 0) ? 'success' : 'failed';
                                $msgText = $item['MessageErrorDescription'] ?? 'Unknown';
                                $mobile = $item['MobileNumber'] ?? null;
    
                                SmsCampaignResponse::create([
                                    'sms_campaign_id' => $campaignId,
                                    'message_status'  => $msgStatus,
                                    'message_text'    => $msgText,
                                    'mobile_no'       => $mobile,
                                    'main_response'   => json_encode($item),
                                    'error_data_field'=> ($item['MessageErrorCode'] == 0) ? null : $item['MessageErrorDescription']
                                ]);
    
                                if ($msgStatus === 'success') $totalSent++;
                            }
                        }
    
                        // ❌ Invalid numbers or API-level error
                        elseif ($errorCode != 0) {
                            foreach ($batch as $num) {
                                SmsCampaignResponse::create([
                                    'sms_campaign_id' => $campaignId,
                                    'message_status'  => 'failed',
                                    'message_text'    => $errorDesc ?? 'Zapim API Error',
                                    'mobile_no'       => $num,
                                    'main_response'   => json_encode($data),
                                    'error_data_field'=> "ErrorCode: {$errorCode}"
                                ]);
                            }
                        }
    
                        // ❌ Empty Data array
                        else {
                            foreach ($batch as $num) {
                                SmsCampaignResponse::create([
                                    'sms_campaign_id' => $campaignId,
                                    'message_status'  => 'failed',
                                    'message_text'    => 'Empty response Data from Zapim',
                                    'mobile_no'       => $num,
                                    'main_response'   => json_encode($data),
                                    'error_data_field'=> 'Empty Data'
                                ]);
                            }
                        }
    
                    } else {
                        // ❌ HTTP Error
                        foreach ($batch as $num) {
                            SmsCampaignResponse::create([
                                'sms_campaign_id' => $campaignId,
                                'message_status'  => 'failed',
                                'message_text'    => 'HTTP Error from Zapim',
                                'mobile_no'       => $num,
                                'main_response'   => $response->body(),
                                'error_data_field'=> 'HTTP failure'
                            ]);
                        }
                    }
    
                } catch (\Throwable $e) {
                    foreach ($batch as $num) {
                        SmsCampaignResponse::create([
                            'sms_campaign_id' => $campaignId,
                            'message_status'  => 'failed',
                            'message_text'    => $e->getMessage(),
                            'mobile_no'       => $num,
                            'error_data_field'=> 'Exception during batch sending'
                        ]);
                    }
    
                    \Log::error('Zapim Batch Exception', [
                        'error' => $e->getMessage(),
                        'payload' => $payload
                    ]);
                }
            }
        } catch (\Throwable $ex) {
            \Log::error('sendSmsViaApi() Exception', [
                'error' => $ex->getMessage(),
                'trace' => $ex->getTraceAsString()
            ]);
    
            SmsCampaignResponse::create([
                'sms_campaign_id' => $campaignId,
                'message_status'  => 'failed',
                'message_text'    => 'Exception during API sending: ' . $ex->getMessage(),
                'mobile_no'       => null,
                'main_response'   => null,
                'error_data_field'=> 'Exception'
            ]);
        }
    
        return ['totalSent' => $totalSent];
    }

    public function show($id){
        // 🔹 Main Campaign
        $campaign = DB::table('sms_campaigns as sms')
            ->leftJoin('partners as partner', 'partner.id', '=', 'sms.partner_id')
            ->leftJoin('associates as associate', 'associate.id', '=', 'sms.associate_id')
            ->leftJoin('users as user', 'user.id', '=', 'sms.client_id')
            ->leftJoin('contactpluses as contactp', 'contactp.id', '=', 'sms.contactp_id')
            ->leftJoin('admins as admin', 'admin.id', '=', 'sms.admin_id')
            ->select(
                'sms.*',
                'partner.owner_name as partner_name',
                'associate.pty_full_name as associate_name',
                'user.name as client_name',
                'contactp.office_eng_name as contactp_name',
                'admin.name as admin_name'
            )
            ->where('sms.id', '=', $id)
            ->first();
    
        if (!$campaign) {
            return redirect()->back()->with('error', 'Campaign not found.');
        }
    
        // 🔹 All responses for this campaign
        $responses = DB::table('sms_campaign_responses')
            ->where('sms_campaign_id', $id)
            ->orderByDesc('id')
            ->get();
    
        return view('admin.sms_campaign.show', compact('campaign', 'responses'));
    }
    
    
}


