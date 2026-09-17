<?php

namespace App\Http\Controllers;

use App\Mail\SendTestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestingController extends Controller
{
    public function mailtest()
    {
        return view('admin.testing.mail');
    }

    public function mailsend(Request $request)
    {
        $data = [
            'email' => $request->email,
            'msgBody' => $request->messageBody
        ];

        Mail::to($data['email'])->send(new SendTestMail($data));

        return redirect()->back();
    }
}
