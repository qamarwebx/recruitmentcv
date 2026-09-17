<?php

namespace App\Http\Controllers;

use App\Models\Metanotification;
use App\Models\Metawhatsappapi;
use App\Models\Otpvalidationmetalist;
use App\Models\Staticmetanotification;
use Illuminate\Http\Request;
use Netflie\WhatsAppCloudApi\Message\Template\Component;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;
class CheckNetFileController extends Controller
{

    public function index(){
        
        $metaAPI = Metawhatsappapi::where('status','=',1)->first();
        $otpCount = Otpvalidationmetalist::where('mobile_no','=','9594785319')->where('otp_date','=',date('Y-m-d'))->get(); 
        $staticTemp = Staticmetanotification::where('status','=',1)->get();
        $latestID = Otpvalidationmetalist::latest()->first();
        $responseData = [];
        $allStoreID = [];
        $allTempID = [];
        if (count($otpCount) > 0) {
            $responseData['status'] = true;
            // Stored ID
            foreach ($otpCount as $otpCount2) {
                $allStoreID [] = $otpCount2->metanotification_id;
            }

            // Meta ID
            foreach ($staticTemp as $staticTemp2) {
               $allTempID [] = $staticTemp2->id;
            }

            $newTempID = array_diff($allTempID,$allStoreID);
            if (count($newTempID) > 0) {
                $metaStaticTemp = Staticmetanotification::wherein('id',$newTempID)->get();
                $initState = 0;
                $responseData['id'] = $metaStaticTemp[$initState]->id;
                $responseData['otp_number'] = $metaStaticTemp[$initState]->otp_number;
                $responseData['meta_template_name'] = $metaStaticTemp[$initState]->meta_template_name;

                // Store Database
                $newOTP = new Otpvalidationmetalist();
                $newOTP->metanotification_id = $metaStaticTemp[$initState]->id;
                $newOTP->otp_no = $metaStaticTemp[$initState]->otp_number;
                $newOTP->mobile_no = "9594785319";
                $newOTP->otp_date = date('Y-m-d');
                $newOTP->save();
                
            } else {
                $allstaticTemp = Staticmetanotification::where('id','!=',$latestID->metanotification_id)->get();
                $initState = 0;
                $totalTemp = count($allstaticTemp) - 1;
                $randomID = mt_rand($initState,$totalTemp);
                $responseData['id'] = $allstaticTemp[$randomID]->id;
                $responseData['otp_number'] = $allstaticTemp[$randomID]->otp_number;
                $responseData['meta_template_name'] = $allstaticTemp[$randomID]->meta_template_name;

                // Store Database
                $newOTP = new Otpvalidationmetalist();
                $newOTP->metanotification_id = $allstaticTemp[$randomID]->id;
                $newOTP->otp_no = $allstaticTemp[$randomID]->otp_number;
                $newOTP->mobile_no = "9594785319";
                $newOTP->otp_date = date('Y-m-d');
                $newOTP->save();
            }
            

        } else {
            $initState = 0;
            $responseData['id'] = $staticTemp[$initState]->id;
            $responseData['otp_number'] = $staticTemp[$initState]->otp_number;
            $responseData['meta_template_name'] = $staticTemp[$initState]->meta_template_name;
            $responseData['status'] = false;

            // Store database
            $newOTP = new Otpvalidationmetalist();
            $newOTP->metanotification_id = $staticTemp[$initState]->id;
            $newOTP->otp_no = $staticTemp[$initState]->otp_number;
            $newOTP->mobile_no = "9594785319";
            $newOTP->otp_date = date('Y-m-d');
            $newOTP->save();
        }
        
        $getAPI = [];

        if (isset($staticTemp) && isset($metaAPI)) {
            $getAPI['api_access_token'] = $metaAPI->api_access_token;
            $getAPI['api_base_url'] = $metaAPI->api_base_url;
            $getAPI['vendor_uid'] = $metaAPI->vendor_uid;
            $getAPI['endpoint'] = $getAPI['api_base_url'].'/'.$getAPI['vendor_uid'].'/contact/send-template-message';



            $response ['status'] = "Available";
        } else {
            $response ['status'] = "Not Available";
        }
        


        

        
        

        
    


        dd($getAPI);
        return view('netfilecheck.index');
    }

    public function sendTest(Request $request){

        $from_phone_number_id = "264001556791706";
        $access_token = "EAANEY61DQ70BOZCiAwuZBmFzOrRhSx8K3qPKlDuskFZAVISIXQCen1jRIvcKXkysEQBmZCCdX4J5oRCjKZAHbMoyVMWAOIeIGtBNYpV2oReYD9ZChidOXP3L9tsZBsgqgVLk0hNTQowVe6ez2pcVFetsZBdA2MyEV8IKsZAdzXhys9al7XsaWEL59p1zPSM7TnUnx";
        $senderNo = "919594785319";
        $message = "Hi Kalam Shaikh";
        $whatsapp_cloud_api  = new WhatsAppCloudApi([
            'from_phone_number_id' => $from_phone_number_id,
            'access_token' => $access_token
        ]);

        $component_header = [];

        $component_body = [
            [
                'type' => 'text',
                'text' => '4102'
            ],

            
        ];

        $component_buttons = [];

        $components = new Component($component_header,$component_body,$component_buttons);

        try {
            // $response = $whatsapp_cloud_api->sendTextMessage($senderNo,$message);
            // $response = $whatsapp_cloud_api->sendTemplate($senderNo,"hello_world",'en_US');
            // $response = $whatsapp_cloud_api->sendTemplate($senderNo,"order",'en_US');
            $response = $whatsapp_cloud_api->sendTemplate($senderNo,"otp_customer",'en_US',$components);
           
            print_r($response->body());

        }catch(\Netflie\WhatsAppCloudApi\Response\ResponseException $e) {
            print_r($e->response()->body());
        }

    }

}
