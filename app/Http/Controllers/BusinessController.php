<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;

class BusinessController extends Controller
{
    /**
     * Display a listing of the businesses.
     */
    public function index()
    {
        $businesses = Business::orderBy('created_at','DESC')->get();
        return view('admin.business.index', compact('businesses'));
    }


    public function businessListJson(Request $request) {
        $post =  Business::orderBy('created_at','DESC')->get();

        $data['data'] = $post;
        return response()->json($data);
    }
    

    /**
     * Show the form for creating a new business.
     */
    public function create()
    {
        return view('admin.business.create');
    }

    /**
     * Store a newly created business in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Business::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success','Business created successfully!');
    }

    /**
     * Show the form for editing the specified business.
     */
    public function edit(Request $request)
    {
        $business = Business::find($request->id);
        return response()->json($business);
    }

    /**
     * Update the specified business in storage.
     */
    public function update(Request $request)
    {
        $business = Business::find($request->edit_id);
        $business->update([
            'name' => $request->name,
        ]);
        return redirect()->back()->with('success','Business updated successfully!');

    }

    /**
     * Remove the specified business from storage.
     */

    public function destroy(Request $request)
    {
        $business = Business::find($request->delete_id);

        if (!$business) {
            return response()->json([
                'status' => 'error',
                'message' => 'Business not found'
            ]);
        }

        // Optional: Check if business is assigned to any deals
        if ($business->deals()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'This business is assigned to deals and cannot be deleted'
            ]);
        }

        $business->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Business deleted successfully'
        ]);
    }

   
}
