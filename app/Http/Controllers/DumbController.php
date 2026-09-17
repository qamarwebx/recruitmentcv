<?php

namespace App\Http\Controllers;

use App\AdminModel\CandCompFinalStatus;
use App\AdminModel\ComplCandStatus;
use Illuminate\Http\Request;

class DumbController extends Controller
{
    public function medhealth(Request $request)
    {
        extract($_POST);
        $finalStatus = new CandCompFinalStatus();
        // check accordingly 

        if($value == 1){
            $chk_mf = CandCompFinalStatus::where('final_status','=','Medical Fit')->where('cand_id','=',$id)->first();
            if($chk_mf == ''){
                $finalStatus->cand_id = $id;
                $finalStatus->final_status = "Medical Fit";
                $finalStatus->save();
            }

            // Check and Remove
            $cs_status = ComplCandStatus::where('cand_id','=',$id)->first();
            $status = explode(",",$cs_status->c_status);
            $rem_st = ["Medical Unfit","Permanently Unfit","Medical Service"];
            $new_st = array_diff($status,$rem_st);
            $cs_status->c_status = implode(",",$new_st);
            $cs_status->save();

            // check and delete
            $chk_munf = CandCompFinalStatus::where('final_status','=','Medical Unfit')->where('cand_id','=',$id)->first();
            $chk_punf = CandCompFinalStatus::where('final_status','=','Permanently Unfit')->where('cand_id','=',$id)->first();
            $chk_ms = CandCompFinalStatus::where('final_status','=','Medical Service')->where('cand_id','=',$id)->first();

            if(isset($chk_munf)){
                $chk_munf->delete();
            }
            if(isset($chk_punf)){
                $chk_punf->delete();
            }
            if(isset($chk_ms)){
                $chk_ms->delete();
            }

        }

        if($value == 0){
             
            // Check and Remove
            $cs_status = ComplCandStatus::where('cand_id','=',$id)->first();
            $status = explode(",",$cs_status->c_status);
            $rem_st = ["Medical Fit","Medical Online","Medical Report Received"];
            $new_st = array_diff($status,$rem_st);
            $cs_status->c_status = implode(",",$new_st);
            $cs_status->save();
            

            $chk_mf = CandCompFinalStatus::where('final_status','=','Medical Fit')->where('cand_id','=',$id)->first();
            $chk_mo = CandCompFinalStatus::where('final_status','=','Medical Online')->where('cand_id','=',$id)->first();
            $chk_mr = CandCompFinalStatus::where('final_status','=','Medical Report Received')->where('cand_id','=',$id)->first();
            if(isset($chk_mf)){
                $chk_mf->delete();
            }
            if(isset($chk_mo)){
                $chk_mo->delete();
            }
            if(isset($chk_mr)){
                $chk_mr->delete();
            }
        }
    }

    public function medStat(Request $request)
    {
        extract($_POST);
        $finalStatus = new CandCompFinalStatus();
        // check accordingly 

        if($value == 1){
            $chk_mf = CandCompFinalStatus::where('final_status','=','Medical Online')->where('cand_id','=',$id)->first();
            if($chk_mf == ''){
                $finalStatus->cand_id = $id;
                $finalStatus->final_status = "Medical Online";
                $finalStatus->save();
            }
        }

        if($value == 0){
            $chk_mo = CandCompFinalStatus::where('final_status','=','Medical Online')->where('cand_id','=',$id)->first();
            
            if(isset($chk_mo)){
                $chk_mo->delete();
            }
            
        }
    }

    public function medRep(Request $request)
    {
        extract($_POST);
        $finalStatus = new CandCompFinalStatus();
        // check accordingly 

        if($value == 'Yes'){
            $chk_mf = CandCompFinalStatus::where('final_status','=','Medical Report Received')->where('cand_id','=',$id)->first();
            if($chk_mf == ''){
                $finalStatus->cand_id = $id;
                $finalStatus->final_status = "Medical Report Received";
                $finalStatus->save();
            }
        }

        if($value == 'No'){
            $chk_mo = CandCompFinalStatus::where('final_status','=','Medical Report Received')->where('cand_id','=',$id)->first();
            
            if(isset($chk_mo)){
                $chk_mo->delete();
            }
            
        }
    }
}
