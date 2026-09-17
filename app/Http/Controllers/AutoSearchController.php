<?php

namespace App\Http\Controllers;

use App\AdminModel\AllContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutoSearchController extends Controller
{
    public function fetch1(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = DB::table('qr_all_contacts_tbl')->where('calling_from','LIKE',"%{$query}%")->groupBy('calling_from')->get();
            $output = '<ul class="list-group">';
            foreach ($data as $row) {
                $output .='<li class="list-group-item call-li"><a href="#">'.$row->calling_from.'</a></li>';
            }
            $output .='</ul>';
            echo $output;
        }
    }

    public function fetch2(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = DB::table('qr_all_contacts_tbl')->where('state','LIKE',"%{$query}%")->groupBy('state')->get();
            $output = '<ul class="list-group">';
            foreach ($data as $row) {
                $output .='<li class="list-group-item state-li"><a href="#">'.$row->state.'</a></li>';
            }
            $output .='</ul>';
            echo $output;
        }
    }

    public function fetch3(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = DB::table('qr_all_contacts_tbl')->where('city','LIKE',"%{$query}%")->groupBy('city')->get();
            $output = '<ul class="list-group">';
            foreach ($data as $row) {
                $output .='<li class="list-group-item city-li"><a href="#">'.$row->city.'</a></li>';
            }
            $output .='</ul>';
            echo $output;
        }
    }

    public function fetch4(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = DB::table('qr_party_tbl')->where('state','LIKE',"%{$query}%")->groupBy('state')->get();
            $output = '<ul class="list-group">';
            foreach ($data as $row) {
                $output .='<li class="list-group-item state2-li"><a href="#">'.$row->state.'</a></li>';
            }
            $output .='</ul>';

            echo $output;
        }
    }

    public function fetch5(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = DB::table('qr_party_tbl')->where('city','LIKE',"%{$query}%")->groupBy('city')->get();
            $output = '<ul class="list-group">';
            foreach ($data as $row) {
                $output .='<li class="list-group-item city2-li"><a href="#">'.$row->city.'</a></li>';
            }
            $output .='</ul>';
            echo $output;
        }
    }

    public function fetch6(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = DB::table('qr_all_contacts_tbl')->where('job_desg','LIKE',"%{$query}%")->groupBy('job_desg')->get();
            $output = '<ul class="list-group">';
            foreach ($data as $row) {
                $output .='<li class="list-group-item job_desg-li"><a href="#">'.$row->job_desg.'</a></li>';
            }
            $output .='</ul>';
            echo $output;
        }
    }

    public function fetch7(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = DB::table('qr_all_contacts_tbl')->where('job_title','LIKE',"%{$query}%")->groupBy('job_title')->get();
            $output = '<ul class="list-group">';
            foreach ($data as $row) {
                $output .='<li class="list-group-item job_title-li"><a href="#">'.$row->job_title.'</a></li>';
            }
            $output .='</ul>';
            echo $output;
        }
    }

    public function fetch8(Request $request){
        $query = $request->get('query');
        $data = DB::table('medicalcenters')->where('city','LIKE',"%{$query}%")->groupBy('city')->get();
        $output = '<ul class="list-group">';
        foreach($data as $row){
            $output .='<li class="list-group-item city2-li"><a href="#">'.$row->city.'</a></li>';
        }
        $output .= '</ul>';
        echo $output;
    }

    public function fetch9(Request $request){
        $query = $request->get('query');
        $data = DB::table('medicalcenters')->where('state','LIKE',"%{$query}%")->groupBy('state')->get();
        $output = '<ul class="list-group">';
        foreach($data as $row){
            $output .='<li class="list-group-item state2-li"><a href="#">'.$row->state.'</a></li>';
        }
        $output .= '</ul>';
        echo $output;
    }
}
