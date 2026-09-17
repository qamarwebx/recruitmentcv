<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class UserLoginController extends Controller
{
    
    public function userStr(Request $request)
    {
        // 🛑 Honeypot (bot detection)
        if ($request->filled('company_name')) {
            Log::warning('Bot detected (honeypot)', [
                'ip' => $request->ip(),
                'data' => $request->all(),
            ]);
            abort(403);
        }
    
        // 🚫 Rate limit (extra protection)
        $key = 'register-'.$request->ip();
    
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['error' => 'Too many attempts. Try again later.']);
        }
    
        RateLimiter::hit($key, 60);
    
        // ✅ Validation
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:6|max:100',
            'current_page' => 'nullable|url',
        ]);
    
        try {
    
            // 👤 Create user
            $user = User::create([
                'name' => trim($validated['name']),
                'email' => strtolower($validated['email']),
                'password' => Hash::make($validated['password']),
            ]);
    
            // 📩 Email verification event
            event(new Registered($user));
    
            // 🔐 Login user
            Auth::login($user);
    
            // 🧾 Log success
            Log::info('User registered', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
            ]);
    
            // 🔁 Safe redirect
            return redirect()->to($validated['current_page'] ?? '/');
    
        } catch (\Exception $e) {
    
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);
    
            return back()->withErrors(['error' => 'Something went wrong. Try again.']);
        }
    }

    public function checkloginemail(Request $request)
    {
        $post = User::where('email','=',$request->email)->count();

        if ($post != 0) {
            echo "true";
        }else{
            echo "false";
        }
    }

    public function checkloginpass(Request $request)
    {
        $post = User::where('email','=',$request->email)->first();

        if (isset($post)) {
            if (!Hash::check($request->password,$post->password)) {
                echo "false";
            } else {
                echo "true";
            }
            
        } else {
            echo "false";
        }
        

    }

    public function checksignupemail(Request $request)
    {
        $post = User::where('email','=',$request->email)->count();

        if ($post == 0) {
            echo "true";
        } else {
            echo "false";
        }
        
    }

    public function findemailf(Request $request)
    {
        $post = User::where('email','=',$request->email)->count();
        if ($post > 0) {
            echo "true";
        } else {
            echo "false";
        }
        
    }

    // public function sendPasswordResetLink(Request $request)
    // {
    //     $token = Str::random(64);
    //     // dd($token);
    //     $del_email = DB::table('password_resets')->where('email','=',$request->email)->delete();
    //     try {

    //         DB::table('password_resets')->insert([
    //             'email' => $request->email, 
    //             'token' => $token, 
    //             'created_at' => Carbon::now()
    //         ]);

    //         Mail::send('email.forgetPassword',['token' => $token],function($message) use($request){
    //             $message->to($request->email);
    //             $message->subject('Reset Password');
    //         });

    //         $retmessage = "We have emailed your password reset link!";
    //         return back()->with('message',$retmessage);

    //     } catch (\Exception $e) {
    //         $retmessage = "Password reset link not send, please try again";
    //         return back()->with('errorMsg',$retmessage);
    //     }

    //     // Mail::send('email.forgetPassword',['token' => $token],function($message) use($request){
    //     //             $message->to($request->email);
    //     //             $message->subject('Reset Password');
    //     //         });

        
    // }
    
    public function sendPasswordResetLink(Request $request)
    {
        $token = Str::random(64);

        // ✅ CHECK IF EMAIL EXISTS
        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user) {
            return back()->with('errorMsg', 'Email not registered');
        }

        // delete old token
        DB::table('password_resets')->where('email', $request->email)->delete();

        try {

            DB::table('password_resets')->insert([
                'email' => $request->email, 
                'token' => $token, 
                'created_at' => Carbon::now()
            ]);

            Mail::send('email.forgetPassword', ['token' => $token], function($message) use($request){
                $message->to($request->email);
                $message->subject('Reset Password');
            });

            return back()->with('message', 'We have emailed your password reset link!');

        } catch (\Exception $e) {
            return back()->with('errorMsg', 'Password reset link not sent, please try again');
        }
    }

    public function showResetPasswordForm($token)
    {
        $post = DB::table('password_resets')->where('token','=',$token)->first();

        // $data = DB::table('password_resets')->where('created_at','<',date('Y-m-d H:i:s'))->delete();


        // dd($data);

        return view('user.forgetpassword',['post' => $post]);
    }


    public function storeResetPassword(Request $request,$email)
    {
        // update password
        $user = User::where('email','=',$email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete token number delete
        $post = DB::table('password_resets')->where('token','=',$request->token)->delete();


        return redirect()->route('welcome')->with('message','Your password has been change, please login again');
    }

}
