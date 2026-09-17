<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Adminpermission;
use App\Models\SmsApi;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SmsTemplate;
use App\Models\Basepathstatus;


class SMSTemplateController extends Controller
{

    public function templateList(Request $request){

        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        $careoff = Admin::where('status', 1)->orderBy('name')->get();

        $metapaiLists = SmsApi::orderBy('api_name')->where('status',1)->get();

        return view('admin.sms_template.index',['perm' => $permission,'careoffs' => $careoff,'metaAPILists' => $metapaiLists]);
    }

    public function templateListJson(Request $request){

        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $post = DB::table('sms_templates as smstemplate')
            ->leftjoin('admins as admin','admin.id','=','smstemplate.admin_id')
            ->leftJoin('admins as admin2','admin2.id','=','smstemplate.careoff_id')
            ->leftJoin('sms_apis as smsapi','smsapi.id','=','smstemplate.sms_api_id')
            ->select('smstemplate.*','admin.name as uname','smsapi.api_name as apiname','admin2.name as uname2')
            ->orderBy('smstemplate.id','DESC')
            ->get();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){

           
                $post = DB::table('sms_templates as smstemplate')
                ->leftjoin('admins as admin','admin.id','=','smstemplate.admin_id')
                ->leftJoin('admins as admin2','admin2.id','=','smstemplate.careoff_id')
                ->leftJoin('sms_apis as smsapi','smsapi.id','=','smstemplate.sms_api_id')
                ->select('smstemplate.*','admin.name as uname','smsapi.api_name as apiname','admin2.name as uname2')
                ->where('smstemplate.admin_id','=',Auth::guard('admin')->user()->id)
                ->orderBy('smstemplate.id','DESC')
                ->get();
        

        }

        $data['data'] = $post;

        return response()->json($data);
    }

    public function templateStore(Request $request){
        try {
            // ✅ Validate input
            $request->validate([
                'template_name'      => 'required|string|max:255',
                'template_for'       => 'required|string',
                'template_used_for'  => 'nullable|string',
                'sms_api_id'         => 'nullable|integer',
                'template_type'      => 'nullable|string|max:120',
                'sms_message'        => 'nullable|string',
                'msg_sms_ar'         => 'nullable|string',
                'sms_url_type'       => 'nullable|string|max:120',
                'static_url'         => 'nullable|string|max:160',
                'document_name'      => 'nullable|string|max:150',
                'careoff_id'         => 'nullable|integer',
                'photo'              => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,docx|max:5120',
            ]);

            $post = new SmsTemplate();
            $basepathstatus = Basepathstatus::first();
            $temp_file = '';

            // ✅ Handle File Upload
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $cleanName = str_replace(' ', '_', $filename) . '.' . $extension;

                $uploadPath = $basepathstatus && $basepathstatus->base_path_status == 1
                    ? base_path('public/admin/assets/images/template')
                    : base_path('public_html/admin/assets/images/template');

                $file->move($uploadPath, $cleanName);
                $temp_file = $cleanName;
            }

            // ✅ Direct field assignments (matching table)
            $post->sms_api_id        = $request->sms_api_id;
            $post->template_name     = $request->template_name;
            $post->template_type     = $request->template_type;
            $post->template_used_for = $request->template_used_for;
            $post->field_variable    = $request->field_variable;
            $post->assign_variable   = $request->assign_variable;
            $post->sms_file          = $temp_file;
            $post->sms_message       = $request->sms_message;
            $post->msg_sms_ar        = $request->msg_sms_ar;
            $post->public            = $request->boolean('public');
            $post->admin_id          = Auth::guard('admin')->id();
            $post->status            = true;
            $post->template_for      = $request->template_for;
            $post->sms_url_type      = $request->sms_url_type;
            $post->static_url        = $request->static_url;
            $post->document_name     = $request->document_name;
            $post->careoff_id        = $request->careoff_id;

            // ✅ Handle dynamic field & assign variable arrays
            $fieldKey  = 'field_var_' . $request->template_for;
            $assignKey = 'assign_var_' . $request->template_for;

            if ($request->has($fieldKey)) {
                $post->sms_field_var = implode(',', (array) $request->get($fieldKey));
            }

            if ($request->has($assignKey)) {
                $post->sms_assign_ar = implode(',', (array) $request->get($assignKey));
            }

            // ✅ Save record
            $post->save();

            return redirect()->back()->with('success', 'Template Created Successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // ⚠️ Validation error — redirect back with messages
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();

        } catch (\Exception $e) {
            // ⚠️ Any other exception — log and show user-friendly message
            \Log::error('Template Store Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()
                ->with('error', 'Something went wrong while creating the template. Please try again.')
                ->withInput();
        }
    }

    public function templateEdit(Request $request){
        $post = SmsTemplate::find($request->id);
        return response()->json($post);
    }

    public function templateUpdate(Request $request){
        try {

            // ✅ Validate input
            $request->validate([
                'edit_id'           => 'required|integer|exists:sms_templates,id',
                'template_name'     => 'required|string|max:255',
                'template_for'      => 'required|string',
                'template_used_for' => 'nullable|string',
                'sms_api_id'        => 'nullable|integer',
                'template_type'     => 'nullable|string|max:120',
                'sms_message'       => 'nullable|string',
                'msg_sms_ar'        => 'nullable|string',
                'sms_url_type'      => 'nullable|string|max:120',
                'static_url'        => 'nullable|string|max:160',
                'document_name'     => 'nullable|string|max:150',
                'careoff_id'        => 'nullable|integer',
                'photo'             => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,docx|max:5120',
            ]);

            $post = SmsTemplate::findOrFail($request->edit_id);
            $basepathstatus = Basepathstatus::first();
            $temp_file = $post->sms_file; // Keep old file by default

            // ✅ Handle new file upload
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $cleanName = str_replace(' ', '_', $filename) . '.' . $extension;

                $uploadPath = $basepathstatus && $basepathstatus->base_path_status == 1
                    ? base_path('public/admin/assets/images/template')
                    : base_path('public_html/admin/assets/images/template');

                $file->move($uploadPath, $cleanName);
                $temp_file = $cleanName;
            }

            // ✅ Update core fields
            $post->sms_api_id        = $request->sms_api_id;
            $post->template_name     = $request->template_name;
            $post->template_type     = $request->template_type;
            $post->template_used_for = $request->template_used_for;
            $post->sms_message       = $request->sms_message;
            $post->msg_sms_ar        = $request->msg_sms_ar;
            $post->public            = $request->boolean('public');
            $post->template_for      = $request->template_for;
            $post->sms_url_type      = $request->sms_url_type;
            $post->static_url        = $request->static_url;
            $post->document_name     = $request->document_name;
            $post->careoff_id        = $request->careoff_id;
            $post->sms_file          = $temp_file;

            // ✅ Handle dynamic variables (same logic as store)
            $fieldKey  = 'field_var_' . $request->template_for;
            $assignKey = 'assign_var_' . $request->template_for;

            if ($request->has($fieldKey)) {
                $post->sms_field_var = implode(',', (array) $request->get($fieldKey));
            }

            if ($request->has($assignKey)) {
                $post->sms_assign_ar = implode(',', (array) $request->get($assignKey));
            }

            // ✅ Save changes
            $post->save();

            return redirect()->back()->with('success', 'Template Updated Successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // ⚠️ Validation errors
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();

        } catch (\Exception $e) {
            // ⚠️ Log and return friendly error
            \Log::error('Template Update Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all(),
            ]);

            return redirect()->back()
                ->with('error', 'Something went wrong while updating the template. Please try again.')
                ->withInput();
        }
    }

    public function templateDelete(Request $request){
        try {
            // Validate input
            $request->validate([
                'delete_id' => 'required|integer|exists:sms_templates,id',
            ]);
    
            // Find and delete
            $template = SmsTemplate::findOrFail($request->delete_id);
            $template->delete();
    
            return redirect()->back()->with('success', 'Template Deleted Successfully!');
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('error', 'Invalid or missing Template ID.');
    
        } catch (\Exception $e) {
            \Log::error('Template Delete Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all(),
            ]);
    
            return redirect()->back()->with('error', 'Something went wrong while deleting the template.');
        }
    }

    public function activeStatus(Request $request){
        $post = SmsTemplate::find($request->tempactID);

        $post->status = true;
        $post->save();

        return redirect()->back()->with('success','Status activated!');
    }

    public function deactiveStatus(Request $request){
        $post = SmsTemplate::find($request->tempdeactID);

        $post->status = false;
        $post->save();

        return redirect()->back()->with('success','Status deactivated!');
    }

    public function templateStatus(Request $request){
        $post = SmsTemplate::find($request->id);

        return response()->json($post);
    }

    public function templateStatusUpdate(Request $request){
        $post = SmsTemplate::find($request->tempchstID);
        $post->status = $request->status;
        $post->save();

        return redirect()->back()->with('success','Status Updated!');
    }

    public function templatePublic(Request $request){
        $post = SmsTemplate::find($request->id);

        return response()->json($post);
    }

    public function templatePublicUpdate(Request $request){
        $post = SmsTemplate::find($request->temppubID);
        $post->public = $request->publish_st;
        $post->save();

        return redirect()->back()->with('success','Publish Updated!');
    }

}
