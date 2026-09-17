<?php

namespace App\Http\Controllers;

use App\AdminModel\Employee;
use App\AdminModel\EmployeeCandidate;
use App\AdminModel\Party;
use App\AdminModel\Profession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\Util\Json;

class ChildController extends Controller
{
    public function getProfession(Request $request)
    {
        
        $empId = $request->empID;

        $profID = Employee::where('emp_id','=',$empId)->get();

        $res = '';
        $res.='<option value=""></option>';

        foreach ($profID as $prof) {
            // get Profession ID
            $get_p_id = explode(',',$prof->profession);
            $get_p_op = explode(',',$prof->openings);

            // $all = count($get_p_id);
            // Testing Code.
            // $get_p_id = $prof->profession;
            // $get_p_op = $prof->openings;
            if (count($get_p_id) > 0) {

                for ($i=0; $i < count($get_p_id) ; $i++) { 
                    // count EmployeCandidate Data
                    $profesion_count = EmployeeCandidate::where('emp_id','=',$empId)->where('cand_profession','=',$get_p_id[$i])->count();
                    // for ($i=0; $i < count($get_p_op) ; $i++) { 
                        $op_val = $get_p_op[$i];
                        if ($op_val > $profesion_count) {
                            $prof_data = Profession::find($get_p_id[$i]);
                            $res.='<option value="'.$prof_data->prof_id.'">'.$prof_data->prof_eng_name.'</option>';
                        }
                    // }
                }
           
            }

        }
        
        $arr['res'] = $res;
		return response()->json($arr);
    }

    public function checkFileNo(Request $request)
    {
        $chk = Employee::where('emp_file_no','=',$request->input('file_no'))->first();
        if ($chk) {
            return response()->json('1');
        }else{
            
        }
    }

    public function checkMember(Request $request)
    {
        $chk = Party::where('member_id','=',$request->input('membership'))->first();
        if ($chk) {
            return response()->json('1');
        } else {

        }
        
    }

    public function checkMemberU(Request $request)
    {
        $chk = Party::where('member_id','=',$request->input('membership'))->where('pty_id','!=',$request->input('pty_id'))->first();
        if($chk){
            return response()->json('1');
        }else{
            
        }
    }

    public function checkprimaryno(Request $request)
    {
        $pty_id = $request->pty_id;
        $primary_no = $request->pno;
        if($primary_no != ''){
            $posts = Party::where('pty_id','!=',$pty_id)->where('pty_comp_contact','=',$primary_no)->orWhere('telephone_number','=',$primary_no)->orWhere('p_mobile','=',$primary_no)->orWhere('pty_contact_no','=',$primary_no)->count();
        }

        
        if ($posts > 0) {
            return response()->json('1');
        } else {

        }
        
    }

    public function checksecno(Request $request)
    {
        $pty_id = $request->pty_id;
        $primary_no = $request->pno;
        if($primary_no != ''){
            $posts = Party::where('pty_id','!=',$pty_id)->where('pty_contact_no','=',$primary_no)->orWhere('telephone_number','=',$primary_no)->orWhere('p_mobile','=',$primary_no)->orWhere('pty_comp_contact','=',$primary_no)->count();
        }

        
        if ($posts > 0) {
            return response()->json('1');
        } else {

        }
        
    }

    public function telephoecheck(Request $request)
    {
        $pty_id = $request->pty_id;
        $primary_no = $request->pno;
        if($primary_no != ''){
            $posts = Party::where('pty_id','!=',$pty_id)->where('telephone_number','=',$primary_no)->orWhere('pty_contact_no','=',$primary_no)->orWhere('p_mobile','=',$primary_no)->orWhere('pty_comp_contact','=',$primary_no)->count();
        }

        
        if ($posts > 0) {
            return response()->json('1');
        } else {

        }
        
    }

    public function checkpmobile(Request $request)
    {
        $pty_id = $request->pty_id;
        $primary_no = $request->pno;
        if($primary_no != ''){
            $posts = Party::where('pty_id','!=',$pty_id)->where('p_mobile','=',$primary_no)->orWhere('pty_contact_no','=',$primary_no)->orWhere('telephone_number','=',$primary_no)->orWhere('pty_comp_contact','=',$primary_no)->count();
        }

        
        if ($posts > 0) {
            return response()->json('1');
        } else {

        }
        
    }

}
