<?php

namespace App\Http\Controllers;

use App\AdminModel\Ipaddress;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class IpaddressController extends Controller
{
    public function index()
    {

        // $posts = Ipaddress::where('expired_at','<',date('Y-m-d H:i:s'))->get();

        // dd($posts);

        $pageConfigs = ['pageHeader' => false];
        return view('content.setting.ipaddress.index',['pageConfigs' => $pageConfigs,]);
    }

    public function indexJson(Request $request)
    {
        $cont = Ipaddress::all();
        $data['data'] = $cont;
        return response()->json($data);
    }

    public function store(Request $request)
    {
        // $new_time = date('Y-m-d H:i:s',strtotime('+'.$hours.' hours'));
        // dd($new_time);
        $post = new Ipaddress();
        $post->ip_address = $request->ip_address;
        $post->hours = $request->hoursIP;
        if ($request->hoursIP != '') {
            $post->expired_at = date('Y-m-d H:i:s',strtotime('+'.$request->hoursIP.' hours'));
        }
        $post->user_id = Auth::user()->user_id;
        $post->save();

        Session::flash('success','IP Address added!');
        return redirect()->back();

    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $post = Ipaddress::find($id);
        return response()->json($post);
    }

    public function update(Request $request)
    {
        $post = Ipaddress::find($request->editIPAdd);
        $post->ip_address = $request->ip_address;
        $post->hours = $request->hoursIP;
        if ($request->hoursIP != '') {
            $post->expired_at = date('Y-m-d H:i:s',strtotime('+'.$request->hoursIP.' hours'));
        }else{
            $post->expired_at = null;
        }

        $post->save();

        Session::flash('success','IP Address updated!');
        return redirect()->back();
    }

    public function delete(Request $request)
    {
        $post = Ipaddress::find($request->deleteID);
        $post->delete();

        Session::flash('success','IP Address deleted!');

        return redirect()->back();
    }

    public function checkIP(Request $request)
    {
        $ipadd = $request->ip_address;
        $post = Ipaddress::where('ip_address','=',$ipadd)->count();

        if ($post == 0) {
            echo "true";
        } else {
            echo "false";
        }
        
    }

    public function checkIP2(Request $request)
    {
        $ipadd = $request->ip_address;
        $id = $request->edit_id;
        $post = Ipaddress::where('ip_address','=',$ipadd)->where('id','!=',$id)->count();

        if ($post == 0) {
            echo "true";
        } else {
            echo "false";
        }
        
    }
}
