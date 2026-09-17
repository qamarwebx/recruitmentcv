<?php

namespace App\Http\Controllers;

use App\Models\Adminpermission;
use App\Models\EmailSmtp; // your model for email smtp table
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailSMTPController extends Controller
{
    public function index()
    {
        $perm = Adminpermission::where('staff_id', '=', Auth::guard('admin')->user()->id)->first();
        return view('admin.email_smtp.index', compact('perm'));
    }

    public function indexJson(Request $request)
    {
        $userID = Auth::guard('admin')->user()->id;
        $permission = Adminpermission::where('staff_id', '=', $userID)->first();

        if ((Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && $permission->full_access == 1)) {
            $post = EmailSmtp::orderBy('id', 'DESC')->get();
        } elseif (isset($permission) && $permission->full_access == 0) {
            $post = EmailSmtp::where('staff_id', '=', $userID)->orderBy('id', 'DESC')->get();
        }

        $data['data'] = $post;
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'smtp_name' => 'required',
            'mail_host' => 'required',
            'mail_port' => 'required|numeric',
            'mail_username' => 'required',
            'mail_password' => 'required',
            'from_address' => 'required|email',
            'from_name' => 'required',
        ]);

        $post = new EmailSmtp();
        $post->smtp_name = $request->smtp_name;
        $post->mail_mailer = $request->mail_mailer ?? 'smtp';
        $post->mail_host = $request->mail_host;
        $post->mail_port = $request->mail_port;
        $post->mail_username = $request->mail_username;
        $post->mail_password = $request->mail_password;
        $post->mail_encryption = $request->mail_encryption ?? null;
        $post->from_address = $request->from_address;
        $post->from_name = $request->from_name;
        $post->notes = $request->notes ?? null;
        $post->staff_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success', 'SMTP details saved successfully!');
    }

    public function edit(Request $request)
    {
        $post = EmailSmtp::find($request->id);
        return response()->json($post);
    }

    public function update(Request $request)
    {
        $request->validate([
            'smtp_name' => 'required',
            'mail_host' => 'required',
            'mail_port' => 'required|numeric',
            'mail_username' => 'required',
            'mail_password' => 'required',
            'from_address' => 'required|email',
            'from_name' => 'required',
        ]);

        $post = EmailSmtp::find($request->edit_id);
        $post->smtp_name = $request->smtp_name;
        $post->mail_mailer = $request->mail_mailer ?? 'smtp';
        $post->mail_host = $request->mail_host;
        $post->mail_port = $request->mail_port;
        $post->mail_username = $request->mail_username;
        $post->mail_password = $request->mail_password;
        $post->mail_encryption = $request->mail_encryption ?? null;
        $post->from_address = $request->from_address;
        $post->from_name = $request->from_name;
        $post->notes = $request->notes ?? null;
        $post->save();

        return redirect()->back()->with('success', 'SMTP details updated successfully!');
    }

    public function delete(Request $request)
    {
        $post = EmailSmtp::find($request->delID);
        if ($post) {
            $post->delete();
            return redirect()->back()->with('success', 'SMTP deleted successfully!');
        }

        return redirect()->back()->with('error', 'Record not found!');
    }

    public function getAssignto(Request $request)
    {
        $post = EmailSmtp::find($request->id);
        return response()->json($post);
    }

    public function getAssigntoStore(Request $request)
    {
        $post = EmailSmtp::find($request->edit_id);

        if ($request->api_assign_to != '') {
            $post->smtp_assign_to = implode(",", $request->api_assign_to);
        } else {
            $post->smtp_assign_to = "";
        }

        $post->save();

        return redirect()->back()->with('success', 'SMTP assigned successfully!');
    }

    public function checkassignto(Request $request)
    {
        $name = trim($request->name);
        $id = $request->id;

        $post = EmailSmtp::whereRaw("FIND_IN_SET(?, smtp_assign_to)", [$name])
            ->where('id', '!=', $id)
            ->count();

        if ($post == 0) {
            $isAvailable = 'true';
        } else {
            $isAvailable = 'false';
        }

        echo json_encode([
            'valid' => $isAvailable,
        ]);
    }

    public function getStatus(Request $request)
    {
        $post = EmailSmtp::find($request->id);
        return response()->json($post);
    }

    public function statusUpdt(Request $request)
    {
        $post = EmailSmtp::find($request->statusID);
        $post->status = $request->status;
        $post->save();

        return redirect()->back()->with('success', 'SMTP status updated successfully!');
    }


}
