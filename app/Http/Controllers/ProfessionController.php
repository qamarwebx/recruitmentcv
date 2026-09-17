<?php

namespace App\Http\Controllers;

use App\Models\Adminpermission;
use App\Models\Candidate;
use App\Models\Profession;
use App\Models\Visadetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfessionController extends Controller
{
    public function index()
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        return view('admin.profession.index',['perm' => $permission]);
    }

    public function indexjson(Request $request)
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $posts = Profession::orderBy('id','DESC')->get();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_profession == 1){
                $posts = Profession::orderBy('id','DESC')->get();
            }else{
                $posts = Profession::where('admin_id','=',Auth::guard('admin')->user()->id)->orderBy('id','DESC')->get();
            }
        }
        
        $data['data'] = $posts;
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $post = new Profession();
        $post->eng_name = $request->eng_name;
        $post->ar_name = $request->ar_name;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back();
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $post = Profession::find($id);
        return response()->json($post);
    }

    public function update(Request $request)
    {
        $id = $request->edit_id;
        $post = Profession::find($id);
        $post->eng_name = $request->eng_name;
        $post->ar_name = $request->ar_name;
        $post->save();

        return redirect()->back();
    }

    public function checkengname(Request $request)
    {
        $eng_name = $request->eng_name;
        $post = Profession::where('eng_name','=',$eng_name)->count();
        // if ($post == 0) {
        //     echo "true";
        // } else {
        //     echo "false";
        // }

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function checkarname(Request $request)
    {
        $ar_name = $request->ar_name;
        $post = Profession::where('ar_name','=',$ar_name)->count();
        // if ($post == 0) {
        //     echo "true";
        // } else {
        //     echo "false";
        // }

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edcheckengname(Request $request)
    {
        $id = $request->id;
        $eng_name = $request->eng_name;
        $post = Profession::where('id','!=',$id)->where('eng_name','=',$eng_name)->count();
        // if ($post == 0) {
        //     echo "true";
        // } else {
        //     echo "false";
        // }

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edcheckarname(Request $request)
    {
        $id = $request->id;
        $ar_name = $request->ar_name;
        $post = Profession::where('id','!=',$id)->where('ar_name','=',$ar_name)->count();
        // if ($post == 0) {
        //     echo "true";
        // } else {
        //     echo "false";
        // }

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function checkDelProf(Request $request){
        $data1 = Candidate::where('proff_id','=',$request->id)->count();
        $data2 = Visadetails::where('proff_id','=',$request->id)->count();

        if ($data1 > 0 || $data2 > 0 ) {
            return response()->json('1');
        } else {
            
        }
    }

    public function deleteProf(Request $request){
        $post = Profession::find($request->proff_ids);

        $post->delete();

        return redirect()->back()->with('success','Profession Deleted!');
    }
}
