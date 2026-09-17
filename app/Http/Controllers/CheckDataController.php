<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckDataController extends Controller
{
    public function checkvisaprofessionAdmin(Request $request){
        $post = Candidate::where('jobtype_id','=',$request->proff_id)->where('id','=',$request->cand_id)->count();

        if ($post == 0) {
            $isAvailable = 'false';
        } else {
            $isAvailable = 'true';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));   
    }

    public function checkvisaworklocationAdmin(Request $request){
        $post = Candidate::where('expwp_id','=',$request->wpcity_id)->where('id','=',$request->cand_id)->count();

        if ($post == 0) {
            $isAvailable = 'false';
        } else {
            $isAvailable = 'true';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function checkvisaembassyAdmin(Request $request){
        if ($request->embassy == 'Mumbai') {
            $embassy = 1;
        }elseif ($request->embassy == 'New Delhi') {
            $embassy = 2;
        }else{
            $embassy = '';
        }

        $post = Candidate::where('embassy_for','=',$embassy)->where('id','=',$request->cand_id)->count();

        if ($post == 0) {
            $isAvailable = 'false';
        } else {
            $isAvailable = 'true';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
        
    }

    public function checkvisapaymentAdmin(Request $request){
        $post = Candidate::find($request->cand_id);
        $checkPost = DB::table('customercosts')->where('exp_type','=',$post->gulfexperience)->where('proff_id','=',$post->jobtype_id)->count();
        if ($checkPost == 0) {
            $isAvailable = 'false';
        } else {
            $isAvailable = 'true';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }
}
