<?php

namespace App\Http\Controllers;

use App\AdminModel\Cflight;
use Illuminate\Http\Request;
use Session;

class CrmFlightStageController extends Controller
{
    public function index(){
        $pageConfigs = ['pageHeader' => false];
        return view('/content/service-master/flightstage',['pageConfigs' => $pageConfigs]);
    }

    public function indexlist(Request $request){
        $post = Cflight::all();
        $data['data'] = $post;
        return response()->json($data);
    }

    public function store(Request $request){
        $post = new Cflight();
        $post->name = $request->input('name');
        $post->save();
        Session::flash('success','Flight status created!');
        return redirect()->back();
    }

    public function edit(Request $request){
        $post = Cflight::find($request->id);
        return response()->json($post);
    }

    public function update(Request $request){
        $post = Cflight::find($request->id);
        $post->name = $request->name;
        $post->save();
        Session::flash('success','Flight status Updated!');
        return redirect()->back();
    }
}
