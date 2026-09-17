<?php

namespace App\Http\Controllers;

use App\AdminModel\CourierServiceName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class CourierServiceNameController extends Controller
{
    public function index()
    {
        $pageConfigs = ['pageHeader' => false];
        return view('content.service-master.couriersn',['pageConfigs' => $pageConfigs]);
    }

    public function indexJson(Request $request)
    {
        $posts = CourierServiceName::all();
        $data['data'] = $posts;
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $post = new CourierServiceName();
        $post->name = $request->name;
        $post->user_id = Auth::user()->user_id;
        $post->save();
        Session::flash('success','Courier service name added!');
        return redirect()->back();
    }

    public function edit(Request $request)
    {
        $post = CourierServiceName::find($request->id);
        return response()->json($post);
    }

    public function update(Request $request)
    {
        $post = CourierServiceName::find($request->id);
        $post->name = $request->name;
        $post->user_id = Auth::user()->user_id;
        $post->save();
        Session::flash('success','Courier service name updated!');
        return redirect()->back();
    }

    public function delete(Request $request)
    {

    }
}
