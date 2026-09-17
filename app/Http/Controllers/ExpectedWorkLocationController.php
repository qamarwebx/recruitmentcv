<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expecworkcity;
use Illuminate\Support\Facades\Auth;

class ExpectedWorkLocationController extends Controller
{
    public function index(){
        return view('admin.expworkloc.index');
    }

    public function indexjson(Request $request){
        $post = Expecworkcity::orderBy('name')->get();

        $data['data'] = $post;
        return response()->json($data);
    }

    public function store(Request $request){
        $post = new Expecworkcity();
        $post->name = $request->name;
        $post->arname = $request->arname;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Expected work location added!');
    }

    public function edit(Request $request){
        $post = Expecworkcity::find($request->id);
        return response()->json($post);
    }

    public function update(Request $request){
        $post = Expecworkcity::find($request->edit_id);
        $post->name = $request->name;
        $post->arname = $request->arname;
        $post->save();

        return redirect()->back()->with('success','Expected work location updated!');
    }

    public function checkname(Request $request){
        

        if($request->id != ''){
            $post = Expecworkcity::where('name','=',$request->name)->where('id','!=',$request->id)->count();
            if($post == 0){
                $isAvailable = 'true';
            }else{
                $isAvailable = 'false';
            }
    
            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }else{
            $post = Expecworkcity::where('name','=',$request->name)->count();
            if($post == 0){
                $isAvailable = 'true';
            }else{
                $isAvailable = 'false';
            }
    
            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }

        
    }

    public function checkarname(Request $request){
        

        if($request->id != ''){
            $post = Expecworkcity::where('arname','=',$request->arname)->where('id','!=',$request->id)->count();
            if($post == 0){
                $isAvailable = 'true';
            }else{
                $isAvailable = 'false';
            }
    
            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }else{
            $post = Expecworkcity::where('arname','=',$request->arname)->count();
            if($post == 0){
                $isAvailable = 'true';
            }else{
                $isAvailable = 'false';
            }
    
            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }

        
    }
}
