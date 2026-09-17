<?php

namespace App\Http\Controllers;

use App\Models\Adminpermission;
use App\Models\Mailcredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MailConfigController extends Controller
{
    public function index()
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        return view('admin.mailsetup.index',['perm' => $permission]);
    }

    public function indexJson(Request $request)
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $post = Mailcredential::all();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_mailsetup == 1){
                $post = Mailcredential::all();
            }else{
                $post = Mailcredential::where('admin_id','=',Auth::guard('admin')->user()->id)->get();
            }
        }


        $data['data'] = $post;

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $post = new Mailcredential();
        $post->mail_mailer = $request->mail_mailer;
        $post->mail_host = $request->mail_host;
        $post->mail_port = $request->mail_port;
        $post->mail_username = $request->mail_username;
        $post->mail_password = $request->mail_password;
        $post->mail_encryption = $request->mail_encryption;
        $post->mail_from_address = $request->mail_from_address;
        $post->mail_from_name = $request->mail_from_name;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        // deactive other
        $deactives = Mailcredential::where('id','!=',$post->id)->get();
        foreach($deactives as $deactive){
            $deactive->status = false;
            $deactive->save();
        }

        return redirect()->back()->with('success','Mail setup details added!');
    }

    public function edit(Request $request)
    {
        $post = Mailcredential::find($request->id);

        return response()->json($post);
    }

    public function update(Request $request)
    {
        $post = Mailcredential::find($request->edit_id);

        $post->mail_mailer = $request->mail_mailer;
        $post->mail_host = $request->mail_host;
        $post->mail_port = $request->mail_port;
        $post->mail_username = $request->mail_username;
        $post->mail_password = $request->mail_password;
        $post->mail_encryption = $request->mail_encryption;
        $post->mail_from_address = $request->mail_from_address;
        $post->mail_from_name = $request->mail_from_name;
        $post->save();

        return redirect()->back()->with('success','Mail setup details updated!');
    }

    public function checkDelStatus(Request $request){
        $post = Mailcredential::where('id','=',$request->id)->where('status','=',1)->count();
        if ($post > 0) {
            return response()->json('1');
        } else {
            
        }
    }

    public function delete(Request $request){
        $post = Mailcredential::find($request->proff_ids);

        $post->delete();

        return redirect()->back()->with('success','Mail credential deleted!');
    }
}
