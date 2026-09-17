<?php

namespace App\Http\Controllers;

use App\Models\Religion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReligionController extends Controller
{
    public function index(){
        return view('admin.religion.index');
    }

    public function indexJson(Request $request) {
        $post = DB::table('religions as religion')
            ->leftJoin('admins as admin','admin.id','=','religion.admin_id')
            ->select('religion.*','admin.name as uname')
            ->orderBy('religion.name','ASC')
            ->get();

        $data['data'] = $post;
        return response()->json($data);
    }

    public function store(Request $request){
        $post = new Religion();
        $post->name = $request->name;
        if($request->name == 'Muslim' || $request->name == 'muslim' || $request->name == 'moslim'){
            $post->arbname = 'مسلم';
        }else{
            $post->arbname = 'غير مسلم';
        }
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Religion added!');
    }

    public function edit(Request $request){
        $post = Religion::find($request->id);

        return response()->json($post);
    }

    public function update(Request $request){
        $post = Religion::find($request->edit_id);
        $post->name = $request->name;
        if($request->name == 'Muslim' || $request->name == 'muslim' || $request->name == 'moslim'){
            $post->arbname = 'مسلم';
        }else{
            $post->arbname = 'غير مسلم';
        }
        $post->save();
        return redirect()->back()->with('success','Religion updated!');
    }

    public function checkreligion(Request $request){
        $name = $request->name;

        if(isset($request->id) && $request->id != ''){
            $post = Religion::where('name','=',$name)->where('id','!=',$request->id)->count();

            if($post == 0){
                $isAvailable = 'true';
            }else{
                $isAvailable = 'false';
            }
    
            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }else{
            $post = Religion::where('name','=',$name)->count();

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
