<?php

namespace App\Http\Middleware;

use App\AdminModel\Ipaddress;
use App\AdminModel\Userwhatsappapi;
use App\Ipuserdetail;
use App\Mail\Senduserdetails;
use App\Mail\SendUserDetailsAdmin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BlockIpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */


    // public $blockIP = ['127.0.0.2'];

    public function handle(Request $request, Closure $next)
    {   

        $posts = Ipaddress::where('status','=',1)->get();

        $blockIP = [];
        foreach ($posts as $post) {
            $blockIP[] = $post->ip_address;
        }

        // if (in_array($request->ip(),$blockIP)) {
        //     return response()->json([
        //         'message' => "You dont have permission to access this website"
        //     ],401);
    
        // }

        if(Auth::user()->user_type != 1){
        
            if (!in_array($request->ip(),$blockIP)) {
                // Update in IPAddress Details
                $post = new Ipuserdetail();
                $post->user_id = Auth::user()->user_id;
                $post->uname = Auth::user()->username;
                $post->ip_address = $request->ip();
                $post->save();
    
                // Send details on Whatsapp
                $getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=','1')->first();
                $whmsg = "Dear Khursheed Khan \nSomebody trying to login your crm from outside, details are mentioned below.\nUsername: ".Auth::user()->username."\nIP Address: ".$request->ip()."\nKindly check";
    
                $url = $getAPI->text_message_url."?number=919004446665&type=text&message=".urlencode($whmsg)."&instance_id=".$getAPI->instance_key."&access_token=".$getAPI->api_key;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result = curl_exec($ch);
                echo $result;
                curl_close($ch);
    
                // Send details on mail
                $data = [
                    'username' => Auth::user()->username,
                    'ip_address' => $request->ip()
                ];
                


                Mail::to('khan786info@gmail.com')->send(new Senduserdetails($data));
    
                Auth::logout();
                return redirect('/')->withErrors(['blockIP' => 'You dont have permission!']);
                // return response()->json([
                //     'message' => "You dont have permission to access this website"
                // ],401);
            }

        }



        return $next($request);
    }
}
