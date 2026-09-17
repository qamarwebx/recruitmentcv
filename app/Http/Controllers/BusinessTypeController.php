<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Businesstype;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BusinessTypeController extends Controller
{
    public function index(){

        return view('admin.businesstype.index');
    }


    public function indexJson(Request $request){
        $post = DB::table('businesstypes as businesstype')
            ->leftjoin('admins as admin','admin.id','=','businesstype.admin_id')
            ->select('businesstype.*','admin.name as uname')
            ->orderBy('businesstype.name')
            ->get();

        $data['data'] = $post;
        return response()->json($data);
    }

    public function store(Request $request){
        $post = new Businesstype();
        $post->name = $request->name;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Business Type Added!');
    }

    public function edit(Request $request){
        $post = Businesstype::find($request->id);

        return response()->json($post);
    }

    public function update(Request $request){
        $post = Businesstype::find($request->edit_id);
        $post->name = $request->name;
        $post->save();

        return redirect()->back()->with('success','Business Type updated!');
    }

    public function checkname(Request $request){
        $name = $request->name;

        if(isset($request->id) && $request->id != ''){
            $post = Businesstype::where('name','=',$name)->where('id','!=',$request->id)->count();

            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));

        }else{
            $post = Businesstype::where('name','=',$name)->count();
            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));

        }
    }
}
