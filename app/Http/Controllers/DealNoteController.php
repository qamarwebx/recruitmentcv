<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DealNote;
use App\Models\DealPipeline;
use App\Models\Admin;
use App\Models\Adminpermission;
use Illuminate\Support\Facades\Auth;


class DealNoteController extends Controller
{
    /**
     * Store a new note via AJAX
     */
    public function store(Request $request)
    {
        $messages = [
            'deal_id.required' => 'Deal ID is required.',
            'deal_id.exists' => 'Selected deal does not exist.',
            'notes.required' => 'Please add Notes.',
            'notes.string' => 'Notes must be a valid string.',
            'conversation_type.string' => 'Conversation type must be a valid string.',
        ];
    
        $request->validate([
            'deal_id' => 'required|exists:deal_pipeline,id',
            'notes' => 'required|string',
            'conversation_type' => 'nullable|string',
        ], $messages);
    
        $note = DealNote::create([
            'deal_id' => $request->deal_id,
            'created_by' => auth()->id(),
            'notes' => $request->notes,
            'conversation_type' => $request->conversation_type,
        ]);

        DealPipeline::where('id', $request->deal_id)->update(['updated_at' => now()]);

        if(Auth::user()->user_type == 1){
            $delete_url = route('admin.dealnotes.delete');
        } else {
            $delete_url = null;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Note added successfully',
            'data' => [
                'id' => $note->id,
                'notes' => $note->notes,
                'conversation_type' => $note->conversation_type,
                'created_by_name' => auth()->user()->name,
                'created_at' => $note->created_at->format('Y-m-d h:i A'),
                'delete_url' => $delete_url,
            ],
        ]);
    }
    
    /**
     * List all notes for a deal (AJAX)
     */
    public function list(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deal_pipeline,id',
        ]);

        $deal = DealPipeline::find($request->deal_id);

        // Same visibility rule DealPipelineController::index() uses: staff
        // without deal_view can only open deals assigned to them.
        $user = Auth::guard('admin')->user();
        $isAdmin = $user->user_type == 1;
        $perms = Adminpermission::where('staff_id', $user->id)->first();

        if (!$isAdmin && optional($perms)->deal_view != 1 && $deal->care_of != $user->id) {
            abort(403, 'You are not authorized to view this deal.');
        }

        $notes = DealNote::where('deal_id', $request->deal_id)
            ->with('creator') // Relationship to get created_by user
            ->orderBy('created_at', 'desc')
            ->get();
        
        $html = '';

        if ($notes->count()) {
            foreach ($notes as $note) {
                $html .= '<tr id="noteRow_'.$note->id.'">';
                $html .= '<td>'.$note->notes.'</td>';
                $html .= '<td>'.($note->conversation_type ?? '---').'</td>';
                $html .= '<td>'.($note->creator->name ?? '---').'</td>';
                $html .= '<td>'.$note->created_at->format('Y-m-d h:i A').'</td>';
                if(Auth::user()->user_type == 1){
                    $html .= '<td>
                                <a href="javascript:void(0);" 
                                class="delete-note" 
                                data-id="'.$note->id.'" 
                                data-url="'.route('admin.dealnotes.delete').'">
                                    <i class="ti ti-trash ti-sm text-danger"></i>
                                </a>
                            </td>';
                } else {
                    $html .= null;
                }    
                $html .= '</tr>';
            }
        }

        return response()->json(['html' => $html,'deal' => $deal]);
    }

    /**
     * Delete note via AJAX
     */
    public function destroy(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'delete_id' => 'required|exists:deal_notes,id',
        ]);
    
        // Find the note by ID
        $note = DealNote::find($request->delete_id);
    
        if ($note) {
            $note->delete();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Note deleted successfully'
            ]);
        }
    
        // If note not found (extra safety)
        return response()->json([
            'status' => 'error',
            'message' => 'Note not found'
        ]);
    }

    public function getDetails(Request $request)
    {
        $deal = DealPipeline::find($request->id);
        if ($deal) {
            return response()->json(['success' => true, 'data' => $deal]);
        }
        return response()->json(['success' => false]);
    }
    
    
}
