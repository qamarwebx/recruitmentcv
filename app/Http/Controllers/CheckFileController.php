<?php

namespace App\Http\Controllers;

use App\AdminModel\AllContact;
use App\AdminModel\Courier;
use App\AdminModel\Courierclone;
use App\AdminModel\CourierDetails;
use App\AdminModel\Couriernotification;
use App\AdminModel\CourierStatus;
use App\AdminModel\Todo;
use App\AdminModel\Userwhatsappapi;
use App\CourerNotificationList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckFileController extends Controller
{
    public function index()
    {
        return view('demo.checkerror');
    }

    public function todoList()
    {
        $posts = AllContact::orderBy('id','DESC')->get();
        
        $conts = Todo::all();
        // $conts = Todo::where('process_status','=',1)->where('status','!=',3)->get();
        // $conts = Todo::where('process_status','=',1)->whereRaw('not find_in_set("Low",tags)')->whereRaw('not find_in_set("Medium",tags)')->whereRaw('not find_in_set("High",tags)')->get();
        // $conts = Todo::where('process_status','=',1)->whereRaw('not find_in_set("Low",tags)')->whereRaw('not find_in_set("Medium",tags)')->whereRaw('not find_in_set("High",tags)')->get();
        
        // $conts = Todo::where('process_status','=',1)->where('status','!=',3)->whereRaw('find_in_set("Low",tags)')->get();
        // $conts = Todo::where('process_status','=',1)->where('status','!=',3)->whereRaw('find_in_set("Medium",tags)')->get();
        $conts = Todo::where('process_status','=',1)->where('status','!=',3)->whereRaw('find_in_set("High",tags)')->get();
        $yesterday = now()->subDay()->toDateString();
        $couriers = Couriernotification::where('upload_date','<=',$yesterday)->get();

        $pCouriers = DB::table('qr_courier_tbl as courier')
            ->leftjoin('courierclones as cclone','courier.id','=','cclone.courier_id')
            ->leftjoin('qr_courier_status_tbl as cors','cclone.id','=','cors.courier_id')
		    ->leftjoin('qr_courier_sd_tbl as cord','cclone.id','=','cord.courier_id')
		    ->leftjoin('qr_party_tbl as party','party.pty_id','=','cclone.pty_id')
            ->select('courier.*','party.pty_ag_name','cors.stage','cors.status as cstatus','cclone.id as cid')
            ->where('cclone.status','=','0')
		    ->get();

        // $groupID = DB::table('courierclones')
        //     ->select('groupid')
        //     ->groupBy('groupid')
        //     ->where('groupid','!=','')
        //     ->get();

        $groupID = Courierclone::where('groupid','!=','')->groupBy('groupid')->select('groupid')->get();

        $cnnot = CourerNotificationList::where('id','=',2)->first();

        

        $url_path = url('/').'/image/service-candidate/'.$cnnot->file;

        dd($url_path);

        $msgw = '<ol style="list-style-type:number !important;">';
            if ($cnnot->valid_pass == 1) {
                $msgw .= '<li>Valid Passport</li>';   
            }
            if ($cnnot->old_pass == 1) {
                $msgw .= '<li>Old Passport</li>';   
            }
            if ($cnnot->driving_lic == 1) {
                $msgw .= '<li>Driving License</li>';   
            }
            if ($cnnot->exit_paper == 1) {
                $msgw .= '<li>Exit Paper</li>';   
            }
            if ($cnnot->photo_r == 1) {
                $msgw .= '<li>Photo</li>';   
            }
            if ($cnnot->med_rep == 1) {
                $msgw .= '<li>Medical Report</li>';   
            }
            if ($cnnot->other_doc == 1) {
                $msgw .= '<li>'.$cnnot->doc_name.'</li>';   
            }

            if($cnnot->medical_token == 1){
                $msgw .= '<li>Medical Token</li>';
            }


        $msgw .= '</ol>';
            
        

        // Second Method

        $i = 1;
        if ($cnnot->valid_pass == 1) {
            $msg2 = $i.") Valid Passport\n";
            $i++;
        }
        if ($cnnot->old_pass == 1) {
            $msg2 .= $i.") Old Passport\n";
            $i++;
        }
        if ($cnnot->driving_lic == 1) {
            $msg2 .= $i.") Driving License\n";
            $i++;
        }
        if ($cnnot->exit_paper == 1) {
            $msg2 .= $i.") Exit Paper\n";
            $i++;
        }
        if ($cnnot->photo_r == 1) {
            $msg2 .= $i.") Photo\n";
            $i++;
        }
        if ($cnnot->med_rep == 1) {
            $msg2 .= $i.") Medical Report\n";
            $i++;
        }
        if ($cnnot->other_doc == 1) {
            $msg2 .= $i.") ".$cnnot->doc_name."\n";
            $i++;
        }
        if ($cnnot->medical_token == 1) {
            $msg2 .= $i.") Medical Token\n";
            $i++;
        }

        
        
        $final_msg = $cnnot->candidate_name."\n".$msg2;
        // dd($final_msg);

        $full_url = url('/');
        

        return view('demo.checkerror',compact('posts','conts','couriers','pCouriers','groupID','cnnot','msgw','msg2','final_msg','full_url'));
    }

    public function employer(){
        $pageConfigs = ['pageHeader' => false];
        return view('demo.employer',['pageConfigs' => $pageConfigs]);
    }

    public function checkContact()
    {
        $posts = AllContact::all();
        $pageConfigs = ['pageHeader' => false];
        return view('demo.contact',['pageConfigs' => $pageConfigs,'posts' => $posts]);
    }

    public function sendWhatsapp()
    {
        return view('demo.whatsapp');
    }

    public function sendWhspost(Request $request)
    {

        $getApi = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=',1)->first();

        // $access_token = '23c728521a324cd8b8332360df985602';
        // $instance_id = '636CF26A76A36';

        $inst_id = $getApi->instance_key;
        $acce_tk = $getApi->api_key;
        $text_url = $getApi->text_message_url;
        
        $msg = urlencode($request->textmsg);
        
        if ($request->type == 'text') {
            $data = [
                'number' => $request->phone,
                'type' => 'text',
                'message' => $request->textmsg,
                'instance_id' => $inst_id,
                'access_token' => $acce_tk
            ];
        
            
            // dd($msg);

            // $url = "https://whatsappnow.in/api/send.php?number=".$request->phone."&type=text&message=".$request->textmsg."&instance_id=".$instance_id."&access_token=".$access_token;
            $url = $text_url."?number=".$request->phone."&type=text&message=".$msg."&instance_id=".$inst_id."&access_token=".$acce_tk;
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);

            $result =  curl_exec($ch);
            $final_result = json_decode($result,true);

            // dd($final_result['message']);
            // curl_exec($ch);
            curl_close($ch);
            // echo $result;
        }elseif($request->type == 'media'){
            $file = $request->file('file');
            $file_name = time().'_'.$file->getClientOriginalName();
            $file->move(base_path().'/public/status-images/mofa_screenshots', $file_name);
            
            $url_path = url('/');
            $file_path = 'status-images/mofa_screenshots';
            $media_url =  $url_path.'/'.$file_path.'/'.$file_name;
            
            // $url = "https://whatsappnow.in/api/send.php?number=".$request->phone."&type=media&message=".$request->textmsg."&media_url=".$media_url."&filename=".$file_name."&instance_id=".$instance_id."&access_token=".$access_token;
            $url = $text_url."?number=".$request->phone."&type=media&message=".$request->msg."&media_url=".$media_url."&filename=".$file_name."&instance_id=".$inst_id."&access_token=".$acce_tk;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_URL,$url);
            $result =  curl_exec($ch);
            echo $result;
        }
        
        return redirect()->back();
    }

    public function tracking($tracking,$id)
    {
        $post = Courier::find($id);
        $cnot = Couriernotification::where('courier_id','=',$id)->where('status','=',0)->first();
        return view('demo.tracking',compact('tracking','post','cnot'));
    }

    public function trackingStore(Request $request,$id,$tracking){
        $courier_info = Courier::find($id);
        $courier_det = CourierDetails::where('courier_id','=',$id)->first();
        $courier_not = Couriernotification::where('courier_id','=',$id)->first();
        if ($request->rec_status == 1) {
            $courier_not->status = 1;
            
            $courier_det->rec_name_f_link = $request->rec_name;
            $courier_det->received_data = Carbon::now();

            $courier_not->save();
            $courier_det->save();

            Session::flash('success','You are received the courier, we will check it our system!');

        }else{
            Session::flash('success','Thank you for your valuable feedback!');
        }
    }
}
