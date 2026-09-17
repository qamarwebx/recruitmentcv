<?php

namespace App\Console\Commands;

use App\Jobs\AllcontactSendMetaJob;
use App\Jobs\ContactplusSendMetaJob;
use App\Models\Allcontact;
use App\Models\City;
use App\Models\Contactplus;
use App\Models\Contactsendtag;
use App\Models\Country;
use App\Models\Metawhatsappapi;
use App\Models\Metawhatsappcampaign;
use App\Models\Metawhatsapptemplate;
use App\Models\Whatsappchaturl;
use Illuminate\Console\Command;
use Str;
use Carbon\Carbon;


class WhatsappScheduledMetaCampaignBack extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:whatsappcampaignmeta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // $datetime = date('Y-m-d h:i');

        // $getCampaignLists = Metawhatsappcampaign::where('sch_type','=','Scheduled')->where('date_and_time','=',$datetime)->get();

        $now = Carbon::now();

        // ---------------------------------------------------
        // Fetch scheduled LEADS campaigns
        // ---------------------------------------------------
        $getCampaignLists = Metawhatsappcampaign::where('sch_type', 'Scheduled')
            ->whereBetween('date_and_time', [
                $now->copy()->startOfMinute(),
                $now->copy()->endOfMinute()
            ])
            ->get();

        $this->info($getCampaignLists->count());

        if (count($getCampaignLists) > 0) {
            foreach ($getCampaignLists as $post) {

                // Get Meta API
                $metaAPI = Metawhatsappapi::find($post->metaapi_id);

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

                // Meta Template
                $metaTemplate = Metawhatsapptemplate::find($post->metatemp_id);

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

                $field_variable = explode(",",$metaTemplate->meta_field_var);
                $assgn_variable = explode(",",$metaTemplate->meta_assign_ar);

                $metatemplatedata = [
                    'header_file_path' => $header_file_path,
                    'field_variable' => $field_variable,
                    'assgn_variable' => $assgn_variable,
                    'template_name' => $metaTemplate->template_name
                ];

                if ($post->audience == 'contactp') {
                    $groupID = $post->group_id;
                    $business_type = $post->business_type_contact;
                    $careoff_id = $post->careoff_id;
                    $subscribe = $post->subscribe;
                    $country_code = $post->country_code;

                    $contactps = Contactplus::where(
                        function($query) use($subscribe,$groupID,$business_type,$careoff_id,$country_code){
                            if($groupID != ''){
                                $query->wherein('group_id',explode(",",$groupID));
                            }

                            if($business_type != ''){
                                $query->wherein('businesstype_id', explode(",",$business_type));
                            }

                            if ($careoff_id != '') {
                                $query->wherein('careoff_id',explode(",",$careoff_id));
                            }

                            if($subscribe != ''){
                                $query->whereIn('subscribe',explode(",",$subscribe));
                            }
                        }
                    )->get();

                    if ($contactps->count() > 0) {

                        $delaySeconds = 0;
                        foreach ($contactps as $contactp) {
                    
                            try {

                                // 🔥 Collect numbers (same as controller)
                                $numbers = [];
                            
                                if ($post->contact_type == 1) {
                                    if (!empty($contactp->owner_contact)) $numbers[] = $contactp->owner_contact;
                                    if (!empty($contactp->prim_contact)) $numbers[] = $contactp->prim_contact;
                                    if (!empty($contactp->sec_contact)) $numbers[] = $contactp->sec_contact;
                            
                                } elseif ($post->contact_type == 2) {
                                    if (!empty($contactp->owner_contact)) $numbers[] = $contactp->owner_contact;
                            
                                } elseif ($post->contact_type == 3) {
                                    if (!empty($contactp->prim_contact)) $numbers[] = $contactp->prim_contact;
                            
                                } elseif ($post->contact_type == 4) {
                                    if (!empty($contactp->sec_contact)) $numbers[] = $contactp->sec_contact;
                                }
                            
                                // ✅ FIX: convert array to string for CLI
                                $this->info('Numbers: ' . implode(',', $numbers));
                            
                                // ✅ Also log properly
                                \Log::channel('contactplus')->info('Numbers Prepared', [
                                    'contact_id' => $contactp->id,
                                    'numbers'    => $numbers
                                ]);
                            
                                $numbers = array_values(array_unique(array_filter($numbers)));
                            
                                if (empty($numbers)) {
                                    continue;
                                }
                            
                                foreach ($numbers as $phone) {
                            
                                    // 🔥 Delay logic (same as controller)
                                    if ($post->delay_frequency === 'Delay 10 to 60 Seconds') {
                                        $rand = rand(10, 60);
                                    } elseif ($post->delay_frequency === 'Delay 30 sec to 2 min') {
                                        $rand = rand(30, 120);
                                    } else {
                                        $rand = 0;
                                    }
                            
                                    $delaySeconds += ($rand + 10);
                            
                                    // 🔁 Generate data per phone
                                    $random_string = Str::random(32);
                                    $unsubscribe_url = 'whatsapp/unsubscribe/request/'.$random_string.'/'.$contactp->id;
                            
                                    $staffDet = Whatsappchaturl::where('staff_id','=',$contactp->careoff_id)
                                                ->where('status',true)
                                                ->first();
                            
                                    $dynamichaturl = $staffDet ? $staffDet->chat_url : "https://wa.me";
                            
                                    $countryName = optional(Country::find($contactp->country_id))->name ?? '';
                                    $cityName    = optional(City::find($contactp->city_id))->name ?? '';
                            
                                    $requestData = [
                                        'contact_type' => $post->contact_type,
                                        'random_string' => $random_string,
                                        'unsubscribe_url' => $unsubscribe_url,
                                        'dynamichaturl' => $dynamichaturl,
                                        'countryName' => $countryName,
                                        'cityName' => $cityName,
                                        'phone' => $phone
                                    ];
                            
                                    \Log::channel('contactplus')->info('Scheduled Dispatch', [
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
                                        $post
                                    )
                                    ->delay(now()->addSeconds($delaySeconds))
                                    ->onQueue('default');
                                }
                            
                                // ✅ TAG UPDATE (unchanged)
                                $countcontactTag = Contactsendtag::where('contact_id','=',$contactp->id)->count();
                            
                                $contactTag = new Contactsendtag();
                                $contactTag->contact_id = $contactp->id;
                                $contactTag->send_tag = $countcontactTag + 1;
                                $contactTag->send_date = date('Y-m-d');
                                $contactTag->save();
                            
                                $updContactSendTag = Contactplus::find($contactp->id);
                                $updContactSendTag->send_tag = "Send ".$contactTag->send_tag;
                                $updContactSendTag->send_date = $contactTag->send_date.",".$contactp->send_date;
                                $updContactSendTag->save();
                            
                            } catch (\Throwable $e) {
                            
                                // ✅ CLI output
                                $this->error('Error: ' . $e->getMessage());
                            
                                // ✅ Log
                                \Log::channel('contactplus')->error('Scheduled contact failed', [
                                    'contact_id' => $contactp->id,
                                    'error'      => $e->getMessage()
                                ]);
                            
                                continue;
                            }
                        }
                    
                        // ✅ Campaign update
                        $updateCampaign = Metawhatsappcampaign::find($post->id);
                        $updateCampaign->message_status = "success";
                        $updateCampaign->message_text = "Message processed for WhatsApp contact";
                        $updateCampaign->save();
                    }

                }
                elseif ($post->audience == 'allcontact') {

                    $groupID = $post->group_id;
                    $careoff_id = $post->careoff_id;
                    $country_id = $post->country_id;
                    $country_code = $post->country_code;
                    $subscribe = $post->subscribe;
                    $lead_type = $post->business_type_contact;
                
                    $allcontacts = Allcontact::where(function($query) use ($groupID,$careoff_id,$country_id,$subscribe,$country_code,$lead_type){
                
                        if($groupID != ''){
                            $query->whereIn('group_id', explode(",",$groupID));
                        }
                
                        if ($careoff_id != '') {
                            $query->whereIn('careoff_id', explode(",",$careoff_id));
                        }
                
                        if ($country_id != '') {
                            $query->whereIn('country_id', explode(",",$country_id));
                        }
                
                        if ($subscribe != '') {
                            $query->whereIn('optinout', explode(",",$subscribe));
                        }
                
                        if ($country_code != '') {
                            $query->where(function($q) use($country_code){
                                foreach (explode(",",$country_code) as $code) {
                                    $q->orWhere('primary_no_wsp', 'like', $code . '%')
                                      ->orWhere('secondary_no_wsp', 'like', $code . '%')
                                      ->orWhere('mobile_no1_wsp', 'like', $code . '%');
                                }
                            });
                        }
                
                        if ($lead_type != '') {
                            $query->whereIn('lead_type', explode(",",$lead_type));
                        }
                
                    })->get();
                
                    if ($allcontacts->count() > 0) {
                
                        $delaySeconds = 0;
                
                        foreach ($allcontacts as $allcontact) {
                
                            try {
                
                                // 🔥 CONTACT TYPE LOGIC (ADDED)
                                $numbers = [];
                
                                if ($post->contact_type == 1) {
                                    if (!empty($allcontact->mobile_no1_wsp)) $numbers[] = $allcontact->mobile_no1_wsp;
                                    if (!empty($allcontact->primary_no_wsp)) $numbers[] = $allcontact->primary_no_wsp;
                                    if (!empty($allcontact->secondary_no_wsp)) $numbers[] = $allcontact->secondary_no_wsp;
                
                                } elseif ($post->contact_type == 2) {
                                    if (!empty($allcontact->mobile_no1_wsp)) $numbers[] = $allcontact->mobile_no1_wsp;
                
                                } elseif ($post->contact_type == 3) {
                                    if (!empty($allcontact->primary_no_wsp)) $numbers[] = $allcontact->primary_no_wsp;
                
                                } elseif ($post->contact_type == 4) {
                                    if (!empty($allcontact->secondary_no_wsp)) $numbers[] = $allcontact->secondary_no_wsp;
                                }
                
                                $numbers = array_values(array_unique(array_filter($numbers)));
                
                                if (empty($numbers)) {
                                    continue;
                                }
                
                                foreach ($numbers as $phone) {
                
                                    // 🔥 Delay logic
                                    if ($post->delay_frequency === 'Delay 10 to 60 Seconds') {
                                        $rand = rand(10, 60);
                                    } elseif ($post->delay_frequency === 'Delay 30 sec to 2 min') {
                                        $rand = rand(30, 120);
                                    } else {
                                        $rand = 0;
                                    }
                
                                    $delaySeconds += ($rand + 20);
                
                                    \Log::channel('AllcontactSendMetaJob')->info('Scheduled Dispatch', [
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
                                        $post
                                    )
                                    ->delay(now()->addSeconds($delaySeconds))
                                    ->onQueue('default');
                                }
                
                            } catch (\Throwable $e) {
                
                                \Log::channel('AllcontactSendMetaJob')->error('Allcontact failed', [
                                    'contact_id' => $allcontact->id,
                                    'error'      => $e->getMessage()
                                ]);
                
                                continue;
                            }
                        }
                
                        // ✅ Campaign update
                        $updateCampaign = Metawhatsappcampaign::find($post->id);
                        $updateCampaign->message_status = "success";
                        $updateCampaign->message_text = "Message processed for WhatsApp contact";
                        $updateCampaign->save();
                    }
                }

            }
        }


        return Command::SUCCESS;
    }
}
