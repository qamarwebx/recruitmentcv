<?php

namespace App\Http\Controllers;

use App\Models\Adminpermission;
use App\Models\Autometanotification;
use App\Models\Metawhatsapptemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AutoNotificationController extends Controller
{
    public function index(){
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        return view('admin.auto_meta_notification.index',compact('permission'));
    }

    public function indexJson(Request $request)  {
        $post = DB::table('autometanotifications as autometanotification')->orderBy('id','DESC')->get();

        $data['data'] = $post;
        return response()->json($data);
    }

    public function edit(Request $request) {
        $post = Autometanotification::find($request->id);
        return response()->json($post);
    }

    public function getStatus(Request $request){
        $post = Autometanotification::find($request->id);
        return response()->json($post);
    }

    public function store(Request $request) {

        $gettemp = Metawhatsapptemplate::find($request->metatemp_id);

        $post = new Autometanotification();
        $post->template_for = $request->template_for;
        $post->metatemp_id = $request->metatemp_id;
        $post->template_name = $request->meta_template_name;
        $post->meta_template_name = $gettemp->template_name;
        $post->meta_message_body = $request->msg_whatsapp;
        $post->staff_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Meta Notification added!');
    }

    public function  update(Request $request) {
        $post = Autometanotification::find($request->edit_id);

        // Get template details
        $gettemp = Metawhatsapptemplate::find($request->metatemp_id);

        $post->template_for = $request->template_for;
        $post->metatemp_id = $request->metatemp_id;
        $post->template_name = $request->meta_template_name;
        $post->meta_template_name = $gettemp->template_name;
        $post->meta_message_body = $request->msg_whatsapp;
        $post->save();

        return redirect()->back()->with('success','Meta Notification Updated!');

    }

    public function statusUpdt(Request $request){
        $post = Autometanotification::find($request->tempchstID);

        $post->status = $request->status;
        $post->save();


        return redirect()->back()->with('success','Meta Notification status updated!');
    }
}
