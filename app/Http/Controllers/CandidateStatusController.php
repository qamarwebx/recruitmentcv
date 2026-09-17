<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\CandidateStatus;

class CandidateStatusController extends Controller
{
    public function index()
    {
      $data['candidateStatus'] = CandidateStatus::paginate(10);
      return view('admin.candidateStatus.index',$data);
    }
  
    public function create()
    {
      //return view('orderstatus.create');
    }
  
    public function store(Request $request)
    {
        $candstatus_data=$this->validatedData();
        CandidateStatus::create($candstatus_data);
        return redirect('/candidate/status');
    }
  
    public function edit(CandidateStatus $candidateStatus)
    {
      $data['candidateStatus'] = CandidateStatus::find($candidateStatus->id);
      return View('admin.candidateStatus.edit',$data);
    }

    public function update(Request $request, CandidateStatus $candidateStatus)
    {
        $candstatus_data=$this->validatedData();
        $candidateStatus->update($candstatus_data);
        return redirect('/orderstatus')->with('success', 'Updated Successfully');;
    }

      public function destroy(CandidateStatus $candidateStatus)
      {
        $candidateStatus->delete();
        return redirect('/orderstatus');
      }
      
      protected function validatedData()
      {
          return request()->validate([
              'Status' => 'required',    
          ]);
      }
}
