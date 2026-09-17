<?php

namespace App\Http\Controllers;

use App\AdminModel\FollowUpStage;
use Illuminate\Http\Request;
use Session;

class FollowupStageController extends Controller
{
    public function index()
    {
        $pageConfigs = ['pageHeader' => false];
        
        return view('/content/service-master/followupstage', ['pageConfigs' => $pageConfigs]);
    }

    public function indexlist(Request $request){
        $post = FollowUpStage::all();
        $data['data'] = $post;
        return response()->json($data);
    }

    public function store(Request $request){
        $post = new FollowUpStage();
        $post->name = $request->input('name');
        $post->save();
        Session::flash('success','Follow Up Stage created!');
        return redirect()->back();
    }

    public function edit(Request $request){
        $post = FollowUpStage::find($request->id);
        return response()->json($post);
    }

    public function update(Request $request){
        $post = FollowUpStage::find($request->id);
        $post->name = $request->name;
        $post->save();
        Session::flash('success','Follow Up Stage Updated!');
        return redirect()->back();
    }
}
