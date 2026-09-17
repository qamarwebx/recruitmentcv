<?php

namespace App\Console\Commands;

use App\Jobs\AllcontactSendJob;
use App\Jobs\AssocSendJob;
use App\Jobs\ClientSendJob;
use App\Jobs\ContactpSendJob;
use App\Jobs\PartnerSendJob;
use App\Models\Allcontact;
use App\Models\Associates;
use App\Models\Campaignlist;
use App\Models\Contactp;
use App\Models\Contactplus;
use App\Models\Partner;
use App\Models\User;
use App\Models\Whatsappapi;
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

                            $contactps = Contactplus::wherein('status',$contact_status)->where(function($query) use($group_id){
                                if ($group_id != '') {
                                    $query->wherein('group_id',explode(",",$group_id));
                                }
                            })->get();

                            foreach ($contactps as $contactp) {
                                // dispatch(new ContactpSendJob($contactp,$getAPI,$post))->onConnection('database')->onQueue('default');
                                $randomdelay = rand($minimumInterval, $maximumInterval);
                                $currentDelay += $randomdelay;
                                ContactpSendJob::dispatch($contactp,$getAPI,$post)->delay(now()->addSeconds($currentDelay));
                            }

                        }

                        if ($post->audience == 'Allcontact') {
                            $minimumInterval = 30; // 25 Seconds
                            $maximumInterval = 60; // 60 Seconds
                            $currentDelay = 0; // 0 Seconds

                            $allcontact_status = $post->allcontact_status;
                            $groupallc_id = $post->groupmallc;

                            $allcontacts = Allcontact::wherein('status',$allcontact_status)->where(function($query) use($groupallc_id){
                                if ($groupallc_id != '') {
                                    $query->wherein('group_id', explode(",",$groupallc_id));
                                }
                            })->get();

                            foreach ($allcontacts as $allcontact) {

                                $randomdelay = rand($minimumInterval, $maximumInterval);
                                $currentDelay += $randomdelay;

                                AllcontactSendJob::dispatch($allcontact,$getAPI,$post)->delay(now()->addSeconds($currentDelay));
                            }

                        }

                        if ($post->audience == 'Associate') {
                            $assocs = Associates::wherein('status',explode(",",$post->assoc_status))->get();
                            foreach ($assocs as $assoc) {
                                dispatch(new AssocSendJob($assoc,$getAPI,$post))->onQueue('default');
                            }
                        }

                        if ($post->audience == 'Client') {
                            $clients = User::wherein('status',explode(',',$post->client_status))->get();
                            foreach ($clients as $client2) {
                                dispatch(new ClientSendJob($client2,$getAPI,$post))->onQueue('default');
                            }
                        }

                        if ($post->audience == 'Partner') {
                            $partners = Partner::wherein('status',explode(',',$post->partner_status))->get();
                            foreach ($partners as $partner) {
                                dispatch(new PartnerSendJob($partner,$getAPI,$post));
                            }
                        }

                    }
                }

            }
        }

        return Command::SUCCESS;
    }
}
