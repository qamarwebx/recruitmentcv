<?php

namespace App\Http\Controllers;

use App\Models\GlobalSourceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SourceController extends Controller
{
    /**
     * Display the Source Management page.
     */
    public function index()
    {
        return view('admin.source.index');
    }

    /**
     * Return Source data as JSON for DataTable.
     */
    public function indexJson(Request $request)
    {
        $sources = DB::table('global_source_type as source')
            ->leftJoin('admins as admin', 'admin.id', '=', 'source.created_by_id')
            ->select(
                'source.id',
                'source.name',
                'source.description',
                'source.is_active',
                'admin.name as created_by_name'
            )
            ->orderBy('source.id', 'desc')
            ->get();

        return response()->json(['data' => $sources]);
    }

    /**
     * Store a new Source.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        GlobalSourceType::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'created_by_id' => Auth::guard('admin')->id(),
        ]);

        return redirect()->back()->with('success', 'Source added successfully!');
    }

    /**
     * Fetch a single Source for editing (AJAX).
     */
    public function edit(Request $request)
    {
        $source = GlobalSourceType::find($request->id);

        if (!$source) {
            return response()->json(['error' => 'Source not found.'], 404);
        }

        return response()->json($source);
    }

    /**
     * Update Source details.
     */
    public function update(Request $request)
    {
        $request->validate([
            'edit_id' => 'required|exists:global_source_type,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $source = GlobalSourceType::find($request->edit_id);

        if (!$source) {
            return redirect()->back()->with('error', 'Source not found.');
        }

        $source->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Source updated successfully!');
    }

    /**
     * Delete a Source (from delete modal).
     */
    public function delete(Request $request)
    {
        $request->validate([
            'source_id' => 'required|exists:global_source_type,id',
        ]);

        $source = GlobalSourceType::find($request->source_id);

        if (!$source) {
            return redirect()->back()->with('error', 'Source not found.');
        }

        $source->delete();

        return redirect()->back()->with('success', 'Source deleted successfully!');
    }
}
