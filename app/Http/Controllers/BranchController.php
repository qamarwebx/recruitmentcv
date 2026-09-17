<?php

namespace App\Http\Controllers;

use App\Models\Adminpermission;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    /**
     * Ensure the current admin can access the Branch module, and optionally
     * a specific ability within it (e.g. 'edit_branch'). Admins (user_type
     * == 1) and staff with full_access always pass.
     */
    protected function authorizeBranch(?string $ability = null)
    {
        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        if ($user->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            return $permission;
        }

        if (!isset($permission) || $permission->branch != 1) {
            abort(403, 'You do not have permission to access the Branch module.');
        }

        if ($ability && $permission->{$ability} != 1) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $permission;
    }

    public function index()
    {
        $permission = $this->authorizeBranch();

        return view('admin.branch.index', ['perm' => $permission]);
    }

    public function indexjson(Request $request)
    {
        $permission = $this->authorizeBranch();
        $user = Auth::guard('admin')->user();

        if ($user->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $posts = Branch::orderBy('id', 'DESC')->get();
        } elseif (isset($permission) && $permission->view_branch == 1) {
            $posts = Branch::orderBy('id', 'DESC')->get();
        } else {
            $posts = Branch::where('admin_id', $user->id)->orderBy('id', 'DESC')->get();
        }

        $data['data'] = $posts;

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $this->authorizeBranch('add_branch');

        $request->validate([
            'name' => 'required|string|max:255|unique:branches,name',
        ]);

        $post = new Branch();
        $post->name = $request->name;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->status = true;
        $post->save();

        return redirect()->back()->with('success', 'Branch Added!');
    }

    public function edit(Request $request)
    {
        $this->authorizeBranch('edit_branch');

        $post = Branch::find($request->id);

        return response()->json($post);
    }

    public function update(Request $request)
    {
        $this->authorizeBranch('edit_branch');

        $request->validate([
            'name' => 'required|string|max:255|unique:branches,name,' . $request->edit_id,
        ]);

        $post = Branch::find($request->edit_id);
        $post->name = $request->name;
        $post->save();

        return redirect()->back()->with('success', 'Branch Updated!');
    }

    public function checkname(Request $request)
    {
        $count = Branch::where('name', '=', $request->name)->count();

        return response()->json(['valid' => $count == 0 ? 'true' : 'false']);
    }

    public function edcheckname(Request $request)
    {
        $count = Branch::where('id', '!=', $request->id)->where('name', '=', $request->name)->count();

        return response()->json(['valid' => $count == 0 ? 'true' : 'false']);
    }

    public function checkDelBranch(Request $request)
    {
        $count = Branch::findOrFail($request->id)->googleReviews()->count();

        if ($count > 0) {
            return response()->json('1');
        }
    }

    public function deleteBranch(Request $request)
    {
        $this->authorizeBranch('delete_branch');

        $post = Branch::find($request->branch_ids);
        $post->delete();

        return redirect()->back()->with('success', 'Branch Deleted!');
    }
}
