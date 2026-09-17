<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\OrderStatus;
use App\Models\Adminpermission;
use App\Models\Candidate;

class OrderStatusController extends Controller
{
    public function index()
  {
      $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
      return view('admin.orderStatus.index',['perm' => $permission]);
  }

  public function indexjson(Request $request)
  {
      $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

      if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
          $posts = OrderStatus::orderBy('id','DESC')->get();
      }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
          if($permission->view_orderStatus == 1){
              $posts = OrderStatus::orderBy('id','DESC')->get();
          }else{
              $posts = OrderStatus::where('admin_id','=',Auth::guard('admin')->user()->id)->orderBy('id','DESC')->get();
          }
      }
      
      $data['data'] = $posts;
      return response()->json($data);
  }

  public function store(Request $request)
  {   
      $post = new OrderStatus();
      $post->ord_status = $request->ord_status;
      $post->ar_status = $request->ar_status;
      $post->admin_id = Auth::guard('admin')->user()->id;
      $post->status = '1';
      $post->save();

      return redirect()->back();
  }

  public function edit(Request $request)
  {
      $id = $request->id;
      $post = OrderStatus::find($id);
      return response()->json($post);
  }

  public function update(Request $request)
  {
      $id = $request->edit_id;
      $post = OrderStatus::find($id);
      $post->ord_status = $request->ord_status;
      $post->ar_status = $request->ar_status;
      $post->status = '1';
      $post->save();

      return redirect()->back();
  }

  
  public function checkarname(Request $request)
  {
      $ar_name = $request->ar_name;
      $post = Profession::where('ar_name','=',$ar_name)->count();
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

  public function edcheckengname(Request $request)
  {
      $id = $request->id;
      $eng_name = $request->eng_name;
      $post = Profession::where('id','!=',$id)->where('eng_name','=',$eng_name)->count();
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

  public function edcheckarname(Request $request)
  {
      $id = $request->id;
      $ar_name = $request->ar_name;
      $post = Profession::where('id','!=',$id)->where('ar_name','=',$ar_name)->count();
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

  public function checkDelProf(Request $request){
      $data1 = Candidate::where('proff_id','=',$request->id)->count();
      $data2 = Visadetails::where('proff_id','=',$request->id)->count();

      if ($data1 > 0 || $data2 > 0 ) {
          return response()->json('1');
      } else {
          
      }
  }

  public function delete(Request $request){
      $post = OrderStatus::find($request->id);

      $post->delete();

      return redirect()->back()->with('success','Order Status Deleted!');
  }
}
