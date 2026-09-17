<?php

namespace App\Http\Controllers;

use App\AdminModel\Staff;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Session;

class PagesController extends Controller
{

  // Account Settings
  public function account_settings()
  {
    $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => "Pages"], ['name' => "Account Settings"]];
    return view('/content/pages/page-account-settings', ['breadcrumbs' => $breadcrumbs]);
  }

  public function updateuserinfo(Request $request){
    $user = User::where('user_id','=',Auth::user()->user_id)->first();
    $staff = Staff::where('staff_id','=',Auth::user()->user_id)->first();

    // update in user
    $user->mobile = $request->phone;
    $user->save();

    // update in staff in staff
    if(isset($staff)){
      $staff->mobile_no = $request->phone;
      $staff->save();
    }

    Session::flash('success','User information updated!');
    return redirect()->back();
  }

  public function password_change(Request $request)
  {
    $newP = $request->input('new_password');
    $user = User::where('user_id','=',Auth::user()->user_id)->first();
    $user->password = Hash::make($newP);
    $user->save();
    Auth::logout();
    Session::flash('success','Password Change Successfully');
    return redirect('/login');
  }

  public function upadte_pic(Request $request)
  {

    $user = User::where('user_id','=',Auth::user()->user_id)->first();

    $staff = Staff::where('staff_id','=',Auth::user()->user_id)->first();



    // Update user
    $user->username = $request->input('username');
    $user->email = $request->input('email');
    $user->name = $request->input('name');
    
    


    // Update in Staff Table
    if($request->hasFile('staff_image')){
      $file = $request->file('staff_image');
      $file_name = time().'_'.$file->getClientOriginalName();
      $file->move(base_path().'/public/images/avatars',$file_name);
    
    }else{
      $file_name = $user->staff_image;
    }
    
    $user->staff_image = $file_name;

    if(isset($staff)){
      $staff->staff_image = $file_name;
      $staff->username = $request->input('username');
      $staff->staff_pers_email = $request->input('email');
      $staff->save();
    }
    
    
    

    $user->save();
    
    if (Auth::user()->username != $request->input('username') || Auth::user()->email != $request->input('email')) {
      Auth::logout();
    }

    Session::flash('success','Profle Updated!');
    return redirect()->back();
  }

  public function check_pass(Request $request)
  {
    $pass = $request->input('pass');
    $user_id = $request->input('user_id');
    $chk = User::where('user_id','=',$user_id)->first();

    if(Hash::check($pass, $chk->password)){
    }else{
      return response()->json('1');
    }
  }
  // Profile
  public function profile()
  {
    $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => "Pages"], ['name' => "Profile"]];

    return view('/content/pages/page-profile', ['breadcrumbs' => $breadcrumbs]);
  }

  // FAQ
  public function faq()
  {
    $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => "Pages"], ['name' => "FAQ"]];
    return view('/content/pages/page-faq', ['breadcrumbs' => $breadcrumbs]);
  }

  // Knowledge Base
  public function knowledge_base()
  {
    $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => "Pages"], ['name' => "Knowledge Base"]];
    return view('/content/pages/page-knowledge-base', ['breadcrumbs' => $breadcrumbs]);
  }

  // Knowledge Base Category
  public function kb_category()
  {
    $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => "Pages"], ['link' => "/page/knowledge-base", 'name' => "Knowledge Base"], ['name' => "Category"]];
    return view('/content/pages/page-kb-category', ['breadcrumbs' => $breadcrumbs]);
  }

  // Knowledge Base Question
  public function kb_question()
  {
    $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => "Pages"], ['link' => "/page/knowledge-base", 'name' => "Knowledge Base"], ['link' => "/page/kb-category", 'name' => "Category"], ['name' => "Question"]];
    return view('/content/pages/page-kb-question', ['breadcrumbs' => $breadcrumbs]);
  }

  // pricing
  public function pricing()
  {
    $pageConfigs = ['pageHeader' => false];
    return view('/content/pages/page-pricing', ['pageConfigs' => $pageConfigs]);
  }

  // blog list
  public function blog_list()
  {
    $pageConfigs = ['contentLayout' => 'content-detached-right-sidebar', 'bodyClass' => 'content-detached-right-sidebar'];

    $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => "Pages"], ['link' => "javascript:void(0)", 'name' => "Blog"], ['name' => "List"]];

    return view('/content/pages/page-blog-list', ['breadcrumbs' => $breadcrumbs, 'pageConfigs' => $pageConfigs]);
  }

  // blog detail
  public function blog_detail()
  {
    $pageConfigs = ['contentLayout' => 'content-detached-right-sidebar', 'bodyClass' => 'content-detached-right-sidebar'];

    $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => "Pages"], ['link' => "javascript:void(0)", 'name' => "Blog"], ['name' => "Detail"]];

    return view('/content/pages/page-blog-detail', ['breadcrumbs' => $breadcrumbs, 'pageConfigs' => $pageConfigs]);
  }

  // blog edit
  public function blog_edit()
  {

    $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => "Pages"], ['link' => "javascript:void(0)", 'name' => "Blog"], ['name' => "Edit"]];

    return view('/content/pages/page-blog-edit', ['breadcrumbs' => $breadcrumbs]);
  }
}
