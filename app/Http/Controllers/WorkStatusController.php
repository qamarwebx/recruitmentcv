<?php

namespace App\Http\Controllers;

use App\AdminModel\WorkStatus;
use Illuminate\Http\Request;
use Session;

class WorkStatusController extends Controller
{
    public function index()
    {
        $pageConfigs = ['pageHeader' => false];
        return view('/content/service-master/workstatuslist', ['pageConfigs' => $pageConfigs]);
    }

    public function indexlist(Request $request){
        $post = WorkStatus::all();
        $data['data'] = $post;
        return response()->json($data);
    }

    public function store(Request $request){
        $post = new WorkStatus();
        $post->name = $request->input('name');
        $post->save();
        Session::flash('success','Work Status created!');
        return redirect()->back();
    }

    public function edit(Request $request){
        $post = WorkStatus::find($request->id);
        return response()->json($post);
    }

    public function update(Request $request){
        $post = WorkStatus::find($request->id);
        $post->name = $request->name;
        $post->save();
        Session::flash('success','Work Status Updated!');
        return redirect()->back();
    }
}
