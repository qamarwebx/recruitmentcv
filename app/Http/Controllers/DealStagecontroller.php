<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DealStage;
use App\Models\DealPipeline;

class DealStagecontroller extends Controller
{
    /**
     * Display a listing of the deal Stage.
     */
    public function index()
    {
        $dealStages = DealStage::orderBy('created_at','DESC')->get();
        return view('admin.deal-stage.index', compact('dealStages'));
    }


    public function DealStageListJson(Request $request) {
        $post =  DealStage::orderBy('created_at','DESC')->get();

        $data['data'] = $post;
        return response()->json($data);
    }
    

    /**
     * Show the form for creating a new deal Stage.
     */
    public function create()
    {
        return view('admin.deal-stage.create');
    }

    /**
     * Store a newly created deal Stage in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DealStage::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success','deal Stage created successfully!');
    }

    /**
     * Show the form for editing the specified deal Stage.
     */
    public function edit(Request $request)
    {
        $dealStages = DealStage::find($request->id);
        return response()->json($dealStages);
    }

    /**
     * Update the specified deal Stage in storage.
     */
    public function update(Request $request)
    {
        $dealStages = DealStage::find($request->edit_id);
        $dealStages->update([
            'name' => $request->name,
        ]);
        return redirect()->back()->with('success','deal Stage updated successfully!');

    }

    /**
     * Remove the specified deal Stage from storage.
     */


    public function destroy(Request $request)
    {
        $dealStage = DealStage::find($request->delete_id);

        if (!$dealStage) {
            return response()->json(['status' => 'error', 'message' => 'Stage not found']);
        }

        // Check if this stage is assigned to any deal
        $assignedDeals = DealPipeline::where('deal_stage_id', $dealStage->id)->count();

        if ($assignedDeals > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'This stage is assigned to deals and cannot be deleted'
            ]);
        }

        $dealStage->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Deal Stage deleted successfully'
        ]);
    }

   
}