<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Adminpermission;
use App\Models\Autoemailnotification;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmailAutomationController extends Controller
{
    public function index(){
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        return view('admin.automation_email_notification.index',compact('permission'));
    }

    public function indexJson(Request $request)  
    {
        $post = DB::table('autoemailnotifications')
            ->select('template_for')
            ->groupBy('template_for')
            ->orderBy('template_for','ASC')
            ->get();
    
        return response()->json(['data' => $post]);
    }

    public function store(Request $request) {

        $gettemp = EmailTemplate::find($request->emailtemp_id);

        $post = new Autoemailnotification();
        $post->template_for = $request->template_for;
        $post->emailtemp_id = $request->emailtemp_id ?? null;
        $post->template_name = $request->email_template_name ?? null;
        $post->trigger_template_type = $request->trigger_template_type ?? null;
        $post->trigger_template_time = $request->trigger_template_time ?? null;
        $post->trigger_template_time_type = $request->trigger_template_time_type ?? null;

        $post->template_table_name = $request->template_table_name;
        $post->field_not_completed = $request->field_not_completed 
                                ? json_encode($request->field_not_completed) 
                                : null;

        $post->staff_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Email Notification added!');
    }

    public function update(Request $request) {
  
        $post = Autoemailnotification::find($request->edit_id);

        // Get template details
        $gettemp = EmailTemplate::find($request->metatemp_id);
        // Hidden value
        $post->template_for = $request->template_for;
        $post->template_table_name = $request->template_table_name;

        // form value
        $post->emailtemp_id = $request->emailtemp_id;
        $post->template_name = $request->email_template_name;
        $post->trigger_template_type = $request->trigger_template_type ?? null;
        $post->trigger_template_time = $request->trigger_template_time ?? null;
        $post->trigger_template_time_type = $request->trigger_template_time_type ?? null;
        $post->field_not_completed = $request->field_not_completed ? json_encode($request->field_not_completed) : null;
         
        $post->save();
  
        return redirect()->back()->with('success','Meta Notification Updated!');
  
      }

    public function templateFor($template_for)
    {
        $templates = EmailTemplate::where('template_for', $template_for)->get();

        // leads_candidate = leads
        $table = array(
            'contactpluses' => 'contactpluses', 
            'allcontacts' => 'allcontacts', 
            'associates' => 'associates', 
            'partners' => 'partners', 
            'employers' => 'employers', 
            'leads' => 'leads'
        );
     

        if (!array_key_exists($template_for, $table)) {
            return redirect()->back()->with('error', 'Invalid template category');
        }

        $tableName = $table[$template_for];
        $columns = Schema::getColumnListing($tableName);

        
        return view('admin.automation_email_notification.template_for.index', [
            'template_for' => $template_for,
            'templates' => $templates,
            'tableName' => $tableName,
            'columns' => $columns
        ]);
    }

    public function templateForJson(Request $request)
    {
        $template_for = $request->template_for;

        if (!$template_for) {
            return response()->json(['data' => []]);
        }

        // Fetch templates for specific category
        $post = DB::table('autoemailnotifications as a')
                ->leftjoin('email_templates', 'a.emailtemp_id', '=', 'email_templates.id')
                ->where('a.template_for', $template_for)
                ->orderBy('a.id', 'DESC')
                ->select('a.id as autoemailnotifications_id','a.status as status','a.template_name as email_template_name', 'email_templates.template_name')
                ->get();


        return response()->json(['data' => $post]);
    }

    public function templateForEdit($id)
    {

        $post = Autoemailnotification::where('id','=',$id)->first();
        return response()->json($post);

    }

    public function getListTempID(Request $request)
    {

        $template = EmailTemplate::where('id', $request->id)->where('status', 1)->first();
    
        if ($template && $template->attachment) {
            // Generate full file path based on your structure
            $template->attachment_url = asset('admin/assets/images/email-template/' . $template->attachment);
        } else {
            $template->attachment_url = null;
        }
    
        return response()->json($template);
    }

    public function getStatus(Request $request){
        $post = Autoemailnotification::find($request->id);
        return response()->json($post);
    }


    public function statusUpdt(Request $request){
        $post = Autoemailnotification::find($request->tempchstID);

        $post->status = $request->status;
        $post->save();

        return redirect()->back()->with('success','Email Notification status updated!');
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
    
        // Delete record
        $delete = DB::table('autoemailnotifications')->where('id', $id)->delete();
    
        if ($delete) {
            return redirect()->back()->with('success','Template deleted successfully');
        } else {
            return redirect()->back()->with('error','Failed to delete template');
        }
    }
    


    


}
