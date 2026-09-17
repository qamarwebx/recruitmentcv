<?php

namespace App\Http\Controllers;

use App\Models\Allowedipaddress;
use App\Models\UnauthorizedLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IpLoginController extends Controller
{
    public function index(){
        $posts = Allowedipaddress::where('status','1')->get();

        return view('admin.iplogincontroller.index',compact('posts'));
    }

    public function store(Request $request){
        $post = new Allowedipaddress();
        $post->ip_address = $request->ip_address;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Allowed IP Address added!');
    }

    public function edit(Request $request) {
        $post = Allowedipaddress::find($request->id);

        return response()->json($post);
    }

    public function update(Request $request){
        $post = Allowedipaddress::find($request->edit_id);
        $post->ip_address = $request->ip_address;
        $post->save();

        return redirect()->back()->with('success','Allowed IP Address updated!');
    }

    public function delete(Request $request) {
        $post = Allowedipaddress::find($request->delete_id);

        $post->delete();

        return redirect()->back()->with('success','Allowed IP Address deleted!');
    }

    public function  unauthorizelist() {
        $posts = UnauthorizedLogin::orderBy('id','DESC')->get();
        return view('admin.iplogincontroller.unauthorizelist',compact('posts'));
    }
}
