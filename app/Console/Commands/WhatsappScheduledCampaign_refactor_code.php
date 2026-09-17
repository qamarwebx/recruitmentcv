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

class WhatsappScheduledCampaign_refactor_code extends Command
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
    protected $description = 'Handles scheduled WhatsApp campaigns';

    /**
     * Execute the console command.
     *
     * @return int
     */

    private $minimumInterval = 30;
    private $maximumInterval = 60;

    public function handle()
    {
        $currentDatetime = now()->format('Y-m-d H:i');

        Campaignlist::where('sch_type', 2)->where('date_and_time', $currentDatetime)->get()->each(function ($campaign) {
            $this->processCampaign($campaign);
        });

        return Command::SUCCESS;
    }

    /**
     * Process a campaign.
     *
     * @param Campaignlist $campaign
     * @return void
    */

    private function processCampaign($campaign)
    {
        Whatsappapi::whereIn('id', explode(',', $campaign->wapi_id_text))->get()->each(function ($api) use ($campaign) {
            $this->dispatchJobsByAudience($campaign, $api);
        });
    }

    /**
     * Dispatch jobs based on audience type.
     *
     * @param Campaignlist $campaign
     * @param Whatsappapi $api
     * @return void
    */

    private function dispatchJobsByAudience($campaign, $api)
    {
        switch ($campaign->audience) {
            case 'Contact+':
                $this->handleContactplus($campaign, $api);
                break;
            case 'Allcontact':
                $this->handleAllcontact($campaign, $api);
                break;
            case 'Associate':
                $this->handleAssociates($campaign, $api);
                break;
            case 'Client':
                $this->handleClients($campaign, $api);
                break;
            case 'Partner':
                $this->handlePartners($campaign, $api);
                break;
        }
    }

    /**
     * Handle Contact+ audience.
     *
     * @param Campaignlist $campaign
     * @param Whatsappapi $api
     * @return void
    */

    private function handleContactplus($campaign, $api)
    {
        $contacts = Contactplus::whereIn('status', explode(',', $campaign->contactp_status))->when($campaign->groupm, function ($query) use ($campaign) {
            $query->whereIn('group_id', explode(',', $campaign->groupm));
        })->get();

        $this->dispatchJobs($contacts, $campaign, $api, ContactplusMultiSendJob::class, ContactpSendJob::class);
    }

    /**
     * Handle Allcontact audience.
     *
     * @param Campaignlist $campaign
     * @param Whatsappapi $api
     * @return void
     */
    private function handleAllcontact($campaign, $api)
    {
        $contacts = Allcontact::whereIn('status', explode(',', $campaign->allcontact_status))
            ->when($campaign->groupmallc, function ($query) use ($campaign) {
                $query->whereIn('group_id', explode(',', $campaign->groupmallc));
            })
            ->get();

        $this->dispatchJobs($contacts, $campaign, $api, AllcontactMultSendJob::class, AllcontactSendJob::class);
    }

    /**
     * Handle Associates audience.
     *
     * @param Campaignlist $campaign
     * @param Whatsappapi $api
     * @return void
     */
    private function handleAssociates($campaign, $api)
    {
        $associates = Associates::whereIn('status', explode(',', $campaign->assoc_status))->get();

        $this->dispatchJobs($associates, $campaign, $api, AssocMultSendJob::class, AssocSendJob::class);
    }

    /**
     * Handle Client audience.
     *
     * @param Campaignlist $campaign
     * @param Whatsappapi $api
     * @return void
     */
    private function handleClients($campaign, $api)
    {
        $clients = User::whereIn('status', explode(',', $campaign->client_status))->get();

        $this->dispatchJobs($clients, $campaign, $api, ClientMultSendJob::class, ClientSendJob::class);
    }

    /**
     * Handle Partner audience.
     *
     * @param Campaignlist $campaign
     * @param Whatsappapi $api
     * @return void
     */
    private function handlePartners($campaign, $api)
    {
        $partners = Partner::whereIn('status', explode(',', $campaign->partner_status))->get();

        $this->dispatchJobs($partners, $campaign, $api, PartnerMultSendJob::class, PartnerSendJob::class);
    }

    /**
     * Dispatch jobs for given contacts.
     *
     * @param Collection $contacts
     * @param Campaignlist $campaign
     * @param Whatsappapi $api
     * @param string $multiSendJobClass
     * @param string $singleSendJobClass
     * @return void
    */

    private function dispatchJobs($contacts, $campaign, $api, $multiSendJobClass, $singleSendJobClass)
    {
        if ($contacts->isEmpty()) {
            return;
        }

        $currentDelay = 0;

        $templateIds = $campaign->wtemp === 'template_multiple' ? explode(',', $campaign->temp_id_text) : [];
        $templateCount = count($templateIds);

        $contacts->each(function($contact, $index) use($templateIds,$templateCount, $campaign, $api, &$currentDelay, $multiSendJobClass,$singleSendJobClass){
            $randomDelay = rand($this->minimumInterval, $this->maximumInterval);
            $currentDelay += $randomDelay;

            if ($templateCount > 0) {
                $templateId = $templateIds[$index % $templateCount];
                $template = Whatsappcamptemplate::find($templateId);
                $multiSendJobClass::dispatch($contact, $api, $campaign, $template)->delay(now()->addSeconds($currentDelay));
            } else {
                $singleSendJobClass::dispatch($contact, $api, $campaign)->delay(now()->addSeconds($currentDelay));
            }


        });

    }

}
