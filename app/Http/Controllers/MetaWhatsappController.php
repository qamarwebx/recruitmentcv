<?php

namespace App\Http\Controllers;

use App\Jobs\AllcontactSendMetaJob;
use App\Jobs\SendMetaLeadJob;
use App\Jobs\ContactplusSendMetaJob;
use App\Jobs\ResendMetawhastapppcampaign;
use App\Models\Admin;
use App\Models\Associates;
use App\Models\City;
use App\Models\Contactp;
use App\Models\Contactplus;
use App\Models\Country;
use App\Models\Metawhatsappcampaign;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Metawhatsappcampaignresponse;
use App\Models\Metawhatsappapi;
use App\Models\Metawhatsapptemplate;
use App\Models\Groupm;
use App\Models\Contactsendtag;
use Illuminate\Support\Str;
use App\Models\Unsubscribedata;
use App\Models\Adminpermission;
use App\Models\Allcontact;
use App\Models\Campaignlist;
use App\Models\Groupallc;
use App\Models\Whatsappchaturl;
use App\Models\Lead;
use Illuminate\Support\Facades\Log;
use App\Models\MetaWhatsupCampaignFilter;

class MetaWhatsappController extends Controller
{
    
    public function index(Request $request)
    {
        $admin  = Auth::guard('admin')->user();
        $userID = $admin->id;
    
        // =====================================================
        // PERMISSIONS
        // =====================================================
        $permission = Adminpermission::where('staff_id', $userID)->first();
        $isSuper = ($admin->user_type == 1) || ($permission && $permission->full_access == 1);
    
        // =====================================================
        // MASTER DATA (VIEW)
        // =====================================================
        $groupms        = Groupm::orderBy('name')->get();
        $groupallcs     = Groupallc::orderBy('name')->get();
        $businesstypes  = DB::table('businesstypes')->orderBy('name')->get();
        $careoffs       = Admin::where('status', 1)->orderBy('name')->get();
        $countries      = Country::orderBy('name')->get();
        $metapaiLists   = Metawhatsappapi::where('status', 1)->orderBy('api_name')->get();
    
        // =====================================================
        // CAMPAIGN STATS
        // =====================================================
        $total_send_campaign    = Metawhatsappcampaignresponse::count();
        $total_faild_campaign   = Metawhatsappcampaignresponse::where('message_status', 'failed')->count();
        $total_success_campaign = Metawhatsappcampaignresponse::where('message_status', 'success')->count();
    
        $campaig_rate = [
            'send'          => $total_send_campaign,
            'failed'        => $total_faild_campaign,
            'success'       => $total_success_campaign,
            'faild_ratio'   => $total_send_campaign ? ($total_faild_campaign / $total_send_campaign) * 100 : 0,
            'success_ratio' => $total_send_campaign ? ($total_success_campaign / $total_send_campaign) * 100 : 0,
        ];
    
        // =====================================================
        // BASE QUERY
        // =====================================================
        $post = Metawhatsappcampaign::with(['admin', 'metatemp']);
    
        // =====================================================
        // APPLY FILTERS (AJAX REQUEST)
        // =====================================================
        if ($request->ajax()) {
    
            if ($request->filled('audience')) {
                $post->whereIn('audience', (array) $request->audience);
            }
    
            if ($request->filled('careoff')) {
                $post->whereIn('careoff_id', (array) $request->careoff);
            }
    
            if ($request->filled('group')) {
                $post->whereIn('group_id', (array) $request->group);
            }
    
            if ($request->filled('created_by')) {
                $post->whereIn('admin_id', (array) $request->created_by);
            }
    
            if ($request->filled('status')) {
                $post->where('message_status', $request->status);
            }

            if ($request->filled('sch_type')) {
                $post->where('sch_type', $request->sch_type);
            }
    
            // ================= LEADS DATE (STRING RANGE) =================
            if ($request->filled('lead_date_range')) {
    
                $range = $request->lead_date_range;
    
                if (str_contains($range, ' to ')) {
                    [$from, $to] = array_map('trim', explode(' to ', $range));
                } else {
                    [$from, $to] = array_map('trim', explode(' - ', $range));
                }
    
                $post->whereNotNull('leads_date')
                     ->where(function ($q) use ($from, $to) {
                         $q->whereRaw(
                             "STR_TO_DATE(SUBSTRING_INDEX(leads_date, ' to ', 1), '%Y-%m-%d') <= ?",
                             [$to]
                         )->whereRaw(
                             "STR_TO_DATE(SUBSTRING_INDEX(leads_date, ' to ', -1), '%Y-%m-%d') >= ?",
                             [$from]
                         );
                     });
            }
    
            // ================= SEND DATE =================
            if ($request->filled('send_date')) {
    
                $range = $request->send_date;
    
                if (str_contains($range, ' to ')) {
                    [$from, $to] = array_map('trim', explode(' to ', $range));
                } else {
                    [$from, $to] = array_map('trim', explode(' - ', $range));
                }
    
                $post->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
            }
    
            if ($request->filled('search_text')) {
                $post->FilterSearchText($request->search_text);
            }
        }
    
        // =====================================================
        // APPLY SAVED FILTER (NON-AJAX FIRST LOAD)
        // =====================================================
        if (!$request->ajax()) {
    
            $savedFilter = MetaWhatsupCampaignFilter::where('admin_id', $userID)->first();
    
            if ($savedFilter) {
    
                if (!empty($savedFilter->audience)) {
                    $post->whereIn('audience', (array) $savedFilter->audience);
                }
    
                if (!empty($savedFilter->careoff)) {
                    $post->whereIn('careoff_id', (array) $savedFilter->careoff);
                }
    
                if (!empty($savedFilter->group)) {
                    $post->whereIn('group_id', (array) $savedFilter->group);
                }
    
                if (!empty($savedFilter->created_by)) {
                    $post->whereIn('admin_id', (array) $savedFilter->created_by);
                }
    
                if (!empty($savedFilter->status)) {
                    $post->where('message_status', $savedFilter->status);
                }
                
                if (!empty($savedFilter->sch_type)) {
                    $post->where('sch_type', $savedFilter->sch_type);
                }

                // ================= LEADS DATE (STRING RANGE) =================
                if (!empty($savedFilter->lead_date_range)) {
    
                    $range = $savedFilter->lead_date_range;
    
                    if (str_contains($range, ' to ')) {
                        [$from, $to] = array_map('trim', explode(' to ', $range));
                    } else {
                        [$from, $to] = array_map('trim', explode(' - ', $range));
                    }
    
                    $post->whereNotNull('leads_date')
                         ->where(function ($q) use ($from, $to) {
                             $q->whereRaw(
                                 "STR_TO_DATE(SUBSTRING_INDEX(leads_date, ' to ', 1), '%Y-%m-%d') <= ?",
                                 [$to]
                             )->whereRaw(
                                 "STR_TO_DATE(SUBSTRING_INDEX(leads_date, ' to ', -1), '%Y-%m-%d') >= ?",
                                 [$from]
                             );
                         });
                }
    
                // ================= SEND DATE =================
                if (!empty($savedFilter->send_date)) {
    
                    $range = $savedFilter->send_date;
    
                    if (str_contains($range, ' to ')) {
                        [$from, $to] = array_map('trim', explode(' to ', $range));
                    } else {
                        [$from, $to] = array_map('trim', explode(' - ', $range));
                    }
    
                    $post->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
                }
            }
        }
    
        // =====================================================
        // ACCESS CONTROL
        // =====================================================
        if (!$isSuper) {
            if (!$permission || $permission->meta_whatsapp_campaign_view != 1) {
                $post->where('admin_id', $userID);
            }
        }
    
        // =====================================================
        // FINAL QUERY
        // =====================================================
        $posts = $post
            ->orderByDesc('id')
            ->paginate($request->page_list ?? 10)
            ->withQueryString();
    
        // =====================================================
        // AJAX RESPONSE
        // =====================================================
        if ($request->ajax()) {
            return view('admin.metawhatsapppcampaign.load', compact('posts', 'permission'));
        }
    
        // =====================================================
        // NORMAL VIEW
        // =====================================================
        return view('admin.metawhatsappcampaign.index', compact(
            'posts',
            'groupms',
            'metapaiLists',
            'campaig_rate',
            'countries',
            'careoffs',
            'groupallcs',
            'businesstypes',
            'permission',
            'savedFilter'
        ));
    }
    
    public function saveCampaignFilter(Request $request){
        MetaWhatsupCampaignFilter::updateOrCreate(
            [
                'admin_id' => Auth::guard('admin')->id()
            ],
            [
                'audience'        => $request->audience,
                'careoff'         => $request->careoff,
                'group'           => $request->group,
                'created_by'      => $request->created_by,
                'lead_date_range' => $request->lead_date_range,
                'send_date'       => $request->send_date,
                'status'          => $request->status,
                'sch_type'        => $request->sch_type
            ]
        );

        return response()->json([
            'status'  => true,
            'message' => 'Campaign filter saved successfully'
        ]);
    }

    public function resetCampaignFilter(){
        MetaWhatsupCampaignFilter::where(
            'admin_id',
            Auth::guard('admin')->id()
        )->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Campaign filter reset successfully'
        ]);
    }

    public function indexBack(Request $request){

        // $careoff = "";
        // $group = "";
        // $country = "";
        // $leadtype = "";
        // $allcontsubs = "";
        // $allcontacttype = "4";
        // $allconmobcountry = "91";

        // // Convert comma-separated values to arrays
        // $group = $group ? explode(',', $group) : [];
        // $careoff = $careoff ? explode(',', $careoff) : [];
        // $country = $country ? explode(',', $country) : [];
        // $leadtype = $leadtype ? explode(',', $leadtype) : [];
        // $allcontsubs = $allcontsubs ? explode(',', $allcontsubs) : [];
        // $allconmobcountry = $allconmobcountry ? explode(',', $allconmobcountry) : [];

        // $data_posts = Allcontact::where(function ($query) use ($careoff, $group, $country, $leadtype, $allcontsubs, $allconmobcountry){

            //     if (!empty($group)) {
            //         $query->whereIn('group_id', $group);
            //     }

            //     if (!empty($careoff)) {
            //         $query->whereIn('careoff_id', $careoff);
            //     }

            //     if (!empty($country)) {
            //         $query->whereIn('country_id', $country);
            //     }

            //     if (!empty($leadtype)) {
            //         $query->whereIn('lead_type', $leadtype);
            //     }

            //     if (!empty($allcontsubs)) {
            //         $query->whereIn('optinout', $allcontsubs);
            //     }

            //     if (!empty($allconmobcountry)) {
            //          $query->where(function($q) use($allconmobcountry) {

            //             // $dial_code_columns = [
            //             //     "secondary_no_wsp_dial_code",
            //             //     "primary_no_wsp_dial_code",
            //             //     "mobile_no1_wsp_dial_code",
            //             // ];

            //             // foreach ($dial_code_columns as $column) {
            //             //     $q->orWhereIn($column, $allconmobcountry);
            //             // }

            //             foreach ($allconmobcountry as $allconmobcountry2) {
            //                $q->orWhere('primary_no_wsp', 'like', $allconmobcountry2 . '%')
            //                 ->orWhere('secondary_no_wsp', 'like', $allconmobcountry2 . '%')
            //                 ->orWhere('mobile_no1_wsp', 'like', $allconmobcountry2 . '%');
            //             }

            //          });
            //     }

        // })->get();

        // $total_contact = 0;

        // foreach ($data_posts as $data_post) {
            //     switch ($allcontacttype) {
            //         case '2':
            //             if (!empty($data_post->secondary_no_wsp)) {
            //                 if (!empty($allconmobcountry)) {
            //                     foreach ($allconmobcountry as $country_code_no) {
            //                         if (strpos($data_post->secondary_no_wsp,$country_code_no) === 0) {
            //                             $total_contact++;
            //                         }
            //                     }
            //                 }else{
            //                     $total_contact++;
            //                 }

            //             }
            //             break;

            //         case '3':
            //             if (!empty($data_post->primary_no_wsp)) {

            //                 if (!empty($allconmobcountry)) {
            //                     foreach ($allconmobcountry as $country_code_no) {
            //                         if (strpos($data_post->primary_no_wsp,$country_code_no) === 0) {
            //                             $total_contact++;
            //                         }
            //                     }
            //                 }else{
            //                     $total_contact++;
            //                 }

            //             }
            //             break;

            //         case '4':
            //             if (!empty($data_post->mobile_no1_wsp)) {

            //                 if (!empty($allconmobcountry)) {
            //                     foreach ($allconmobcountry as $country_code_no) {
            //                         if (strpos($data_post->mobile_no1_wsp,$country_code_no) === 0) {
            //                             $total_contact++;
            //                         }
            //                     }
            //                 }else{
            //                     $total_contact++;
            //                 }

            //             }
            //             break;

            //         default:

            //             if (!empty($data_post->secondary_no_wsp)) {

            //                 if (!empty($allconmobcountry)) {
            //                     foreach ($allconmobcountry as $country_code_no_1) {
            //                         if (strpos($data_post->secondary_no_wsp,$country_code_no_1) === 0) {
            //                             $total_contact++;
            //                         }
            //                     }
            //                 }else{
            //                     $total_contact++;
            //                 }


            //             }
            //             if (!empty($data_post->primary_no_wsp)) {
            //                 if (!empty($allconmobcountry)) {
            //                     foreach ($allconmobcountry as $country_code_no_2) {
            //                         if (strpos($data_post->primary_no_wsp,$country_code_no_2) === 0) {
            //                             $total_contact++;
            //                         }
            //                     }
            //                 }else{
            //                     $total_contact++;
            //                 }

            //             }
            //             if (!empty($data_post->mobile_no1_wsp)) {
            //                 if (!empty($allconmobcountry)) {
            //                     foreach ($allconmobcountry as $country_code_no_3) {
            //                         if (strpos($data_post->mobile_no1_wsp,$country_code_no_3) === 0) {
            //                             $total_contact++;
            //                         }
            //                     }
            //                 }else{
            //                     $total_contact++;
            //                 }
            //             }
            //             break;
            //     }
        // }

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

        // $metapaiLists = Metawhatsappapi::orderBy('api_name')->whereRaw("FIND_IN_SET (?,api_assign_to)",['campaign'])->get();
        $metapaiLists = Metawhatsappapi::orderBy('api_name')->where('status',1)->get();

        // Message Records

        $total_send_campaign = Metawhatsappcampaignresponse::count();
        $total_faild_campaign = Metawhatsappcampaignresponse::where('message_status','=','failed')->count();
        $total_success_campaign = Metawhatsappcampaignresponse::where('message_status','=','success')->count();

        $percent_faild = ($total_faild_campaign / $total_send_campaign) * 100;
        $percent_success = ($total_success_campaign / $total_send_campaign) * 100;

        $campaig_rate = [
            'send' => $total_send_campaign,
            'failed' => $total_faild_campaign,
            'success' => $total_success_campaign,
            'faild_ratio' => $percent_faild,
            'success_ratio' => $percent_success
        ];

        $post = Metawhatsappcampaign::with(['admin','metatemp']);

        if ((Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && $permission->full_access == 1)) {

            if ($request->ajax()) {
                $post->FilterSearchText($request->search_text);

                $posts = $post->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

                return view('admin.metawhatsapppcampaign.load',compact('posts','permission'));
            }

            $posts = $post->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

            return view('admin.metawhatsappcampaign.index',compact('posts','groupms','metapaiLists','campaig_rate','countries','careoffs','groupallcs','businesstypes','permission'));

        }elseif (isset($permission) && $permission->full_access == 0) {

            if ($permission->meta_whatsapp_campaign_view == 1) {

                if ($request->ajax()) {
                    $post->FilterSearchText($request->search_text);

                    $posts = $post->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

                    return view('admin.metawhatsapppcampaign.load',compact('posts','permission'));
                }

                $posts = $post->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

                return view('admin.metawhatsappcampaign.index',compact('posts','groupms','metapaiLists','campaig_rate','countries','careoffs','groupallcs','businesstypes','permission'));

            }else{

                if ($request->ajax()) {
                    $post->FilterSearchText($request->search_text);

                    $posts = $post->where('admin','=', $userID)->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

                    return view('admin.metawhatsapppcampaign.load',compact('posts','permission'));
                }

                $posts = $post->where('admin_id','=',$userID)->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

                return view('admin.metawhatsappcampaign.index',compact('posts','groupms','metapaiLists','campaig_rate','countries','careoffs','groupallcs','businesstypes','permission'));

            }

        }


    }

    public function indexJson(Request $request){

        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $post = DB::table('metawhatsappcampaigns as metawhatsap')
            ->leftJoin('admins as admin','admin.id','=','metawhatsap.care_off_id')
            ->leftJoin('metawhatsapptemplates as metawhatsapptemplate','metawhatsapptemplate.id','=','metawhatsap.metatemp_id')
            ->select('metawhatsap.*','admin.name as caroffname','metawhatsapptemplate.template_name as templatename')
            ->orderBy('metawhatsap.id','DESC')
            ->get();

        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if ($permission->meta_whatsapp_campaign_view == 1) {
                $post = DB::table('metawhatsappcampaigns as metawhatsap')
                ->leftJoin('admins as admin','admin.id','=','metawhatsap.care_off_id')
                ->leftJoin('metawhatsapptemplates as metawhatsapptemplate','metawhatsapptemplate.id','=','metawhatsap.metatemp_id')
                ->select('metawhatsap.*','admin.name as caroffname','metawhatsapptemplate.template_name as templatename')
                ->orderBy('metawhatsap.id','DESC')
                ->get();
            } else {
                $post = DB::table('metawhatsappcampaigns as metawhatsap')
                ->leftJoin('admins as admin','admin.id','=','metawhatsap.care_off_id')
                ->leftJoin('metawhatsapptemplates as metawhatsapptemplate','metawhatsapptemplate.id','=','metawhatsap.metatemp_id')
                ->select('metawhatsap.*','admin.name as caroffname','metawhatsapptemplate.template_name as templatename')
                ->where('metawhatsap.admin_id','=',Auth::guard('admin')->user()->id)
                ->orderBy('metawhatsap.id','DESC')
                ->get();
            }

        }

        $data['data'] = $post;
        return response()->json($data);
    }

    public function getsendList(Request $request) {

        // dd($request->all());
        $audience = $request->audience;

        if ($audience === 'allcontact') {

            // Normalize inputs
            $careoff          = array_filter((array) $request->input('careoff', []));
            $group            = array_filter((array) $request->input('group', []));
            $country          = array_filter((array) $request->input('country', []));
            $leadtype         = array_filter((array) $request->input('leadtype', []));
            $allcontsubs = array_map(
                'intval',
                (array) $request->input('allcontsubs', [])
            );
            $allcontacttype   = $request->input('allcontacttype') ?: 'all';
            $allconmobcountry = array_values(
                array_filter(
                    (array) $request->input('allconmobcountry', []),
                    fn ($v) => is_string($v) && trim($v) !== ''
                )
            );

            $query = Allcontact::query();

            // Filters
            if ($group) {
                $query->whereIn('group_id', $group);
            }

            if ($careoff) {
                $query->whereIn('careoff_id', $careoff);
            }

            if ($country) {
                $query->whereIn('country_id', $country);
            }

            if ($leadtype) {
                $query->whereIn('lead_type', $leadtype);
            }

            // Apply filter safely
            if (count($allcontsubs) > 0) {
                $query->whereIn('optinout', $allcontsubs);
            }

            if ($allconmobcountry) {
                $query->where(function ($q) use ($allconmobcountry) {
                    foreach ($allconmobcountry as $code) {
                        $q->orWhere('primary_no_wsp', 'like', $code . '%')
                        ->orWhere('secondary_no_wsp', 'like', $code . '%')
                        ->orWhere('mobile_no1_wsp', 'like', $code . '%');
                    }
                });
            }

            // 🚀 COUNT LOGIC (SQL)
            switch ($allcontacttype) {

                case '4':
                    $query->whereNotNull('mobile_no1_wsp')
                        ->where('mobile_no1_wsp', '!=', '');
                    break;

                case '3':
                    $query->whereNotNull('primary_no_wsp')
                        ->where('primary_no_wsp', '!=', '');
                    break;

                case '2':
                    $query->whereNotNull('secondary_no_wsp')
                        ->where('secondary_no_wsp', '!=', '');
                    break;

                default:
                    $query->where(function ($q) {
                        $q->whereNotNull('mobile_no1_wsp')->where('mobile_no1_wsp', '!=', '')
                        ->orWhereNotNull('primary_no_wsp')->where('primary_no_wsp', '!=', '')
                        ->orWhereNotNull('secondary_no_wsp')->where('secondary_no_wsp', '!=', '');
                    });
            }

            $post = $query->count();

            return response()->json([
                'result' => $post > 0
                    ? $post . ' Contacts are ready to be send'
                    : 'No data Record Found'
            ]);
        }

        elseif ($audience == 'contactp') {

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

        }
        elseif ($audience == 'leads') {

            $jobTitles        = $request->addleadsjobtitle;     // array
            $jobCountries     = $request->addleadscountry;      // array
            $dateRange        = $request->leadsdaterange;       // "YYYY-MM-DD to YYYY-MM-DD"
            $drivinglicense   = $request->drivinglicense;
            $contacttypeleads = $request->contacttypeleads;     // 1 / 2 / 3
            $careoffleads = $request->careoffleads;     // 1 / 2 / 3
            
            // =====================================================
            // BASE QUERY
            // =====================================================
            $leads = Lead::query();

            // -----------------------------------------------------
            // Job Titles
            // -----------------------------------------------------
            if (!empty($jobTitles)) {
                $leads->whereIn('required_service', $jobTitles);
            }

            // -----------------------------------------------------
            // Careoff 
            // -----------------------------------------------------
            if (!empty($careoffleads)) {
                $leads->whereIn('leadassign_id', $careoffleads);
            }
        
            // -----------------------------------------------------
            // Countries (JSON)
            // -----------------------------------------------------
            if (!empty($jobCountries)) {
                $leads->whereIn(
                    DB::raw("JSON_UNQUOTE(JSON_EXTRACT(submit_lead_from, '$.country'))"),
                    $jobCountries
                );
            }
        
            // -----------------------------------------------------
            // Date Range
            // -----------------------------------------------------
            if (!empty($dateRange)) {
                $dates = explode(' to ', $dateRange);
        
                $start = $dates[0];
                $end   = $dates[1] ?? $dates[0];
        
                $leads->whereBetween(
                    'lead_date',
                    [$start . ' 00:00:00', $end . ' 23:59:59']
                );
            }
        
            // -----------------------------------------------------
            // Driving License
            // -----------------------------------------------------
            if (!empty($drivinglicense)) {
                $leads->filterDrivingLicense($drivinglicense);
            }


            
            // =====================================================
            // UNIQUE CONTACT COUNT (🔥 CORE LOGIC)
            // =====================================================
            if ($contacttypeleads == 1) {
                // 🔥 Mobile OR WhatsApp → UNIQUE COUNT ACROSS BOTH
        
                $mobileQuery = (clone $leads)
                    ->select('mob_no as phone')
                    ->whereNotNull('mob_no')
                    ->where('mob_no', '!=', '');
        
                $whatsappQuery = (clone $leads)
                    ->select('whatsapp_no as phone')
                    ->whereNotNull('whatsapp_no')
                    ->where('whatsapp_no', '!=', '');
        
                $post = $mobileQuery
                    ->union($whatsappQuery)
                    ->distinct()
                    ->count();
        
            } elseif ($contacttypeleads == 2) {
                // ✅ Unique Mobile only
                $post = (clone $leads)
                    ->whereNotNull('mob_no')
                    ->where('mob_no', '!=', '')
                    ->distinct('mob_no')
                    ->count('mob_no');
        
            } elseif ($contacttypeleads == 3) {
                // ✅ Unique WhatsApp only
                $post = (clone $leads)
                    ->whereNotNull('whatsapp_no')
                    ->where('whatsapp_no', '!=', '')
                    ->distinct('whatsapp_no')
                    ->count('whatsapp_no');
        
            } else {
                $post = $leads->count();
            }
        
            // =====================================================
            // RESPONSE
            // =====================================================
            if ($post > 0) {
                $data = [
                    'result' => $post . ' Contacts are ready to be send'
                ];
            } else {
                $data = [
                    'result' => 'No data Record Found'
                ];
            }
        }        
        else{
            $data = [
                "result" => "No data Record Found"
            ];
        }

        return response()->json($data);
    }

    public function getsendList_back(Request $request) {
        $audience = $request->audience;
        // dd($request->all());

        if ($audience == 'allcontact') {

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

                    // $dial_code_columns = [
                    //     "secondary_no_wsp_dial_code",
                    //     "primary_no_wsp_dial_code",
                    //     "mobile_no1_wsp_dial_code",
                    // ];

                    // foreach ($dial_code_columns as $column) {
                    //     $q->orWhereIn($column, (array) $allconmobcountry);
                    // }

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

        }elseif ($audience == 'leads') {

            $jobTitles     = $request->addleadsjobtitle;    // array of job titles
            $jobCountries  = $request->addleadscountry;     // array of countries
            $dateRange     = $request->leadsdaterange;  // "2025-01-01 to 2025-01-31"
            $drivinglicense     = $request->drivinglicense;  // "2025-01-01 to 2025-01-31"
        
            $leads = Lead::query();

            $leads->whereNotNull('whatsapp_no');
        
            // Filter by job titles
            if (!empty($jobTitles)) {
                $leads->whereIn('required_service', $jobTitles);
            }
        
            // Filter by countries (submit_lead_from->country)
            if (!empty($jobCountries)) {
                $leads->whereIn(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(submit_lead_from, '$.country'))"), $jobCountries);
            }

            // Date range filter
            if (!empty($dateRange)) {
                $dates = explode(" to ", $dateRange);

                $start = $dates[0];
                $end   = $dates[1] ?? $dates[0];

                $leads->whereBetween('lead_date', [$start." 00:00:00", $end." 23:59:59"]);
            }

            // Driving license filter
            if (!empty($drivinglicense)) {

                $leads->filterDrivingLicense($drivinglicense);

            }
            // dd($leads->get());
        
            // Get total count
            $post = $leads->count();
            
           
            if ($post > 0) {
                $data = [
                    "result" => $post . " Contacts are ready to be send"
                ];
            } else {
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

    public function messageResendWhatsapp(Request $request){
        $idsv = explode(",",$request->contactIDGRPTR);

        $metaresponses = Metawhatsappcampaignresponse::wherein('id',$idsv)->get();

        $campaign_id = $metaresponses->pluck('metawhatsappcampaign_id')->first();

        $metacampaign = Metawhatsappcampaign::whereId($campaign_id)->first();
        
        $metaAPI = Metawhatsappapi::find($metacampaign->metaapi_id);
        $metaTemplate = Metawhatsapptemplate::find($metacampaign->metatemp_id);

        if(isset($metaAPI)){
            $base_url = $metaAPI->api_base_url;
            $vendor_id = $metaAPI->vendor_uid;
            $access_token = $metaAPI->api_access_token;
        }else{
            $base_url = "https://wa.qamr.in/api";
            $vendor_id = "d97cec67-b154-4f5c-9b93-61649b2e650e";
            $access_token = "7GiC9fzJDJ5Df5HF4bEjKw1aZ6ouxIwlkmpIWHjNVqpijP1BumIMCpq1ua0McaNT";
        }

        $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
        $token = "Authorization: Bearer ".$access_token;

        $api_data = [
            'base_url' => $base_url,
            'vendor_id' => $vendor_id,
            'access_token' => $access_token,
            'endpoint_api' => $endpoint_api,
            'token' => $token
        ];

        // Header File Path
        if ($metaTemplate->meta_url_type == 0) {
            if($metaTemplate->whatsapp_file != ''){
                $header_file_path = url('admin/assets/images/template/'.$metaTemplate->whatsapp_file);
            }else{
                $header_file_path = "";
            }
        } elseif ($metaTemplate->meta_url_type == 1) {
            if ($metaTemplate->static_url != '') {
                $header_file_path = $metaTemplate->static_url;
            } else {
                $header_file_path = "";
            }

        } else {
            if($metaTemplate->whatsapp_file != ''){
                $header_file_path = url('admin/assets/images/template/'.$metaTemplate->whatsapp_file);
            }else{
                $header_file_path = "";
            }
        }

        $metatemplatedata = [
            'header_file_path' => $header_file_path,
            'template_name' => $metaTemplate->template_name
        ];

        foreach ($metaresponses as $metaresponse) {

            ResendMetawhastapppcampaign::dispatch($metaresponse,$metatemplatedata,$api_data,$metaTemplate,$metaAPI,$metacampaign)->onQueue('default');
        }

        return redirect()->back()->with('success','Resend Messages are processed to be send');



    }

    public function store(Request $request){

        $metaAPI = Metawhatsappapi::find($request->metaapi_id);

        // Meta Template
        $metaTemplate = Metawhatsapptemplate::find($request->metatemplate_id);

        if(isset($metaAPI)){
            $base_url = $metaAPI->api_base_url;
            $vendor_id = $metaAPI->vendor_uid;
            $access_token = $metaAPI->api_access_token;
        }else{
            $base_url = "https://wa.qamr.in/api";
            $vendor_id = "d97cec67-b154-4f5c-9b93-61649b2e650e";
            $access_token = "7GiC9fzJDJ5Df5HF4bEjKw1aZ6ouxIwlkmpIWHjNVqpijP1BumIMCpq1ua0McaNT";
        }

        $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
        $token = "Authorization: Bearer ".$access_token;

        $api_data = [
            'base_url' => $base_url,
            'vendor_id' => $vendor_id,
            'access_token' => $access_token,
            'endpoint_api' => $endpoint_api,
            'token' => $token
        ];

        // Header File Path
        if ($metaTemplate->meta_url_type == 0) {
            if($metaTemplate->whatsapp_file != ''){
                $header_file_path = url('admin/assets/images/template/'.$metaTemplate->whatsapp_file);
            }else{
                $header_file_path = "";
            }
        } elseif ($metaTemplate->meta_url_type == 1) {
            if ($metaTemplate->static_url != '') {
                $header_file_path = $metaTemplate->static_url;
            } else {
                $header_file_path = "";
            }

        } else {
            if($metaTemplate->whatsapp_file != ''){
                $header_file_path = url('admin/assets/images/template/'.$metaTemplate->whatsapp_file);
            }else{
                $header_file_path = "";
            }
        }

        $metatemplatedata = [
            'header_file_path' => $header_file_path,
            'template_name' => $metaTemplate->template_name
        ];

        $audience = $request->audience;

        $campaign_store = new Metawhatsappcampaign();
        $campaign_store->campaign_name = $request->campaign_name;
        $campaign_store->audience = $audience;
        $campaign_store->msg_body = $request->whs_msg;
        $campaign_store->field_var = $metaTemplate->meta_field_var;
        $campaign_store->assign_var = $metaTemplate->meta_assign_ar;
        $campaign_store->admin_id = Auth::guard('admin')->user()->id;
        $campaign_store->metatemp_id = $request->metatemplate_id;
        $campaign_store->metaapi_id = $request->metaapi_id;
        $campaign_store->sch_type = $request->sch_type;
        $campaign_store->delay_frequency = $request->delay_frequency;

        if ($request->sch_type == 'Scheduled') {
            $campaign_store->date_and_time = $request->date_and_time;
            $campaign_store->message_status = 'Scheduled';
            $campaign_store->message_text = 'Scheduled';
        }

        if ($audience == 'allcontact') {
            if ($request->careoff_id2 != '') {
                $campaign_store->careoff_id = implode(",",$request->careoff_id2);
            }
            if ($request->country_id != '') {
                $campaign_store->country_id = implode(",", $request->country_id);
            }

            if ($request->country_code != '') {
                $campaign_store->country_code = implode(",",$request->country_code);
            }

            $campaign_store->contact_type = $request->allcontact_contact_type;

            if ($request->groupmallc != '') {
                $campaign_store->group_id = implode(",",$request->groupmallc);
            }

            if ($request->allcontact_subscribe != '') {
                $campaign_store->subscribe = implode(",",$request->allcontact_subscribe);
            }

            if ($request->lead_type !='') {
                $campaign_store->business_type_contact = implode(',',$request->lead_type);
            }

        }

        if ($audience == 'contactp') {
            if ($request->careoff_id != '') {
                $campaign_store->careoff_id = implode(",",$request->careoff_id);
            }
            $campaign_store->contact_type = $request->contactp_contact_type;

            if ($request->contact_group != '') {
                $campaign_store->group_id = implode(",",$request->contact_group);
            }

            if ($request->contactp_subscribe != '') {
                $campaign_store->subscribe = implode(",",$request->contactp_subscribe);
            }

            if ($request->business_type_contact != '') {
                $campaign_store->business_type_contact = implode(",",$request->business_type_contact);
            }

        }

        if ($audience == 'partner') {
            $campaign_store->contact_type = $request->partner_contact_type;
        }

        if ($audience == 'associate') {
            $campaign_store->contact_type = $request->assoc_contact_type;
        }

        if ($audience == 'leads') {
            if(isset($request->leads_job_title) && !empty($request->leads_job_title)){
                $campaign_store->leads_job_title = implode(",",$request->leads_job_title);
            }

            if(isset($request->leads_country) && !empty($request->leads_country)){
                $campaign_store->leads_country = implode(",",$request->leads_country);
            }

            if(isset($request->leads_date_range) && !empty($request->leads_date_range)){
                $campaign_store->leads_date = $request->leads_date_range;
            }

            if(isset($request->lead_driving_license) && !empty($request->lead_driving_license)){
                $campaign_store->lead_driving_license = implode(",",$request->lead_driving_license);
            }

            if(isset($request->leads_contact_type) && !empty($request->leads_contact_type)){
                $campaign_store->leads_contact_type = $request->leads_contact_type;
            }

            if(isset($request->lead_careoff_id) && !empty($request->lead_careoff_id)){
                $campaign_store->lead_careoff_id = implode(",",$request->lead_careoff_id);
            }
            
        }
        

        $campaign_store->save();

        if ($request->sch_type == 'Now') {

            if ($audience == 'partner') {
                return redirect()->back()->with('success','Work in progress!');
            }elseif ($audience == 'client') {
                return redirect()->back()->with('success','Work in progress!');
            }elseif ($audience == 'associate') {
                return redirect()->back()->with('success','Work in progress!');
            }elseif ($audience == 'contactp') {
                $message_response = [];
                $total_send = 0;
                $groupID = $request->contact_group;
                $business_type = $request->business_type_contact;
                $careoff_id = $request->careoff_id;
                $subscribe = $request->contactp_subscribe;
                $country_code = $request->country_code2;
                // $contact_status = $request->contactp_status;

                $contactps = Contactplus::where(
                    function($query) use($subscribe,$groupID,$business_type,$careoff_id,$country_code){
                        if($groupID != ''){
                            $query->wherein('group_id',$groupID);
                        }

                        if($business_type != ''){
                            $query->wherein('businesstype_id',$business_type);
                        }

                        if ($careoff_id != '') {
                            $query->wherein('careoff_id',$careoff_id);
                        }

                        if($subscribe != ''){
                            $query->whereIn('subscribe',$subscribe);
                        }
                    }
                )->get();

                if ($contactps->count() > 0) {

                    $delaySeconds = 0;

                    foreach ($contactps as $contactp) {
                    
                        try {
                    
                            $numbers = [];
                    
                            if ($request->contactp_contact_type == 1) {
                                if (!empty($contactp->owner_contact)) $numbers[] = $contactp->owner_contact;
                                if (!empty($contactp->prim_contact)) $numbers[] = $contactp->prim_contact;
                                if (!empty($contactp->sec_contact)) $numbers[] = $contactp->sec_contact;
                    
                            } elseif ($request->contactp_contact_type == 2) {
                                if (!empty($contactp->owner_contact)) $numbers[] = $contactp->owner_contact;
                    
                            } elseif ($request->contactp_contact_type == 3) {
                                if (!empty($contactp->prim_contact)) $numbers[] = $contactp->prim_contact;
                    
                            } elseif ($request->contactp_contact_type == 4) {
                                if (!empty($contactp->sec_contact)) $numbers[] = $contactp->sec_contact;
                            }
                    
                            $numbers = array_values(array_unique(array_filter($numbers)));
                    
                            if (empty($numbers)) {
                                continue;
                            }
                    
                            foreach ($numbers as $phone) {
                    
                                if ($campaign_store->delay_frequency === 'Delay 10 to 60 Seconds') {
                                    $rand = rand(10, 60);
                                } elseif ($campaign_store->delay_frequency === 'Delay 30 sec to 2 min') {
                                    $rand = rand(30, 120);
                                } else {
                                    $rand = 0;
                                }
                    
                                $delaySeconds += ($rand + 5);
                    
                                $random_string = Str::random(32);
                                $unsubscribe_url = 'whatsapp/unsubscribe/request/'.$random_string.'/'.$contactp->id;
                    
                                $staffDet = Whatsappchaturl::where('staff_id','=',$contactp->careoff_id)
                                            ->where('status',true)
                                            ->first();
                    
                                $dynamichaturl = $staffDet ? $staffDet->chat_url : "https://wa.me";
                    
                                $countryName = optional(Country::find($contactp->country_id))->name ?? '';
                                $cityName    = optional(City::find($contactp->city_id))->name ?? '';
                    
                                $requestData = [
                                    'contactp_contact_type' => $request->contactp_contact_type,
                                    'random_string' => $random_string,
                                    'unsubscribe_url' => $unsubscribe_url,
                                    'dynamichaturl' => $dynamichaturl,
                                    'countryName' => $countryName,
                                    'cityName' => $cityName,
                                    'phone' => $phone
                                ];
                    
                                \Log::channel('contactplus')->info('Dispatching Job', [
                                    'contact_id'  => $contactp->id,
                                    'phone'       => $phone,
                                    'added_delay' => $rand,
                                    'total_delay' => $delaySeconds,
                                    'run_at'      => now()->addSeconds($delaySeconds)->toDateTimeString()
                                ]);
                    
                                ContactplusSendMetaJob::dispatch(
                                    $contactp,
                                    $phone,
                                    $api_data,
                                    $metatemplatedata,
                                    $requestData,
                                    $metaTemplate,
                                    $campaign_store
                                )
                                ->delay(now()->addSeconds($delaySeconds))
                                ->onQueue('default');
                            }

                    
                        } catch (\Throwable $e) {
                    
                            \Log::channel('contactplus')->error('Contact failed', [
                                'contact_id' => $contactp->id,
                                'error'      => $e->getMessage()
                            ]);
                    
                            continue;
                        }
                    }

                    // Update Status and Response into our campaign message
                    $campaign_store->update([
                        'message_status' => 'success',
                        'message_text'   => 'Message processed for WhatsApp contact'
                    ]);

                    $total_send = count($contactps)." Messages are send";

                
                } else {
                
                    $campaign_store->update([
                        'message_status' => 'Failed',
                        'message_text'   => 'Message were not send!'
                    ]);
                
                    $total_send = count($message_response)." Messages are send";
                }

                
              return redirect()->back()->with('success',$total_send);

            }
            elseif ($audience == 'allcontact') {

                $message_response = [];
                $groupID = $request->groupmallc;
                $careoff_id = $request->careoff_id2;
                $country_id = $request->country_id;
                $unsubscribe = $request->allcontact_subscribe;
                $country_code = $request->country_code;
                $lead_type = $request->lead_type;
            
                $allcontacts = Allcontact::where(function($query) use ($groupID,$careoff_id,$country_id,$unsubscribe,$country_code,$lead_type){
            
                    if($groupID != ''){
                        $query->whereIn('group_id',$groupID);
                    }
            
                    if ($careoff_id != '') {
                        $query->whereIn('careoff_id',$careoff_id);
                    }
            
                    if ($country_id != '') {
                        $query->whereIn('country_id',$country_id);
                    }
            
                    if ($unsubscribe != '') {
                        $query->whereIn('optinout',$unsubscribe);
                    }
            
                    if ($country_code != '') {
                        $query->where(function($q) use($country_code){
                            foreach ($country_code as $code) {
                                $q->orWhere('primary_no_wsp', 'like', $code . '%')
                                  ->orWhere('secondary_no_wsp', 'like', $code . '%')
                                  ->orWhere('mobile_no1_wsp', 'like', $code . '%');
                            }
                        });
                    }
            
                    if ($lead_type != '') {
                        $query->whereIn('lead_type',$lead_type);
                    }
            
                })->get();

               
                if ($allcontacts->count() > 0) {
            
                    $delaySeconds = 0;
                    $totalJobs = 0;
            
                    foreach ($allcontacts as $allcontact) {
            
                        try {
            
                            // 🔥 CONTACT TYPE LOGIC
                            $numbers = [];
            
                            if ($request->allcontact_contact_type == 1) {
                                if (!empty($allcontact->mobile_no1_wsp)) $numbers[] = $allcontact->mobile_no1_wsp;
                                if (!empty($allcontact->primary_no_wsp)) $numbers[] = $allcontact->primary_no_wsp;
                                if (!empty($allcontact->secondary_no_wsp)) $numbers[] = $allcontact->secondary_no_wsp;
            
                            } elseif ($request->allcontact_contact_type == 2) {
                                if (!empty($allcontact->mobile_no1_wsp)) $numbers[] = $allcontact->mobile_no1_wsp;
            
                            } elseif ($request->allcontact_contact_type == 3) {
                                if (!empty($allcontact->primary_no_wsp)) $numbers[] = $allcontact->primary_no_wsp;
            
                            } elseif ($request->allcontact_contact_type == 4) {
                                if (!empty($allcontact->secondary_no_wsp)) $numbers[] = $allcontact->secondary_no_wsp;
                            }
                            
                            $numbers = array_values(array_unique(array_filter($numbers)));
            
                            if (empty($numbers)) {
                                continue;
                            }
            
                            foreach ($numbers as $phone) {
            
                                // 🔥 Delay logic
                                if ($campaign_store->delay_frequency === 'Delay 10 to 60 Seconds') {
                                    $rand = rand(10, 60);
                                } elseif ($campaign_store->delay_frequency === 'Delay 30 sec to 2 min') {
                                    $rand = rand(30, 120);
                                } else {
                                    $rand = 0;
                                }
            
                                $delaySeconds += ($rand + 10);
            
                                \Log::channel('AllcontactSendMetaJob')->info('Instant Dispatch', [
                                    'contact_id'  => $allcontact->id,
                                    'phone'       => $phone,
                                    'added_delay' => ($rand + 10),
                                    'total_delay' => $delaySeconds,
                                    'run_at'      => now()->addSeconds($delaySeconds)->toDateTimeString()
                                ]);
            
                                AllcontactSendMetaJob::dispatch(
                                    $allcontact,
                                    $phone, // ✅ IMPORTANT
                                    $api_data,
                                    $metatemplatedata,
                                    $metaTemplate,
                                    $campaign_store
                                )
                                ->delay(now()->addSeconds($delaySeconds))
                                ->onQueue('default');
            
                                $totalJobs++;
                            }
            
                        } catch (\Throwable $e) {
            
                            \Log::channel('AllcontactSendMetaJob')->error('Allcontact instant failed', [
                                'contact_id' => $allcontact->id,
                                'error'      => $e->getMessage()
                            ]);
            
                            continue;
                        }
                    }
            
                    // ✅ Campaign update
                    $campaign_store->update([
                        'message_status' => 'success',
                        'message_text'   => 'Message processed for WhatsApp contact'
                    ]);
            
                    $total_send = $totalJobs . " Messages scheduled";
            
                } else {
            
                    $campaign_store->update([
                        'message_status' => 'Failed',
                        'message_text'   => 'Message were not send!'
                    ]);
            
                    $total_send = count($message_response)." Message are send";
                }
            
                return redirect()->back()->with('success',$total_send);
            }
            elseif ($audience == 'leads') {

                $jobTitles        = $request->leads_job_title ?? [];
                $jobCountries     = $request->leads_country ?? [];
                $dateRange        = $request->leads_date_range ?? null;
                $drivinglicense   = $request->lead_driving_license ?? null;
                $careoffleads     = $request->lead_careoff_id ?? [];
            
                /*
                |--------------------------------------------------------------------------
                | BASE QUERY
                |--------------------------------------------------------------------------
                */
                $leads = Lead::query();
            
                // Job Titles
                if (!empty($jobTitles)) {
                    $leads->whereIn('required_service', $jobTitles);
                }
            
                // Careoff
                if (!empty($careoffleads)) {
                    $leads->whereIn('leadassign_id', $careoffleads);
                }
            
                // Countries (JSON)
                if (!empty($jobCountries)) {
                    $leads->whereIn(
                        DB::raw("JSON_UNQUOTE(JSON_EXTRACT(submit_lead_from, '$.country'))"),
                        $jobCountries
                    );
                }
            
                // Date Range
                if (!empty($dateRange)) {
                    $dates = explode(' to ', $dateRange);
                    $start = $dates[0];
                    $end   = $dates[1] ?? $dates[0];
            
                    $leads->whereBetween('lead_date', [
                        $start . ' 00:00:00',
                        $end   . ' 23:59:59'
                    ]);
                }
            
                // Driving License
                if (!empty($drivinglicense)) {
                    $leads->filterDrivingLicense($drivinglicense);
                }
            
                /*
                |--------------------------------------------------------------------------
                | GET FINAL LEADS (🔥 IMPORTANT FIX)
                |--------------------------------------------------------------------------
                | Only leads that have at least one valid contact number
                */
                $finalLeads = (clone $leads)
                    ->where(function ($q) {
                        $q->where(function ($qq) {
                            $qq->whereNotNull('mob_no')
                               ->where('mob_no', '!=', '');
                        })
                        ->orWhere(function ($qq) {
                            $qq->whereNotNull('whatsapp_no')
                               ->where('whatsapp_no', '!=', '');
                        });
                    })
                    ->get()
                    ->unique('id'); // 🔥 prevent duplicate lead
            
                /*
                |--------------------------------------------------------------------------
                | DISPATCH JOBS (ONE PER LEAD)
                |--------------------------------------------------------------------------
                */
                if ($finalLeads->count() > 0) {

                    $delaySeconds = 0;
                    $totalJobs = 0;
                
                    foreach ($finalLeads as $lead) {
                
                        // 🔥 Collect numbers based on contact type
                        $numbers = [];
                
                        if ($campaign_store->leads_contact_type == 1) {
                            if (!empty($lead->whatsapp_no)) $numbers[] = $lead->whatsapp_no;
                            if (!empty($lead->mob_no)) $numbers[] = $lead->mob_no;
                        } elseif ($campaign_store->leads_contact_type == 2) {
                            if (!empty($lead->mob_no)) $numbers[] = $lead->mob_no;
                        } elseif ($campaign_store->leads_contact_type == 3) {
                            if (!empty($lead->whatsapp_no)) $numbers[] = $lead->whatsapp_no;
                        }
                
                        $numbers = array_values(array_unique(array_filter($numbers)));
                
                        // ❌ Skip if no numbers
                        if (empty($numbers)) {
                            continue;
                        }
                
                        foreach ($numbers as $phone) {
                
                            // 🔥 Delay logic (cumulative across all leads)
                            if ($campaign_store->delay_frequency === 'Delay 10 to 60 Seconds') {
                                $rand = rand(10, 60);
                            } elseif ($campaign_store->delay_frequency === 'Delay 30 sec to 2 min') {
                                $rand = rand(30, 120);
                            } else {
                                $rand = 0;
                            }
                
                            $delaySeconds += $rand;
                
                            SendMetaLeadJob::dispatch(
                                $lead,
                                $phone,
                                $api_data,
                                $metatemplatedata,
                                $metaTemplate,
                                $campaign_store
                            )
                            ->delay(now()->addSeconds($delaySeconds))
                            ->onQueue('default');
                
                            \Log::channel('SendMetaLeadJob')->info('Delay Debug', [
                                'lead_id'     => $lead->id,
                                'phone'       => $phone,
                                'added_delay' => $rand,
                                'total_delay' => $delaySeconds,
                                'run_at'      => now()->addSeconds($delaySeconds)->toDateTimeString()
                            ]);
                
                            $totalJobs++;
                        }
                    }
                
                    $campaign_store->update([
                        'message_status' => 'success',
                        'message_text'   => 'Messages scheduled successfully'
                    ]);
                
                    return redirect()->back()->with(
                        'success',
                        $totalJobs . ' messages scheduled'
                    );
                
                } else {
                
                    $campaign_store->update([
                        'message_status' => 'Failed',
                        'message_text'   => 'No Leads Found!'
                    ]);
                
                    return redirect()->back()->with('error', 'No Leads Found!');
                }
            }                                     
            else{
                return redirect()->back()->with('error','please select right');
            }

        } else {

            Log::info("📌 Campaign Scheduled", [
                'campaign_id' => $campaign_store->id,
                'run_at' => $request->date_and_time,
                'audience' => $audience
            ]);
            
            return redirect()->back()->with('success','Campaign message scheduled successfully!');            
        }

    }

    private function getRotateTemplate(
        $templateIds,
        $metaTemplates,
        &$templateIndex
    ){

        $templateId =
        $templateIds[
            $templateIndex %
            count($templateIds)
        ];

        $template =
        $metaTemplates[$templateId];

        if($template->meta_url_type == 0){

            $header =
            !empty($template->whatsapp_file)
            ? url(
            'admin/assets/images/template/'.
            $template->whatsapp_file
            )
            : '';

        }
        elseif($template->meta_url_type == 1){

            $header =
            $template->static_url ?? '';

        }
        else{

            $header =
            !empty($template->whatsapp_file)
            ? url(
            'admin/assets/images/template/'.
            $template->whatsapp_file
            )
            : '';

        }

        $templateIndex++;

        return [

            'template'=>$template,

            'data'=>[

                'header_file_path'=>$header,

                'template_name'=>
                $template->template_name

            ]

        ];

    }
    
    public function store_leatest(Request $request){
        $metaAPI = Metawhatsappapi::find($request->metaapi_id);

        // Meta Template

        $templateIds = $request->metatemplate_id ?? [];

        if(empty($templateIds)){

            return back()->with(
                'error',
                'Please select template'
            );

        }

        $metaTemplates = Metawhatsapptemplate::whereIn('id',$templateIds)->get()->keyBy('id');
        // dd($metaTemplates);

        $templateIndex = 0;

        /*
        Campaign metadata ke liye
        first template
        */

        $metaTemplate = $metaTemplates->first();

        if(isset($metaAPI)){
            $base_url = $metaAPI->api_base_url;
            $vendor_id = $metaAPI->vendor_uid;
            $access_token = $metaAPI->api_access_token;
        }
        else{
            $base_url = "https://wa.qamr.in/api";
            $vendor_id="";
            $access_token="";
        }

        $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
        $token = "Authorization: Bearer ".$access_token;

        $api_data = [
            'base_url'=>$base_url,
            'vendor_id'=>$vendor_id,
            'access_token'=>$access_token,
            'endpoint_api'=>$endpoint_api,
            'token'=>$token
        ];

        $audience = $request->audience;

        $campaign_store = new Metawhatsappcampaign();
        $campaign_store->campaign_name = $request->campaign_name;
        $campaign_store->audience = $audience;
        $campaign_store->msg_body = $request->whs_msg;
        $campaign_store->field_var = $metaTemplate->meta_field_var;
        $campaign_store->assign_var = $metaTemplate->meta_assign_ar;
        $campaign_store->admin_id = Auth::guard('admin')->user()->id;

        /*
        Store selected templates
        Example:
        1,2,5,8
        */
        
        $campaign_store->metatemp_id = implode(',',$templateIds);
        $campaign_store->metaapi_id = $request->metaapi_id;
        $campaign_store->sch_type = $request->sch_type;
        $campaign_store->delay_frequency = $request->delay_frequency;

        if($request->sch_type == 'Scheduled'){
            $campaign_store->date_and_time = $request->date_and_time;
            $campaign_store->message_status = 'Scheduled';
            $campaign_store->message_text = 'Scheduled';
        }

        if ($audience == 'allcontact') {

            if ($request->careoff_id2 != '') {

                $campaign_store->careoff_id =
                implode(
                    ",",
                    $request->careoff_id2
                );

            }

            if ($request->country_id != '') {

                $campaign_store->country_id =
                implode(
                    ",",
                    $request->country_id
                );

            }

            if ($request->country_code != '') {

                $campaign_store->country_code =
                implode(
                    ",",
                    $request->country_code
                );

            }

            $campaign_store->contact_type = $request->allcontact_contact_type;

            if ($request->groupmallc != '') {

                $campaign_store->group_id =
                implode(
                    ",",
                    $request->groupmallc
                );

            }

            if ($request->allcontact_subscribe != '') {

                $campaign_store->subscribe =
                implode(
                    ",",
                    $request->allcontact_subscribe
                );

            }

            if ($request->lead_type != '') {

                $campaign_store->business_type_contact =
                implode(
                    ',',
                    $request->lead_type
                );

            }

        }

        if ($audience == 'contactp') {

            if ($request->careoff_id != '') {

                $campaign_store->careoff_id =
                implode(
                    ",",
                    $request->careoff_id
                );

            }

            $campaign_store->contact_type = $request->contactp_contact_type;

            if ($request->contact_group != '') {

                $campaign_store->group_id =
                implode(
                    ",",
                    $request->contact_group
                );

            }

            if ($request->contactp_subscribe != '') {

                $campaign_store->subscribe =
                implode(
                    ",",
                    $request->contactp_subscribe
                );

            }

            if ($request->business_type_contact != '') {

                $campaign_store->business_type_contact =
                implode(
                    ",",
                    $request->business_type_contact
                );

            }

        }

        if ($audience == 'partner') {

            $campaign_store->contact_type = $request->partner_contact_type;

        }

        if ($audience == 'associate') {

            $campaign_store->contact_type = $request->assoc_contact_type;

        }

        if ($audience == 'leads') {

            if(isset($request->leads_job_title)
            && !empty($request->leads_job_title)){

                $campaign_store->leads_job_title =
                implode(
                    ",",
                    $request->leads_job_title
                );

            }

            if(isset($request->leads_country)
            && !empty($request->leads_country)){

                $campaign_store->leads_country =
                implode(
                    ",",
                    $request->leads_country
                );

            }

            if(isset($request->leads_date_range)
            && !empty($request->leads_date_range)){

                $campaign_store->leads_date =
                $request->leads_date_range;

            }

            if(isset($request->lead_driving_license)
            && !empty($request->lead_driving_license)){

                $campaign_store->lead_driving_license =
                implode(
                    ",",
                    $request->lead_driving_license
                );

            }

            if(isset($request->leads_contact_type)
            && !empty($request->leads_contact_type)){

                $campaign_store->leads_contact_type =
                $request->leads_contact_type;

            }

            if(isset($request->lead_careoff_id)
            && !empty($request->lead_careoff_id)){

                $campaign_store->lead_careoff_id =
                implode(
                    ",",
                    $request->lead_careoff_id
                );

            }

        }
        
        $campaign_store->save();

        if ($request->sch_type == 'Now') {

            if ($audience == 'partner') {
                return redirect()->back()->with('success','Work in progress!');
            }elseif ($audience == 'client') {
                return redirect()->back()->with('success','Work in progress!');
            }elseif ($audience == 'associate') {
                return redirect()->back()->with('success','Work in progress!');
            }elseif ($audience == 'contactp') {
                $message_response = [];
                $total_send = 0;
                $groupID = $request->contact_group;
                $business_type = $request->business_type_contact;
                $careoff_id = $request->careoff_id;
                $subscribe = $request->contactp_subscribe;
                $country_code = $request->country_code2;
                // $contact_status = $request->contactp_status;

                $contactps = Contactplus::where(
                    function($query) use($subscribe,$groupID,$business_type,$careoff_id,$country_code){
                        if($groupID != ''){
                            $query->wherein('group_id',$groupID);
                        }

                        if($business_type != ''){
                            $query->wherein('businesstype_id',$business_type);
                        }

                        if ($careoff_id != '') {
                            $query->wherein('careoff_id',$careoff_id);
                        }

                        if($subscribe != ''){
                            $query->whereIn('subscribe',$subscribe);
                        }
                    }
                )->get();

                if ($contactps->count() > 0) {

                    $delaySeconds = 0;

                    foreach ($contactps as $contactp) {
                    
                        try {
                    
                            $numbers = [];
                    
                            if ($request->contactp_contact_type == 1) {
                                if (!empty($contactp->owner_contact)) $numbers[] = $contactp->owner_contact;
                                if (!empty($contactp->prim_contact)) $numbers[] = $contactp->prim_contact;
                                if (!empty($contactp->sec_contact)) $numbers[] = $contactp->sec_contact;
                    
                            } elseif ($request->contactp_contact_type == 2) {
                                if (!empty($contactp->owner_contact)) $numbers[] = $contactp->owner_contact;
                    
                            } elseif ($request->contactp_contact_type == 3) {
                                if (!empty($contactp->prim_contact)) $numbers[] = $contactp->prim_contact;
                    
                            } elseif ($request->contactp_contact_type == 4) {
                                if (!empty($contactp->sec_contact)) $numbers[] = $contactp->sec_contact;
                            }
                    
                            $numbers = array_values(array_unique(array_filter($numbers)));
                    
                            if (empty($numbers)) {
                                continue;
                            }
                    
                            foreach ($numbers as $phone) {
                    
                                if ($campaign_store->delay_frequency === 'Delay 10 to 60 Seconds') {
                                    $rand = rand(10, 60);
                                } elseif ($campaign_store->delay_frequency === 'Delay 30 sec to 2 min') {
                                    $rand = rand(30, 120);
                                } else {
                                    $rand = 0;
                                }
                    
                                $delaySeconds += ($rand + 5);
                    
                                $random_string = Str::random(32);
                                $unsubscribe_url = 'whatsapp/unsubscribe/request/'.$random_string.'/'.$contactp->id;
                    
                                $staffDet = Whatsappchaturl::where('staff_id','=',$contactp->careoff_id)
                                            ->where('status',true)
                                            ->first();
                    
                                $dynamichaturl = $staffDet ? $staffDet->chat_url : "https://wa.me";
                    
                                $countryName = optional(Country::find($contactp->country_id))->name ?? '';
                                $cityName    = optional(City::find($contactp->city_id))->name ?? '';
                    
                                $requestData = [
                                    'contactp_contact_type' => $request->contactp_contact_type,
                                    'random_string' => $random_string,
                                    'unsubscribe_url' => $unsubscribe_url,
                                    'dynamichaturl' => $dynamichaturl,
                                    'countryName' => $countryName,
                                    'cityName' => $cityName,
                                    'phone' => $phone
                                ];
                    
                                \Log::channel('contactplus')->info('Dispatching Job', [
                                    'contact_id'  => $contactp->id,
                                    'phone'       => $phone,
                                    'added_delay' => $rand,
                                    'total_delay' => $delaySeconds,
                                    'run_at'      => now()->addSeconds($delaySeconds)->toDateTimeString()
                                ]);

                                $templateData =
                                    $this->getRotateTemplate(
                                    $templateIds,
                                    $metaTemplates,
                                    $templateIndex
                                    );
                    
                                ContactplusSendMetaJob::dispatch(

                                    $contactp,

                                    $phone,

                                    $api_data,

                                    $templateData['data'],

                                    $requestData,

                                    $templateData['template'],

                                    $campaign_store

                                )->delay(now()->addSeconds($delaySeconds))
                                ->onQueue('default');
                            }

                    
                        } catch (\Throwable $e) {
                    
                            \Log::channel('contactplus')->error('Contact failed', [
                                'contact_id' => $contactp->id,
                                'error'      => $e->getMessage()
                            ]);
                    
                            continue;
                        }
                    }

                    // Update Status and Response into our campaign message
                    $campaign_store->update([
                        'message_status' => 'success',
                        'message_text'   => 'Message processed for WhatsApp contact'
                    ]);

                    $total_send = count($contactps)." Messages are send";

                
                } else {
                
                    $campaign_store->update([
                        'message_status' => 'Failed',
                        'message_text'   => 'Message were not send!'
                    ]);
                
                    $total_send = count($message_response)." Messages are send";
                }

                
              return redirect()->back()->with('success',$total_send);

            }
            elseif ($audience == 'allcontact') {

                $message_response = [];
                $groupID = $request->groupmallc;
                $careoff_id = $request->careoff_id2;
                $country_id = $request->country_id;
                $unsubscribe = $request->allcontact_subscribe;
                $country_code = $request->country_code;
                $lead_type = $request->lead_type;
            
                $allcontacts = Allcontact::where(function($query) use ($groupID,$careoff_id,$country_id,$unsubscribe,$country_code,$lead_type){
            
                    if($groupID != ''){
                        $query->whereIn('group_id',$groupID);
                    }
            
                    if ($careoff_id != '') {
                        $query->whereIn('careoff_id',$careoff_id);
                    }
            
                    if ($country_id != '') {
                        $query->whereIn('country_id',$country_id);
                    }
            
                    if ($unsubscribe != '') {
                        $query->whereIn('optinout',$unsubscribe);
                    }
            
                    if ($country_code != '') {
                        $query->where(function($q) use($country_code){
                            foreach ($country_code as $code) {
                                $q->orWhere('primary_no_wsp', 'like', $code . '%')
                                  ->orWhere('secondary_no_wsp', 'like', $code . '%')
                                  ->orWhere('mobile_no1_wsp', 'like', $code . '%');
                            }
                        });
                    }
            
                    if ($lead_type != '') {
                        $query->whereIn('lead_type',$lead_type);
                    }
            
                })->get();

               
                if ($allcontacts->count() > 0) {
            
                    $delaySeconds = 0;
                    $totalJobs = 0;
            
                    foreach ($allcontacts as $allcontact) {
            
                        try {
            
                            // 🔥 CONTACT TYPE LOGIC
                            $numbers = [];
            
                            if ($request->allcontact_contact_type == 1) {
                                if (!empty($allcontact->mobile_no1_wsp)) $numbers[] = $allcontact->mobile_no1_wsp;
                                if (!empty($allcontact->primary_no_wsp)) $numbers[] = $allcontact->primary_no_wsp;
                                if (!empty($allcontact->secondary_no_wsp)) $numbers[] = $allcontact->secondary_no_wsp;
            
                            } elseif ($request->allcontact_contact_type == 2) {
                                if (!empty($allcontact->mobile_no1_wsp)) $numbers[] = $allcontact->mobile_no1_wsp;
            
                            } elseif ($request->allcontact_contact_type == 3) {
                                if (!empty($allcontact->primary_no_wsp)) $numbers[] = $allcontact->primary_no_wsp;
            
                            } elseif ($request->allcontact_contact_type == 4) {
                                if (!empty($allcontact->secondary_no_wsp)) $numbers[] = $allcontact->secondary_no_wsp;
                            }
                            
                            $numbers = array_values(array_unique(array_filter($numbers)));
            
                            if (empty($numbers)) {
                                continue;
                            }
            
                            foreach ($numbers as $phone) {
            
                                // 🔥 Delay logic
                                if ($campaign_store->delay_frequency === 'Delay 10 to 60 Seconds') {
                                    $rand = rand(10, 60);
                                } elseif ($campaign_store->delay_frequency === 'Delay 30 sec to 2 min') {
                                    $rand = rand(30, 120);
                                } else {
                                    $rand = 0;
                                }
            
                                $delaySeconds += ($rand + 10);
            
                                \Log::channel('AllcontactSendMetaJob')->info('Instant Dispatch', [
                                    'contact_id'  => $allcontact->id,
                                    'phone'       => $phone,
                                    'added_delay' => ($rand + 10),
                                    'total_delay' => $delaySeconds,
                                    'run_at'      => now()->addSeconds($delaySeconds)->toDateTimeString()
                                ]);
            
                                $templateData =
                                $this->getRotateTemplate(
                                $templateIds,
                                $metaTemplates,
                                $templateIndex
                                );

                                AllcontactSendMetaJob::dispatch(

                                    $allcontact,

                                    $phone,

                                    $api_data,

                                    $templateData['data'],

                                    $templateData['template'],

                                    $campaign_store

                                )
                                ->delay(now()->addSeconds($delaySeconds))
                                ->onQueue('default');
            
                                $totalJobs++;
                            }
            
                        } catch (\Throwable $e) {
            
                            \Log::channel('AllcontactSendMetaJob')->error('Allcontact instant failed', [
                                'contact_id' => $allcontact->id,
                                'error'      => $e->getMessage()
                            ]);
            
                            continue;
                        }
                    }
            
                    // ✅ Campaign update
                    $campaign_store->update([
                        'message_status' => 'success',
                        'message_text'   => 'Message processed for WhatsApp contact'
                    ]);
            
                    $total_send = $totalJobs . " Messages scheduled";
            
                } else {
            
                    $campaign_store->update([
                        'message_status' => 'Failed',
                        'message_text'   => 'Message were not send!'
                    ]);
            
                    $total_send = count($message_response)." Message are send";
                }
            
                return redirect()->back()->with('success',$total_send);
            }
            elseif ($audience == 'leads') {

                $jobTitles        = $request->leads_job_title ?? [];
                $jobCountries     = $request->leads_country ?? [];
                $dateRange        = $request->leads_date_range ?? null;
                $drivinglicense   = $request->lead_driving_license ?? null;
                $careoffleads     = $request->lead_careoff_id ?? [];
            
                /*
                |--------------------------------------------------------------------------
                | BASE QUERY
                |--------------------------------------------------------------------------
                */
                $leads = Lead::query();
            
                // Job Titles
                if (!empty($jobTitles)) {
                    $leads->whereIn('required_service', $jobTitles);
                }
            
                // Careoff
                if (!empty($careoffleads)) {
                    $leads->whereIn('leadassign_id', $careoffleads);
                }
            
                // Countries (JSON)
                if (!empty($jobCountries)) {
                    $leads->whereIn(
                        DB::raw("JSON_UNQUOTE(JSON_EXTRACT(submit_lead_from, '$.country'))"),
                        $jobCountries
                    );
                }
            
                // Date Range
                if (!empty($dateRange)) {
                    $dates = explode(' to ', $dateRange);
                    $start = $dates[0];
                    $end   = $dates[1] ?? $dates[0];
            
                    $leads->whereBetween('lead_date', [
                        $start . ' 00:00:00',
                        $end   . ' 23:59:59'
                    ]);
                }
            
                // Driving License
                if (!empty($drivinglicense)) {
                    $leads->filterDrivingLicense($drivinglicense);
                }
            
                /*
                |--------------------------------------------------------------------------
                | GET FINAL LEADS (🔥 IMPORTANT FIX)
                |--------------------------------------------------------------------------
                | Only leads that have at least one valid contact number
                */
                $finalLeads = (clone $leads)
                    ->where(function ($q) {
                        $q->where(function ($qq) {
                            $qq->whereNotNull('mob_no')
                               ->where('mob_no', '!=', '');
                        })
                        ->orWhere(function ($qq) {
                            $qq->whereNotNull('whatsapp_no')
                               ->where('whatsapp_no', '!=', '');
                        });
                    })
                    ->get()
                    ->unique('id'); // 🔥 prevent duplicate lead
            
                /*
                |--------------------------------------------------------------------------
                | DISPATCH JOBS (ONE PER LEAD)
                |--------------------------------------------------------------------------
                */
                if ($finalLeads->count() > 0) {

                    $delaySeconds = 0;
                    $totalJobs = 0;
                
                    foreach ($finalLeads as $lead) {
                
                        // 🔥 Collect numbers based on contact type
                        $numbers = [];
                
                        if ($campaign_store->leads_contact_type == 1) {
                            if (!empty($lead->whatsapp_no)) $numbers[] = $lead->whatsapp_no;
                            if (!empty($lead->mob_no)) $numbers[] = $lead->mob_no;
                        } elseif ($campaign_store->leads_contact_type == 2) {
                            if (!empty($lead->mob_no)) $numbers[] = $lead->mob_no;
                        } elseif ($campaign_store->leads_contact_type == 3) {
                            if (!empty($lead->whatsapp_no)) $numbers[] = $lead->whatsapp_no;
                        }
                
                        $numbers = array_values(array_unique(array_filter($numbers)));
                
                        // ❌ Skip if no numbers
                        if (empty($numbers)) {
                            continue;
                        }
                
                        foreach ($numbers as $phone) {
                
                            // 🔥 Delay logic (cumulative across all leads)
                            if ($campaign_store->delay_frequency === 'Delay 10 to 60 Seconds') {
                                $rand = rand(10, 60);
                            } elseif ($campaign_store->delay_frequency === 'Delay 30 sec to 2 min') {
                                $rand = rand(30, 120);
                            } else {
                                $rand = 0;
                            }
                
                            $delaySeconds += $rand;
                
                            $templateData =
                            $this->getRotateTemplate(
                            $templateIds,
                            $metaTemplates,
                            $templateIndex
                            );

                            SendMetaLeadJob::dispatch(

                                $lead,

                                $phone,

                                $api_data,

                                $templateData['data'],

                                $templateData['template'],

                                $campaign_store

                            )
                            ->delay(now()->addSeconds($delaySeconds))
                            ->onQueue('default');
                
                            \Log::channel('SendMetaLeadJob')->info('Delay Debug', [
                                'lead_id'     => $lead->id,
                                'phone'       => $phone,
                                'added_delay' => $rand,
                                'total_delay' => $delaySeconds,
                                'run_at'      => now()->addSeconds($delaySeconds)->toDateTimeString()
                            ]);
                
                            $totalJobs++;
                        }
                    }
                
                    $campaign_store->update([
                        'message_status' => 'success',
                        'message_text'   => 'Messages scheduled successfully'
                    ]);
                
                    return redirect()->back()->with(
                        'success',
                        $totalJobs . ' messages scheduled'
                    );
                
                } else {
                
                    $campaign_store->update([
                        'message_status' => 'Failed',
                        'message_text'   => 'No Leads Found!'
                    ]);
                
                    return redirect()->back()->with('error', 'No Leads Found!');
                }
            }                                     
            else{
                return redirect()->back()->with('error','please select right');
            }

        } else {

            Log::info("📌 Campaign Scheduled", [
                'campaign_id' => $campaign_store->id,
                'run_at' => $request->date_and_time,
                'audience' => $audience
            ]);
            
            return redirect()->back()->with('success','Campaign message scheduled successfully!');            
        }

    }

    public function show($id){
        $post = DB::table('metawhatsappcampaigns as metawhatsapp')
            ->leftjoin('partners as partner','partner.id','=','metawhatsapp.partner_id')
            ->leftjoin('associates as associate','associate.id','=','metawhatsapp.associate_id')
            ->leftjoin('users as user','user.id','=','metawhatsapp.client_id')
            ->leftjoin('contactpluses as contactp','contactp.id','metawhatsapp.contactp_id')
            ->leftjoin('admins as admin','admin.id','=','metawhatsapp.care_off_id')
            ->select('metawhatsapp.*','partner.rec_off_name','associate.pty_ag_name','contactp.office_eng_name','admin.name as careoffname')
            ->where('metawhatsapp.id','=',$id)
            ->first();

        // Response Data
        $postResponses = DB::table('metawhatsappcampaignresponses')->where('metawhatsappcampaign_id',$id)->get();

        return view("admin.metawhatsappcampaign.show", compact('post','postResponses'));

    }

    public function getListTempAuto(Request $request){
        $audience = $request->template_for;

        $userD = Auth::guard('admin')->user();
        $userpermission = Adminpermission::where('staff_id','=',$userD->id)->first();

        $tempLists = Metawhatsapptemplate::where('template_for','=',$audience)->where('template_used_for','=','auto_message')->get();

        $res = '<option></option>';
        if($tempLists->count() > 0){
            foreach($tempLists as $tempList){
                if ($userD->user_type == 1 || (isset($userpermission) && $userpermission->meta_whatsapp_campaign_create_user == 1) || $tempList->public == 1 || $tempList->careoff_id == $userD->id) {
                    $res .= "<option value=".$tempList->id.">".$tempList->template_name."</option>";
                }
             }
        }

        $data['res'] = $res;

        return response()->json($data);
    }

    public function getListTemp(Request $request){

        $audience = $request->audience;
        $metapai_id = $request->metaapi_id;

        $userD = Auth::guard('admin')->user();
        $userpermission = Adminpermission::where('staff_id','=',$userD->id)->first();


        if ($metapai_id != '') {
            
            $tempLists = Metawhatsapptemplate::where('template_for', 'LIKE', '%' . $audience . '%')
                                            ->where('metaapi_id','=',$metapai_id)
                                            ->where('template_used_for','!=','auto_message')
                                            ->orWhereNull('template_used_for')
                                            ->get();
            // $tempLists = Metawhatsapptemplate::where('template_for','=',$audience)->where('metaapi_id','=',$metapai_id)->where(function($query){
            //     $query->where('template_used_for', '!=', 'auto_message')
            //       ->orWhereNull('template_used_for');
            // })->get();
        }else{
            $tempLists = Metawhatsapptemplate::where('template_for', 'LIKE', '%' . $audience . '%')->where(function($query){
                $query->where('template_used_for', '!=', 'auto_message')
                  ->orWhereNull('template_used_for');
            })->get();
        }



        // if ($userD->user_type == 1 || (isset($userpermission) && $userpermission->meta_whatsapp_campaign_create_user == 1)) {

        //     if ($metapai_id != '') {
        //         $tempLists = Metawhatsapptemplate::where('template_for','=',$audience)->where('metaapi_id','=',$metapai_id)->where('metaapi_id','!=','')->get();
        //     } else {
        //         $tempLists = Metawhatsapptemplate::where('template_for','=',$audience)->get();
        //     }

        // } else {

        //     if ($metapai_id != '') {
        //         // $tempLists = Metawhatsapptemplate::where('template_for','=',$audience)->where('metaapi_id','=',$metapai_id)->where('careoff_id','=',$userD->id)->get();
        //         $tempLists = Metawhatsapptemplate::where('template_for','=',$audience)->where('metaapi_id','=',$metapai_id)->where('careoff_id','=',$userD->id)->get();
        //     } else {
        //         $tempLists = Metawhatsapptemplate::where('template_for','=',$audience)->where('careoff_id','=',$userD->id)->get();
        //     }

        // }

        $res = '';
        if($tempLists->count() > 0){
            foreach($tempLists as $tempList){
                if ($userD->user_type == 1 || (isset($userpermission) && $userpermission->meta_whatsapp_campaign_create_user == 1) || (isset($userpermission) && $userpermission->meta_whatsapp_campaign_view == 1) || $tempList->public == 1 || $tempList->careoff_id == $userD->id) {
                    $res .= "<option value=".$tempList->id.">".$tempList->template_name."</option>";
                }
             }
        }

        $data['res'] = $res;

        return response()->json($data);
    }

    public function getListTempID(Request $request){
        $post = Metawhatsapptemplate::where('id','=',$request->id)->where('status','=',1)->first();

        return response()->json($post);
    }

    public function campaignDelete(Request $request)
    {
        DB::beginTransaction();
    
        try {
    
            $campaignId = $request->campaign_id;
    
            // ✅ Find campaign
            $campaign = Metawhatsappcampaign::findOrFail($campaignId);
    
            // ✅ Delete campaign responses first (child records)
            Metawhatsappcampaignresponse::where(
                'metawhatsappcampaign_id',
                $campaignId
            )->delete();
    
            // ✅ Delete main campaign
            $campaign->delete();
    
            DB::commit();
    
            return redirect()->back()->with(
                'success',
                'Campaign deleted successfully!'
            );
    
        } catch (\Throwable $e) {
    
            DB::rollBack();
    
            Log::error('Campaign Delete Failed', [
                'campaign_id' => $request->campaign_id,
                'error'       => $e->getMessage(),
                'line'        => $e->getLine(),
            ]);
    
            return redirect()->back()->with(
                'error',
                'Something went wrong while deleting the campaign.'
            );
        }
    }

    public function updateMobile(Request $request)
    {
        try {

            $request->validate([
                'id' => 'required',
                'mobile_no' => 'required',
                'umn_audience' => 'required'
            ]);

            DB::beginTransaction();

            $postResponse = DB::table('metawhatsappcampaignresponses')
                ->where('id', $request->id)
                ->first();

            if (!$postResponse) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found'
                ], 404);
            }

            $old = $postResponse->mobile_no;
            $new = $request->mobile_no;
            $audience = $request->umn_audience;

            // ===================== ALLCONTACTS =====================
            if ($audience == 'allcontact') {

                DB::table('allcontacts')
                    ->where(function ($q) use ($old) {
                        $q->where('primary_no_wsp', $old)
                        ->orWhere('secondary_no_wsp', $old)
                        ->orWhere('mobile_no1_wsp', $old)
                        ->orWhere('mobile_no2_wsp', $old)
                        ->orWhere('mobile_no3_wsp', $old);
                    })
                    ->update([
                        'primary_no_wsp'   => DB::raw("IF(primary_no_wsp = '$old', '$new', primary_no_wsp)"),
                        'secondary_no_wsp' => DB::raw("IF(secondary_no_wsp = '$old', '$new', secondary_no_wsp)"),
                        'mobile_no1_wsp'   => DB::raw("IF(mobile_no1_wsp = '$old', '$new', mobile_no1_wsp)"),
                        'mobile_no2_wsp'   => DB::raw("IF(mobile_no2_wsp = '$old', '$new', mobile_no2_wsp)"),
                        'mobile_no3_wsp'   => DB::raw("IF(mobile_no3_wsp = '$old', '$new', mobile_no3_wsp)")
                    ]);
            }

            // ===================== CONTACTPLUSES =====================
            elseif ($audience == 'contactp') {

                DB::table('contactpluses')
                    ->where(function ($q) use ($old) {
                        $q->where('prim_contact', $old)
                        ->orWhere('sec_contact', $old)
                        ->orWhere('contact4', $old)
                        ->orWhere('contact5', $old)
                        ->orWhere('contact6', $old)
                        ->orWhere('contact7', $old)
                        ->orWhere('contact8', $old)
                        ->orWhere('contact9', $old)
                        ->orWhere('contact10', $old)
                        ->orWhere('contact11', $old)
                        ->orWhere('contact12', $old);
                    })
                    ->update([
                        'prim_contact' => DB::raw("IF(prim_contact = '$old', '$new', prim_contact)"),
                        'sec_contact'  => DB::raw("IF(sec_contact = '$old', '$new', sec_contact)"),
                        'contact4'     => DB::raw("IF(contact4 = '$old', '$new', contact4)"),
                        'contact5'     => DB::raw("IF(contact5 = '$old', '$new', contact5)"),
                        'contact6'     => DB::raw("IF(contact6 = '$old', '$new', contact6)"),
                        'contact7'     => DB::raw("IF(contact7 = '$old', '$new', contact7)"),
                        'contact8'     => DB::raw("IF(contact8 = '$old', '$new', contact8)"),
                        'contact9'     => DB::raw("IF(contact9 = '$old', '$new', contact9)"),
                        'contact10'    => DB::raw("IF(contact10 = '$old', '$new', contact10)"),
                        'contact11'    => DB::raw("IF(contact11 = '$old', '$new', contact11)"),
                        'contact12'    => DB::raw("IF(contact12 = '$old', '$new', contact12)")
                    ]);
            }

            // ===================== LEADS =====================
            elseif ($audience == 'leads') {

                DB::table('leads')
                    ->where('mob_no', $old)
                    ->orWhere('whatsapp_no', $old)
                    ->update([
                        'mob_no'      => DB::raw("IF(mob_no = '$old', '$new', mob_no)"),
                        'whatsapp_no' => DB::raw("IF(whatsapp_no = '$old', '$new', whatsapp_no)")
                    ]);
            }

            // ===================== USERS =====================
            elseif ($audience == 'client') {

                DB::table('users')
                    ->where('mobile_no', $old)
                    ->update([
                        'mobile_no' => $new
                    ]);
            }

            // ===================== ASSOCIATES =====================
            elseif ($audience == 'associates') {

                DB::table('associates')
                    ->where(function ($q) use ($old) {
                        $q->where('pty_mobile', $old)
                        ->orWhere('sec_mob_no', $old);
                    })
                    ->update([
                        'pty_mobile' => DB::raw("IF(pty_mobile = '$old', '$new', pty_mobile)"),
                        'sec_mob_no' => DB::raw("IF(sec_mob_no = '$old', '$new', sec_mob_no)")
                    ]);
            }

            // ===================== PARTNERS =====================
            elseif ($audience == 'partners') {

                DB::table('partners')
                    ->where(function ($q) use ($old) {
                        $q->where('owner_mobile_no', $old)
                        ->orWhere('office_no', $old)
                        ->orWhere('primary_mob', $old)
                        ->orWhere('secondary_mob', $old);
                    })
                    ->update([
                        'owner_mobile_no' => DB::raw("IF(owner_mobile_no = '$old', '$new', owner_mobile_no)"),
                        'office_no'       => DB::raw("IF(office_no = '$old', '$new', office_no)"),
                        'primary_mob'     => DB::raw("IF(primary_mob = '$old', '$new', primary_mob)"),
                        'secondary_mob'   => DB::raw("IF(secondary_mob = '$old', '$new', secondary_mob)")
                    ]);
            }

            else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid audience'
                ], 400);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'id' => $request->id,
                'mobile_no' => $new
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            \Log::error('Mobile Update Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

}
