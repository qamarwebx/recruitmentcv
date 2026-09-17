<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\Adminprofile;
use App\Models\Booking;
use App\Models\Candidate;
use App\Models\Carnknown;
use App\Models\City;
use App\Models\Country;
use App\Models\Mailcredential;
use App\Models\Partner;
use App\Models\Personaliseclass;
use App\Models\Placeofissue;
use App\Models\Profession;
use App\Models\Region;
use App\Models\Templatecampaign;
use App\Models\Visadetails;
use App\Models\Websiteconfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Basepathstatus;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        return view('admin.staff.index',['perm' => $permission]);
    }

    public function indexjson(Request $request)
    {

        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $posts = Admin::orderBy('id','DESC')->get();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_staff == 1){
                $posts = Admin::orderBy('id','DESC')->where('user_type','!=',1)->get();
            }else{
                $posts = Admin::where('createby_id','=',Auth::guard('admin')->user()->id)->where('user_type','!=',2)->orderBy('id','DESC')->get();
            }
        }


        $data['data'] = $posts;
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$this->canManageStaff('add_staff')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to perform this action.'
            ], 403);
        }

        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email'],
                'phone' => ['nullable', 'regex:/^[0-9]+$/'],
                'password' => ['required', 'string', 'min:6'],
                'username' => ['nullable', 'string', 'max:255', 'unique:admins,username'],
            ], [
                'email.unique' => 'The email is already exists',
                'username.unique' => 'The username is already exists',
                'phone.regex' => 'Please enter only digits',
            ]);

            $post = new Admin();
            $post->name = $validated['name'];
            $post->username = $validated['username'] ?? null;
            $post->email = $validated['email'];
            $post->phone = $validated['phone'] ?? null;
            $post->password = Hash::make($validated['password']);
            $post->user_type = 2;
            $post->createby_id = Auth::guard('admin')->user()->id;
            $post->last_login_at = now();
            $post->save();

            return response()->json([
                'success' => true,
                'message' => 'Staff added successfully!',
                'data' => $post,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Determine whether the current admin can perform the given staff action.
     */
    private function canManageStaff(string $permissionField): bool
    {
        $admin = Auth::guard('admin')->user();

        if ($admin->user_type == 1) {
            return true;
        }

        $permission = Adminpermission::where('staff_id', '=', $admin->id)->first();

        if (!$permission) {
            return false;
        }

        if ($permission->full_access == 1) {
            return true;
        }

        return (bool) $permission->{$permissionField};
    }

    public function editlist(Request $request)
    {
        $id = $request->id;
        $post = Admin::find($id);
        return response()->json($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = DB::table('admins as admin')
        ->leftJoin('adminprofiles as profile','admin.id','profile.user_id')
        ->select('admin.*','profile.dob','profile.photo','profile.gender','profile.religion','profile.designation','profile.address','profile.region_id','profile.city_id','profile.pincode')
        ->where('admin.id','=',$id)
        ->first();

        $regions = Region::orderBy('name','ASC')->get();
        $cities = City::orderBy('name','ASC')->get();

        return view('admin.staff.show',['post' => $post,'regions' => $regions,'cities' => $cities]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if (!$this->canManageStaff('edit_staff')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to perform this action.'
            ], 403);
        }

        try {
            $post = Admin::findOrFail($request->edit_id);

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email,' . $post->id],
                'phone' => ['nullable', 'regex:/^[0-9]+$/'],
                'username' => ['nullable', 'string', 'max:255', 'unique:admins,username,' . $post->id],
                'work_number' => ['nullable', 'string', 'max:255'],
                'care_no_1' => ['nullable', 'string', 'max:255'],
                'care_no_2' => ['nullable', 'string', 'max:255'],
            ], [
                'email.unique' => 'The email is already exists',
                'username.unique' => 'The username is already exists',
                'phone.regex' => 'Please enter only digits',
            ]);

            $post->name = $validated['name'];
            $post->username = $validated['username'] ?? null;
            $post->email = $validated['email'];
            $post->phone = $validated['phone'] ?? null;
            $post->work_number = $validated['work_number'] ?? null;
            $post->whatsaapp_chat_url = $request->whatsaapp_chat_url;
            $post->care_no_1 = $validated['care_no_1'] ?? null;
            $post->care_no_2 = $validated['care_no_2'] ?? null;
            $post->save();

            return response()->json([
                'success' => true,
                'message' => 'Staff updated successfully!',
                'data' => $post,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function deleteStaff(Request $request){
        if (!$this->canManageStaff('delete_staff')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to perform this action.'
            ], 403);
        }

        try {
            $post = Admin::findOrFail($request->staff_id);
            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Staff deleted!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to delete staff: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function updatetoactive(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:admins,id',
        ]);

        $staff = Admin::findOrFail($request->staff_id);

        $staff->status = true;
        $staff->save();

        return response()->json([
            'status' => true,
            'message' => 'Staff activated successfully.'
        ]);
    }

    public function updatetodeactive(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:admins,id',
        ]);

        $staff = Admin::findOrFail($request->staff_id);

        $staff->status = false;
        $staff->save();

        return response()->json([
            'status' => true,
            'message' => 'Staff deactivated successfully.'
        ]);
    }

    // public function updatetoactive(Request $request){

        //     $post = Admin::find($request->staff_id);

        //     $post->status = true;
        //     $post->save();

        //     return redirect()->back()->with('success','Staff activated!');

    // }

    // public function updatetodeactive(Request $request){
    //     $post = Admin::find($request->staff_id);

    //     $post->status = false;
    //     $post->save();

    //     return redirect()->back()->with('success','Staff inactive!');

    // }

    // public function updatetoactiveCareoff(Request $request){
    //     $post = Admin::find($request->careoff_id);
    //     $post->login_status = true;
    //     $post->save();

    //     return redirect()->back()->with('success','Staff activated!');

    // }

    // public function updatetodeactiveCareoff(Request $request){
    //     $post = Admin::find($request->careoff_id);
    //     $post->login_status = false;
    //     $post->save();

    //     return redirect()->back()->with('success','Staff inactive!');

    // }


    public function updatetoactiveCareoff(Request $request)
    {
        $post = Admin::findOrFail($request->careoff_id);

        $post->login_status = true;
        $post->save();

        return response()->json([
            'status' => true,
            'message' => 'Staff activated successfully.'
        ]);
    }

    public function updatetodeactiveCareoff(Request $request)
    {
        $post = Admin::findOrFail($request->careoff_id);

        $post->login_status = false;
        $post->save();

        return response()->json([
            'status' => true,
            'message' => 'Staff deactivated successfully.'
        ]);
    }

    public function checkemail(Request $request)
    {
        $email = $request->email;
        $post = Admin::where('email','=',$email)->count();

        // if ($post == 0) {
        //     echo "true";
        // } else {
        //     echo "false";
        // }

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));

    }

    public function checkusername(Request $request)
    {
        $username = $request->username;
        $post = Admin::where('username','=',$username)->count();

        // if($post == 0){
        //     echo "true";
        // }else{
        //     echo "false";
        // }

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function checkphone(Request $request)
    {
        $phone = $request->phone;
        $post = Admin::where('phone','=',$phone)->count();
        // if($post == 0){
        //     echo "true";
        // }else{
        //     echo "false";
        // }

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edcheckemail(Request $request)
    {
        $email = $request->email;
        $id = $request->id;
        $post = Admin::where('email','=',$email)->where('id','!=',$id)->count();

        // if ($post == 0) {
        //     echo "true";
        // } else {
        //     echo "false";
        // }

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));

    }

    public function edcheckusername(Request $request)
    {
        $username = $request->username;
        $id = $request->id;
        $post = Admin::where('username','=',$username)->where('id','!=',$id)->count();

        // if($post == 0){
        //     echo "true";
        // }else{
        //     echo "false";
        // }

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edcheckphone(Request $request)
    {
        $phone = $request->phone;
        $id = $request->id;
        $post = Admin::where('phone','=',$phone)->where('id','!=',$id)->count();
        // if($post == 0){
        //     echo "true";
        // }else{
        //     echo "false";
        // }

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function uploadProfile(Request $request,$id)
    {
        // dd($request->all());
        $admin = Admin::find($id);
        $profile = Adminprofile::where('user_id','=',$id)->first();

        $basepathstatus = Basepathstatus::first();

        // upload photo files
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $name = $file->getClientOriginalName();

            // remove space from image
            $filename_ren1 = pathinfo($name, PATHINFO_FILENAME);
            $fileext_ren1 = pathinfo($name, PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ", "_", $filename_ren1);
            $new_file1 = $repspfilename.'.'.$fileext_ren1;
            if($basepathstatus->base_path_status == 1){
                $file->move(base_path().'/public/admin/assets/img/avatars/',$new_file1);
            }else{
                $file->move(base_path().'/public_html/admin/assets/img/avatars/',$new_file1);
            }
            $profile_photo = $new_file1;
        } else {
            $profile_photo = $admin->profile;
        }


        // Update in Admin Table
        $admin->name = $request->name;
        $admin->username = $request->username;
        $admin->role = json_encode($request->role ?? []);
        $admin->phone = $request->phone;
        $admin->profile = $profile_photo;
        $admin->working_email = $request->working_email;
        $admin->work_number = $request->work_number;
        $admin->care_no_1 = $request->care_no_1;
        $admin->care_no_2 = $request->care_no_2;
        $admin->save();


        // Update in Admin Profile
        if (isset($profile)) {
            $profile->dob = date('Y-m-d',strtotime($request->dob));
            $profile->photo = $profile_photo;
            $profile->gender = $request->gender;
            $profile->religion = $request->religion;
            $profile->designation = $request->designation;
            $profile->address = $request->address;
            $profile->region_id = $request->region_id;
            $profile->city_id = $request->city_id;
            $profile->pincode = $request->pincode;
            $profile->save();
        } else {
            $post = new Adminprofile();
            $post->user_id = $id;
            $post->dob = date('Y-m-d',strtotime($request->dob));
            $post->photo = $profile_photo;
            $post->gender = $request->gender;
            $post->religion = $request->religion;
            $post->designation = $request->designation;
            $post->address = $request->address;
            $post->region_id = $request->region_id;
            $post->city_id = $request->city_id;
            $post->pincode = $request->pincode;
            $post->save();
        }

        return redirect()->back()->with('success','profile updated!');

    }

    public function checkpassword(Request $request)
    {
        $post = Admin::find($request->id);
        if (Hash::check($request->password,$post->password)) {
            $isAvailable = 'true';
        } else {
            $isAvailable = 'false';
        }
        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function passwordchange(Request $request,$id)
    {
        if($request->has('login_access_email_admin_id')){
            $post = Admin::find($request->login_access_email_admin_id);
            $post->login_access_email = $request->login_access_email;
            $post->save();

            return redirect()->back()->with('success','Login access email changed successfully!');
        }
        else{
           
            $post = Admin::find($id);
            $post->password = Hash::make($request->newPassword);
            $post->save();

            // If Staff login then redirect to login page
            if (Auth::guard('admin')->user()->id == $id) {

                Auth::guard('admin')->logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();

                return redirect('/admin/login');
            }

            return redirect()->back()->with('success','Password changed successfully!');

        }

    }


    public function checkStaffExist(Request $request){
        $data1 = Candidate::where('admin_id','=',$request->id)->count();
        $data2 = Partner::where('admin_id','=',$request->id)->count();
        $data3 = Booking::where('payconfirm_admin_id','=',$request->id)->count();
        $data4 = Activity::where('admin_id','=',$request->id)->count();
        $data5 = Carnknown::where('admin_id','=',$request->id)->count();
        $data6 = City::where('admin_id','=',$request->id)->count();
        $data7 = Country::where('admin_id','=',$request->id)->count();
        $data8 = Mailcredential::where('admin_id','=',$request->id)->count();
        $data9 = Personaliseclass::where('admin_id','=',$request->id)->count();
        $data10 = Placeofissue::where('admin_id','=',$request->id)->count();
        $data11 = Profession::where('admin_id','=',$request->id)->count();
        $data12 = Region::where('admin_id','=',$request->id)->count();
        $data13 = Templatecampaign::where('staff_id','=',$request->id)->count();
        $data14 = Visadetails::where('admin_id','=',$request->id)->count();

        if ($data1 > 0 ||
            $data2 > 0 ||
            $data3 > 0 ||
            $data4 > 0 ||
            $data5 > 0 ||
            $data6 > 0 ||
            $data7 > 0 ||
            $data8 > 0 ||
            $data9 > 0 ||
            $data10 > 0 ||
            $data11 > 0 ||
            $data12 > 0 ||
            $data13 > 0 ||
            $data14 > 0) {
            return response()->json('1');
        } else {

        }

    }

}
