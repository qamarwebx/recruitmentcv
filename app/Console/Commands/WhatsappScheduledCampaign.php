<?php

namespace App\Console\Commands;

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
use App\Models\Allcontact;
use App\Models\Associates;
use App\Models\Campaignlist;
use App\Models\Contactp;
use App\Models\Contactplus;
use App\Models\Partner;
use App\Models\User;
use App\Models\Whatsappapi;
use App\Models\Whatsappcamptemplate;
use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;

class WhatsappScheduledCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:whatsappcampaign';

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

        $datetime = date('Y-m-d h:i');

        $getCampaignLists = Campaignlist::where('sch_type','=',2)->where('date_and_time','=',$datetime)->get();

        if (isset($getCampaignLists)) {
            foreach ($getCampaignLists as $post) {
                // Get API details
                $getAPIs = Whatsappapi::wherein('id',explode(",",$post->wapi_id_text))->get();

                if (isset($getAPIs)) {
                    foreach ($getAPIs as $getAPI) {
                        if ($post->audience == 'Contact+') {
                            $minimumInterval = 30; // 25 Seconds
                            $maximumInterval = 60; // 60 Seconds
                            $currentDelay = 0; // 0 Seconds

                            $group_id = $post->groupm;
                            $contact_status = explode(",",$post->contactp_status);
                            $country_id = $post->country_id;
                            $city_id = $post->city_id;

                            $contactps = Contactplus::wherein('status',$contact_status)->where(function($query) use($group_id,$country_id,$city_id){
                                if ($group_id != '') {
                                    $query->wherein('group_id',explode(",",$group_id));
                                }

                                if ($country_id != '') {
                                    $query->whereIn('country_id', explode(",",$country_id));
                                }

                                if ($city_id != '') {
                                    $query->whereIn('city_id',explode(",",$city_id));
                                }

                            })->get();

                            $contact_count2 = count($contactps);

                            if ($contact_count2 > 0) {
                                if ($post->wtemp == 'template_multiple') {
                                    $template_id = explode(",",$post->temp_id_text);
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
                                        // dispatch(new ContactpSendJob($contactp,$getAPI,$post))->onConnection('database')->onQueue('default');
                                        $randomdelay = rand($minimumInterval, $maximumInterval);
                                        $currentDelay += $randomdelay;
                                        ContactpSendJob::dispatch($contactp,$getAPI,$post)->delay(now()->addSeconds($currentDelay));
                                    }
                                }
                            }








                        }

                        if ($post->audience == 'Allcontact') {
                            $minimumInterval = 30; // 25 Seconds
                            $maximumInterval = 60; // 60 Seconds
                            $currentDelay = 0; // 0 Seconds

                            $allcontact_status = $post->allcontact_status;
                            $groupallc_id = $post->groupmallc;
                            $country_id = $post->country_id;
                            $city_id = $post->city_id;

                            $allcontacts = Allcontact::wherein('status',$allcontact_status)->where(function($query) use($groupallc_id,$country_id,$city_id){
                                if ($groupallc_id != '') {
                                    $query->wherein('group_id', explode(",",$groupallc_id));
                                }

                                if ($country_id != '') {
                                    $query->wherein('country_id', explode(",",$country_id));
                                }

                                if ($city_id != '') {
                                    $query->whereIn('city_id',explode(",",$city_id));
                                }

                            })->get();

                            $contactCount = count($allcontacts);

                            if ($contactCount > 0) {
                                if ($post->wtemp == 'template_multiple') {
                                    $template_id = explode(",",$post->temp_id_text);
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



                        }

                        if ($post->audience == 'Associate') {

                            $minimumInterval = 30; // 25 Seconds
                            $maximumInterval = 60; // 60 Seconds
                            $currentDelay = 0; // 0 Seconds




                            $assocs = Associates::wherein('status',explode(",",$post->assoc_status))->get();

                            $assoc_count = count($assocs);

                            if ($assoc_count > 0) {
                                if ($post->wtemp == 'template_multiple') {
                                    $template_id = $post->temp_id;
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
                                        dispatch(new AssocSendJob($assoc,$getAPI,$post))->onQueue('default');
                                    }
                                }

                            }


                        }

                        if ($post->audience == 'Client') {

                            $minimumInterval = 30; // 25 Seconds
                            $maximumInterval = 60; // 60 Seconds
                            $currentDelay = 0; // 0 Seconds

                            $clients = User::wherein('status',explode(',',$post->client_status))->get();

                            $client_count = Count($clients);

                            if ($client_count > 0) {

                                if ($post->wtemp == 'template_multiple') {
                                    $template_id = $post->temp_id;
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

                                }else{
                                    foreach ($clients as $client2) {
                                        dispatch(new ClientSendJob($client2,$getAPI,$post))->onQueue('default');
                                    }
                                }


                            }



                        }

                        if ($post->audience == 'Partner') {
                            $minimumInterval = 30; // 25 Seconds
                            $maximumInterval = 60; // 60 Seconds
                            $currentDelay = 0; // 0 Seconds

                            $partners = Partner::wherein('status',explode(',',$post->partner_status))->get();

                            $partner_count = count($partners);

                            if ($partner_count > 0) {
                                if ($post->wtemp == 'template_multiple') {
                                    $template_id = $post->temp_id;
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
                                        dispatch(new PartnerSendJob($partner,$getAPI,$post));
                                    }
                                }

                            }


                        }

                    }
                }

            }
        }

        return Command::SUCCESS;
    }
}
