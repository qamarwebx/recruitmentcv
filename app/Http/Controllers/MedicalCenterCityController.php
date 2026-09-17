<?php

namespace App\Http\Controllers;

use App\Medicalcentercity;
use App\Admin\Medicalcenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalCenterCityController extends Controller
{
    public function index(){
        $pageConfigs = ['pageHeader' => false];
        return view('content.service-master.medicalcity.index',['pageConfigs' => $pageConfigs]);
    }

    public function indexJson(Request $request){

        $post = Medicalcentercity::orderBy('name')->get();
        $data['data'] = $post;
        return response()->json($data);
    }

    public function store(Request $request){
        $post = new Medicalcentercity();
        $post->name = $request->name;
        $post->user_id = Auth::user()->user_id;
        $post->save();

        return redirect()->back()->with('success','Medical center city added!');
    }

    public function edit(Request $request){
        $id = $request->id;
        $post = Medicalcentercity::find($id);

        return response()->json($post);
    }

    public function update(Request $request){
        $post = Medicalcentercity::find($request->edit_id);
        $post->name = $request->name;
        $post->save();

        return redirect()->back()->with('success','Medical Center city updated!');
    }

    public function getDeldet(Request $request){
        $id = $request->id;
        $chekMedicalc = Medicalcenter::where('city_id','=',$id)->count();
        if($chekMedicalc > 0){
            $data = [
                'status' => 1
            ];
        }else{
            $data = [
                'status' => 2
            ];
        }

        return response()->json($data);

    }

    public function deleteCity(Request $request){
        $id = $request->id;
        $post = Medicalcentercity::find($id);
        $post->delete();

        return redirect()->back()->with('success','medical center city deleted!');
    }
}
