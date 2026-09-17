<?php

namespace App\Http\Controllers;

use App\AdminModel\AllContact;
use App\Jobs\SendSmsJob;
use App\Jobs\TestEmailJob;
use App\Mail\TestMail as MailTestMail;
use App\TestMail;
use App\User;
use Illuminate\Http\Request;

class TestEmailJobController extends Controller
{
    public function createMail()
    {
        return view('email.create');
    }

    public function sendMail(Request $request)
    {
        $post = new TestMail();
        $post->name = $request->name;
        $post->desc = $request->description;
        $post->save();

        $users = User::where('user_id','!=','200')->get();
        foreach ($users as $user) {
            dispatch(new TestEmailJob($user,$post));
        }

        return redirect()->back();
    }

    public function createSMS()
    {
        return view('email.sms');
    }

    public function sendSMS(Request $request)
    {
        
        $post = new TestMail();
        $post->name = $request->mobile;
        $post->desc = $request->msgBody;
        $post->save();

        $conts = AllContact::all();

        foreach ($conts as $cont) {
            dispatch(new SendSmsJob($cont,$post));
        }
        
        
        return redirect()->back();
    }
}

