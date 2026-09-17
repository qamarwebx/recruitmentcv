<?php

namespace App\Http\Controllers;

use App\Models\Adminpermission;
use App\Models\Candidate;
use App\Models\Placeofissue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlaceofIssueController extends Controller
{
    public function index()
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        return view('admin.poi.index',['perm' => $permission]);
    }

    public function indexjson(Request $request)
    {

        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
           $post = Placeofissue::orderBy('id','DESC')->get();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_placeofissue == 1){
               $post = Placeofissue::orderBy('id','DESC')->get();
            }else{
                $post = Placeofissue::where('admin_id','=',Auth::guard('admin')->user()->id)->orderBy('id','DESC')->get();
            }
        }

        

        $data['data'] = $post;

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $post = new Placeofissue();
        $post->name = $request->name;
        $post->arname = $request->arname;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back();
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $post = Placeofissue::find($id);
        return response()->json($post);
    }

    public function update(Request $request)
    {
        $post = Placeofissue::find($request->edit_id);
        $post->name = $request->name;
        $post->arname = $request->arname;
        $post->save();

        return redirect()->back()->with('success','Place of issue updated!');
    }


    public function checkname(Request $request)
    {   
        $post = Placeofissue::where('name','=',$request->name)->count();

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
        $post = Placeofissue::where('arname','=',$request->arname)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edcheckname(Request $request)
    {
        $post = Placeofissue::where('name','=',$request->name)->where('id','!=',$request->id)->count();

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
        $post = Placeofissue::where('arname','=',$request->arname)->where('id','!=',$request->id)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function delCheck(Request $request){
        $data1 = Candidate::where('poi','=',$request->id)->count();
        

        if ($data1 > 0) {
            return response()->json('1');
        } else {
            
        }
    }

    public function poidelete(Request $request){
        $post = Placeofissue::find($request->proff_ids);
        $post->delete();

        return redirect()->back()->with('success','Place of issue deleted!');
    }
}
