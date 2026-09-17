<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\Basepathstatus;
use Illuminate\Http\Request;
use App\Models\Whatsappapi;
use App\Models\Whatslinemessagestatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WhatsappApiController extends Controller
{
    public function index(){

        $whatsappAPI =  Whatsappapi::orderBy('id','DESC')->where('status','=',1)->get();
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        $staffs = Admin::where('status', 1)->orderBy('name')->get();

        return view('admin.whatsapp.index',['apilists' => $whatsappAPI,'permission' => $permission,'staffs' => $staffs]);
    }

    public function indexJson(Request $request){

        $userID = Auth::guard('admin')->user()->id;

        $permission = Adminpermission::where('staff_id','=',$userID)->first();

        if ((Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && $permission->full_access == 1)) {
            // $post = Whatsappapi::orderBy('id','DESC')->get();

            $post = DB::table('whatsappapis as wapi')
                ->leftJoin('admins as admin','admin.id','=','wapi.createby_id')
                ->leftJoin('admins as admin2','admin2.id','=','wapi.staff_id')
                ->select('wapi.*','admin.name as adminname','admin2.name as adminname2')
                ->orderBy('wapi.id','DESC')
                ->get();

        } elseif (isset($permission) && $permission->full_access == 0) {
            if ($permission->view_whatsapp_api == 1) {
                // $post = Whatsappapi::orderBy('id','DESC')->get();

                $post = DB::table('whatsappapis as wapi')
                ->leftJoin('admins as admin','admin.id','=','wapi.createby_id')
                ->leftJoin('admins as admin2','admin2.id','=','wapi.staff_id')
                ->select('wapi.*','admin.name as adminname','admin2.name as adminname2')
                ->orderBy('wapi.id','DESC')
                ->get();

            } else {
                // $post = Whatsappapi::where('createby_id','=',$userID)->orderBy('id','DESC')->get();

                $post = DB::table('whatsappapis as wapi')
                ->leftJoin('admins as admin','admin.id','=','wapi.createby_id')
                ->leftJoin('admins as admin2','admin2.id','=','wapi.staff_id')
                ->select('wapi.*','admin.name as adminname','admin2.name as adminname2')
                ->where('wapi.staff_id','=',$userID)
                ->orderBy('wapi.id','DESC')
                ->get();

            }

        }

        $data['data'] = $post;
        return response()->json($data);
    }

    public function store(Request $request){
        $post = new Whatsappapi();
        $post->mobile_no = $request->mobile_no;
        $post->instance_id = $request->instance_id;
        $post->access_token = $request->access_token;
        $post->api_for = $request->api_for;
        $post->api_url = $request->api_url;
        $post->notes = $request->notes;
        if (isset($request->staff_id) && $request->staff_id != '') {
            $post->staff_id = $request->staff_id;
        }else{
            $post->staff_id = Auth::guard('admin')->user()->id;
        }
        $post->createby_id = Auth::guard('admin')->user()->id;
        $post->notes = $request->notes;
        $post->save();

        return redirect()->back()->with('success','Whatsapp API created!');
    }

    public function edit(Request $request){
        $post = Whatsappapi::find($request->id);

        return response()->json($post);
    }

    public function update(Request $request){
        $post = Whatsappapi::find($request->edit_id);
        $post->mobile_no = $request->mobile_no;
        $post->instance_id = $request->instance_id;
        $post->access_token = $request->access_token;
        $post->api_for = $request->api_for;
        $post->api_url = $request->api_url;
        $post->notes = $request->notes;
        if (isset($request->staff_id) && $request->staff_id != '') {
            $post->staff_id = $request->staff_id;
        } else {
            $post->staff_id = Auth::guard('admin')->user()->id;
        }


        $post->notes = $request->notes;
        $post->save();

        return redirect()->back()->with('success','Whatsapp API updated!');
    }

    public function getStatus(Request $request){
        $post = Whatsappapi::find($request->id);

        return response()->json($post);
    }

    public function statusUpdt(Request $request){
        $post = Whatsappapi::find($request->statusID);
        $post->status = $request->status;
        $post->save();

        return redirect()->back()->with('success','Status updated!');
    }

    public function delete(Request $request){
        $post = Whatsappapi::find($request->delID);

        $post->delete();

        return redirect()->back()->with('success','Whatsapp API deleted!');
    }

    public function sendTest(Request $request){

        $getApi = Whatsappapi::find($request->api_id);
        $url_text = $getApi->api_url;
        $instance_id = $getApi->instance_id;
        $access_token = $getApi->access_token;

        $basepathstatus = Basepathstatus::first();
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $name = $file->getClientOriginalName();
            $filename_ren = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ","_",$filename_ren);
            $new_file = $repspfilename.'.'.$fileext_ren;
            if($basepathstatus->base_path_status == 1){
                $file->move(base_path().'/public/admin/assets/images/whatsapp',$new_file);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/whatsapp',$new_file);
            }

            // Send Whatsapp withoud file
            $media = url('/admin/assets/images/whatsapp/'.$new_file);
            $sendURL3 = $url_text."?number=".$request->mobile_no."&type=media&message=".urlencode($request->message_text)."&media_url=".$media."&filename=".$new_file."&instance_id=".$instance_id."&access_token=".$access_token;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_URL,$sendURL3);
            $result =  curl_exec($ch);
            // echo $result;
            // if(isset($result)){
            //     return redirect()->back()->with('success','Message send');
            // }else{
            //     return redirect()->back()->with('success','Message not send');
            // }

            curl_close($ch);

            $result_data = json_decode($result);
            // dd($result_data);
            if (isset($result_data)) {
                if ($result_data->status == 'success') {
                    // update in whatsapp message statsu
                    $new_msg_status = new Whatslinemessagestatus();
                    $new_msg_status->message_status = $result_data->status;
                    $new_msg_status->mobile_no = $request->mobile_no;
                    $new_msg_status->message_text = $request->message_text;
                    $new_msg_status->message_time = $result_data->message->messageTimestamp;
                    $new_msg_status->message_type = "Test API";
                    $new_msg_status->save();
                    return redirect()->back()->with('success','Message send');
                } else {
                    $new_msg_status = new Whatslinemessagestatus();
                    $new_msg_status->message_status = $result_data->status;
                    $new_msg_status->error_msg = $result_data->message;
                    $new_msg_status->mobile_no = $request->mobile_no;
                    $new_msg_status->message_text = $request->message_text;
                    // $new_msg_status->message_time = $result_data->message->messageTimestamp;
                    $new_msg_status->message_type = "Test API";
                    $new_msg_status->save();
                    return redirect()->back()->with('errorMsg', $result_data->message);
                }
            }else{
                $new_msg_status = new Whatslinemessagestatus();
                $new_msg_status->message_status = "Failed";
                $new_msg_status->error_msg = "API Not Work";
                $new_msg_status->mobile_no = $request->mobile_no;
                $new_msg_status->message_text = "API Not work properly";
                // $new_msg_status->message_time = $result_data->message->messageTimestamp;
                $new_msg_status->message_type = "Test API";
                $new_msg_status->save();
                return redirect()->back()->with('errorMsg', "API Not work properly");
            }


        } else {
            // Send whatsapp without file
            $sendURL = $url_text."?number=".$request->mobile_no."&type=text&message=".urlencode($request->message_text)."&instance_id=".$instance_id."&access_token=".$access_token;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_URL,$sendURL);
            $result =  curl_exec($ch);
            // echo $result;
            $result_data = json_decode($result);
            // dd($result_data);
            curl_close($ch);

            if (isset($result_data)) {
                if ($result_data->status == 'success') {
                    // update in whatsapp message statsu
                    $new_msg_status = new Whatslinemessagestatus();
                    $new_msg_status->message_status = $result_data->status;
                    $new_msg_status->mobile_no = $request->mobile_no;
                    $new_msg_status->message_text = $request->message_text;
                    $new_msg_status->message_time = $result_data->message->messageTimestamp;
                    $new_msg_status->message_type = "Test API";
                    $new_msg_status->save();
                    return redirect()->back()->with('success','Message send');
                } else {
                    $new_msg_status = new Whatslinemessagestatus();
                    $new_msg_status->message_status = $result_data->status;
                    $new_msg_status->error_msg = $result_data->message;
                    $new_msg_status->mobile_no = $request->mobile_no;
                    $new_msg_status->message_text = $request->message_text;
                    // $new_msg_status->message_time = $result_data->message->messageTimestamp;
                    $new_msg_status->message_type = "Test API";
                    $new_msg_status->save();
                    return redirect()->back()->with('errorMsg', $result_data->message);
                }
            } else {
                $new_msg_status = new Whatslinemessagestatus();
                $new_msg_status->message_status = "Failed";
                $new_msg_status->error_msg = "API Not Work";
                $new_msg_status->mobile_no = $request->mobile_no;
                $new_msg_status->message_text = "API Not work properly";
                // $new_msg_status->message_time = $result_data->message->messageTimestamp;
                $new_msg_status->message_type = "Test API";
                $new_msg_status->save();
                return redirect()->back()->with('errorMsg', "API Not work properly");
            }




        }

    }
}
