<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Adminpermission;
use App\Models\ImageUrl;
use App\Models\Admin;

class ImageUrlController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $permission = Adminpermission::where('staff_id', $admin->id)->first();

        // ❌ Authorization check
        if (
            $admin->user_type != 1 &&
            (
                !$permission ||
                ($permission->full_access != 1 && $permission->whatsapp_url != 1)
            )
        ) {
            abort(403, 'Unauthorized access');
        }

        // Base query
        $query = ImageUrl::with('staff')->orderBy('id', 'DESC');

        // ✅ Super admin OR full access → see all
        if ($admin->user_type == 1 || ($permission && $permission->full_access == 1)) {

            $posts = $query->paginate(10);

        }
        // ✅ Staff with limited permissions
        else {
            // Can view all WhatsApp URLs
            if ($permission && $permission->view_whatsapp_url == 1) {
                $posts = $query->paginate(10);
            }
            // Can view only own URLs
            else {
                $posts = $query
                    ->where('staff_id', $admin->id)
                    ->paginate(10);
            }
        }

        $staffs = Admin::where('status', 1)
            ->orderBy('name')
            ->get();

        return view(
            'admin.dynamic-image-url.index',
            compact('posts', 'staffs', 'permission')
        );
    }

    public function edit(Request $request){
        $post = ImageUrl::find($request->id);
        return response()->json($post);
    }

    public function store(Request $request)
    {
        try {

            // validation
            $request->validate([
                'staff_id' => 'required',
                'image_url' => 'required'
            ]);

            // =====================================================
            // Staff
            // =====================================================
            $staff = Admin::where('id', $request->staff_id)->first();

            if (!$staff) {
                return redirect()->back()->with('error', 'Staff not found!');
            }

            // =====================================================
            // Data
            // =====================================================
            $data = [
                'staff_id' => $staff->id ?? null,
                'url' => $request->image_url ?? null,
            ];

            // =====================================================
            // Check Exist
            // =====================================================
            $checkExist = ImageUrl::where('staff_id', $request->staff_id)->first();

            if ($checkExist) {

                $checkExist->update($data);

            } else {

                ImageUrl::create($data);
            }

            return redirect()->back()->with('success', 'Saved successfully!');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function update(Request $request)
    {
        try {

            // validation
            $request->validate([
                'edit_id' => 'required',
                'staff_id' => 'required',
            ]);

            // =====================================================
            // Find Record
            // =====================================================
            $teamMemberPage = ImageUrl::find($request->edit_id);

            if (!$teamMemberPage) {
                return redirect()->back()->with('error', 'Record not found!');
            }

            // =====================================================
            // Staff
            // =====================================================
            $staff = Admin::where('id', $request->staff_id)->first();

            if (!$staff) {
                return redirect()->back()->with('error', 'Staff not found!');
            }

            // =====================================================
            // Update Data
            // =====================================================
            $teamMemberPage->update([
                'staff_id' => $request->staff_id
            ]);

            return redirect()->back()->with('success', 'Updated successfully!');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function delete(Request $request)
    {
        try {

            $request->validate([
                'delete_id' => 'required'
            ]);

            // =====================================================
            // Find Record
            // =====================================================
            $teamMemberPage = ImageUrl::find($request->delete_id);

            if (!$teamMemberPage) {
                return redirect()->back()->with('error', 'Record not found!');
            }

            // =====================================================
            // Delete Image
            // =====================================================
            if (!empty($teamMemberPage->image)) {

                $basepathstatus = \App\Models\Basepathstatus::first();

                $imagePath = null;

                if ($basepathstatus && $basepathstatus->base_path_status == 1) {

                    $imagePath = base_path().'/public/admin/assets/img/avatars/'.$teamMemberPage->image;

                } else {

                    $imagePath = base_path().'/public_html/admin/assets/img/avatars/'.$teamMemberPage->image;
                }

                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                }
            }

            // =====================================================
            // Delete Record
            // =====================================================
            $teamMemberPage->delete();

            return redirect()->back()->with('success', 'Deleted successfully!');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }
}
