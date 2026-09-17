<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\Facades\DataTables;

class CheckPageController extends Controller
{
    public function index()
    {
        $pageConfigs = ['pageHeader' => false];
        return view('/content/service/pageCheck',['pageConfigs' => $pageConfigs]);
    }

    public function list_cand_json(Request $request)
    {
        $data_cand = DB::table('qr_candidate_tbl as cand')

        ->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
        ->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
        ->select('cand.cand_id','cand.cand_passport_no','cand.cand_fname','cand.cand_place_birth')
        ->get();

        // $data['data'] = $data_cand;
        // return response()->json($data);

        return DataTables::of($data_cand)->toJson();
    }
}
