<?php

namespace App\Http\Controllers;

use App\Models\Basepathstatus;
use App\Models\Imagehost;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImageHostController extends Controller
{
    public function index(Request $request){

        $posts = Imagehost::orderBy('id','DESC')->get();
        return view('admin.imagehost.index',compact('posts'));
    }

    public function store(Request $request){

        $basepathstatus = Basepathstatus::first();

        if ($request->hasFile('file_name')) {
            $file = $request->file('file_name');
            $name = $file->getClientOriginalName();
            // Remove Space From image
            $filename_ren = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
            $filename_reps = str_replace(" ","_",$filename_ren);
            $new_file = $filename_reps.'.'.$fileext_ren;

            if ($basepathstatus->base_path_status == 1) {
                $file->move(base_path().'/public/admin/assets/images/imagehost',$new_file);
                
            } else {
                $file->move(base_path().'/public_html/admin/assets/images/imagehost',$new_file);
            }

            $db_file_name = $new_file;
            $db_file_url = url('admin/assets/images/imagehost',$new_file);
        }else{
            $db_file_name = "";
            $db_file_url = "";

        }

        // Upload File into Database
        $post = new Imagehost();
        $post->file_name = $db_file_name;
        $post->file_url = $db_file_url;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','File uploaded!');

    }

    public function delete(Request $request){
        $post = Imagehost::find($request->id);
        $basepathstatus = Basepathstatus::first();
        if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
            $filepath = base_path('public/admin/assets/images/imagehost/'.$post->file_name);
        }else{
            $filepath = base_path('public_html/admin/assets/images/imagehost/'.$post->file_name);
        }

        if(file_exists($filepath)){
            // unlink($filepath);
            File::delete($filepath);
            $post->delete();
        }else{
            $post->delete();
        }

        return redirect()->back()->with('success','File has been deleted!');
    }
}
