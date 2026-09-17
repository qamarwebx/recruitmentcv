<?php

namespace App\Http\Controllers;

use App\Buswhatsappapi;
use App\Buswhatsversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessWhatsappController extends Controller
{
    public function index(){
        $post = Buswhatsversion::all();
        $pageConfigs = ['pageHeader' => false];
        return view('whatsapp.business.index',['pageConfigs' => $pageConfigs,'posts' => $post]);
    }

    public function indexJson(Request $request) {
        $posts = Buswhatsappapi::all();

        $data['data'] = $posts;
        return response()->json($data);
    }

    public function busversion(){
        $pageConfigs = ['pageHeader' => false];
        return view('whatsapp.version.index',['pageConfigs' => $pageConfigs]);
    }

    public function busversionJson(Request $request){
        $post = Buswhatsversion::orderBy('version','DESC')->get();

        $data['data'] = $post;
        return response()->json($data);
    }

    public function busversionStore(Request $request){
        $post = new Buswhatsversion();
        $post->version = $request->version;
        $post->user_id = Auth::user()->user_id;
        $post->save();

        return redirect()->back()->with('success','Business whatsapp version added!');
    }

    public function checkversion(Request $request){
        $version = $request->version;

        if(isset($request->id) && $request->id != ''){
            $post = Buswhatsversion::where('version','=',$version)->where('id','!=',$request->id)->count();

            if($post == 0){
                echo "true";
              }else{
                echo "false";
              }
        }else{
            $post = Buswhatsversion::where('version','=',$version)->count();

            if($post == 0){
                echo "true";
              }else{
                echo "false";
              }
        }
    }

    public function busversionEdit(Request $request){
        $post = Buswhatsversion::find($request->id);
        return response()->json($post);
    }

    public function busversionUpdt(Request $request){
        $post = Buswhatsversion::find($request->bustwh_id);
        $post->version = $request->version;
        $post->user_id = Auth::user()->user_id;
        $post->save();

        return redirect()->back()->with('success','Business whatsapp version updated!');
    }
    

    public function storeAPI(Request $request){
        $post = new Buswhatsappapi();
        $post->access_token = $request->access_token;
        $post->phone_number_id = $request->phone_number_id;
        $post->whatsapp_business_id = $request->whatsapp_business_id;
        $post->send_url = $request->send_url;
        $post->version_id = $request->version_id;
        $post->user_id = Auth::user()->id;
        $post->save();

        return redirect()->back()->with('success','Business API details stored!');
    }

    public function checkphonenumberID(Request $request){
        $number_id = $request->phone_number_id;

        if(isset($request->id) && $request->id != ''){
            $post = Buswhatsappapi::where('phone_number_id','=',$number_id)->where('id','!=',$request->id)->count();

            if($post == 0){
                echo "true";
              }else{
                echo "false";
              }
        }else{
            $post = Buswhatsappapi::where('phone_number_id','=',$number_id)->count();

            if($post == 0){
                echo "true";
              }else{
                echo "false";
              }
        }


    }

    public function editAPI(Request $request){
        $post = Buswhatsappapi::find($request->id);
        return response()->json($post);
    }

    public function updateAPI(Request $request){
        $post = Buswhatsappapi::find($request->bustwh_id);
        $post->access_token = $request->access_token;
        $post->phone_number_id = $request->phone_number_id;
        $post->whatsapp_business_id = $request->whatsapp_business_id;
        $post->send_url = $request->send_url;
        $post->version_id = $request->version_id;
        $post->save();

        return redirect()->back()->with('success','Business API details updated!');
    }

    public function viewAPI(Request $request){
        $post = Buswhatsappapi::find($request->id);
        return response()->json($post);
    }

    public function buswhattemplateL(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://graph.facebook.com/v17.0/114522728225678/message_templates',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer EAAUSZBNpYFXkBOZCETEPpRMC5x3S4GtXSZCdhAiqnzNMB800Nu46DUYsfoTVZBKrr5qyaGCcZAlWWHnTZAQJu6GCoYMeTPFCU1NTyfsPSbEohvpInbZBo2ZBsoP0pnCPGHRvodkcsXvZB6sWQ0PY0r1OdLQoMcVX4dnvhGas8JZCytnyaaVdPx43sthPCTx7PoYzGWblIh118l9m1Ow92YHUVWMxEeUVZAk4oSYZBBUZD'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    public function buswhattemplateI($id){
        $post = Buswhatsappapi::find($id);

        try {
            $token = 'Bearer '.$post->access_token;
            $url = 'https://graph.facebook.com/v17.0/'.$post->whatsapp_business_id.'/message_templates';
        
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: '.$token
            ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $data = json_decode($response);

        
            $pageConfigs = ['pageHeader' => false];

            return view('whatsapp.business.templist',['posts' => $data,'bus_id' => $id,'pageConfigs' => $pageConfigs]);
        } catch (\Exception $e) {
            return redirect()->route('buswhatsapplist')->with('error',$e->getMessage());
        }

    }

    public function buswhattemplatev1(Request $request){
    
        $post = Buswhatsappapi::find($request->bus_id);
        $url = "https://graph.facebook.com/v17.0/".$request->id;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$post->access_token
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $post_data = json_decode($response);



        // $data = [
        //     'name' => $post_data->name,
        //     'language' => $post_data->language,
        //     'status' => $post_data->status,
        //     'category' => $post_data->category,
        //     'id' => $post_data->id,

        // ];

        $res = '';
        $res .= '<tr><th>ID</th><td>'.$post_data->id.'</td></tr>';
        $res .= '<tr><th>Name</th><td>'.$post_data->name.'</td></tr>';
        $res .= '<tr><th>Language</th><td>'.$post_data->language.'</td></tr>';
        $res .= '<tr><th>Category</th><td>'.$post_data->category.'</td></tr>';
        $res .= '<tr><th>Status</th><td>'.$post_data->status.'</td></tr>';
        foreach ($post_data->components as $component) {
            if ($component->type == 'HEADER') {
                $res .= '<tr><th>'.$component->format.'</th><td>';
                if ($component->format == 'IMAGE') {
                    if (isset($component->example)) {
                        foreach ($component->example->header_handle as $imghand) {
                            $res .= '<img src="'.$imghand.'" class="img-fluid img-thumbnail"><br>';
                        }
                    }
                    $res .= '</td></tr>';
                }

                if ($component->format == 'DOCUMENT') {
                    if (isset($component->example)) {
                        foreach ($component->example->header_handle as $dochand) {
                            $res .= '<a href="'.$dochand.'" target="_blank">View Document</a>';
                        }
                    }
                    $res .= '</td></tr>';
                }

                if ($component->format == 'VIDEO') {
                    
                }
    
                if ($component->format == 'LOCATION') {
                    
                }
    
                if ($component->format == 'TEXT') {
                    $res .= '<tr><th>'.ucfirst($component->type).'</th><td>'.$component->text.'</td></tr>';
                }
            }

            if ($component->type == 'BODY') {
                $res .= '<tr><th>'.ucfirst($component->type).'</th><td>'.$component->text.'</td></tr>';
            }


            if ($component->type == 'FOOTER') {
                $res .= '<tr><th>'.ucfirst($component->type).'</th><td>'.$component->text.'</td></tr>';
            }

            if($component->type == 'BUTTONS'){
                $res .= '<tr><th>'.$component->type.'</th>';
                foreach ($component->buttons as $button) {
                    $res .= '<td><a href="'.$button->url.'" class="btn btn-sm btn-success">'.$button->text.'</a></td>';
                }
                $res .= '</tr>';
            }
        }

        $arr['res'] = $res;

        return response()->json($arr);

    }


    public function buswhattemplatev2(Request $request,$bus_id,$temp_id){
    
        $post = Buswhatsappapi::find($bus_id);
        $url = "https://graph.facebook.com/v17.0/".$temp_id;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$post->access_token
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        dump(json_decode($response));


    }

    public function sendTemplate(Request $request){
        
    }
}
