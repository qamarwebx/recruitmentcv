<?php

namespace App\Http\Controllers;

use App\AdminModel\Cmedical;
use Illuminate\Http\Request;
use Session;
class CrmcMedicalStageController extends Controller
{
    public function index(){
        $pageConfigs = ['pageHeader' => false];
        return view('/content/service-master/medicalstatus',['pageConfigs' => $pageConfigs]);
    }

    public function indexlist(Request $request){
        $post = Cmedical::all();
        $data['data'] = $post;
        return response()->json($data);
    }

    public function store(Request $request){
        $post = new Cmedical();
        $post->name = $request->input('name');
        $post->save();
        Session::flash('success','Medical status created!');
        return redirect()->back();
    }

    public function edit(Request $request){
        $post = Cmedical::find($request->id);
        return response()->json($post);
    }

    public function update(Request $request){
        $post = Cmedical::find($request->id);
        $post->name = $request->name;
        $post->save();
        Session::flash('success','Medical status Updated!');
        return redirect()->back();
    }


}
