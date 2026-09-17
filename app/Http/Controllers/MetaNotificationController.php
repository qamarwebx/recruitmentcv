<?php

namespace App\Http\Controllers;

use App\Models\Adminpermission;
use App\Models\Basepathstatus;
use App\Models\Metanotification;
use App\Models\Metawhatsapptemplate;
use App\Models\Personaliseclass;
use App\Models\Staticmetanotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MetaNotificationController extends Controller
{
    public function index(){
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        $personalises = Personaliseclass::orderBy('name')->get();
        return view('admin.notification.index',compact('permission','personalises'));
    }

    public function indexJson(Request $request){
        $post = DB::table('metanotifications')->orderBy('id','DESC')->get();


        $data['data'] = $post;
        return response()->json($data);
    }

    public function store(Request $request){
        //28-07-2025
        $basepathstatus = Basepathstatus::first();
        if ($request->has('photo')) {
            $file = $request->file('photo');
            $name = $file->getClientOriginalName();
            // remove space from image
            $filename_ren = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ","_",$filename_ren);
            $new_file = $repspfilename.'.'.$fileext_ren;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/template',$new_file);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/template',$new_file);
            }

            $temp_file = $new_file;
        } else {
            $temp_file = '';
        }

        $post = new Metanotification();

        $post->template_name = $request->template_name;
        $post->meta_template_name = $request->meta_template_name;

        if ($request->meta_field_name) {
            $post->meta_field_name = implode(",",$request->meta_field_name);
        }
        if ($request->assign_var_name) {
            $post->assign_var_name = implode(",",$request->assign_var_name);
        }

        $post->meta_message_body = $request->msg_whatsapp;
        $post->status = false;
        $post->api_for_template = $request->api_for_template;
        $post->staff_id = Auth::guard('admin')->user()->id;
        $post->meta_url_type = $request->meta_url_type;
        $post->static_url = $request->static_url;
        $post->document_name = $request->document_name;
        $post->whatsapp_file = $temp_file;
        $post->save();

        return redirect()->back();

        // New Code for Meta Notification
        // $basepathstatus = Basepathstatus::first();

        // Get template details
        // $gettemp = Metawhatsapptemplate::find($request->metatemp_id);

        // $post = new Metanotification();
        // $post->template_for = $request->template_for;
        // $post->metatemp_id = $request->metatemp_id;
        // $post->template_name = $request->meta_template_name;
        // $post->meta_template_name = $gettemp->template_name;
        // $post->meta_message_body = $request->msg_whatsapp;
        // $post->staff_id = Auth::guard('admin')->user()->id;
        // $post->save();

        // return redirect()->back();
    }

    public function edit(Request $request){
        $post = Metanotification::find($request->id);
        return response()->json($post);
    }

    public function update(Request $request){

        $post = Metanotification::find($request->edit_id);

        $basepathstatus = Basepathstatus::first();

        // Get template details
        $gettemp = Metawhatsapptemplate::find($request->metatemp_id);

        if ($request->has('photo')) {
            $file = $request->file('photo');
            $name = $file->getClientOriginalName();
            // remove space from image
            $filename_ren = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ","_",$filename_ren);
            $new_file = $repspfilename.'.'.$fileext_ren;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/template',$new_file);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/template',$new_file);
            }

            $temp_file = $new_file;
        } else {
            $temp_file = $post->whatsapp_file;
        }



        $post->template_name = $request->template_name;
        $post->meta_template_name = $request->meta_template_name;

        if ($request->meta_field_name) {
            $post->meta_field_name = implode(",",$request->meta_field_name);
        }
        if ($request->assign_var_name) {
            $post->assign_var_name = implode(",",$request->assign_var_name);
        }


        $post->meta_message_body = $request->msg_whatsapp;
        $post->api_for_template = $request->api_for_template;
        $post->status = true;
        $post->meta_url_type = $request->meta_url_type;
        $post->static_url = $request->static_url;
        $post->document_name = $request->document_name;
        $post->whatsapp_file = $temp_file;
        $post->save();

        // $post->template_for = $request->template_for;
        // $post->metatemp_id = $request->metatemp_id;
        // $post->template_name = $request->meta_template_name;
        // $post->meta_template_name = $gettemp->template_name;
        // $post->meta_message_body = $request->msg_whatsapp;
        // $post->save();

        return redirect()->back()->with('success','Meta Notification Updated!');
    }

    public function getStatus(Request $request){
        $post = Metanotification::find($request->id);
        return response()->json($post);
    }

    public function statusUpdt(Request $request){

        // dd($request);
        $post = Metanotification::find($request->tempchstID);
        // if ($request->status == 1) {
        //     // find other and deactive it
        //     $findcheck = Metanotification::where('status',1)->where('id','!=',$request->tempchstID)->where('template_for','=',$post->template_for)->whereNotNull('template_for')->get();

        //     if ($findcheck->count() > 0) {
        //         foreach ($findcheck as $findchec) {
        //             $findchec->status = false;
        //             $findchec->save();
        //         }
        //     }

        // }

        $post->status = $request->status;
        $post->save();


        return redirect()->back()->with('success','Meta Notification status updated!');
    }


    public function indexStatic(){
        return view('admin.notification.static.index');
    }

    public function indexJsonStatic(Request $request){
        $post = DB::table('staticmetanotifications')->orderBy('id','DESC')->get();

        $data['data'] = $post;

        return response()->json($data);
    }

    public function storeStatic(Request $request){
        $post = new Staticmetanotification();
        $post->meta_template_name = $request->meta_template_name;
        $post->otp_number = $request->otp_number;
        $post->staff_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Notification created!');
    }

    public function StaticEdit(Request $request){
        $post = Staticmetanotification::find($request->id);

        return response()->json($post);
    }

    public function updateStatic(Request $request){
        $post = Staticmetanotification::find($request->edit_id);
        $post->meta_template_name = $request->meta_template_name;
        $post->otp_number = $request->otp_number;
        $post->staff_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Notification updated!');
    }
}
