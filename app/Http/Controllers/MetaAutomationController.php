<?php

namespace App\Http\Controllers;


use App\Models\Adminpermission;
use App\Models\Autometanotification;
use App\Models\Metawhatsapptemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MetaAutomationController extends Controller
{
    public function index(){
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        return view('admin.automation_meta_notification.index',compact('permission'));
    }

    public function indexJson(Request $request)  
    {
        $post = DB::table('autometanotifications')
            ->select('template_for')
            ->groupBy('template_for')
            ->orderBy('template_for','ASC')
            ->get();
    
        return response()->json(['data' => $post]);
    }


    public function templateFor($template_for)
    {
        $perm = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        $templates = Metawhatsapptemplate::where('template_for', $template_for)->get();
        // leads_candidate = leads
        $table = array(
            'contactpluses' => 'contactpluses', 
            'allcontacts' => 'allcontacts', 
            'associates' => 'associates', 
            'partners' => 'partners', 
            'employers' => 'employers', 
            'leads' => 'leads', 
            'todos' => 'todos',
            'qamarhire' => 'bookings',
            // recruitmentcv.com orders: same bookings data as qamarhire.com.
            'recruitmentcv' => 'bookings',
        );
     

        if (!array_key_exists($template_for, $table)) {
            return redirect()->back()->with('error', 'Invalid template category');
        }

        $tableName = $table[$template_for];
        $columns = Schema::getColumnListing($tableName);

        
        return view('admin.automation_meta_notification.template_for.index', [
            'template_for' => $template_for,
            'templates' => $templates,
            'tableName' => $tableName,
            'columns' => $columns,
            'perm' => $perm
        ]);
    }

    public function templateForJson(Request $request)
    {
        $template_for = $request->template_for;

        if (!$template_for) {
            return response()->json(['data' => []]);
        }

        // Fetch templates for specific category
        $post = DB::table('autometanotifications')
            ->where('template_for', $template_for)
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json(['data' => $post]);
    }
    
    public function edit(Request $request) {
        $post = Autometanotification::find($request->id);
        return response()->json($post);
    }

    public function getStatus(Request $request){
        $post = Autometanotification::find($request->id);
        return response()->json($post);
    }

    public function store(Request $request)
    {
        $request->validate([
            'metatemp_id' => 'required',
            'meta_template_name' => 'required',
            'trigger_template_type' => 'required',
            'action_type' => 'required|in:immediate,wait',
        ]);
    
        // If wait selected → validate delay
        if ($request->action_type === 'wait') {
            $request->validate([
                'trigger_template_time' => 'required|numeric',
                'trigger_template_time_type' => 'required'
            ]);
        }
    
        $gettemp = Metawhatsapptemplate::find($request->metatemp_id);
    
        $post = new Autometanotification();
    
        $post->template_for = $request->template_for;
        $post->metatemp_id = $request->metatemp_id;
        $post->template_name = $request->meta_template_name;
        $post->meta_template_name = $gettemp->template_name ?? null;
        $post->meta_message_body = $request->msg_whatsapp ?? null;
        $post->trigger_template_type = $request->trigger_template_type;
        $post->template_send_to = $request->template_send_to;
        $post->action_type = $request->action_type;
        $post->template_table_name = $request->template_table_name;
    
        // 🔥 Action Based Logic
        if ($request->action_type === 'wait') {
    
            $post->trigger_template_time = $request->trigger_template_time;
            $post->trigger_template_time_type = $request->trigger_template_time_type;
    
            $post->field_not_completed = $request->field_not_completed
                ? json_encode($request->field_not_completed)
                : null;
    
        } else {
    
            // Immediate → clear delay fields
            $post->trigger_template_time = null;
            $post->trigger_template_time_type = null;
            $post->field_not_completed = null;
        }
    
        $post->staff_id = Auth::guard('admin')->user()->id;
        $post->save();
    
        return redirect()->back()->with('success','Meta Notification added!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'edit_id' => 'required',
            'metatemp_id' => 'required',
            'meta_template_name' => 'required',
            'trigger_template_type' => 'required',
            'action_type' => 'required|in:immediate,wait',
        ]);

        if ($request->action_type === 'wait') {
            $request->validate([
                'trigger_template_time' => 'required|numeric',
                'trigger_template_time_type' => 'required'
            ]);
        }

        $post = Autometanotification::findOrFail($request->edit_id);
        $gettemp = Metawhatsapptemplate::find($request->metatemp_id);

        $post->template_for = $request->template_for;
        $post->metatemp_id = $request->metatemp_id;
        $post->template_name = $request->meta_template_name;
        $post->meta_template_name = $gettemp->template_name ?? null;
        $post->meta_message_body = $request->msg_whatsapp ?? null;
        $post->trigger_template_type = $request->trigger_template_type;
        $post->template_send_to = $request->template_send_to;
        $post->action_type = $request->action_type;
        $post->template_table_name = $request->template_table_name;

        // 🔥 Action Based Logic
        if ($request->action_type === 'wait') {

            $post->trigger_template_time = $request->trigger_template_time;
            $post->trigger_template_time_type = $request->trigger_template_time_type;

            $post->field_not_completed = $request->field_not_completed
                ? json_encode($request->field_not_completed)
                : null;

        } else {

            $post->trigger_template_time = null;
            $post->trigger_template_time_type = null;
            $post->field_not_completed = null;
        }

        $post->save();

        return redirect()->back()->with('success','Meta Notification Updated!');
    }

    public function statusUpdt(Request $request){
        $post = Autometanotification::find($request->tempchstID);

        $post->status = $request->status;
        $post->save();


        return redirect()->back()->with('success','Meta Notification status updated!');
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
    
        // Delete record
        $delete = DB::table('autometanotifications')->where('id', $id)->delete();
    
        if ($delete) {
            return redirect()->back()->with('success','Template deleted successfully');
        } else {
            return redirect()->back()->with('error','Failed to delete template');
        }
    }
    
}


