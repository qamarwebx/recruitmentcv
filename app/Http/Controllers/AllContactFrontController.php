<?php

namespace App\Http\Controllers;

use App\AdminModel\AllContact;
use Illuminate\Http\Request;

class AllContactFrontController extends Controller
{
    public function contactList($id,$random){
        $post = AllContact::find($id);
        return view('front.contactform',['post' => $post,'random' => $random]);
    }

    public function contactListStr(Request $request,$id){
        $post = AllContact::find($id);
        $post->full_name = $request->full_name;
        $post->mobile_no = $request->mobile_no;
        $post->email = $request->email;
        $post->city = $request->city;
        $post->country_id = $request->country_id;
        $post->save();

        return redirect()->back('success','Thank you '.$request->full_name.' update you on whatsapp about new vacancy and services.');
    }
}
