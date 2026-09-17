<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobTitle;

class JobTitleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jobTitles = JobTitle::orderBy('created_at','DESC')->get();
        return view('admin.job-title.index', compact('jobTitles'));
    }


    /**
     * Display a listing of the JobTitle.
     */

    public function jobTitleListJson(Request $request) {
        $post =  JobTitle::orderBy('created_at','DESC')->get();

        $data['data'] = $post;
        return response()->json($data);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.job-title.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        JobTitle::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success','Job Title created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\JobTitle  $jobTitle
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\JobTitle  $jobTitle
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $jobTitle = JobTitle::find($request->id);
        return response()->json($jobTitle);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\JobTitle  $jobTitle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $jobTitle = JobTitle::find($request->edit_id);
        $jobTitle->update([
            'name' => $request->name,
        ]);
        return redirect()->back()->with('success','Job Title updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\JobTitle  $jobTitle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $jobTitle = JobTitle::find($request->delete_id);

        if (!$jobTitle) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job Title not found'
            ]);
        }

        // Optional: Check if this status is assigned to any deal
        if ($jobTitle->deals()->count() > 0) {
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
