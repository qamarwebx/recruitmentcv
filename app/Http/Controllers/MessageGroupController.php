<?php

namespace App\Http\Controllers;

use App\AdminModel\Messagegroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageGroupController extends Controller
{
    public function index()
    {
        $pageConfigs = ['pageHeader' => false];
        return view('content.crm-master.groupmessage.index',['pageConfigs' => $pageConfigs]);
    }

    public function indexJson(Request $request)
    {
        $posts = DB::table('messagegroups as msg')
            ->leftjoin('users as staff','msg.user_id','=','staff.user_id')
            ->select('msg.*','staff.name as uname')
            ->get();

        $data['data'] = $posts;

        return response()->json($data);
    }
}
