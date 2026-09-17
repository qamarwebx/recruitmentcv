<?php

namespace App\Http\Controllers;

use App\AdminModel\Candmusaned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class MusanedStageController extends Controller
{
    public function index(){
        $pageConfigs = ['pageHeader' => false];
        return view('/content/service-master/musanedstage',['pageConfigs' => $pageConfigs]);
    }

    public function indexlist(Request $request){
        $post = Candmusaned::all();
        $data['data'] = $post;
        return response()->json($data);
    }

    public function store(Request $request){
        $post = new Candmusaned();
        $post->name = $request->input('name');
        $post->user_id = Auth::user()->user_id;
        $post->save();
        Session::flash('success','musaned status created!');
        return redirect()->back();
    }

    public function edit(Request $request){
        $post = Candmusaned::find($request->id);
        return response()->json($post);
    }

    public function update(Request $request){
        $post = Candmusaned::find($request->id);
        $post->name = $request->name;
        $post->save();
        Session::flash('success','musaned status Updated!');
        return redirect()->back();
    }
}
