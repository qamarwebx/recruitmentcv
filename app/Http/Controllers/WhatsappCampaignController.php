<?php

namespace App\Http\Controllers;

use App\Jobs\AllcontactMultSendJob;
use App\Jobs\AllcontactSendJob;
use App\Jobs\AssocMultSendJob;
use App\Jobs\AssocSendJob;
use App\Jobs\ClientMultSendJob;
use App\Jobs\ClientSendJob;
use App\Jobs\ContactplusMultiSendJob;
use App\Jobs\ContactpSendJob;
use App\Jobs\PartnerMultSendJob;
use App\Jobs\PartnerSendJob;
use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\Allcontact;
use App\Models\Associates;
use App\Models\Basepathstatus;
use App\Models\Campaignlist;
use App\Models\City;
use App\Models\Contactp;
use App\Models\Contactplus;
use App\Models\Country;
use App\Models\Groupallc;
use App\Models\Partner;
use App\Models\Templatecampaign;
use App\Models\User;
use App\Models\Whatsappapi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Whatsappcamptemplate;
use App\Models\Metawhatsapptemplate;
use App\Models\Groupm;
use App\Models\Metawhatsappapi;
use App\Models\Normalwhatsappcampaignresponse;
use App\Models\Whatsappchaturl;
use PHPUnit\Framework\Constraint\Count;
use Str;
use Illuminate\Support\Facades\Schema;


use function PHPUnit\Framework\fileExists;

class WhatsappCampaignController extends Controller
{
    public function index(){
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        // $wapis = Whatsappapi::orderBy('id','DESC')->get();
        $wapis = Whatsappapi::where('api_for','=','campaign_not')->orderBy('id','DESC')->where('status',1)->get();
        // $wtemps = Templatecampaign::orderBy('id','DESC')->where('template_for','=','2')->get();
        $wtemps = Whatsappcamptemplate::orderBy('id','DESC')->get();
        $groupms = Groupm::orderBy('name')->get();
        $groupallcs = Groupallc::orderBy('name')->get();
        $countries = Country::orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        // dd($wapis);

        return view('admin.whatsapp.campaign.index',['wapis' => $wapis,'cities' => $cities,'countries' => $countries,'groupallcs' => $groupallcs,'wtemps' => $wtemps,'permission' => $permission,'groupms' => $groupms]);
    }



    public function indexJson(Request $request){

        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $data_det = DB::table('campaignlists as campign')
            ->leftJoin('admins as admin','campign.admin_id','=','admin.id')
            ->select('campign.*','admin.name as uname')
            ->orderBy('campign.id','DESC')
            ->get();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
          if ($permission->whatsapp_campaign_view == 1) {
            $data_det = DB::table('campaignlists as campign')
            ->leftJoin('admins as admin','campign.admin_id','=','admin.id')
            ->select('campign.*','admin.name as uname')
            ->orderBy('campign.id','DESC')
            ->get();
          } else {
            $data_det = DB::table('campaignlists as campign')
            ->leftJoin('admins as admin','campign.admin_id','=','admin.id')
            ->select('campign.*','admin.name as uname')
            ->where('campign.admin_id','=',Auth::guard('admin')->user()->id)
            ->orderBy('campign.id','DESC')
            ->get();
          }

        }




        $data['data'] = $data_det;
        return response()->json($data);
    }

    public function store(Request $request){
        $basepathstatus = Basepathstatus::first();
        // All Request Data
        $audience = $request->audience;
        $name = $request->name;
        $wapi_id_text = $request->wapi_id_text;
        $wtemp = $request->wtemp;
        $whs_msg = $request->whs_msg;
        $whs_msg_ar = $request->whs_msg_ar;
        $campaign_type = $request->campaign_type;
        // Get Normal Whatsapp API for Send Message
        $getAPIs = Whatsappapi::wherein('id',$wapi_id_text)->get();
        // Get Template
        if ($wtemp == 'custom_temp') {
            if ($request->hasFile('temp_file')) {
                $file = $request->file('temp_file');
                $file_name = $file->getClientOriginalName();
                $filename_ren = pathinfo($file_name,PATHINFO_FILENAME);
                $fileext_ren = pathinfo($file_name,PATHINFO_EXTENSION);
                $repspfilename = str_replace(" ","_",$filename_ren);
                $new_file = $repspfilename.'.'.$fileext_ren;
                if (isset($basepathstatus) && $basepathstatus->base_path_status == 1) {
                    $file->move(base_path().'/public/admin/assets/images/template',$new_file);
                } else {
                    $file->move(base_path().'/public_html/admin/assets/images/template',$new_file);
                }

                $tempfile = $new_file;
                $tempfileurl = url('admin/assets/images/template/'.$tempfile);
            } else {
                $tempfile = '';
                $tempfileurl = "";
            }

            $eng_msg_body = $whs_msg;
            $ar_mesg_body = $whs_msg_ar;

        }elseif ($wtemp == 'template') {
            // Get Template Data
            $templateDet = Whatsappcamptemplate::find($request->temp_id);

            if ($request->hasFile('temp_file')) {
                $file = $request->file('temp_file');
                $file_name = $file->getClientOriginalName();
                $filename_ren = pathinfo($file_name,PATHINFO_FILENAME);
                $fileext_ren = pathinfo($file_name,PATHINFO_EXTENSION);
                $repspfilename = str_replace(" ","_",$filename_ren);
                $new_file = $repspfilename.'.'.$fileext_ren;
                if (isset($basepathstatus) && $basepathstatus->base_path_status == 1) {
                    $file->move(base_path().'/public/admin/assets/images/template',$new_file);
                } else {
                    $file->move(base_path().'/public_html/admin/assets/images/template',$new_file);
                }

                $tempfile = $new_file;
                $tempfileurl = url('admin/assets/images/template/'.$tempfile);
            } else {
                if (isset($templateDet) && $templateDet->file != '') {
                    if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                        $filepath = base_path('public/admin/assets/images/template/'.$templateDet->file);
                    }else{
                        $filepath = base_path('public_html/admin/assets/images/template/'.$templateDet->file);
                    }

                    if (fileExists($filepath)) {
                        $tempfile = $templateDet->file;
                        $tempfileurl = $filepath;
                    } else {
                        $tempfile = '';
                        $tempfileurl = "";
                    }

                } else {
                    $tempfile = '';
                    $tempfileurl = "";
                }

            }

            if ($whs_msg != '') {
                $eng_msg_body = $whs_msg;
            } else {
                if (isset($templateDet->msg_whatsapp) && $templateDet->msg_whatsapp != '') {
                    $eng_msg_body = $templateDet->msg_whatsapp;
                } else {
                    $eng_msg_body = "";
                }
            }

            if ($whs_msg_ar != '') {
                $ar_mesg_body = $whs_msg_ar;
            } else {
                if (isset($templateDet->msg_whatsapp_ar) && $templateDet->msg_whatsapp_ar != '') {
                    $ar_mesg_body = $templateDet->msg_whatsapp_ar;
                } else {
                    $ar_mesg_body = "";
                }
            }

        }elseif ($wtemp == 'template_multiple') {
            $template_dets = Whatsappcamptemplate::whereIn('id',$request->temp_id)->get();

            if (isset($template_dets)) {
                $filen_arr = [];
                $fielpathurl_arr = [];
                $eng_msg_body_arr = [];
                $ar_msg_body_arr = [];

                foreach ($template_dets as $template_det) {
                    $filen_arr  [] = $template_det->file;
                    $eng_msg_body_arr [] = $template_det->msg_whatsapp;
                    $ar_msg_body_arr [] = $template_det->msg_whatsapp_ar;

                    if (isset($basepathstatus) && $basepathstatus->base_path_status == 1) {
                        $filepath = base_path('public/admin/assets/images/template/'.$template_det->file);
                    } else {
                        $filepath = base_path('public_html/admin/assets/images/template/'.$template_det->file);
                    }

                   $fielpathurl_arr [] = $filepath;

                }

                $tempfile = implode("//Desc//",$filen_arr);
                $tempfileurl = implode("//Desc//",$fielpathurl_arr);
                $eng_msg_body = implode("//Desc//",$eng_msg_body_arr);
                $ar_mesg_body = implode("//Desc//",$ar_msg_body_arr);

            } else {
                $tempfile = "";
                $tempfileurl = "";
                $eng_msg_body = "";
                $ar_mesg_body = "";
            }


        }else {
            $tempfile = "";
            $tempfileurl = "";
            $eng_msg_body = "";
            $ar_mesg_body = "";
        }

        // Store Campaign
        $post = New Campaignlist();
        $post->name = $name;
        $post->audience = $audience;
        $post->wapi_id_text = implode(",",$wapi_id_text);
        $post->campaign_type = $campaign_type;
        $post->wtemp = $wtemp;
        if ($wtemp == 'template_multiple') {
            $post->temp_id_text = implode(",",$request->temp_id);
        }
        if ($campaign_type == '2') {
            $post->date_and_time = $request->date_and_time;
        }
        if ($audience == 'Partner') {
            $post->partner_status = implode(",",$request->partner_status);
            $post->partner_contact_type = $request->partner_contact_type;
        }
        if ($audience == 'Client') {
            $post->client_status = implode(",",$request->client_status);
        }
        if ($audience == 'Associate') {
            $post->assoc_contact_type = $request->assoc_contact_type;
            $post->assoc_status = implode(",",$request->assoc_status);
        }
        if ($audience == 'Contact+') {
            if ($request->contactp_status != '') {
                $post->contactp_status = implode(",",$request->contactp_status);
            }

            if ($request->contactp_contact_type != '') {
                $post->contactp_contact_type = implode(",",$request->contactp_contact_type);
            }

            if ($request->groupm != '') {
                $post->groupm = implode(",",$request->groupm);
            }

            if ($request->country_id2 != '') {
                $post->country_id = implode(",",$request->country_id2);
            }

            if ($request->city_id2 != '') {
                $post->city_id = implode(",",$request->city_id2);
            }

        }

        if ($audience == 'Allcontact') {
            if ($request->allcontact_status != '') {
                $post->allcontact_status = implode(",",$request->allcontact_status);
            }

            if ($request->allcontact_contact_type != '') {
                $post->allcontact_contact_type = implode(",",$request->allcontact_contact_type);
            }

            if ($request->groupmallc != '') {
                $post->groupmallc = implode(",",$request->groupmallc);
            }

            if ($request->country_id != '') {
                $post->country_id = implode(",",$request->country_id);
            }

            if ($request->city_id != '') {
                $post->city_id = implode(",",$request->city_id);
            }


        }

        $post->whs_msg = $eng_msg_body;
        $post->whs_msg_ar = $ar_mesg_body;
        $post->temp_file = $tempfile;
        $post->temp_file_url = $tempfileurl;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        // Send Message Based on Campaign Type
        if ($campaign_type == 1) {
            $totalcontact = [];

            if (isset($getAPIs)) {
                foreach ($getAPIs as $getAPI) {
                    if ($audience == 'Allcontact') {
                        $minimumInterval = 30; // 25 Seconds
                        $maximumInterval = 60; // 60 Seconds
                        $currentDelay = 0; // 0 Seconds

                        $groupallc_id = $request->groupmallc;
                        $allcontact_status = $request->allcontact_status;
                        $country_id = $request->country_id;
                        $city_id = $request->city_id;

                        $allcontacts = Allcontact::wherein('status',$allcontact_status)->where(function($query) use($groupallc_id,$country_id,$city_id){
                            if ($groupallc_id != '') {
                                $query->wherein('group_id',$groupallc_id);
                            }

                            if ($country_id != '') {
                                $query->wherein('country_id',$groupallc_id);
                            }

                            if ($city_id != '') {
                                $query->whereIn('city_id', $city_id);
                            }

                        })->get();

                        $contactCount = count($allcontacts);

                        if ($contactCount > 0) {
                            if ($wtemp == 'template_multiple') {
                                $template_id = $request->temp_id;
                                $counter = 0;
                                $tempC = count($template_id);
                                $tempcd = $tempC - 1;
                                $cdCount = 1;

                                foreach ($allcontacts as $allcontact) {
                                    $randomdelay = rand($minimumInterval, $maximumInterval);
                                    $currentDelay += $randomdelay;
                                    $template_list = Whatsappcamptemplate::find($template_id[$counter]);

                                    AllcontactMultSendJob::dispatch($allcontact,$getAPI,$post,$template_list)->delay(now()->addSeconds($currentDelay));

                                    if ($counter == $tempcd) {
                                        $counter = 0;
                                    }else{
                                        $counter++;
                                    }

                                }

                            } else {
                                foreach ($allcontacts as $allcontact) {

                                    $randomdelay = rand($minimumInterval, $maximumInterval);
                                    $currentDelay += $randomdelay;

                                    AllcontactSendJob::dispatch($allcontact,$getAPI,$post)->delay(now()->addSeconds($currentDelay));
                                }
                            }
                        }

                        $totalcontact [] = $contactCount;

                    }

                    if ($audience == 'Associate') {
                        $minimumInterval = 30; // 25 Seconds
                        $maximumInterval = 60; // 60 Seconds
                        $currentDelay = 0; // 0 Seconds

                        // Get all associate details
                        $assoc_status = $request->assoc_status;
                        $assocs = Associates::wherein('status',$assoc_status)->get();

                        $assoc_count = count($assocs);

                        if ($assoc_count > 0) {
                            if ($wtemp == 'template_multiple') {
                                $template_id = $request->temp_id;
                                $counter = 0;
                                $tempC = count($template_id);
                                $tempcd = $tempC - 1;
                                $cdCount = 1;

                                foreach ($assocs as $assoc) {
                                    $randomdelay = rand($minimumInterval, $maximumInterval);
                                    $currentDelay += $randomdelay;
                                    $template_list = Whatsappcamptemplate::find($template_id[$counter]);

                                    AssocMultSendJob::dispatch($assoc,$getAPI,$post,$template_list)->delay(now()->addSeconds($currentDelay));
                                    if ($counter == $tempcd) {
                                        $counter = 0;
                                    }else{
                                        $counter++;
                                    }
                                }


                            } else {
                                foreach ($assocs as $assoc) {
                                    AssocSendJob::dispatch($assoc,$getAPI,$post)->delay(now()->addSeconds($currentDelay));
                                }
                            }
                        }

                        $totalcontact [] = $assoc_count;
                    }

                    if ($audience == 'Contact+') {
                        $minimumInterval = 30; // 25 Seconds
                        $maximumInterval = 60; // 60 Seconds
                        $currentDelay = 0; // 0 Seconds

                        $groupm_id = $request->groupm;
                        $contactp_status = $request->contactp_status;
                        $country_id = $request->country_id2;
                        $city_id = $request->city_id2;

                        $contactps = Contactplus::wherein('status',$contactp_status)->where(function($query) use($groupm_id,$country_id,$city_id){
                            if ($groupm_id != '') {
                                $query->wherein('group_id',$groupm_id);
                            }

                            if ($country_id != '') {
                                $query->wherein('country_id',$country_id);
                            }

                            if ($city_id != '') {
                                $query->whereIn('city_id',$city_id);
                            }

                        })->get();

                        $contact_count2 = count($contactps);

                        if ($contact_count2 > 0) {
                            if ($wtemp == 'template_multiple') {
                                $template_id = $request->temp_id;
                                $counter = 0;
                                $tempC = count($template_id);
                                $tempcd = $tempC - 1;
                                $cdCount = 1;

                                foreach ($contactps as $contactp) {
                                    $randomdelay = rand($minimumInterval, $maximumInterval);
                                    $currentDelay += $randomdelay;
                                    $template_list = Whatsappcamptemplate::find($template_id[$counter]);

                                    ContactplusMultiSendJob::dispatch($contactp,$getAPI,$post,$template_list)->delay(now()->addSeconds($currentDelay));

                                    if ($counter == $tempcd) {
                                        $counter = 0;
                                    }else{
                                        $counter++;
                                    }
                                }



                            } else {
                                foreach ($contactps as $contactp) {
                                    $randomdelay = rand($minimumInterval, $maximumInterval);
                                    $currentDelay += $randomdelay;
                                    ContactpSendJob::dispatch($contactp,$getAPI,$post)->delay(now()->addSeconds($currentDelay));
                                }
                            }
                        }


                        $totalcontact [] = $contact_count2;
                    }

                    if ($audience == 'Client') {
                        $minimumInterval = 30; // 25 Seconds
                        $maximumInterval = 60; // 60 Seconds
                        $currentDelay = 0; // 0 Seconds

                        $client_status = $request->client_status;
                        $clients = User::where(function($query) use($client_status){
                            $query->wherein('status',$client_status);
                        })->get();

                        $client_count = Count($clients);

                        if ($client_count > 0) {
                            if ($wtemp == 'template_multiple') {
                                $template_id = $request->temp_id;
                                $counter = 0;
                                $tempC = count($template_id);
                                $tempcd = $tempC - 1;
                                $cdCount = 1;

                                foreach ($clients as $client) {
                                    $randomdelay = rand($minimumInterval, $maximumInterval);
                                    $currentDelay += $randomdelay;
                                    $template_list = Whatsappcamptemplate::find($template_id[$counter]);

                                    ClientMultSendJob::dispatch($client,$getAPI,$post,$template_list)->delay(now()->addSeconds($currentDelay));

                                    if ($counter == $tempcd) {
                                        $counter = 0;
                                    }else{
                                        $counter++;
                                    }

                                }

                            } else {
                                foreach ($clients as $client2) {
                                    ClientSendJob::dispatch($client2,$getAPI,$post)->delay(now()->addSeconds($currentDelay));
                                }
                            }
                        }

                        $totalcontact [] = $client_count;

                    }

                    if ($audience == 'Partner') {
                        $minimumInterval = 30; // 25 Seconds
                        $maximumInterval = 60; // 60 Seconds
                        $currentDelay = 0; // 0 Seconds

                        // Get all partner details
                        $partner_status = $request->partner_status;
                        $partners = Partner::where(function($query) use($partner_status){
                            $query->wherein('status',$partner_status);
                        })->get();

                        $partner_count = count($partners);

                        // Send Whatsapp Message
                        if ($partner_count > 0) {
                            if ($wtemp == 'template_multiple') {
                                $template_id = $request->temp_id;
                                $counter = 0;
                                $tempC = count($template_id);
                                $tempcd = $tempC - 1;
                                $cdCount = 1;

                                foreach ($partners as $partner) {
                                    $randomdelay = rand($minimumInterval, $maximumInterval);
                                    $currentDelay += $randomdelay;
                                    $template_list = Whatsappcamptemplate::find($template_id[$counter]);

                                    PartnerMultSendJob::dispatch($partner,$getAPI,$post,$template_list)->delay(now()->addSeconds($currentDelay));

                                    if ($counter == $tempcd) {
                                        $counter = 0;
                                    }else{
                                        $counter++;
                                    }
                                }

                            } else {
                                foreach ($partners as $partner) {
                                    PartnerSendJob::dispatch($partner,$getAPI,$post)->delay(now()->addSeconds($currentDelay));
                                }
                            }
                        }



                        $totalcontact [] = $partner_count;

                    }
                }
            }

            $total_rec = array_sum($totalcontact).' messages are to be processed for send!';
        } else {
            $total_rec = "Campaign message are scheduled";
        }
        return redirect()->back()->with('success',$total_rec);
    }

    // Refactor Code Start Here

    public function store_25012025(Request $request){
        $basepathstatus = Basepathstatus::first();

        // Retrieve Request Data
        $audience = $request->audience;
        $name = $request->name;
        $wapi_id_text = $request->wapi_id_text;
        $wtemp = $request->wtemp;
        $whs_msg = $request->whs_msg;
        $whs_msg_ar = $request->whs_msg_ar;
        $campaign_type = $request->campaign_type;

        // Get Normal Whatsapp API for sending messages
        $apis = Whatsappapi::whereIn('id', $wapi_id_text)->get();

        // Process Template
        [$tempfile, $tempfileurl, $eng_msg_body, $ar_msg_body] = $this->processTemplate($request, $wtemp, $basepathstatus);

        // Store Campaign
        $campaign = $this->storeCampaign($request, $name, $audience, $wapi_id_text, $campaign_type, $wtemp, $tempfile, $tempfileurl, $eng_msg_body, $ar_msg_body);

        // Dispatch Messages Based on Campaign Type
        $totalMessages = ($campaign_type == 1) ? $this->dispatchMessages($request, $audience, $apis, $campaign, $wtemp) : "Campaign message are scheduled";

        return redirect()->back()->with('success', $totalMessages);
    }

    /**
        * Process Template and File Uploads
    */
    private function processTemplate(Request $request, $wtemp, $basepathstatus){
        $tempfile = '';
        $tempfileurl = '';
        $eng_msg_body = '';
        $ar_msg_body = '';

        if ($wtemp === 'custom_temp') {
            [$tempfile, $tempfileurl] = $this->handleFileUpload($request, $basepathstatus);
            $eng_msg_body = $request->whs_msg;
            $ar_msg_body = $request->whs_msg_ar;
        } elseif ($wtemp === 'template') {
            [$tempfile, $tempfileurl, $eng_msg_body, $ar_msg_body] = $this->processSingleTemplate($request, $basepathstatus);
        } elseif ($wtemp === 'template_multiple') {
            [$tempfile, $tempfileurl, $eng_msg_body, $ar_msg_body] = $this->processMultipleTemplates($request, $basepathstatus);
        }

        return [$tempfile, $tempfileurl, $eng_msg_body, $ar_msg_body];
    }

    /**
        * Handle File Upload
    */
    private function handleFileUpload(Request $request, $basepathstatus){
        if ($request->hasFile('temp_file')) {
            $file = $request->file('temp_file');
            $filename = str_replace(" ", "_", pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();
            $newFileName = "$filename.$extension";

            $uploadPath = (isset($basepathstatus) && $basepathstatus->base_path_status == 1) ? base_path('/public/admin/assets/images/template') : base_path('/public_html/admin/assets/images/template');

            $file->move($uploadPath, $newFileName);

            return [$newFileName, url("admin/assets/images/template/$newFileName")];
        }

        return ['', ''];
    }

    /**
        * Process Single Template
    */

    private function processSingleTemplate(Request $request, $basepathstatus){
        $template = Whatsappcamptemplate::find($request->temp_id);
        [$tempfile, $tempfileurl] = $this->handleFileUpload($request, $basepathstatus);

        if (!$tempfile && isset($template->file)) {
            $filePath = $this->getTemplateFilePath($template->file, $basepathstatus);
            $tempfile = file_exists($filePath) ? $template->file : '';
            $tempfileurl = file_exists($filePath) ? $filePath : '';
        }

        $eng_msg_body = $request->whs_msg ?: ($template->msg_whatsapp ?? '');
        $ar_msg_body = $request->whs_msg_ar ?: ($template->msg_whatsapp_ar ?? '');

        return [$tempfile, $tempfileurl, $eng_msg_body, $ar_msg_body];
    }

    /**
        * Process Multiple Templates
    */

    private function processMultipleTemplates(Request $request, $basepathstatus) {
        $templates = Whatsappcamptemplate::whereIn('id', $request->temp_id)->get();

        $files = [];
        $fileUrls = [];
        $engMessages = [];
        $arMessages = [];

        foreach ($templates as $template) {
            $files[] = $template->file;
            $fileUrls[] = $this->getTemplateFilePath($template->file, $basepathstatus);
            $engMessages[] = $template->msg_whatsapp;
            $arMessages[] = $template->msg_whatsapp_ar;
        }

        return [
            implode('//Desc//', $files),
            implode('//Desc//', $fileUrls),
            implode('//Desc//', $engMessages),
            implode('//Desc//', $arMessages),
        ];
    }

    /**
        * Get Template File Path
    */
    private function getTemplateFilePath($fileName, $basepathstatus){
        return (isset($basepathstatus) && $basepathstatus->base_path_status == 1) ? base_path("public/admin/assets/images/template/$fileName") : base_path("public_html/admin/assets/images/template/$fileName");
    }

    /**
        * Store Campaign
    */

    private function storeCampaign(Request $request, $name, $audience, $wapi_id_text, $campaign_type, $wtemp, $tempfile, $tempfileurl, $eng_msg_body, $ar_msg_body){
        $campaign = new Campaignlist();
        $campaign->name = $name;
        $campaign->audience = $audience;
        $campaign->wapi_id_text = implode(',', $wapi_id_text);
        $campaign->campaign_type = $campaign_type;
        $campaign->wtemp = $wtemp;
        $campaign->temp_file = $tempfile;
        $campaign->temp_file_url = $tempfileurl;
        $campaign->whs_msg = $eng_msg_body;
        $campaign->whs_msg_ar = $ar_msg_body;
        $campaign->admin_id = Auth::guard('admin')->user()->id;

        if ($wtemp === 'template_multiple') {
            $campaign->temp_id_text = implode(',', $request->temp_id);
        }

        if ($campaign_type == '2') {
            $campaign->date_and_time = $request->date_and_time;
        }

        if ($audience === 'Contact+') {
            $campaign->contactp_status = implode(',', $request->contactp_status);
            $campaign->contactp_contact_type = implode(',', $request->contactp_contact_type);
            $campaign->groupm = implode(',', $request->groupm);
        }

        if ($audience === 'Allcontact') {
            $campaign->allcontact_status = implode(',', $request->allcontact_status);
            $campaign->allcontact_contact_type = implode(',', $request->allcontact_contact_type);
            $campaign->groupmallc = implode(',', $request->groupmallc);
        }

        if ($audience == 'Partner') {
            $campaign->partner_status = implode(",",$request->partner_status);
            $campaign->partner_contact_type = $request->partner_contact_type;
        }
        if ($audience == 'Client') {
            $campaign->client_status = implode(",",$request->client_status);
        }
        if ($audience == 'Associate') {
            $campaign->assoc_contact_type = $request->assoc_contact_type;
            $campaign->assoc_status = implode(",",$request->assoc_status);
        }

        $campaign->save();
        return $campaign;
    }

    /**
        * Dispatch Messages Based on Audience
    */

    private function dispatchMessages(Request $request, $audience, $apis, $campaign, $wtemp){
        $totalContacts = 0;

        foreach ($apis as $api) {
            if ($audience === 'Allcontact') {
                $totalContacts += $this->handleAllContactDispatch($request, $campaign, $api, $wtemp);
            } elseif ($audience === 'Contact+') {
                $totalContacts += $this->handleContactPlusDispatch($request, $campaign, $api, $wtemp);
            }elseif ($audience === 'Partner') {
                $totalContacts += $this->handlePartnerDispatch($request, $campaign, $api, $wtemp);
            }elseif ($audience === 'Client') {
                $totalContacts += $this->handleClienttDispatch($request, $campaign, $api, $wtemp);
            }elseif ($audience === 'Associate') {
                $totalContacts += $this->handleAssociateDispatch($request, $campaign, $api, $wtemp);
            }
        }

        return "$totalContacts messages are to be processed for send!";
    }


    /**
        * Handle All Contact Dispatch
    */

    private function handleAllContactDispatch(Request $request, $campaign, $api, $wtemp) {
        $contacts = Allcontact::whereIn('status', $request->allcontact_status)
            ->when($request->groupmallc, function ($query, $groupIds) {
                $query->whereIn('group_id', $groupIds);
            })->get();

        $this->scheduleJobs($contacts, $campaign, $api, $wtemp, 'all_contact');

        return count($contacts);
    }

    /**
        * Handle Contact+ Dispatch
    */

    private function handleContactPlusDispatch(Request $request, $campaign, $api, $wtemp){
        $contacts = Contactplus::whereIn('status', $request->contactp_status)
        ->when($request->groupm, function ($query, $groupIds) {
            $query->whereIn('group_id', $groupIds);
        })->get();

        $this->scheduleJobs($contacts, $campaign, $api, $wtemp, 'contact_plus');

        return count($contacts);
    }

    /**
     * Handle Partner Dispatch
     */

    private function handlePartnerDispatch(Request $request, $campaign, $api, $wtemp){
        $partners = Partner::whereIn('status',$request->partner_status)->get();

        $this->scheduleJobs($partners,$campaign,$api,$wtemp, 'partners');

        return count($partners);
    }

    /**
     * Handle Client Dispatch
    */

    private function handleClienttDispatch(Request $request, $campaign, $api, $wtemp) {
        $clients = User::whereIn('status',$request->client_status)->get();

        $this->scheduleJobs($clients, $campaign, $api, $wtemp, 'clients');

        return count($clients);
    }

    /**
     * Handle Associate Dispatch
    */

    private function handleAssociateDispatch(Request $request, $campaign, $api, $wtemp){
        $associates = Associates::whereIn('status',$request->assoc_status)->get();

        $this->scheduleJobs($associates,$campaign, $api,$wtemp, 'associates');

        return count($associates);
    }

    /**
        * Schedule Jobs for Message Dispatch
    */

    private function scheduleJobs($contacts, $campaign, $api, $wtemp, $type) {
        $minInterval = 30;
        $maxInterval = 60;
        $currentDelay = 0;

        $counter = 0;
        $template_id = request('temp_id');
        $tempC = count($template_id);
        $tempcd = $tempC - 1;

        $templates = [];
        if ($wtemp === 'template_multiple') {
            // Pre-fetch the templates based on the template ids to avoid multiple database queries within the loop.
            $templates = Whatsappcamptemplate::whereIn('id', $template_id)->get();
        }

        foreach ($contacts as $index => $contact) {
            // Random delay calculation
            $randomDelay = rand($minInterval, $maxInterval);
            $currentDelay += $randomDelay;

            // Handle template logic
            if ($wtemp === 'template_multiple') {
                $template = $templates[$index % count($templates)]; // Recycle through templates if the array is smaller than the contacts
                $this->dispatchJob($contact, $campaign, $api, $template, $currentDelay, $type);
            } else {
                $this->dispatchJob($contact, $campaign, $api, null, $currentDelay, $type);
            }
        }


    }

    /**
        * Dispatch Individual Job
    */
    private function dispatchJob($contact, $campaign, $api, $template, $delay, $type){
        if ($type === 'all_contact') {
            AllcontactSendJob::dispatch($contact, $api, $campaign, $template)->delay(now()->addSeconds($delay));
        } elseif ($type === 'contact_plus') {
            ContactpSendJob::dispatch($contact, $api, $campaign, $template)->delay(now()->addSeconds($delay));
        } elseif ($type === 'partners') {
            PartnerSendJob::dispatch($contact, $api,$campaign, $template)->delay(now()->addSeconds($delay));
        }elseif ($type === 'clients') {
            ClientSendJob::dispatch($contact, $api,$campaign, $template)->delay(now()->addSeconds($delay));
        }elseif ($type === 'Associate') {
            AssocSendJob::dispatch($contact, $api, $campaign, $template)->delay(now()->addSeconds($delay));
        }
    }

    // Refactor Code End Here

    public function store_old24012025(Request $request){
        $basepathstatus = Basepathstatus::first();

        // All Request Data
        $audience = $request->audience;
        $name = $request->name;
        $wapi_id_text = $request->wapi_id_text;
        $wtemp = $request->wtemp;
        $temp_id = $request->temp_id;
        $whs_msg = $request->whs_msg;
        $whs_msg_ar = $request->whs_msg_ar;
        $campaign_type = $request->campaign_type;

        // Get Normal Whatsapp API for Send Message
        $getAPIs = Whatsappapi::wherein('id',$wapi_id_text)->get();

        // Get Template Details
        $template_det = Whatsappcamptemplate::find($temp_id);

        // Store File
        if ($request->hasFile('temp_file')) {
            $file = $request->file('temp_file');
            $file_name = $file->getClientOriginalName();
            $filename_ren = pathinfo($file_name,PATHINFO_FILENAME);
            $fileext_ren = pathinfo($file_name,PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ","_",$filename_ren);
            $new_file = $repspfilename.'.'.$fileext_ren;

            if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/template',$new_file);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/template',$new_file);
            }

            $tempfile = $new_file;

            $tempfileurl = url('admin/assets/images/template/'.$tempfile);
        }else{

            if (isset($template_det) && $template_det->file != '') {
                if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                    $filepath = base_path('public/admin/assets/images/template/'.$template_det->fil);
                }else{
                    $filepath = base_path('public_html/admin/assets/images/template/'.$template_det->fil);
                }

                if (fileExists($filepath)) {
                    $tempfile = $template_det->file;
                    $tempfileurl = $filepath;
                } else {
                    $tempfile = '';
                    $tempfileurl = "";
                }


            } else {
                $tempfile = '';
                $tempfileurl = "";
            }


        }

        // Store data in whatsapp camapaign list table
        $post = new Campaignlist();
        $post->audience = $audience;
        $post->name = $name;
        $post->wapi_id_text = implode(",",$wapi_id_text);
        $post->wtemp = $wtemp;
        $post->whs_msg = $whs_msg;
        $post->whs_msg_ar = $whs_msg_ar;
        $post->temp_file = $tempfile;
        $post->temp_file_url = $tempfileurl;
        $post->campaign_type = $campaign_type;
        $post->admin_id = Auth::guard('admin')->user()->id;



        if ($campaign_type == 2) {
            $post->date_and_time = $request->date_and_time;
        }

        if ($audience == 'Contact+') {
            $post->contactp_status = implode(",",$request->contactp_status);
            $post->contactp_contact_type = implode(",", $request->contactp_contact_type);
            $post->groupm = implode(",",$request->groupm);
        }

        if ($audience == 'Allcontact') {
            $post->allcontact_status = implode(",",$request->allcontact_status);
            $post->allcontact_contact_type = implode(",", $request->allcontact_contact_type);
            $post->groupmallc = implode(",",$request->groupmallc);
        }

        if ($audience == 'Associate') {
            $post->assoc_contact_type = $request->assoc_contact_type;
            $post->assoc_status = implode(",",$request->assoc_status);
        }

        if ($audience == 'Client') {
            $post->client_status = implode(",",$request->client_status);
        }

        if ($audience == 'Partner') {
            $post->partner_status = implode(',',$request->partner_status);
            $post->partner_contact_type = $request->partner_contact_type;
        }

        $post->save();

        // Send Whatsapp Messages as per condition and type

        if ($campaign_type == 1) {
            $totalcontact = [];

            if (isset($getAPIs)) {
                foreach ($getAPIs as $getAPI) {
                    if ($audience == 'Contact+') {
                        $groupm_id = $request->groupm;
                        $contactp_status = $request->contactp_status;

                        // $randomdelay = rand(30,60);

                        $minimumInterval = 30; // 25 Seconds
                        $maximumInterval = 60; // 60 Seconds
                        $currentDelay = 0; // 0 Seconds

                        // $contactps = Contactplus::whereIn('status',$contactp_status)->where(function($query) use($group_id){
                        //     if ($group_id != '') {
                        //         $query->wherein('group_id',$group_id);
                        //     }
                        // })->get();

                        $contactps = Contactplus::wherein('status',$contactp_status)->where(function($query) use($groupm_id){
                            if ($groupm_id != '') {
                                $query->wherein('group_id',$groupm_id);
                            }
                        })->get();

                        foreach ($contactps as $contactp) {
                            // dispatch(new ContactpSendJob($contactp,$getAPI,$post))->onConnection('database')->onQueue('default');
                            $randomdelay = rand($minimumInterval, $maximumInterval);
                            $currentDelay += $randomdelay;
                            ContactpSendJob::dispatch($contactp,$getAPI,$post)->delay(now()->addSeconds($currentDelay));
                        }

                        $totalcontact [] = count($contactps);

                    }

                    if ($audience == 'Allcontact') {

                        $minimumInterval = 30; // 25 Seconds
                        $maximumInterval = 60; // 60 Seconds
                        $currentDelay = 0; // 0 Seconds


                        $groupallc_id = $request->groupmallc;
                        $allcontact_status = $request->allcontact_status;

                        $allcontacts = Allcontact::wherein('status',$allcontact_status)->where(function($query) use($groupallc_id){
                            if ($groupallc_id != '') {
                                $query->wherein('group_id',$groupallc_id);
                            }
                        })->get();

                        foreach ($allcontacts as $allcontact) {

                            $randomdelay = rand($minimumInterval, $maximumInterval);
                            $currentDelay += $randomdelay;

                            AllcontactSendJob::dispatch($allcontact,$getAPI,$post)->delay(now()->addSeconds($currentDelay));
                        }



                        $totalcontact [] = count($allcontacts);

                    }

                    if ($audience == 'Associate') {
                        // Get all associate details
                        $assoc_status = $request->assoc_status;
                        $assocs = Associates::wherein('status',$assoc_status)->get();

                        // Send whatsapp Message
                        foreach ($assocs as $assoc) {
                            dispatch(new AssocSendJob($assoc,$getAPI,$post))->onQueue('default');
                        }

                        $totalcontact [] = count($assocs);
                    }

                    if ($audience == 'Client') {
                        // Get all client details
                        $client_status = $request->client_status;
                        $clients = User::where(function($query) use($client_status){
                            $query->wherein('status',$client_status);
                        })->get();

                        // Send whatsapp message
                        foreach ($clients as $client2) {
                            dispatch(new ClientSendJob($client2,$getAPI,$post))->onQueue('default');
                        }

                        $totalcontact [] = count($clients);
                    }

                    if ($audience == 'Partner') {
                        // Get all partner details
                        $partner_status = $request->partner_status;
                        $partners = Partner::where(function($query) use($partner_status){
                            $query->wherein('status',$partner_status);
                        })->get();

                        // Send Whatsapp Message
                        foreach ($partners as $partner) {
                            dispatch(new PartnerSendJob($partner,$getAPI,$post))->onConnection('database')->onQueue('default');
                            //dd(dispatch(new PartnerSendJob($partner,$getAPI,$post))->onConnection('database')->onQueue('default'));
                        }

                        $totalcontact [] = count($partners);
                    }
                }
            }

            $total_rec = array_sum($totalcontact).' messages are to be processed for send!';
        } else {
            $total_rec = "Campaign message are scheduled";
        }

        return redirect()->back()->with('success',$total_rec);

    }

    public function store_old(Request $request){
        $basepathstatus = Basepathstatus::first();
        $audience = $request->audience;
        $wtemp = $request->wtemp;
        $msgBody = $request->whs_msg;
        $armsgBody = $request->whs_msg_ar;
        $tempID = $request->temp_id;
        $sch_type = $request->campaign_type;
        $api_text_id = $request->wapi_id_text;

        // get API
        $getAPIs = Whatsappapi::wherein('id',$api_text_id)->get();

        // Get temp details
        if ($tempID != '') {
            $tempDet = Whatsappcamptemplate::find($tempID);
            $tempdetFile = $tempDet->file;
        }

        // File Store
        if ($request->hasFile('temp_file')) {
            $file = $request->file('temp_file');
            $name = $file->getClientOriginalName();
            $filename_ren = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ","_",$filename_ren);
            $new_file = $repspfilename.'.'.$fileext_ren;

            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/template',$new_file);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/template',$new_file);
            }

            $tempfile = $new_file;

            $tempfileurl = url('admin/assets/images/template/'.$tempfile);


        } else {
            if ($tempID != '' && isset($tempDet)) {
                $tempfile = $tempDet->file;
                $tempfileurl = url('admin/assets/images/template/'.$tempfile);

            } else {
                $tempfile = '';
                $tempfileurl = "";

            }
        }

        // Store in whatsapp campaign
        $post = new Campaignlist();
        $post->name = $request->name;
        $post->audience = $audience;
        if ($tempID != '') {
            $post->temp_id = $tempID;
        }
        if ($request->wapi_id_text != '') {
            $post->wapi_id_text = implode(',',$request->wapi_id_text);
        }
        $post->sch_type = $request->campaign_type;
        $post->wtemp = $request->wtemp;
        if ($sch_type == '2') {
            $post->date_and_time = date('Y-m-d h:i',strtotime($request->date_and_time));
        }
        if($audience == 'Associate'){
            $post->assoc_contact_type = $request->assoc_contact_type;
            $post->assoc_status = implode(",",$request->assoc_status);
        }

        if ($audience == 'Client') {
            $post->client_status = implode(",",$request->client_status);
        }

        if ($audience == 'Partner') {
            $post->partner_status = implode(',',$request->partner_status);
            $post->partner_contact_type = $request->partner_contact_type;
        }

        if ($audience == 'Contact+') {
            $post->contactp_status = implode(',',$request->contactp_status);
            if ($request->groupm != '') {
                $post->groupm = implode(',',$request->groupm);
            }
            $post->contactp_contact_type = implode(",",$request->contactp_contact_type);
        }

        if ($audience == 'Allcontact') {
            if ($request->groupm != '') {
                $post->groupm = implode(',',$request->groupm);
            }
            $post->allcontact_status = implode(",",$request->allcontact_status);
            $post->allcontact_contact_type = implode(",",$request->allcontact_contact_type);
        }


        $post->whs_msg = $msgBody;
        $post->whs_msg_ar = $armsgBody;
        $post->temp_file = $tempfile;
        $post->temp_file_url = $tempfileurl;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();


        if($sch_type == '1'){
            $totalcontact = [];

            if (isset($getAPIs)) {
                foreach ($getAPIs as $getAPI) {
                    // Send message based on audience
                    if ($audience == 'Associate') {
                        // Get all associate details
                        $assoc_status = $request->assoc_status;
                        $assocs = Associates::wherein('status',$assoc_status)->get();

                        // Send whatsapp Message
                        foreach ($assocs as $assoc) {
                            dispatch(new AssocSendJob($assoc,$getAPI,$post))->onQueue('default');
                        }

                        $totalcontact [] = count($assocs);
                    }

                    if ($audience == 'Client') {
                        // Get all client details
                        $client_status = $request->client_status;
                        $clients = User::where(function($query) use($client_status){
                            $query->wherein('status',$client_status);
                        })->get();

                        // Send whatsapp message
                        foreach ($clients as $client2) {
                            dispatch(new ClientSendJob($client2,$getAPI,$post))->onQueue('default');
                        }

                        $totalcontact [] = count($clients);
                    }

                    if ($audience == 'Partner') {
                        // Get all partner details
                        $partner_status = $request->partner_status;
                        $partners = Partner::where(function($query) use($partner_status){
                            $query->wherein('status',$partner_status);
                        })->get();

                        // Send Whatsapp Message
                        foreach ($partners as $partner) {
                            dispatch(new PartnerSendJob($partner,$getAPI,$post))->onConnection('database')->onQueue('default');
                            //dd(dispatch(new PartnerSendJob($partner,$getAPI,$post))->onConnection('database')->onQueue('default'));
                        }

                        $totalcontact [] = count($partners);
                    }

                    if ($audience == 'Contact+') {
                        $groupID = $request->groupm;
                        // Get All Contactp Details
                        $contactP_status = $request->contactp_status;
                        $ontactps = Contactplus::wherein('status',$contactP_status)->where(function($query) use($groupID){
                            if ($groupID != '') {
                                $query->wherein('group_id',$groupID);
                            }
                        })->get();

                        // Send Whatsapp Message
                        foreach ($ontactps as $ontactp) {
                            dispatch(new ContactpSendJob($ontactp,$getAPI,$post))->onConnection('database')->onQueue('default');
                        }
                    }

                    if ($audience == 'Allcontact') {
                        $groupID = $request->groupm;
                        $allcontact_status = $request->allcontact_status;
                        $allcontacts = Allcontact::wherein('status',$allcontact_status)->where(function($query) use($groupID){
                            if ($groupID != '') {
                                $query->wherein('group_id',$groupID);
                            }
                        })->get();

                        // Send Whatsapp Message
                        foreach ($allcontacts as $allcontact) {
                            dispatch(new AllcontactSendJob($allcontact,$getAPI,$post))->onConnection('database')->onQueue('default');
                        }
                    }

                }
                $total_rec = array_sum($totalcontact).' message are to be send!';
            }


        }else{
            $total_rec = "Campaign message are scheduled";
        }

        return redirect()->back()->with('success',$total_rec);


        // return redirect()->back()->with('success','Total '.$total_rec.' message send');

    }


    public function getTemp(Request $request){
        $id = $request->id;
        $post = Whatsappcamptemplate::find($id);
        // $post = Templatecampaign::find($id);

        return response()->json($post);
    }

    public function getTempList(Request $request){
        $templateLists = Whatsappcamptemplate::where('audience','=',$request->audience)->where('audience','!=','')->get();
        $res = '</option value="">Select</option>';
        if ($templateLists->count() > 0) {
            foreach ($templateLists as $templateList) {
                $res .= '<option value='.$templateList->id.'>'.$templateList->template_name.'</option>';
            }
        }

        $data['res'] = $res;

        return response()->json($data);
    }

    public function wtemplateList(){
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        $careoffs = Admin::where('status',1)->orderBy('name')->get();
        return view('admin.whatsapp.template.index',['perm' => $permission,'careoffs' => $careoffs]);
    }

    public function wtemplateListJson(Request $request){
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        // if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
        //     $post = DB::table('templatecampaigns as temp')
        //     ->leftJoin('admins as admin','temp.staff_id','=','admin.id')
        //     ->where('temp.template_for','=','2')
        //     ->select('temp.*','admin.name as uname')
        //     ->get();
        // }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
        //     if($permission->view_template == 1){
        //         $post = DB::table('templatecampaigns as temp')
        //         ->leftJoin('admins as admin','temp.staff_id','=','admin.id')
        //         ->select('temp.*','admin.name as uname')
        //         ->where('temp.template_for','=','2')
        //         ->get();
        //     }else{
        //         $post = DB::table('templatecampaigns as temp')
        //         ->leftJoin('admins as admin','temp.staff_id','=','admin.id')
        //         ->where('temp.staff_id','=',Auth::guard('admin')->user()->id)
        //         ->where('temp.template_for','=','2')
        //         ->select('temp.*','admin.name as uname')
        //         ->get();
        //     }
        // }

        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $post = DB::table('whatsappcamptemplates as temp')
            ->leftJoin('admins as admin','temp.staff_id','=','admin.id')
            ->leftJoin('admins as admin2','temp.careoff_id','=','admin2.id')
            ->select('temp.*','admin.name as uname','admin2.name as uname2')
            ->get();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_whatsapp_template == 1){
                $post = DB::table('whatsappcamptemplates as temp')
                ->leftJoin('admins as admin','temp.staff_id','=','admin.id')
                ->leftJoin('admins as admin2','temp.careoff_id','=','admin2.id')
                ->select('temp.*','admin.name as uname','admin2.name as uname2')
                ->get();
            }else{
                $post = DB::table('whatsappcamptemplates as temp')
                ->leftJoin('admins as admin','temp.staff_id','=','admin.id')
                ->leftJoin('admins as admin2','temp.careoff_id','=','admin2.id')
                // ->where('temp.staff_id','=',Auth::guard('admin')->user()->id)
                ->Where('temp.careoff_id','=',Auth::guard('admin')->user()->id)
                ->select('temp.*','admin.name as uname','admin2.name as uname2')
                ->get();
            }
        }


        $data['data'] = $post;
        return response()->json($data);
    }

    public function wtempGetStatus(Request $request) {
        $post = Whatsappcamptemplate::find($request->id);
        return response()->json($post);
    }

    public function wtempGetStatusUpdate(Request $request) {
        $post = Whatsappcamptemplate::find($request->tempchstID);
        $post->status = $request->status;
        $post->save();
        return redirect()->back()->with('success','status has been changed!');
    }

    public function wtemplateStore(Request $request){
        $basepathstatus = Basepathstatus::first();
        $temp_file = '';

        if ($request->has('photo')) {
            $file = $request->file('photo');
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $new_file = str_replace(' ', '_', $filename) . '.' . $extension;
            $uploadPath = $basepathstatus->base_path_status == 1
                ? base_path('/public/admin/assets/images/template')
                : base_path('/public_html/admin/assets/images/template');

            $file->move($uploadPath, $new_file);
            $temp_file = $new_file;
        }

        // $post = new Templatecampaign();
        $post = new Whatsappcamptemplate();
        $post->audience = $request->audience;
        $post->template_name = $request->template_name;
        // $post->subject_name = $request->subject_name;
        $post->msg_whatsapp = $request->msg_whatsapp;
        $post->msg_whatsapp_ar = $request->msg_whatsapp_ar;
        $post->file = $temp_file;
        $post->public = $request->public == 1 ? true : false;
        $post->staff_id = Auth::guard('admin')->user()->id;
        $post->careoff_id = $request->careoff_id;
        $post->save();

        return redirect()->back()->with('success','Template created!');
    }

    public function wtemplateEdit(Request $request){
       $post = Whatsappcamptemplate::find($request->id);


       return response()->json($post);
    }

    public function wtemplateUpdate(Request $request){
        // $post = Templatecampaign::find($request->edit_id);
        $post = Whatsappcamptemplate::find($request->edit_id);
        $basepathstatus = Basepathstatus::first();



        if ($request->has('photo')) {
            $file = $request->file('photo');
            $name = $file->getClientOriginalName();
            // remove space from image
            $filename_ren = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ","_",$filename_ren);
            $new_file = $repspfilename.'.'.$fileext_ren;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/template',$new_file);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/template',$new_file);
            }

            $temp_file = $new_file;
        } else {
            $temp_file = $post->file;
        }

        $post->template_name = $request->template_name;
        $post->audience = $request->audience;
        // $post->subject_name = $request->subject_name;
        $post->msg_whatsapp = $request->msg_whatsapp;
        $post->msg_whatsapp_ar = $request->msg_whatsapp_ar;

        $post->file = $temp_file;
        if ($request->public == 1) {
            $post->public = true;
        } else {
            $post->public = false;
        }
        // $post->template_for = "2";
        // $post->staff_id = Auth::guard('admin')->user()->id;
        $post->careoff_id = $request->careoff_id;
        $post->save();

        return redirect()->back()->with('success','Template updated!');
    }

    public function wtemplateDelete(Request $request){
        $post = Whatsappcamptemplate::find($request->proff_ids);
        $post->delete();

        return redirect()->back()->with('success','Template deleted!');
    }

    public function mwtemplateList(Request $request){
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        $careoff = Admin::where('status',1)->orderBy('name')->get();
        $metapaiLists = Metawhatsappapi::orderBy('created_at')->where('status',1)->get();
        $whatsupchaturlusers = Whatsappchaturl::with('staff:id,name')->get();
        // dd($whatsupchaturlusers);
        return view('admin.whatsapp.metatemplate.index',['perm' => $permission,'careoffs' => $careoff,'metaAPILists' => $metapaiLists,'whatsupchaturlusers' => $whatsupchaturlusers]);
    }

   
    public function getTemplateVariables(Request $request)
    {
        $key = $request->template_for;

        $configValue = config("constants.template_for_map.$key");

        if (!$configValue) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid template_for key'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | If qamarhire (array mapping, not table)
        |--------------------------------------------------------------------------
        */
        if (is_array($configValue)) {

            $tableFields = array_keys($configValue);

            $count = count($tableFields);
            if ($count > 12) {
                $count = 12; // Meta max 12
            }

            $metaFields = [];
            for ($i = 1; $i <= $count; $i++) {
                $metaFields[] = "field_" . $i;
            }

            return response()->json([
                'status'        => true,
                'table'         => $key,
                'table_fields'  => $tableFields,
                'meta_fields'   => $metaFields,
                'fields_count'  => $count
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Normal Table Flow (old logic)
        |--------------------------------------------------------------------------
        */
        $table = $configValue;

        if (!Schema::hasTable($table)) {
            return response()->json([
                'status' => false,
                'message' => "Table '$table' does not exist"
            ]);
        }

        $tableFields = Schema::getColumnListing($table);

        if ($table === 'leads') {
            $tableFields = array_unique(array_merge($tableFields, [
                'country_as_per_location',
                'state_as_per_location',
                'city_as_per_location',
                'license'
            ]));
        }
        
        $count = count($tableFields);
        if ($count > 12) {
            $count = 12;
        }

        $metaFields = [];
        for ($i = 1; $i <= $count; $i++) {
            $metaFields[] = "field_" . $i;
        }

        return response()->json([
            'status'        => true,
            'table'         => $table,
            'table_fields'  => $tableFields,
            'meta_fields'   => $metaFields,
            'fields_count'  => $count
        ]);
    }

    public function mwtemplateListJson(Request $request){
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $post = DB::table('metawhatsapptemplates as metatamp')
            ->leftjoin('admins as admin','admin.id','=','metatamp.admin_id')
            ->leftJoin('admins as admin2','admin2.id','=','metatamp.careoff_id')
            ->leftJoin('metawhatsappapis as metawhatsappapi','metawhatsappapi.id','=','metatamp.metaapi_id')
            ->select('metatamp.*','admin.name as uname','metawhatsappapi.api_name as apiname','admin2.name as uname2')
            ->orderBy('metatamp.created_at','DESC')
            ->get();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){

            if ($permission->view_meta_whatsapp_template == 1) {
                $post = DB::table('metawhatsapptemplates as metatamp')
                ->leftjoin('admins as admin','admin.id','=','metatamp.admin_id')
                ->leftJoin('admins as admin2','admin2.id','=','metatamp.careoff_id')
                ->leftJoin('metawhatsappapis as metawhatsappapi','metawhatsappapi.id','=','metatamp.metaapi_id')
                ->select('metatamp.*','admin.name as uname','metawhatsappapi.api_name as apiname','admin2.name as uname2')
                ->orderBy('metatamp.created_at','DESC')
                ->get();
            } else {
                $post = DB::table('metawhatsapptemplates as metatamp')
                ->leftjoin('admins as admin','admin.id','=','metatamp.admin_id')
                ->leftJoin('admins as admin2','admin2.id','=','metatamp.careoff_id')
                ->leftJoin('metawhatsappapis as metawhatsappapi','metawhatsappapi.id','=','metatamp.metaapi_id')
                ->select('metatamp.*','admin.name as uname','metawhatsappapi.api_name as apiname','admin2.name as uname2')
                ->where('metatamp.admin_id','=',Auth::guard('admin')->user()->id)
                ->orderBy('metatamp.created_at','DESC')
                ->get();
            }

        }

        $data['data'] = $post;

        return response()->json($data);
    }


    public function mwtemplateStore(Request $request){

        // dd($request->all());
        $post = new Metawhatsapptemplate();
      
        $basepathstatus = Basepathstatus::first();
        if ($request->has('photo')) {
            $file = $request->file('photo');
            $name = $file->getClientOriginalName();
            $filename_ren = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ","_",$filename_ren);
            $new_file = $repspfilename.'.'.$fileext_ren;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/template',$new_file);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/template',$new_file);
            }

            $temp_file = $new_file;
        } else {
            $temp_file = '';
        }

        $post->template_name = $request->template_name;
       
        $post->whatsapp_file = $temp_file;
        $post->whatsapp_message = $request->msg_whatsapp;
        $post->msg_whatsapp_ar = $request->msg_whatsapp_ar ?? null;
        $post->language_code = $request->language_code ?? 'en';
        
        if ($request->public == 1) {
            $post->public = true;
        } else {
            $post->public = false;
        }

        $template_for =  $request->template_for;
        $field_var = 'field_var_'.$template_for;
        $assign_var = 'assign_var_'.$template_for;

        if(isset($request->$field_var) && isset($request->$assign_var)){
            $post->field_variable = implode(",",$request->$field_var);
            $post->assign_variable = implode(",",$request->$assign_var);
            $post->meta_field_var = implode(",",$request->$field_var);
            $post->meta_assign_ar = implode(",",$request->$assign_var);
        }

        if(isset($request->field_variable) && isset($request->assign_variable)){
            $post->field_variable = implode(",",$request->field_variable);
            $post->assign_variable = implode(",",$request->assign_variable);
            $post->meta_field_var = implode(",",$request->field_variable);
            $post->meta_assign_ar = implode(",",$request->assign_variable);
        }


        if($request->has('careoff_id_static') && $request->careoff_id_static != '' && is_array($request->careoff_id_static)){
            $post->careoff_id_static = implode(",",$request->careoff_id_static);
        }

        if($request->has('careoff_field_static') && $request->careoff_field_static != '' && is_array($request->careoff_field_static)){
            $post->careoff_field_static = implode(",",$request->careoff_field_static);
        }

        if($request->has('whatsup_chat_careoff_id_static') && $request->whatsup_chat_careoff_id_static != '' && is_array($request->whatsup_chat_careoff_id_static)){
            $post->whatsup_chat_careoff_id_static = implode(",",$request->whatsup_chat_careoff_id_static);
        }

        if($request->has('whatsup_chat_url_static') && $request->whatsup_chat_url_static != '' && is_array($request->whatsup_chat_url_static)){
            $post->whatsup_chat_url_static = implode(",",$request->whatsup_chat_url_static);
        }

        $post->template_for = $request->template_for;
        $post->template_used_for = $request->template_used_for;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->meta_url_type = $request->meta_url_type;
        $post->static_url = $request->static_url;
        $post->template_type = $request->template_type;
        $post->document_name = $request->document_name;
        $post->careoff_id = $request->careoff_id;
        $post->metaapi_id = $request->metaapi_id;


        $post->save();
        return redirect()->back()->with('success','Template Created!');
    }

    public function duplicateTemplate(Request $request)
    {
        $template = Metawhatsapptemplate::find($request->id);
    
        if (!$template) {
            return response()->json([
                'status' => false,
                'message' => 'Template not found'
            ]);
        }
    
        // Clone record
        $newTemplate = $template->replicate();
    
        // Change template name
        $newTemplate->template_name = $template->template_name . ' (Copy)';
    
        // Set new creator
        $newTemplate->admin_id = Auth::guard('admin')->user()->id;
    
        // Optional: reset metaapi_id if needed
        // $newTemplate->metaapi_id = null;
    
        $newTemplate->save();
    
        return response()->json([
            'status' => true,
            'message' => 'Template duplicated successfully'
        ]);
    }

    public function mwtemplateStoreBack(Request $request){

        $post = new Metawhatsapptemplate();

        $basepathstatus = Basepathstatus::first();
        if ($request->has('photo')) {
            $file = $request->file('photo');
            $name = $file->getClientOriginalName();
            // remove space from image
            $filename_ren = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ","_",$filename_ren);
            $new_file = $repspfilename.'.'.$fileext_ren;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/template',$new_file);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/template',$new_file);
            }

            $temp_file = $new_file;
        } else {
            $temp_file = '';
        }

        $post->template_name = $request->template_name;
        $post->field_variable = $request->field_variable;
        $post->assign_variable = $request->assign_variable;
        $post->whatsapp_file = $temp_file;
        $post->whatsapp_message = $request->msg_whatsapp;
        $post->msg_whatsapp_ar = $request->msg_whatsapp_ar ?? null;
        if ($request->public == 1) {
            $post->public = true;
        } else {
            $post->public = false;
        }

       
        $post->meta_field_var = implode(",",$request->field_variable);
        $post->meta_assign_ar = implode(",",$request->assign_variable);
      
        $post->template_for = $request->template_for;
        $post->template_used_for = $request->template_used_for;

        if ($request->template_for == 'contactp') {
            if ($request->field_var_contactp != '') {
                $post->meta_field_var = implode(",",$request->field_var_contactp);
            }

            if ($request->assign_var_contactp != '') {
                $post->meta_assign_ar =implode(",",$request->assign_var_contactp);
            }
        }
        if ($request->template_for == 'allcontact') {
            if ($request->field_var_allcontact != '') {
                $post->meta_field_var = implode(",",$request->field_var_allcontact);
            }

            if ($request->assign_var_allcontact != '') {
                $post->meta_assign_ar =implode(",",$request->assign_var_allcontact);
            }
        }

        if ($request->template_for == 'todo') {
            if ($request->field_var_todo != '') {
                $post->meta_field_var = implode(",",$request->field_var_todo);
            }

            if ($request->assign_var_todo != '') {
                $post->meta_assign_ar =implode(",",$request->assign_var_todo);
            }
        }

        if ($request->template_for == 'employer') {
            if ($request->field_var_employer != '') {
                $post->meta_field_var = implode(",",$request->field_var_employer);
            }

            if ($request->assign_var_employer != '') {
                $post->meta_assign_ar =implode(",",$request->assign_var_employer);
            }
        }

        if ($request->template_for == 'leads_employer') {
            if ($request->field_var_leads_employer != '') {
                $post->meta_field_var = implode(",",$request->field_var_leads_employer);
            }

            if ($request->assign_var_leads_employer != '') {
                $post->meta_assign_ar =implode(",",$request->assign_var_leads_employer);
            }
        }

        if ($request->template_for == 'leads_candidate') {
            if ($request->field_var_leads_candidate != '') {
                $post->meta_field_var = implode(",",$request->field_var_leads_candidate);
            }

            if ($request->assign_var_leads_candidate != '') {
                $post->meta_assign_ar =implode(",",$request->assign_var_leads_candidate);
            }
        }

        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->meta_url_type = $request->meta_url_type;
        $post->static_url = $request->static_url;
        $post->template_type = $request->template_type;
        $post->document_name = $request->document_name;
        $post->careoff_id = $request->careoff_id;
        $post->metaapi_id = $request->metaapi_id;
        $post->save();

        return redirect()->back()->with('success','Template Created!');
    }

    public function mwtemplateEdit(Request $request){
        $post = Metawhatsapptemplate::find($request->id);

        return response()->json($post);
    }

    public function mwtemplateUpdate(Request $request){

        $post = Metawhatsapptemplate::find($request->edit_id);

        $basepathstatus = Basepathstatus::first();
        if ($request->has('photo')) {
            $file = $request->file('photo');
            $name = $file->getClientOriginalName();
            // remove space from image
            $filename_ren = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ","_",$filename_ren);
            $new_file = $repspfilename.'.'.$fileext_ren;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/template',$new_file);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/template',$new_file);
            }

            $temp_file = $new_file;
        } else {
            $temp_file = $post->whatsapp_file;
        }

        $post->template_name = $request->template_name;
        $post->template_for = $request->template_for;
        $post->whatsapp_file = $temp_file;
        $post->whatsapp_message = $request->msg_whatsapp;
        $post->msg_whatsapp_ar = $request->msg_whatsapp_ar;
        $post->language_code = $request->language_code ?? 'en';

        if ($request->public == 1) {
            $post->public = true;
        } else {
            $post->public = false;
        }

        $template_for =  $request->template_for;
        $field_var = 'field_var_'.$template_for;
        $assign_var = 'assign_var_'.$template_for;

        if(isset($request->$field_var) && isset($request->$assign_var)){
            $post->field_variable = implode(",",$request->$field_var);
            $post->assign_variable = implode(",",$request->$assign_var);
            $post->meta_field_var = implode(",",$request->$field_var);
            $post->meta_assign_ar = implode(",",$request->$assign_var);
        }

        if(isset($request->field_variable) && isset($request->assign_variable)){
            $post->field_variable = implode(",",$request->field_variable);
            $post->assign_variable = implode(",",$request->assign_variable);
            $post->meta_field_var = implode(",",$request->field_variable);
            $post->meta_assign_ar = implode(",",$request->assign_variable);
        }

        if($request->has('careoff_id_static') && $request->careoff_id_static != '' && is_array($request->careoff_id_static)){
            $post->careoff_id_static = implode(",",$request->careoff_id_static);
        }

        if($request->has('careoff_field_static') && $request->careoff_field_static != '' && is_array($request->careoff_field_static)){
            $post->careoff_field_static = implode(",",$request->careoff_field_static);
        }


        if($request->has('whatsup_chat_careoff_id_static') && $request->whatsup_chat_careoff_id_static != '' && is_array($request->whatsup_chat_careoff_id_static)){
            $post->whatsup_chat_careoff_id_static = implode(",",$request->whatsup_chat_careoff_id_static);
        }

        if($request->has('whatsup_chat_url_static') && $request->whatsup_chat_url_static != '' && is_array($request->whatsup_chat_url_static)){
            $post->whatsup_chat_url_static = implode(",",$request->whatsup_chat_url_static);
        }


        $post->meta_url_type = $request->meta_url_type;
        $post->static_url = $request->static_url;
        $post->template_type = $request->template_type;
        $post->document_name = $request->document_name;
        $post->careoff_id = $request->careoff_id;
        $post->metaapi_id = $request->metaapi_id;
        $post->template_used_for = $request->template_used_for;

        $post->save();

        return redirect()->back()->with('success','Template Updated!');
    }

    public function metatemplateStatus(Request $request){
        $post = Metawhatsapptemplate::find($request->id);

        return response()->json($post);
    }

    public function metatemplateStatusUpdate(Request $request){
        $post = Metawhatsapptemplate::find($request->tempchstID);
        $post->status = $request->status;
        $post->save();

        return redirect()->back()->with('success','Status Updated!');
    }

    public function metatemplatePublic(Request $request){
        $post = Metawhatsapptemplate::find($request->id);

        return response()->json($post);
    }

    public function metatemplatePublicUpdate(Request $request){
        $post = Metawhatsapptemplate::find($request->temppubID);
        $post->public = $request->publish_st;
        $post->save();

        return redirect()->back()->with('success','Publish Updated!');
    }

    public function metatemplateGet(Request $request){
        $post = Metawhatsapptemplate::find($request->id);

        return response()->json($post);
    }

    public function whatsappreportshow(Request $request,$id){
        // $posts = Normalwhatsappcampaignresponse::where('campaignlist_id','=',$id)->get();
        $posts = Normalwhatsappcampaignresponse::with('campaignlist')->where('campaignlist_id','=',$id)->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();


        return view('admin.whatsapp.campaign.report.show',compact('posts'));
    }

    // public function whatsappchatredirecturllist(Request $request) {
        
    //     $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
    //     $posts = Whatsappchaturl::with('staff')->orderBy('id','DESC')->paginate(10);
    //     $staffs = Admin::where('status',1)->orderBy('name')->get();
    //     return view('admin.whatsappchaturl.index',compact('posts','staffs','permission'));
    // }

    public function whatsappchatredirecturllist(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $permission = Adminpermission::where('staff_id', $admin->id)->first();

        // ❌ Authorization check
        if (
            $admin->user_type != 1 &&
            (
                !$permission ||
                ($permission->full_access != 1 && $permission->whatsapp_url != 1)
            )
        ) {
            abort(403, 'Unauthorized access');
        }

        // Base query
        $query = Whatsappchaturl::with('staff')->orderBy('id', 'DESC');

        // ✅ Super admin OR full access → see all
        if ($admin->user_type == 1 || ($permission && $permission->full_access == 1)) {

            $posts = $query->paginate(10);

        }
        // ✅ Staff with limited permissions
        else {
            // Can view all WhatsApp URLs
            if ($permission && $permission->view_whatsapp_url == 1) {
                $posts = $query->paginate(10);
            }
            // Can view only own URLs
            else {
                $posts = $query
                    ->where('staff_id', $admin->id)
                    ->paginate(10);
            }
        }

        $staffs = Admin::where('status', 1)
            ->orderBy('name')
            ->get();

        return view(
            'admin.whatsappchaturl.index',
            compact('posts', 'staffs', 'permission')
        );
    }


    public function whatsappredirecturlgetStaff(Request $request){
        $post = Admin::find($request->id);

        return response()->json($post);
    }

    public function whatsappchatredirecturlStr(Request $request) {
        $checkExist = Whatsappchaturl::where('staff_id','=',$request->staff_id)->first();

        $random_string = Str::random(10);
        $chatredirecturl = url('/newchat/'.$random_string.'/'.$request->staff_id);
        if ($checkExist) {
            $checkExist->chat_url = $chatredirecturl;
            $checkExist->status = true;
            $checkExist->save();
        } else {
            $post = new Whatsappchaturl();
            $post->chat_url = $chatredirecturl;
            $post->staff_id = $request->staff_id;
            $post->save();
        }


        // Update Whatsapp Chat Number based on Number
        if($request->working_mobile_no != ''){
            $staff = Admin::find($request->staff_id);

            $staff->work_number = $request->working_mobile_no;
            $staff->calling_number = $request->calling_number;
            $staff->save();
        }

        return redirect()->back()->with('success','Chaturl Generated!');

    }


    public function whatsappchatredirecturlEdit(Request $request){
        $post = Whatsappchaturl::find($request->id);

        return response()->json($post);
    }


    public function whatsappchatredirecturlUpdt(Request $request){
        $post = Whatsappchaturl::find($request->edit_id);

        $random_string = Str::random(10);
        $chatredirecturl = url('/newchat/'.$random_string.'/'.$request->staff_id);
        if ($post->staff_id == $request->staff_id) {
            $post->chat_url = $chatredirecturl;
            $post->status = true;
            $post->save();
        }else{
            $checkExist = Whatsappchaturl::where('staff_id','=',$request->staff_id)->first();

            if ($checkExist) {
                $checkExist->chat_url = $chatredirecturl;
                $checkExist->status = true;
                $checkExist->save();
            } else {
                $post = new Whatsappchaturl();
                $post->chat_url = $chatredirecturl;
                $post->staff_id = $request->staff_id;
                $post->save();
            }

        }

        // Update Whatsapp Chat Number based on Number
        if($request->working_mobile_no != ''){
            $staff = Admin::find($request->staff_id);

            $staff->work_number = $request->working_mobile_no;
            $staff->calling_number = $request->calling_number;
            $staff->save();
        }

        return redirect()->back()->with('success','Chaturl Updated');
    }

    public function whatsappchatredirecturlDelete(Request $request){
        $post = Whatsappchaturl::find($request->edit_id);

        $post->delete();

        return redirect()->back()->with('success','Dynamic Chat URL deleted!');
    }

    public function metatemplateDelete(Request $request) {
        $post = Metawhatsapptemplate::find($request->proff_ids);

        $post->delete();

        return redirect()->back()->with('success','Template Deleted!');
    }

}
