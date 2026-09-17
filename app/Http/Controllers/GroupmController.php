<?php

namespace App\Http\Controllers;

use App\AdminModel\AllContact;
use App\Groupm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupmController extends Controller
{
    public function index(){
        $pageConfigs = ['pageHeader' => false];
        return view('content.crm.group.index',['pageConfigs' => $pageConfigs]);
    }

    public function indexJson(Request $request){
        $posts = DB::table('groupms as groupm')
            ->leftJoin('users as user','user.user_id','=','groupm.user_id')
            ->select('groupm.*','user.name as uname')
            ->orderBy('name')
            ->get();

        $data['data'] = $posts;
        return response()->json($data); 
    }

    public function store(Request $request){
        $post = new Groupm();
        $post->name = $request->name;
        $post->max_limit = $request->max_limit;
        $post->user_id = Auth::user()->user_id;
        $post->save();

        return redirect()->back()->with('success','Group name created!');
    }

    public function edit(Request $request){
        $post = Groupm::find($request->id);

        return response()->json($post);
    }

    public function update(Request $request){
        $post = Groupm::find($request->group_id);
        $post->name = $request->name;
        $post->max_limit = $request->max_limit;
        $post->save();

        return redirect()->back()->with('success','Group name updated!');
    }

    public function checkName(Request $request){
        $name = $request->name;

        if ($request->id != '') {
            $post = Groupm::where('name','=',$name)->where('id','!=',$request->id)->count();
            if($post == 0){
                echo "true";
            }else{
                echo "false";
            }
        } else {
            $post = Groupm::where('name','=',$name)->count();
            if($post == 0){
                echo "true";
            }else{
                echo "false";
            }
        }
        


    }

    public function getDatac(Request $request){
        $id = $request->id;
        $checkP = AllContact::where('group_id','=',$id)->count();
        
        if ($checkP > 0) {
            return response()->json('1');
        } else {
            
        }
        
    }

    public function deleteGrp(Request $request){
        $post = Groupm::find($request->del_grp_id);
        $post->delete();

        return redirect()->back()->with('success','Group name deleted!');
    }
}
