<?php

namespace App\Http\Controllers;

use App\Models\Adminpermission;
use App\Models\Metawhatsappapi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MetaWhatsappApiController extends Controller
{
    public function index(){
        return view('admin.metawhatsappapi.index');


    }

    public function indexJson(Request $request){

        $userID = Auth::guard('admin')->user()->id;

        $permission = Adminpermission::where('staff_id','=',$userID)->first();

        if ((Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && $permission->full_access == 1)) {
            $post = Metawhatsappapi::orderBy('id','DESC')->get();
        } elseif (isset($permission) && $permission->full_access == 0) {
            if ($permission->view_whatsapp_meta_api == 1) {
                $post = Metawhatsappapi::orderBy('id','DESC')->get();
            } else {
                $post = Metawhatsappapi::where('staff_id','=',$userID)->orderBy('id','DESC')->get();
            }

        }



        $data['data'] = $post;

        return response()->json($data);
    }

    public function store(Request $request){
        $post = new Metawhatsappapi();
        $post->mobile_no = $request->mobile_no;
        $post->api_access_token = $request->api_access_token;
        $post->api_base_url = $request->api_base_url;
        $post->vendor_uid = $request->vendor_uid;
        $post->staff_id = Auth::guard('admin')->user()->id;
        $post->notes = $request->notes;
        $post->api_name = $request->api_name;
        $post->status = 0;        
        $post->save();

        // Deactive Previous API
        // $prePosts = Metawhatsappapi::where('id','!=',$post->id)->get();
        // if(isset($prePosts)){
        //     foreach($prePosts as $prePost){
        //         $prePost->status = false;
        //         $prePost->save();
        //     }
        // }

        return redirect()->back()->with('success','Meta Whatsapp API stored!');
    }

    public function edit(Request $request){
        $post = Metawhatsappapi::find($request->id);

        return response()->json($post);
    }
    
    public function checkMobileUnique(Request $request)
    {
        $query = Metawhatsappapi::where('mobile_no', $request->mobile_no);
    
        // Edit mode → ignore current record
        if (!empty($request->id)) {
            $query->where('id', '!=', $request->id);
        }
    
        return response()->json([
            'exists' => $query->exists()
        ]);
    }
    
    
    public function getAssignto(Request $request) {
        $post = Metawhatsappapi::find($request->id);

        return response()->json($post);
    }

    public function getAssigntoStore(Request $request){
        $post = Metawhatsappapi::find($request->edit_id);

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

        $post = Metawhatsappapi::whereRaw("FIND_IN_SET(?,api_assign_to)", [$name])->where('id','!=',$id)->count();

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
        $post = Metawhatsappapi::find($request->edit_id);
        $post->mobile_no = $request->mobile_no;
        $post->api_access_token = $request->api_access_token;
        $post->api_base_url = $request->api_base_url;
        $post->vendor_uid = $request->vendor_uid;
        $post->notes = $request->notes;
        $post->api_name = $request->api_name;
        $post->save();

        return redirect()->back()->with('success','Meta Whatsapp API updated!');
    }

    public function getStatus(Request $request){
        $post = Metawhatsappapi::find($request->id);

        return response()->json($post);
    }

    public function statusUpdt(Request $request){
        $post = Metawhatsappapi::find($request->statusID);
        $post->status = $request->status;
        $post->save();
        // Deactive when status is 1
        // if($request->status == 1){
        //     $prePosts = Metawhatsappapi::where('id','!=',$post->id)->get();
        //     if(isset($prePosts)){
        //         foreach($prePosts as $prePost){
        //             $prePost->status = false;
        //             $prePost->save();
        //         }
        //     }
        // }

        return redirect()->back()->with('success','Status updated!');
    }


      public function delete(Request $request){
        $post = Metawhatsappapi::find($request->delID);

        $post->delete();

        return redirect()->back()->with('success','Meta Whatsapp API deleted!');
    }
}
