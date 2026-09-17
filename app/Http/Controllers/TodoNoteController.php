<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TodoNote;
use App\Models\Admin;
use App\Models\Todo;
use App\Models\Adminpermission;
use Illuminate\Support\Facades\Auth;

class TodoNoteController extends Controller
{
    /**
     * Store a new note via AJAX
     */
    public function store(Request $request)
    {
        $messages = [
            'todo_id.required' => 'Todo ID is required.',
            'todo_id.exists' => 'Selected todo does not exist.',
            'notes.required' => 'Please add Notes.',
            'notes.string' => 'Notes must be a valid string.',
        ];
    
        $request->validate([
            'todo_id' => 'required|exists:todos,id',
            'notes' => 'required|string',
        ], $messages);
    
        $note = TodoNote::create([
            'todo_id' => $request->todo_id,
            'created_by' => auth()->id(),
            'notes' => $request->notes,
        ]);

        Todo::where('id', $request->todo_id)->update(['updated_at' => now()]);
    
        return response()->json([
            'status' => 'success',
            'message' => 'Note added successfully',
            'data' => [
                'id' => $note->id,
                'notes' => $note->notes,
                'created_by_name' => auth()->user()->name,
                'created_at' => $note->created_at->format('Y-m-d h:i A'),
                'delete_url' => route('admin.todonotes.delete'),
            ],
        ]);
    }
    
    /**
     * List all notes for a todo (AJAX)
     */
    public function list(Request $request)
    {
        $request->validate([
            'todo_id' => 'required|exists:todos,id',
        ]);

        $todo = Todo::find($request->todo_id);

        // Same visibility rule as TodoController::index(): staff without
        // todo_view can only open tasks assigned to them.
        $user = Auth::guard('admin')->user();
        $isAdmin = $user->user_type == 1;
        $permission = Adminpermission::where('staff_id', $user->id)->first();
        $isAssignee = $todo->assignto_id && in_array((string) $user->id, explode(',', $todo->assignto_id));

        if (!$isAdmin && !optional($permission)->full_access && optional($permission)->todo_view != 1 && !$isAssignee) {
            abort(403, 'You are not authorized to view this task.');
        }

        $notes = TodoNote::where('todo_id', $request->todo_id)
            ->with('creator') // Relationship to get created_by user
            ->orderBy('created_at', 'desc')
            ->get();

        $html = '';

        if ($notes->count()) {
            foreach ($notes as $note) {
                $html .= '<tr id="todoNoteRow_'.$note->id.'">';
                $html .= '<td>'.$note->notes.'</td>';
                $html .= '<td>'.($note->creator->name ?? '---').'</td>';
                $html .= '<td>'.$note->created_at->format('Y-m-d h:i A').'</td>';
                $html .= '<td>
                                <a href="javascript:void(0);" 
                                class="delete-todo-note" 
                                data-id="'.$note->id.'" 
                                data-url="'.route('admin.todonotes.delete').'">
                                    <i class="ti ti-trash ti-sm text-danger"></i>
                                </a>
                            </td>';
    
                $html .= '</tr>';
            }
        }

        return response()->json([
            'todo' => $todo,
            'html' => $html]);
    }

    /**
     * Delete note via AJAX
     */
    public function destroy(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'delete_id' => 'required|exists:todo_notes,id',
        ]);
    
        // Find the note by ID
        $note = TodoNote::find($request->delete_id);
    
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
        $todo = Todo::find($request->id);
        if ($todo) {
            return response()->json(['success' => true, 'data' => $todo]);
        }
        return response()->json(['success' => false]);
    }
    
    
}
