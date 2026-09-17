<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MetaWhatsappLogController extends Controller
{
    public function index(){
        return view('admin.metawhatsapplog.index');
    }

    public function indexJson(Request $request){
        $post = DB::table('metawhatsapplogs')->orderBy('id','DESC')->get();

        $data['data'] = $post;

        return response()->json($data);

    }
}
