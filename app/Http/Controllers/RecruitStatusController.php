<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RecruitStatus;

class RecruitStatusController extends Controller
{
    /**
     * Display a listing of the Recruit Status.
     */
    public function index()
    {
        $recruitStatus = RecruitStatus::orderBy('created_at','DESC')->get();
        return view('admin.recruit-status.index', compact('recruitStatus'));
    }


    public function RecruitStatusListJson(Request $request) {
        $post =  RecruitStatus::orderBy('created_at','DESC')->get();

        $data['data'] = $post;
        return response()->json($data);
    }
    

    /**
     * Show the form for creating a new Recruit Status.
     */
    public function create()
    {
        return view('admin.recruit-status.create');
    }

    /**
     * Store a newly created Recruit Status in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        RecruitStatus::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success','Recruit Status created successfully!');
    }

    /**
     * Show the form for editing the specified Recruit Status.
     */
    public function edit(Request $request)
    {
        $recruitStatus = RecruitStatus::find($request->id);
        return response()->json($recruitStatus);
    }

    /**
     * Update the specified Recruit Status in storage.
     */
    public function update(Request $request)
    {
        $recruitStatus = RecruitStatus::find($request->edit_id);
        $recruitStatus->update([
            'name' => $request->name,
        ]);
        return redirect()->back()->with('success','Recruit Status updated successfully!');

    }

    /**
     * Remove the specified Recruit Status from storage.
     */

    public function destroy(Request $request)
    {
        $recruitStatus = RecruitStatus::find($request->delete_id);

        if (!$recruitStatus) {
            return response()->json([
                'status' => 'error',
                'message' => 'Recruit Status not found'
            ]);
        }

        // Optional: Check if this status is assigned to any deal
        if ($recruitStatus->deals()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'This Recruit Status is assigned to deals and cannot be deleted'
            ]);
        }

        $recruitStatus->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Recruit Status deleted successfully'
        ]);
    }

     
   
}