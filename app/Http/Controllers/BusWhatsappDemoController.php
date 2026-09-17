<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class BusWhatsappDemoController extends Controller
{
    public function index(){
        return view('whatsapp.index');
    }

    public function sendmessge(Request $request){
        $url = "https://graph.facebook.com/v17.0/114220348257981/messages";
        $token = "EAAUSZBNpYFXkBOzpZCkJX12ARyooc6BBeRiHlHqEoZCoJZANElo1sK1EfUjQiY2SxF52XZBD9QKyhKs2OsZAU0BaZC6ZA1VgKQlAQBv3bq8r58JLQTJmzWcB9OQj0ZABcW5G4lmwndUZAXNkiDXiiZBrJssk0ZAmrLZAfBzZCoptyUSzKZCP1bTEdTzBVZA2HoJpD7ZCwqOawhkDaDfpEbBvnhrx0JzZARA1sGK3VZBt4ceNv8ZD";
        
        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $request->mobile_no,
            'type' => 'template',
            'template' => [
                'name' => 'hello_world',
                'language' => ['code' => 'en_US'],
            
            ],
        ];

        $dataBody = json_encode($data);

        // dd(json_encode($data));


        try {

            $client = new Client();
            $header = [
                'Content-Type' => 'application/json',
                'Authorization' => $token
            ];
            $bodyData = json_encode($data);
            $response = $client->post($url,$header,$bodyData);


            $business = $response->getBody();

            return json_decode($business);

            // $curl = curl_init();
            // curl_setopt($curl,CURLOPT_URL,$url);
            // curl_setopt($curl,CURLOPT_HEADER,array('Content-Type' => 'application/json','Authorization' => $token));
            // curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
            // curl_setopt($curl,CURLOPT_POSTFIELDS,$dataBody);
            // $resp = curl_exec($curl);
            // curl_close($curl);

            // echo $resp;


        } catch (\Throwable $e) {
            return $e->getMessage();
        }




    }
}
