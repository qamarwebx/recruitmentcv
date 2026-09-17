<?php

namespace App\Http\Controllers;

use App\AdminModel\Numberadd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;

class MobileaddController extends Controller
{
    public function index(Request $request)
    {
        $pageConfigs = ['pageHeader' => false];
        
        return view('content.setting.mobile.index',['pageConfigs' => $pageConfigs]);
    }

    public function indexJson(Request $request)
    {
        $post = DB::table('numberadds as mob')
            ->leftJoin('users as user','mob.assoc_user_id','=','user.user_id')
            ->select('mob.*','user.name as uname')
            ->get();

        $data['data'] = $post;
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $post = new Numberadd();
        $post->number = $request->number;
        $post->assoc_user_id = $request->assoc_user_id;
        $post->service_provider = $request->service_provider;
        $post->user_id = Auth::user()->user_id;
        $post->save();

        Session::flash('success','mobile added!');
        return redirect()->back();
    }

    public function edit(Request $request)
    {
        $post = Numberadd::find($request->id);
        return response()->json($post);
    }

    public function update(Request $request)
    {
        $post = Numberadd::find($request->editID);
        $post->number = $request->number;
        $post->assoc_user_id = $request->assoc_user_id;
        $post->service_provider = $request->service_provider;
        $post->save();

        Session::flash('success','mobile number updated!');
        return redirect()->back();
    }

    public function checknumber(Request $request)
    {
        $number = $request->number;
        $post = Numberadd::where('number','=',$number)->count();

        if ($post == 0) {
            echo "true";
        } else {
            echo "false";
        }
    }

    public function checknumber2(Request $request)
    {
        $number = $request->number;
        $id = $request->edit_id;
        $post = Numberadd::where('number','=',$number)->where('id','!=',$id)->count();

        if ($post == 0) {
            echo "true";
        } else {
            echo "false";
        }
    }

    public function delMobile(Request $request){
        $post = Numberadd::find($request->deleteID);
        $post->delete();

        return redirect()->back()->with('success','Mobile number deleted!');
    }



}
