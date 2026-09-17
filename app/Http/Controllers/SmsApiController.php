<?php

namespace App\Http\Controllers;

use App\Models\Adminpermission;
use App\Models\SmsApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmsApiController extends Controller
{
    public function index(){

        $perm = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        return view('admin.sms_api.index',compact('perm'));
    }

    public function indexJson(Request $request){

        $userID = Auth::guard('admin')->user()->id;

        $permission = Adminpermission::where('staff_id','=',$userID)->first();

        if ((Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && $permission->full_access == 1)) {
            $post = SmsApi::orderBy('id','DESC')->get();
        } elseif (isset($permission) && $permission->full_access == 0) {
            $post = SmsApi::where('staff_id','=',$userID)->orderBy('id','DESC')->get(); 
        }

        $data['data'] = $post;

        return response()->json($data);
    }

    public function store(Request $request){
        $post = new SmsApi();
        $post->api_name = $request->api_name;
        $post->mobile_no = $request->mobile_no;
        $post->api_base_url = $request->api_base_url;
        $post->staff_id = Auth::guard('admin')->user()->id;
        $post->notes = $request->notes;
        $post->save();

        return redirect()->back()->with('success','SMS API stored!');
    }

    public function edit(Request $request){
        $post = SmsApi::find($request->id);

        return response()->json($post);
    }

    public function getAssignto(Request $request) {
        $post = SmsApi::find($request->id);

        return response()->json($post);
    }

    public function getAssigntoStore(Request $request){
        $post = SmsApi::find($request->edit_id);

        if ($request->api_assign_to != '') {
            $post->api_assign_to = implode(",",$request->api_assign_to);

        }else{
            $post->api_assign_to = "";
        }

        $post->save();

        return redirect()->back()->with('success','API Assignto successfully!');
    }

    public function checkassignto(Request $request){

        $name = trim($request->name);
        $id = $request->id;

        $post = SmsApi::whereRaw("FIND_IN_SET(?,api_assign_to)", [$name])->where('id','!=',$id)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));


        // $post = Metawhatsappapi::whereRaw('api_assign_to');

    }


    public function update(Request $request){
        $post = SmsApi::find($request->edit_id);
        $post->api_name = $request->api_name;
        $post->mobile_no = $request->mobile_no;
        $post->api_base_url = $request->api_base_url;
        $post->notes = $request->notes;
        $post->save();

        return redirect()->back()->with('success','SMS API updated!');
    }

    public function getStatus(Request $request){
        $post = SmsApi::find($request->id);

        return response()->json($post);
    }

    public function statusUpdt(Request $request){
        $post = SmsApi::find($request->statusID);
        $post->status = $request->status;
        $post->save();

        return redirect()->back()->with('success','Status updated!');
    }


      public function delete(Request $request){
        $post = SmsApi::find($request->delID);

        $post->delete();

        return redirect()->back()->with('success','SMS API deleted!');
    }
}
