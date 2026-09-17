<?php

namespace App\Http\Controllers;

use App\Models\Allcontact;
use Illuminate\Http\Request;
use DB;
use App\Models\Unsubscribedata;
use App\Models\Contactp;
use App\Models\Contactplus;
class UnsubscribedynamicurlController extends Controller
{
    public function index(Request $request,$randomgenerate,$contactid){
        $post = Unsubscribedata::where('contactp_id','=',$contactid)->where('random_string','=',$randomgenerate)->where('unsubscribe','=',1)->first();

        return view('whatsapp.unsubscribe',['post' => $post]);
    }

    public function index2(Request $request,$randomgenerate,$allcontactID){
        $post = Unsubscribedata::where('allcontact_id','=',$allcontactID)->where('random_string','=',$randomgenerate)->where('unsubscribe','=',1)->first();

        // $post = Unsubscribedata::where('random_string','=',$randomgenerate)->first();

        // dd($post);

        return view('whatsapp.unsubscribe2',['post' => $post]);
    }

    public function stopWhatsapp(Request $request,$randstring,$id) {
        $post = Contactplus::find($id);
        // $post->optinout = false;
        $post->unsubscribe_date = date('Y-m-d h:i');
        $post->save();

        return view('whatsapp.unsubscribesucc');
    }

    public function stopWhatsapp2(Request $request,$randstring,$id){
        $post = Allcontact::find($id);
        $post->optinout = false;
        $post->save();

        return view('whatsapp.unsubscribesucc');
    }



}
