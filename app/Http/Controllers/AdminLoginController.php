<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminLoginController extends Controller
{
    public function forgotpassword()
    {
        return view('admin.forgot.index');
    }

    public function findmailf(Request $request)
    {


        $email = $request->email;
        $post = Admin::where('status', 1)->where('email','=',$email)->count();

        if($post > 0){
            $isAvailable = 'true';
        }else{  
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function sendPasswordResetLink(Request $request)
    {
        $token = Str::random(64);
    }
}
