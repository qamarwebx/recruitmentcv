<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Adminpermission;
use App\Models\TeamMemberWebPage;
use App\Models\Admin;

class TeamMemberPageController extends Controller
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
        $query = TeamMemberWebPage::with('staff')->orderBy('id', 'DESC');

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
            'admin.team-member-page.index',
            compact('posts', 'staffs', 'permission')
        );
    }

    public function edit(Request $request){
        $post = TeamMemberWebPage::find($request->id);
        return response()->json($post);
    }

    public function store(Request $request)
    {
        try {

            // validation
            $request->validate([
                'staff_id' => 'required',
                'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'working_mobile_no' => 'required',
                'calling_number' => 'required',
            ]);

            $profile_image = null;

            // =====================================================
            // Upload Image
            // =====================================================
            if ($request->hasFile('profile_image')) {

                $file = $request->file('profile_image');

                $name = $file->getClientOriginalName();

                // remove spaces
                $filename_ren = pathinfo($name, PATHINFO_FILENAME);
                $fileext_ren = pathinfo($name, PATHINFO_EXTENSION);

                $filename_reps = str_replace(" ", "_", $filename_ren);

                $new_file = time().'_'.$filename_reps.'.'.$fileext_ren;

                $basepathstatus = \App\Models\Basepathstatus::first();

                // upload image
                if ($basepathstatus && $basepathstatus->base_path_status == 1) {

                    $file->move(
                        base_path().'/public/admin/assets/img/avatars',
                        $new_file
                    );

                } else {

                    $file->move(
                        base_path().'/public_html/admin/assets/img/avatars',
                        $new_file
                    );
                }

                // store only filename
                $profile_image = $new_file;

            } elseif (!empty($request->existing_profile_image)) {

                $profile_image = $request->existing_profile_image;
            }

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
                'name' => $staff->name ?? null,
                'image' => $profile_image,
                'whatsapp_number' => $request->working_mobile_no,
                'calling_number' => $request->calling_number,
            ];

            // =====================================================
            // Check Exist
            // =====================================================
            $checkExist = TeamMemberWebPage::where('staff_id', $request->staff_id)->first();

            if ($checkExist) {

                $checkExist->update($data);

            } else {

                $data['staff_id'] = $request->staff_id;

                TeamMemberWebPage::create($data);
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
                'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'working_mobile_no' => 'required',
                'calling_number' => 'required',
            ]);

            // =====================================================
            // Find Record
            // =====================================================
            $teamMemberPage = TeamMemberWebPage::find($request->edit_id);

            if (!$teamMemberPage) {
                return redirect()->back()->with('error', 'Record not found!');
            }

            $profile_image = $teamMemberPage->image;

            // =====================================================
            // Upload New Image
            // =====================================================
            if ($request->hasFile('profile_image')) {

                $file = $request->file('profile_image');

                $name = $file->getClientOriginalName();

                // remove spaces
                $filename_ren = pathinfo($name, PATHINFO_FILENAME);
                $fileext_ren = pathinfo($name, PATHINFO_EXTENSION);

                $filename_reps = str_replace(" ", "_", $filename_ren);

                $new_file = time().'_'.$filename_reps.'.'.$fileext_ren;

                $basepathstatus = \App\Models\Basepathstatus::first();

                // upload image
                if ($basepathstatus && $basepathstatus->base_path_status == 1) {

                    $file->move(
                        base_path().'/public/admin/assets/img/avatars',
                        $new_file
                    );

                } else {

                    $file->move(
                        base_path().'/public_html/admin/assets/img/avatars',
                        $new_file
                    );
                }

                // delete old image
                if (!empty($teamMemberPage->image)) {

                    $oldImage = null;

                    if ($basepathstatus && $basepathstatus->base_path_status == 1) {

                        $oldImage = base_path().'/public/admin/assets/img/avatars/'.$teamMemberPage->image;

                    } else {

                        $oldImage = base_path().'/public_html/admin/assets/img/avatars/'.$teamMemberPage->image;
                    }

                    if (file_exists($oldImage)) {
                        @unlink($oldImage);
                    }
                }

                $profile_image = $new_file;

            } elseif (!empty($request->existing_profile_image)) {

                $profile_image = $request->existing_profile_image;
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

                'staff_id' => $request->staff_id,
                'name' => $staff->name ?? null,
                'image' => $profile_image,
                'whatsapp_number' => $request->working_mobile_no,
                'calling_number' => $request->calling_number,

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
            $teamMemberPage = TeamMemberWebPage::find($request->delete_id);

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

    public function getTeamMember(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $teamMember = TeamMemberWebPage::where('staff_id',$request->id)->first();

        if (!$teamMember) {
            return response()->json([
                'status' => false,
                'message' => 'Team member not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $teamMember
        ]);
    }


}
