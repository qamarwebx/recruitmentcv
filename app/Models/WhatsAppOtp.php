<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppOtp extends Model
{
    use HasFactory;

    function __construct() {

    }
   
    private $API_KEY = '64d4daa924136';
    private $SENDER_ID = "654B71229F1F6";
    private $PATH_URL = 'https://whatsline.in/api/send';
    private $RESPONSE_TYPE = 'json';

    public function sendSMS($OTP, $mobileNumber){
        $isError = 0;
        $errorMessage = true;              
        $message = urlencode($OTP." is your OTP for phone verification at qamarhair.com");    
      
        //Preparing post parameters
        $postData = array(
            'authkey' => \config('sms.api_key'),
            'mobiles' => $mobileNumber,
            'message' => $message,
            'path' => \config('sms.path_url'),
            'sender' => \config('sms.sender_id'),
            'route' => \config('sms.route_no'),
            'response' => \config('sms.response_type'),
            
        );
    
        /*  $url = "https://2factor.in/API/V1/05fd2675-741a-11ec-b710-0200cd936042/SMS/+919970000060/AUTOGEN";  */
      /*  $url = "https://2factor.in/API/V1/05fd2675-741a-11ec-b710-0200cd936042/BAL/SMS";  */
        /*  $url = "https://2factor.in/API/V1/05fd2675-741a-11ec-b710-0200cd936042/SMS/VERIFY/803c0be2-ff58-4417-bc22-845f327df3ca/826898"; */  
        /*  $url = "https://2factor.in/API/V1/{api_key}/SMS/{phone_number}/{otp}/{template_name}" */         
        /*  $url = "https://2factor.in/API/V1/05fd2675-741a-11ec-b710-0200cd936042/SMS/+917249859991/4499/SWOTP";  */

      

      /*  $url = $postData['path'].$postData['authkey'].'/SMS/'.$postData['mobiles'].'/'.$OTP.'/'.$postData['sender']; */
       $sendURL3 = $postData['path']."?number=".$postData['mobiles']."&type=text&message=".urlencode($postData['message'])."&instance_id=".$postData['sender']."&access_token=".$postData['authkey'];
       

               
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
           /*  CURLOPT_POSTFIELDS => $postData */
           CURLOPT_HTTPHEADER => array(
            "content-type: application/json"
          ),   
        ));     
     
        //Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);     
        
        //get response
        $smsResponse = curl_exec($ch);
        /* dd($smsResponse); */
        //Print error if any
        if (curl_errno($ch)) {
            $isError = true;
            $errorMessage = curl_error($ch);
        }      
        curl_close($ch);    
     
        if($isError){
            return array('error' => 1 , 'message' => $errorMessage);  
                     
        }else{
            return $smsResponse;            
        } 
       
    }

}
