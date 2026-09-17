<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function citySearch(Request $request){
        $query = $request->get('query');
        $finalResult = Candidate::where('plb_text','LIKE',"%$query%")->get();
        return response()->json($finalResult);
    }
}
