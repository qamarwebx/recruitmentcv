<?php

namespace App\Jobs;

use App\Models\City;
use App\Models\Country;
use App\Models\Normalwhatsappcampaignresponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Str;

class AllcontactSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $allcontact,$getAPI,$post;

    public function __construct($allcontact,$getAPI,$post)
    {
        $this->allcontact = $allcontact;
        $this->getAPI = $getAPI;
        $this->post = $post;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $apiEndpoint = $this->getAPI['api_url'];
        $instanceId = $this->getAPI['instance_id'];
        $accessToken = $this->getAPI['access_token'];

        $filename = $this->post['temp_file'];
        $fullPathUrl = $this->post['temp_file_url'];

        $countryName = $this->getEntityName(Country::find($this->allcontact['country_id']));
        $cityName = $this->getEntityName(City::find($this->allcontact['city_id']));

        $unsubscribeUrl = $this->generateUnsubscribeUrl();

        $placeholders = [
            "[Business Type]", "[Full Name]", "[Country]", "[City]", "[Email]",
            "[Phone0]", "[Email0]", "[Phone1]", "[Email1]", "[Phone2]", "[Email2]",
            "[Unsubscribe]", "[Company]", "[Membership]"
        ];

        $replacements = [
            $this->allcontact['lead_type'], $this->allcontact['full_name'], $countryName,
            $cityName, $this->allcontact['email'], $this->allcontact['phone0'],
            $this->allcontact['email0'], $this->allcontact['phone1'], $this->allcontact['email1'],
            $this->allcontact['phone2'], $this->allcontact['email2'], $unsubscribeUrl,
            $this->allcontact['company_name'], $this->allcontact['id']
        ];

        $finalMsgBody = str_replace($placeholders, $replacements, $this->post['whs_msg']);
        $finalMsgBodyAr = str_replace($placeholders, $replacements, $this->post['whs_msg_ar']);

        $contactTypes = explode(",", $this->post['allcontact_contact_type']);

        foreach ($contactTypes as $contactType) {
            $this->sendMessage($contactType, $filename, $fullPathUrl, $finalMsgBody, $finalMsgBodyAr, $apiEndpoint, $instanceId, $accessToken);
        }
    }

    private function getEntityName($entity)
    {
        return $entity ? $entity->name : "";
    }

    private function generateUnsubscribeUrl()
    {
        $randomStr = Str::random(32);
        return url("/whatsapp/unsubscribe/request/{$randomStr}/{$this->allcontact['id']}");
    }

    private function sendMessage($contactType, $filename, $fullPathUrl, $finalMsgBody, $finalMsgBodyAr, $apiEndpoint, $instanceId, $accessToken){
        $contactMethods = [
            'primary' => [
                'number' => $this->allcontact['primary_contact_no'],
                'dial_code' => $this->allcontact['primary_con_dial_code'],
                'types' => ['1', '2']
            ],
            'secondary' => [
                'number' => $this->allcontact['mobile_no'],
                'dial_code' => $this->allcontact['mobile_no_dial_code'],
                'types' => ['1', '3']
            ],
            'tertiary' => [
                'number' => $this->allcontact['phone0'],
                'dial_code' => $this->allcontact['phone0_dial_code'],
                'types' => ['1','4']
            ]
        ];

        foreach ($contactMethods as $method => $details) {
            if (in_array($contactType, $details['types']) && !empty($details['number'])) {
                $contactNumber = $this->formatContactNumber($details['dial_code'],$details['number']);

                $mediaType = !empty($filename) ? 'media' :'text';

                if (!empty($this->post['whs_msg'])) {
                    $this->processMessage($contactNumber, $filename, $fullPathUrl, $finalMsgBody, $mediaType, $apiEndpoint, $instanceId, $accessToken);
                }

                if (!empty($this->post['whs_msg_ar'])) {
                    $this->processMessage($contactNumber, $filename, $fullPathUrl, $finalMsgBodyAr, $mediaType, $apiEndpoint, $instanceId, $accessToken);
                }


            }
        }
    }

    private function formatContactNumber($dialCode, $number){
        return !empty($dialCode) ? $dialCode . $number : $number;
    }

    private function processMessage($number, $filename, $fullPathUrl, $messageBody, $type, $apiEndpoint, $instanceId, $accessToken){
        $data = [
            'number' => $number,
            'type' => $type,
            'message' => $messageBody,
            'media_url' => $fullPathUrl,
            'filename' => $filename,
            'instance_id' => $instanceId,
            'access_token' => $accessToken
        ];

        $response = $this->sendRequest($apiEndpoint, $data);

        $this->storeResponse($response, $number);
    }

    private function sendRequest($url, $data){
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

    private function storeResponse($response, $number){
        $status = $response->status ?? "error";
        $messageText = $response->message ?? "Message Not Send";
        $respost = new Normalwhatsappcampaignresponse();
        $respost->campaignlist_id = $this->post['id'];
        $respost->name = $this->allcontact['full_name'];
        $respost->mobile_no = $number;
        $respost->allcontact_id = $this->allcontact['id'];
        $respost->message_status = $status;
        $respost->message_text = $status === "success" ? "Message has been sent!" : $messageText;
        $respost->save();
    }
}
