<?php

namespace App\Http\Controllers;

use App\AdminModel\Userwhatsappapi;
use Illuminate\Http\Request;
use File;
use Session;
class WhatsappSendController extends Controller
{
    public function index()
    {
        $pageConfigs = ['pageHeader' => false];
        return view('demo.whatsapp.index',['pageConfigs' => $pageConfigs]);
    }

    public function fileMsg()
    {
        $pageConfigs = ['pageHeader' => false];
        return view('demo.whatsapp.indexf',['pageConfigs' => $pageConfigs]);
    }

    public function sendm(Request $request)
    {

        $getApi = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','!=',0)->first();
        $instance_id = $getApi->instance_key;
        $access_token = $getApi->api_key;
        $url_text = $getApi->text_message_url;

        $phone = $request->input('phone');
        $msgBody = urlencode($request->input('msgBody'));

        // $msgBody = "Dear Kalam Shaikh \nThe Candidate Ashfaq Ahmed bearing O74865203 with Visa number 246874120 has been stamped successfully from Saudi Consulate on 24-02-2022.";
        

        // fetch details
        // $ins = "1871e3380fd98f6bcfe57afc132bf94621f2037cc43dcea5999d5cec1dd9267a";
        // $api = "26bf73bacb1eb4d265f22cdd8f05ce96ca2c91df89cb5f18c059cc3b9297310d";
    
        // $url = "http://whatsapi.smsinsta.com/api/send-text";
        // $data = [
        //     "number" => $phone,
        //     "msg" => $msgBody,
        //     "instance" => $ins,
        //     "apikey" => $api
        // ];

        
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // $result = curl_exec($ch);
        // curl_close($ch);
        // echo $result;
               


        $url = $url_text."?number=".$phone."&type=text&message=".$msgBody."&instance_id=".$instance_id."&access_token=".$access_token;
        
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_URL,$url);
        $result =  curl_exec($ch);
        echo $result;

        if(isset($result)){
            Session::flash('success','Message Send Successfully');
            return redirect()->back();
        }


    }

    public function sendMf(Request $request)
    {
        $phone = $request->input('phone');
        $msgBody = urlencode($request->input('msgBody'));

        $getApi = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','!=',0)->first();
        $instance_id = $getApi->instance_key;
        $access_token = $getApi->api_key;
        $url_text = $getApi->text_message_url;


        $file = $request->file('file');
        $file_count = File::files(base_path().'/public/status-images/mofa_screenshots');
        $filecount = 0;
        if ($file_count !== false) {
            $filecount = count($file_count);
        }
        $file_exe = $file->getClientOriginalExtension();
        $name = $filecount . '.' . $file_exe;
        $file->move(base_path().'/public/status-images/mofa_screenshots', $name);
        $mofa_screenshots = $name;
    
        $url_path = url('/');
                
		$file_name = 'status-images/mofa_screenshots';
        $filepath = $url_path.'/'.$file_name.'/'.$mofa_screenshots;
        // dd($filepath);
	    
        // $media ="https://cdn.pixabay.com/photo/2022/03/25/23/47/bible-7092020_960_720.jpg";
        $media = $filepath;



        // $ins = "1871e3380fd98f6bcfe57afc132bf94621f2037cc43dcea5999d5cec1dd9267a";
	    // $api = "26bf73bacb1eb4d265f22cdd8f05ce96ca2c91df89cb5f18c059cc3b9297310d";
	    // $type = "image";

	    // $url = "http://whatsapi.smsinsta.com/api/send-media";
        // $data = [
        //     "number" => $phone,
        //     "msg" => $msgBody,
        //     "media" => $media,
        //     "type" => $type,
        //     "instance" => $ins,
        //     "apikey" => $api
        // ];


        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // $result = curl_exec($ch);
        // curl_close($ch);

        $url = $url_text."?number=".$phone."&type=media&message=".$msgBody."&media_url=".$media."&filename=".$mofa_screenshots."&instance_id=".$instance_id."&access_token=".$access_token;
            
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_URL,$url);
        $result =  curl_exec($ch);
        echo $result;

        if(isset($result)){
            Session::flash('success','Send');
            return redirect()->back();
        }else{
            Session::flash('error','Not Send');
            return redirect()->back();
        }

    }

    public function sendmm()
    {
        
    }

    public function sendmms(Request $request)
    {

    }

    public function testmsg(Request $request){

        // Get API details based on user
        $getApi = Userwhatsappapi::where('staff_id','=',$request->staff_id)->where('status','=',1)->first();
        $instance_id = $getApi->instance_key;
        $access_token = $getApi->api_key;
        $url_text = $getApi->text_message_url;

        $file = $request->file('file');


        if(isset($getApi)){
            if($file != ''){
                $file_count = File::files(base_path().'/public/status-images/mofa_screenshots');
                $filecount = 0;
                if ($file_count !== false) {
                    $filecount = count($file_count);
                }
                $file_exe = $file->getClientOriginalExtension();
                $name = $filecount . '.' . $file_exe;
                $file->move(base_path().'/public/status-images/mofa_screenshots', $name);
                $mofa_screenshots = $name;
        
                $url_path = url('/');
                $file_name = 'status-images/mofa_screenshots';
                $filepath = $url_path.'/'.$file_name.'/'.$mofa_screenshots;
        
                $media = $filepath;
    
                $sendURL3 = $url_text."?number=".$request->mob_no."&type=media&message=".urlencode($request->notes)."&media_url=".$media."&filename=".$mofa_screenshots."&instance_id=".$instance_id."&access_token=".$access_token;
                
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$sendURL3);
                $result =  curl_exec($ch);
                echo $result;
                if(isset($result)){
                    Session::flash('success','Send');
                    return redirect()->back();
                }else{
                    Session::flash('error','Not Send');
                    return redirect()->back();
                }
            }else{
    
                $sendURL = $url_text."?number=".$request->mob_no."&type=text&message=".urlencode($request->notes)."&instance_id=".$instance_id."&access_token=".$access_token;
    
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$sendURL);
                $result =  curl_exec($ch);
                echo $result;
                if(isset($result)){
                    Session::flash('success','Send');
                    return redirect()->back();
                }else{
                    Session::flash('error','Not Send');
                    return redirect()->back();
                }
            }
        }else{
            Session::flash('success','No Message send');
            return redirect()->back();
        }

    }
    
}
