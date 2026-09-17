<?php

namespace App\Http\Controllers;

use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EducationController extends Controller
{
    public function index(){
        return view('admin.education.index');
    }

    public function indexJson(Request $request){
        $post = DB::table('education as education')
            ->leftJoin('admins as admin','education.admin_id','=','admin.id')
            ->select('education.*','admin.name as uname')
            ->orderBy('education.name','ASC')
            ->get();

        $data['data'] = $post;

        return response()->json($data);

    }

    public function store(Request $request){
        $post = new Education();
        $post->name = $request->name;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Education added!');
    }

    public function checkEducation(Request $request){
        $name = $request->name;

        if(isset($request->id) && $request->id != ''){
            $post = Education::where('name','=',$name)->where('id','!=',$request->id)->count();

            if($post == 0){
                $isAvailable = 'true';
            }else{
                $isAvailable = 'false';
            }
    
            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }else{
            $post = Education::where('name','=',$name)->count();

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

    public function edit(Request $request){
        $post = Education::find($request->id);

        return response()->json($post);
    }


    public function update(Request $request){
        $post = Education::find($request->edit_id);
        $post->name = $request->name;
        $post->save();

        return redirect()->back()->with('success','Education updated!');
    }


}
