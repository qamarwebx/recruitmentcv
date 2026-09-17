<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contactp;
use App\Models\Contactplus;

class CheckExistanceController extends Controller
{
    public function contactmobileexists(Request $request){
        $mobileNo = $request->mobile_no;
        $edit_id = $request->edit_id;
        if ($edit_id != '') {
            $checkNo = Contactplus::where(function($query) use($mobileNo){
                $query->orwhere('prim_contact','=',$mobileNo);
                $query->orwhere('sec_contact','=',$mobileNo);
                $query->orwhere('contact3','=',$mobileNo);
                $query->orwhere('contact4','=',$mobileNo);
                $query->orwhere('contact5','=',$mobileNo);
                $query->orwhere('contact6','=',$mobileNo);
            })->where('id','!=',$edit_id)->count();
        } else {
            $checkNo = Contactplus::where(function($query) use($mobileNo){
                $query->orwhere('prim_contact','=',$mobileNo);
                $query->orwhere('sec_contact','=',$mobileNo);
                $query->orwhere('contact3','=',$mobileNo);
                $query->orwhere('contact4','=',$mobileNo);
                $query->orwhere('contact5','=',$mobileNo);
                $query->orwhere('contact6','=',$mobileNo);
            })->count();
        }

        if ($checkNo > 0) {
            $data = [
                'response_status' => 1,
                'response_msg' => $mobileNo.' Mobile No is already exists!'
            ];
        } else {
            $data = [
                'response_status' => 0,
                'response_msg' => ''
            ];
        }




        return response()->json($data);

    }
}
