<?php

namespace App\Http\Controllers;

use App\Models\Basepathstatus;
use App\Models\Frontendwebsiteconfig;
use App\Models\Socialmediaauth;
use App\Models\Websiteconfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class WebsiteConfigController extends Controller
{
    public function index(Request $request)
    {
        $post = Websiteconfig::first();
        $googleauth = Socialmediaauth::where('type','=','google')->first();
        $facebookauth = Socialmediaauth::where('type','=','facebook')->first();
        // dd($post);
        return view('admin.websiteconfig.index',compact('post','googleauth','facebookauth'));
    }

    public function store(Request $request)
    {
        $post = Websiteconfig::first();
        if ($request->booking_panel != "") {
            $post->booking_panel = $request->booking_panel;
        }

        if ($request->order_receieved != "") {
            $post->order_receieved = $request->order_receieved;
        }

        if ($request->max_booking_limit != '') {
            $post->max_booking_limit = $request->max_booking_limit;
        }

        if ($request->max_cand_booking_limit != '') {
            $post->cand_booking_limit = $request->max_cand_booking_limit;
        }

        if ($request->whatsapp_number != '') {
            $post->whatsapp_number = $request->whatsapp_number;
        }

        if ($request->otp_verification != '') {
            $post->otp_verification = $request->otp_verification;
        }

        if ($request->meta_response != '') {
            $post->meta_response = $request->meta_response;
        }

        $post->save();

        // Session::flash('success','Website configuration updated!');
        
        return redirect()->back()->with('success','Website configuration updated!');
    }

    public function socialgoogleauth(Request $request){
        if ($request->google_auth_id != '') {
            $updateP = Socialmediaauth::find($request->google_auth_id);
            $updateP->client_id = $request->google_client_id;
            $updateP->client_secret = $request->google_client_secret;
            $updateP->callback_url = $request->callback_url;
            $updateP->save();
        }else{
            $post = new Socialmediaauth();
            $post->type = $request->type;
            $post->client_id = $request->google_client_id;
            $post->client_secret = $request->google_client_secret;
            $post->callback_url = $request->callback_url;
            $post->admin_id = Auth::guard('admin')->user()->id;
            $post->save();
        }

        return redirect()->back()->with('success','Google secret Key and ID is updated!');
    }

    public function checkgclientid(Request $request){
        if($request->id != ''){
            $post = Socialmediaauth::where('id','!=',$request->id)->where('type','!=','google')->where('client_id','=',$request->client_id)->count();

            if ($post == 0) {
                $isAvailable = 'true';
            }else{
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }else{
            $post = Socialmediaauth::where('client_id','=',$request->client_id)->where('type','!=','google')->count();

            if ($post == 0) {
                $isAvailable = 'true';
            }else{
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }
    }

    public function checkgclientsecret(Request $request){
        if($request->id != ''){
            $post = Socialmediaauth::where('id','!=',$request->id)->where('type','!=','google')->where('client_secret','=',$request->client_secret)->count();

            if ($post == 0) {
                $isAvailable = 'true';
            }else{
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }else{
            $post = Socialmediaauth::where('client_secret','=',$request->client_secret)->where('type','!=','google')->count();

            if ($post == 0) {
                $isAvailable = 'true';
            }else{
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }
    }

    
    public function socialfacebookauth(Request $request){
        if ($request->facebook_auth_id != '') {
            $updateP = Socialmediaauth::find($request->facebook_auth_id);
            $updateP->client_id = $request->facebook_client_id;
            $updateP->client_secret = $request->facebook_client_secret;
            $updateP->callback_url = $request->callback_url;
            $updateP->save();
        }else{
            $post = new Socialmediaauth();
            $post->type = $request->type;
            $post->client_id = $request->facebook_client_id;
            $post->client_secret = $request->facebook_client_secret;
            $post->admin_id = Auth::guard('admin')->user()->id;
            $post->callback_url = $request->callback_url;
            $post->save();
        }

        return redirect()->back()->with('success','Facebook secret Key and ID is updated!');
    }

    public function checkfclientid(Request $request){
        if($request->id != ''){
            $post = Socialmediaauth::where('id','!=',$request->id)->where('client_id','=',$request->client_id)->where('type','!=','facebook')->count();

            if ($post == 0) {
                $isAvailable = 'true';
            }else{
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }else{
            $post = Socialmediaauth::where('client_id','=',$request->client_id)->where('type','!=','facebook')->count();

            if ($post == 0) {
                $isAvailable = 'true';
            }else{
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }
    }

    public function checkfclientsecret(Request $request){
        if($request->id != ''){
            $post = Socialmediaauth::where('id','!=',$request->id)->where('type','!=','facebook')->where('client_secret','=',$request->client_secret)->count();

            if ($post == 0) {
                $isAvailable = 'true';
            }else{
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }else{
            $post = Socialmediaauth::where('client_secret','=',$request->client_secret)->where('type','!=','facebook')->count();

            if ($post == 0) {
                $isAvailable = 'true';
            }else{
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }
    }

    public function frontendwebsite(){
        $post = Frontendwebsiteconfig::first();
        return view('admin.frontendwebsite.index',compact('post'));
    }

    public function frontendwebsiteUploadLogo(Request $request){
        $post = Frontendwebsiteconfig::first();
        $basepathstatus = Basepathstatus::first();

        // Upload Arabic and English Logo

        if ($request->hasFile('english_logo')) {
            $file = $request->file('english_logo');
            $name = $file->getClientOriginalName();
            // remove sapce from name
            $filename_ren = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
            $repfilename = str_replace(" ","_",$filename_ren);
            $new_file1 = $repfilename.'.'.$fileext_ren;
            if ($basepathstatus->base_path_status == 1) {
                $file->move(base_path().'/public/user/img/logo',$new_file1);
            } else {
                $file->move(base_path().'/public_html/user/img/logo',$new_file1);
            }
            $english_logo = $new_file1;
        } else {
            $english_logo = "";
        }

        if ($request->hasFile('arabic_logo')) {
            $file = $request->file('arabic_logo');
            $name = $file->getClientOriginalName();
            // remove sapce from name
            $filename_ren = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
            $repfilename = str_replace(" ","_",$filename_ren);
            $new_file2 = $repfilename.'.'.$fileext_ren;
            if ($basepathstatus->base_path_status == 1) {
                $file->move(base_path().'/public/user/img/logo',$new_file2);
            } else {
                $file->move(base_path().'/public_html/user/img/logo',$new_file2);
            }
            $arabic_logo = $new_file2;
        } else {
            $arabic_logo = "";
        }
        
        

        if (isset($post)) {
            if ($english_logo != '') {
                $post->english_logo = $english_logo;
            }
            if ($arabic_logo != '') {
                $post->arabic_logo = $arabic_logo;
            }
        
            $post->admin_id = Auth::guard('admin')->user()->id;
            $post->save();
            return redirect()->back()->with('success','Logo Uploaded!');
        } else {
            $newPost = new Frontendwebsiteconfig();
            $newPost->english_logo = $english_logo;
            $newPost->arabic_logo = $arabic_logo;
            $newPost->admin_id = Auth::guard('admin')->user()->id;
            $newPost->save();

            return redirect()->back()->with('success','Logo Uploaded!');
        }
        


    }

    public function frontendwebsiteaboutusstore(Request $request){
        $post = Frontendwebsiteconfig::first();

        if(isset($post)){
            $post->about_us_eng = $request->about_us_eng;
            $post->about_us_ar = $request->about_us_ar;
            $post->save();

            return redirect()->back()->with('success','About us updated!');
        }else{
            $newPost = new Frontendwebsiteconfig();
            $newPost->about_us_eng = $request->about_us_eng;
            $newPost->about_us_ar = $request->about_us_ar;
            $newPost->admin_id = Auth::guard('admin')->user()->id;
            $newPost->save();

            return redirect()->back()->with('success','About us updated!');
        }
    }

    public function frontendwebsitecontactusstore(Request $request){
        $post = Frontendwebsiteconfig::first();

        if(isset($post)){
            $post->contact_us_eng = $request->contact_us_eng;
            $post->contact_us_ar = $request->contact_us_ar;
            $post->contact_us_email = $request->contact_us_email;
            $post->contact_us_phone = $request->contact_us_phone;
            $post->contact_us_location = $request->contact_us_location;
            $post->contact_us_location_ar = $request->contact_us_location_ar;

            $post->save();

            return redirect()->back()->with('success','Contact us updated!');
        }else{
            $newPost = new Frontendwebsiteconfig();
            $newPost->contact_us_eng = $request->contact_us_eng;
            $newPost->contact_us_ar = $request->contact_us_ar;
            $newPost->contact_us_email = $request->contact_us_email;
            $newPost->contact_us_phone = $request->contact_us_phone;
            $newPost->contact_us_location = $request->contact_us_location;
            $newPost->contact_us_location_ar = $request->contact_us_location_ar;
            $newPost->admin_id = Auth::guard('admin')->user()->id;
            $newPost->save();

            return redirect()->back()->with('success','Contact us updated!');
        }
    }

    public function frontendwebsitecontactusbottomstore(Request $request){
        $post = Frontendwebsiteconfig::first();
        
        if(isset($post)){
            $post->bottom_contact_us_addr = $request->bottom_contact_us_addr;
            $post->bottom_contact_us_addr_arabic = $request->bottom_contact_us_addr_arabic;
            $post->bottom_contact_us_phone = $request->bottom_contact_us_phone;
            $post->bottom_contact_us_email = $request->bottom_contact_us_email;
            $post->bottom_contact_us_fb_link = $request->bottom_contact_us_fb_link;
            $post->bottom_contact_us_twitter_link = $request->bottom_contact_us_twitter_link;
            $post->bottom_contact_us_instagram_link = $request->bottom_contact_us_instagram_link;
            $post->bottom_contact_us_linkedin_link = $request->bottom_contact_us_linkedin_link;

            $post->save();
        }else{
            $newPost = new Frontendwebsiteconfig();
            $newPost->bottom_contact_us_addr = $request->bottom_contact_us_addr;
            $newPost->bottom_contact_us_addr_arabic = $request->bottom_contact_us_addr_arabic;
            $newPost->bottom_contact_us_phone = $request->bottom_contact_us_phone;
            $newPost->bottom_contact_us_email = $request->bottom_contact_us_email;
            $newPost->bottom_contact_us_fb_link = $request->bottom_contact_us_fb_link;
            $newPost->bottom_contact_us_twitter_link = $request->bottom_contact_us_twitter_link;
            $newPost->bottom_contact_us_instagram_link = $request->bottom_contact_us_instagram_link;
            $newPost->bottom_contact_us_linkedin_link = $request->bottom_contact_us_linkedin_link;
            $newPost->admin_id = Auth::guard('admin')->user()->id;
            $newPost->save();
        }

        return redirect()->back()->with('success','Contact us Bottom updated!');
    }
}
