<?php

namespace App\Http\Controllers;

use App\Models\Metanotification;
use App\Models\Metawhatsapplog;
use Illuminate\Http\Request;
use App\Models\Metawhatsappapi;

class OfficialWhatsappController extends Controller
{
    public function index(){

        return view('testofficialwhatsapp');
    }

    public function SendTextMessage(Request $request){

        // $metaAPI = Metawhatsappapi::where('status','=',1)->first();
        $metaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['otp'])->first();

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



        $data = [
            'phone_number' => $request->mobile,
            "template_name" => "otp5",
            "template_language" => "en",
            // "header_image" => "https://images.pexels.com/photos/8386440/pexels-photo-8386440.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1",
            // "field_1" => "4562",
            // "button_0" => "4562",
            "contact" => [
                'first_name' => $request->fname,
                'last_name' => $request->lname,
                "email" => "johndoe@doamin.com",
                "country" => "india",
                "language_code" => "en"
            ]
        ];



        $curl = curl_init();

        // dd($data);



        curl_setopt_array($curl, array(
            CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
            CURLOPT_URL => $endpoint_api,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data)
        ));

        $response = curl_exec($curl);


        curl_close($curl);
        $responseGet = json_decode($response);
        if (isset($responseGet->result)) {
            // Store in Meta Logs
            $post = new Metawhatsapplog();
            $post->message_for = "Test Message";
            $post->message_for_data = json_encode($data);
            $post->message_type = "Template";
            $post->template_name = "order";
            $post->message_status = $responseGet->result;
            $post->message_text = $responseGet->message;
            $post->save();
            // return redirect()->back();
        }else{
            // Store in Meta Logs
            $post = new Metawhatsapplog();
            $post->message_for = "Test Message";
            $post->message_for_data = json_encode($data);
            $post->message_type = "Template";
            $post->template_name = "order";
            $post->message_status = "Failed";
            $post->message_text = $responseGet->message;
            $post->message_data_error = json_encode($responseGet->errors);
            $post->save();
            // return redirect()->back();
        }

        dd($response);

    }
}
