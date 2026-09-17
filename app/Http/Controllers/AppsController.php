<?php

namespace App\Http\Controllers;

use App\AdminModel\Staff;
use App\AdminModel\City;
use App\AdminModel\Label;
use App\AdminModel\Todo;
use App\AdminModel\Type;
use App\AdminModel\Country;
use App\AdminModel\OwnerTodo;
use App\AdminModel\TodoAssignee;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Session;
use Auth;
use Carbon\Carbon;
use File;
Use DB;
use App\AdminModel\TaskProcess;
use Illuminate\Http\Request;
use App\AdminModel\TodoFile;
use App\AdminModel\TodoStatusActivity;

class AppsController extends Controller
{
  // invoice list App
  public function invoice_list(){
    $pageConfigs = ['pageHeader' => false];
    return view('/content/apps/invoice/invoice-list', ['pageConfigs' => $pageConfigs]);
  }

  // invoice preview App
  public function invoice_preview(){
    $pageConfigs = ['pageHeader' => false];
    return view('/content/apps/invoice/invoice-preview', ['pageConfigs' => $pageConfigs]);
  }

  public function get_city(Request $request){
    $country_id = $request->input('country_id');
    $all_city = City::where('country_id','=',$country_id)->get();
    $res= '<option value="">Select</option>';
    
    foreach($all_city as $c){
      $res.='<option value='.$c->city_id.'>'.$c->city_name.'<option>';
    }

    $res.='<option value="0">Any</option>';
    $data['res'] = $res;
    return response()->json($data);
  }
  public function get_todo_label(Request $request){
    $type_id = $request->input('type');
    $all_lb = Label::where('type_id','=',$type_id)->get();

    $res= '';
    $res.= '<option></option>';
    foreach ($all_lb as $lb) {
      $res.='<option value='.$lb->id.'>'.$lb->label.'</option>';
    }
    $data['res'] = $res;
    return response()->json($data);
  }

  // invoice edit App
  public function invoice_edit(){
    $pageConfigs = ['pageHeader' => false];

    return view('/content/apps/invoice/invoice-edit', ['pageConfigs' => $pageConfigs]);
  }

  // invoice edit App
  public function invoice_add(){
    $pageConfigs = ['pageHeader' => false];
    return view('/content/apps/invoice/invoice-add', ['pageConfigs' => $pageConfigs]);
  }

  // invoice print App
  public function invoice_print(){
    $pageConfigs = ['pageHeader' => false];
    return view('/content/apps/invoice/invoice-print', ['pageConfigs' => $pageConfigs]);
  }

  // User List Page
  public function user_list(){
    $pageConfigs = ['pageHeader' => false];
    return view('/content/apps/user/app-user-list', ['pageConfigs' => $pageConfigs]);
  }

  public function staff_list(){
    $pageConfigs = ['pageHeader' => false];
    return view('/content/apps/user/app-staff-list', ['pageConfigs' => $pageConfigs]);
  }

  // User View Page
  public function user_view(){
    $pageConfigs = ['pageHeader' => false];
    return view('/content/apps/user/app-user-view', ['pageConfigs' => $pageConfigs]);
  }

  // User Edit Page
  public function user_edit(){
    $pageConfigs = ['pageHeader' => false];
    return view('/content/apps/user/app-user-edit', ['pageConfigs' => $pageConfigs]);
  }


  public function staff_validate(Request $request){
    $email = $request->input('email');
    
    if($email){
      $uchk = Staff::where('staff_pers_email','=',$email)->first();
      $admin_uchk = User::where('email','=',$email)->first();
      if(isset($uchk) || isset($admin_uchk))
      {
        echo 1;
      }else{
        
      }
    }
  }
  public function staff_username_validate(Request $request){
    $username = $request->input('username');
    
    if($username){
      $uchk = Staff::where('username','=',$username)->first();
      $admin_uchk = User::where('username','=',$username)->first();
      if(isset($uchk) || isset($admin_uchk))
      {
        echo 1;
      }else{
        
      }
    }
  }

  public function staff_update(Request $request){
    // extract($_POST);

    if($request->input('accountForm')==1){

      if ($request->hasFile('staff_image')) {
        $file = $request->file('staff_image');
        $file_count = File::files(base_path().'/public/images/staff');
        $filecount = 0;

        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        $file_exe = $file->getClientOriginalExtension();
        $name = $filecount . '.' . $file_exe;

        $file->move(base_path().'/public/images/staff', $name);
        $staff_image = $name;

      } else {
        $staff_image = $request->input('old_staff_image') ? $request->input('old_staff_image') : '';
      }

      $st =  Staff::find($request->edit_id);

      $st->staff_fname = $request->input('staff_fname') ?  $request->input('staff_fname') : $st->staff_fname;
      $st->staff_lname = $request->input('staff_lname') ?  $request->input('staff_lname') : $st->staff_lname;
      $st->staff_branch = $request->input('staff_branch') ?  $request->input('staff_branch') : $st->staff_branch;
      $st->staff_designation = $request->input('staff_designation') ?  $request->input('staff_designation') : $st->staff_designation;
      $st->staff_job_role = $request->input('staff_job_role') ?  $request->input('staff_job_role') : $st->staff_job_role;
      $st->rel_cst_id = $request->input('rel_cst_id') ?  $request->input('rel_cst_id') : $st->rel_cst_id;
      $st->staff_aadhar_no = $request->input('staff_aadhar_no') ?  $request->input('staff_aadhar_no') : $st->staff_aadhar_no;
      $st->staff_family_contact = $request->input('staff_family_contact') ?  $request->input('staff_family_contact') : $st->staff_family_contact;
      $st->mobile_no = $request->input('staff_mobile_no') ?  $request->input('staff_mobile_no') : $st->mobile_no;
      $st->telephone_no = $request->input('telephone_no') ?  $request->input('telephone_no') : $st->telephone_no;
      $st->username = $request->input('username') ?  $request->input('username') : '';
      $st->staff_image = $staff_image ? $staff_image : $st->staff_image;

      $user = User::where('user_id','=',$request->edit_id)->first();
      $user->username = $request->input('username');
      $user->name = $request->input('staff_fname').' '.$request->input('staff_lname');
      $user->staff_image = $staff_image ? $staff_image : $user->staff_image;
      $user->mobile = $request->input('staff_mobile_no') ?  $request->input('staff_mobile_no') : $user->mobile;
      $user->save();

      $st->save();
    }

    /*     social form        */
    if($request->input('socialForm')==1){

      if ($request->hasFile('staff_aadhar_card')) {
        $file = $request->file('staff_aadhar_card');
        $file_count = File::files(base_path().'/public/images/staff');
        $filecount = 0;

        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        $file_exe = $file->getClientOriginalExtension();
        $name = $filecount . '.' . $file_exe;
        $file->move(base_path().'/public/images/staff', $name);
        $staff_aadhar_card = $name;
      } else {
        $staff_aadhar_card = $request->input('old_staff_aadhar_card') ? $request->input('old_staff_aadhar_card') : '';
      }

      if ($request->hasFile('staff_retion_card')) {
        $file = $request->file('staff_retion_card');
        $file_count = File::files(base_path().'/public/images/staff');
        $filecount = 0;

        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        $file_exe = $file->getClientOriginalExtension();
        $name = $filecount . '.' . $file_exe;

        $file->move(base_path().'/public/images/staff', $name);
        $staff_retion_card = $name;
      } else {
        $staff_retion_card = $request->input('old_staff_retion_card') ? $request->input('old_staff_retion_card') : '';
      }

      if ($request->hasFile('cv')) {
        $file = $request->file('cv');
        $file_count = File::files(base_path().'/public/images/staff');
        $filecount = 0;

        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        $file_exe = $file->getClientOriginalExtension();
        $name = $filecount . '.' . $file_exe;

        $file->move(base_path().'/public/images/staff', $name);
        $cv = $name;

      } else {
        $cv = $request->input('old_cv') ? $request->input('old_cv') : ''; 
      }

      if ($request->hasFile('staff_contract')) {
        $file = $request->file('staff_contract');
        $file_count = File::files(base_path().'/public/images/staff');
        $filecount = 0;

        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        $file_exe = $file->getClientOriginalExtension();
        $name = $filecount . '.' . $file_exe;

        $file->move(base_path().'/public/images/staff', $name);
        $staff_contract = $name;

      } else {
        $staff_contract = $request->input('old_staff_contract') ? $request->input('old_staff_contract') : ''; 
      }

      $st =  Staff::find($request->edit_id);
      $st->staff_aadhar_card=$staff_aadhar_card ? $staff_aadhar_card : $st->staff_aadhar_card;
      $st->staff_retion_card=$staff_retion_card ? $staff_retion_card : $st->staff_retion_card;
      $st->staff_cv =$cv ? $cv : $st->staff_cv;
      $st->staff_contract=$staff_contract ? $staff_contract : $st->staff_contract;
      
      $st->save();
    

    }

    if($request->input('personalDataForm')==1){

      // dd($request);

      $st =  Staff::find($request->edit_id);
      $st->address = $request->input('address') ?  $request->input('address') : $st->address;
      $st->staff_dob = $request->input('staff_dob') ?  $request->input('staff_dob') : $st->staff_dob;
      $st->staff_gender = $request->input('staff_gender') ?  $request->input('staff_gender') : $st->staff_gender;
      $st->staff_sallary = $request->input('staff_sallary') ?  $request->input('staff_sallary') : $st->staff_sallary;
      $st->staff_country = $request->input('staff_country') ?  $request->input('staff_country') : $st->staff_country;
      $st->staff_pers_email = $request->input('staff_pers_email') ?  $request->input('staff_pers_email') : $st->staff_pers_email;
      $st->staff_comp_email = $request->input('staff_comp_email') ?  $request->input('staff_comp_email') : $st->staff_comp_email;
      $st->staff_joining_date = $request->input('staff_joining_date') ?  $request->input('staff_joining_date') : $st->staff_joining_date;

      $user = User::where('user_id','=',$request->edit_id)->first();

      $user->email = $request->input('staff_comp_email') ?  $request->input('staff_comp_email') : $user->email;
      $user->save();

      $st->update();
    }

    /*     social form end       */

    Session::flash('success', 'Staff Updated Successfully !');
    return redirect()->back();

  }

  public function staff_store(Request $request){
    // extract($_POST);
    if ($request->hasFile('staff_image')) {
      $file = $request->file('staff_image');
      $file_count = File::files(base_path().'/public/images/staff');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/images/staff', $name);
      $staff_image = $name;

    } else {
      $staff_image = '';
    }

    if ($request->hasFile('staff_aadhar_card')) {
      $file = $request->file('staff_aadhar_card');
      $file_count = File::files(base_path().'/public/images/staff');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/images/staff', $name);
      $staff_aadhar_card = $name;

    } else {
      $staff_aadhar_card = '';
    }

    if ($request->hasFile('staff_retion_card')) {
      $file = $request->file('staff_retion_card');
      $file_count = File::files(base_path().'/public/images/staff');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/images/staff', $name);
      $staff_retion_card = $name;

    } else {
      $staff_retion_card = '';
    }

    if ($request->hasFile('cv')) {
      $file = $request->file('cv');
      $file_count = File::files(base_path().'/public/images/staff');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/images/staff', $name);
      $cv = $name;

    } else {
      $cv = '';
    }

    if ($request->hasFile('staff_contract')) {
      $file = $request->file('staff_contract');
      $file_count = File::files(base_path().'/public/images/staff');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/images/staff', $name);
      $staff_contract = $name;

    } else {
      $staff_contract = '';
    }

    $st =  new Staff();
    $st->staff_fname = $request->staff_fname;
    $st->staff_lname = $request->staff_lname;
    $st->staff_designation= $request->staff_designation;
    $st->staff_job_role = $request->staff_job_role;
    $st->staff_branch = $request->staff_branch;
    $st->staff_aadhar_no = $request->staff_aadhar_no;
    $st->staff_dob = $request->staff_dob;
    $st->staff_gender = $request->staff_gender;
    $st->rel_cst_id = $request->rel_cst_id;
    $st->staff_sallary = $request->staff_sallary;
    $st->staff_country = $request->staff_country;
    $st->mobile_no = $request->staff_mobile_no;
    $st->address = $request->address;
    $st->staff_pers_email = $request->staff_pers_email;
    $st->staff_comp_email = $request->staff_comp_email;
    $st->staff_joining_date = $request->staff_joining_date;
    $st->staff_family_contact = $request->staff_family_contact;
    $st->staff_image = $staff_image;
    $st->staff_aadhar_card = $staff_aadhar_card;
    $st->staff_retion_card = $staff_retion_card;
    $st->staff_cv = $cv;
    $st->staff_contract = $staff_contract;
    $st->username = $request->username;
    $st->password = Hash::make($request->password);
    $st->user_id = Auth::user()->user_id;
    $st->save();

    $last_id = $st->staff_id;
    
    $user = new User();
    $user->user_type = 2;
    $user->user_id = $last_id;
    $user->username = $request->username;
    $user->name = $request->staff_fname.' '.$request->staff_lname;
    $user->password =  Hash::make($request->password);
    $user->email = $request->staff_comp_email;
    $user->mobile = $request->staff_mobile_no;
    $user->staff_image = $staff_image;
    $user->save();

    Session::flash('success', 'Staff Created Successfully !');
    return redirect('master/staff/list');
  }

  public function staff_view($type,$id){
    
    $staff_data = Staff::find($id);
    $pageConfigs = ['pageHeader' => false];
    return view('/content/apps/user/app-staff-view', ['pageConfigs' => $pageConfigs,'staff_data' => $staff_data,'type' => $type]);
  }

  public function staff_edit($type,$id){

    $staff_data = Staff::find($id);
    $pageConfigs = ['pageHeader' => false];
    return view('/content/apps/user/app-staff-edit', ['pageConfigs' => $pageConfigs,'staff_data' => $staff_data,'type' => $type]);
  }

  public function changePass(Request $request){
    $user_id = $request->input('user_id');
    $password = $request->input('newPassword');
    $user = User::where('user_id','=',$user_id)->first();
    $staff = Staff::find($user_id);
    // Update Data
    $user->password = Hash::make($password);
    $user->save();

    $staff->password = Hash::make($password);
    $staff->save();

    Session::flash('success','Password Update Successfully!');
    return redirect()->back();
  }

  public function staff_delete($type,$id){
    $st = Staff::find($id);
    $st->delete();

    $user = User::where(['user_id' => $id],['user_type' => '2'])->first();
    $user->delete();

    if($type=='profile'){
      Session::flash('success', 'Staff Deleted Successfully !');
      return redirect('/');  
    }
    Session::flash('success', 'Staff Deleted Successfully !');
    return redirect('master/staff/list');

  }

  public function staff_list_json(Request $request)
  {
    $all_staff = Staff::all();

    $all_staff_n = DB::table('qr_staff_tbl as staffd')
      ->join('qr_branch_details as branch','staffd.staff_branch','=','branch.br_id')
      ->select('staffd.*','branch.br_name',\DB::raw("(CASE WHEN staffd.staff_status = '1' THEN 'Active' ELSE 'De-Active' END) AS staff_st"),\DB::raw("(CASE WHEN staffd.careoff = '0' THEN 'Active' ELSE 'De-Active' END) AS careoff"))
      ->get();



    $data['data'] = $all_staff_n;
    // $data['data'] = $all_staff ;
    return response()->json($data);
  }

  // Active and Deactive Start
  
  public function active(Request $request){
    extract($_POST);
    $std = Staff::find($staff_id);
    $user = User::where('user_id','=',$staff_id)->first();

    $std->staff_status = 1;
    $user->status = 1;

    $user->save();
    $std->save();

    Session::flash('success','Staff Login Status Active');
    return redirect()->back();

  }

  public function activec(Request $request){
    extract($_POST);
    $std = Staff::find($staff_id);
    $user = User::where('user_id','=',$staff_id)->first();

    $std->careoff = 0;
    $user->careoff = 0;

    $user->save();
    $std->save();

    Session::flash('success','Staff Careoff Status Active');
    return redirect()->back();

  }

  public function deactive(Request $request){
    extract($_POST);
    $std = Staff::find($staff_id);
    $user = User::where('user_id','=',$staff_id)->first();

    $std->staff_status = 0;
    $user->status = 0;

    $user->save();
    $std->save();

    Session::flash('success','Staff Login Status De-Active');
    return redirect()->back();

  }

  public function deactivec(Request $request){
    extract($_POST);
    $std = Staff::find($staff_id);
    $user = User::where('user_id','=',$staff_id)->first();

    $std->careoff = 1;
    $user->careoff = 1;

    $user->save();
    $std->save();

    Session::flash('success','Staff Careoff Status De-Active');
    return redirect()->back();

  }


  // Chat App
  public function chatApp(){
    $pageConfigs = [
      'pageHeader' => false,
      'contentLayout' => "content-left-sidebar",
      'pageClass' => 'chat-application',
    ];

    return view('/content/apps/chat/app-chat', [
      'pageConfigs' => $pageConfigs
    ]);
  }

  // Calender App
  public function calendarApp(){
    $pageConfigs = [
      'pageHeader' => false
    ];

    return view('/content/apps/calendar/app-calendar', [
      'pageConfigs' => $pageConfigs
    ]);
  }

  // Email App
  public function emailApp(){
    $pageConfigs = [
      'pageHeader' => false,
      'contentLayout' => "content-left-sidebar",
      'pageClass' => 'email-application',
    ];

    return view('/content/apps/email/app-email', ['pageConfigs' => $pageConfigs]);
  }

  // ToDo App
  public function change_status(Request $request){
    extract($_POST);
    $td = Todo::find($type);
    if($td->status==1){
      $td->status=0;
      $td->process_status = 1;
    }else if($td->status==0){
      $td->status=1; 
      $td->process_status = 4;
    }
    $td->save();

    return response()->json('1');
  }
  public function change_status_imp(Request $request){
    extract($_POST);
    $td = Todo::find($type);
    if($td->status_imp==1)
    {
      $td->status_imp=0;
    }
    else if($td->status_imp==0)
    {
      $td->status_imp=1; 
    }
    $td->save();

    return response()->json('1');
  }

  public function todo_store(Request $request){

    // dd($request);

    // $task_ass = implode(",",$request->input('task-assigned'));
    // $owner_ass = Auth::user()->user_id;

    // $final_user = $task_ass.','.$owner_ass;

    // dd($final_user);

    if($request->hasFile('file')) {
      $file = $request->file('file');
      $file_count = File::files(base_path().'/public/image/crm-contact');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/crm-contact', $name);
      $profile_pic = $name;

    } else {
      $profile_pic = '';
    }


    $edit_id = $request->input('edit_id');
    if($edit_id){

      $td = Todo::find($edit_id);
      $td->title= $request->input('todoTitleAdd') ? $request->input('todoTitleAdd') : '';
      $td->assignee = $request->input('task-assigned')  ? implode(",", $request->input('task-assigned')) : '';
      $td->type= $request->input('type') ? $request->input('type') : '';
      $td->label=$request->input('label') ? $request->input('label') : '';
      // $td->user_id=Auth::user()->user_id;
      // $td->user_type=Auth::user()->user_type;
      $td->due_date=$request->input('task-due-date') ? $request->input('task-due-date') : '';
      $td->tags= $request->input('task_tag') ? implode(",", $request->input('task_tag')) : '';
      $td->description= $request->input('desc') ? html_entity_decode($request->input('desc')) : '';
      $td->process_status = $request->input('task_process');
      $td->file = $profile_pic;
      $td->pty_id = $request->pty_id;
      
      // Stope Temporary
      if($request->input('task-assigned') !=''){
        
        $td->owner = implode(",",$request->input('task-assigned')).','.$td->user_id;
      }else{
        $td->owner = $td->user_id;
      }

      $dd= $td->save();

      // check if its available on TodoAssignee
      $todoAssC = TodoAssignee::where('todo_id','=',$edit_id)->get();
      if(isset($todoAssC)){
        foreach ($todoAssC as $todoAss) {
          $todoAss->delete();
        }
      }

      // upload file in TodoFile databse
      if($profile_pic != ''){
        $todoFile = new TodoFile();
        $todoFile->todo_id = $edit_id;
        $todoFile->file = $profile_pic;
        $todoFile->save();

      }



      // Check if Owner Todo is available or not
      if(Auth::user()->user_type == 1){
        $ownerTodocs = OwnerTodo::where('todo_id','=',$edit_id)->get();
        if(isset($ownerTodocs)){
          foreach ($ownerTodocs as $ownerTodoc) {
            $ownerTodoc->delete();
          }
        }

        // insert into Owner Tod
        // Store on Owner Todo
        $ownerTodo = new OwnerTodo();
        $ownerTodo->user_id = $td->user_id;
        $ownerTodo->todo_id = $edit_id;
        $ownerTodo->save();

      }else{
        $ownerTodocs = OwnerTodo::where('todo_id','=',$edit_id)->where('user_id','!=','1')->get();
        if(isset($ownerTodocs)){
          foreach ($ownerTodocs as $ownerTodoc) {
            $ownerTodoc->delete();
          }
        }
      }


      // Insert new user


      // Update Assignee
      $assignees = $request->input('task-assigned');
      if($assignees !=''){
        foreach ($assignees as $assignee) {
          if($assignee != '' && $td->user_id != $assignee){
            $assigN = new TodoAssignee();
            $assigN->todo_id = $edit_id;
            $assigN->user_id = $assignee;
            $assigN->save();
          }
        }


        foreach ($assignees as $assignee) {
          if($td->user_id != $assignee){
            if($assignee != ''){
              $ownerN = new OwnerTodo();
              $ownerN->todo_id = $edit_id;
              $ownerN->user_id = $assignee;
              $ownerN->save();
            }
          }
        }

      }
      $assignU = new TodoAssignee();
      $assignU->todo_id = $edit_id;
      $assignU->user_id = $td->user_id;
      $assignU->save();

      // store user detail when change the process
      $task_name = TaskProcess::where('id','=',$request->task_process)->first();

      $todostact = new TodoStatusActivity();
      $todostact->todo_id = $edit_id;
      $todostact->user_id = Auth::user()->user_id;
      $todostact->subject = Auth::user()->name." change process into ".$task_name->name;
      $todostact->save();

      Session::flash('success', 'Todo Update Successfully!');
      return redirect('app/todo');
    }else{
      $td = new Todo();
      $td->title= $request->input('todoTitleAdd') ? $request->input('todoTitleAdd') : '';
      $td->assignee = $request->input('task-assigned')  ? implode(",", $request->input('task-assigned')): '';
      $td->type= $request->input('type') ? $request->input('type') : '';
      $td->label=$request->input('label') ? $request->input('label') : '';
      
      $td->user_id=Auth::user()->user_id;
      $td->user_type=Auth::user()->user_type;
      
      $td->due_date=$request->input('task-due-date') ? $request->input('task-due-date') : '';
      $td->tags= $request->input('task_tag') ? implode(",", $request->input('task_tag')) : '';
      $td->description= $request->input('desc') ? html_entity_decode($request->input('desc')) : '';
      $td->pty_id = $request->pty_id;
      //$td->description=$description
      $td->process_status = $request->input('task_process');
      $td->file = $profile_pic;
      if($request->input('task-assigned') !=''){
        $td->owner = implode(",",$request->input('task-assigned')).','.Auth::user()->user_id;
      }else{
        $td->owner = Auth::user()->user_id;
      }
      $td->save();

      $get_last_id = $td->id;
      // Store on Owner Todo
      // $ownerTodo = new OwnerTodo();
      // $ownerTodo->user_id = $td->user_id;
      // $ownerTodo->todo_id = $get_last_id;
      // $ownerTodo->createby_id = $td->user_id;
      // $ownerTodo->save();

      // Insert into Assignee table
      $assignees = $request->input('task-assigned');
      if($assignees != ''){
        foreach ($assignees as $assignee) {
          if($assignee != '' && $assignee != Auth::user()->user_id){
            $assigN = new TodoAssignee();
            $assigN->todo_id = $get_last_id;
            $assigN->user_id = $assignee;
            $assigN->save();
          }
        }
      }
      $assigneeU = new TodoAssignee();
      $assigneeU->todo_id = $get_last_id;
      $assigneeU->user_id = Auth::user()->user_id;
      $assigneeU->save();

      // Insert in Owner Table
      // if($assignees != ''){
      //   foreach ($assignees as $assignee) {
      //     if($td->user_id != $assignee){
      //       if($assignee !=''){
      //         $ownerTodo = new OwnerTodo();
      //         $ownerTodo->user_id = $assignee;
      //         $ownerTodo->todo_id = $get_last_id;
      //         $ownerTodo->createby_id = $td->user_id;
      //         $ownerTodo->save();
      //       }
      //     }
      //   }
      // }

      if($profile_pic != ''){
        $todoFile = new TodoFile();
        $todoFile->todo_id = $get_last_id;
        $todoFile->file = $profile_pic;
        $todoFile->save();
      }

      Session::flash('success', 'Todo Created Successfully !');
      return redirect('app/todo');
    }
  }

  public function todoListn()
  {
    $pageConfigs = [
      'pageHeader' => false,
      'contentLayout' => "content-left-sidebar",
      'pageClass' => 'todo-application',
    ];

    return view('/content/apps/todo/app-todo2',[
      'pageConfigs' => $pageConfigs
    ]);
  }


// public function todo_update(Request $request)
// {
//   extract($_POST);
//   $td = Todo::find($edit_id);
//    $td->title= $request->input('todoTitleAdd') ? $request->input('todoTitleAdd') : '';
//   $td->assignee = $request->input('task-assigned')  ? $request->input('task-assigned') : '';
//   $td->type= $request->input('type') ? $request->input('type') : '';
//   $td->label=$request->input('label') ? $request->input('label') : '';
//   $td->due_date=$request->input('task-due-date') ? $request->input('task-due-date') : '';
//   $td->tags= $request->input('task-tag') ? $request->input('task-tag') : '';
//    $td->description= $request->input('desc') ? html_entity_decode($request->input('desc')) : '';
//   $dd= $td->save();

//   Session::flash('success', 'Todo Update Successfully !');
//   return redirect('app/todo');
// }

  public function todo_delete(Request $request){
    // extract($_POST);
    $id = $request->id;
    $td = Todo::find($id);
    $td->status = '3';
    $dd=  $td->save();
    Session::flash('success', 'Todo Removed Successfully !');
    return redirect('app/todo');
  }

  public function todo_delete_permanent(Request $request){
    extract($_POST);
    $td = Todo::find($id);
    $td->delete();

    echo '1';
  }

  public function todo_edit(Request $request){
    extract($_POST);

  }

  public function add_todo_type(Request $request){
    $tp = new Type();
    $tp->type = $request->input('type');
    $tp->save();

    $all_tp = Type::all();

    $res= '';
    $res.= '<option></option>';
    $res.= '<option value="add">--Add Type--</option>';
    foreach ($all_tp as $tp) {
      $res.='<option value='.$tp->id.'>'.$tp->type.'</option>';
    }
    $data['res'] = $res;
    return response()->json($data);
  }

  public function add_todo_label(Request $request){
    $lb = new Label();
    $lb->label = $request->input('label');
    $lb->save();

    $all_lb = Label::all();
    
    $res= '';
    $res.= '<option></option>';
    $res.= '<option value="add">--Add Type--</option>';
    foreach ($all_lb as $lb) {
      $res.='<option value='.$lb->id.'>'.$lb->label.'</option>';
    }
    $data['res'] = $res;
    return response()->json($data);
  }

  public function add_todo_taskprocess(Request $request){
    $tsp = new TaskProcess();
    $tsp->name = $request->input('taskProcess');
    $tsp->save();

    $all_lb = TaskProcess::all();
    
    $res= '';
    $res.= '<option></option>';
    $res.= '<option value="add">--Add Type--</option>';
    foreach ($all_lb as $lb) {
      $res.='<option value='.$lb->id.'>'.$lb->name.'</option>';
    }
    $data['res'] = $res;
    return response()->json($data);

  }


  public function todoApp(){

    $pageConfigs = [
      'pageHeader' => false,
      'contentLayout' => "content-left-sidebar",
      'pageClass' => 'todo-application',
    ];

      // dd(Auth::user()->user_id);
    if(Auth::user()->user_id == 1){
      // $all_td = Todo::where('status','!=','3')->get();

      $all_td = DB::table('qr_todo as todo')
        ->leftjoin('users as user','todo.user_id','=','user.user_id')
        ->leftjoin('task_processes as tsp','todo.process_status','=','tsp.id')
        ->select('todo.*','user.name as uname','tsp.name as task_status')
        ->where('todo.status','!=','3')
        ->orderBy('id','DESC')
        ->get();

    }else{
    
      $all_td = DB::table('qr_todo as todo')
        ->leftjoin('users as user','todo.user_id','=','user.user_id')
        ->leftjoin('task_processes as tsp','todo.process_status','=','tsp.id')
        ->select('todo.*','user.name as uname','tsp.name as task_status')
        // ->where('todo.user_id','=',Auth::user()->user_id)
        ->whereRaw("find_in_set('".Auth::user()->user_id."',todo.owner)")
        ->where('todo.status','!=','3')
        ->orderBy('id','DESC')
        ->get();

      // $all_td = DB::table('qr_todo as todo')
      //     ->leftjoin('users as user','todo.user_id','=','user.user_id')
      //     ->leftjoin('todo_assignees as todoas','todoas.todo_id','=','todo.id')
      //     ->select('todo.*')
      //     ->where('todo.user_id',Auth::user()->user_id)
      //     ->orWhere('todoas.user_id',Auth::user()->user_id)
      //     ->where('todo.status','!=','3')
      //     ->get();


      // $all_todo = Todo::where('status','!=','3');
      // $all_todo->where('user_id','=', Auth::user()->user_id);
      // $all_todo->where('user_type','=', Auth::user()->user_type);
      // $all_todo->OrwhereRaw("find_in_set('".Auth::user()->user_id."',assignee)");
      // $all_td = $all_todo->get();
    }
    

    return view('/content/apps/todo/app-todo', [
      'pageConfigs' => $pageConfigs,'all_td' => $all_td
    ]);
  }
  // File manager App
  public function file_manager(){
    $pageConfigs = [
      'pageHeader' => false,
      'contentLayout' => "content-left-sidebar",
      'pageClass' => 'file-manager-application',
    ];

    return view('/content/apps/fileManager/app-file-manager', ['pageConfigs' => $pageConfigs]);
  }

  // Kanban App
  public function kanbanApp(){
    $pageConfigs = [
      'pageHeader' => false,
      'pageClass' => 'kanban-application',
    ];

    return view('/content/apps/kanban/app-kanban', ['pageConfigs' => $pageConfigs]);
  }

  // Ecommerce Shop
  public function ecommerce_shop(){
    $pageConfigs = [
      'contentLayout' => "content-detached-left-sidebar",
      'pageClass' => 'ecommerce-application',
    ];

    $breadcrumbs = [
      ['link' => "/", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => "eCommerce"], ['name' => "Shop"]
    ];

    return view('/content/apps/ecommerce/app-ecommerce-shop', [
      'pageConfigs' => $pageConfigs,
      'breadcrumbs' => $breadcrumbs
    ]);
  }

  // Ecommerce Details
  public function ecommerce_details(){
    $pageConfigs = [
      'pageClass' => 'ecommerce-application',
    ];

    $breadcrumbs = [
      ['link' => "/", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => "eCommerce"], ['link' => "/app/ecommerce/shop", 'name' => "Shop"], ['name' => "Details"]
    ];

    return view('/content/apps/ecommerce/app-ecommerce-details', [
      'pageConfigs' => $pageConfigs,
      'breadcrumbs' => $breadcrumbs
    ]);
  }

  // Ecommerce Wish List
  public function ecommerce_wishlist(){
    $pageConfigs = [
      'pageClass' => 'ecommerce-application',
    ];

    $breadcrumbs = [
      ['link' => "/", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => "eCommerce"], ['name' => "Wish List"]
    ];

    return view('/content/apps/ecommerce/app-ecommerce-wishlist', [
      'pageConfigs' => $pageConfigs,
      'breadcrumbs' => $breadcrumbs
    ]);
  }

  // Ecommerce Checkout
  public function ecommerce_checkout(){
    $pageConfigs = [
      'pageClass' => 'ecommerce-application',
    ];

    $breadcrumbs = [
      ['link' => "/", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => "eCommerce"], ['name' => "Checkout"]
    ];

    return view('/content/apps/ecommerce/app-ecommerce-checkout', [
      'pageConfigs' => $pageConfigs,
      'breadcrumbs' => $breadcrumbs
    ]);
  }

  public function todoDelete2(Request $request){
    $id = $request->todo_id;
    $post = Todo::find($id);
    $post->delete();
    Session::flash('success','Todo deleted!');
    return redirect()->back();
  }

  public function todo_filter(Request $request){
    extract($_POST);

    if(Auth::user()->user_type == 1){

      $delete='';

      $all_todo = DB::table('qr_todo as todo')
        ->leftjoin('users as user','todo.user_id','=','user.user_id')
        ->leftjoin('task_processes as tsp','todo.process_status','=','tsp.id')
        ->select('todo.*','user.name as uname','tsp.name as task_status');

        

        // if ($request->input('status')=='3') {
        //   $all_todo->where('todo.status','!=',4);
        //   $delete='1';
        // }else{
        //   $all_todo->where('todo.status','!=',3);
        // }

        if ($request->input('taskPro')=='Trash') {
          $all_todo->where('todo.status','!=',4);
          $delete='1';
        }else{
          $all_todo->where('todo.status','!=',3);
        }

        if ($request->input('label_id')) {
          $all_todo->where('todo.label','=', $request->label_id);
        }



        // if ($request->input('status')) {

        //   if($request->input('status')=='important'){
        //     $all_todo->where('todo.status_imp','=', 1);  
        //   }
    
        //   $all_todo->where('todo.status','=', $request->status);
          
        // }

        


        if ($request->input('tags')) {
          $all_todo->whereRaw("find_in_set('".$request->tags."',todo.tags)");
        }

        if($request->input('staff')){
          $all_todo->whereRaw("find_in_set('".$request->staff."',todo.owner)");
        }

        
        

        if($request->input('taskPro')){
          if($request->input('taskPro') == 'Important'){
            $all_todo->where('todo.status_imp','=', 1); 
          }elseif ($request->input('taskPro') == 'Trash') {
            $all_todo->where('todo.status','=', 3);
          }else{
            $all_todo->where('todo.process_status',$request->taskPro);
          }
 
        }

    }else{
      $delete='';

      $all_todo = DB::table('qr_todo as todo')
        ->leftjoin('users as user','todo.user_id','=','user.user_id')
        ->leftjoin('task_processes as tsp','todo.process_status','=','tsp.id')
        ->select('todo.*','user.name as uname','tsp.name as task_status')
        // ->where('todo.user_id','=',Auth::user()->user_id)
        ->WhereRaw("find_in_set('".Auth::user()->user_id."',todo.owner)");
        
        // if ($request->input('status')=='3') {
        //   $all_todo->where('todo.status','!=',4);
        //   $delete='1';
        // }else{
        //   $all_todo->where('todo.status','!=',3);
        // }

        if ($request->input('taskPro')=='Trash') {
          $all_todo->where('todo.status','!=',4);
          $delete='1';
        }else{
          $all_todo->where('todo.status','!=',3);
        }

        if ($request->input('label_id')) {
          $all_todo->where('todo.label','=', $request->label_id);
        }

        // if ($request->input('status')) {
        //   if($request->input('status')=='important'){
        //     $all_todo->where('todo.status_imp','=', 1);  
        //   }
    
        //   $all_todo->where('todo.status','=', $request->status);
        // }

        if ($request->input('tags')) {
          $all_todo->whereRaw("find_in_set('".$request->tags."',todo.tags)");
        }
    
        if($request->input('taskPro')){
          if($request->input('taskPro') == 'Important'){
            $all_todo->where('todo.status_imp','=', 1); 
          }elseif ($request->input('taskPro') == 'Trash') {
            $all_todo->where('todo.status','=', 3);
          }else{
            $all_todo->where('todo.process_status',$request->taskPro);
          }
 
        }

        // if($request->input('taskPro')){
        //   $all_todo->where('todo.process_status',$request->taskPro);
        // }

    }

    $all_td = $all_todo->orderby('id','DESC')->get();

    if (isset($all_td)){
        $badgeColor = [
          'Team' => 'primary',
          'Low' => 'success',
          'Medium' => 'warning',
          'High' => 'danger',
          'Update' => 'info'
        ];
      
        foreach($all_td as $td){?>
          <li  <?php if($td->status==1) {?> class="todo-item todo-afdel-<?php echo $td->id?> completed" <?php }else{?> class="todo-item todo-afdel-<?php echo $td->id?>" <?php }?> >
            <div class="todo-title-wrapper">
              <div class="todo-title-area">
                <i data-feather="more-vertical" class="drag-icon"></i>
                <div class="title-wrapper">           
                  <span class="todo-title text-capitalize badge <?php if($td->process_status == 3){ 
                    echo 'badge-light-primary';
                    }elseif($td->process_status == 1){ 
                      echo 'badge-light-warning';
                      }elseif($td->process_status == 4){ 
                        echo 'badge-light-success';
                      }elseif($td->process_status == 5){ 
                        echo 'badge-light-danger';
                      }else{
                        echo 'badge-light-secondary';
                      } ?>"><?php echo $td->title?></span>
                  <span class="todo-desc12 text-capitalize">&nbsp;&nbsp;&nbsp;<span style="color: grey;"><?php echo strip_tags(substr($td->description,0,50)) ?>... </span>  </span>
                  <span class="todo-id" style="display: none;"><?php echo $td->id?></span>
                  <span class="todo-desc" style="display: none;"><?php echo  html_entity_decode($td->description)?></span>
                  <span class="todo-assign-id" style="display: none;"><?php echo $td->assignee?></span>
                  <span class="todo-label-hidden" style="display: none;"><?php echo $td->label?></span>
                  <span class="todo-imp-hidden" style="display: none;"><?php echo $td->status_imp ?></span>
                  <span class="todo-tag-hidden" style="display: none;"><?php echo  $td->tags ?></span>
                  <span class="todo-username-hidden" style="display: none;"><?php echo $td->uname  ?></span>
                  <span class="todo-process-task" style="display: none;"><?php echo $td->process_status ?></span>
                  <span class="todo-file-hidden" style="display:none;"><?php echo $td->file ?></span>
                  <span class="todo-due-date" style="display:none"><?= $td->due_date ?></span>
                  <span class="tofo-ptyID-hidden" style="display:none"><?= $td->pty_id ?></span>
                </div>
              </div>
              <div class="todo-item-action">
                <div class="badge-wrapper mr-1">
                  <span class="mr-1 badge badge-pill <?php if($td->process_status == 3){ 
                    echo 'badge-light-primary';
                    }elseif($td->process_status == 1){ 
                      echo 'badge-light-warning';
                      }elseif($td->process_status == 4){ 
                        echo 'badge-light-success';
                      }elseif($td->process_status == 5){ 
                        echo 'badge-light-danger';
                      }else{
                        echo 'badge-light-secondary';
                      } ?>"><?= $td->task_status ?></span>
                  <?php
                    $tags =explode(",", $td->tags);
                    foreach($tags as $tag){
                  ?>
                  <div id="todo-tag-hidden-array" class="badge badge-pill badge-light-<?php echo $badgeColor[$tag] ?>"><?php echo  $tag ?></div>
                  <?php }?>
                </div>
                <small class="text-nowrap text-muted mr-1"><?php echo  $td->due_date ?></small>
                <?php if($td->status_imp==1){?>
                <span class="badge badge-pill badge-light-warning">IMP</span>
                <?php }?>
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" onclick="change_status('<?php echo  $td->id ?>')" id="customCheck<?php echo $td->id ?>" <?php if($td->status==1){?>  checked <?php }?>/>
                  <label class="custom-control-label" for="customCheck<?php echo $td->id ?>"></label>
                </div>
                  
                

                <?php
                  if($request->input('taskPro')=='Trash' && Auth::user()->user_id == 1){?>
                    <div class="custom-control custom-checkbox">    
                      <a  onclick="delete_permanent('<?php echo $td->id ?>')" href="#"><span class="badge badge-pill badge-light-danger">Delete</span></a>
                    </div>              
                  <?php }
                ?>
              </div>
            </div>
          </li>  
    
        <?php
        }
      }
    
  
  }
}
