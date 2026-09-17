<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\EmailSmtp;
use App\Models\EmailTemplate;
use App\Models\Basepathstatus;

class EmailTemplateController extends Controller
{
    // ✅ Template List Page
    public function templateList(Request $request)
    {
        $permission = Adminpermission::where('staff_id', Auth::guard('admin')->id())->first();
        $careoff = Admin::where('status', 1)->orderBy('name')->get();
        $smtpLists = EmailSmtp::where('status', 1)->orderBy('smtp_name')->get();

        return view('admin.email_template.index', [
            'perm' => $permission,
            'careoffs' => $careoff,
            'smtpLists' => $smtpLists
        ]);
    }

    // ✅ Datatable JSON List
    public function templateListJson(Request $request)
    {
        $permission = Adminpermission::where('staff_id', Auth::guard('admin')->id())->first();

        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $templates = DB::table('email_templates as et')
                ->leftJoin('admins as a1', 'a1.id', '=', 'et.created_by')
                ->leftJoin('admins as a2', 'a2.id', '=', 'et.careoff_id')
                ->leftJoin('email_smtps as smtp', 'smtp.id', '=', 'et.smtp_id')
                ->select('et.*', 'a1.name as created_by_name', 'smtp.smtp_name as smtp_name', 'a2.name as careoff_name')
                ->orderBy('et.id', 'DESC')
                ->get();
        } else {
            $templates = DB::table('email_templates as et')
                ->leftJoin('admins as a1', 'a1.id', '=', 'et.created_by')
                ->leftJoin('admins as a2', 'a2.id', '=', 'et.careoff_id')
                ->leftJoin('email_smtps as smtp', 'smtp.id', '=', 'et.smtp_id')
                ->select('et.*', 'a1.name as created_by_name', 'smtp.smtp_name as smtp_name', 'a2.name as careoff_name')
                ->where('et.created_by', Auth::guard('admin')->id())
                ->orderBy('et.id', 'DESC')
                ->get();
        }

        $data['data'] = $templates;
        return response()->json($data);
    }

    // ✅ Store New Email Template
    public function templateStore(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'template_name' => 'required|string|max:255',
                'subject' => 'required|string|max:255',
                'email_body' => 'required|string',
                'smtp_id' => 'nullable|integer',
                'template_for' => 'nullable|string|max:120',
                'template_used_for' => 'nullable|string|max:120',
                'photo' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,docx|max:5120',
            ]);

            $post = new EmailTemplate();
            $basepathstatus = Basepathstatus::first();
            $uploadedFile = '';

            // Handle attachment upload (if provided)
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $cleanName = str_replace(' ', '_', $filename) . '.' . $extension;

                $uploadPath = $basepathstatus && $basepathstatus->base_path_status == 1
                    ? base_path('public/admin/assets/images/email-template')
                    : base_path('public_html/admin/assets/images/email-template');

                $file->move($uploadPath, $cleanName);
                $uploadedFile = $cleanName;
            }

            // --------------------------------------------
            // Update field_variable & assign_variable
            // --------------------------------------------

            if(isset($request->field_variable) && isset($request->assign_variable)){
                $post->field_variable = implode(",",$request->field_variable);
                $post->assign_variable = implode(",",$request->assign_variable);
            }

            // Assign core fields
            $post->smtp_id = $request->smtp_id;
            $post->template_name = $request->template_name;
            $post->subject = $request->subject;
            $post->template_for = $request->template_for;
            $post->template_used_for = $request->template_used_for;
            $post->email_body = $request->email_body;
            $post->email_body_bg = $request->email_body_bg;
            $post->language = $request->language ?? 'en';
            $post->attachment = $uploadedFile;
            $post->public = $request->boolean('public');
            $post->status = true;
            $post->created_by = Auth::guard('admin')->id();
            $post->save();

            return redirect()->back()->with('success', 'Email Template Created Successfully!');

        } catch (\Exception $e) {
            \Log::error('Email Template Store Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Something went wrong while creating the template.')->withInput();
        }
    }

    // ✅ Edit Template (AJAX)
    public function templateEdit(Request $request)
    {
        $template = EmailTemplate::find($request->id);
    
        if ($template && $template->attachment) {
            // Generate full file path based on your structure
            $template->attachment_url = asset('admin/assets/images/email-template/' . $template->attachment);
        } else {
            $template->attachment_url = null;
        }
    
        return response()->json($template);
    }
    
    // ✅ Update Template
    public function templateUpdate(Request $request)
    {
        try {
            $request->validate([
                'edit_id' => 'required|integer|exists:email_templates,id',
                'template_name' => 'required|string|max:255',
                'subject' => 'required|string|max:255',
                'email_body' => 'required|string',
                'smtp_id' => 'nullable|integer',
                'template_for' => 'nullable|string|max:120',
                'template_used_for' => 'nullable|string|max:120',
                'photo' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,docx|max:5120',
            ]);
    
            $post = EmailTemplate::findOrFail($request->edit_id);
            $basepathstatus = Basepathstatus::first();
            $uploadedFile = $post->attachment;
    
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $cleanName = str_replace(' ', '_', $filename) . '.' . $extension;
    
                $uploadPath = $basepathstatus && $basepathstatus->base_path_status == 1
                    ? base_path('public/admin/assets/images/email-template')
                    : base_path('public_html/admin/assets/images/email-template');
    
                $file->move($uploadPath, $cleanName);
                $uploadedFile = $cleanName;
            }
    
            // --------------------------------------------
            // Update field_variable & assign_variable
            // --------------------------------------------
            if (isset($request->field_variable) && isset($request->assign_variable)) {
                $post->field_variable = implode(",", $request->field_variable);
                $post->assign_variable = implode(",", $request->assign_variable);
            }
    
            // -------
            // UPDATE
            // -------
            $post->update([
                'smtp_id' => $request->smtp_id,
                'template_name' => $request->template_name,
                'subject' => $request->subject,
                'template_for' => $request->template_for,
                'template_used_for' => $request->template_used_for,
                'email_body' => $request->email_body,
                'email_body_bg' => $request->email_body_bg,
                'language' => $request->language ?? 'en',
                'attachment' => $uploadedFile,
                'public' => $request->boolean('public'),
            ]);
    
            return redirect()->back()->with('success', 'Email Template Updated Successfully!');
    
        } catch (\Exception $e) {
            \Log::error('Email Template Update Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()
                ->with('error', 'Something went wrong while updating the template.')
                ->withInput();
        }
    }
    

    // ✅ Delete Template
    public function templateDelete(Request $request)
    {
        try {
            $request->validate(['delete_id' => 'required|integer|exists:email_templates,id']);
            $template = EmailTemplate::findOrFail($request->delete_id);
            $template->delete();
            return back()->with('success', 'Email Template Deleted Successfully!');
        } catch (\Exception $e) {
            \Log::error('Email Template Delete Error: ' . $e->getMessage());
            return back()->with('error', 'Error deleting template.');
        }
    }

    public function deleteAttachment(Request $request)
    {
        $template = EmailTemplate::find($request->id);
        if (!$template || !$template->attachment) {
            return response()->json(['success' => false, 'message' => 'Attachment not found.']);
        }

        $basepathstatus = Basepathstatus::first();

        $filePath = ($basepathstatus && $basepathstatus->base_path_status == 1)
            ? public_path('admin/assets/images/email-template/' . $template->attachment)
            : base_path('public_html/admin/assets/images/email-template/' . $template->attachment);

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $template->attachment = null;
        $template->save();

        return response()->json(['success' => true]);
    }


    // ✅ Activate / Deactivate
    public function activeStatus(Request $request)
    {
        $post = EmailTemplate::find($request->tempactID);
        $post->status = true;
        $post->save();
        return back()->with('success', 'Template Activated Successfully!');
    }

    public function deactiveStatus(Request $request)
    {
        $post = EmailTemplate::find($request->tempdeactID);
        $post->status = false;
        $post->save();
        return back()->with('success', 'Template Deactivated Successfully!');
    }

    // ✅ Fetch for Modal (Status)
    public function templateStatus(Request $request)
    {
        $post = EmailTemplate::find($request->id);
        return response()->json($post);
    }

    // ✅ Update Status
    public function templateStatusUpdate(Request $request)
    {
        $post = EmailTemplate::find($request->tempchstID);
        $post->status = $request->status;
        $post->save();

        return back()->with('success', 'Status Updated Successfully!');
    }

    // ✅ Fetch for Modal (Public)
    public function templatePublic(Request $request)
    {
        $post = EmailTemplate::find($request->id);
        return response()->json($post);
    }

    // ✅ Update Public Status
    public function templatePublicUpdate(Request $request)
    {
        $post = EmailTemplate::find($request->temppubID);
        $post->public = $request->publish_st;
        $post->save();

        return back()->with('success', 'Publish Status Updated!');
    }
}
