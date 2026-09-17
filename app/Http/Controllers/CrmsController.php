<?php

namespace App\Http\Controllers;

use App\AdminModel\AccessPermissionModule2;
use App\User;
use App\AdminModel\Staff;
use App\AdminModel\Lead;
use App\AdminModel\City;
use App\AdminModel\Country;
use App\AdminModel\Note;
use App\AdminModel\Task;
use App\AdminModel\Appoint;
use App\AdminModel\Files;
use App\AdminModel\Party;
use App\AdminModel\PartyServicePrice;
use App\AdminModel\Product;
use App\AdminModel\ProductCategory;
use App\AdminModel\MofaPaymentCategory;
use App\AdminModel\Type;
use App\AdminModel\Branch;
use App\AdminModel\LeadProfession;
use App\AdminModel\Profession;
use App\AdminModel\Company;
use App\AdminModel\Sector;
use App\AdminModel\CompanyContact;
use App\AdminModel\ActivitiesContactCrm;
use App\AdminModel\ContactCsvData;
use App\AdminModel\ConversationContact;
use App\AdminModel\TimelineContactCrm;
use App\AdminModel\QamrgulfjobsDownload;
use App\AdminModel\Industry;
use App\AdminModel\MofaPrice;
use App\AdminModel\Label;
use App\AdminModel\LeadStage;
use App\AdminModel\LifeCycleStage;
use App\AdminModel\CandidatePlaceIssue;
use App\AdminModel\ContactType;
use App\AdminModel\Candidate;
use App\AdminModel\Todo;
use App\AdminModel\JobSeeker;
use App\AdminModel\NoteContact;
use App\AdminModel\AppointContact;
use App\AdminModel\TaskContact;
use App\AdminModel\Recruitment;
use App\AdminModel\EmployeeServiceType;
use App\AdminModel\AllContactAct;
use App\AdminModel\Regarding;
use App\AdminModel\Tradeitecenter;
use App\AdminModel\ContactTradesitecenter;
use Maatwebsite\Excel\Concerns\ToModel;
use App\AdminModel\ContactListFilter;
use App\AdminModel\JobSeekerContact;
use App\AdminModel\AssociateContact;
use App\AdminModel\ContactCompany;
use App\AdminModel\TextMessageDumb;
use App\AdminModel\WhatsappTemplate;
use App\AdminModel\Userwhatsappapi;
use App\AdminModel\Campaignlist;
use App\AdminModel\Source;
use App\AdminModel\Service;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Http\Requests\CsvImportRequest;
use App\AdminModel\FilesContact;
use App\AdminModel\Visa;
use Illuminate\Support\Facades\Hash;
use Session;
use Auth;
use Carbon\Carbon;
use File;
use DB;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\AdminModel\AllContact;
use App\AdminModel\ContactConversation;
use App\AdminModel\PartyAddress;
use App\AdminModel\ContactRegarding;
use App\AdminModel\Lstage;
use App\AdminModel\Importexport;
use App\AdminModel\WhatsappSendDetails;
use App\Jobs\WhatsappSendJob;
use App\AdminModel\TaskProcess;
use App\AdminModel\ContactsFile;
use App\AdminModel\Crmcandfilter;
use App\AdminModel\CrmCandidate;
use App\AdminModel\Crmcpass;
use App\Jobs\PartySendJob;
use App\Jobs\StaffSendJob;
use App\PartyFile;
use App\AdminModel\PartyServiceFee;
use App\AdminModel\Crmcpay;
use App\AdminModel\Crmcfile;
use App\Crmcnotes;
use App\Crmcpayment;
use App\AdminModel\Crmccv;
use App\AdminModel\Courier;
use App\AdminModel\Courierclone;
use App\AdminModel\PartyFilter;
use App\AdminModel\PartyNotesStatus;
use App\PartyStatus;
use App\AdminModel\PartyActivitiesList;
use App\PartyCampaignList;
use App\PartyMessageActivity;
use App\Jobs\SinglePartyCampaignList;
use App\AdminModel\PartyReminder;
use App\AdminModel\ContactReminder;
use App\AdminModel\ContactCampaignList;
use App\ContactMessageActivity;
use App\Mail\SingleContactCampaignMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\SinglePartyCampaignMail;
use App\AdminModel\Partywakalacard;
use App\Jobs\Staffmulsendjob;
use App\Jobs\PtyMulSendJob;
use App\Jobs\AllcMulSendJob;
use App\PartyStatusActivity;
use App\AdminModel\Ptycareoffst;
use App\AdminModel\Numberadd;
use App\Jobs\Welcomemsg;
use App\Groupm;
use App\Jobs\PartyWelcomeMessage;

use App\Ptyleadonwerpre;
use Str;

class CrmsController extends Controller implements  WithHeadingRow
{
  public function type_list(Request $request)
  {
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm-master/types-list', ['pageConfigs' => $pageConfigs]);
  }

  public function qamrgulfjobs_count_download(Request $request)
  {
    if($request->cnt == 1){
      $visit = QamrgulfjobsDownload::find('1');
      if($visit){
        $visit->visiting_card = ($visit->visiting_card + 1);
        $visit->save();
      }else{
       $visit =  new QamrgulfjobsDownload();
       $visit->visiting_card = '1';
       $visit->save();
      }
    }else if($request->cnt == 2){
      $wakala = QamrgulfjobsDownload::find('1');
      if($wakala){
        $wakala->wakala_card  = ($wakala->wakala_card + 1);
        $id = $wakala->save();
      }else{
        $wakala =  new QamrgulfjobsDownload();
        $wakala->wakala_card = '1';
        $wakala->save();
      }
    }
  }

  public function branch_list()
  { 
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm-master/branch', ['pageConfigs' => $pageConfigs]);  
  }

  public function branch_view($id)
  { 
    $br_data = Branch::where('br_id',$id)->first();

    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm-master/branch_view', ['pageConfigs' => $pageConfigs,'br_data' => $br_data]);  
  }
  public function branch_edit($id)
  { 
    $br_data = Branch::where('br_id',$id)->first();

    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm-master/branch_edit', ['pageConfigs' => $pageConfigs,'br_data' => $br_data]);  
  }


  public function branch_update(Request $request)
  {

    $br = Branch::where('br_id','=',$request->id)->first();
    if($request->hasFile('br_logo')) {
      $file = $request->file('br_logo');
      $file_count = File::files(base_path().'/public/image/crm-candidate/branch');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/crm-candidate/branch', $name);
      $br_logo = $name;
    } else {
      $br_logo = $br->br_logo;
    }

    $br->br_name =  $request->input('br_name');
    $br->mobile_no =  $request->input('mobile_no');
    $br->email_id =  $request->input('email_id');
    $br->wb_link =  $request->input('wb_link');
    $br->br_logo =  $br_logo;
    $br->ow_name =  $request->input('ow_name');
    $br->ow_mobile_no =  $request->input('ow_mobile_no');
    $br->ow_address =  $request->input('ow_address');
    $br->terms =  $request->input('terms');
    $br->user_name =  $request->input('user_name');
    $br->city = $request->input('city');
    $br->pincode = $request->input('pincode');
    // $br->password = $request->input('password') ?  Hash::make($request->input('password')) : $br->password;

    $br->save();

    $pageConfigs = ['pageHeader' => false];

    Session::flash('success', 'Branch Updated Successfully !', ['pageConfigs' => $pageConfigs]);
    return redirect('master/branch/list');  
  }

  public function branch_delete(Request $request)
  {

    $brs = Branch::find($request->input('br_id'));
    if($brs)
      $brs->delete();
    $pageConfigs = ['pageHeader' => false];
    Session::flash('success', 'Branch Delted Successfully !', ['pageConfigs' => $pageConfigs]);
    return redirect('master/branch/list');  
  }

  public function branch_store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'br_name' => 'required',
      'mobile_no' => 'required',
    ]);

    if ($validator->fails()) {
      return redirect('master/branch/list')
      ->withErrors($validator)
      ->withInput();
    }

    if($request->hasFile('br_logo')) {
      $file = $request->file('br_logo');
      $file_count = File::files(base_path().'/public/image/crm-candidate/branch');
      $filecount = 0;

      if ($file_count !== false) 
      {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/crm-candidate/branch', $name);
      $br_logo = $name;

    } else {
      $br_logo = '';
    }

    $br = new Branch();
    $br->br_name =  $request->input('br_name');
    $br->mobile_no =  $request->input('mobile_no');
    $br->email_id =  $request->input('email_id');
    $br->wb_link =  $request->input('wb_link');
    $br->br_logo =  $br_logo;
    $br->ow_name =  $request->input('ow_name');
    $br->ow_mobile_no =  $request->input('ow_mobile_no');
    $br->ow_address =  $request->input('ow_address');
    $br->terms =  $request->input('terms');
    $br->user_name =  $request->input('user_name');
    $br->password =  Hash::make($request->input('password'));
    $br->user_id = Auth::user()->user_id;
    $br->city = $request->city;
    $br->pincode = $request->pincode;
    $br->save();
    $pageConfigs = ['pageHeader' => false];
    Session::flash('success', 'Branch created Successfully !', ['pageConfigs' => $pageConfigs]);
    return redirect('master/branch/list');  

  }

  public function branch_list_json()
  {
    $all_br =  Branch::all();
    $data['data'] = $all_br ;
    return response()->json($data);
  }

  public function get_download_qamrgulfjobs()
  {
    $data = QamrgulfjobsDownload::find('1');
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm/qamrgulf_download', ['pageConfigs' => $pageConfigs,'data' => $data]);  
  }

  public function type_edit(Request $request)
  {
    $id =  $request->input('id');
    $t_data = Type::find($id);
    return response()->json($t_data);
  }

  public function getSelectedParty(Request $request){
    $ids = explode(',',$request->ids);
    $party_dets = DB::table('qr_party_tbl as party')
      ->leftjoin('users as user','party.care_of_id','=','user.user_id')
      ->select('party.*','user.name as uname')
      ->whereIn('pty_id',$ids)
      ->get();

    return response()->json($party_dets);
  }

  public function type_delete($id)
  {
    $t_data = Type::find($id);
    $t_data->delete();
    $pageConfigs = ['pageHeader' => false];
    // return view('/content/apps/crm-master/typeslist', ['pageConfigs' => $pageConfigs]);
    Session::flash('success', 'Category Deletd Successfully !', ['pageConfigs' => $pageConfigs]);
    return redirect('master/crm/type/list');  
  }

  public function label_list()
  {
  $pageConfigs = ['pageHeader' => false];
  return view('/content/crm-master/labels-list', ['pageConfigs' => $pageConfigs]);		
  }

  public function label_edit(Request $request)
  {
    $id =  $request->input('id');
    $l_data = Label::find($id);
    return response()->json($l_data);
  }

  public function label_delete($id)
  {
    $t_data = Label::find($id);
    $t_data->delete();
    $pageConfigs = ['pageHeader' => false];
    Session::flash('success', 'Label Deletd Successfully !', ['pageConfigs' => $pageConfigs]);
    return redirect('master/crm/label/list');  
  }

  public function candidate_attach_Store(Request $request)
  {
      $id = $request->input('edit_id');
      if ($request->hasFile('attachment')) {
      $file = $request->file('attachment');
      $file_count = File::files(base_path().'/public/image/crm-candidate/view');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/crm-candidate/view', $name);
      $attachment = $name;

    } else {
      $attachment = '';
    }

    $jbs = JobSeeker::find($id);
    $jbs->attachment = $attachment ? $attachment : '';
    $jbs->save();

    Session::flash('success', 'Attachment Stored Successfully !');
    return redirect('master/candidate/view/indv/'.$id);

  }

  public function type_store(Request $request)
  {
    $edit_id  = $request->input('edit_id');
    if($edit_id)
    {
      $type = Type::find($edit_id);
      $type->type = $request->input('type');
      $type->save();

      Session::flash('success', 'Category Updated Successfully !');
      return redirect('master/crm/type/list');   
    }else{
    $type = new Type();
    $type->type = $request->input('type');
    $type->save();
    if($request->input('todo')==1){
      Session::flash('success', 'Type Created Successfully !');
      return redirect('app/todo');	
    }
    else
    {
      Session::flash('success', 'Type Created Successfully !');
      return redirect('master/crm/type/list');
    }
  }
  }

  public function type_list_json(Request $request)
  {
  $types = Type::all();
  $data['data'] = $types;
  return response()->json($data);
  }

  public function label_list_json(Request $request)
  {
  $labels = Label::all();
  $data['data'] = $labels;
  return response()->json($data);
  }

  public function label_store(Request $request)
  {

    $edit_id = $request->input('edit_id');
    if($edit_id){
      $type = Label::find($edit_id);
      $type->label = $request->input('label');
      $type->save();
      Session::flash('success', 'Label Updated Successfully !');
      return redirect('master/crm/label/list');  
    }else{
      $lb = new Label();
      $lb->label = $request->input('label');
      $lb->save();
      if($request->input('todo')==1){
        Session::flash('success', 'Label Created Successfully !');
        return redirect('app/todo');	
      }else{
        Session::flash('success', 'Label Created Successfully !');
        return redirect('master/crm/label/list');
      }
    }
  }

  public function job_seeker_list_json()
  {
    $all_jb = JobSeeker::where('status','=','0')->orderBy('id','DESC')->get();
    $data['data'] = $all_jb;
    return response()->json($data);
  }


  public function candidates_list()
  {
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm/candidates', ['pageConfigs' => $pageConfigs]);
  }

  public function job_seeker_list()
  {
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm/job-seeker', ['pageConfigs' => $pageConfigs]);
  }

  public function candidate_edit($type,$id)
  {
    // $jb_data = JobSeeker::find($id);
    $jb_data = CrmCandidate::find($id);
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm/candidate-edit', ['pageConfigs' => $pageConfigs, 'jb_data' => $jb_data,'type' => $type]);
  }
  public function job_seeker_edit($type,$id)
  {
    $jb_data = JobSeeker::find($id);

    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm/job-seeker-edit', ['pageConfigs' => $pageConfigs, 'jb_data' => $jb_data,'type' => $type]);
  }
  public function candidates_list_json()
  {

    $perms = AccessPermissionModule2::where('user_id','=',Auth::user()->user_id)->first();
    $userAuth = Auth::user()->user_type;

    if ($userAuth == 1 || (isset($perms) && $perms->full_access == 1)) {
      $all_jb = DB::table('crm_candidates as carmc')
        ->leftjoin('qr_all_contacts_tbl as conts','carmc.cont_id','=','conts.id')
        ->leftjoin('follow_up_stages as foll','carmc.followup_id','=','foll.id')
        ->leftjoin('work_statuses as wost','carmc.workstatus_id','=','wost.id')
        ->leftjoin('qr_profession_tbl as prof','carmc.prof_id','=','prof.prof_id')
        ->leftjoin('users as user','carmc.careoff_id','=','user.user_id')
        ->leftjoin('crmcpasses as cpass','cpass.crmc_id','=','carmc.id')
        ->leftjoin('qr_emp_service_tbl as empserv','carmc.ser_id','empserv.ser_id')
        ->select('carmc.*','empserv.ser_name as sername','cpass.pass_no as passport','conts.full_name as ag_name','foll.name as follname','wost.name as wosname','prof.prof_eng_name as proffname','user.name as uname')
        ->orderBy('id','DESC')
        ->get();
    }elseif ($userAuth == 2 || (isset($perms) && $perms->full_access == 0)) {
      if ($perms->crm_cand_r == 1) {
        $all_jb = DB::table('crm_candidates as carmc')
        ->leftjoin('qr_all_contacts_tbl as conts','carmc.cont_id','=','conts.id')
        ->leftjoin('follow_up_stages as foll','carmc.followup_id','=','foll.id')
        ->leftjoin('work_statuses as wost','carmc.workstatus_id','=','wost.id')
        ->leftjoin('qr_profession_tbl as prof','carmc.prof_id','=','prof.prof_id')
        ->leftjoin('users as user','carmc.careoff_id','=','user.user_id')
        ->leftjoin('crmcpasses as cpass','cpass.crmc_id','=','carmc.id')
        ->leftjoin('qr_emp_service_tbl as empserv','carmc.ser_id','empserv.ser_id')
        ->select('carmc.*','empserv.ser_name as sername','cpass.pass_no as passport','conts.full_name as ag_name','foll.name as follname','wost.name as wosname','prof.prof_eng_name as proffname','user.name as uname')
        ->orderBy('id','DESC')
        ->get();
      } else {
        $all_jb = DB::table('crm_candidates as carmc')
        ->leftjoin('qr_all_contacts_tbl as conts','carmc.cont_id','=','conts.id')
        ->leftjoin('follow_up_stages as foll','carmc.followup_id','=','foll.id')
        ->leftjoin('work_statuses as wost','carmc.workstatus_id','=','wost.id')
        ->leftjoin('qr_profession_tbl as prof','carmc.prof_id','=','prof.prof_id')
        ->leftjoin('users as user','carmc.careoff_id','=','user.user_id')
        ->leftjoin('crmcpasses as cpass','cpass.crmc_id','=','carmc.id')
        ->leftjoin('qr_emp_service_tbl as empserv','carmc.ser_id','empserv.ser_id')
        ->select('carmc.*','empserv.ser_name as sername','cpass.pass_no as passport','conts.full_name as ag_name','foll.name as follname','wost.name as wosname','prof.prof_eng_name as proffname','user.name as uname')
        ->orderBy('id','DESC')
        ->where('carmc.user_id','=',Auth::user()->user_id)
        ->orWhere('carmc.careoff_id','=',Auth::user()->user_id)
        ->get();
      }
      
    }

    // $all_jb = JobSeeker::where('status','=','1')->orderBy('id','DESC')->get();

    $data['data'] = $all_jb;

    return response()->json($data);
  }

  public function crmcandupdate(Request $request){
    
    $post = CrmCandidate::find($request->id);
    // dd($request);
    if($request->cont_id == 'qin'){
      $post->in_house = 'qin';
    }else{
      $post->cont_id = $request->cont_id;
    }

    $post->careoff_id = $request->care_of_id;
    $post->followup_id = $request->f_stage;
    $post->workstatus_id = $request->jb_status;
    $post->ser_id = $request->ser_id;
    $post->prof_id = $request->position;
    $post->fname = $request->fname;
    $post->lname = $request->lname;
    $post->mobile_no = $request->mob_no;
    $post->whatsapp_no = $request->whats_mob_no;
    $post->alt_no_mob = $request->alt_mob_no;
    $post->email = $request->email;
    $post->expc_id = $request->country;
    $post->sources = $request->source;
    $post->scode = $request->scode;
    $post->save();
    Session::flash('success','Candidate updated!');
    return redirect()->back();
  }

  public function crmcandcvupdate(Request $request){
    // dd($request);

    $post = Crmccv::find($request->cvc_id);
    $post->name = $request->fname.' '.$request->lname;
    $post->prof_id = $request->position;
    $post->expc_id = $request->expc_id;
    $post->expct_sal = $request->expct_sal;
    $post->period = $request->period;
    $post->country_id = $request->country_id;
    $post->edqual = $request->edqual;
    $post->language = $request->language;
    $post->contract_period = $request->contract_period;
    $post->date = $request->date;
    $post->city = $request->city;
    $post->user_id = Auth::user()->user_id;
    $post->save();
    Session::flash('success','CV added!');
    return redirect()->back();
  }

  public function leads_list()
  {
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm/leads', ['pageConfigs' => $pageConfigs]);
  }

  public function lead_profession_list(Request $request)
  {
    $all_p = LeadProfession::all();
    $country = Country::all();
    $prof='';
    $cnt='';
    foreach($all_p as $p)
    {
      $prof.= '<option value='.$p->id.'>'.$p->lang.'</option>';
    }
    $prof.= '<option value=0>Other</option>';
    foreach($country as $c)
    {
      if($c->country_id <= 4)
      {
      $cnt.= '<option value='.$c->country_id.'>'.$c->country_name.'</option>';
      }
    }
    $data['prof'] = $prof;
    $data['cnt'] = $cnt;
    return response()->json($data);
  }

  public function leads_form_list()
  {
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm/lead-form', ['pageConfigs' => $pageConfigs]);
  }

  public function leads_list_json()
  {

    // $all_lead = Lead::orderBy('id', 'DESC')->get();

    $all_lead = Lead::where('lead_status','!=','1')->where('view_status','!=','1')->get();

    $data['data'] = $all_lead;

    return response()->json($data);
  }

  public function leads_view($id)
  {
    $leads =  Lead::find($id);
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm/leads-view', ['pageConfigs' => $pageConfigs, 'leads' => $leads]);
  }

  public function leads_edit($id)
  {
    $leads =  Lead::find($id);
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm/leads-edit', ['pageConfigs' => $pageConfigs, 'leads' => $leads]);
  }

  public function leads_update(Request $request)
  {
    $leads = Lead::find($request->id);
    $leads->cand_name = $request->cand_name ? $request->cand_name : '';
    $leads->mob_no = $request->mob_no ? $request->mob_no : '';
    $leads->email = $request->email ? $request->email : '';
    $leads->country = $request->country ? $request->country : '0';
    $leads->required_service = $request->required_service ? $request->required_service : '';
    $leads->message = $request->message ? $request->message : '';
    $leads->save();
    Session::flash('success', 'Lead Updated successfully !');
    return redirect('master/leads/list');
  }

  public function eupdate(Request $request){
    $post = Lead::find($request->leads_id);
    $post->cand_name = $request->cand_name;
    $post->mob_no = $request->mob_no;
    $post->email = $request->email;
    $post->country = $request->country;
    $post->required_service = $request->required_service;
    $post->message = $request->message;
    $post->lead_status = $request->lead_status;
    $post->assign_id = $request->staff_id;
    $post->save();

    // Upload in All Contact if lead_status 
    if($request->lead_status == 1){
      // check if all contact is exists
      $contch = Allcontact::where('lead_id','=',$request->leads_id)->first();
      if(!isset($contch)){
        $newcont = new Allcontact();
        $newcont->lead_id = $request->leads_id;
        $newcont->full_name = $request->cand_name;
        $newcont->mobile_no = $request->mob_no;
        $newcont->email = $request->email;
        $newcont->expected_cid = $request->country;
        $newcont->user_id = $request->staff_id;
        $newcont->owner_id = $request->staff_id;
        $newcont->assign_id = $request->staff_id;
        $newcont->lead_type = $request->lead_type;
        $newcont->source_id = '9';
        $newcont->source = "QAMARJOBCOM";
        $newcont->lead_msg = $request->message;
        $newcont->save();

        // update in conversation
        $converc = new ContactConversation();
        $converc->contact_id = $newcont->id;
        $converc->messageText = $request->message;
        $converc->user_id = $request->staff_id;
        $converc->contype_id = '6';
        $converc->save();
      }
    }

    Session::flash('success','Lead updated!');
    return redirect()->back();

  }

  public function leads_delete(Request $request)
  {
    $id = $request->lead_id;
    $lead = Lead::find($id);
    // $lead->delete();
    $lead->view_status = '1';
    $lead->save();
    Session::flash('warning', 'Lead Deleted successfully !');
    return redirect('master/leads/list');
  }

  public function profileUpload(Request $request)
  {
    if ($request->hasFile('profile_pic')) {
      $file = $request->file('profile_pic');
      $file_count = File::files(base_path().'/public/image/crm-candidate/profile');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/crm-candidate/profile', $name);
      $profile_pic = $name;

    } else {
      $profile_pic = '';
    }

    $post = CrmCandidate::find($request->crmc_id);
    $post->profile_pic = $profile_pic;
    $post->save();
    Session::flash('success','Profile Picture uploaded!');
    return redirect()->back();
  } 

  public function candidate_view($type,$id)
  {
  //  $jb_data    =   JobSeeker::find($id);
    $jb_data = CrmCandidate::find($id);
    $pass_data = Crmcpass::where('crmc_id','=',$id)->first();
    $crmcpay = Crmcpay::where('crmc_id','=',$id)->get();
    $crmcfiles = Crmcfile::where('crmc_id','=',$id)->get();
    $crmcv = Crmccv::where('crmc_id','=',$id)->first();

    $note_data  = Note::where('jb_id','=',$id)->get();
    $task_data  = Task::where('jb_id','=',$id)->get();
    $app_data  = Appoint::where('jb_id','=',$id)->get();
    $file_data  = Files::where('jb_id','=',$id)->get();

    $pageConfigs = ['pageHeader' => false];    
    return view('/content/crm/candidate-view', ['pageConfigs' => $pageConfigs,'jb_data' => $jb_data ,'crmcv' => $crmcv,'type' => $type,'note_data'=> $note_data,'task_data'=>$task_data,'app_data'=>$app_data,'file_data'=>$file_data,'pass_data' => $pass_data,'crmcpay' => $crmcpay,'crmcfiles' => $crmcfiles ]);
  }

  public function cvstore(Request $request){
    $post = new Crmccv();
    $post->crmc_id = $request->crmc_id; 
    $post->name = $request->fname.' '.$request->lname;
    $post->prof_id = $request->position;
    $post->expc_id = $request->expc_id;
    $post->expct_sal = $request->expct_sal;
    $post->period = $request->period;
    $post->country_id = $request->country_id;
    $post->edqual = $request->edqual;
    $post->language = $request->language;
    $post->contract_period = $request->contract_period;
    $post->date = $request->date;
    $post->city = $request->city;
    $post->user_id = Auth::user()->user_id;
    $post->save();
    Session::flash('success','CV added!');
    return redirect()->back();

  }

  public function crmcpassupdate(Request $request)
  { 
    $post = Crmcpass::find($request->id);

    if ($request->hasFile('cand_photo')) {
      $file = $request->file('cand_photo');
      foreach ($file as  $file) {
        $file_count = File::files(base_path().'/public/image/crm-candidate/passport');
        $filecount = 0;
        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        $file_exe = $file->getClientOriginalExtension();
        $name = $filecount . '.' . $file_exe;
        $file->move(base_path().'/public/image/crm-candidate/passport', $name);
        // $file->move(base_path().'/public/status-images/mofa_images', $name);
        $candidate_photo[] = $name;
      }
    }else {
      $candidate_photo[] = $post->photo;
    }

    if ($request->hasFile('cand_passport_doc')) {
      $file = $request->file('cand_passport_doc');
      foreach ($file as  $file) {
        $file_count = File::files(base_path().'/public/image/crm-candidate/passport');
        $filecount = 0;

        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        
        $file_exe = $file->getClientOriginalExtension();
        $name = $filecount . '.' . $file_exe;

        $file->move(base_path().'/public/image/crm-candidate/passport', $name);
        $candidate_passport[] = $name;
      }
    }else {
      $candidate_passport[] = $post->passport;
    }
    
    if ($request->hasFile('cand_doc')) {
      $file = $request->file('cand_doc');
      foreach ($file as  $file) {
        $file_count = File::files(base_path().'/public/image/crm-candidate/passport');
        $filecount = 0;

        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        $file_exe = $file->getClientOriginalExtension();
        $name = $filecount . '.' . $file_exe;

        $file->move(base_path().'/public/image/crm-candidate/passport', $name);
        // $file->move(base_path().'/public/status-images/passport_documents', $name);
        $candidate_doc[] = $name;
      }
    } else {
      $candidate_doc[] = $post->documents;
    }

    $post->fname = $request->fname;
    $post->mname = $request->mname;
    $post->lname = $request->lname;
    $post->pass_no = $request->pass_no;
    $post->pob = $request->pob;
    $post->poi_id = $request->poi_id;
    $post->doi = $request->doi;
    $post->doe = $request->doe;
    $post->dob = $request->dob;
    $post->country_id = $request->country_id;
    $post->gender = $request->gender;
    $post->prof_id = $request->prof_id;
    $post->religion_id = $request->religion_id;
    $post->address = $request->address;
    $post->pincode = $request->pincode;
    $post->mobile_no = $request->mobile_no;
    $post->notes = $request->notes;
    $post->photo = implode(',', $candidate_photo);
    $post->passport = implode(',', $candidate_passport);
    $post->documents = implode(',', $candidate_doc);
    $post->user_id = Auth::user()->user_id;
    $post->save();
    Session::flash('success','Passport updated!');
    return redirect()->back();

  }

  public function candPaymentstore(Request $request){

    if($request->hasFile('file')){
      $file = $request->file('file');
      $file_count = File::files(base_path().'/public/image/crm-candidate/payment');
      $filecount = 0;
      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;
      $file->move(base_path().'/public/image/crm-candidate/payment', $name);
      $payment_slip = $name;
    }else{
      $payment_slip = '';
    }

    // if ($request->hasFile('file')) {
    //   $file = $request->file('file');
    //   foreach ($file as  $file) {
    //     $file_count = File::files(base_path().'/public/image/crm-candidate/payment');
    //     $filecount = 0;
    //     if ($file_count !== false) {
    //       $filecount = count($file_count);
    //     }
    //     $file_exe = $file->getClientOriginalExtension();
    //     $name = $filecount . '.' . $file_exe;
    //     $file->move(base_path().'/public/image/crm-candidate/payment', $name);
    //     // $file->move(base_path().'/public/status-images/mofa_images', $name);
    //     $payment_slip[] = $name;
    //   }
    // }else {
    //   $payment_slip[] = '';
    // }

    $post = new Crmcpayment();
    $post->crmc_id = $request->crmc_id;
    $post->txn_id = $request->txn_id;
    $post->amount = $request->amount;
    $post->bank_name = $request->bank_name;
    $post->account_no = $request->account_no;
    $post->notes = $request->notes;
    $post->file = $payment_slip;
    $post->user_id = Auth::user()->user_id;
    $post->save();
    Session::flash('success','Payment added!');
    return redirect()->back();
  }

  public function candPaymentDelete(Request $request){
    extract($_POST);
    $post = Crmcpayment::find($id);
    $post->delete();
  }

  public function candScodeDelete(Request $request){
    extract($_POST);
    $post = Crmcpay::find($id);
    $post->delete();
  }

  public function storeNotes(Request $request){
    $post = new Crmcnotes();
    $post->crmc_id = $request->input('crmc_id');
    $post->notes = $request->input('notes');
    $post->user_id = Auth::user()->user_id;
    $post->save();
    Session::flash('success','Notes is created!');
    return redirect()->back();

  }

  public function deleteNotes(Request $request){
    extract($_POST);
    $post = Crmcnotes::find($id);
    $post->delete();
  }

  public function profileDeletefile(Request $request){
    extract($_POST);
    $post = CrmCandidate::find($id);
    $_images = [];
    $_image = [];
    $_images[] = $post->profile_pic;
    $_image[] = $ch_val;
    $new_arr = array_diff($_images,$_image);
    $post->profile_pic = implode($new_arr);
    $post->save();
    Session::flash('success','file deleted!');
    return redirect()->back();  
  }

  public function photoDeletefile(Request $request){
    extract($_POST);
    $post = Crmcpass::where('crmc_id','=',$id)->where('photo','=',$ch_val)->first();
    $_images = [];
    $_image = [];
    $_images[] = $post->photo;
    $_image[] = $ch_val;
    $new_arr = array_diff($_images,$_image);
    $post->photo = implode($new_arr);
    $post->save();
    Session::flash('success','file deleted!');
    return redirect()->back();  
  }

  public function passportDeletefile(Request $request){
    extract($_POST);
    $post = Crmcpass::where('crmc_id','=',$id)->where('passport','=',$ch_val)->first();
    $_images = [];
    $_image = [];
    $_images[] = $post->passport;
    $_image[] = $ch_val;
    $new_arr = array_diff($_images,$_image);
    $post->passport = implode($new_arr);
    $post->save();
    Session::flash('success','file deleted!');
    return redirect()->back();  
  }

  public function docsDeletefile(Request $request){
    extract($_POST);
    $post = Crmcpass::where('crmc_id','=',$id)->where('documents','=',$ch_val)->first();
    $_images = [];
    $_image = [];
    $_images[] = $post->documents;
    $_image[] = $ch_val;
    $new_arr = array_diff($_images,$_image);
    $post->documents = implode($new_arr);
    $post->save();
    Session::flash('success','file deleted!');
    return redirect()->back();  
  }

  public function crmcDeletefile(Request $request){
    extract($_POST);
    $post = Crmcfile::find($ch_val);
    $post->delete();
    Session::flash('success',$post->title.' deleted!');
    return redirect()->back();
  }

  public function fileuploadC(Request $request){
    if ($request->hasFile('file')) {
      $file = $request->file('file');
      $file_count = File::files(base_path().'/public/image/crm-candidate/view');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/crm-candidate/view', $name);
      $cand_file = $name;

    } else {
      $cand_file = '';
    }

    $cont_id =  $request->input('crmc_id');
    $post = new Crmcfile();
    $post->crmc_id = $cont_id;
    $post->file = $cand_file;
    $post->title = $request->title;
    $post->user_id = Auth::user()->user_id;
    $post->save();

    Session::flash('success','File Uploaded successfully!');
    return redirect()->back();
  }

  public function updateFollowupStage(Request $request)
  {
    extract($_POST);
    $post = CrmCandidate::find($id);
    $post->followup_id = $ch_val;
    $post->save();
  }

  public function updateRecruitStatus(Request $request){
    extract($_POST);
    $post = CrmCandidate::find($id);
    $post->workstatus_id	 = $ch_val;
    $post->save();
  }

  public function updateMedicalStage(Request $request){
    extract($_POST);
    $post = CrmCandidate::find($id);
    $post->medical_id	 = $ch_val;
    $post->save();
  }

  public function crmMusanedupt(Request $request){
    extract($_POST);
    $post = CrmCandidate::find($id);
    $post->musaned_id	 = $ch_val;
    $post->save();
  }


  public function updateFlightStage(Request $request){
    extract($_POST);
    $post = CrmCandidate::find($id);
    $post->flight_id	 = $ch_val;
    $post->save();
  }

  public function addCrmcpassport(Request $request){
    if ($request->hasFile('cand_photo')) {
      $file = $request->file('cand_photo');
      foreach ($file as  $file) {
        $file_count = File::files(base_path().'/public/image/crm-candidate/passport');
        $filecount = 0;
        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        $file_exe = $file->getClientOriginalExtension();
        $name = $filecount . '.' . $file_exe;
        $file->move(base_path().'/public/image/crm-candidate/passport', $name);
        // $file->move(base_path().'/public/status-images/mofa_images', $name);
        $candidate_photo[] = $name;
      }
    }else {
      $candidate_photo[] = '';
    }

    if ($request->hasFile('cand_passport_doc')) {
      $file = $request->file('cand_passport_doc');
      foreach ($file as  $file) {
        $file_count = File::files(base_path().'/public/image/crm-candidate/passport');
        $filecount = 0;

        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        
        $file_exe = $file->getClientOriginalExtension();
        $name = $filecount . '.' . $file_exe;

        $file->move(base_path().'/public/image/crm-candidate/passport', $name);
        $candidate_passport[] = $name;
      }
    }else {
      $candidate_passport[] = '';
    }
    
    if ($request->hasFile('cand_doc')) {
      $file = $request->file('cand_doc');
      foreach ($file as  $file) {
        $file_count = File::files(base_path().'/public/image/crm-candidate/passport');
        $filecount = 0;

        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        $file_exe = $file->getClientOriginalExtension();
        $name = $filecount . '.' . $file_exe;

        $file->move(base_path().'/public/image/crm-candidate/passport', $name);
        // $file->move(base_path().'/public/status-images/passport_documents', $name);
        $candidate_doc[] = $name;
      }
    } else {
      $candidate_doc[] = '';
    }

    $post = new Crmcpass();
    $post->crmc_id = $request->crmc_id;
    $post->fname = $request->fname;
    $post->mname = $request->mname;
    $post->lname = $request->lname;
    $post->pass_no = $request->pass_no;
    $post->pob = $request->pob;
    $post->poi_id = $request->poi_id;
    $post->doi = $request->doi;
    $post->doe = $request->doe;
    $post->dob = $request->dob;
    $post->country_id = $request->country_id;
    $post->gender = $request->gender;
    $post->prof_id = $request->prof_id;
    $post->religion_id = $request->religion_id;
    $post->address = $request->address;
    $post->pincode = $request->pincode;
    $post->mobile_no = $request->mobile_no;
    $post->notes = $request->notes;
    $post->photo = implode(',', $candidate_photo);
    $post->passport = implode(',', $candidate_passport);
    $post->documents = implode(',', $candidate_doc);
    $post->user_id = Auth::user()->user_id;
    $post->save();
    Session::flash('success','Passport updated!');
    return redirect()->back();


  }

  public function job_seeker_view($type,$id)
  {
  $jb_data    =   JobSeeker::find($id);
  $note_data  = Note::where('jb_id','=',$id)->get();
  $task_data  = Task::where('jb_id','=',$id)->get();
  $app_data  = Appoint::where('jb_id','=',$id)->get();
  $file_data  = Files::where('jb_id','=',$id)->get();

  $pageConfigs = ['pageHeader' => false];    
  return view('/content/crm/job-seeker-view', ['pageConfigs' => $pageConfigs,'jb_data' => $jb_data ,'type' => $type,'note_data'=> $note_data,'task_data'=>$task_data,'app_data'=>$app_data,'file_data'=>$file_data ]);
  }

  public function change_status(Request $request)
  {
    $type =   $request->input('type');
    $value = $request->input('values');
    $id = $request->input('jbs_id');
    
    if($type=='f_stage'){
      $jbs = JobSeeker::find($id);
      $jbs->f_stage = $value;
      $jbs->save();
    }
    if($type=='work_st'){
      $jbs = JobSeeker::find($id);
      $jbs->jb_status = $value;
      $jbs->save();
    }
    if($type=='ass_to'){
      $jbs = JobSeeker::find($id);
      $jbs->ass_to = $value;
      $jbs->save();
    }



  }

  public function files_store(Request $request)
  {
    if ($request->hasFile('cand_file')) {
      $file = $request->file('cand_file');
      $file_count = File::files(base_path().'/public/image/crm-candidate/view');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/crm-candidate/view', $name);
      $cand_file = $name;

    } else {
      $cand_file = '';
    }
    $jb_id =  $request->input('jb_id');
    $crm = new Files();
    $crm->cand_file = $cand_file;
    $crm->jb_id = $jb_id;
    $crm->save();
    Session::flash('success', 'File Uploaded successfully !');
    return redirect('master/candidate/view/indv/'.$jb_id);

  }

  public function files_contact_store(Request $request)
  {
    if ($request->hasFile('cand_file')) {
      $file = $request->file('cand_file');
      $file_count = File::files(base_path().'/public/image/crm-contact/files');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/crm-contact/files', $name);
      $cand_file = $name;

    } else {
      $cand_file = '';
    }
    $cont_id =  $request->input('cont_id');
    $crm = new FilesContact();
    $crm->cand_file = $cand_file;
    $crm->cont_id = $cont_id;
    $crm->save();

    $tm = new TimelineContactCrm();
    $tm->cont_id = $cont_id;
    $tm->subject = 'File created';
    $tm->insert_id = $crm->id;
    $tm->user_type = Auth::user()->user_type;
    $tm->user_id = Auth::user()->user_id;
    $tm->save();
    Session::flash('success', 'File Uploaded successfully !');
    return redirect('master/contact/view/'.$cont_id);

  }

  public function partynotes(Request $request){

    $post = new PartyNotesStatus();
    $post->pty_id = $request->pty_id;
    $post->notes = $request->notes;
    $post->user_id = Auth::user()->user_id;
    $post->conv_type = $request->conv_type;
    $post->save();

    // Update data in Party List
    $party = Party::find($request->pty_id);
    $party->conv_type = $request->conv_type;
    $party->notes_date = date('Y-m-d');
    $party->save();

    $data = "Notes saved!";
    return response()->json($data);
    

  }

  public function candidate_rdel(Request $request){
    $post = CrmCandidate::find($request->cand_id);
    $post->delete();
    Session::flash('success','Candidate deleted!');
    return redirect()->back();
  }

  public function candidate_delete($type,$id){
    $st = JobSeeker::find($id);
    $st->delete();

    $user = User::where(['user_id' => $id],['user_type' => '4'])->first();
    $user->delete();

    if($type=='profile'){
      Session::flash('success', 'Candidate Deleted Successfully !');
      return redirect('/');  
    }
    Session::flash('success', 'Candidate Deleted Successfully !');
    return redirect('master/candidates/list');

  }

  public function job_seeker_delete($type,$id){
    $st = JobSeeker::find($id);
    $st->delete();

    $user = User::where(['user_id' => $id],['user_type' => '4'])->first();
    $user->delete();

    if($type=='profile'){
      Session::flash('success', 'Candidate Deleted Successfully !');
      return redirect('/');  
    }
    Session::flash('success', 'Candidate Deleted Successfully !');
    return redirect('master/job-seeker/list');

  }

  public function notes_store(Request $request)
  {
    $note = $request->input('notes');
    $id = $request->input('id');
    $crm = new Note();
    $crm->notes = $note;
    $crm->jb_id = $id;
    $crm->save();
    Session::flash('success', 'Notes created successfully !');

    return redirect('master/candidate/view/indv/'.$id);
  }

  public function task_edit(Request $request)
  {
    $id = $request->input('id');
    $task_data = Task::find($id);
    return response()->json($task_data);
  }


  public function appoint_edit(Request $request)
  {
    $id = $request->input('id');
    $app_data = Appoint::find($id);
    return response()->json($app_data);
  }



  public function tasks_store(Request $request)
  {
    $edit_id = $request->input('edit_id');
    if($edit_id){
      $jb_id = $request->input('jb_id');
      $title = $request->input('title');
      $desc = $request->input('desc');
      $task_type = $request->input('task_type');
      $due_date = $request->input('due_date');
      $time = $request->input('time');

      $crm = Task::find($edit_id);
      $crm->title = $title;
      $crm->desc = $desc;
      $crm->task_type = $task_type;
      $crm->due_date = $due_date;
      $crm->time = $time;
      $crm->save();
      Session::flash('success', 'Task Updated successfully !');
      return redirect('master/candidate/view/indv/'.$jb_id); 
    }else{

      $title = $request->input('title');
      $desc = $request->input('desc');
      $task_type = $request->input('task_type');
      $due_date = $request->input('due_date');
      $time = $request->input('time');
      $id = $request->input('id');

      $crm = new Task();
      $crm->title = $title;
      $crm->desc = $desc;
      $crm->task_type = $task_type;
      $crm->due_date = $due_date;
      $crm->time = $time;
      $crm->jb_id = $id;
      $crm->save();
      Session::flash('success', 'Task created successfully !');
      return redirect('master/candidate/view/indv/'.$id); 
    }
  }

  public function appoint_store(Request $request)
  {
    // dd($request);
    $edit_id = $request->input('edit_id');
    if($edit_id){

      $jb_id = $request->input('jb_id');

      $crm = Appoint::find($edit_id);
      $crm->title = $request->input('title') ? $request->input('title') : '';
      $crm->description = $request->input('description') ? $request->input('description') :'';
      $crm->froms = $request->input('froms') ? $request->input('froms') : '';
      $crm->time1 = $request->input('time1') ? $request->input('time1') :'';
      $crm->tos = $request->input('tos') ? $request->input('tos') : '';
      $crm->time2 = $request->input('time2') ? $request->input('time2') :'';
      $crm->wheres = $request->input('wheres') ? $request->input('wheres') :'';
      $crm->outcome = $request->input('outcome') ? $request->input('outcome') :'';
      $crm->save();
      Session::flash('success', 'Task Updated successfully !');
      return redirect('master/candidate/view/indv/'.$jb_id); 
    }else{
      $crm = new Appoint();
      $crm->title = $request->input('title') ? $request->input('title') : '';
      $crm->description = $request->input('description') ? $request->input('description') :'';
      $crm->froms = $request->input('froms') ? $request->input('froms') : '';
      $crm->time1 = $request->input('time1') ? $request->input('time1') :'';
      $crm->tos = $request->input('tos') ? $request->input('tos') : '';
      $crm->time2 = $request->input('time2') ? $request->input('time2') :'';
      $crm->wheres = $request->input('wheres') ? $request->input('wheres') :'';
      $crm->outcome = $request->input('outcome') ? $request->input('outcome') :'';
      $crm->jb_id = $request->input('jb_id') ? $request->input('jb_id') : '';
      $crm->save();
      $jb_id = $request->input('jb_id');
    
      Session::flash('success', 'Appointment created successfully !');
      return redirect('master/candidate/view/indv/'.$jb_id); 
    }
  }



  public function notes_delete($jb_id,$id)
  {
    $jb = Note::find($id);
    $jb->delete();
    Session::flash('danger', 'Notes deteted successfully !');
    return redirect('master/candidate/view/indv/'.$jb_id); 
  }

  public function task_delete($jb_id,$id)
  {
    $jb = Task::find($id);
    $jb->delete();
    Session::flash('danger', 'Task deteted successfully !');
    return redirect('master/candidate/view/indv/'.$jb_id); 
  }
  public function appoint_delete($jb_id,$id)
  {
    $jb = Appoint::find($id);
    $jb->delete();
    Session::flash('danger', 'Appointment deteted successfully !');
    return redirect('master/candidate/view/indv/'.$jb_id); 
  }
  public function file_delete($jb_id,$id)
  {
    $jb = Files::find($id);
    $jb->delete();
    Session::flash('danger', 'File deteted successfully !');
    return redirect('master/candidate/view/indv/'.$jb_id); 
  }

  public function job_seeker_update(Request $request)
  {
    if($request->input('account')){
      if ($request->hasFile('profile_pic')) {
        $file = $request->file('profile_pic');
        $file_count = File::files(base_path().'/public/image/crm-candidate/profile');
        $filecount = 0;
        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        $file_exe = $file->getClientOriginalExtension();
        $name = $filecount . '.' . $file_exe;
        $file->move(base_path().'/public/image/crm-candidate/profile', $name);
        $profile_pic = $name;
      } else {
        $profile_pic = '';
      }
      $edit_id = $request->input('edit_id');
      $jbs =  JobSeeker::find($edit_id);
    
      $jbs->f_stage= $request->input('f_stage') ? $request->input('f_stage') : '';
      $jbs->jb_status= $request->input('jb_status') ? $request->input('jb_status') : '';
      $jbs->ass_to= $request->input('ass_to') ? $request->input('ass_to') :'';
      $jbs->source= $request->input('source') ? $request->input('source') : '';
      $jbs->cand_name = $request->input('cand_name') ? $request->input('cand_name') : '' ;
      $jbs->mob_no= $request->input('mob_no') ? $request->input('mob_no') : '';
      $jbs->whats_mob_no= $request->input('whats_mob_no') ? $request->input('whats_mob_no') : '';
      $jbs->email= $request->input('email')  ? $request->input('email') : '';

      $jbs->position= $request->input('position') ?  $request->input('position') : '';
      $jbs->exp_sal= $request->input('exp_sal')  ? $request->input('exp_sal') : '';
      $jbs->profile_pic= $profile_pic ? $profile_pic  : $request->input('old_profile_pic');
      $jbs->save();

      $last_id = $jbs->id;

      $user = User::where('user_id','=',$last_id)->where('user_type','=','4')->first();
      $user->email = $request->input('email')  ? $request->input('email') : $user->email;
      $user->save();

      Session::flash('success', 'Candidate Updated Successfully !');
      return redirect('master/candidates/list');

    }
    if($request->input('personal_info')){

      $edit_id = $request->input('edit_id');
      $jbs =  JobSeeker::find($edit_id);
      $jbs->alt_mob_no= $request->input('alt_mob_no') ? $request->input('alt_mob_no') : '';
      $jbs->fb_link= $request->input('fb_link')  ? $request->input('fb_link') : '';
      $jbs->notes= $request->input('notes')  ? $request->input('notes') : '';
      $jbs->country= $request->input('country') ? $request->input('country') : '';
      $jbs->address= $request->input('address') ? $request->input('address') : '';
      $jbs->username= $request->input('username') ? $request->input('username') : '';
      $jbs->password= $request->input('password') ? Hash::make($request->input('password')) : $jbs->password;
      $jbs->sc_link= $request->input('sc_link') ? $request->input('sc_link') : '';
      $jbs->exp_loc= $request->input('exp_loc') ? $request->input('exp_loc') : '';
      $jbs->save();

      $last_id = $jbs->id;

      $user = User::where('user_id','=',$last_id)->where('user_type','=','4')->first();
    
      $user->username = $request->input('username') ? $request->input('username') : '' ;
      $user->name = $request->input('cand_name') ? $request->input('cand_name') : '' ;
      $user->password = $request->input('password') ? Hash::make($request->input('password')) : $user->password;
      $user->save();

      Session::flash('success', 'Candidate Updated Successfully !');
      return redirect('master/candidates/list');
    }
    if($request->input('social')){
      if ($request->hasFile('resume')) {
        $file = $request->file('resume');
        $file_count = File::files(base_path().'/public/image/crm-candidate/resume');
        $filecount = 0;
        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        $file_exe = $file->getClientOriginalExtension();
        $name = $filecount . '.' . $file_exe;
        $file->move(base_path().'/public/image/crm-candidate/resume', $name);
        $resume = $name;
      } else {
        $resume = '';
      }
      $edit_id = $request->input('edit_id');
      $jbs = JobSeeker::find($edit_id);
      $jbs->resume= $resume ? $resume  : $request->input('old_resume');
      $jbs->save();
      Session::flash('success', 'Candidate Updated Successfully !');
      return redirect('master/candidates/list');
    }
  }

  public function leads_store(Request $request){

    if ($request->hasFile('resume')) {
      $file = $request->file('resume');
      $file_count = File::files(base_path().'/public/image/crm-lead/resume');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/crm-lead/resume', $name);
      $resume = $name;

    } else {
      $resume = '';
    }

    if ($request->hasFile('profile_pic')) {
      $file = $request->file('profile_pic');
      $file_count = File::files(base_path().'/public/image/crm-lead/profile');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/crm-lead/profile', $name);
      $profile_pic = $name;

    } else {
      $profile_pic = '';
    }

    $ld = new Lead();
    $ld->cand_name = $request->input('cand_name') ? $request->input('cand_name') : '' ;
    $ld->mob_no= $request->input('mob_no') ? $request->input('mob_no') : '';
    $ld->alt_mob_no= $request->input('alt_mob_no') ? $request->input('alt_mob_no') : '';
    $ld->email= $request->input('email')  ? $request->input('email') : '';

    $ld->position= $request->input('position') ?  $request->input('position') : '';
    $ld->exp_sal= $request->input('exp_sal')  ? $request->input('exp_sal') : '';
    $ld->exp_loc= $request->input('exp_loc') ? $request->input('exp_loc') : '';
    $ld->fb_link= $request->input('fb_link')  ? $request->input('fb_link') : '';
    $ld->status= $request->input('status')  ? $request->input('status') : '';
    $ld->notes= $request->input('notes')  ? $request->input('notes') : '';
    $ld->f_stage= $request->input('f_stage') ? $request->input('f_stage') : '';
    $ld->country= $request->input('country') ? $request->input('country') : '';
      // $ld->country_code= $request->input('country_code') ? $request->input('country_code') : '';
    $ld->whats_mob_no= $request->input('whats_mob_no') ? $request->input('whats_mob_no') : '';
    $ld->sc_link= $request->input('sc_link') ? $request->input('sc_link') : '';
    $ld->source= $request->input('source') ? $request->input('source') : '';
    $ld->address= $request->input('address') ? $request->input('address') : '';
    $ld->message= $request->input('message') ? $request->input('message') : '';
    $ld->email= $request->input('email') ? $request->input('email') : '';
    $ld->required_service= $request->input(' required_service ') ? $request->input(' required_service ') : '';
    $ld->username= $request->input('username') ? $request->input('username') : '';
    $ld->password= $request->input('password') ? Hash::make($request->input('password')) : '';

    $ld->resume= $resume ? $resume  : '';
    $ld->date= date('Y-m-d');

    $ld->created_at= date("F j, Y, g:i a"); 
    $ld->profile_pic= $profile_pic ? $profile_pic  : '';

    $ld->jb_status= $request->input('jb_status') ? $request->input('jb_status') : '';
    $ld->ass_to= $request->input('ass_to') ? $request->input('ass_to') :'';
    $ld->save();
    $last_id = $ld->id;

    $user = new User();
    $user->user_type =4;
    $user->user_id = $last_id;
    $user->username = $request->input('username') ? $request->input('username') : '' ;
    $user->name = $request->input('cand_name') ? $request->input('cand_name') : '' ;
    $user->password = $request->input('password') ? Hash::make($request->input('password')) : '';
    $user->email = $request->input('email')  ? $request->input('email') : '';
    $user->save();


    Session::flash('success', 'Lead Created Successfully !');
    return redirect('master/leads/list');

  }

  public function lead_store(Request $request)
  {

    if($request->input('job_seeker')){

      if ($request->hasFile('resume')) {
        $file = $request->file('resume');
        $file_count = File::files(base_path().'/public/image/crm-candidate/resume');
        $filecount = 0;

        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        $file_exe = $file->getClientOriginalExtension();
        $name = $filecount . '.' . $file_exe;

        $file->move(base_path().'/public/image/crm-candidate/resume', $name);
        $resume = $name;

      } else {
        $resume = '';
      }

      if ($request->hasFile('profile_pic')) {
        $file = $request->file('profile_pic');
        $file_count = File::files(base_path().'/public/image/crm-candidate/profile');
        $filecount = 0;

        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        $file_exe = $file->getClientOriginalExtension();
        $name = $filecount . '.' . $file_exe;

        $file->move(base_path().'/public/image/crm-candidate/profile', $name);
        $profile_pic = $name;

      } else {
        $profile_pic = '';
      }

      $jbs = new JobSeeker();
      $jbs->cand_name = $request->input('cand_name') ? $request->input('cand_name') : '' ;
      $jbs->mob_no= $request->input('mob_no') ? $request->input('mob_no') : '';
      $jbs->alt_mob_no= $request->input('alt_mob_no') ? $request->input('alt_mob_no') : '';
      $jbs->email= $request->input('email')  ? $request->input('email') : '';

      // $jbs->position= $request->input('position') ?  $request->input('position') : '';
      $jbs->pos_id = $request->input('position') ?  $request->input('position') : '';
      $jbs->exp_sal= $request->input('exp_sal')  ? $request->input('exp_sal') : '';
      $jbs->exp_loc= $request->input('exp_loc') ? $request->input('exp_loc') : '';
      $jbs->fb_link= $request->input('fb_link')  ? $request->input('fb_link') : '';
      $jbs->status= $request->input('status')  ? $request->input('status') : '';
      $jbs->notes= $request->input('notes')  ? $request->input('notes') : '';
      $jbs->f_stage= $request->input('f_stage') ? $request->input('f_stage') : '';
      $jbs->country= $request->input('country') ? $request->input('country') : '';
      // $jbs->country_code= $request->input('country_code') ? $request->input('country_code') : '';
      $jbs->whats_mob_no= $request->input('whats_mob_no') ? $request->input('whats_mob_no') : '';
      $jbs->sc_link= $request->input('sc_link') ? $request->input('sc_link') : '';
      $jbs->source= $request->input('source') ? $request->input('source') : '';
      $jbs->address= $request->input('address') ? $request->input('address') : '';
      $jbs->username= $request->input('username') ? $request->input('username') : '';
      $jbs->password= $request->input('password') ? Hash::make($request->input('password')) : '';
      $jbs->resume= $resume ? $resume  : '';
      $jbs->date= date('Y-m-d');
      $jbs->profile_pic= $profile_pic ? $profile_pic  : '';

      $jbs->jb_status= $request->input('jb_status') ? $request->input('jb_status') : '';
      $jbs->ass_to= $request->input('ass_to') ? $request->input('ass_to') :'';
      $jbs->save();
      $last_id = $jbs->id;

      $user = new User();
      $user->user_type =4;
      $user->user_id = $last_id;
      $user->username = $request->input('username') ? $request->input('username') : '' ;
      $user->name = $request->input('cand_name') ? $request->input('cand_name') : '' ;
      $user->password = $request->input('password') ? Hash::make($request->input('password')) : '';
      $user->email = $request->input('email')  ? $request->input('email') : '';
      $user->save();
      
      Session::flash('success', 'Candidate Created Successfully !');
      return redirect('master/candidates/list');
      
    }
    if($request->input('associate')){
      extract($_POST);
      $ass = new Associate();
      $ass ->ag_type =$ag_type;
      $ass ->ass_name =$ass_name;
      $ass ->mob_no =$mob_no;
      $ass ->email =$email;
      $ass ->alt_mob_no =$alt_mob_no;
      $ass ->fb_link =$fb_link;
      $ass ->sc_link =$sc_link;
      $ass ->address =$address;
      $ass ->ass_to = $ass_to;
      $ass ->jb_status = $status;
      $ass ->user_id = Auth::id();
      $ass ->staff_id = Auth::id();
      $ass ->f_stage = $f_stage;
      $ass ->save();

      Session::flash('success', 'Associate created successfully !');
      return redirect('options/leads');

    }

    if($request->input('compony')){

    extract($_POST);
    $comp =  new Company();
    $comp->co_name =  $co_name;
    $comp->co_person =$co_person;
    $comp->co_name_arb =$co_name_arb;
    $comp->position =$position;
    $comp->mob_no =$mob_no;
    $comp->tel_no =$tel_no;
    $comp->email1 =$email1;
    $comp->email2 =$email2;
    $comp->email3 =$email3;
    $comp->website =$website;
    $comp->city =$city;
    $comp->country =$country;
    $comp->lk_link =$lk_link;
    $comp->sc_link =$sc_link;
    $comp->address =$address;
    $comp->status =$status;
    $comp->f_stage =$f_stage;

    $comp->ass_to =$ass_to;
    $comp->t_date =$t_date;
    $comp->save();

    Session::flash('success', 'Company created successfully !');
    return redirect('options/leads');
  }
  }

  public function category_list()
  {

  }


  public function leadEdit(Request $request){
    $post = Lead::find($request->id);

    return response()->json($post);
  }

  public function api_lead_store(Request $request)
  {
    // dd($request);
    $lid = new Lead();
    $lid->cand_name =  $request->cand_name ? $request->cand_name :'';
    $lid->mob_no = $request->mob_no ? $request->mob_no : '';
    $lid->message = $request->message ? $request->message : '';
    $lid->required_service = $request->required_service ? $request->required_service : '';
    $lid->email= $request->input('email') ? $request->input('email') : '';
    $lid->country= $request->input('country') ? $request->input('country') : '';

    date_default_timezone_set("Asia/Kolkata");
    $lid->lead_date = date("F / j / Y, g:i a");
    $lid->save();
    return redirect('https://qamrjob.com/');

  }

  public function taskprocesslist(){
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm/taskprocess',['pageConfigs' => $pageConfigs]);
  }

  public function getRegardingListJson(Request $request){
    $cont = Regarding::all();
    $data['data'] = $cont;
    return response()->json($data);
  }

  public function regardingStore(Request $request){
    $post = new Regarding();
    $post->name = $request->input('name');
    $post->save();
    Session::flash('success','Enquiry for services Added!');
    return redirect()->back();
  }

  public function editRegarding(Request $request){
    $post = Regarding::find($request->input('id'));

    return response()->json($post);

  }

  public function updateRegarding2(Request $request){
    $post = Regarding::find($request->input('id'));
    $post->name = $request->name;
    $post->save();
    Session::flash('success','Enquiry for services updated!');
    return redirect()->back();
  }

  public function taskprocesslist_json(Request $request){
    $cont = TaskProcess::all();
    $data['data'] = $cont;
    return response()->json($data);
  }

  public function editTaskprocess(Request $request){
    extract($_POST);
    $cont = TaskProcess::find($id);

    return response()->json($cont);
  }

  public function updateTaskprocess(Request $request){
    extract($_POST);

    $cont = TaskProcess::find($id);
    $cont->name = $type;
    $cont->save();
    Session::flash('success','Task Process updated successfully');
    return redirect('master/taskprocess/list');
  }

  public function taskProcess(Request $request){
    extract($_POST);
    $cont = new TaskProcess();
    $cont->name = $type; 
    $cont->save();
    Session::flash('success','Task Process created successfully');
    return redirect('master/taskprocess/list');
  }

  public function company_list()
  {
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm/companies', ['pageConfigs' => $pageConfigs]);    
  }


  public function contact_list()
  {
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm/contact-list', ['pageConfigs' => $pageConfigs]);    
  }


  public function company_list_json(Request $request)
  {
    $data_comp = DB::table('qr_company as comp')
    ->leftjoin('qr_company_contact as cont', 'comp.id', '=', 'cont.comp_id')
    ->leftjoin('qr_lifecycle_stage as lf', 'lf.id', '=', 'cont.lifecy_stage')
    ->leftjoin('qr_lead_stage as ld', 'ld.id', '=', 'cont.lead_stage')
    ->leftjoin('qr_city as city', 'comp.city', '=', 'city.city_id')
    ->leftjoin('qr_country as co', 'comp.country', '=', 'co.country_id')
    ->leftjoin('qr_staff_tbl as st', 'st.staff_id', '=', 'cont.staff')
    ->leftjoin('qr_industry as ind', 'ind.id', '=', 'comp.id')
    ->select('comp.*','cont.lifecy_stage','ld.*','lf.*','cont.lead_stage','cont.id as cont_id','st.staff_fname','st.staff_lname','comp.created_at as cre_date','comp.updated_at as up_date','comp.id as cid','city.*','co.*','ind.name as indname')
    ->orderBy('comp.id','DESC')
    ->where('comp.id','!=', '0')
    ->get();

    // $data = DB::table("qr_company as comp")
    // ->leftjoin('qr_company_contact as cont', 'comp.id', '=', 'cont.comp_id')
    // ->leftjoin('qr_lifecycle_stage as lf', 'lf.id', '=', 'cont.lifecy_stage')

    // ->join(DB::raw("(SELECT 
    //     *
    //   FROM `qr_lifecycle_stage`
    // ) as qr_st"),function($join){
    //   $join->on("lf.id","=","cont.lifecy_stage");

    // })
    // ->select("cont.*","lf.name as name","qr_st.*")
    // ->get();



    //    $data_comp = DB::table('qr_company as comp')

    //  ->leftjoin('qr_city as city', 'comp.city', '=', 'city.city_id')
    //  ->leftjoin('qr_country as co', 'comp.country', '=', 'co.country_id')
    //   ->leftjoin('qr_company_contact as cont', 'comp.id', '=', 'cont.comp_id')
    //  ->leftjoin('qr_lifecycle_stage as lf', 'lf.id', '=', 'cont.lifecy_stage')
    
    //  ->leftjoin('qr_industry as ind', 'ind.id', '=', 'comp.id')
    // ->join(DB::raw("(SELECT 
    //       qr_lifecycle_stage.name
    //       FROM qr_lifecycle_stage
    //       ) as lifecy_stage_name"),function($join){
    //         $join->on("qr_lifecycle_stage.id","=","cont.lifecy_stage");
    //   })

    //  ->select('comp.*','comp.created_at as cre_date','comp.updated_at as up_date','comp.id as cid','city.*','co.*','ind.name as indname')

    //  ->orderBy('comp.id','DESC')
    //  ->where('comp.id','!=', '0')
    //  ->get();

    $data['data'] = $data_comp;
    return response()->json($data);
  }

  public function contact_list_json(Request $request)
  {
  $data_comp = DB::table('qr_company_contact as cont')

  ->leftjoin('qr_city as city', 'cont.city2', '=', 'city.city_id')
  ->leftjoin('qr_company as comp', 'comp.id', '=', 'cont.comp_id')
  ->leftjoin('qr_lifecycle_stage as cy', 'cy.id', '=', 'cont.lifecy_stage')
  ->leftjoin('qr_lead_stage as ld', 'ld.id', '=', 'cont.lead_stage')
  ->leftjoin('qr_country as country', 'country.country_id', '=', 'comp.country')
  ->leftjoin('qr_staff_tbl as st', 'st.staff_id', '=', 'cont.staff')
  ->leftjoin('qr_industry as ind', 'ind.id', '=', 'comp.industry')
  ->select('cont.*','comp.*','cy.name as lifecy_stage','ld.name as lead_stage','comp.id as cid','cont.id as cont_id','city.*','st.staff_fname as stf_name','st.staff_lname as stl_name','ind.name as indname','country.country_name','cont.created_at as cont_created','cont.updated_at as cont_updated')
  ->orderBy('cont.id','DESC')
  ->where('cont.id','!=', '0')
  ->get();

  $data['data'] = $data_comp;
  return response()->json($data);
  }

  public function allctransferGrp(Request $request){
    if ($request->bulk2 == 2) {
      $idsv = explode(',', $request->idsv2);

      foreach ($idsv as $key => $ids) {
        // update assignee
        $allcontact = AllContact::find($ids);
        $allcontact->group_id = $request->group_id;
        $allcontact->save();
      }

      // Get user details
      // $cuser = User::where('user_id','=',$request->careoffID)->first();

      Session::flash('success','Group transfer successfully!');
      return redirect()->back();

    }
  }

  public function allctransfer(Request $request)
  {

    // dd($request);

    if ($request->bulk == 2) {
      $idsv = explode(',', $request->idsv);

      foreach ($idsv as $key => $ids) {
        // update assignee
        $allcontact = AllContact::find($ids);
        $allcontact->assign_id = $request->careoffID;
        $allcontact->save();
      }

      // Get user details
      $cuser = User::where('user_id','=',$request->careoffID)->first();

      Session::flash('success','Data transfer successfully to '.$cuser->name);
      return redirect()->back();

    }


  }

  public function transferPartyleadowner(Request $request){
    if($request->bulk == 1){
      $post = Party::where('pty_id','=',$request->pty_id)->first();
      $post->care_of_id = $request->care_off_id;
      $post->save();

      // User Details
      $user = User::where('user_id','=',$request->care_off_id)->first();
      $puser = User::where('user_id','=',$request->leadownerID)->first();

      if(isset($puser)){
        $pusername = $puser->name;
        $puserID = $puser->user_id;
      }else{
        $pusername = "None";
        $puserID = "200";
      }

      // Party Activities
      $pact = new PartyActivitiesList();
      $pact->pty_id = $request->pty_id;
      $pact->user_id = Auth::user()->user_id;
      $pact->specific_id = '1';
      $pact->message = "Transfer Lead Owner from ".$pusername.' to '.$user->name;
      $pact->save();

      // Maintain Careoff Status
      $pcarst = new Ptyleadonwerpre();
      $pcarst->pty_id = $request->pty_id;
      $pcarst->pleadowner_id = $puserID;
      $pcarst->cleadowner_id = $request->care_off_id;
      $pcarst->user_id = Auth::user()->user_id;
      $pcarst->message = "Lead Owner change from ".$pusername.' to '.$user->name;
      $pcarst->save();

      $data = "Lead Owner transfer successfully!";
      return response()->json($data);

    }
  }


  public function transferPartycareoff(Request $request){

    if($request->bulk == 1){
      $post = Party::where('pty_id','=',$request->pty_id)->first();
      $post->lead_owner_id = $request->care_off_id;
      $post->save();

      // User Details
      $user = User::where('user_id','=',$request->care_off_id)->first();
      $puser = User::where('user_id','=',$request->careoffid)->first();

      if(isset($puser)){
        $pusername = $puser->name;
        $puserID = $puser->user_id;
      }else{
        $pusername = "None";
        $puserID = "200";
      }

      // Party Activities
      $pact = new PartyActivitiesList();
      $pact->pty_id = $request->pty_id;
      $pact->user_id = Auth::user()->user_id;
      $pact->specific_id = '1';
      $pact->message = "Transfer careoff from ".$pusername.' to '.$user->name;
      $pact->save();

      // Maintain Careoff Status
      $pcarst = new Ptycareoffst();
      $pcarst->pty_id = $request->pty_id;
      $pcarst->pcareoff_id = $puserID;
      $pcarst->ccareoff_id = $request->care_off_id;
      $pcarst->user_id = Auth::user()->user_id;
      $pcarst->message = "Careoff change from ".$pusername.' to '.$user->name;
      $pcarst->save();

      $data = "Careoff transfer successfully!";
      return response()->json($data);

    }

    if($request->bulk == 2){
      $idsv = explode(',', $request->idsv);
      $precar = explode(',',$request->precar);
      // User Details
      $cuser = User::where('user_id','=',$request->careoffID)->first();
      foreach($idsv as $key => $ids){
        // update careoff
        $party = Party::where('pty_id','=',$ids)->first();
        
        
        // Previous Careoff
        $puser = User::where('user_id','=',$party->lead_owner_id)->first();

        if(isset($puser)){
          $precareoff = $puser->name;
          $precareoffID =  $party->lead_owner_id;
        }else{
          $precareoff = "None";
          $precareoffID = "200";
        }

        // Party Activites
        $pact = new PartyActivitiesList();
        $pact->pty_id = $party->pty_id;
        $pact->user_id = Auth::user()->user_id;
        $pact->specific_id = '2';
        $pact->message = "Transfer careoff from ".$precareoff.' to '.$cuser->name;
        $pact->save();


        // Maintain Careoff Status
        $pcarst = new Ptycareoffst();
        $pcarst->pty_id =  $party->pty_id;
        $pcarst->pcareoff_id = $precareoffID;
        $pcarst->ccareoff_id = $request->careoffID;
        $pcarst->user_id = Auth::user()->user_id;
        $pcarst->message = "Careoff change from ".$precareoff.' to '.$cuser->name;
        $pcarst->save();

        $party->lead_owner_id = $request->careoffID;
        $party->save();
      }
      $data = "Careoff transfer successfully!";
      return response()->json($data);
      // Session::flash('success','Careoff transfer successfull!');
      // return redirect()->back();
    }

  }

  public function singlecontcamp(Request $request){
    
    if($request->wTemp == 'template'){
      // Get File and message only
      $getTemp = WhatsappTemplate::find($request->temp_id);
      $msgBody = $request->texMesg;
      $msgFile = $getTemp->file;

    }else{
      if ($request->hasFile('file')) {
        $file = $request->file('file');
        $file_count = File::files(base_path().'/public/image/whatsapp');
        $filecount = 0;
        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        // $file_exe = $file->getClientOriginalExtension();
        // $name = $filecount . '.' . $file_exe;
        $name = $file->getClientOriginalName();
        $file->move(base_path().'/public/image/whatsapp', $name);
        $msgFile = $name;
      }else{
        $msgFile = '';
      }
      $msgBody = $request->msgBody;

    }

    if($request->specific_id == 1){
      // Contact Campaign List created
      $post = new ContactCampaignList();
      $post->cont_id = $request->cont_id;
      $post->msg_type = $request->message_type;
      $post->temp_type = $request->wTemp;
      if ($request->wTemp == 'template') {
        $post->temp_id = $request->temp_id;
      }
      $post->file = $msgFile;
      $post->msgText = $msgBody;
      if($request->message_type == 'Mail'){
        $post->mailsub = $request->mailsub;
      }
      $post->user_id = Auth::user()->user_id;
      $post->api_id = $request->api_id;
      $post->save();

      // Get Details of All Contact and User
      $partyD = AllContact::where('id','=',$request->cont_id)->first();
      
      // Get API Details
      $getAPI = Userwhatsappapi::where('id','=',$request->api_id)->first();

      // Create message activites
      $pact = new ContactMessageActivity();
      $pact->cont_id = $request->cont_id;
      $pact->specific_id = $request->specific_id;
      $pact->campaign_id = $post->id;
      $pact->user_id = Auth::user()->user_id;
      $pact->subject = Auth::user()->name." send ".$request->message_type." to ".$partyD->full_name." on ".date('d-m-Y',strtotime($post->created_at));    
      $pact->save();

      // Create JOB
      // SinglePartyCampaignList::dispatch($post,$partyD,$getAPI)->onQueue('default');
      
      // Send Direct Message
      if($request->message_type == 'Whatsapp'){
        $ins = $getAPI->instance_key;
        $api = $getAPI->api_key;
        $txt_url = $getAPI->text_message_url;

        $whmsg = "Dear ".$partyD->full_name."\n\n".$post->msgText;

        if($post->file != ''){
          // $url_path = 'http://crm.qamrintl.com';
          // $url_path =  "http://crm.qamr.in";
          // $url_path = url('/');
          // $file_path2 = 'image/whatsapp';
          // $media = $url_path.'/'.$file_path2.'/'.$post->file;

          $media = url('/image/whatsapp/'.$post->file);

          if($partyD->mobile_no != ''){
              $url = $txt_url."?number=".$partyD->mobile_no."&type=media&message=".urlencode($whmsg)."&media_url=".$media."&filename=".$post->file."&instance_id=".$ins."&access_token=".$api;
          
              $ch = curl_init();
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
              curl_setopt($ch,CURLOPT_URL,$url);
              $result1 =  curl_exec($ch);
              
              echo $result1;
              curl_close($ch);
          }

          if($partyD->primary_contact_no != ''){
              $url2 = $txt_url."?number=".$partyD->primary_contact_no."&type=media&message=".urlencode($whmsg)."&media_url=".$media."&filename=".$post->file."&instance_id=".$ins."&access_token=".$api;
          
              $ch = curl_init();
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
              curl_setopt($ch,CURLOPT_URL,$url2);
              $result2 =  curl_exec($ch);
              
              echo $result2;
              curl_close($ch);
          }

        }else{

            if($partyD->mobile_no != ''){
                $url = $txt_url."?number=".$partyD->mobile_no."&type=text&message=".urlencode($whmsg)."&instance_id=".$ins."&access_token=".$api;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result1 = curl_exec($ch);
                echo $result1;
                curl_close($ch);
            }

            if($partyD->primary_contact_no != ''){
                $url = $txt_url."?number=".$partyD->primary_contact_no."&type=text&message=".urlencode($whmsg)."&instance_id=".$ins."&access_token=".$api;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result2 = curl_exec($ch);
                echo $result2;
                curl_close($ch);
            }

        }

        if($result1 != false || $result2 != false){
          $data = "Messege send!";
        }else{
          $data = "Message not send!";
        }

        // $data = $result2;
        return response()->json($data);
        // if($result1){
        //   $data = "Message job created!";
        //   return response()->json($data);
        // }

        // Session::flash('success','Message Send!');
        // return redirect()->back();

      }

      if($request->message_type == 'Mail'){
        if($partyD->email != ''){
          Mail::to($partyD->email)->send(new SingleContactCampaignMail($post,$partyD));
          
          if(count(Mail::failures()) > 0){
            $data = "Mail send successfully!";
            return response()->json($data);
          }else{
            $data = "Mail send successfully!";
            return response()->json($data);
          }


          
          // Session::flash('success','Mail send successfully!');
          // return redirect()->back();

        }else{
          $data = "Mail not send";
          return response()->json($data);



          // Session::flash('success','Mail not send');
          // return redirect()->back();
        }
      }






    }

  }

  public function singleptycamp(Request $request){

   

    if($request->wTemp == 'template'){
      // Get File and message body
      $getTemp = WhatsappTemplate::find($request->temp_id);
      // $msgBody = $getTemp->msgBody;

      
      $msgBody = $request->texMesg;
      $msgFile = $getTemp->file;
    }else{
      if ($request->hasFile('file')) {
        $file = $request->file('file');
        $file_count = File::files(base_path().'/public/image/whatsapp');
        $filecount = 0;
        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        // $file_exe = $file->getClientOriginalExtension();
        // $name = $filecount . '.' . $file_exe;
        $name = $file->getClientOriginalName();

        // remove space from image
        $filename_ren = pathinfo($name,PATHINFO_FILENAME);
        $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
        $repspfilename = str_replace(" ","_",$filename_ren);
        $new_file = $repspfilename.'.'.$fileext_ren;

        $file->move(base_path().'/public/image/whatsapp', $new_file);

        // $file->move(base_path().'/public/image/whatsapp', $name);
        $msgFile = $new_file;
      }else{
        $msgFile = '';
      }
      $msgBody = $request->msgBody;
    }


    $getptydet = Party::where('pty_id','=',$request->pty_id)->first();

    $rem_str = ["[Party Name]","[Agency Name]","[City]","[State]","[ID]","[Email]","[Primary Contact]","[Secondary Contact]","[DOB]","[Wakala Card]","[Membership]"];
    $rep_str = [$getptydet->pty_full_name,$getptydet->pty_ag_name,$getptydet->city,$getptydet->state,$getptydet->pty_id,$getptydet->pty_email,$getptydet->pty_comp_contact,$getptydet->pty_contact_no,"",$getptydet->member_id];

    $fmsgBody = str_replace($rem_str,$rep_str,$msgBody);

    // dd($msgFile);

    if($request->specific_id == 1){
      // Party Campaign List created
      $post = new PartyCampaignList();
      $post->pty_id = $request->pty_id;
      $post->msg_type = $request->message_type;
      $post->temp_type = $request->wTemp;
      if ($request->wTemp == 'template') {
        $post->temp_id = $request->temp_id;
      }
      $post->file = $msgFile;
      $post->msgText = $fmsgBody;
      if($request->message_type == 'Mail'){
        $post->mailsub = $request->mailsub;
      }
      $post->user_id = Auth::user()->user_id;
      $post->api_id = $request->api_id;
      $post->save();


      // Get Details of Party and User
      $partyD = Party::where('pty_id','=',$request->pty_id)->first();
      
      // Get API Details
      $getAPI = Userwhatsappapi::where('id','=',$request->api_id)->first();

      // Create message activites
      $pact = new PartyMessageActivity();
      $pact->pty_id = $request->pty_id;
      $pact->specific_id = $request->specific_id;
      $pact->campaign_id = $post->id;
      $pact->user_id = Auth::user()->user_id;
      $pact->subject = Auth::user()->name." send ".$request->message_type." to ".$partyD->pty_ag_name." on ".date('d-m-Y',strtotime($post->created_at));    
      $pact->save();


      // Create JOB
      // SinglePartyCampaignList::dispatch($post,$partyD,$getAPI)->onQueue('default');
      
      // Send Direct Message
      if($post->msg_type == 'Whatsapp'){
        $ins = $getAPI->instance_key;
        $api = $getAPI->api_key;
        $txt_url = $getAPI->text_message_url;

        // $whmsg = "Dear ".$partyD->pty_full_name."\n".$partyD->pty_ag_name."\n\n".$post->msgText;
        $whmsg = $post->msgText;
        if($post->file != ''){
          
          // $url_path = 'http://crm.qamrintl.com';
          // $url_path =  "http://crm.qamr.in";
          $url_path = url('/');
          $file_path2 = 'image/whatsapp';
          // $media = $url_path.'/'.$file_path2.'/'.$post->file;
          $media = url('/image/whatsapp/'.$msgFile);


          if($partyD->pty_comp_contact != ''){
              $url = $txt_url."?number=".$partyD->pty_comp_contact."&type=media&message=".urlencode($whmsg)."&media_url=".$media."&filename=".$msgFile."&instance_id=".$ins."&access_token=".$api;
          
              $ch = curl_init();
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
              curl_setopt($ch,CURLOPT_URL,$url);
              $result1 =  curl_exec($ch);
              
              echo $result1;
              curl_close($ch);
          }

          if($partyD->pty_contact_no != ''){
              $url2 = $txt_url."?number=".$partyD->pty_contact_no."&type=media&message=".urlencode($whmsg)."&media_url=".$media."&filename=".$msgFile."&instance_id=".$ins."&access_token=".$api;
          
              $ch = curl_init();
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
              curl_setopt($ch,CURLOPT_URL,$url2);
              $result2 =  curl_exec($ch);
              
              echo $result2;
              curl_close($ch);
          }

          if($partyD->p_mobile != ''){
            $url2 = $txt_url."?number=".$partyD->p_mobile."&type=media&message=".urlencode($whmsg)."&media_url=".$media."&filename=".$msgFile."&instance_id=".$ins."&access_token=".$api;
        
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_URL,$url2);
            $result2 =  curl_exec($ch);
            
            echo $result2;
            curl_close($ch);
        }

        }else{

          if($partyD->pty_comp_contact != ''){
              $url = $txt_url."?number=".$partyD->pty_comp_contact."&type=text&message=".urlencode($whmsg)."&instance_id=".$ins."&access_token=".$api;
              $ch = curl_init();
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
              curl_setopt($ch,CURLOPT_URL,$url);
              $result1 = curl_exec($ch);
              echo $result1;
              curl_close($ch);
          }

          if($partyD->pty_contact_no != ''){
              $url = $txt_url."?number=".$partyD->pty_contact_no."&type=text&message=".urlencode($whmsg)."&instance_id=".$ins."&access_token=".$api;
              $ch = curl_init();
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
              curl_setopt($ch,CURLOPT_URL,$url);
              $result2 = curl_exec($ch);
              echo $result2;
              curl_close($ch);
          }

          if($partyD->p_mobile != ''){
            $url = $txt_url."?number=".$partyD->p_mobile."&type=text&message=".urlencode($whmsg)."&instance_id=".$ins."&access_token=".$api;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_URL,$url);
            $result2 = curl_exec($ch);
            echo $result2;
            curl_close($ch);
          }

        }

        if($result1 != false || $result2 != false){
          $data = "Messege send!";
        }else{
          $data = "Message not send!";
        }

        // $data = $result1;

        return response()->json($data);

        // Session::flash('success','Message Send!');
        // return redirect()->back();

      }

      if($request->message_type == 'Mail'){
        if($partyD->pty_email != ''){
          Mail::to($partyD->pty_email)->send(new SinglePartyCampaignMail($post,$partyD));

          if(count(Mail::failures()) > 0){
            $data = "Mail not send";
          }else{
            $data = "Mail sent successfully!";
          }

          return response()->json($data);

          // Session::flash('success','Mail send successfully!');
          // return redirect()->back();

        }else{
          // $data = "Mail not send";
          // return response()->json($data);

          Session::flash('success','Mail not send');
          return redirect()->back();

        }
      } 



    }

    

  }

  // Contacts List Start
  public function contact_lists(){
    

    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm/contact-lists', ['pageConfigs' => $pageConfigs]);    
  }

  public function findReg2(Request $request){
    $id = $request->id;

    $post = DB::table('qr_contact_regarding_tbl as cr')
      ->leftjoin('qr_regarding_c_tbl as rc','rc.id','=','cr.regarding_id')
      ->select('rc.name')
      ->where('cr.cont_id',$request->cont_id)
      ->get();

      // $post = DB::table('qr_all_contact as allc')
      //   ->leftjoin('qr_contact_regarding_tbl as crt','crt.cont_id','=','allc.id')
      //   ->leftjoin('qr_regarding_c_tbl as rct','rct.id','=','crt.regarding_id')
      //   ->select('rct.name')
      //   ->where('crt.cont_id','=',$request->cont_id)
      //   ->get();


    return response()->json($post);
  }

  public function wtemplatel(){
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm/whatsapp-template',['pageConfigs' => $pageConfigs]);
  }

  public function getRegardingList(){
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm-master/regardingList',['pageConfigs' => $pageConfigs]);
  }

  public function deleteRegarding2(Request $request){
    $id = $request->reg_id;
    $post = Regarding::find($id);
    $post->delete();
    Session::flash('success','Enquiry for service deleted!');
    return redirect()->back();
  }

  public function wtemplatel_json(Request $request){

    $perms = AccessPermissionModule2::where('user_id','=',Auth::user()->user_id)->first();

    if(Auth::user()->user_type == 1 || (isset($perms) && $perms->full_access == 1)){
      $data_det = DB::table('whatsapp_templates as whats')
        ->leftjoin('users as user','user.user_id','=','whats.user_id')
        ->select('whats.*','user.name as uname')
        ->orderBy('id','DESC')
        ->get();
    }elseif(Auth::user()->user_type == 2 || (isset($perms) && $perms->full_access == 0)){

      if($perms->whtemplater == 1){
        $data_det = DB::table('whatsapp_templates as whats')
        ->leftjoin('users as user','user.user_id','=','whats.user_id')
        ->select('whats.*','user.name as uname')
        ->orderBy('id','DESC')
        ->get();
      }else{
        $data_det = DB::table('whatsapp_templates as whats')
        ->leftjoin('users as user','user.user_id','=','whats.user_id')
        ->select('whats.*','user.name as uname')
        ->where('whats.user_id','=',Auth::user()->user_id)
        ->orWhere('whats.careoff_id','=',Auth::user()->user_id)
        ->orWhere('whats.public_st','=',1)
        ->orderBy('id','DESC')
        ->get();
      }


    }

    $data['data'] = $data_det;
    return response()->json($data);
  }

  public function campaignStop(Request $request){
    $post = Campaignlist::find($request->campaign_id);
    $post->stop = '1';
    $post->save();
    Session::flash('success','Campaign Stop!');
    return redirect()->back();
  }

  public function campaignStart(Request $request){
    $post = Campaignlist::find($request->campaign_id);
    $post->stop = '0';
    $post->save();
    Session::flash('success','Campaign Start!');
    return redirect()->back();
  }

  public function campaignWhatsappjson(Request $request){

    $perms = AccessPermissionModule2::where('user_id','=',Auth::user()->user_id)->first();

    if(Auth::user()->user_type == 1 || (isset($perms) && $perms->full_access == 1)){
      $data_det = DB::table('campaignlists as whats')
        ->leftjoin('users as user','user.user_id','=','whats.user_id')
        ->leftjoin('groupms as groupm','groupm.id','=','whats.group_id')
        ->select('whats.*','user.name as uname','groupm.name as gpname')
        ->orderBy('id','DESC')
        ->get();
    }elseif(Auth::user()->user_type == 2 || (isset($perms) && $perms->full_access == 0)){
      
      if($perms->whcampaignmr == 1){
        $data_det = DB::table('campaignlists as whats')
        ->leftjoin('users as user','user.user_id','=','whats.user_id')
        ->leftjoin('groupms as groupm','groupm.id','=','whats.group_id')
        ->select('whats.*','user.name as uname','groupm.name as gpname')
        ->orderBy('id','DESC')
        ->get();
      }else{
        $data_det = DB::table('campaignlists as whats')
        ->leftjoin('users as user','user.user_id','=','whats.user_id')
        ->leftjoin('groupms as groupm','groupm.id','=','whats.group_id')
        ->select('whats.*','user.name as uname','groupm.name as gpname')
        ->where('whats.user_id','=',Auth::user()->user_id)
        ->orderBy('id','DESC')
        ->get();
      }    
    }

    $data['data'] = $data_det;
    return response()->json($data);
  }

  public function campaignWhatsappstore2(Request $request){
    // Get Request Details
    $campaign_name = $request->campaign_name;
    $module_name = $request->module_name;

    $api_id = $request->input('api_id');
    $temp_id = $request->temp_id;

    $wtemp = $request->wTemp;
    $userID = $request->input('user_id');

    // Get API Details
    $getAPI = Userwhatsappapi::find($api_id);

    if($module_name == 'contact'){

      $lead_type = $request->lead_type;
      $lcs_id = $request->lcs_id;

      if($wtemp == 'template'){
        // get Template Details
        $getTemp = WhatsappTemplate::find($request->temp_id);
        $post = new Campaignlist();
        $post->name = $campaign_name;
        $post->module_name = $module_name;
        $post->user_id = Auth::user()->user_id;
        $post->temp_id = $temp_id;
        $post->whatsappi_id = $api_id;
        $post->msgBody = $getTemp->msgBody;
        $post->file = $getTemp->file;
        $post->save();

      }else{

        if ($request->hasFile('file')) {
          $file = $request->file('file');
          $file_count = File::files(base_path().'/public/image/whatsapp');
          $filecount = 0;
      
          if ($file_count !== false) {
            $filecount = count($file_count);
          }
          $file_exe = $file->getClientOriginalExtension();
          $name = $filecount . '.' . $file_exe;
      
          $file->move(base_path().'/public/image/whatsapp', $name);
          $cand_file = $name;
      
          // API Details
        } else {
          $cand_file = '';
        }

        $post = new Campaignlist();
        $post->name = $campaign_name;
        $post->module_name = $module_name;
        $post->user_id = Auth::user()->user_id;
        $post->whatsappi_id = $api_id;
        $post->msgBody = $request->msgBody;
        $post->file = $cand_file;
        $post->save();
      }

      if($lead_type != '' && $lcs_id != ''){
        $cont_dets = AllContact::where('lead_type','=',$lead_type)->where('lcs_id','=',$lcs_id)->where('user_id','=',$getAPI->staff_id)->get();
      }else if($lead_type != '' && $lcs_id == ''){
        $cont_dets = AllContact::where('lead_type','=',$lead_type)->where('user_id','=',$getAPI->staff_id)->get();
      }else if($lcs_id != '' && $lead_type == ''){
        $cont_dets = AllContact::where('lcs_id','=',$lcs_id)->where('user_id','=',$getAPI->staff_id)->get();
      }else{
        $cont_dets = AllContact::where('user_id','=',$getAPI->staff_id)->get();
      }

      // $cont_dets = AllContact::all();
      

      $total_rec = count($cont_dets);

      $get_last_id = $post->id;
      $get_camp_list = Campaignlist::find($get_last_id);

      $cdCount = 1;
    
      foreach($cont_dets as $cont_det){

        if($cdCount % 2 == 0){
          dispatch(new WhatsappSendJob($cont_det,$get_camp_list,$getAPI))->onQueue('mediumps');
        }else{
          dispatch(new WhatsappSendJob($cont_det,$get_camp_list,$getAPI))->onQueue('default'); 
        }

        $cdCount++;
      }

      $data = "Total ".$total_rec." message send";
      return response()->json($data);

    }elseif($module_name == 'party'){
      if($wtemp == 'template'){
        // get Template Details
        $getTemp = WhatsappTemplate::find($request->temp_id);
        $post = new Campaignlist();
        $post->name = $campaign_name;
        $post->module_name = $module_name;
        $post->user_id = Auth::user()->user_id;
        $post->temp_id = $temp_id;
        $post->whatsappi_id = $api_id;
        $post->msgBody = $getTemp->msgBody;
        $post->file = $getTemp->file;
        $post->save();

      }else{

        if ($request->hasFile('file')) {
          $file = $request->file('file');
          $file_count = File::files(base_path().'/public/image/whatsapp');
          $filecount = 0;
      
          if ($file_count !== false) {
            $filecount = count($file_count);
          }
          $file_exe = $file->getClientOriginalExtension();
          $name = $filecount . '.' . $file_exe;
      
          $file->move(base_path().'/public/image/whatsapp', $name);
          $cand_file = $name;
      
          // API Details
        } else {
          $cand_file = '';
        }

        $post = new Campaignlist();
        $post->name = $campaign_name;
        $post->module_name = $module_name;
        $post->user_id = Auth::user()->user_id;
        $post->whatsappi_id = $api_id;
        $post->msgBody = $request->msgBody;
        $post->file = $cand_file;
        $post->save();
      }

      // Get all party data
      $parties = Party::where('act_status','=','0')->where('pty_id','!=','200')->get();
      $total_rec = count($parties);

      $get_last_id = $post->id;
      $get_camp_list = Campaignlist::find($get_last_id);

      foreach($parties as $party){
        dispatch(new PartySendJob($party,$get_camp_list,$getAPI))->onQueue('default');
      }

      $data = "Total ".$total_rec." message send";
      return response()->json($data);
      
    }elseif($module_name == 'staff'){
      if($wtemp == 'template'){
        // get Template Details
        $getTemp = WhatsappTemplate::find($request->temp_id);
        $post = new Campaignlist();
        $post->name = $campaign_name;
        $post->module_name = $module_name;
        $post->user_id = Auth::user()->user_id;
        $post->temp_id = $temp_id;
        $post->whatsappi_id = $api_id;
        $post->msgBody = $getTemp->msgBody;
        $post->file = $getTemp->file;
        $post->save();

      }else{

        if ($request->hasFile('file')) {
          $file = $request->file('file');
          $file_count = File::files(base_path().'/public/image/whatsapp');
          $filecount = 0;
      
          if ($file_count !== false) {
            $filecount = count($file_count);
          }
          $file_exe = $file->getClientOriginalExtension();
          $name = $filecount . '.' . $file_exe;
      
          $file->move(base_path().'/public/image/whatsapp', $name);
          $cand_file = $name;
      
          // API Details
        } else {
          $cand_file = '';
        }

        $post = new Campaignlist();
        $post->name = $campaign_name;
        $post->module_name = $module_name;
        $post->user_id = Auth::user()->user_id;
        $post->whatsappi_id = $api_id;
        $post->msgBody = $request->msgBody;
        $post->file = $cand_file;
        $post->save();
      }

      // Get all staff data
      $staffs = Staff::where('staff_status','!=','0')->where('staff_id','!=','200')->get();
      $total_rec = count($staffs);

      $get_last_id = $post->id;
      $get_camp_list = Campaignlist::find($get_last_id);

      foreach($staffs as $staff){
        dispatch(new StaffSendJob($staff,$get_camp_list,$getAPI))->onQueue('default');
      }

      $data = "Total ".$total_rec." message send";
      return response()->json($data);

    }else{
      
      $data = "Total None message send";
      return response()->json($data);
    }

    
  } 

  public function campaignWhatsappstore(Request $request){
    // Get Request Details
    $module_name = $request->module_name;
    $lead_type = $request->lead_type;
    $lcs_id = $request->lcs_id;
    $api_id = $request->input('api_id');
    // Find API Details
    $api_det = Userwhatsappapi::find($api_id);

    ini_set('max_execution_time', 1200);

    // Message Data
    $ins = $api_det->instance_key;
    $api = $api_det->api_key;
    $url1 = $api_det->text_message_url;
    $url2 = $api_det->media_message_url;
    $urlPath = '/';
    $filePath = 'image/crm-contact/files';

    if($module_name == 'contact'){

      if($lead_type != '' && $lcs_id != ''){
        $cont_dets = AllContact::where('lead_type','=',$lead_type)->where('lcs_id','=',$lcs_id)->get();
      }else if($lead_type != ''){
        $cont_dets = AllContact::where('lead_type','=',$lead_type)->get();
      }else if($lcs_id != ''){
        $cont_dets = AllContact::where('lcs_id','=',$lcs_id)->get();
      }

      $total_rec = count($cont_dets);

      // Send Whatsapp Based on Template
      $wtemp = $request->wTemp;

      if($wtemp == 'template'){
        // get Template Details
        $getTemp = WhatsappTemplate::find($request->temp_id);

        // Get template details
        $getTempmsg = $getTemp->msgBody;
        $getTempfile = $getTemp->file;

        // Send Whatsapp Message
        foreach($cont_dets as $cont_det){
          // Get Contact Details
          $getContId = $cont_det->id;
          $getCname = $cont_det->full_name;
          $getCphone1 = $cont_det->mobile_no;
          $getCphone2 = $cont_det->phone0;
          $getCphone3 = $cont_det->phone1;
          $getCphone4 = $cont_det->phone2;
          $getCwhts = $cont_det->primary_contact_no;

          // Send Message based on Media File is blank or not
          $msgWhs = "Dear ".$getCname."\n".$getTempmsg;
          if($getTempfile != ''){
            $media = $urlPath.'/'.$filePath.'/'.$getTempfile;
            // Send Message on First Number
            if($getCphone1 !=''){
              $data = [
                'number' => $getCphone1,
                'msg' => $msgWhs,
                'media' => $media,
                "type" => "image",
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
              curl_setopt($ch, CURLOPT_URL, $url2);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }
            // Send Message on Second Number
            if($getCphone2 !=''){
              $data2 = [
                'number' => $getCphone2,
                'msg' => $msgWhs,
                'media' => $media,
                "type" => "image",
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
              curl_setopt($ch, CURLOPT_URL, $url2);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }
            // Send Message on Third Number
            if($getCphone3 !=''){
              $data3 = [
                'number' => $getCphone3,
                'msg' => $msgWhs,
                'media' => $media,
                "type" => "image",
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data3));
              curl_setopt($ch, CURLOPT_URL, $url2);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }
            // Send Message on First Number
            if($getCphone4 !=''){
              $data4 = [
                'number' => $getCphone4,
                'msg' => $msgWhs,
                'media' => $media,
                "type" => "image",
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data4));
              curl_setopt($ch, CURLOPT_URL, $url2);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }
            // Send Message on First Number
            if($getCwhts !=''){
              $data5 = [
                'number' => $getCwhts,
                'msg' => $msgWhs,
                'media' => $media,
                "type" => "image",
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data5));
              curl_setopt($ch, CURLOPT_URL, $url2);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }

            // Insert in Whatsapp Send Details
            // $post = new WhatsappSendDetails();
            // $post->module_name = $request->module_name;
            // $post->user_id = Auth::user()->user_id;
            // $post->contact_id = $getContId;
            // $post->whatsappi_id = $request->api_id;
            // $post->whatsapptemp_id = $request->temp_id;
            // $post->msgBody = $getTempmsg;
            // $post->file = $getTempfile;
            // $post->save();

          }else{
            // Send Message on First Number
            if($getCphone1 !=''){
              $data = [
                'number' => $getCphone1,
                'msg' => $msgWhs,
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
              curl_setopt($ch, CURLOPT_URL, $url1);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }
            // Send Message on Second Number
            if($getCphone2 !=''){
              $data2 = [
                'number' => $getCphone2,
                'msg' => $msgWhs,
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
              curl_setopt($ch, CURLOPT_URL, $url1);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }
            // Send Message on Third Number
            if($getCphone3 !=''){
              $data3 = [
                'number' => $getCphone3,
                'msg' => $msgWhs,
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data3));
              curl_setopt($ch, CURLOPT_URL, $url1);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }
            // Send Message on First Number
            if($getCphone4 !=''){
              $data4 = [
                'number' => $getCphone4,
                'msg' => $msgWhs,
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data4));
              curl_setopt($ch, CURLOPT_URL, $url1);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }
            // Send Message on First Number
            if($getCwhts !=''){
              $data5 = [
                'number' => $getCwhts,
                'msg' => $msgWhs,
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data5));
              curl_setopt($ch, CURLOPT_URL, $url1);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }

            // Insert in Whatsapp Send Details
            // $post = new WhatsappSendDetails();
            // $post->module_name = $request->module_name;
            // $post->user_id = Auth::user()->user_id;
            // $post->contact_id = $getContId;
            // $post->whatsappi_id = $request->api_id;
            // $post->whatsapptemp_id = $request->temp_id;
            // $post->msgBody = $getTempmsg;
            // $post->file = $getTempfile;
            // $post->save();

          }
        }
      }else{

        if ($request->hasFile('file')) {
          $file = $request->file('file');
          $file_count = File::files(base_path().'/public/image/crm-contact/files');
          $filecount = 0;
      
          if ($file_count !== false) {
            $filecount = count($file_count);
          }
          $file_exe = $file->getClientOriginalExtension();
          $name = $filecount . '.' . $file_exe;
      
          $file->move(base_path().'/public/image/crm-contact/files', $name);
          $cand_file = $name;
      
          // API Details
        } else {
          $cand_file = '';
        }  
    
        foreach($cont_dets as $cont_det){
    
          // Get Contact Details
          $getContId = $cont_det->id;
          $getCname = $cont_det->full_name;
          $getCphone1 = $cont_det->mobile_no;
          $getCphone2 = $cont_det->phone0;
          $getCphone3 = $cont_det->phone1;
          $getCphone4 = $cont_det->phone2;
          $getCwhts = $cont_det->primary_contact_no;
    
          // Send Message Data
          $msgWhs = "Dear ".$getCname."\n".$request->msgBody;
          $url_path = url('/');
          $file_path = 'image/crm-contact/files';
    
          if($cand_file !=''){
            // $mediaPath = $url_path.'/'.$file_path.'/'.$cand_file;
            $mediaPath = url('/image/crm-contact/files/'.$cand_file);
            $type = "image";
    
            // Send Message on First Number
            if($getCphone1 !=''){
              $data = [
                'number' => $getCphone1,
                'msg' => $msgWhs,
                'media' => $mediaPath,
                "type" => "image",
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
              curl_setopt($ch, CURLOPT_URL, $url2);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }
            // Send Message on Second Number
            if($getCphone2 !=''){
              $data2 = [
                'number' => $getCphone2,
                'msg' => $msgWhs,
                'media' => $mediaPath,
                "type" => "image",
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
              curl_setopt($ch, CURLOPT_URL, $url2);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }
            // Send Message on Third Number
            if($getCphone3 !=''){
              $data3 = [
                'number' => $getCphone3,
                'msg' => $msgWhs,
                'media' => $mediaPath,
                "type" => "image",
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data3));
              curl_setopt($ch, CURLOPT_URL, $url2);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }
            // Send Message on First Number
            if($getCphone4 !=''){
              $data4 = [
                'number' => $getCphone4,
                'msg' => $msgWhs,
                'media' => $mediaPath,
                "type" => "image",
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data4));
              curl_setopt($ch, CURLOPT_URL, $url2);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }
            // Send Message on First Number
            if($getCwhts !=''){
              $data5 = [
                'number' => $getCwhts,
                'msg' => $msgWhs,
                'media' => $mediaPath,
                "type" => "image",
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data5));
              curl_setopt($ch, CURLOPT_URL, $url2);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }

            // Insert in Whatsapp Send Details
            // $post = new WhatsappSendDetails();
            // $post->module_name = $request->module_name;
            // $post->user_id = Auth::user()->user_id;
            // $post->contact_id = $getContId;
            // $post->whatsappi_id = $request->api_id;
            // $post->msgBody = $request->msgBody;
            // $post->file = $cand_file;
            // $post->save();

          }else{
    
            // Send Message on First Number
            if($getCphone1 !=''){
              $data = [
                'number' => $getCphone1,
                'msg' => $msgWhs,
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
              curl_setopt($ch, CURLOPT_URL, $url1);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }
            // Send Message on Second Number
            if($getCphone2 !=''){
              $data2 = [
                'number' => $getCphone2,
                'msg' => $msgWhs,
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
              curl_setopt($ch, CURLOPT_URL, $url2);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }
            // Send Message on Third Number
            if($getCphone3 !=''){
              $data3 = [
                'number' => $getCphone3,
                'msg' => $msgWhs,
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data3));
              curl_setopt($ch, CURLOPT_URL, $url1);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }
            // Send Message on First Number
            if($getCphone4 !=''){
              $data4 = [
                'number' => $getCphone4,
                'msg' => $msgWhs,
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data4));
              curl_setopt($ch, CURLOPT_URL, $url1);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }
            // Send Message on First Number
            if($getCwhts !=''){
              $data5 = [
                'number' => $getCwhts,
                'msg' => $msgWhs,
                "instance" => $ins,
                "apikey" => $api
              ];
    
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data5));
              curl_setopt($ch, CURLOPT_URL, $url1);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              $result = curl_exec($ch);
              curl_close($ch);
            }

            // Insert in Whatsapp Send Details
            // $post = new WhatsappSendDetails();
            // $post->module_name = $request->module_name;
            // $post->user_id = Auth::user()->user_id;
            // $post->contact_id = $getContId;
            // $post->whatsappi_id = $request->api_id;
            // $post->msgBody = $request->msgBody;
            // $post->file = $cand_file;
            // $post->save();
    
          }
    
        }

      }

    }else{
      // Insert in Whatsapp Send Details
      $post = new WhatsappSendDetails();
      $post->module_name = $request->module_name;
      $post->user_id = Auth::user()->user_id;
      $post->whatsappi_id = $request->api_id;
      $post->msgBody = $request->msgBody;
      $post->save();

      $total_rec = "3";
    }

    $data = "Total ".$total_rec." message send";

    return response()->json($data);
  }

  public function campaignWhatsappstore3(Request $request){
    // WFAPP
    // Get Request Details
    $module_name = $request->module_name;
    $wTemp = $request->wTemp;
    if($wTemp == 'template'){
      // Get File and Message from template
      $getTemp = WhatsappTemplate::find($request->temp_id);
      $msgBody = $getTemp->msgBody;
      $msgFile = $getTemp->file;
    }else{
      if ($request->hasFile('file')) {
        $file = $request->file('file');
        $file_count = File::files(base_path().'/public/image/whatsapp');
        $filecount = 0;
        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        $file_exe = $file->getClientOriginalExtension();
        $name = $filecount . '.' . $file_exe;
    
        $file->move(base_path().'/public/image/whatsapp', $name);
        $msgFile = $name;
      }else{
        $msgFile = '';
      }
      $msgBody = $request->msgBody;
    }

    // Campaogn List Created

    $post = new Campaignlist();
    $post->name = $request->campaign_name;
    $post->module_name = $module_name;
    if($module_name == 'contact'){
      $post->lead_type = $request->lead_type;
      $post->lcs_id = $request->lcs_id;
    }

    $post->whatsappi_id = $request->api_id;
    $post->wTemp = $wTemp;
    
    if($module_name == 'party'){
      $post->religion = $request->religion;
      $post->party_status = $request->party_status;
      $post->party_contact_type = $request->party_contact_type;
    }

    $post->temp_id = $request->temp_id;
    $post->file =  $msgFile;
    $post->msgBody = $msgBody;
    $post->sch_type = $request->sch_type;
    if($request->sch_type == '2'){
      $post->date_and_time = date('Y-m-d H:i:s',strtotime($request->date_and_time));
      $post->camp_date = date('Y-m-d',strtotime($request->date_and_time));
      $post->camp_time = date('H:i',strtotime($request->date_and_time));
    }
    $post->user_id = Auth::user()->user_id;
    $post->save();

    // Get API Details
    $getAPI = Userwhatsappapi::find($request->api_id);

    $lead_type = $request->lead_type;
    $lcs_id = $request->lcs_id;  

    $religion = $request->religion;
    $party_status = $request->party_status;
    $party_contact_type = $request->party_contact_type;


    $get_last_id = $post->id;
    $get_camp_list = Campaignlist::find($get_last_id);

    if($module_name == 'contact'){

      $cont_dets = AllContact::where('user_id','=',$getAPI->staff_id)->where(function($query) use($lead_type,$lcs_id){
        if($lead_type != ''){
          $query->where('lead_type','=',$lead_type);
        }
        if($lcs_id != ''){
          $query->where('lcs_id','=',$lcs_id);
        }
      })->get();

      $cdCount = 1;

      if($request->sch_type == '1'){
        foreach($cont_dets as $cont_det){
          if($cdCount % 2 == 0){
            dispatch(new WhatsappSendJob($cont_det,$get_camp_list,$getAPI))->onQueue('mediumps');
          }else{
            dispatch(new WhatsappSendJob($cont_det,$get_camp_list,$getAPI))->onQueue('default'); 
          }
          $cdCount++;
        }
      }

      $total_rec = count($cont_dets);
      $data = "Total ".$total_rec." message send";

      return response()->json($data);

    }elseif($module_name == 'party'){
      // Get all party data
      $parties = Party::where('pty_id','!=',200)->where(function($query) use($religion,$party_status){
        if($religion != 'All'){
          $query->where('pty_rel','=',$religion);
        }
        if($party_status != 'All'){
          $query->where('act_status','=',$party_status);
        }
      })->get();

      if($request->sch_type == '1'){
        foreach($parties as $party){
          dispatch(new PartySendJob($party,$get_camp_list,$getAPI))->onQueue('default');
        }
      }

      $total_rec = count($parties);

      $data = "Total ".$total_rec." message send";
      return response()->json($data);

    }elseif($module_name == 'staff'){
      // Get all staff data
      $staffs = Staff::where('staff_status','!=','0')->where('staff_id','!=','200')->get();
      $total_rec = count($staffs);

      if($request->sch_type == '1'){
        foreach($staffs as $staff){
          dispatch(new StaffSendJob($staff,$get_camp_list,$getAPI))->onQueue('default');
        }
      }


      $data = "Total ".$total_rec." message send";
      return response()->json($data);

    }else{
      $data = "Total None message send";
      return response()->json($data);
    }

    
  }

  public function campaignWhatsappstore5(Request $request){
    $module_name = $request->module_name;
    $campaign_name = $request->campaign_name;
    $lead_type = $request->lead_type;
    $optinout = $request->optinout;
    $lcs_id = $request->lcs_id;
    $wTemp = $request->wTemp;
    $religion = $request->religion;
    $party_status = $request->party_status;
    $api_id = $request->api_id;
    $group_id = $request->group_id;

    // $post_dum = AllContact::wherein('optinout',$optinout)->get();
    // dd($post_dum);
    // $apiGET = Userwhatsappapi::wherein('id',$api_id)->get();
    // dd($request->msgBody);



    if($module_name == 'staff'){

      if($wTemp == 'custom_temp'){
        if ($request->hasFile('file')) {
          $file = $request->file('file');
          $file_count = File::files(base_path().'/public/image/whatsapp');
          $filecount = 0;
          if ($file_count !== false) {
            $filecount = count($file_count);
          }
          $file_exe = $file->getClientOriginalExtension();
          // $name = $filecount . '.' . $file_exe;
          $name = $file->getClientOriginalName();
          // remove space from image
          $filename_ren = pathinfo($name,PATHINFO_FILENAME);
          $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
          $repspfilename = str_replace(" ","_",$filename_ren);
          $new_file = $repspfilename.'.'.$fileext_ren;
          $file->move(base_path().'/public/image/whatsapp', $new_file);
          $msgFile = $new_file;
        }else{
          $msgFile = '';
        }

        $msgBody = $request->msgBody;

        $totalcontact = [];

        // Created campaign
        $post = new Campaignlist();
        $post->name = $request->campaign_name;
        $post->module_name = $module_name;
        $post->whatsappi_id = implode(",",$request->api_id);
        $post->wTemp = $wTemp;
        // $post->temp_id = $request->temp_id;
        $post->file =  $msgFile;
        $post->msgBody = $msgBody;
        $post->sch_type = $request->sch_type;
        if($request->sch_type == '2'){
          $post->date_and_time = date('Y-m-d H:i:s',strtotime($request->date_and_time));
          $post->camp_date = date('Y-m-d',strtotime($request->date_and_time));
          $post->camp_time = date('H:i',strtotime($request->date_and_time));
        }
        $post->api_id_text = implode(",",$request->api_id);
        $post->user_id = Auth::user()->user_id;
        $post->save();

        $get_camp_list = Campaignlist::find($post->id);

        if($request->sch_type == '1'){
          foreach($request->api_id as $capi_id){
            // Get API details
            $getAPI = Userwhatsappapi::find($capi_id);
    
            // Get all Staff data
            $staffs = Staff::where('staff_status','!=','0')->where('staff_id','!=','200')->get();
    
            foreach($staffs as $staff){
              dispatch(new StaffSendJob($staff,$get_camp_list,$getAPI))->onQueue('default');
            }
    
            // Count Contact
            $totalcontact [] = count($staffs);
      
            $total_rec = array_sum($totalcontact);
          }
        }else{
          $total_rec = "scheduled";
        }

        Session::flash('success','Total '.$total_rec.' message send');
        return redirect()->back();

      }

      if($wTemp == 'template'){
        // Get File and Message from template
        $getTemp = WhatsappTemplate::find($request->temp_id);
        $msgBody = $getTemp->msgBody;
        $msgFile = $getTemp->file;

        $totalcontact = [];

        // Created campaign
        $post = new Campaignlist();
        $post->name = $request->campaign_name;
        $post->module_name = $module_name;
        $post->whatsappi_id = implode(",",$request->api_id);
        $post->wTemp = $wTemp;
        $post->temp_id = $request->temp_id;
        $post->file =  $msgFile;
        $post->msgBody = $msgBody;
        $post->sch_type = $request->sch_type;
        if($request->sch_type == '2'){
          $post->date_and_time = date('Y-m-d H:i:s',strtotime($request->date_and_time));
          $post->camp_date = date('Y-m-d',strtotime($request->date_and_time));
          $post->camp_time = date('H:i',strtotime($request->date_and_time));
        }
        $post->user_id = Auth::user()->user_id;
        $post->api_id_text = implode(",",$request->api_id);
        // $post->temp_id_text = implode(",",$request->temp_id);
        $post->save();

        $get_camp_list = Campaignlist::find($post->id);

        if($request->sch_type == '1'){
          foreach($request->api_id as $capi_id){
            // Get API details
            $getAPI = Userwhatsappapi::find($capi_id);
    
            // Get all party data
            $staffs = Staff::where('staff_status','!=','0')->where('staff_id','!=','200')->get();
    
            foreach($staffs as $staff){
              dispatch(new StaffSendJob($staff,$get_camp_list,$getAPI))->onQueue('default');
            }
    
            // Count Contact
            $totalcontact [] = count($staffs);
    
          }
          $total_rec = array_sum($totalcontact);
        }else{
          $total_rec = "scheduled";
        }



        Session::flash('success','Total '.$total_rec.' message send');
        return redirect()->back();

      }

      if($wTemp == 'multipe_temp'){

        $temp_id2 = $request->temp_id2;

        // dd($request->temp_id2[0]);

        $totalcontact = [];

        // foreach($request->api_id as $capi_id){
        //   // Get API details
        //   $getAPI = Userwhatsappapi::find($capi_id);

        //   // Get all party data
        //   $staffs = Staff::where('staff_status','!=','0')->where('staff_id','!=','200')->get();
        //   $totalcontact [] = count($staffs);

        //   $counter = 0;
        //   $tempC = count($request->temp_id2);
        //   $tempcd = $tempC - 1;

        //   if(count($staffs) > $tempC){

        //     foreach($staffs as $staff){
        //       // Get Template Details
        //       $tempDet = WhatsappTemplate::find($temp_id2[$counter]);
              
        //       // Store in Campaign List
        //       $post = new Campaignlist();
        //       $post->name = $request->campaign_name;
        //       $post->module_name = $module_name;
        //       $post->wTemp = $wTemp;
        //       $post->user_id = Auth::user()->user_id;
        //       $post->temp_id = $temp_id2[$counter];
        //       $post->whatsappi_id = $capi_id;
        //       $post->msgBody = $tempDet->msgBody;
        //       $post->file = $tempDet->file;
        //       $post->sch_type = $request->sch_type;
        //       if($request->sch_type == '2'){
        //         $post->date_and_time = date('Y-m-d H:i:s',strtotime($request->date_and_time));
        //         $post->camp_date = date('Y-m-d',strtotime($request->date_and_time));
        //         $post->camp_time = date('H:i',strtotime($request->date_and_time));
        //       }
        //       $post->save();

        //       $get_camp_list = Campaignlist::find($post->id);
        //       // Create JOB
        //       if($request->sch_type == '1'){
        //         StaffSendJob::dispatch($staff,$get_camp_list,$getAPI)->onQueue('default');  
        //       }

        //       if($counter == $tempcd){
        //         $counter = 0;
        //       }else{
        //         $counter++;
        //       }
            
        //     }


            

            

        //   }else{

        //     foreach($staffs as $staff){
        //       // Get Template Details
        //       $tempDet = WhatsappTemplate::find($temp_id2[$counter]);

        //       // Store in Campaign List
        //       $post = new Campaignlist();
        //       $post->name = $request->campaign_name;
        //       $post->module_name = $module_name;
        //       $post->wTemp = $wTemp;
        //       $post->user_id = Auth::user()->user_id;
        //       $post->temp_id = $temp_id2[$counter];
        //       $post->whatsappi_id = $capi_id;
        //       $post->msgBody = $tempDet->msgBody;
        //       $post->file = $tempDet->file;
        //       $post->sch_type = $request->sch_type;
        //       if($request->sch_type == '2'){
        //         $post->date_and_time = date('Y-m-d H:i:s',strtotime($request->date_and_time));
        //         $post->camp_date = date('Y-m-d',strtotime($request->date_and_time));
        //         $post->camp_time = date('H:i',strtotime($request->date_and_time));
        //       }
        //       $post->save();

        //       $get_camp_list = Campaignlist::find($post->id);
        //       // Create JOB
        //       if($request->sch_type == '1'){
        //         StaffSendJob::dispatch($staff,$get_camp_list,$getAPI)->onQueue('default');  
        //       }

        //       $counter++;

        //     }

        //   }



        // }

        // foreach($request->api_id as $capi_id){
        //   // Get API details
        //   $getAPI = Userwhatsappapi::find($capi_id);

        //   // Get all staff data
        //   $staffs = Staff::where('staff_status','!=','0')->where('staff_id','!=','200')->get();
        //   $totalcontact [] = count($staffs);

        //   // Get Template Details
        //   $getMtemps = WhatsappTemplate::wherein('id',$temp_id2)->get();

        //   $msgMbody = [];
        //   $fileM = []; 

        //   foreach($getMtemps as $getMtemp){
        //     $msgMbody [] = $getMtemp->msgBody;
        //     $fileM [] = $getMtemp->file;
        //   }

        //   // Store in Campaign List
        //   $post = new Campaignlist();
        //   $post->name = $request->campaign_name;
        //   $post->module_name = $module_name;
        //   $post->wTemp = $wTemp;
        //   $post->user_id = Auth::user()->user_id;
        //   $post->temp_id = implode(',',$temp_id2);
        //   $post->whatsappi_id = $capi_id;
        //   $post->sch_type = $request->sch_type;
        //   $post->msgBody = implode("//",$msgMbody);
        //   $post->file = implode("//",$fileM);
        //   if($request->sch_type == '2'){
        //     $post->date_and_time = date('Y-m-d H:i:s',strtotime($request->date_and_time));
        //     $post->camp_date = date('Y-m-d',strtotime($request->date_and_time));
        //     $post->camp_time = date('H:i',strtotime($request->date_and_time));
        //   }
        //   $post->save();

        //   // Create JOB based on Schedule type
        //   if($request->sch_type == '1'){

        //     $counter = 0;
        //     $tempC = count($request->temp_id2);
        //     $tempcd = $tempC - 1;

        //     if(count($staffs) > $tempC){
        //       foreach($staffs as $staff){
        //         // Get Template Details
        //         $tempDet = WhatsappTemplate::find($temp_id2[$counter]);
        //         Staffmulsendjob::dispatch($tempDet,$staff,$getAPI)->onQueue('default');
        //         if($counter == $tempcd){
        //           $counter = 0;
        //         }else{
        //           $counter++;
        //         }
        //       }
        //     }else{
        //       foreach($staffs as $staff){
        //         // Get Template Details
        //         $tempDet = WhatsappTemplate::find($temp_id2[$counter]);
        //         Staffmulsendjob::dispatch($tempDet,$staff,$getAPI)->onQueue('default');
        //         $counter++;
        //       }
        //     }

        //   }

        // }


        /* Cretae campaign list */
        // Get Template Details
        $getMtemps = WhatsappTemplate::wherein('id',$temp_id2)->get();
        $msgMbody = [];
        $fileM = []; 

        foreach($getMtemps as $getMtemp){
          $msgMbody [] = $getMtemp->msgBody;
          $fileM [] = $getMtemp->file;
        }

        // Store in Campaign List
        $post = new Campaignlist();
        $post->name = $request->campaign_name;
        $post->module_name = $module_name;
        $post->wTemp = $wTemp;
        $post->user_id = Auth::user()->user_id;
        $post->temp_id = implode(',',$temp_id2);
        $post->whatsappi_id = implode(",",$request->api_id);
        $post->sch_type = $request->sch_type;
        $post->msgBody = implode("//",$msgMbody);
        $post->file = implode("//",$fileM);
        if($request->sch_type == '2'){
          $post->date_and_time = date('Y-m-d H:i:s',strtotime($request->date_and_time));
          $post->camp_date = date('Y-m-d',strtotime($request->date_and_time));
          $post->camp_time = date('H:i',strtotime($request->date_and_time));
        }
        $post->api_id_text = implode(",",$request->api_id);
        $post->temp_id_text = implode(',',$temp_id2);
        $post->save();

        if($request->sch_type == '1'){
          foreach($request->api_id as $capi_id){
            // Get API details
            $getAPI = Userwhatsappapi::find($capi_id);

            // Get all staff data
            $staffs = Staff::where('staff_status','!=','0')->where('staff_id','!=','200')->get();
            $totalcontact [] = count($staffs);
            
            $counter = 0;
            $tempC = count($request->temp_id2);
            $tempcd = $tempC - 1;

            if(count($staffs) > $tempC){
              foreach($staffs as $staff){
                // Get Template Details
                $tempDet = WhatsappTemplate::find($temp_id2[$counter]);
                $get_camp_list = Campaignlist::find($post->id);

                dispatch(new Staffmulsendjob($tempDet,$staff,$getAPI))->onQueue('default');
                
                if($counter == $tempcd){
                  $counter = 0;
                }else{
                  $counter++;
                }
              }
            }else{
              foreach($staffs as $staff){
                // Get Template Details
                $tempDet = WhatsappTemplate::find($temp_id2[$counter]);
                dispatch(new Staffmulsendjob($tempDet,$staff,$getAPI))->onQueue('default');
                $counter++;
              }
            }
            $total_recc = array_sum($totalcontact);
          }
        }else{
          $total_recc = "scheduled";
        }
        

        // $total_recc = array_sum($totalcontact);

        // dd($total_recc);

        Session::flash('success','Total '.$total_recc.' message send');
        return redirect()->back();

        // Session::flash('success','Multiple template is pending');
        // return redirect()->back();
      }

    }

    if($module_name == 'party'){

      if($wTemp == 'custom_temp'){
        if ($request->hasFile('file')) {
          $file = $request->file('file');
          $file_count = File::files(base_path().'/public/image/whatsapp');
          $filecount = 0;
          if ($file_count !== false) {
            $filecount = count($file_count);
          }
          $file_exe = $file->getClientOriginalExtension();
          // $name = $filecount . '.' . $file_exe;
          $name = $file->getClientOriginalName();
          // remove space from image
          $filename_ren = pathinfo($name,PATHINFO_FILENAME);
          $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
          $repspfilename = str_replace(" ","_",$filename_ren);
          $new_file = $repspfilename.'.'.$fileext_ren;


          $file->move(base_path().'/public/image/whatsapp', $new_file);

          $msgFile = $new_file;
        }else{
          $msgFile = '';
        }

        $msgBody = $request->msgBody;

        $totalcontact = [];

        $rem_str = ["[Party Name]","[Agency Name]","[City]","[State]","[ID]","[Email]","[Primary Contact]","[Secondary Contact]","[DOB]","[Wakala Card]","[Membership]"];
        $rep_str2 = ["Kalam Shaikh","Heaven Tours and Travel","Mumbai","Maharashtra","205","kashaikh611@gmail.com","919594785319","919324838205","05-12-2022","","412034"];

        $final_msgbody = str_replace($rem_str,$rep_str2,$msgBody);

        $check_str = stripos($msgBody,"[Wakala Card]");


        // dd($msgBody);
        // dd($final_msgbody);
        // dd($check_str);

        // check if wakala card is required or not
        $wakcardst = stripos($msgBody,"[Wakala Card]");
        if($wakcardst != false){
          $wakst = 1;
        }else{
          $wakst = 0;
        }

        // Created campaign
        $post = new Campaignlist();
        $post->name = $request->campaign_name;
        $post->module_name = $module_name;
        $post->whatsappi_id = implode(",",$request->api_id);
        $post->wTemp = $wTemp;
        $post->religion = $request->religion;
        $post->party_status = implode(",",$request->party_status);
        $post->party_contact_type = $request->party_contact_type;
        $post->temp_id = $request->temp_id;
        $post->file =  $msgFile;
        $post->msgBody = $msgBody;
        $post->sch_type = $request->sch_type;
        if($request->sch_type == '2'){
          $post->date_and_time = date('Y-m-d H:i:s',strtotime($request->date_and_time));
          $post->camp_date = date('Y-m-d',strtotime($request->date_and_time));
          $post->camp_time = date('H:i',strtotime($request->date_and_time));
        }
        $post->user_id = Auth::user()->user_id;
        $post->wakala_st = $wakst;
        $post->api_id_text = implode(",",$request->api_id);
        $post->save();

        $get_camp_list = Campaignlist::find($post->id);

        if($request->sch_type == '1'){
          foreach($request->api_id as $capi_id){
            // Get API details
            $getAPI = Userwhatsappapi::find($capi_id);
    
            // Get all party data
            $parties = Party::where('pty_id','!=',200)->where(function($query) use($religion,$party_status){
              if($religion != 'All'){
                $query->where('pty_rel','=',$religion);
              }
              // if($party_status != 'All'){
              //   $query->where('act_status','=',$party_status);
              // }
              $query->wherein('act_status',$party_status);
            })->get();
            
            foreach($parties as $party){
              dispatch(new PartySendJob($party,$get_camp_list,$getAPI))->onQueue('default');
            }
    
    
            // Count Contact
            $totalcontact [] = count($parties);
    
          }
          $total_rec = array_sum($totalcontact);
        }else{
          $total_rec = "scheduled";
        }

        

        Session::flash('success','Total '.$total_rec.' message send');
        return redirect()->back();

        // Updated Foreach Loop
        // foreach($request->api_id as $capi_id){
        //   // Get API details
        //   $getAPI = Userwhatsappapi::find($capi_id);

        //   // Get all party data
        //   $parties = Party::where('pty_id','!=',200)->where(function($query) use($religion,$party_status){
        //     if($religion != 'All'){
        //       $query->where('pty_rel','=',$religion);
        //     }
        //     // if($party_status != 'All'){
        //     //   $query->where('act_status','=',$party_status);
        //     // }
        //     $query->wherein('act_status',$party_status);
        //   })->get();

        //   // Created campaign
        //   foreach($parties as $party){

        //     // check if wakala card is required or not
        //     $wakcardst = stripos($msgBody,"[Wakala Card]");

        //     if($wakcardst != false){
        //       $wakacard = Partywakalacard::where('pty_id','=',$party->pty_id)->first();
        //       if(isset($wakacard)){
        //         $wakdata = [
        //           'file' => $wakacard->file,
        //         ];
        //         $wakst = 1;
        //       }else{
        //         $wakdata = [
        //           'file' => 'none',
        //         ];
        //         $wakst = 0;
        //       }
        //     }else{
        //       $wakdata = [
        //         'file' => 'none',
        //       ];
        //       $wakst = 0;
        //     }

        //     $rep_str = [$party->pty_full_name,$party->pty_ag_name,$party->city,$party->state,$party->pty_id,$party->pty_email,$party->pty_comp_contact,$party->pty_contact_no,"",$party->member_id];
        //     $final_msg_body = str_replace($rem_str,$rep_str,$msgBody);

        //     // Created campaign
        //     $post = new Campaignlist();
        //     $post->name = $request->campaign_name;
        //     $post->module_name = $module_name;
        //     $post->whatsappi_id = $capi_id;
        //     $post->wTemp = $wTemp;
        //     $post->religion = $request->religion;
        //     $post->party_status = implode(",",$request->party_status);
        //     $post->party_contact_type = $request->party_contact_type;
        //     $post->temp_id = $request->temp_id;
        //     $post->file =  $msgFile;
        //     $post->msgBody = $final_msg_body;
        //     $post->sch_type = $request->sch_type;
        //     if($request->sch_type == '2'){
        //       $post->date_and_time = date('Y-m-d H:i:s',strtotime($request->date_and_time));
        //       $post->camp_date = date('Y-m-d',strtotime($request->date_and_time));
        //       $post->camp_time = date('H:i',strtotime($request->date_and_time));
        //     }
        //     $post->wakala_st = $wakst;
        //     $post->user_id = Auth::user()->user_id;
        //     $post->save();

        //     $get_camp_list = Campaignlist::find($post->id);

        //     if($request->sch_type == '1'){
        //       foreach($parties as $party){
        //         dispatch(new PartySendJob($party,$get_camp_list,$getAPI))->onQueue('default');
        //       }
        //     }

        //   }

        //   // Count Contact
        //   $totalcontact [] = count($parties);
        //   $total_rec = array_sum($totalcontact);
        //   Session::flash('success','Total '.$total_rec.' message send');
        //   return redirect()->back();
        // }

      }

      if($wTemp == 'template'){
        // Get File and Message from template
        $getTemp = WhatsappTemplate::find($request->temp_id);
        $msgBody = $getTemp->msgBody;
        $msgFile = $getTemp->file;

        $totalcontact = [];

        // Created campaign
        $post = new Campaignlist();
        $post->name = $request->campaign_name;
        $post->module_name = $module_name;
        $post->whatsappi_id = implode(",",$request->api_id);
        $post->wTemp = $wTemp;
        $post->religion = $request->religion;
        $post->party_status = implode(",",$request->party_status);
        $post->party_contact_type = $request->party_contact_type;
        $post->temp_id = $request->temp_id;
        $post->file =  $msgFile;
        $post->msgBody = $msgBody;
        $post->sch_type = $request->sch_type;
        if($request->sch_type == '2'){
          $post->date_and_time = date('Y-m-d H:i:s',strtotime($request->date_and_time));
          $post->camp_date = date('Y-m-d',strtotime($request->date_and_time));
          $post->camp_time = date('H:i',strtotime($request->date_and_time));
        }
        $post->api_id_text = implode(",",$request->api_id);
        $post->user_id = Auth::user()->user_id;
        $post->save();

        $get_camp_list = Campaignlist::find($post->id);

        if($request->sch_type == '1'){
          foreach($request->api_id as $capi_id){
            // Get API details
            $getAPI = Userwhatsappapi::find($capi_id);
    
            // Get all party data
            $parties = Party::where('pty_id','!=',200)->where(function($query) use($religion,$party_status){
              if($religion != 'All'){
                $query->where('pty_rel','=',$religion);
              }
              // if($party_status != 'All'){
              //   $query->where('act_status','=',$party_status);
              // }
    
              $query->wherein('act_status',$party_status);
    
            })->get();
    
    
            foreach($parties as $party){
              dispatch(new PartySendJob($party,$get_camp_list,$getAPI))->onQueue('default');
            }

    
            // Count Contact
            $totalcontact [] = count($parties);  
          }
          $total_rec = array_sum($totalcontact);
        }else{
          $total_rec = "scheduled";
        }

        


        Session::flash('success','Total '.$total_rec.' message send');
        return redirect()->back();

      }

      if($wTemp == 'multipe_temp'){

        $temp_id2 = $request->temp_id2;
        $totalcontact = [];

        $getMtemps = WhatsappTemplate::wherein('id',$temp_id2)->get();
        $msgMbody = [];
        $fileM = []; 

        foreach($getMtemps as $getMtemp){
          $msgMbody [] = $getMtemp->msgBody;
          $fileM [] = $getMtemp->file;
        }

        // Store in Campaign List
        $post = new Campaignlist();
        $post->name = $request->campaign_name;
        $post->module_name = $module_name;
        $post->wTemp = $wTemp;
        $post->user_id = Auth::user()->user_id;
        $post->temp_id = implode(',',$temp_id2);
        $post->whatsappi_id = implode(",",$request->api_id);
        $post->sch_type = $request->sch_type;
        $post->msgBody = implode("//",$msgMbody);
        $post->party_status = implode(",",$request->party_status);
        $post->file = implode("//",$fileM);
        if($request->sch_type == '2'){
          $post->date_and_time = date('Y-m-d H:i:s',strtotime($request->date_and_time));
          $post->camp_date = date('Y-m-d',strtotime($request->date_and_time));
          $post->camp_time = date('H:i',strtotime($request->date_and_time));
        }
        $post->api_id_text = implode(",",$request->api_id);
        $post->temp_id_text = implode(',',$temp_id2);
        $post->save();

        if($request->sch_type == '1'){
          foreach($request->api_id as $capi_id){
            // Get API details
            $getAPI = Userwhatsappapi::find($capi_id);

            // Get all staff data
            $parties = Party::where('pty_id','!=',200)->where(function($query) use($religion,$party_status){
              if($religion != 'All'){
                $query->where('pty_rel','=',$religion);
              }
              $query->wherein('act_status',$party_status);
            })->get();

            $totalcontact [] = count($parties);

            $counter = 0;
            $tempC = count($request->temp_id2);
            $tempcd = $tempC - 1;

            if(count($parties) > $tempC){
              foreach($parties as $party){

                // Get Template Details
                $tempDet = WhatsappTemplate::find($temp_id2[$counter]);
                $get_camp_list = Campaignlist::find($post->id);

                dispatch(new PtyMulSendJob($tempDet,$party,$getAPI))->onQueue('default');

                if($counter == $tempcd){
                  $counter = 0;
                }else{
                  $counter++;
                }
              }
            }else{
              foreach($parties as $party){
                // Get Template Details
                $tempDet = WhatsappTemplate::find($temp_id2[$counter]);
                dispatch(new PtyMulSendJob($tempDet,$party,$getAPI))->onQueue('default');
                $counter++;
              }
            }

          }
          $total_recc = array_sum($totalcontact);
        }else{
          $total_recc = "scheduled";
        }


        Session::flash('success','Total '.$total_recc.' message send');
        return redirect()->back();


        // Session::flash('success','Multiple template is pending');
        // return redirect()->back();
      }

    }

    if($module_name == 'contact'){

      if($wTemp == 'custom_temp'){
        if ($request->hasFile('file')) {
          $file = $request->file('file');
          $file_count = File::files(base_path().'/public/image/whatsapp');
          $filecount = 0;
          if ($file_count !== false) {
            $filecount = count($file_count);
          }
          $file_exe = $file->getClientOriginalExtension();
          // $name = $filecount . '.' . $file_exe;
          
          $name = $file->getClientOriginalName();

          // remove space from image
          $filename_ren = pathinfo($name,PATHINFO_FILENAME);
          $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
          $repspfilename = str_replace(" ","_",$filename_ren);
          $new_file = $repspfilename.'.'.$fileext_ren;

          $file->move(base_path().'/public/image/whatsapp', $new_file);


          // $file->move(base_path().'/public/image/whatsapp', $name);
          
          $msgFile = $new_file;
        }else{
          $msgFile = '';
        }

        $msgBody = $request->msgBody;

        $totalcontact = [];

        // Campaign List
        $post = new Campaignlist();
        $post->name = $request->campaign_name;
        $post->module_name = $module_name;
        $post->lead_type = $request->lead_type;
        $post->lcs_id = $request->lcs_id;
        $post->whatsappi_id = implode(",",$request->api_id);
        $post->wTemp = $wTemp;
        $post->file =  $msgFile;
        $post->msgBody = $msgBody;
        $post->sch_type = $request->sch_type;
        $post->optinout = $optinout;
        $post->group_id = $group_id;
        if($request->sch_type == '2'){
          $post->date_and_time = date('Y-m-d H:i:s',strtotime($request->date_and_time));
          $post->camp_date = date('Y-m-d',strtotime($request->date_and_time));
          $post->camp_time = date('H:i',strtotime($request->date_and_time));
        }
        $post->api_id_text = implode(",",$request->api_id);
        $post->user_id = Auth::user()->user_id;
        $post->save();

        $get_camp_list = Campaignlist::find($post->id);

        if($request->sch_type == '1'){
          foreach($request->api_id as $capi_id){
            // Get API details
            $getAPI = Userwhatsappapi::find($capi_id);
            // Contact Details
            $cont_dets = AllContact::where('user_id','=',$getAPI->staff_id)->where(function($query) use($lead_type,$lcs_id,$optinout,$group_id){
              if($lead_type != ''){
                $query->where('lead_type','=',$lead_type);
              }
              if($lcs_id != ''){
                $query->where('lcs_id','=',$lcs_id);
              }
              if($optinout != 'All'){
                $query->where('optinout','=',$optinout);
              }
              if($group_id != ''){
                $query->where('group_id','=',$group_id);
              }
            })->get();
    
            // Count Contact
            $totalcontact [] = count($cont_dets);
            $cdCount = 1;
            foreach($cont_dets as $cont_det){
              if($cdCount % 2 == 0){
                dispatch(new WhatsappSendJob($cont_det,$get_camp_list,$getAPI))->onQueue('mediumps');
              }else{
                dispatch(new WhatsappSendJob($cont_det,$get_camp_list,$getAPI))->onQueue('default'); 
              }
              $cdCount++;
            }
          }

          $total_rec = array_sum($totalcontact);
        }else{
          $total_rec = "scheduled";
        }

        Session::flash('success','Total '.$total_rec.' message send');
        return redirect()->back();        
      }

      if($wTemp == 'template'){
        // Get File and Message from template
        $getTemp = WhatsappTemplate::find($request->temp_id);
        $msgBody = $getTemp->msgBody;
        $msgFile = $getTemp->file;

        $totalcontact = [];

        // Campaign List
        $post = new Campaignlist();
        $post->name = $request->campaign_name;
        $post->module_name = $module_name;
        $post->lead_type = $request->lead_type;
        $post->lcs_id = $request->lcs_id;
        $post->whatsappi_id = implode(",",$request->api_id);
        $post->wTemp = $wTemp;
        $post->file =  $msgFile;
        $post->msgBody = $msgBody;
        $post->sch_type = $request->sch_type;
        $post->optinout = $optinout;
        $post->group_id = $group_id;
        if($request->sch_type == '2'){
          $post->date_and_time = date('Y-m-d H:i:s',strtotime($request->date_and_time));
          $post->camp_date = date('Y-m-d',strtotime($request->date_and_time));
          $post->camp_time = date('H:i',strtotime($request->date_and_time));
        }
        $post->user_id = Auth::user()->user_id;
        $post->api_id_text = implode(",",$request->api_id);
        $post->temp_id = $request->temp_id;
        // $post->temp_id_text = implode(",",$request->temp_id);
        $post->save();

        $get_camp_list = Campaignlist::find($post->id);
        
        if($request->sch_type == '1'){
          foreach($request->api_id as $capi_id){
            // Get API details
            $getAPI = Userwhatsappapi::find($capi_id);
            // Contact Details
            $cont_dets = AllContact::where('user_id','=',$getAPI->staff_id)->where(function($query) use($lead_type,$lcs_id,$optinout,$group_id){
              if($lead_type != ''){
                $query->where('lead_type','=',$lead_type);
              }
              if($lcs_id != ''){
                $query->where('lcs_id','=',$lcs_id);
              }
              if($optinout != 'All'){
                $query->where('optinout','=',$optinout);
              }
              if($group_id != ''){
                $query->where('group_id','=',$group_id);
              }
            })->get();
            // Count Contact
            $totalcontact [] = count($cont_dets);
            $cdCount = 1;
            foreach($cont_dets as $cont_det){
              if($cdCount % 2 == 0){
                dispatch(new WhatsappSendJob($cont_det,$get_camp_list,$getAPI))->onQueue('mediumps');
              }else{
                dispatch(new WhatsappSendJob($cont_det,$get_camp_list,$getAPI))->onQueue('default'); 
              }
              $cdCount++;
            }
          }
          $total_rec = array_sum($totalcontact);
        }else{
          $total_rec = "schedule";
        }

        


        
        Session::flash('success','Total '.$total_rec.' message send');
        return redirect()->back();

      }

      if($wTemp == 'multipe_temp'){

        $temp_id2 = $request->temp_id2;
        $totalcontact = [];

        $getMtemps = WhatsappTemplate::wherein('id',$temp_id2)->get();
        $msgMbody = [];
        $fileM = [];

        foreach($getMtemps as $getMtemp){
          $msgMbody [] = $getMtemp->msgBody;
          $fileM [] = $getMtemp->file;
        }

        // Store in Campaign List
        $post = new Campaignlist();
        $post->name = $request->campaign_name;
        $post->module_name = $module_name;
        $post->wTemp = $wTemp;
        $post->user_id = Auth::user()->user_id;
        $post->temp_id = implode(',',$temp_id2);
        $post->whatsappi_id = implode(",",$request->api_id);
        $post->sch_type = $request->sch_type;
        $post->optinout = $optinout;
        $post->group_id = $group_id;
        $post->msgBody = implode("//",$msgMbody);
        $post->file = implode("//",$fileM);
        if($request->sch_type == '2'){
          $post->date_and_time = date('Y-m-d H:i:s',strtotime($request->date_and_time));
          $post->camp_date = date('Y-m-d',strtotime($request->date_and_time));
          $post->camp_time = date('H:i',strtotime($request->date_and_time));
        }
        $post->api_id_text = implode(",",$request->api_id);
        $post->temp_id_text = implode(',',$temp_id2);
        $post->save();

        if($request->sch_type == '1'){
          foreach($request->api_id as $capi_id){
            // Get API details
            $getAPI = Userwhatsappapi::find($capi_id);

            // Contact Details
            $cont_dets = AllContact::where('user_id','=',$getAPI->staff_id)->where(function($query) use($lead_type,$lcs_id,$optinout,$group_id){
              if($lead_type != ''){
                $query->where('lead_type','=',$lead_type);
              }
              if($lcs_id != ''){
                $query->where('lcs_id','=',$lcs_id);
              }
              if($optinout != 'All'){
                $query->where('optinout','=',$optinout);
              }
              if($group_id != ''){
                $query->where('group_id','=',$group_id);
              }
            })->get();

            $totalcontact [] = count($cont_dets);

            $counter = 0;
            $tempC = count($request->temp_id2);
            $tempcd = $tempC - 1;
            $cdCount = 1;

            if(count($cont_dets) > $tempC){
              foreach($cont_dets as $cont_det){
                // Get Template Details
                $tempDet = WhatsappTemplate::find($temp_id2[$counter]);
                $get_camp_list = Campaignlist::find($post->id);
                if($cdCount % 2 == 0){
                  dispatch(new AllcMulSendJob($tempDet,$cont_det,$getAPI))->onQueue('mediumps');
                }else{
                  dispatch(new AllcMulSendJob($tempDet,$cont_det,$getAPI))->onQueue('default');
                }

                $cdCount++;

                if($counter == $tempcd){
                  $counter = 0;
                }else{
                  $counter++;
                }
              }
            }else{
              foreach($cont_dets as $cont_det){
                // Get Template Details
                $tempDet = WhatsappTemplate::find($temp_id2[$counter]);
                $get_camp_list = Campaignlist::find($post->id);
                if($cdCount % 2 == 0){
                  dispatch(new AllcMulSendJob($tempDet,$cont_det,$getAPI))->onQueue('mediumps');
                }else{
                  dispatch(new AllcMulSendJob($tempDet,$cont_det,$getAPI))->onQueue('default');
                }
                $cdCount++;
                $counter++;
              }
            }

          }
          $total_recc = array_sum($totalcontact);
        }else{
          $total_recc = "scheduled";
        }


        Session::flash('success','Total '.$total_recc.' message send');
        return redirect()->back();

        // Session::flash('success','Multiple template is pending');
        // return redirect()->back();
      }



    }
    



  }


  public function campaignWhatsappstore4(Request $request){
    // WIPF
    // Get Request Details
    
  }

  public function wtemplatel_store(Request $request){

    // dd($request);

    if ($request->hasFile('file')) {
      $file = $request->file('file');
      $file_count = File::files(base_path().'/public/image/whatsapp');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      // $file_exe = $file->getClientOriginalExtension();
      // $name = $filecount . '.' . $file_exe;

      $name = $file->getClientOriginalName();

      // remove space from image
      $filename_ren = pathinfo($name,PATHINFO_FILENAME);
      $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
      $repspfilename = str_replace(" ","_",$filename_ren);
      $new_file = $repspfilename.'.'.$fileext_ren;

      $file->move(base_path().'/public/image/whatsapp', $new_file);
      $cand_file = $new_file;

    } else {
      $cand_file = '';
    }

    if($request->public_st == 1){
      $public_st = 1;
    }else{
      $public_st = 0;
    }

    // Multiple Images Upload
    // if($request->hasFile('file')){
    //   $files = $request->file('file');
    //   foreach ($files as $file) {
    //     $file_count = File::files(base_path().'/public/image/whatsapp');
    //     $filecount = 0;
    //     if ($file_count !== false) {
    //       $filecount = count($file_count);
    //     }
    //     $file_exe = $file->getClientOriginalExtension();
    //     $name = $filecount . '.' . $file_exe;
    //     $file->move(base_path().'/public/image/whatsapp', $name);
    //     $cand_file[] = $name;
    //   }
    // }else{
    //   $cand_file = "";
    // }


    if(Auth::user()->user_type == 2){
      $staffD = Staff::where('staff_id','=',Auth::user()->user_id)->first();
      $department = $staffD->staff_job_role;
    }else{
      $department = 'Admin';
    }

    // Multiple Message
    


    $post = new WhatsappTemplate();
    $post->subject_name = $request->input('subject_name');
    // $post->msgBody = json_encode($request->input('msgBody'));
    // $post->msgBody = implode(':',$request->input('msgBody'));
    $post->msgBody = $request->input('msgBody');
    $post->department = $department;
    // $post->file = implode(',',$cand_file);
    $post->file = $cand_file;
    $post->user_id = Auth::user()->user_id;
    $post->temp_type = $request->temp_type;
    $post->careoff_id = $request->careoff_id;
    $post->temp_for = $request->temp_for;
    $post->campaign_for = $request->campaign_for;
    $post->esubname = $request->email_subject_name;
    $post->public_st = $public_st;
    $post->save();

    Session::flash('success','Whatsapp Template created!');
    return redirect()->back();

  }

  public function wtemplatel_active(Request $request){
    extract($_POST);
    $post = WhatsappTemplate::find($pty_id);
    $post->status = 1;
    $post->save();
    Session::flash('success','Whatsapp Template Active');
    return redirect()->back();

  }

  public function wtemplatel_deactive(Request $request){
    extract($_POST);
    $post = WhatsappTemplate::find($pty_id);
    $post->status = 0;
    $post->save();

    Session::flash('success','Whatsapp Template Deactive');
    return redirect()->back();
  }


  public function lcsLead($id){
    $post = LifeCycleStage::find($id);
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm-master/stage_lists',['pageConfigs' => $pageConfigs,'post' => $post]);
  }

  public function getStage(Request $request){
    $id = $request->id;
    $posts = Lstage::where('lcs_id','=',$id)->get();
    $res = '';
    $res .= '<option value=""></option>';
    foreach ($posts as $post) {
      $res .= '<option value="'.$post->id.'">'.$post->name.'</option>';
    }

    $arr['res'] = $res;
    return response()->json($arr);
  }

  public function getStage2(Request $request)
  {
    $id = $request->id;
    $ls_id = $request->ls_id;

    // Get Lead Stage
    $posts = Lstage::where('lcs_id','=',$id)->get();

    // Get All Contact Detaisl
    $cont = AllContact::find($ls_id);

    $res = '';

    $res .= '<option value=""></option>';
    foreach($posts as $post){
      // $res .= '<option value="'.$post->id.'">'.$post->name.'</option>';

      $res .= '<option value="'.$post->id.'"';
      if($post->id == $cont->ls_id){
        $res .= ' selected';
      }
      $res .= '>'.$post->name.'</option>';
    }

    $arr['res'] = $res;
    return response()->json($arr);

  }

  public function contacts_update(Request $request){

    $getGroups = AllContact::where('group_id','!=','')->where('assign_id','=',$request->assign_id)->get();
    
    $cont = AllContact::find($request->id);
    $cont->lead_type = $request->lead_type;
    $cont->full_name = $request->full_name;
    $cont->mobile_no = $request->phone;
    $cont->email = $request->email;
    $cont->phone0 = $request->input('phone0') ? $request->input('phone0') : '';
    $cont->email0 = $request->input('email0') ? $request->input('email0') : '';
    $cont->phone1 = $request->input('phone1') ? $request->input('phone1') : '';
    $cont->email1 = $request->input('email1') ? $request->input('email1') : '';
    $cont->phone2 = $request->input('phone2') ? $request->input('phone2') : '';
    $cont->email2 = $request->input('email2') ? $request->input('email2') : '';
    $cont->assign_id = $request->assign_id;

    $sourceFind = Source::find($request->source2);

    // check whatsapp number is vailable
    if ($request->mobile_no !='') {
      if (($request->mobile_no != $cont->mobile_no) && ($cont->mobile_no == $cont->primary_contact_no)) {
        $cont->primary_contact_number = $request->phone;
      }
    }
    if ($request->phone0 !='') {
      if (($request->phone0 != $cont->phone0) && ($cont->phone0 == $cont->primary_contact_no)) {
        $cont->primary_contact_no = $request->phone0;
      }
    }
    if ($request->phone1 !='') {
      if (($request->phone1 != $cont->phone1) && ($cont->phone1 == $cont->primary_contact_no)) {
        $cont->primary_contact_no = $request->phone1;
      }
    }
    if ($request->phone2 !='') {
      if (($request->phone2 != $cont->phone2) && ($cont->phone2 == $cont->primary_contact_no)) {
        $cont->primary_contact_no = $request->phone2;
      }
    }

    $cont->optinout = $request->optinout;
    if($request->assoc_number_id != ''){
      $cont->assoc_number_id = implode(",",$request->assoc_number_id);
    }

    $cont->source = $sourceFind->name;
    $cont->source_id = $request->source2;
    $cont->descr = $request->input('descr') ? $request->input('descr') :'';
    $cont->lcs_id = $request->input('lcs_id') ? $request->input('lcs_id') :'';
    $cont->ls_id = $request->input('ls_id') ? $request->input('ls_id') :'';
    $cont->full_address = $request->input('full_address') ? $request->input('full_address') :'';

    $cont->googleplus = $request->input('googleplus') ? $request->input('googleplus') :'';
    $cont->linkedin_link = $request->input('linkedin_link') ? $request->input('linkedin_link') :'';
    $cont->tw_link = $request->input('tw_link') ? $request->input('tw_link') :'';
    $cont->fb_link = $request->input('fb_link') ? $request->input('fb_link') :'';
    $cont->skype = $request->input('skype') ? $request->input('skype') :'';

    $cont->primary_contact_no = $request->primary_contact_no;

    // $cont->calling_from = $request->calling_from;
    // $cont->state = $request->state;
    // $cont->city = $request->city;
    $cont->regarding = $request->input('regarding') ? implode(",",$request->input('regarding')) : '';
    if($request->lead_type == 'Direct Wakala Candidate' || $request->lead_type == 'Regular Wakala Party' || $request->lead_type == 'Agent without office' || $request->lead_type == 'Unknown'){
      // $cont->regarding = $request->input('regarding') ? implode(",",$request->input('regarding')) : '';    
      $cont->country_id = $request->country_id2;
      $cont->city = $request->city2;
      $cont->office_name = '';
      $cont->job_title = '';
      $cont->expected_cid = '';
      $cont->indust_id = '';
      $cont->job_desg = '';
      $cont->tradesitecenter = '';
      $cont->company_name = '';
    }elseif($request->lead_type == 'Associate with office'){
      // $cont->regarding = $request->input('regarding') ? implode(",",$request->input('regarding')) : '';
      $cont->office_name = $request->input('office_name') ? $request->input('office_name') :'';
      $cont->country_id = $request->country_id2;
      $cont->city = $request->city2;
      $cont->job_title = '';
      $cont->expected_cid = '';
      $cont->indust_id = '';
      $cont->job_desg = '';
      $cont->tradesitecenter = '';
      $cont->company_name = '';
    }elseif($request->lead_type == 'Job seeker'){
      $cont->job_title = $request->input('job_title') ? $request->input('job_title') :'';
      $cont->country_id = $request->country_id2;
      $cont->city = $request->city2;
      $cont->expected_cid = $request->input('expected_cid') ? $request->input('expected_cid') :'';
      $cont->office_name = '';
      // $cont->regarding = '';
      $cont->indust_id = '';
      $cont->job_desg = '';
      $cont->tradesitecenter = '';
      $cont->company_name = '';
    }elseif($request->lead_type == 'Company'){
      $cont->company_name = $request->input('company_name') ? $request->input('company_name') :'';
      $cont->country_id = $request->country_id;
      $cont->city = $request->city;
      $cont->indust_id = $request->input('indust_id') ? $request->input('indust_id') :'';
      $cont->job_desg = $request->input('job_desg') ? $request->input('job_desg') :'';
      $cont->office_name = '';
      // $cont->regarding = '';
      $cont->job_title = '';
      $cont->expected_cid = '';
      $cont->tradesitecenter = '';
    }elseif($request->lead_type == 'Trade site Center'){
      $cont->tradesitecenter = $request->input('tradecenter') ? implode(",",$request->input('tradecenter')) : '';
      $cont->office_name = '';
      // $cont->regarding = '';
      $cont->job_title = '';
      $cont->expected_cid = '';
      $cont->indust_id = '';
      $cont->job_desg = '';
      $cont->company_name = '';
    }

    if($cont->group_id == ''){
      if($getGroups->count() > 0){
        // Associate as per group
        $groupMs = Groupm::orderBy('name','ASC')->get();
        $ic = 0;
        foreach($groupMs as $groupM){
          if($ic < 1){
            $getAllc = AllContact::where('group_id','=',$groupM->id)->where('assign_id','=',$request->assign_id)->count();
            // check if maximaum limit is greater than specific count
            if($getAllc < $groupM->max_limit){
              $cont->group_id = $groupM->id;
              $ic++;
            }
          }
        }
      }else{
        $groupM = Groupm::orderBy('name','ASC')->first();
        if($groupM){
          $cont->group_id = $groupM->id;
        }
      }
    }

    $cont->save();
    // Update All Related Table
    $del_id = $request->id;
    $regardings = $request->input('regarding');
    $tradecenters = $request->input('tradecenter');

    // delete all related ID
    $regards = ContactRegarding::where('cont_id','=',$del_id)->get();
    $tradecds = ContactTradesitecenter::where('cont_id','=',$del_id)->get();
    // Delete Regarding
    if(isset($regards)){
      foreach ($regards as $regard) {
        $regard->delete();
      }
    }

    if(isset($tradecds)){
      foreach ($tradecds as $tradecd) {
        $tradecd->delete();
      }
    }

    if ($regardings !='') {
      foreach($regardings as $regard){
        // Get ID from Regarding
        $getIDR = Regarding::where('name','=',$regard)->first();
        $conreg = new ContactRegarding();
        $conreg->cont_id = $del_id;
        $conreg->regarding_id = $getIDR->id;
        $conreg->save();
      } 
    }

    // Update and Deleted respective Table
    if($request->lead_type == 'Job seeker'){
      // Check it available in other table or same table
      $jsPost = JobSeekerContact::where('contact_id','=',$del_id)->first();
      $ccPost = ContactCompany::where('contact_id','=',$del_id)->first();
      $acPost = AssociateContact::where('contact_id','=',$del_id)->first();
      
      if($ccPost !='') {
        $ccPost->delete();
        
      }
      if($acPost !='') {
        $acPost->delete();
      }

      if($jsPost == ''){
        $post = new JobSeekerContact();
        $post->contact_id = $del_id;
        $post->user_id = Auth::user()->user_id;
        $post->save();
      }
    }

    if($request->lead_type == 'Company'){
      // Check it available in other table or same table
      $jsPost = JobSeekerContact::where('contact_id','=',$del_id)->first();
      $ccPost = ContactCompany::where('contact_id','=',$del_id)->first();
      $acPost = AssociateContact::where('contact_id','=',$del_id)->first();
      

      if($jsPost !='') {
        $jsPost->delete();
        
      }
      if($acPost !='') {
        $acPost->delete();
      }

      if($ccPost == ''){
        $post = new ContactCompany();
        $post->contact_id = $del_id;
        $post->user_id = Auth::user()->user_id;
        $post->save();
      }
    }

    

    if($request->lead_type == 'Unknown' || $request->lead_type == 'Direct Wakala Candidate' || $request->lead_type == 'Agent without office' || $request->lead_type == 'Associate with office' || $request->lead_type == 'Trade site Center'){
      // Check it available in other table or same table
      $jsPost = JobSeekerContact::where('contact_id','=',$del_id)->first();
      $ccPost = ContactCompany::where('contact_id','=',$del_id)->first();
      $acPost = AssociateContact::where('contact_id','=',$del_id)->first();
      if($jsPost !='') {
        $jsPost->delete();
        
      }
      if($ccPost !='') {
        $ccPost->delete();
      }
      if($acPost == ''){
        $post = new AssociateContact();
        $post->contact_id = $del_id;
        $post->user_id = Auth::user()->user_id;
        $post->save();
      }
    }
    

    // if($request->lead_type == 'Direct Wakala Candidate' || $request->lead_type == 'Regular Wakala Party' || $request->lead_type == 'Agent without office' || $request->lead_type == 'Associate with office' || $request->lead_type == 'Unknown'){
    //   if ($regardings !='') {
    //     foreach($regardings as $regard){
    //       $conreg = new ContactRegarding();
    //       $conreg->cont_id = $del_id;
    //       $conreg->regarding_id = $regard;
    //       $conreg->save();
    //     } 
    //   }
    // }

    if($request->lead_type == 'Trade site Center'){
      foreach($tradecenters as $tradec){
        $conreg = new ContactTradesitecenter();
        $conreg->cont_id = $del_id;
        $conreg->tradesc_id = $tradec;
        $conreg->save();
      }
    }
    
    
    Session::flash('success','All Contacts Updated!');
    return redirect()->back();
  }

  public function stgandstup(Request $request)
  {
    $post = AllContact::find($request->id);
    $post->lcs_id = $request->lcs2_id;
    $post->ls_id = $request->lss2_id;
    $post->save();

    // $data = "All Contacts updated!";
    // return response()->json($data);

    Session::flash('success','Stage updated!');
    return redirect()->back();

  }

  public function contacts_update2(Request $request){

    $getGroups = AllContact::where('group_id','!=','')->where('assign_id','=',$request->assign_id)->get();

    $cont = AllContact::find($request->cont_id);
    $cont->lead_type = $request->lead_type;
    $cont->full_name = $request->full_name;
    $cont->mobile_no = $request->phone1;
    $cont->email = $request->email;
    
    if($request->input('phone01') != ''){
      $cont->phone0 = $request->input('phone01');
    }else{
      $cont->phone0 = $request->input('phone0');
    }

    if($request->input('phone11') != ''){
      $cont->phone1 = $request->input('phone11');
    }else{
      $cont->phone1 = $request->input('phone1');
    }

    if($request->input('phone21') != ''){
      $cont->phone2 = $request->input('phone21');
    }else{
      $cont->phone2 = $request->input('phone2');
    }

    if($request->input('email01') != ''){
      $cont->email0 = $request->input('email01');
    }else{
      $cont->email0 = $request->input('email0');
    }

    if($request->input('email11') != ''){
      $cont->email1 = $request->input('email11');
    }else{
      $cont->email1 = $request->input('email1');
    }

    if($request->input('email21') != ''){
      $cont->email2 = $request->input('email21');
    }else{
      $cont->email2 = $request->input('email2');
    }

    $cont->assign_id = $request->assign_id;

    $sourceFind = Source::find($request->source2);

    $cont->source = $sourceFind->name;
    $cont->source_id = $request->source2;
    $cont->descr = $request->input('descr') ? $request->input('descr') :'';
    $cont->lcs_id = $request->input('lcs_id') ? $request->input('lcs_id') :'';
    $cont->ls_id = $request->input('ls_id') ? $request->input('ls_id') :'';
    $cont->full_address = $request->input('full_address') ? $request->input('full_address') :'';
    $cont->lead_prority = $request->input('lead_priority');
    $cont->optinout = $request->optinout;
    if($request->assoc_number_id !=''){
      $cont->assoc_number_id = implode(",",$request->assoc_number_id);
    }

    if($cont->group_id == ''){
      if($getGroups->count() > 0){
        // Associate as per group
        $groupMs = Groupm::orderBy('name','ASC')->get();
        $ic = 0;
        foreach($groupMs as $groupM){
          if($ic < 1){
            $getAllc = AllContact::where('group_id','=',$groupM->id)->where('assign_id','=',$request->assign_id)->count();
            // check if maximaum limit is greater than specific count
            if($getAllc < $groupM->max_limit){
              $cont->group_id = $groupM->id;
              $ic++;
            }
          }
        }
      }else{
        $groupM = Groupm::orderBy('name','ASC')->first();
        if($groupM){
          $cont->group_id = $groupM->id;
        }
      }
    }



    $cont->regarding = $request->input('regarding') ? implode(",",$request->input('regarding')) : '';
    if($request->lead_type == 'Direct Wakala Candidate' || $request->lead_type == 'Regular Wakala Party' || $request->lead_type == 'Agent without office' || $request->lead_type == 'Unknown'){
      // $cont->regarding = $request->input('regarding') ? implode(",",$request->input('regarding')) : '';    
      $cont->country_id = $request->country_id2;
      $cont->city = $request->city2;
      $cont->office_name = '';
      $cont->job_title = '';
      $cont->expected_cid = '';
      $cont->indust_id = '';
      $cont->job_desg = '';
      $cont->tradesitecenter = '';
      $cont->company_name = '';
    }elseif($request->lead_type == 'Associate with office'){
      // $cont->regarding = $request->input('regarding') ? implode(",",$request->input('regarding')) : '';
      $cont->office_name = $request->input('office_name') ? $request->input('office_name') :'';
      $cont->country_id = $request->country_id2;
      $cont->city = $request->city2;
      $cont->job_title = '';
      $cont->expected_cid = '';
      $cont->indust_id = '';
      $cont->job_desg = '';
      $cont->tradesitecenter = '';
      $cont->company_name = '';
    }elseif($request->lead_type == 'Job seeker'){
      $cont->job_title = $request->input('job_title') ? $request->input('job_title') :'';
      $cont->country_id = $request->country_id2;
      $cont->city = $request->city2;
      $cont->expected_cid = $request->input('expected_cid') ? $request->input('expected_cid') :'';
      $cont->office_name = '';
      // $cont->regarding = '';
      $cont->indust_id = '';
      $cont->job_desg = '';
      $cont->tradesitecenter = '';
      $cont->company_name = '';
    }elseif($request->lead_type == 'Company'){
      $cont->company_name = $request->input('company_name') ? $request->input('company_name') :'';
      $cont->country_id = $request->country_id;
      $cont->city = $request->city;
      $cont->indust_id = $request->input('indust_id') ? $request->input('indust_id') :'';
      $cont->job_desg = $request->input('job_desg') ? $request->input('job_desg') :'';
      $cont->office_name = '';
      // $cont->regarding = '';
      $cont->job_title = '';
      $cont->expected_cid = '';
      $cont->tradesitecenter = '';
    }elseif($request->lead_type == 'Trade site Center'){
      $cont->tradesitecenter = $request->input('tradecenter') ? implode(",",$request->input('tradecenter')) : '';
      $cont->office_name = '';
      // $cont->regarding = '';
      $cont->job_title = '';
      $cont->expected_cid = '';
      $cont->indust_id = '';
      $cont->job_desg = '';
      $cont->company_name = '';
    }
    $cont->save();
    // Update All Related Table
    $del_id = $request->cont_id;
    $regardings = $request->input('regarding');
    $tradecenters = $request->input('tradecenter');

    // delete all related ID
    $regards = ContactRegarding::where('cont_id','=',$del_id)->get();
    $tradecds = ContactTradesitecenter::where('cont_id','=',$del_id)->get();
    // Delete Regarding
    if(isset($regards)){
      foreach ($regards as $regard) {
        $regard->delete();
      }
    }

    if(isset($tradecds)){
      foreach ($tradecds as $tradecd) {
        $tradecd->delete();
      }
    }

    if ($regardings !='') {
      foreach($regardings as $regard){
        // Get ID from Regarding
        $getIDR = Regarding::where('name','=',$regard)->first();
        $conreg = new ContactRegarding();
        $conreg->cont_id = $del_id;
        $conreg->regarding_id = $getIDR->id;
        $conreg->save();
      } 
    }

    // Update and Deleted respective Table
    if($request->lead_type == 'Job seeker'){
      // Check it available in other table or same table
      $jsPost = JobSeekerContact::where('contact_id','=',$del_id)->first();
      $ccPost = ContactCompany::where('contact_id','=',$del_id)->first();
      $acPost = AssociateContact::where('contact_id','=',$del_id)->first();
      
      if($ccPost !='') {
        $ccPost->delete();
        
      }
      if($acPost !='') {
        $acPost->delete();
      }

      if($jsPost == ''){
        $post = new JobSeekerContact();
        $post->contact_id = $del_id;
        $post->user_id = Auth::user()->user_id;
        $post->save();
      }
    }

    if($request->lead_type == 'Company'){
      // Check it available in other table or same table
      $jsPost = JobSeekerContact::where('contact_id','=',$del_id)->first();
      $ccPost = ContactCompany::where('contact_id','=',$del_id)->first();
      $acPost = AssociateContact::where('contact_id','=',$del_id)->first();
      

      if($jsPost !='') {
        $jsPost->delete();
        
      }
      if($acPost !='') {
        $acPost->delete();
      }

      if($ccPost == ''){
        $post = new ContactCompany();
        $post->contact_id = $del_id;
        $post->user_id = Auth::user()->user_id;
        $post->save();
      }
    }

    

    if($request->lead_type == 'Unknown' || $request->lead_type == 'Direct Wakala Candidate' || $request->lead_type == 'Agent without office' || $request->lead_type == 'Associate with office' || $request->lead_type == 'Trade site Center'){
      // Check it available in other table or same table
      $jsPost = JobSeekerContact::where('contact_id','=',$del_id)->first();
      $ccPost = ContactCompany::where('contact_id','=',$del_id)->first();
      $acPost = AssociateContact::where('contact_id','=',$del_id)->first();
      if($jsPost !='') {
        $jsPost->delete();
        
      }
      if($ccPost !='') {
        $ccPost->delete();
      }
      if($acPost == ''){
        $post = new AssociateContact();
        $post->contact_id = $del_id;
        $post->user_id = Auth::user()->user_id;
        $post->save();
      }
    }
    
    if($request->lead_type == 'Trade site Center'){
      foreach($tradecenters as $tradec){
        $conreg = new ContactTradesitecenter();
        $conreg->cont_id = $del_id;
        $conreg->tradesc_id = $tradec;
        $conreg->save();
      }
    }

    $data = "All Contacts updated!";
    return response()->json($data);
    
  }

  public function getWhatsappDet(Request $request)
  {
    $post = AllContact::find($request->id);
    return response()->json($post);
  }

  public function updateWhatsappDet(Request $request){
    $post = AllContact::find($request->id);
    $post->primary_contact_no = $request->whatsapp_n;
    $post->save();

    $data = "Whatsapp number updated!";
    return response()->json($data);


  }

  public function getTemplist(Request $request){
    $id  = $request->input('id');
    $post = WhatsappTemplate::find($id);

    return response()->json($post);
  }

  public function getDelete(Request $request){
    $post = WhatsappTemplate::find($request->pty_id);
    $post->delete();
    Session::flash('success','Template deleted!');
    return redirect()->back();
  }

  public function wtemplatel_update(Request $request){
    $post = WhatsappTemplate::find($request->id);

    if ($request->hasFile('file')) {
      $file = $request->file('file');
      $file_count = File::files(base_path().'/public/image/whatsapp');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/whatsapp', $name);
      $cand_file = $name;

    } else {
      $cand_file = $post->file;
    }
    
    $post->subject_name = $request->subject_name;
    $post->msgBody = $request->msgBdy;
    $post->file = $cand_file;
    $post->temp_type = $request->temp_type;
    $post->careoff_id = $request->careoff_id;

    $post->campaign_for = $request->campaign_for;
    $post->temp_for = $request->temp_for;
    $post->esubname = $request->email_subject_name;

    $post->save();
    Session::flash('success','Whatsapp Template updated!');
    return redirect()->back();
  }

  public function whatsappStorem2(Request $request){

    // dd($request);
    // Get request details
    $idsv = explode(',', $request->idsv);

    $api_id = $request->input('api_id');
    $temp_id = $request->input('temp_id');
    $campaign_name = $request->input('campaign_name');
    $wtemp = $request->wTemp;
    $msgBody = $request->textBody;

    // Get API Details
    $getAPI = Userwhatsappapi::find($api_id);
    if ($request->hasFile('file')) {
      $file = $request->file('file');
      $file_count = File::files(base_path().'/public/image/whatsapp');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/whatsapp', $name);
      $cand_file = $name;

      // API Details
    } else {
      $cand_file = '';
    } 

    
    // Send whatsapp based on Template
    if($wtemp == 'template'){
      // get Template Details
      $getTemp = WhatsappTemplate::find($temp_id);
      $post = new Campaignlist();
      $post->name = $campaign_name;
      $post->module_name = 'Contact';
      $post->user_id = Auth::user()->user_id;
      $post->temp_id = $temp_id;
      $post->whatsappi_id = $api_id;
      $post->msgBody = $getTemp->msgBody;
      $post->file = $getTemp->file;
      $post->ids = $request->idsv;
      $post->save();
    }else{
      $post = new Campaignlist();
      $post->name = $campaign_name;
      $post->module_name = 'Contact';
      $post->user_id = Auth::user()->user_id;
      $post->whatsappi_id = $api_id;
      $post->msgBody = $request->textBody;
      $post->file = $cand_file;
      $post->ids = $request->idsv;
      $post->save();
    }

    $get_last_id = $post->id;
    $get_camp_list = Campaignlist::find($get_last_id);

    $cont_dets = AllContact::whereIn('id',$idsv)->get();
    $total_rec = count($cont_dets);

    // dd($total_rec);



    foreach($cont_dets as $cont_det){
      dispatch(new WhatsappSendJob($cont_det,$get_camp_list,$getAPI))->onQueue('default');
    }

    Session::flash('success','Total '.$total_rec.' message send');
    return redirect()->back();
    
  }

  public function getSelectedList(Request $request){
    $ids = explode(',',$request->ids);
    $cont_dets = AllContact::whereIn('id',$ids)->get();
    return response()->json($cont_dets);
  }

  public function getPrimaryno2(Request $request){
    $primary_no = $request->pty_comp_contact;
    $pty_id = $request->pty_id;

    $posts = Party::where('pty_id','!=',$pty_id)->where('pty_comp_contact','=',$primary_no)->orWhere('telephone_number','=',$primary_no)->orWhere('p_mobile','=',$primary_no)->orWhere('pty_contact_no','=',$primary_no)->count();

    if($posts == 0){
      echo "true";
    }else{
      echo "false";
    }
  }


  public function gettelepno2(Request $request){
    $primary_no = $request->telephone_number;
    $pty_id = $request->pty_id;

    $posts = Party::where('pty_id','!=',$pty_id)->where('telephone_number','=',$primary_no)->orWhere('pty_comp_contact','=',$primary_no)->orWhere('p_mobile','=',$primary_no)->orWhere('pty_contact_no','=',$primary_no)->count();

    if($posts == 0){
      echo "true";
    }else{
      echo "false";
    }
  }

  public function getsecondaryno2(Request $request){
    $primary_no = $request->p_mobile;
    $pty_id = $request->pty_id;

    $posts = Party::where('pty_id','!=',$pty_id)->where('p_mobile','=',$primary_no)->orWhere('pty_comp_contact','=',$primary_no)->orWhere('telephone_number','=',$primary_no)->orWhere('pty_contact_no','=',$primary_no)->count();

    if($posts == 0){
      echo "true";
    }else{
      echo "false";
    }
  }

  public function getpersonalno2(Request $request){
    $primary_no = $request->pty_contact_no;
    $pty_id = $request->pty_id;

    $posts = Party::where('pty_id','!=',$pty_id)->where('pty_contact_no','=',$primary_no)->orWhere('pty_comp_contact','=',$primary_no)->orWhere('telephone_number','=',$primary_no)->orWhere('p_mobile','=',$primary_no)->count();

    if($posts == 0){
      echo "true";
    }else{
      echo "false";
    }
  }

  public function uploadwakalacard(Request $request){

    if($request->hasFile('file')){
      $file = $request->file('file');
      $name = $file->getClientOriginalName();
      $file->move(base_path().'/public/image/service-master',$name);
      $wakala = $name;
    }else{
      $wakala = "";
    }

    $post = new Partywakalacard();
    $post->pty_id = $request->pty_id;
    $post->file = $wakala;
    $post->user_id = Auth::user()->user_id;
    $post->save();

    Session::flash('success','Wakala card uploaded!');
    return redirect()->back();

  }

  public function deletewakalacard(Request $request){
    $post = Partywakalacard::find($request->pty_wfile_id);

    // check file exist on folder and remove
    $file_path = public_path('image/service-master/'.$post->file);

    if(file_exists($file_path)){
      @unlink($file_path);
      $post->delete();
    }else{
      $post->delete();
    }

    Session::flash('success','wakala card deleted!');
    return redirect()->back();

  }

  public function getPrimaryno(Request $request){
    $primary_no = $request->pty_comp_contact;

    $posts = Party::where('pty_comp_contact','=',$primary_no)->orWhere('telephone_number','=',$primary_no)->orWhere('p_mobile','=',$primary_no)->orWhere('pty_contact_no','=',$primary_no)->count();
    if($posts == 0){
      echo "true";
    }else{
      echo "false";
    }
  }

  public function gettelepno(Request $request){
    $primary_no = $request->telephone_number;

    $posts = Party::where('telephone_number','=',$primary_no)->orWhere('pty_comp_contact','=',$primary_no)->orWhere('p_mobile','=',$primary_no)->orWhere('pty_contact_no','=',$primary_no)->count();
    if($posts == 0){
      echo "true";
    }else{
      echo "false";
    }
  }

  public function getsecondaryno(Request $request){
    $primary_no = $request->p_mobile;

    $posts = Party::where('p_mobile','=',$primary_no)->orWhere('telephone_number','=',$primary_no)->orWhere('pty_comp_contact','=',$primary_no)->orWhere('pty_contact_no','=',$primary_no)->count();
    if($posts == 0){
      echo "true";
    }else{
      echo "false";
    }
  }

  public function getpersonalno(Request $request){
    $primary_no = $request->pty_contact_no;

    $posts = Party::where('pty_contact_no','=',$primary_no)->orWhere('telephone_number','=',$primary_no)->orWhere('p_mobile','=',$primary_no)->orWhere('pty_comp_contact','=',$primary_no)->count();
    if($posts == 0){
      echo "true";
    }else{
      echo "false";
    }
  }


  public function getMob1(Request $request){
    $mob_no = $request->phone;
    $conts = AllContact::where('mobile_no','=',$mob_no)->orWhere('phone0','=',$mob_no)->orWhere('phone1','=',$mob_no)->orWhere('phone2','=',$mob_no)->count();
    
    
    if($conts == 0){
      echo "true";
    }else{
      echo "false";
    }
  }

  public function getMoble(Request $request){
    $mob_no = $request->phone;
    $id = $request->id;
    // $post = AllContact::where('mobile_no','=',$mob_no)->orWhere('phone0','=',$mob_no)->orWhere('phone1','=',$mob_no)->orWhere('phone2','=',$mob_no)->where('id','!=',$id)->count();
    $post = AllContact::where('id','!=',$id)->where('mobile_no','=',$mob_no)->orWhere('phone0','=',$mob_no)->orWhere('phone1','=',$mob_no)->orWhere('phone2','=',$mob_no)->count();
    if($post == 0){
      echo "true";
    }else{
      echo "false";
    }
  }

  public function getMob2(Request $request){
    $phone0 = $request->phone0;
    // if(!empty($request->phone0)){
    //   $phone_count = AllContact::where('mobile_no','=',$phone0)->orWhere('phone0','=',$phone0)->orWhere('phone1','=',$phone0)->orWhere('phone2','=',$phone0)->count(); 
    //   if($phone_count == 0){
    //     echo "true";
    //   }else{
    //     echo "false";
    //   }
    // }else{
    //   echo "false";
    // }

    $conts = AllContact::where('phone0','=',$phone0)->orWhere('mobile_no','=',$phone0)->orWhere('phone1','=',$phone0)->orWhere('phone2','=',$phone0)->count();
    if($conts == 0){
      echo "true";
    }else{
      echo "false";
    }
  }

  public function getMob3(Request $request){
    $phone1 = $request->phone1;
    // if(!empty($request->phone1)){
    //   $phone_count = AllContact::where('mobile_no','=',$phone1)->orWhere('phone0','=',$phone1)->orWhere('phone1','=',$phone1)->orWhere('phone2','=',$phone1)->count(); 
    //   if($phone_count == 0){
    //     echo "true";
    //   }else{
    //     echo "false";
    //   }
    // }else{
    //   echo "false";
    // }

    $phone_count = AllContact::where('mobile_no','=',$phone1)->orWhere('phone0','=',$phone1)->orWhere('phone1','=',$phone1)->orWhere('phone2','=',$phone1)->count();
    if($phone_count == 0){
      echo "true";
    }else{
      echo "false";
    }

  }


  public function getMob4(Request $request){
    $phone2 = $request->phone2;
    // if(!empty($request->phone2)){
    //   $phone_count = AllContact::where('mobile_no','=',$phone2)->orWhere('phone0','=',$phone2)->orWhere('phone1','=',$phone2)->orWhere('phone2','=',$phone2)->count(); 
    //   if($phone_count == 0){
    //     echo "true";
    //   }else{
    //     echo "false";
    //   }
    // }else{
    //   echo "false";
    // }

    $phone_count = AllContact::where('mobile_no','=',$phone2)->orWhere('phone0','=',$phone2)->orWhere('phone1','=',$phone2)->orWhere('phone2','=',$phone2)->count();
    if($phone_count == 0){
      echo "true";
    }else{
      echo "false";
    }
  }

  public function getMobDet(Request $request){
    $post = DB::table('qr_all_contacts_tbl as allc')
      ->leftjoin('users as user','allc.assign_id','=','user.user_id')
      ->select('allc.full_name','allc.mobile_no','allc.id as cid','allc.assign_id','user.name as uname')
      ->where('allc.mobile_no','=',$request->mobile_no)
      ->first();

      if(isset($post)){
        return response()->json($post);
      }else{
        $data['type'] = '1';
        return response()->json($data);
      }
    
  }

  public function updateFilter(Request $request){
    // store request value into variable
    $filter_type = $request->selectv;
    $business_type = $request->business_type;
    $lead_status = $request->lead_status;
    $lead_stage = $request->lead_stage;
    $lead_priority = $request->lead_priority;
    $country = $request->country;
    $owner = $request->owner;
    $source = $request->source;
    $created = $request->created;
    $updated = $request->updated;
    $new_lead = $request->new_lead;
    $todays_followup = $request->todays_followup;
    $followup_update = $request->followup_update;
    $next_followup = $request->next_followup;
    $appointment = $request->appointment;
    $interested = $request->interested;
    $converted = $request->converted;
    $date_range = $request->date_range;
    $createby = $request->createby;
    $shortform = $request->shortform;
    $optinout = $request->optinout;
    $group_id = $request->groupID;

    // Check User is exists or Not
    $user = ContactListFilter::where('user_id','=',Auth::user()->user_id)->count();
    if($user > 0){
      $updateF = ContactListFilter::where('user_id','=',Auth::user()->user_id)->first();
      $updateF->user_id = Auth::user()->user_id;
      
      if($business_type == 1){
        $updateF->business_type = '1';
      }else{
        $updateF->business_type = '0';
      }
      if($lead_status == 1){
        $updateF->lead_status = '1';
      }else{
        $updateF->lead_status = '0';
      }
      if($lead_stage == 1){
        $updateF->lead_stage = '1';
      }else{
        $updateF->lead_stage = '0';
      }
      if($lead_priority == 1){
        $updateF->lead_priority = '1';
      }else{
        $updateF->lead_priority = '0';
      }
      if($country == 1){
        $updateF->country = '1';
      }else{
        $updateF->country = '0';
      }
      if($owner == 1){
        $updateF->owner = '1';
      }else{
        $updateF->owner = '0';
      }

      if($createby == 1){
        $updateF->create_by = '1';
      }else{
        $updateF->create_by = '0';
      }

      if($source == 1){
        $updateF->source = '1';
      }else{
        $updateF->source = '0';
      }
      if($created == 1){
        $updateF->created = '1';
      }else{
        $updateF->created = '0';
      }
      if($updated == 1){
        $updateF->updated = '1';
      }else{
        $updateF->updated = '0';
      }
      if($new_lead == 1){
        $updateF->new_lead = '1';
      }else{
        $updateF->new_lead = '0';
      }
      if($todays_followup == 1){
        $updateF->todays_followup = '1';
      }else{
        $updateF->todays_followup = '0';
      }
      if($followup_update == 1){
        $updateF->followup_update = '1';
      }else{
        $updateF->followup_update = '0';
      }
      if($next_followup == 1){
        $updateF->next_followup = '1';
      }else{
        $updateF->next_followup = '0';
      }
      if($appointment == 1){
        $updateF->appointment = '1';
      }else{
        $updateF->appointment = '0';
      }
      if($interested == 1){
        $updateF->interested = '1';
      }else{
        $updateF->interested = '0';
      }
      if($converted == 1){
        $updateF->converted = '1';
      }else{
        $updateF->converted = '0';
      }
      if($date_range == 1){
        $updateF->date_range = '1';
      }else{
        $updateF->date_range = '0';
      }

      if($shortform == 1){
        $updateF->shortform = '1';
      }else{
        $updateF->shortform = '0';
      }

      if($optinout == 1){
        $updateF->optinout = '1';
      }else{
        $updateF->optinout = '0';
      }

      if($group_id == 1){
        $updateF->group_id = '1';
      }else{
        $updateF->group_id = '0';
      }



      

      $updateF->save();
      return response()->json('success');

    }else{
      $createF = new ContactListFilter();
      $createF->user_id = Auth::user()->user_id;
      
      if($business_type == 1){
        $createF->business_type = '1';
      }else{
        $createF->business_type = '0';
      }
      if($lead_status == 1){
        $createF->lead_status = '1';
      }else{
        $createF->lead_status = '0';
      }
      if($lead_stage == 1){
        $createF->lead_stage = '1';
      }else{
        $createF->lead_stage = '0';
      }
      if($lead_priority == 1){
        $createF->lead_priority = '1';
      }else{
        $createF->lead_priority = '0';
      }
      if($country == 1){
        $createF->country = '1';
      }else{
        $createF->country = '0';
      }
      if($owner == 1){
        $createF->owner = '1';
      }else{
        $createF->owner = '0';
      }

      if($createby == 1){
        $createF->create_by = '1';
      }else{
        $createF->create_by = '0';
      }

      if($source == 1){
        $createF->source = '1';
      }else{
        $createF->source = '0';
      }
      if($created == 1){
        $createF->created = '1';
      }else{
        $createF->created = '0';
      }
      if($updated == 1){
        $createF->updated = '1';
      }else{
        $createF->updated = '0';
      }
      if($new_lead == 1){
        $createF->new_lead = '1';
      }else{
        $createF->new_lead = '0';
      }
      if($todays_followup == 1){
        $createF->todays_followup = '1';
      }else{
        $createF->todays_followup = '0';
      }
      if($followup_update == 1){
        $createF->followup_update = '1';
      }else{
        $createF->followup_update = '0';
      }
      if($next_followup == 1){
        $createF->next_followup = '1';
      }else{
        $createF->next_followup = '0';
      }
      if($appointment == 1){
        $createF->appointment = '1';
      }else{
        $createF->appointment = '0';
      }
      if($interested == 1){
        $createF->interested = '1';
      }else{
        $createF->interested = '0';
      }
      if($converted == 1){
        $createF->converted = '1';
      }else{
        $createF->converted = '0';
      }
      if($date_range == 1){
        $createF->date_range = '1';
      }else{
        $createF->date_range = '0';
      }

      if($shortform == 1){
        $createF->shortform = '1';
      }else{
        $createF->shortform = '0';
      }

      if($optinout == 1){
        $createF->optinout = '1';
      }else{
        $createF->optinout = '0';
      }

      if($group_id == 1){
        $createF->group_id = '1';
      }else{
        $createF->group_id = '0';
      }

      
      $createF->save();
      return response()->json('success');


    }
  }

  public function crmupdatefilter(Request $request){
    // store request value into variable
    $party = $request->fparty;
    $followup_st = $request->ffollowup;
    $recruit_st = $request->frecruitst;
    $musaned = $request->fmusaned;
    $service_type = $request->fservicet;
    $position = $request->fpostion;
    $source = $request->fsource;
    $care_off = $request->fcareoff;
    $additional = $request->fadditional;


    // Check User is exists or Not
    $user = Crmcandfilter::where('user_id','=',Auth::user()->user_id)->count();
    if($user > 0){
      $updateF = Crmcandfilter::where('user_id','=',Auth::user()->user_id)->first();
      $updateF->user_id = Auth::user()->user_id;
      
      if($party == 1){
        $updateF->party = '1';
      }else{
        $updateF->party = '0';
      }
      if($followup_st == 1){
        $updateF->followup_st = '1';
      }else{
        $updateF->followup_st = '0';
      }
      if($recruit_st == 1){
        $updateF->recruit_st = '1';
      }else{
        $updateF->recruit_st = '0';
      }
      if($musaned == 1){
        $updateF->musaned = '1';
      }else{
        $updateF->musaned = '0';
      }
      if($service_type == 1){
        $updateF->service_type = '1';
      }else{
        $updateF->service_type = '0';
      }
      if($position == 1){
        $updateF->position = '1';
      }else{
        $updateF->position = '0';
      }

      if($care_off == 1){
        $updateF->care_off = '1';
      }else{
        $updateF->care_off = '0';
      }

      if($source == 1){
        $updateF->source = '1';
      }else{
        $updateF->source = '0';
      }
      if($additional == 1){
        $updateF->additional = '1';
      }else{
        $updateF->additional = '0';
      }
    
      $updateF->save();
      return response()->json('success');

    }else{
      $createF = new Crmcandfilter();
      $createF->user_id = Auth::user()->user_id;
      
      if($party == 1){
        $createF->party = '1';
      }else{
        $createF->party = '0';
      }
      if($followup_st == 1){
        $createF->followup_st = '1';
      }else{
        $createF->followup_st = '0';
      }
      if($recruit_st == 1){
        $createF->recruit_st = '1';
      }else{
        $createF->recruit_st = '0';
      }
      if($musaned == 1){
        $createF->musaned = '1';
      }else{
        $createF->musaned = '0';
      }
      if($service_type == 1){
        $createF->service_type = '1';
      }else{
        $createF->service_type = '0';
      }
      if($position == 1){
        $createF->position = '1';
      }else{
        $createF->position = '0';
      }

      if($care_off == 1){
        $createF->care_off = '1';
      }else{
        $createF->care_off = '0';
      }

      if($source == 1){
        $createF->source = '1';
      }else{
        $createF->source = '0';
      }
      if($additional == 1){
        $createF->additional = '1';
      }else{
        $createF->additional = '0';
      }

      $createF->save();
      return response()->json('success');

    }
  }

  public function whatsappStorem(Request $request){
    $idsv = $request->idsv;
    $api_id = $request->input('api_id');
    // Find API details
    $api_det = Userwhatsappapi::find($api_id);

    // Message Data
    $ins = $api_det->instance_key;
    $api = $api_det->api_key;
    $url1 = $api_det->text_message_url;
    $url2 = $api_det->media_message_url;
    $urlPath = '/';
    $filePath = 'image/crm-contact/files';

    // Send Whatsapp Based on Template
    $wtemp = $request->wTemp;
    if($wtemp == 'template'){
      // get Template Details
      $getTemp = WhatsappTemplate::find($request->temp_id);
      // Get template details
      $getTempmsg = $getTemp->msgBody;
      $getTempfile = $getTemp->file;
      // Send Whatsapp Message
      foreach(explode(",",$idsv) as $ids){
        // Get all contact details
        $getCont = AllContact::find($ids);
        // Get Contact Details
        $getCname = $getCont->full_name;
        $getCphone1 = $getCont->mobile_no;
        $getCphone2 = $getCont->phone0;
        $getCphone3 = $getCont->phone1;
        $getCphone4 = $getCont->phone2;
        $getCwhts = $getCont->primary_contact_no;

        // Send Message based on Media File is blank or not
        $msgWhs = "Dear ".$getCname."\n".$getTempmsg;
        if($getTempfile != ''){
          $media = $urlPath.'/'.$filePath.'/'.$getTempfile;
          // Send Message on First Number
          if($getCphone1 !=''){
            $data = [
              'number' => $getCphone1,
              'msg' => $msgWhs,
              'media' => $media,
              "type" => "image",
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_URL, $url2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
          // Send Message on Second Number
          if($getCphone2 !=''){
            $data2 = [
              'number' => $getCphone2,
              'msg' => $msgWhs,
              'media' => $media,
              "type" => "image",
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
            curl_setopt($ch, CURLOPT_URL, $url2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
          // Send Message on Third Number
          if($getCphone3 !=''){
            $data3 = [
              'number' => $getCphone3,
              'msg' => $msgWhs,
              'media' => $media,
              "type" => "image",
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data3));
            curl_setopt($ch, CURLOPT_URL, $url2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
          // Send Message on First Number
          if($getCphone4 !=''){
            $data4 = [
              'number' => $getCphone4,
              'msg' => $msgWhs,
              'media' => $media,
              "type" => "image",
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data4));
            curl_setopt($ch, CURLOPT_URL, $url2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
          // Send Message on First Number
          if($getCwhts !=''){
            $data5 = [
              'number' => $getCwhts,
              'msg' => $msgWhs,
              'media' => $media,
              "type" => "image",
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data5));
            curl_setopt($ch, CURLOPT_URL, $url2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
        }else{
          // Send Message on First Number
          if($getCphone1 !=''){
            $data = [
              'number' => $getCphone1,
              'msg' => $msgWhs,
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_URL, $url1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
          // Send Message on Second Number
          if($getCphone2 !=''){
            $data2 = [
              'number' => $getCphone2,
              'msg' => $msgWhs,
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
            curl_setopt($ch, CURLOPT_URL, $url1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
          // Send Message on Third Number
          if($getCphone3 !=''){
            $data3 = [
              'number' => $getCphone3,
              'msg' => $msgWhs,
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data3));
            curl_setopt($ch, CURLOPT_URL, $url1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
          // Send Message on First Number
          if($getCphone4 !=''){
            $data4 = [
              'number' => $getCphone4,
              'msg' => $msgWhs,
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data4));
            curl_setopt($ch, CURLOPT_URL, $url1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
          // Send Message on First Number
          if($getCwhts !=''){
            $data5 = [
              'number' => $getCwhts,
              'msg' => $msgWhs,
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data5));
            curl_setopt($ch, CURLOPT_URL, $url1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
        }
        
      }
    }else{
      if ($request->hasFile('file')) {
        $file = $request->file('file');
        $file_count = File::files(base_path().'/public/image/crm-contact/files');
        $filecount = 0;
    
        if ($file_count !== false) {
          $filecount = count($file_count);
        }
        $file_exe = $file->getClientOriginalExtension();
        $name = $filecount . '.' . $file_exe;
    
        $file->move(base_path().'/public/image/crm-contact/files', $name);
        $cand_file = $name;
    
        // API Details
      } else {
        $cand_file = '';
      }  

      foreach(explode(",",$idsv) as $ids){
        // Get all contact details
        $getCont = AllContact::find($ids);

        // Get Contact Details
        $getCname = $getCont->full_name;
        $getCphone1 = $getCont->mobile_no;
        $getCphone2 = $getCont->phone0;
        $getCphone3 = $getCont->phone1;
        $getCphone4 = $getCont->phone2;
        $getCwhts = $getCont->primary_contact_no;

        // Send Message Data
        $msgWhs = "Dear ".$getCname."\n".$request->textBody;
        $url_path = url('/');
        $file_path = 'image/crm-contact/files';

        if($cand_file !=''){
          // $mediaPath = $url_path.'/'.$file_path.'/'.$cand_file;
          $mediaPath = url('/image/crm-contact/files/'.$cand_file);
          $type = "image";

          // Send Message on First Number
          if($getCphone1 !=''){
            $data = [
              'number' => $getCphone1,
              'msg' => $msgWhs,
              'media' => $mediaPath,
              "type" => "image",
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_URL, $url2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
          // Send Message on Second Number
          if($getCphone2 !=''){
            $data2 = [
              'number' => $getCphone2,
              'msg' => $msgWhs,
              'media' => $mediaPath,
              "type" => "image",
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
            curl_setopt($ch, CURLOPT_URL, $url2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
          // Send Message on Third Number
          if($getCphone3 !=''){
            $data3 = [
              'number' => $getCphone3,
              'msg' => $msgWhs,
              'media' => $mediaPath,
              "type" => "image",
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data3));
            curl_setopt($ch, CURLOPT_URL, $url2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
          // Send Message on First Number
          if($getCphone4 !=''){
            $data4 = [
              'number' => $getCphone4,
              'msg' => $msgWhs,
              'media' => $mediaPath,
              "type" => "image",
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data4));
            curl_setopt($ch, CURLOPT_URL, $url2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
          // Send Message on First Number
          if($getCwhts !=''){
            $data5 = [
              'number' => $getCwhts,
              'msg' => $msgWhs,
              'media' => $mediaPath,
              "type" => "image",
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data5));
            curl_setopt($ch, CURLOPT_URL, $url2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
        }else{

          // Send Message on First Number
          if($getCphone1 !=''){
            $data = [
              'number' => $getCphone1,
              'msg' => $msgWhs,
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_URL, $url1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
          // Send Message on Second Number
          if($getCphone2 !=''){
            $data2 = [
              'number' => $getCphone2,
              'msg' => $msgWhs,
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
            curl_setopt($ch, CURLOPT_URL, $url2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
          // Send Message on Third Number
          if($getCphone3 !=''){
            $data3 = [
              'number' => $getCphone3,
              'msg' => $msgWhs,
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data3));
            curl_setopt($ch, CURLOPT_URL, $url1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
          // Send Message on First Number
          if($getCphone4 !=''){
            $data4 = [
              'number' => $getCphone4,
              'msg' => $msgWhs,
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data4));
            curl_setopt($ch, CURLOPT_URL, $url1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }
          // Send Message on First Number
          if($getCwhts !=''){
            $data5 = [
              'number' => $getCwhts,
              'msg' => $msgWhs,
              "instance" => $ins,
              "apikey" => $api
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data5));
            curl_setopt($ch, CURLOPT_URL, $url1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
          }

        }

      }

    }




    Session::flash('success','Whatsapp message send successfully!');
    return redirect()->back();

  }


  public function campaignWhatsapp(){
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm/campwhatsapp',['pageConfigs' => $pageConfigs]);
  }

  public function storeRegarding(Request $request){

    extract($_POST);
    $chk = Regarding::where('name','=',$name)->first(); 
    if($chk){
      return response()->json('0');
    }else{
      $c = new Regarding();
      $c->name = $name;
      $c->save();
      return response()->json($c->id);
    }

  }

  public function storeTradesitecenter(Request $request){
    extract($_POST);
    $chk = Tradeitecenter::where('name','=',$name)->first();
    if($chk){
      return response()->json('0');
    }else{
      $c = new Tradeitecenter();
      $c->name = $name;
      $c->save();
      return response()->json($c->id);
    }
  }

  public function findSource(Request $request){
    $id = $request->id;
    $post = Source::find($id);
    return response()->json($post);
  }

  public function findStagel(Request $request){
    $id = $request->id;
    $post = Lstage::where('lcs_id','=',$id)->get();
    return response()->json($post);
  }

  public function contact_lists_json(Request $request){
    
    $perms = AccessPermissionModule2::where('user_id','=',Auth::user()->user_id)->first();

    if (Auth::user()->user_type == 1 || (isset($perms) && $perms->full_access == 1)) {

      // use before 20.03.2023
      $data_con = DB::table('qr_all_contacts_tbl as cont')
        ->leftjoin('users as user','cont.assign_id','=','user.user_id')
        ->leftjoin('qr_lifecycle_stage as lcs','cont.lcs_id','=','lcs.id')
        ->leftjoin('lstages as ls','cont.ls_id','=','ls.id')
        ->leftjoin('qr_country as country','cont.country_id','=','country.country_id')
        ->leftjoin('sources as source','cont.source_id','=','source.id')
        ->select('cont.*','user.name as username','lcs.name as lcsname','ls.name as lsname','country.country_name','source.name as sourcename')
        ->get();

      // use after 20.03.2023
      // $data_con = DB::table('qr_all_contacts_tbl as cont')
      // ->leftjoin('users as user','cont.assign_id','=','user.user_id')
      // ->leftjoin('qr_lifecycle_stage as lcs','cont.lcs_id','=','lcs.id')
      // ->leftjoin('lstages as ls','cont.ls_id','=','ls.id')
      // ->leftjoin('qr_country as country','cont.country_id','=','country.country_id')
      // ->leftjoin('sources as source','cont.source_id','=','source.id')
      // ->select('cont.id','cont.full_name','cont.company_name','cont.lead_prority','cont.lead_type','cont.city','cont.followup_date','cont.created_at','cont.updated_at','cont.mobile_no','cont.phone0','cont.phone1','cont.phone2','cont.primary_contact_no','cont.email','cont.email0','cont.email1','cont.email2','cont.regarding','cont.user_id','user.name as username','lcs.name as lcsname','ls.name as lsname','country.country_name','source.name as sourcename')
      // ->get();


      // $data_con = DB::table('qr_all_contacts_tbl as cont')
      // ->leftjoin('users as user','cont.assign_id','=','user.user_id')
      // ->select('cont.*','user.name')
      // ->get();  
    }elseif (Auth::user()->user_type == 2 || (isset($perms) && $perms->full_access == 0)) {
      
      if (isset($perms)) {
        
        if ($perms->r_crm_ac == 1) {

          $data_con = DB::table('qr_all_contacts_tbl as cont')
          ->leftjoin('users as user','cont.assign_id','=','user.user_id')
          ->leftjoin('qr_lifecycle_stage as lcs','cont.lcs_id','=','lcs.id')
          ->leftjoin('lstages as ls','cont.ls_id','=','ls.id')
          ->leftjoin('qr_country as country','cont.country_id','=','country.country_id')
          ->leftjoin('sources as source','cont.source_id','=','source.id')
          ->select('cont.*','user.name as username','lcs.name as lcsname','ls.name as lsname','country.country_name','source.name as sourcename')
          ->where('cont.contact_dl_status','=','0')
          ->orWhere('cont.assign_id','=',Auth::user()->user_id)
          ->get();

          // $data_con = DB::table('qr_all_contacts_tbl as cont')
          //   ->leftjoin('users as user','cont.assign_id','=','user.user_id')
          //   ->select('cont.*','user.name')
          //   ->where('cont.contact_dl_status','=','0')
          //   ->orWhere('cont.assign_id','=',Auth::user()->user_id)
          //   ->get();  
        }else{

          $data_con = DB::table('qr_all_contacts_tbl as cont')
          -> leftjoin('users as user','cont.assign_id','=','user.user_id')
          ->leftjoin('qr_lifecycle_stage as lcs','cont.lcs_id','=','lcs.id')
          ->leftjoin('lstages as ls','cont.ls_id','=','ls.id')
          ->leftjoin('qr_country as country','cont.country_id','=','country.country_id')
          ->leftjoin('sources as source','cont.source_id','=','source.id')
          ->select('cont.*','user.name as username','lcs.name as lcsname','ls.name as lsname','country.country_name','source.name as sourcename')
          ->where('cont.user_id','=',Auth::user()->user_id)
          ->orWhere('cont.public_st','=','1')
          ->orWhere('cont.assign_id','=',Auth::user()->user_id)
          ->where('cont.contact_dl_status','=','0')
          ->get();

          // $data_con = DB::table('qr_all_contacts_tbl as cont')
          // ->leftjoin('users as user','cont.assign_id','=','user.user_id')
          //   ->select('cont.*','user.name')
          //   ->where('cont.user_id','=',Auth::user()->user_id)
          //   ->orWhere('cont.public_st','=','1')
          //   ->orWhere('cont.assign_id','=',Auth::user()->user_id)
          //   ->where('cont.contact_dl_status','=','0')
          //   ->get();
        }

      }else{

        $data_con = DB::table('qr_all_contacts_tbl as cont')
        -> leftjoin('users as user','cont.assign_id','=','user.user_id')
        ->leftjoin('qr_lifecycle_stage as lcs','cont.lcs_id','=','lcs.id')
        ->leftjoin('lstages as ls','cont.ls_id','=','ls.id')
        ->leftjoin('qr_country as country','cont.country_id','=','country.country_id')
        ->leftjoin('sources as source','cont.source_id','=','source.id')
        ->select('cont.*','user.name as username','lcs.name as lcsname','ls.name as lsname','country.country_name','source.name as sourcename')
        ->where('cont.user_id','=',Auth::user()->user_id)
        ->orWhere('cont.public_st','=','1')
        ->orWhere('cont.assign_id','=',Auth::user()->user_id)
        ->where('cont.contact_dl_status','=','0')
        ->get();


        // $data_con = DB::table('qr_all_contacts_tbl as cont')
        //   ->leftjoin('users as user','cont.assign_id','=','user.user_id')
        //   ->select('cont.*','user.name')
        //   ->where('cont.user_id','=',Auth::user()->user_id)
        //   ->orWhere('cont.public_st','=','1')
        //   ->orWhere('cont.assign_id','=',Auth::user()->user_id)
        //   ->where('cont.contact_dl_status','=','0')
        //   ->get();
      }
    }

    

      // $data['data'] = $data_con;
      // return response()->json($data);

      return DataTables::of($data_con)->toJson();
  }

  public function daterange(Request $request){
    $perms = AccessPermissionModule2::where('user_id','=',Auth::user()->user_id)->first();
    
    $dates = array($request->fromdate,$request->todate);
    if (Auth::user()->user_type == 1 || (isset($perms) && $perms->full_access == 1)){
      $data_con = DB::table('qr_all_contacts_tbl as cont')
      -> leftjoin('users as user','cont.assign_id','=','user.user_id')
      ->leftjoin('qr_lifecycle_stage as lcs','cont.lcs_id','=','lcs.id')
      ->leftjoin('lstages as ls','cont.ls_id','=','ls.id')
      ->leftjoin('qr_country as country','cont.country_id','=','country.country_id')
      ->leftjoin('sources as source','cont.source_id','=','source.id')
      ->select('cont.*','user.name as username','lcs.name as lcsname','ls.name as lsname','country.country_name','source.name as sourcename')
      ->wherebetween('cont.created_at',$dates)
      ->get();
    }elseif(Auth::user()->user_type == 2 || (isset($perms) && $perms->full_access == 0)){}

    $pageConfigs = ['pageHeader' => false];
    return view('content.crm.contact-lists-serach',['pageConfigs' => $pageConfigs,'data_con' => $data_con]);
  }

  public function contshortstore(Request $request){
    // dd($request);
    $getGroups = AllContact::where('group_id','!=','')->where('assign_id','=',Auth::user()->user_id)->get();
    // dd($getGroups);

    $conts = new AllContact();
    $conts->lead_type = 'Unknown';
    $conts->full_name = $request->full_name;
    $conts->mobile_no = $request->input('phone') ? $request->input('phone') : '';
    $conts->lcs_id = 4;
    $conts->ls_id = 2;
    $conts->owner_id = Auth::user()->user_id;
    $conts->user_id = Auth::user()->user_id;
    $conts->assign_id = Auth::user()->user_id;
    if($request->assoc_number_id != ''){
      $conts->optinout = true;
      $conts->assoc_number_id = implode(",",$request->assoc_number_id);
    }else{
      $conts->optinout = false;
    }

    if($getGroups->count() > 0){
      // Associate as per group
      $groupMs = Groupm::orderBy('name','ASC')->get();

      // dd($groupMs);

      $ic = 0;
      foreach($groupMs as $groupM){
        if($ic < 1){
          $getAllc = AllContact::where('group_id','=',$groupM->id)->where('assign_id','=',Auth::user()->user_id)->count();
          // check if maximaum limit is greater than specific count
          if($getAllc < $groupM->max_limit){
            $conts->group_id = $groupM->id;
            $ic++;
          }

        }
      }      
    }else{
      $groupM = Groupm::orderBy('name','ASC')->first();
      if($groupM){
        $conts->group_id = $groupM->id;
      }
    }

    $conts->save();

    $get_last_id = $conts->id;

    $acPost = new AssociateContact();
    $acPost->contact_id = $get_last_id;
    $acPost->user_id = Auth::user()->user_id;
    $acPost->save();

    Session::flash('success','All Contacts Added Successfully');
    return redirect()->back();

  }

  public function contacts_store(Request $request)
  {
    // dd($request);
    $getGroups = AllContact::where('group_id','!=','')->where('assign_id','=',$request->assign_id)->get();

    $conts = new AllContact();
    $conts->lead_type = $request->lead_type;
    $conts->full_name = $request->full_name;
    $conts->mobile_no = $request->input('phone') ? $request->input('phone') : '';
    $conts->email = $request->input('email') ? $request->input('email') : '';
    $conts->phone0 = $request->input('phone0') ? $request->input('phone0') : '';
    $conts->email0 = $request->input('email0') ? $request->input('email0') : '';
    $conts->phone1 = $request->input('phone1') ? $request->input('phone1') : '';
    $conts->email1 = $request->input('email1') ? $request->input('email1') : '';
    $conts->phone2 = $request->input('phone2') ? $request->input('phone2') : '';
    $conts->email2 = $request->input('email2') ? $request->input('email2') : '';
    
    // $conts->calling_from = $request->calling_from;
    // $conts->state = $request->state;

    // Get Regarding Name
    // $getRegnames = Regarding::whereIn('id',$request->regarding)->get()->toArray();
    
    // dd($getRegnames);

    // Find Source Name
    $sourceFind = Source::find($request->source2);
    
    $conts->assign_id = $request->assign_id;
    $conts->source = $sourceFind->name;
    $conts->source_id = $request->source2;
    $conts->user_id = Auth::user()->user_id;
    $conts->lcs_id = 4;
    $conts->indust_id = $request->input('indust_id') ? $request->input('indust_id') :'';
    $conts->company_name = $request->input('company_name') ? $request->input('company_name') :'';
    $conts->job_title = $request->input('job_title') ? $request->input('job_title') :'';
    $conts->job_desg = $request->input('job_desg') ? $request->input('job_desg') :'';
    $conts->descr = $request->input('descr') ? $request->input('descr') :'';
    $conts->lcs_id = $request->input('lcs_id') ? $request->input('lcs_id') :'';
    $conts->ls_id = $request->input('ls_id') ? $request->input('ls_id') :'';
    $conts->expected_cid = $request->input('expected_cid') ? $request->input('expected_cid') :'';
    $conts->full_address = $request->input('full_address') ? $request->input('full_address') :'';
    
    if($getGroups->count() > 0){
      // Associate as per group
      $groupMs = Groupm::orderBy('name','ASC')->get();
      $ic = 0;
      foreach($groupMs as $groupM){
        if($ic < 1){
          $getAllc = AllContact::where('group_id','=',$groupM->id)->where('assign_id','=',$request->assign_id)->count();
          // check if maximaum limit is greater than specific count
          if($getAllc < $groupM->max_limit){
            $conts->group_id = $groupM->id;
            $ic++;
          }
        }
      }
    }else{
      $groupM = Groupm::orderBy('name','ASC')->first();
      if($groupM){
        $conts->group_id = $groupM->id;
      }
    }

    if($request->lead_type == 'Company'){
      $conts->country_id = $request->country_id;
      $conts->city = $request->city;
    }else{
      $conts->country_id = $request->country_id2;
      $conts->city = $request->city2;
    }

    if($request->lead_type == 'Associate with office'){
      $conts->office_name = $request->input('office_name') ? $request->input('office_name') :'';
    }


    


    if($request->lead_type == 'Direct Wakala Candidate' || $request->lead_type == 'Regular Wakala Party' || $request->lead_type == 'Agent without office' || $request->lead_type == 'Associate with office' || $request->lead_type == 'Unknown'){

      $conts->regarding = $request->input('regarding') ? implode(",",$request->input('regarding')) : '';
      
    }

    if($request->lead_type == 'Trade site Center'){
      $conts->tradesitecenter = $request->input('tradecenter') ? implode(",",$request->input('tradecenter')) : '';
    }


    // upload optinout
    $conts->optinout = $request->optinout;
    if($request->assoc_number_id != ''){
      $conts->assoc_number_id = implode(",",$request->assoc_number_id);
    }

    $conts->save();
  
    // create queue if welcome message checked
    if($request->welcomemsg == 1){
      $getWelAPI =  Userwhatsappapi::where('staff_id','=',$conts->assign_id)->where('api_for','=','Marketing')->first();
      dispatch(new Welcomemsg($conts,$getWelAPI))->onQueue('mediumps');
    }

    // insert into ContactRegarding Tab
    $get_last_id = $conts->id;
    $regardings = $request->input('regarding');
    $tradecenters = $request->input('tradecenter');

    if($request->lead_type == 'Direct Wakala Candidate' || $request->lead_type == 'Regular Wakala Party' || $request->lead_type == 'Agent without office' || $request->lead_type == 'Associate with office' || $request->lead_type == 'Unknown'){
      foreach($regardings as $regard){
        // get Regarding ID
        $regNameD = Regarding::where('name','=',$regard)->first();
        $conreg = new ContactRegarding();
        $conreg->cont_id = $get_last_id;
        $conreg->regarding_id = $regNameD->id;
        $conreg->save();
      }
    }

    if($request->lead_type == 'Trade site Center'){
      foreach($tradecenters as $tradec){
        $conreg = new ContactTradesitecenter();
        $conreg->cont_id = $get_last_id;
        $conreg->tradesc_id = $tradec;
        $conreg->save();
      }
    }

    // Add in respective table
    if($request->lead_type == 'Job seeker'){
      $jbPost = new JobSeekerContact();
      $jbPost->contact_id = $get_last_id;
      $jbPost->user_id = Auth::user()->user_id;
      $jbPost->save();
    }
    if($request->lead_type == 'Company'){
      $ccPost = new ContactCompany();
      $ccPost->contact_id = $get_last_id;
      $ccPost->user_id = Auth::user()->user_id;
      $ccPost->save();
    }
    if($request->lead_type == 'Unknown' || $request->lead_type == 'Direct Wakala Candidate' || $request->lead_type == 'Agent without office' || $request->lead_type == 'Associate with office' || $request->lead_type == 'Trade site Center'){
      $acPost = new AssociateContact();
      $acPost->contact_id = $get_last_id;
      $acPost->user_id = Auth::user()->user_id;
      $acPost->save();
    }


    // dd($get_last_id);

    Session::flash('success','All Contacts Added Successfully');
    return redirect()->back();

  }

  public function getArrayReg(Request $request){
      $id = $request->input('id');
      $data = ContactRegarding::where('cont_id','=',$id)->get();
      return response()->json($data);
  }

  public function storeFbl(Request $request)
  {
    $id = $request->input('cont_id');
    $cont_dt = AllContact::find($id);
    $cont_dt->fb_link = $request->input('fb_link');
    $cont_dt->save();
    Session::flash('success','Facebook Profile Link Updated');
    return redirect()->back();
  }

  public function storetwl(Request $request)
  {
    $id = $request->input('cont_id');
    $cont_dt = AllContact::find($id);
    $cont_dt->tw_link = $request->input('tw_link');
    $cont_dt->save();
    Session::flash('success','Twitter Link Updated');
    return redirect()->back();
  }

  public function storelinkedinl(Request $request)
  {
    $id = $request->input('cont_id');
    $cont_dt = AllContact::find($id);
    $cont_dt->linkedin_link = $request->input('linkedin_link');
    $cont_dt->save();
    Session::flash('success','LinkedIn Link Updated');
    return redirect()->back();

  }

  public function storeWhatsapp(Request $request){
    $id = $request->input('cont_id');
    $cont_d = AllContact::find($id);
    $cont_d->primary_contact_no = $request->input('primary_contact_no');
    $cont_d->save();
    Session::flash('success','Whatsapp Number Updated!');
    return redirect()->back();
  }

  public function contacts_view($id){

    $cont_dt = AllContact::find($id);

    $pageConfigs = ['pageHeader' => false];
    return view('content.crm.contacts-view',['pageConfigs' => $pageConfigs,'cont_dt' => $cont_dt]);
  }

  public function partyPhoto(Request $request){
    $post = Party::find($request->pty_id);
    $pphoto = $post->photo;

    $randomStr = Str::random(16);
    $curr_date = date('Y-m-d-h-i-s');
    $newFilename = $randomStr.'-ID'.$request->pty_id.'-'.$curr_date;

    if ($request->hasFile('photo')) {
      $file = $request->file('photo');
      $file_count = File::files(base_path().'/public/image/service-master');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      // $name = $filecount . '.' . $file_exe;
      $name = $newFilename . '.' . $file_exe;

      $file->move(base_path().'/public/image/service-master', $name);
      // $file->move(base_path().'/public/image/service-candidate', $name);
      $candidate_photo = $name;

    } else {
      $candidate_photo = $pphoto;
    }

    $post->photo = $candidate_photo;
    $post->save();

    Session::flash('success', 'Party Photo updated successfully !');
    return redirect()->back();
  }

  public function conversationStore(Request $request)
  {
    $post = new ContactConversation();
    $post->contact_id = $request->id;
    $post->contype_id = $request->conver_on;
    $post->messageText = $request->conversation;
    $post->user_id = Auth::user()->user_id;
    $post->save();

    Session::flash('success','Conversation added successfully!');
    return redirect()->back();
  }

  public function filesStore(Request $request){
    if ($request->hasFile('cand_file')) {
      $file = $request->file('cand_file');
      $file_count = File::files(base_path().'/public/image/crm-contact/files');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/crm-contact/files', $name);
      $cand_file = $name;

    } else {
      $cand_file = '';
    }

    $cont_id =  $request->input('cont_id');
    $post = new ContactsFile();
    $post->contact_id = $cont_id;
    $post->file = $cand_file;
    $post->doc_name = $request->doc_name;
    $post->user_id = Auth::user()->user_id;
    $post->save();

    Session::flash('success','File Uploaded successfully!');
    return redirect()->back();
  }

  public function get_company(Request $request)
  {
    $comp= Company::find($request->input('id'));  

    return response()->json($comp);
  }

  public function company_store(Request $request)
  {

    $comp_chk= Company::find($request->input('comp_id'));

    if(($comp_chk->comp_name_ar=='')){

      $comp =   Company::find($request->input('comp_id'));
      $comp->comp_name_ar =  $request->input('comp_name_ar') ? $request->input('comp_name_ar') : '';
      $comp->industry = $request->input('industry') ? $request->input('industry') : '';
      $comp->website = $request->input('website') ? $request->input('website') : '';
      $comp->city = $request->input('city') ? $request->input('city') : '';
      $comp->country = $request->input('country') ? $request->input('country') : '';
      
      $comp->address = $request->input('address') ? $request->input('address') : '';
      $comp->fb_link = $request->input('fb_link') ? $request->input('fb_link') : '';
      $comp->tw_link = $request->input('tw_link') ? $request->input('tw_link') : '';
      $comp->lnk_link = $request->input('lnk_link') ? $request->input('lnk_link') : '';
      $comp->save();
    }
    /**/

    $cont =  new CompanyContact();
    $cont->comp_id = $request->input('comp_id');
    $cont->full_name = $request->input('full_name') ? $request->input('full_name') : '';
    $cont->email = $request->input('email') ? implode(",", $request->input('email')) : '';
    $cont->phone = $request->input('phone') ? implode(",", $request->input('phone')) : '';
    // $cont->stage = $request->input('stage') ? $request->input('stage') : '';
    $cont->city2 = $request->input('city2') ? $request->input('city2') : '';
    $cont->fb_link2 = $request->input('fb_link2') ? $request->input('fb_link2') : '';
    $cont->tw_link2 = $request->input('tw_link2') ? $request->input('tw_link2') : '';
    $cont->lnk_link2 = $request->input('lnk_link2') ? $request->input('lnk_link2') : '';
    $cont->lifecy_stage = $request->input('lifecy_stage') ? $request->input('lifecy_stage') : '';
    $cont->lead_stage = $request->input('lead_stage') ? $request->input('lead_stage') : '';
    $cont->staff = $request->input('staff') ? $request->input('staff') : '';
    $cont->source = $request->input('source') ? $request->input('source') : '';
    $cont->save();

    Session::flash('success', 'Company created successfully !');
    return redirect('master/company/list');
  }

  public function company_update(Request $request)
  {


  $comp = Company::find($request->input('comp_id'));
  $comp->comp_name =  $request->input('comp_name') ? $request->input('comp_name') : '';
  $comp->comp_name_ar =  $request->input('comp_name_ar') ? $request->input('comp_name_ar') : '';
  $comp->industry = $request->input('industry') ? $request->input('industry') : '';
  $comp->website = $request->input('website') ? $request->input('website') : '';
  $comp->city = $request->input('city') ? $request->input('city') : '';
  $comp->country = $request->input('country') ? $request->input('country') : '';
  $comp->address = $request->input('address') ? $request->input('address') : '';
  $comp->fb_link = $request->input('fb_link') ? $request->input('fb_link') : '';
  $comp->tw_link = $request->input('tw_link') ? $request->input('tw_link') : '';
  $comp->lnk_link = $request->input('lnk_link') ? $request->input('lnk_link') : '';
  $comp->tele_phone = $request->input('tele_phone') ? $request->input('tele_phone') : '';
  $comp->save();

  Session::flash('success', 'Compnay updated successfully !');
  return redirect()->back();
  }

  public function contact_store(Request $request)
  {

    // dd($request);

    $comp_chk= Company::find($request->input('comp_id'));

    $validator = Validator::make($request->all(), [
      'comp_id' => 'required|integer'
    ]);

    if ($validator->fails()) {
      Session::flash('error', 'Please select company !');
      return redirect('master/contact/list')->withErrors($validator)->withInput();
    }

    if(isset($comp_chk->industry)){
      $comp = Company::find($request->input('comp_id'));
      $comp->comp_name_ar =  $request->input('comp_name_ar') ? $request->input('comp_name_ar') : '';
      $comp->industry = $request->input('industry') ? $request->input('industry') : '';
      $comp->website = $request->input('website') ? $request->input('website') : '';
      $comp->city = $request->input('city') ? $request->input('city') : '';
      $comp->country = $request->input('country') ? $request->input('country') : '';
      $comp->address = $request->input('address') ? $request->input('address') : '';
      $comp->fb_link = $request->input('fb_link') ? $request->input('fb_link') : '';
      $comp->tw_link = $request->input('tw_link') ? $request->input('tw_link') : '';
      $comp->lnk_link = $request->input('lnk_link') ? $request->input('lnk_link') : '';
      $comp->tele_phone = $request->input('tele_phone') ? $request->input('tele_phone') : '';
      $comp->save();
    }
  
    $cont =  new CompanyContact();

    $cont->comp_id = $request->input('comp_id');
    $cont->full_name = $request->input('full_name') ? $request->input('full_name') : '';
    $cont->designation = $request->input('designation') ? $request->input('designation') : '';
    $cont->email = $request->input('email') ? implode(",", $request->input('email')) : '';
    $cont->phone = $request->input('phone') ? implode(",", $request->input('phone')) : '';
    $cont->lifecy_stage = $request->input('lifecy_stage') ? $request->input('lifecy_stage') : '';
    $cont->lead_stage = $request->input('lead_stage') ? $request->input('lead_stage') : '';
    $cont->staff = $request->input('staff') ? $request->input('staff') : '';
    $cont->source = $request->input('source') ? $request->input('source') : '';
    $cont->city2 = $request->input('city2') ? $request->input('city2') : '';
    $cont->fb_link2 = $request->input('fb_link2') ? $request->input('fb_link2') : '';
    $cont->tw_link2 = $request->input('tw_link2') ? $request->input('tw_link2') : '';
    $cont->lnk_link2 = $request->input('lnk_link2') ? $request->input('lnk_link2') : '';
    $cont->save();
    Session::flash('success', 'Contact created successfully !');
    return redirect('master/contact/list');
  }

  public function store_company_name(Request $request)
  {
    extract($_POST);
    $chk = Company::where('comp_name','=',$name)->first(); 
    if($chk){
      return response()->json('0');
    }else{
      $c = new Company();
      $c->comp_name = $name;
      $c->save();
      return response()->json($c->id);
    }
    

    
  }


  public function company_delete(Request $request)
  {
    extract($_POST);
    Company::find($id)->delete();;
    CompanyContact::where('comp_id','=',$id)->delete();
    Session::flash('success', 'Company deleted successfully !');
    return redirect('master/company/list');
  }

  public function contact_delete(Request $request)
  {
    extract($_POST);

    $user_type = Auth::user()->user_type;

    if($user_type == 1){
      AllContact::find($id)->delete();
      Session::flash('success', 'Contact permenantly deleted successfully !');
    return redirect('master/contacts/list');
    }else{
      $post = AllContact::find($id);
      $post->contact_dl_status = 1;
      $post->deleteby_id = Auth::user()->user_id;
      $post->save();
      Session::flash('success', 'Contact deleted successfully !');
    return redirect('master/contacts/list');
    }

    // CompanyContact::find($id)->delete();;

    
  }

  public function company_edit($id)
  {

    $comp =  Company::find($id);
    $pageConfigs = ['pageHeader' => false];   
    return view('content/crm/company-edit',['comp' => $comp, 'pageConfigs' => $pageConfigs]);
  }

  public function contact_edit($id)
  {

    $cont =  CompanyContact::find($id);
    $pageConfigs = ['pageHeader' => false];   
    return view('content/crm/contact-edit',['cont' => $cont, 'pageConfigs' => $pageConfigs]);
  }

  public function company_view($id)
  {


  $comp =  DB::table('qr_company as comp')
  // ->leftjoin('qr_staff_tbl as st', 'st.staff_id','=','comp.staff')
  ->leftjoin('qr_industry as ind', 'ind.id','=','comp.industry')
  ->leftjoin('qr_country as co', 'co.country_id','=','comp.country')
  ->leftjoin('qr_city as ci', 'ci.city_id','=','comp.city')
  ->select('comp.*','comp.id as cid','ind.name as indname','co.country_name','ci.city_name')
  ->orderBy('cid','DESC')
  ->where('comp.id','=',$id)
  ->first();


  $pageConfigs = ['pageHeader' => false];   
  return view('content/crm/company-view',['comp' => $comp,'pageConfigs' => $pageConfigs]);
  }

  public function contact_view($id)
  {

    $comp =  DB::table('qr_company_contact as cont')
    ->leftjoin('qr_company as comp', 'cont.comp_id','=','comp.id')
    ->leftjoin('qr_staff_tbl as st', 'st.staff_id','=','cont.staff')
    ->leftjoin('qr_industry as ind', 'ind.id','=','comp.industry')
    ->leftjoin('qr_lead_stage as ld_stage', 'ld_stage.id','=','cont.lead_stage')
    ->leftjoin('qr_lifecycle_stage as lf_stage', 'lf_stage.id','=','cont.lifecy_stage')
    ->leftjoin('qr_city as ci', 'ci.city_id','=','comp.city')
    ->leftjoin('qr_country as co', 'co.country_id','=','comp.country')
    ->select('comp.*','cont.*','cont.id as cont_id','ld_stage.name as lead_stage_name','lf_stage.name as lifecy_stage_name','comp.id as cid','comp.city as comp_city','cont.id as cont_id','st.*','ind.name as indname','co.country_name','ci.city_name','ci.city_id')
    ->where('cont.id','=',$id)
    ->first();
    // dd($comp);
    $all_note = NoteContact::where('cont_id','=',$id)->get();
    $all_appoint = AppointContact::where('cont_id','=',$id)->get();
    $all_contact = TaskContact::where('cont_id','=',$id)->get();

    $all_files = FilesContact::where('cont_id','=',$id)->get();
  
    $pageConfigs = ['pageHeader' => false];   
    return view('content/crm/contact-view',['comp' => $comp,'note_data' => $all_note,'app_data' => $all_appoint,'task_data' => $all_contact,'file_data' => $all_files,'pageConfigs' => $pageConfigs]);
  
  }

  public function contact_update(Request $request)
  {

  $comp = Company::find($request->input('comp_edit_id'));
  $comp->comp_name_ar =  $request->input('comp_name_ar') ? $request->input('comp_name_ar') : '';
  $comp->industry = $request->input('industry') ? $request->input('industry') : '';
  $comp->website = $request->input('website') ? $request->input('website') : '';
  $comp->city = $request->input('city') ? $request->input('city') : '';
  $comp->country = $request->input('country') ? $request->input('country') : '';
  $comp->address = $request->input('address') ? $request->input('address') : '';
  $comp->fb_link = $request->input('fb_link') ? $request->input('fb_link') : '';
  $comp->tw_link = $request->input('tw_link') ? $request->input('tw_link') : '';
  $comp->lnk_link = $request->input('lnk_link') ? $request->input('lnk_link') : '';
  $comp->tele_phone = $request->input('tele_phone') ? $request->input('tele_phone') : '';
  $comp->save();

  
  $cont =  CompanyContact::find($request->input('cont_edit_id'));

  $cont->comp_id = $request->input('comp_id');
  $cont->full_name = $request->input('full_name') ? $request->input('full_name') : '';
  $cont->designation = $request->input('designation') ? $request->input('designation') : '';
  $cont->email = $request->input('email') ? implode(",", $request->input('email')) : '';
  $cont->phone = $request->input('phone') ? implode(",", $request->input('phone')) : '';

  $cont->city2 = $request->input('city2') ? $request->input('city2') : '';
  $cont->fb_link2 = $request->input('fb_link2') ? $request->input('fb_link2') : '';
  $cont->tw_link2 = $request->input('tw_link2') ? $request->input('tw_link2') : '';
  $cont->lnk_link2 = $request->input('lnk_link2') ? $request->input('lnk_link2') : '';
  $cont->other_link = $request->input('other_link') ? $request->input('other_link') : '';
  $cont->save();


  Session::flash('success', 'Contact updated successfully !');
  return redirect('master/contact/view/'.$request->input('cont_edit_id'));
  }

  public function industry_list()
  {
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm-master/industry-list', ['pageConfigs' => $pageConfigs]);
  }

  public function industry_list_json()
  {
    $ind = Industry::all();
    $data['data'] = $ind;
    return response()->json($data);
  }

  public function industry_store(Request $request)
  {
    extract($_POST);
    $ind = new Industry();
    $ind->name = $name ? $name :'';
    $ind->user_id = Auth::user()->user_id;
    $ind->save();
    Session::flash('success', 'Industry created successfully !');
    return redirect('master/industry');
    
  }
  public function industry_edit(Request $request)
  {
    extract($_POST);
    $ind =  Industry::find($id);
    return response()->json($ind);
    
  }
  public function industry_update(Request $request)
  {
    extract($_POST);
    $ind =  Industry::find($id);
    $ind->name = $name ?$name :'';
    $ind->save();
    Session::flash('success', 'Industry updated successfully !');
    return redirect('master/industry');
    
  }
  public function industry_delete($id)
  {  
  $ind =  Industry::find($id);
  $ind->delete();
  Session::flash('success', 'Industry deleted successfully !');
  return redirect('master/industry');
  }  

  public function lifecycle_stage_list()
  {
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm-master/lifecycle-stage-list', ['pageConfigs' => $pageConfigs]);
  }

  public function lifecycle_stage_list_json(Request $request)
  {

    $lf = LifeCycleStage::all();
    $data['data'] = $lf;
    return response()->json($data);
  }

  public function lifecycle_stage2_list_json(Request $request){

    $lcs = $request->input('lcs_id');

    // $lbs = DB::table('lstages as lstage')
    //     ->leftjoin('qr_lifecycle_stage as lfc','lstage.lcs_id','=','lfc.id')
    //     ->select('lstage.*','lfc.name as lname')
    //     ->get();

    $lbs = Lstage::where('lcs_id','=',$lcs)->get();

    $data['data'] = $lbs;
    return response()->json($data);
  }

  public function lifecycle_stage_store(Request $request)
  {
    extract($_POST);
    $l= new LifeCycleStage ();
    $l->name = $name;
    $l->user_id = Auth::user()->user_id;
    $l->save();
    Session::flash('success', 'Lifecycle created successfully !');
    return redirect('master/lifecycle/stage/list');
  }

  public function leadStageStore(Request $request){
    $post = new Lstage();
    $post->name = $request->input('name');
    $post->lcs_id = $request->input('lcs_id');
    $post->user_id = Auth::user()->user_id;
    $post->save();
    Session::flash('success', 'Lead Stage created successfully !');
    return redirect()->back();
  }

  public function lifecycle_stage_update(Request $request)
  {
    extract($_POST);
    $l= LifeCycleStage::find($id);
    $l->name = $name;
    $l->save();
    Session::flash('success', 'Lifecycle updated successfully !');
    return redirect('master/lifecycle/stage/list');
  }

  public function contact_lifecycle_update(Request $request)
  { 
    extract($_POST);
    $con = CompanyContact::find($cont_id);

    $con->lifecy_stage = $ch_val;
    $con->save();

    $res = LifeCycleStage::find($ch_val);
    $ti = new ActivitiesContactCrm();
    $ti->cont_id = $cont_id;
    $ti->subject = 'Lifecycle stage update to '.$res->name;
    $ti->save();
  }

  public function contact_followup_date_update(Request $request)
  { 
    extract($_POST);
    $con = CompanyContact::find($cont_id);

    $con->next_followup = $ch_val;
    $con->save();


    $ti = new ActivitiesContactCrm();
    $ti->cont_id = $cont_id;
    $ti->subject = 'Next followup date update to '.$ch_val;
    $ti->save();
  }

  public function contacts_followup_date_update(Request $request)
  { 
    extract($_POST);
    $con = AllContact::find($id);

    $con->followup_date = $ch_val;
    $con->save();


    $ti = new AllContactAct();
    $ti->cont_id = $id;
    $ti->subject = 'Next followup date update to '.$ch_val;
    $ti->user_id = Auth::user()->user_id;
    $ti->save();
  }

  public function contacts_lead_update(Request $request)
  {
    extract($_POST);
    $con = AllContact::find($id);
    $con->ls_id = $ch_val;
    $con->save();

    $res = Lstage::find($ch_val);
    $ti = new AllContactAct();
    $ti->cont_id = $id;
    $ti->subject = 'Lead stage update to '.$res->name;
    $ti->user_id = Auth::user()->user_id;
    $ti->save();
  }

  public function contacts_lead_type(Request $request)
  {
    extract($_POST);
    $con = AllContact::find($id);
    $con->lead_type = $ch_val;
    $con->save();

    // $res = LeadStage::find($ch_val);
    $ti = new AllContactAct();
    $ti->cont_id = $id;
    $ti->subject = 'Lead Type update to '.$ch_val;
    $ti->user_id = Auth::user()->user_id;
    $ti->save();
  }

  public function updateRegarding(Request $request){
    $id = $request->input('cont_id');
    $con = AllContact::find($id);
    $con->regarding = implode(",",$request->input('regarding'));
    $con->lead_type = $request->input('lead_type');
    $con->save();

    $ti = new AllContactAct();
    $ti->cont_id = $id;
    $ti->subject = 'Regarding update to ';
    $ti->user_id = Auth::user()->user_id;
    $ti->save();
    Session::flash('success','Regading Updated');
    return redirect()->back();
  }

  public function contacts_status_update(Request $request){
    extract($_POST);
    $con = AllContact::find($id);
    $con->lead_prority = $ch_val;
    $con->save();

    // $res = LeadStage::find($ch_val);
    $ti = new AllContactAct();
    $ti->cont_id = $id;
    $ti->subject = 'Lead Priority update to '.$ch_val;
    $ti->user_id = Auth::user()->user_id;
    $ti->save();

    // return response()->json('1');

  }

  public function company_contacts_type_update(Request $request){
    extract($_POST);
    $con = AllContact::find($id);
    $con->ct_id = $ct_id;
    $con->save();

    // $res = ContactType::find($ct_id);
    // $ti = new AllContactAct();
    // $ti->cont_id = $cont_id;
    // $ti->subject = 'Contact type update to '.$res->type;
    // $ti->user_id = Auth::user()->user_id;
    // $ti->save(); 



  }

  public function updatePublicSt(Request $request){
    extract($_POST);
    $con = AllContact::find($id);
    $con->public_st = $ch_val;
    $con->save();

    // $ti = new AllContactAct();
    // $ti->cont_id = $cont_id;
    // $ti->subject = 'Public Status update to '.$ch_val;
    // $ti->save();

  }


  public function contacts_primarycontact_update(Request $request){
    extract($_POST);
    $con = AllContact::find($id);
    $final_value = str_replace("+","",$ch_val);
    $con->primary_contact_no = $final_value;
    $con->save();

    
    $ti = new AllContactAct();
    $ti->cont_id = $id;
    $ti->subject = 'Primary contact updated to '.$final_value;
    $ti->save();

  }

  public function contacts_profilepic_update(Request $request){
    $id = $request->input('cont_id') ? $request->input('cont_id') : '';
    if($request->hasFile('profile_pic')) {
      $file = $request->file('profile_pic');
      $file_count = File::files(base_path().'/public/image/crm-contact');
      $filecount = 0;
      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;
      $file->move(base_path().'/public/image/crm-contact', $name);
      $profile_pic = $name;
    }else{
      $profile_pic = '';
    }

    $con = AllContact::find($id);
    $con->profile_pic = $profile_pic ?  $profile_pic :  $con->profile_pic;
    $con->save();
    Session::flash('success', 'Profile pic updated successfully !');
    return redirect('master/contacts/view/'.$id); 

  }

  public function contacts_staff_update(Request $request){
    // extract($_POST);
    // dd($request);
    $con = AllContact::find($request->input('id'));
    $con->assign_id = $request->input('assign_id');
    $con->save();
    $res = User::where('user_id','=',$request->assign_id)->first();
    $ti = new AllContactAct();
    $ti->cont_id = $request->id;
    $ti->subject = 'Staff update to '.$res->name;
    $ti->save();
    
    Session::flash('success','Transfer Owner Successfully!');
    return redirect()->back();
    
  }

  public function contacts_lifecycle_update(Request $request){
    extract($_POST);
    $con = AllContact::find($id);

    $con->lcs_id = $ch_val;
    $con->save();

    $res = LifeCycleStage::find($ch_val);
    $ti = new AllContactAct();
    $ti->cont_id = $id;
    $ti->subject = 'Lifecycle stage update to '.$res->name;
    $ti->save();
  }

  public function contact_lead_update(Request $request)
  { 
    extract($_POST);
    $con = CompanyContact::find($cont_id);
    $con->lead_stage = $ch_val;
    $con->save();
    $res = Lstage::find($ch_val);
    $ti = new ActivitiesContactCrm();
    $ti->cont_id = $cont_id;
    $ti->subject = 'Lead stage update to '.$res->name;
    $ti->save();
  }

  public function company_contact_type_update(Request $request)
  { 
    extract($_POST);
    $con = CompanyContact::find($cont_id);
    $con->contact_type = $ch_val;
    $con->save();
    $res = ContactType::find($ch_val);
    $ti = new ActivitiesContactCrm();
    $ti->cont_id = $cont_id;
    $ti->subject = 'Contact type update to '.$res->type;
    $ti->save();
  }


  public function contact_staff_update(Request $request)
  { 
    extract($_POST);
    $con = CompanyContact::find($cont_id);
    $con->staff = $ch_val;
    $con->save();
    $res = Staff::find($ch_val);
    $ti = new ActivitiesContactCrm();
    $ti->cont_id = $cont_id;
    $ti->subject = 'Staff update to '.$res->staff_fname.' '.$res->staff_lname;
    $ti->save();
  }


  public function contact_source_update(Request $request)
  { 
    extract($_POST);
    // $con = CompanyContact::find($cont_id);
    $con = AllContact::find($cont_id);
    $con->source_id = $ch_val;
    $con->save();

    $source = Source::find($ch_val);

    $ti = new ActivitiesContactCrm();
    $ti->cont_id = $cont_id;
    $ti->subject = 'Source update to '.$source->name;
    $ti->save();
  }
  public function contact_primarycontact_update(Request $request)
  { 
    extract($_POST);
    $con = CompanyContact::find($cont_id);
    $con->primary_contact_no = $ch_val;
    $con->save();

    $ti = new ActivitiesContactCrm();
    $ti->cont_id = $cont_id;
    $ti->subject = 'Primary contact updated to '.$ch_val;
    $ti->save();
  }


  public function lifecycle_stage_edit(Request $request)
  {
    extract($_POST);
    $lf= LifeCycleStage::find($id);
    return response()->json($lf);
  }

  public function lead_stage_edit2(Request $request){
    extract($_POST);
    $lsa = Lstage::find($id);
    return response()->json($lsa);
  }

  public function updateleadSt2(Request $request){
    extract($_POST);
    $lsa = Lstage::find($id);
    $lsa->name = $request->input('name');
    $lsa->save();

    Session::flash('success','Lead Stage Updated!');
    return redirect()->back();
  }

  public function lifecycle_stage_delete($id)
  {

    $lf= LifeCycleStage::find($id);
    $lf->delete();
    Session::flash('success', 'Lifecycle deleted successfully !');
    return redirect('master/lifecycle/stage/list');
  }

  public function lead_stage_list()
  {
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm-master/lead-stage-list', ['pageConfigs' => $pageConfigs]);
  }

  public function lead_stage_list_json(Request $request)
  {
    $lf = LeadStage::all();
    $data['data'] = $lf;
    return response()->json($data);
  }

  public function sourceList(){
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm-master/source-list',['pageConfigs' => $pageConfigs]);
  }

  public function source_list_json(Request $request){
    $source = Source::orderBy('id','DESC')->get();
    $data['data'] = $source;
    return response()->json($data);
  }

  public function sourceStore(Request $request){
    $post = new Source();
    $post->name = $request->input('name');
    $post->user_id = Auth::user()->user_id;
    $post->save();
    Session::flash('success','Soure Uploaded successfully!');
    return redirect('master/source/list');
  }

  public function source_edit(Request $request){
      extract($_POST);
      $lf= Source::find($id);

      return response()->json($lf);
  }

  public function source_update(Request $request){
    $post = Source::find($request->id);
    $post->name = $request->input('name');
    $post->updateby_id = Auth::user()->user_id;
    $post->save();
    Session::flash('success','Source updated successfully!');
    return redirect('master/source/list');
  }


  public function lead_stage_store(Request $request)
  {
    extract($_POST);
    $l= new LeadStage();
    $l->name = $name;
    $l->save();
    Session::flash('success', 'Lead created successfully !');
    return redirect('master/lead/stage/list');
  }

  public function lead_stage_update(Request $request)
  {
    extract($_POST);
    $l= LeadStage::find($id);
    $l->name = $name;
    $l->save();
    Session::flash('success', 'Lead updated successfully !');
    return redirect('master/lead/stage/list');
  }

  public function lead_stage_edit(Request $request)
  {
    extract($_POST);
    $lf= LeadStage::find($id);

    return response()->json($lf);
  }

  public function lead_stage_delete($id)
  {
    $lf = LeadStage::find($id);
    $lf->delete();
    Session::flash('success', 'Lead deleted successfully !');
    return redirect('master/lead/stage/list');
  }

  public function notes_contact_store(Request $request)
  {
    $note = $request->input('notes');
    $id = $request->input('id');
    $crm = new NoteContact();
    $crm->notes = $note;
    $crm->cont_id = $id;
    $crm->save();

    $tm = new TimelineContactCrm();
    $tm->cont_id = $id;
    $tm->subject = 'Notes created';
    $tm->insert_id = $crm->id;
    $tm->user_type = Auth::user()->user_type;
    $tm->user_id = Auth::user()->user_id;
    $tm->save();
    Session::flash('success', 'Notes created successfully !');

    return redirect('master/contact/view/'.$id);
  }

  public function notes_contact_delete($id)
  {
    $del = NoteContact::find($id);
    $del->delete();
    Session::flash('success', 'Notes deleted successfully !');
    return redirect()->back(); 
  }

  public function task_contact_delete($id)
  {
    $del = TaskContact::find($id);
    $del->delete();
    Session::flash('success', 'Task deleted successfully !');
    return redirect()->back(); 
  }

  public function appoint_contact_delete($id)
  {
    $del = AppointContact::find($id);
    $del->delete();
    Session::flash('success', 'Appoint deleted successfully !');
    return redirect()->back(); 
  }

  public function file_contact_delete($id)
  {
    $del = FilesContact::find($id);
    $del->delete();
    Session::flash('success', 'File deleted successfully !');
    return redirect()->back(); 
  }



  public function task_contact_edit(Request $request)
  {
    $id = $request->input('id');
    $task_data = TaskContact::find($id);
    return response()->json($task_data);
  }


  public function appoint_contact_edit(Request $request)
  {
    $id = $request->input('id');
    $app_data = AppointContact::find($id);
    return response()->json($app_data);
  }

  public function tasks_contact_store(Request $request)
  {
    // dd($request);
    $edit_id = $request->input('edit_id');
    if($edit_id){
      $cont_id = $request->input('cont_id');
      $title = $request->input('title');
      $desc = $request->input('desc');
      $task_type = $request->input('task_type');
      $due_date = $request->input('due_date');
      $time = $request->input('time');

      $crm = TaskContact::find($edit_id);
      $crm->title = $title;
      $crm->desc = $desc;
      $crm->task_type = $task_type;
      $crm->due_date = $due_date;
      $crm->time = $time;
      $crm->save();

      $tm = new TimelineContactCrm();
      $tm->cont_id = $cont_id;
      $tm->subject = 'Task updated';
      $tm->insert_id = $crm->id;
      $tm->user_type = Auth::user()->user_type;
      $tm->user_id = Auth::user()->user_id;
      $tm->save();
      Session::flash('success', 'Task Updated successfully !');
      return redirect('master/contact/view/'.$cont_id); 
    }else{

      $title = $request->input('title');
      $desc = $request->input('desc');
      $task_type = $request->input('task_type');
      $due_date = $request->input('due_date');
      $time = $request->input('time');
      $id = $request->input('cont_id');

      $crm = new TaskContact();
      $crm->title = $title;
      $crm->desc = $desc;
      $crm->task_type = $task_type;
      $crm->due_date = $due_date;
      $crm->time = $time;
      $crm->cont_id = $id;
      $crm->save();

      $tm = new TimelineContactCrm();
      $tm->cont_id = $id;
      $tm->subject = 'Task created';
      $tm->insert_id = $crm->id;
      $tm->user_type = Auth::user()->user_type;
      $tm->user_id = Auth::user()->user_id;
      $tm->save();
      Session::flash('success', 'Task created successfully !');
      return redirect('master/contact/view/'.$id); 
    }
  }

  public function appoint_contact_store(Request $request)
  {
    // dd($request);
    $edit_id = $request->input('edit_id');
    if($edit_id){
      $cont_id = $request->input('cont_id');
      $crm = AppointContact::find($edit_id);
      $crm->title = $request->input('title') ? $request->input('title') : '';
      $crm->description = $request->input('description') ? $request->input('description') :'';
      $crm->froms = $request->input('froms') ? $request->input('froms') : '';
      $crm->time1 = $request->input('time1') ? $request->input('time1') :'';
      $crm->tos = $request->input('tos') ? $request->input('tos') : '';
      $crm->time2 = $request->input('time2') ? $request->input('time2') :'';
      $crm->wheres = $request->input('wheres') ? $request->input('wheres') :'';
      $crm->outcome = $request->input('outcome') ? $request->input('outcome') :'';
      $crm->save();

      $tm = new TimelineContactCrm();
      $tm->cont_id = $cont_id;
      $tm->subject = 'Appointment updated';
      $tm->insert_id = $crm->id;
      $tm->user_type = Auth::user()->user_type;
      $tm->user_id = Auth::user()->user_id;
      $tm->save();
      Session::flash('success', 'Task Updated successfully !');
      return redirect('master/contact/view/'.$cont_id); 
    }else{
      $crm = new AppointContact();
      $crm->title = $request->input('title') ? $request->input('title') : '';
      $crm->description = $request->input('description') ? $request->input('description') :'';
      $crm->froms = $request->input('froms') ? $request->input('froms') : '';
      $crm->time1 = $request->input('time1') ? $request->input('time1') :'';
      $crm->tos = $request->input('tos') ? $request->input('tos') : '';
      $crm->time2 = $request->input('time2') ? $request->input('time2') :'';
      $crm->wheres = $request->input('wheres') ? $request->input('wheres') :'';
      $crm->outcome = $request->input('outcome') ? $request->input('outcome') :'';
      $crm->cont_id = $request->input('cont_id') ? $request->input('cont_id') : '';
      $crm->save();

      $cont_id = $request->input('cont_id');
      $tm = new TimelineContactCrm();
      $tm->cont_id = $cont_id;
      $tm->subject = 'Appointment created';
      $tm->insert_id = $crm->id;
      $tm->user_type = Auth::user()->user_type;
      $tm->user_id = Auth::user()->user_id;
      $tm->save();
    
      Session::flash('success', 'Appointment created successfully !');
      return redirect('master/contact/view/'.$cont_id); 
    }
  }

  public function contact_profilepic_update(Request $request)
  {
    $cont_id = $request->input('cont_id') ? $request->input('cont_id') : '';
    if($request->hasFile('profile_pic')) 
    {
      $file = $request->file('profile_pic');
      $file_count = File::files(base_path().'/public/image/crm-contact');
      $filecount = 0;

      if ($file_count !== false) 
      {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/crm-contact', $name);
      $profile_pic = $name;

    } else {
      $profile_pic = '';
    }

    $cont = CompanyContact::find($cont_id);
    $cont->profile_pic =  $profile_pic ?  $profile_pic :  $cont->profile_pic;
    $cont->save();
    Session::flash('success', 'Profile pic updated successfully !');
    return redirect('master/contact/view/'.$cont_id); 

  }

  public function contact_status_update(Request $request)
  {
    extract($_POST);
    $cont = CompanyContact::find($cont_id);
    $cont->status = $status;
    $cont->save();
    return response()->json('1');
  }

  public function visa_profession_list(Request $request)
  {
    $pageConfigs = ['pageHeader' => false];
    return view('/content/service-master/visa-profession', ['pageConfigs' => $pageConfigs]);
  }

  public function visa_profession_list_json(Request $request)
  {
    $prof = Profession::orderBy('prof_id','DESC')->get();
    $data['data'] = $prof;
    return response()->json($data);
  }

  public function visa_profession_list_store(Request $request)
  {
    $prof = new Profession();
    $prof->prof_eng_name = $request->input('prof_eng_name') ? $request->input('prof_eng_name') :'';
    $prof->prof_arabic_name = $request->input('prof_arabic_name') ? $request->input('prof_arabic_name') :'';
    $prof->user_id = Auth::user()->user_id;
    $prof->save();
    Session::flash('success', 'Visa profession created successfully !');
    return redirect('master/visa/profession/list');
  }

  public function visa_profession_list_edit(Request $request)
  {

    $pf = Profession::find($request->input('id'));
    return response()->json($pf);
  }

  public function visa_profession_list_update(Request $request)
  {

    $pf = Profession::find($request->input('edit_id'));
    $pf->prof_eng_name = $request->input('prof_eng_name') ? $request->input('prof_eng_name') :'';
    $pf->prof_arabic_name = $request->input('prof_arabic_name') ? $request->input('prof_arabic_name') :'';
    $pf->save();
    Session::flash('success', 'Visa profession updated successfully !');
    return redirect('master/visa/profession/list');
  }

  public function visa_profession_delete(Request $request)
  {
    $pf = Profession::find($request->input('id'));
    $pf->delete();
    Session::flash('success', 'Visa profession deleted successfully !');
    return redirect('master/visa/profession/list');
  }

  public function party_status_list (){
    $pageConfigs = ['pageHeader' => false];
    return view('content.crm.party_status_list',['pageConfigs' => $pageConfigs]);
  }

  public function party_status_list_json(Request $request){
    $post = PartyStatus::all();
    $data['data'] = $post;
    return response()->json($data);
  }

  public function party_status_store(Request $request){

    $post = new PartyStatus();
    $post->name = $request->name;
    $post->user_id = Auth::user()->user_id;
    $post->save();
    Session::flash('success','Party Status created!');
    return redirect()->back();
  }

  public function party_status_edit(Request $request){
    extract($_POST);
    $post = PartyStatus::find($id);
    return response()->json($post);
  }

  public function party_status_update(Request $request){
    $post = PartyStatus::find($request->id);
    $post->name = $request->name;
    $post->save();
    Session::flash('success','Party Status updated!');
    return redirect()->back();
  }

  public function delmac(Request $request){
    $post = PartyMessageActivity::find($request->pmac_id);
    $post->delete();
    Session::flash('success','Message activity deleted!');
    return redirect()->back();
  }

  public function delnotes(Request $request){
    $post = PartyNotesStatus::find($request->notes_id);
    $post->delete();
    Session::flash('success','Party notes deleted!');
    return redirect()->back();
  }

  public function delserch(Request $request){
    $post = PartyServiceFee::find($request->pty_serch_id);
    $post->delete();
    Session::flash('success','Party Service Charge deleted!');
    return redirect()->back();
  }

  public function party_list()
  {
    

    $pageConfigs = ['pageHeader' => false];
    return view('/content/service-master/party-list', ['pageConfigs' => $pageConfigs]);
  }

  public function getEditDatacon(Request $request){
    $id = $request->id;
    $post = AllContact::find($id);
    return response()->json($post);
  }

  public function activeWhatsappApi(Request $request){
    extract($_POST);

    $post = Userwhatsappapi::find($pty_id);

    // $po = Party::find($pty_id);
    $post->status = 1;
    $post->save();
    Session::flash('success', 'Whatsapp API Status actived!');
    return redirect()->back();
  }

  public function deactiveWhatsappApi(Request $request){
      extract($_POST);
      $post = Userwhatsappapi::find($pty_id);
      $post->status = 0;
      $post->save();
      Session::flash('success', 'Whatsapp API Status De-activated!');
      return redirect()->back();
  }

  public function deleteWhatsappApi(Request $request){
    extract($_POST);
    $post = Userwhatsappapi::find($pty_id);
    $post->delete();
    Session::flash('success', 'Whatsapp API deleted!');
    return redirect()->back();
  }

  public function checkapilist(Request $request){
    $post = Userwhatsappapi::where('assoc_number_id','=',$request->assocID)->count();
    $assoc_num = Numberadd::find($request->assocID);
    if($post == 0){
      $data = [
        'user_id' => $assoc_num->assoc_user_id,
        'success' => 'Number is available',
        'error' => ''
      ];
    }else{
      $data = [
        'user_id' => $assoc_num->assoc_user_id,
        'success' => '',
        'error' => 'Already exists'
      ];
    }

    return response()->json($data);
    
  }

  public function getPartySTatus(Request $request){
    $post = Party::where('pty_id','=',$request->pty_id)->first();

    return response()->json($post);
  }


  public function sendcareoffmessage(Request $request){


    $post = Party::where('pty_id','=',$request->pty_id)->first();
    $user = User::where('user_id','=',$request->careoffmID)->first();


    $primary_no = substr($post->pty_comp_contact,2);
    
    if($post->pty_contact_no != ''){
      $secondary_no = substr($post->pty_contact_no,2);
    }else{
      $secondary_no = '';
    }

    if($post->p_mobile != ''){
      $personal_no = substr($post->p_mobile,2);
    }else{
      $personal_no = '';
    }

    $getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=',1)->first();


    $msgD = "Dear ".$user->name."\nParty detail:\nParty Name: ".$post->pty_full_name."\nParty Agency Name: ".$post->pty_ag_name."\nParty City: ".$post->city."\nParty State: ".$post->state."\nParty Primary No.: ".$primary_no."\nPersonal No.: ".$personal_no."\nParty Secondary No.: ".$secondary_no;

    $url = $getAPI->text_message_url."?number=".$user->mobile."&type=text&message=".urlencode($msgD)."&instance_id=".$getAPI->instance_key."&access_token=".$getAPI->api_key;


    $ch = curl_init();
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_URL,$url);
    $result =  curl_exec($ch);
    // echo $result;
    curl_close($ch);

    if($result){
      $data = "Party detail are send!";
      return response()->json($data);
    }

    
    // Session::flash('success','Party detail are send!');
    // return redirect()->back();
    
  }

  public function updateptstaus(Request $request){
    $post = Party::where('pty_id','=',$request->pty_id)->first();
    $post->ptstatus_id = $request->ptstatus_id;
    $post->save();

    // Create Notes
    $npost = new PartyNotesStatus();
    $npost->pty_id = $request->pty_id;
    $npost->notes = $request->notes;
    $npost->user_id = Auth::user()->user_id;
    $npost->save();

    // Create Party Update Status Activities
    $pty_status = PartyStatus::find($request->ptstatus_id);

    $pact = new PartyStatusActivity();
    $pact->pty_id = $request->pty_id;
    $pact->subject = "Party status change to ".$pty_status->name;
    $pact->user_id = Auth::user()->user_id;
    $pact->save();
    // Session::flash('success','Party Status Updated!');
    // return redirect()->back();

    $data = "Status updated!";
    return response()->json($data);
  }

  public function party_list_json(Request $request)
  {

    $perms = AccessPermissionModule2::where('user_id','=',Auth::user()->user_id)->first();
    
    if(Auth::user()->user_type == 1 || (isset($perms) && $perms->full_access == 1)){
      $party = DB::table('qr_party_tbl as party')
      ->leftjoin('users as luser','party.care_of_id','=','luser.user_id')
      ->leftjoin('users as cuser','party.user_id','=','cuser.user_id')
      ->leftjoin('users as user','party.lead_owner_id','=','user.user_id')
      ->leftjoin('party_statuses as ptstaus','party.ptstatus_id','ptstaus.id')
      ->select('party.*','user.name as uname','ptstaus.name as ptsname','cuser.name as cuname','luser.name as leadname')
      ->get();

      // $prty = Party::where('pty_id','!=','200')->get();
    }elseif(Auth::user()->user_type == 2 || (isset($perms) && $perms->full_access == 0)){
      if($perms){
        if($perms->smpartyr == '1'){
          $party = DB::table('qr_party_tbl as party')
          ->leftjoin('users as luser','party.care_of_id','=','luser.user_id')
          ->leftjoin('users as cuser','party.user_id','=','cuser.user_id')
          ->leftjoin('party_statuses as ptstaus','party.ptstatus_id','ptstaus.id')
          ->leftjoin('users as user','party.lead_owner_id','=','user.user_id')
          ->select('party.*','user.name as uname','ptstaus.name as ptsname','cuser.name as cuname','luser.name as leadname')
          ->get();
        
          // $prty = Party::where('pty_id','!=','200')->get();
        }else{

          $party = DB::table('qr_party_tbl as party')
          ->leftjoin('users as luser','party.care_of_id','=','luser.user_id')
          ->leftjoin('users as cuser','party.user_id','=','cuser.user_id')
          ->leftjoin('party_statuses as ptstaus','party.ptstatus_id','ptstaus.id')
          ->leftjoin('users as user','party.lead_owner_id','=','user.user_id')
          ->select('party.*','user.name as uname','ptstaus.name as ptsname','cuser.name as cuname','luser.name as leadname')
          // ->where('party.user_id','=',Auth::user()->user_id)
          // ->orWhere('party.care_of_id','=',Auth::user()->user_id)
          ->Where('party.lead_owner_id','=',Auth::user()->user_id)
          ->get();

          // $prty = Party::where('pty_id','!=','200')->where('user_id','=',Auth::user()->user_id)->orWhere('care_of_id','=',Auth::user()->user_id)->get();
        }
      }else{  
        $party = DB::table('qr_party_tbl as party')
        ->leftjoin('users as luser','party.care_of_id','=','user.user_id')
        ->leftjoin('users as cuser','party.user_id','=','cuser.user_id')
        ->leftjoin('party_statuses as ptstaus','party.ptstatus_id','ptstaus.id')
        ->leftjoin('users as user','party.lead_owner_id','=','user.user_id')
        ->select('party.*','user.name as uname','ptstaus.name as ptsname','cuser.name as cuname','luser.name as leadname')
        // ->where('party.user_id','=',Auth::user()->user_id)
        // ->orWhere('party.care_of_id','=',Auth::user()->user_id)
        ->Where('party.lead_owner_id','=',Auth::user()->user_id)
        ->get();
        // $prty = Party::where('pty_id','!=','200')->where('user_id','=',Auth::user()->user_id)->orWhere('care_of_id','=',Auth::user()->user_id)->get();
      }
    }

    



    $data['data'] = $party;
    return response()->json($data);
  }

  public function checkExistParty(Request $request){
    
    $pty_id = $request->input('pty_id');
    $data1 = DB::table('qr_candidate_tbl')->where('pty_id','=',$pty_id)->first();
    $data2 = DB::table('qr_employee_tbl')->where('pty_id','=',$pty_id)->first();
    if(isset($data1) || isset($data2)){
      return response()->json('1');
    }else{
      
    }
  }


  public function party_store(Request $request)
  {

    if($request->hasFile('photo')) {
      $file = $request->file('photo');
      $file_count = File::files(base_path().'/public/image/service-master');
      $filecount = 0;
      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;
      $file->move(base_path().'/public/image/service-master', $name);
      $photo = $name;

    } else {
      $photo = '';
    }

    if($request->hasFile('adhar_card')) {
      $file = $request->file('adhar_card');
      $file_count = File::files(base_path().'/public/image/service-master');
      $filecount = 0;
      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/service-master', $name);
      $adhar_card = $name;
    } else {
      $adhar_card = '';
    }

    if($request->hasFile('ration_card')) {
      $file = $request->file('ration_card');
      $file_count = File::files(base_path().'/public/image/service-master');
      $filecount = 0;
      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;
      $file->move(base_path().'/public/image/service-master', $name);
      $ration_card = $name;
    } else {
      $ration_card = '';
    }

    if($request->hasFile('cv')) {
      $file = $request->file('cv');
      $file_count = File::files(base_path().'/public/image/service-master');
      $filecount = 0;
      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;
      $file->move(base_path().'/public/image/service-master', $name);
      $cv = $name;
    } else {
      $cv = '';
    }

    // if($request->hasFile('contract')){
    //   $file = $request->file('contract');
    //   $file_count = File::files(base_path().'/public/image/service-master');
    //   $filecount = 0;

    //   if ($file_count !== false) {
    //     $filecount = count($file_count);
    //   }
    //   $file_exe = $file->getClientOriginalExtension();
    //   $name = $filecount . '.' . $file_exe;
    //   $file->move(base_path().'/public/image/service-master', $name);
    //   $contract = $name;
    // } else {
    //   $contract ='';
    // }

    $party = new Party();
    $party->pty_full_name = $request->input('pty_full_name') ? $request->input('pty_full_name') : '';
    $party->pty_ag_name = $request->input('pty_ag_name') ? $request->input('pty_ag_name') : '';
    $party->pty_email = $request->input('pty_email') ? $request->input('pty_email') : '';
    $party->pty_comp_contact = $request->input('pty_comp_contact') ? $request->input('pty_comp_contact') : '';
    $party->pty_contact_no = $request->input('pty_contact_no') ? $request->input('pty_contact_no') : '';
    $party->pty_aadhar_no = $request->input('pty_aadhar_no') ? $request->input('pty_aadhar_no') : '';
    $party->pty_dob = $request->input('pty_dob') ? $request->input('pty_dob') : '';
    $party->country = $request->input('country') ? $request->input('country') : '';
    $party->state = $request->input('state') ? $request->input('state') : '';
    $party->city = $request->input('city') ? $request->input('city') : '';
    $party->address = $request->input('address') ? $request->input('address') : '';
    $party->pty_gender = $request->input('pty_gender') ? $request->input('pty_gender') : '';
    $party->pty_rel = $request->input('pty_gender') ? $request->input('pty_gender') : '';
    $party->photo = $request->input('photo') ? $request->input('photo') : '';
    $party->adhar_card = $request->input('adhar_card') ? $request->input('adhar_card') : '';
    $party->ration_card = $request->input('ration_card') ? $request->input('ration_card') : '';
    $party->cv = $request->input('cv') ? $request->input('cv') : '';
    // $party->contract = $request->input('contract') ? $request->input('contract') : '';
    $party->p_mobile = $request->input('p_mobile') ? $request->input('p_mobile') : '';
    $party->c_person = $request->input('c_person') ? $request->input('c_person') : '';
    $party->sec_email = $request->input('sec_email') ? $request->input('sec_email') : '';
    $party->pincode = $request->input('pincode') ? $request->input('pincode') : '';
    $party->member_id = $request->input('membership') ? $request->input('membership') : '';
    $party->user_id = Auth::user()->user_id;
    $party->care_of_id = $request->input('care_of_id') ? $request->input('care_of_id') : '';
    $party->verified = $request->input('verified') ? $request->input('verified') : '';
    $party->act_status = '1';
    $party->telephone_number = $request->input('telephone_number') ? $request->input('telephone_number') : '';
    $party->lead_owner_id = $request->lead_owner_id;

    if(Auth::user()->user_type != 1){
      $party->act_status = 1;
    }

    $party->save();

    // Insert into Address Type
    $postAdd = new PartyAddress();
    $postAdd->name = $request->pty_full_name;
    $postAdd->office_name = $request->pty_ag_name;
    $postAdd->address = $request->address;
    $postAdd->pincode = $request->pincode;
    $postAdd->city = $request->city;
    $postAdd->user_id = Auth::user()->user_id;
    $postAdd->save();

    // Get Last ID
    $get_last_id = $party->id;
    // Upload File on PartyFile
    if($photo != ''){
      $ppfile = new PartyFile();
      $ppfile->pty_id = $get_last_id;
      $ppfile->doc_name = $request->doc_name;
      $ppfile->file = $photo;
      $ppfile->user_id = Auth::user()->user_id;
      $ppfile->save();
    }


    
    Session::flash('success', 'Party created successfully !');
    return redirect('master/party/list'); 
  }

  public function getEditparty(Request $request){
    $pty_id = $request->input('pty_id');
    $data = Party::find($pty_id);
    return response()->json($data);
  }

  public function updateParty2(Request $request){
    
    // dd($request);

    if($request->hasFile('photo')) {
      $file = $request->file('photo');
      $file_count = File::files(base_path().'/public/image/service-master');
      $filecount = 0;
      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;
      $file->move(base_path().'/public/image/service-master', $name);
      $photo = $name;

    } else {
      $photo = '';
    }

    // Update in Party
    $post = Party::find($request->input('pty_id_edit'));
    $post->pty_full_name = $request->input('pty_full_name') ? $request->input('pty_full_name') : '';
    $post->pty_ag_name = $request->input('pty_ag_name') ? $request->input('pty_ag_name') : '';
    $post->pty_email = $request->input('pty_email') ? $request->input('pty_email') : '';
    $post->pty_comp_contact = $request->input('pty_comp_contact2') ? $request->input('pty_comp_contact2') : '';
    $post->pty_contact_no = $request->input('pty_contact_no2') ? $request->input('pty_contact_no2') : '';
    $post->pty_aadhar_no = $request->input('pty_aadhar_no') ? $request->input('pty_aadhar_no') : '';
    $post->pty_dob = $request->input('pty_dob') ? $request->input('pty_dob') : '';
    $post->country = $request->input('country') ? $request->input('country') : '';
    $post->state = $request->input('state') ? $request->input('state') : '';
    $post->city = $request->input('city') ? $request->input('city') : '';
    $post->address = $request->input('address') ? $request->input('address') : '';
    $post->pty_gender = $request->input('pty_gender') ? $request->input('pty_gender') : '';
    $post->pty_rel = $request->input('pty_gender') ? $request->input('pty_gender') : '';
    $post->photo = $request->input('photo') ? $request->input('photo') : $post->photo;
    // $post->adhar_card = $request->input('adhar_card') ? $request->input('adhar_card') : '';
    // $post->ration_card = $request->input('ration_card') ? $request->input('ration_card') : '';
    // $post->cv = $request->input('cv') ? $request->input('cv') : '';
    // $party->contract = $request->input('contract') ? $request->input('contract') : '';
    $post->p_mobile = $request->input('p_mobile2') ? $request->input('p_mobile2') : '';
    $post->c_person = $request->input('c_person') ? $request->input('c_person') : '';
    $post->sec_email = $request->input('sec_email') ? $request->input('sec_email') : '';
    $post->pincode = $request->input('pincode') ? $request->input('pincode') : '';
    $post->member_id = $request->input('membership') ? $request->input('membership') : '';
    // $post->care_of_id = $request->input('care_of_id') ? $request->input('care_of_id') : '';
    // $post->verified = $request->input('verified') ? $request->input('verified') : '';

    $post->telephone_number = $request->input('telephone_number2') ? $request->input('telephone_number2') : '';

    // if(Auth::user()->user_type != 1){
    //   $post->act_status = 1;
    // }

    $post->save();

    // Create Party file
    if($photo != ''){
      $ppfile = new PartyFile();
      $ppfile->pty_id = $request->input('pty_id');
      $ppfile->doc_name = $request->doc_name;
      $ppfile->file = $photo;
      $ppfile->user_id = Auth::user()->user_id;
      $ppfile->save();
    }

    // Session::flash('success','Party details updated successfully!');
    // return redirect()->back();

    $data = "Party details updated successfully!";
    return response()->json($data);
  
  }

  public function partyfilterup(Request $request){
    // check user is exist or not
    $user = PartyFilter::where('user_id','=',Auth::user()->user_id)->count();
    if($user > 0){
      $updateF = PartyFilter::where('user_id','=',Auth::user()->user_id)->first();
    
      if($request->partyf == 1){
        $updateF->partyf = '1';
      }else{
        $updateF->partyf = '0';
      }

      if($request->pstatusf == 1){
        $updateF->pstatusf = '1';
      }else{
        $updateF->pstatusf = '0';
      }

      if($request->pdocumentf == 1){
        $updateF->pdocumentf = '1';
      }else{
        $updateF->pdocumentf = '0';
      }

      if($request->pwstatusf == 1){
        $updateF->pwstatusf = '1';
      }else{
        $updateF->pwstatusf = '0';
      }

      if($request->preligionf == 1){
        $updateF->preligionf = '1';
      }else{
        $updateF->preligionf = '0';
      }

      if($request->pcareofff == 1){
        $updateF->pcareofff = '1';
      }else{
        $updateF->pcareofff = '0';
      }

      if($request->pleadownerfff == 1){
        $updateF->pleadownerfff = '1';
      }else{
        $updateF->pleadownerfff = '0';
      }

      if($request->pstatef == 1){
        $updateF->pstatef = '1';
      }else{
        $updateF->pstatef = '0';
      }

      if($request->pcityf == 1){
        $updateF->pcityf = '1';
      }else{
        $updateF->pcityf = '0';
      }

      if($request->pupdatebyf == 1){
        $updateF->pupdatebyf = '1';
      }else{
        $updateF->pupdatebyf = '0';
      }

      if($request->pcreatedbyf == 1){
        $updateF->pcreatedbyf = '1';
      }else{
        $updateF->pcreatedbyf = '0';
      }

      if($request->pupdatedatf == 1){
        $updateF->pupdatedatf = '1';
      }else{
        $updateF->pupdatedatf = '0';
      }

      if($request->docverified == 1){
        $updateF->docverified = '1';
      }else{
        $updateF->docverified = '0';
      }

      if($request->docnotverified == 1){
        $updateF->docnotverified = '1';
      }else{
        $updateF->docnotverified = '0';
      }

      if($request->docmoderate == 1){
        $updateF->docmoderate = '1';
      }else{
        $updateF->docmoderate = '0';
      }

      if($request->ptyactive == 1){
        $updateF->ptyactive = '1';
      }else{
        $updateF->ptyactive = '0';
      }

      if($request->ptydeactive == 1){
        $updateF->ptydeactive = '1';
      }else{
        $updateF->ptydeactive = '0';
      }

      if($request->ptypermdeactive == 1){
        $updateF->ptypermdeactive = '1';
      }else{
        $updateF->ptypermdeactive = '0';
      }

      if($request->ptyconversation == 1){
        $updateF->conv_type_filter = '1';
      }else{
        $updateF->conv_type_filter = '0';
      }

      if($request->ptyconversationdte == 1){
        $updateF->notes_data_filter = '1';
      }else{
        $updateF->notes_data_filter = '0';
      }

      $updateF->save();
      return response()->json('success');

    }else{
      $createF = new PartyFilter();
      $createF->user_id = Auth::user()->user_id;

      if($request->partyf == 1){
        $createF->partyf = '1';
      }else{
        $createF->partyf = '0';
      }

      if($request->pstatusf == 1){
        $createF->pstatusf = '1';
      }else{
        $createF->pstatusf = '0';
      }

      if($request->pdocumentf == 1){
        $createF->pdocumentf = '1';
      }else{
        $createF->pdocumentf = '0';
      }

      if($request->pwstatusf == 1){
        $createF->pwstatusf = '1';
      }else{
        $createF->pwstatusf = '0';
      }

      if($request->preligionf == 1){
        $createF->preligionf = '1';
      }else{
        $createF->preligionf = '0';
      }

      if($request->pcareofff == 1){
        $createF->pcareofff = '1';
      }else{
        $createF->pcareofff = '0';
      }

      if($request->pleadownerfff == 1){
        $createF->pleadownerfff = '1';
      }else{
        $createF->pleadownerfff = '0';
      }

      if($request->pstatef == 1){
        $createF->pstatef = '1';
      }else{
        $createF->pstatef = '0';
      }

      if($request->pcityf == 1){
        $createF->pcityf = '1';
      }else{
        $createF->pcityf = '0';
      }

      if($request->pupdatebyf == 1){
        $createF->pupdatebyf = '1';
      }else{
        $createF->pupdatebyf = '0';
      }

      if($request->pcreatedbyf == 1){
        $createF->pcreatedbyf = '1';
      }else{
        $createF->pcreatedbyf = '0';
      }

      if($request->pupdatedatf == 1){
        $createF->pupdatedatf = '1';
      }else{
        $createF->pupdatedatf = '0';
      }

      if($request->ptyconversation == 1){
        $createF->conv_type_filter = '1';
      }else{
        $createF->conv_type_filter = '0';
      }

      if($request->ptyconversationdte == 1){
        $createF->notes_data_filter = '1';
      }else{
        $createF->notes_data_filter = '0';
      }

      $createF->save();
      return response()->json('success');

    }
  }

  public function party_update(Request $request)
  {

    if($request->hasFile('photo')) {
      $file = $request->file('photo');
      $file_count = File::files(base_path().'/public/image/service-master');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/service-master', $name);
      $photo = $name;

    } else {
      $photo = '';
    }

    if($request->hasFile('adhar_card')) {
      $file = $request->file('adhar_card');
      $file_count = File::files(base_path().'/public/image/service-master');
      $filecount = 0;
      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/service-master', $name);
      $adhar_card = $name;
    } else {
      $adhar_card = '';
    }

    if($request->hasFile('ration_card')) {
      $file = $request->file('ration_card');
      $file_count = File::files(base_path().'/public/image/service-master');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/service-master', $name);
      $ration_card = $name;

    } else {
      $ration_card = ''; 
    }

    if($request->hasFile('cv')) {
      $file = $request->file('cv');
      $file_count = File::files(base_path().'/public/image/service-master');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/service-master', $name);
      $cv = $name;

    } else {
      $cv = '';
    }

    if($request->hasFile('contract')){
      $file = $request->file('contract');
      $file_count = File::files(base_path().'/public/image/service-master');
      $filecount = 0;

      if ($file_count !== false) {
        $filecount = count($file_count);
      }
      $file_exe = $file->getClientOriginalExtension();
      $name = $filecount . '.' . $file_exe;

      $file->move(base_path().'/public/image/service-master', $name);
      $contract = $name;

    } else {
      $contract ='';
    }

    $party = Party::find($request->input('pty_id'));
    if($request->input('party_details')){
      $party->pty_full_name = $request->input('pty_full_name') ? $request->input('pty_full_name') : '';
      $party->pty_ag_name = $request->input('pty_ag_name') ? $request->input('pty_ag_name') : '';
      $party->pty_email = $request->input('pty_email') ? $request->input('pty_email') : '';
      $party->pty_comp_contact = $request->input('pty_comp_contact') ? $request->input('pty_comp_contact') : '';
      $party->pty_contact_no = $request->input('pty_contact_no') ? $request->input('pty_contact_no') : '';
      $party->pty_aadhar_no = $request->input('pty_aadhar_no') ? $request->input('pty_aadhar_no') : '';
      $party->pty_dob = $request->input('pty_dob') ? $request->input('pty_dob') : '';
    
      $party->country = $request->input('country') ? $request->input('country') : '';
      $party->state = $request->input('state') ? $request->input('state') : '';
      $party->city = $request->input('city') ? $request->input('city') : '';
      $party->address = $request->input('address') ? $request->input('address') : '';
      $party->pty_gender = $request->input('pty_gender') ? $request->input('pty_gender') : '';
      $party->pty_rel = $request->input('pty_gender') ? $request->input('pty_gender') : '';

      $party->p_mobile = $request->input('p_mobile') ? $request->input('p_mobile') : '';
      $party->c_person = $request->input('c_person') ? $request->input('c_person') : '';
      $party->sec_email = $request->input('sec_email') ? $request->input('sec_email') : '';
      $party->pincode = $request->input('pincode') ? $request->input('pincode') : '';
      $party->member_id = $request->input('membership') ? $request->input('membership') : '';
      // $party->user_id = Auth::user()->user_id;
      $party->care_of_id = $request->input('care_of_id') ? $request->input('care_of_id') : '';
      $party->update_by = Auth::user()->user_id;

      $party->verified = $request->input('verified') ? $request->input('verified') : '';
      $party->act_status = $request->input('act_status') ? $request->input('act_status') : '';

    }else if($request->input('files')){
      $party->photo = $photo ? $photo : $party->photo;
      $party->adhar_card = $adhar_card ? $adhar_card :  $party->adhar_card;
      $party->ration_card = $ration_card ? $ration_card : $party->ration_card;
      $party->cv = $cv ? $cv : $party->cv;
      // $party->contract = $contract ? $contract : $party->contract;

      // Check and Delete and Insert New File
      $partyDs = PartyFile::where('pty_id','=',$party->pty_id)->get();
      if(isset($partyDs)){
        foreach($partyDs as $partyD){
          $partyD->delete();
        }
      }
      // Insert New File
      if($photo !=''){
        $ppFile = new PartyFile();
        $ppFile->pty_id = $party->pty_id;
        $ppFile->doc_name = 'Photo';
        $ppFile->file = $photo;
        $ppFile->user_id = Auth::user()->user_id;
        $ppFile->save();
      }elseif($party->photo !=''){
        $ppFile = new PartyFile();
        $ppFile->pty_id = $party->pty_id;
        $ppFile->doc_name = 'Photo';
        $ppFile->file = $party->photo;
        $ppFile->user_id = Auth::user()->user_id;
        $ppFile->save();
      }

      if($adhar_card !=''){
        $ppFile = new PartyFile();
        $ppFile->pty_id = $party->pty_id;
        $ppFile->doc_name = 'Aadhar Card';
        $ppFile->file = $adhar_card;
        $ppFile->user_id = Auth::user()->user_id;
        $ppFile->save();
      }elseif($party->adhar_card !=''){
        $ppFile = new PartyFile();
        $ppFile->pty_id = $party->pty_id;
        $ppFile->doc_name = 'Aadhar Card';
        $ppFile->file = $party->adhar_card;
        $ppFile->user_id = Auth::user()->user_id;
        $ppFile->save();
      }

      if($ration_card !=''){
        $ppFile = new PartyFile();
        $ppFile->pty_id = $party->pty_id;
        $ppFile->doc_name = 'Business Card';
        $ppFile->file = $ration_card;
        $ppFile->user_id = Auth::user()->user_id;
        $ppFile->save();
      }elseif($party->ration_card != ''){
        $ppFile = new PartyFile();
        $ppFile->pty_id = $party->pty_id;
        $ppFile->doc_name = 'Business Card';
        $ppFile->file = $party->ration_card;
        $ppFile->user_id = Auth::user()->user_id;
        $ppFile->save();
      }

      if($cv !=''){
        $ppFile = new PartyFile();
        $ppFile->pty_id = $party->pty_id;
        $ppFile->doc_name = 'Other Document';
        $ppFile->file = $cv;
        $ppFile->user_id = Auth::user()->user_id;
        $ppFile->save();
      }elseif($party->cv !=''){
        $ppFile = new PartyFile();
        $ppFile->pty_id = $party->pty_id;
        $ppFile->doc_name = 'Other Document';
        $ppFile->file = $party->cv;
        $ppFile->user_id = Auth::user()->user_id;
        $ppFile->save();
      }

    }

    $ss = $party->update();
    // dd($ss);
    Session::flash('success', 'Party updated successfully !');
    return redirect('master/party/list'); 
  }

  public function party_file_upload(Request $request){

    $randomString = Str::random(16);
    $curr_date = date("Y-m-d-h-i-s");
    $filenameRe = $randomString.'-ID'.$request->pty_id.'-'.$curr_date;
    // dd($filenameRe);

    if($request->hasFile('file')) {
      $file = $request->file('file');
      // $file_count = File::files(base_path().'/public/image/service-master');
      // $filecount = 0;
      // if ($file_count !== false) {
      //   $filecount = count($file_count);
      // }

      $filename = $file->getClientOriginalName();
      $file_exe = $file->getClientOriginalExtension();
      $name = $filenameRe. '.' . $file_exe;
      $file->move(base_path().'/public/image/service-master', $name);
      $file = $name;

    } else {
      $file = '';
    }
    
    $post = new PartyFile();
    $post->pty_id = $request->pty_id;
    $post->doc_name = $request->doc_name;
    $post->file = $file;
    $post->user_id = Auth::user()->user_id;
    $post->save();

    // Session::flash('success','File Upload Successfully!');
    // return redirect()->back();
    // $dataResponse = "File Upload Successfully!";
    // return response()->json($dataResponse);
    
    $data2 = "File Upload Successfully!";  
    return response()->json($data2);
  
  }

  public function party_file_deleted(Request $request){
    $post = PartyFile::find($request->input('pty_file_id'));
    $post->delete();
    Session::flash('success','File deleted!');
    return redirect()->back();
  }

  public function ptyprodel(Request $request,$id){
    $post = Party::find($id);

    $file_path = public_path('image/service-master/'.$post->photo);
    if(file_exists($file_path)){
      @unlink($file_path);
      $post->photo = '';
      $post->save();
    }else{
      $post->photo = '';
      $post->save();
    }

    Session::flash('success','Photo deleted!');
    return redirect()->back();

  }


  public function getSingleParty(Request $request){
    $pty_id = $request->input('pty_id');
    $party = Party::find($pty_id);
    
    return response()->json($party);
  }

  public function setPartyStatus(Request $request){
    $pty_id = $request->input('pty_id');

    $post = Party::find($pty_id);
    $post->act_status = $request->act_status;
    $post->reason_pmd = $request->reason_pmd;
    $post->save();

    // Get API details
    $getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=',1)->first();

    if($request->act_status == 0){
      PartyWelcomeMessage::dispatch($post,$getAPI)->onQueue('default');
    }

    // Session::flash('success','Party Status changed!');
    // return redirect()->back();

    $data = "Party status changed!";
    return response()->json($data);

  }

  public function setVerified(Request $request){
    $pty_id = $request->input('pty_id');

    $post = Party::find($pty_id);
    $post->verified = $request->verified;
    $post->save();


    // Session::flash('success','Verified Status changed!');
    // return redirect()->back();

    $data = "Verified Status changed!";
    return response()->json($data);
  }

  public function party_view($id){
    $pageConfigs = ['pageHeader' => false];
    $party = Party::find($id);

    // $sp_data = DB::table('party_service_fees as psf')
    //   ->leftjoin('qr_service_tbl as ser','ser.ser_id','=','psf.sertype_id')
    //   ->leftjoin('qr_country as country','country.country_id','=','psf.country_id')
    //   ->select('psf.*','ser.*')
    //   ->where('psf.pty_id','=',$id)
    //   ->get();

    return view('/content/service-master/party-view', ['pageConfigs' => $pageConfigs,'party' => $party]);
  }


  public function storePtyrem(Request $request){
    // dd($request);

    // check previous reminder is available or not
    $pposts = PartyReminder::where('pty_id','=',$request->pty_id)->where('status','=',0)->get();
    if($pposts->count() > 0){
      foreach($pposts as $ppost){
        $ppost->status = 1;
        $ppost->save();
      }
    }

    $post = new PartyReminder();
    $post->pty_id = $request->pty_id;
    $post->user_id = $request->careoff_id;
    $post->title = $request->title;
    $post->desc = $request->desc;
    $post->reminder_type = $request->task_type;
    $post->due_date = $request->due_date;
    $post->time = $request->time;
    $post->save();

    $data = "Reminder created!";
    return response()->json($data);

    // Session::flash('success','Reminder created!');
    // return redirect()->back();
  }

  public function editPtyrem(Request $request){
    $post = PartyReminder::find($request->id);

    return response()->json($post);
  }

  public function updatePtyrem(Request $request){
    $post = PartyReminder::find($request->remider_id);
    

    // $post->pty_id = $request->pty_id;
    // $post->user_id = $request->careoff_id;
    // $post->title = $request->title;
    // $post->desc = $request->desc;
    // $post->reminder_type = $request->task_type;
    // $post->due_date = $request->due_date;
    // $post->time = $request->time;
    

    if($request->new_reminder == 'required'){
      $newReminder = new PartyReminder();
      $newReminder->pty_id = $request->pty_id;
      $newReminder->user_id = $request->careoff_id;
      $newReminder->title = $request->title2;
      $newReminder->desc = $request->desc2;
      $newReminder->reminder_type = $request->task_type2;
      $newReminder->due_date = $request->due_date2;
      $newReminder->time = $request->time2;
      $newReminder->save();
    }else{
      // Create New Reminder with same date and time
      
      $post->title = $request->title;
      $post->desc = $request->desc;
    
    }

    $post->status = '1';
    $post->save();

    $data = "Reminder created!";  
    return response()->json($data);
  }

  public function contreminder(Request $request){
    // check previous reminder is available or not
    $pposts = ContactReminder::where('cont_id','=',$request->cont_id)->where('status','=',0)->get();

    if($pposts->count() > 0){
      foreach($pposts as $ppost){
        $ppost->status = 1;
        $ppost->save();
      }
    }

    $post = new ContactReminder();
    $post->cont_id = $request->cont_id;
    $post->user_id = $request->assign_id;
    $post->title = $request->title;
    $post->desc = $request->desc;
    $post->reminder_type = $request->task_type;
    $post->due_date = $request->due_date;
    $post->time = $request->time;
    $post->save();

    $data = "Reminder created!";
    return response()->json($data);

  }

  public function party_service_price_view_final_json(Request $request){
    $pty_id = $request->input('pty_id');
    $sp_data = DB::table('party_service_fees as psf')
      ->leftjoin('qr_service_tbl as ser','ser.ser_id','=','psf.sertype_id')
      ->leftjoin('qr_country as country','country.country_id','=','psf.country_id')
      ->select('psf.*','psf.created_at as sp_created_at','ser.*','country.*')
      ->where('psf.pty_id','=',$pty_id)
      ->get();

    $data['data'] = $sp_data;
    return response()->json($data);
  }

  public function party_service_price2_edit(Request $request){
    $sp_id = $request->input('sp_id') ? $request->input('sp_id'): '';
    $data = PartyServiceFee::find($sp_id);
    return response()->json($data);
  }

  public function party_service_price2_update(Request $request){

    // dd($request);

    $id = $request->input('sp_id');
    $sp_service = $request->input('ser_id') ? $request->input('ser_id'): '';
    $sp_price = $request->input('sp_price') ? $request->input('sp_price'): '';
    $visa_stamp_price = $request->input('visa_stamp_price') ? $request->input('visa_stamp_price'): '';
    $mofa_price = $request->input('mofa_price') ? $request->input('mofa_price'): '';
    $country_id = $request->input('country_id') ? $request->input('country_id'): '';

    $status = PartyServiceFee::where('pty_id',$request->input('pty_id'))
      ->where('sertype_id','=',$sp_service)
      ->where('country_id','=',$country_id)
      ->update(array('status' => 0));

      $post = PartyServiceFee::find($id);
      $post->pty_id = $request->pty_id;
      $post->country_id = $request->country_id;
      $post->sertype_id = $request->ser_id;
      $post->service_charge = $request->service_charge;
      if($request->ser_id == 2){
        $post->visa_stamp_price = '0';
        $post->mofa_price = '0';
        $post->sp_price = $request->sp_price;
      }else{
        $post->visa_stamp_price = $request->visa_stamp_price;
        $post->mofa_price = $request->mofa_price;
        $post->sp_price = '0';
      }
      // $post->visa_stamp_price = $request->visa_stamp_price;
      // $post->mofa_price = $request->mofa_price;
      // $post->sp_price = $request->sp_price;

      $post->save();
      // Check and disable previous

      // In-Active service charge if same request is appear
      $post2s = PartyServiceFee::where('user_id','=',Auth::user()->user_id)->where('sertype_id','=',$request->ser_id)->where('id','!=',$id)->get();

      if(isset($post2s)){
        foreach($post2s as $post2){
          $post2->status = '1';
          $post2->save();
        }
      }


      Session::flash('success','Party Price updated!');
      return redirect()->back();

  }

  public function editPartyAdd(Request $request){
    $id = $request->id;
    $post = PartyAddress::find($id);
    return response()->json($post);
  }

  public function updatePartyAdd(Request $request){
    $id = $request->input('add_pty_id');
    $post = PartyAddress::find($id);
    $post->name = $request->input("name");
    $post->office_name = $request->input("office_name");
    $post->address = $request->input("address");
    $post->pincode = $request->input("pincode");
    $post->city = $request->input('city');
    $post->save();
    Session::flash('success','Party Address updated successfully!');
    return redirect()->back();
  }

  public function party_service_price_store2(Request $request){

    

    $post = new PartyServiceFee();
    $post->user_id = Auth::user()->user_id;
    $post->pty_id = $request->pty_id;
    $post->country_id = $request->country_id;
    $post->sertype_id = $request->ser_id;
    // $post->sp_price = $request->sp_price;
    // $post->visa_stamp_price = $request->visa_stamp_price;
    // $post->mofa_price = $request->mofa_price;
    $post->service_charge = $request->service_charge;
    $post->save();

    // Get Last ID
    $get_last_id = $post->id;

    // In-Active service charge if same request is appear
    $post2s = PartyServiceFee::where('user_id','=',Auth::user()->user_id)->where('sertype_id','=',$request->ser_id)->where('id','!=',$get_last_id)->get();

    if(isset($post2s)){
      foreach($post2s as $post2){
        $post2->status = '1';
        $post2->save();
      }
    }


    Session::flash('success','Party price created!');
    return redirect()->back();
  } 

  public function addparty(Request $request){
    $pty_id = $request->input('pty_id');
    $addr = $request->input('address');
    $name = $request->input('name');
    $office_name = $request->input('office_name');
    $city = $request->input('city');

    if($request->partadd_id != 'New'){
      $addUP = PartyAddress::find($request->partadd_id);
      $addUP->name = $name;
      $addUP->office_name = $office_name;
      $addUP->address = $addr;
      $addUP->pincode = $request->input('pincode');
      $addUP->city = $city;
      $addUP->save();

      // Update in Courier Clone and Courier
      $courier = Courier::where('last_clone_id','=',$request->courier_id)->first();
      $courierC = Courierclone::where('id','=',$request->courier_id)->first();

      $courier->courier_to = $request->partadd_id;
      $courierC->courier_to = $request->partadd_id;
      $courier->save();
      $courierC->save();
    }else{
      $addP = new PartyAddress();
      $addP->pty_id = $pty_id;
      $addP->name = $name;
      $addP->office_name = $office_name;
      $addP->address = $addr;
      $addP->pincode = $request->input('pincode');
      $addP->city = $city;
      $addP->user_id = Auth::user()->user_id;
      $addP->save();

      // Update in Courier Clone and Courier
      $courier = Courier::where('last_clone_id','=',$request->courier_id)->first();
      $courierC = Courierclone::where('id','=',$request->courier_id)->first();

      $courier->courier_to = $addP->id;
      $courierC->courier_to = $addP->id;
      $courier->save();
      $courierC->save();
    }


    Session::flash('success','Party Address Added!');
    return redirect()->back();
  }


  public function addparty2(Request $request){
    
    $addP = new PartyAddress();
    $addP->pty_id = $request->pty_id;
    $addP->name = $request->name;
    $addP->office_name = $request->office_name;
    $addP->address = $request->address;
    $addP->pincode = $request->pincode;
    $addP->city = $request->city;
    $addP->user_id = Auth::user()->user_id;
    $addP->save();

    Session::flash('success','Party address added!');
    return redirect()->back();
  }

  public function deletepartyAdd(Request $request){
    $id = $request->id;
    $post = PartyAddress::where('id','=',$id)->first();
    $post->delete();

  }

  public function deactive(Request $request)
  {
    extract($_POST);
    $po = Party::find($pty_id);
    $po->act_status = 1;
    $po->save();
    Session::flash('success', 'Party Status Deactived!');
    return redirect()->back(); 

  }

  public function active(Request $request)
  {
    extract($_POST);
    $po = Party::find($pty_id);
    $po->act_status = 0;
    $po->save();
    Session::flash('success', 'Party Status actived!');
    return redirect()->back(); 
  }


  public function party_edit($id)
  {
    $pty = Party::find($id);
    $pageConfigs = ['pageHeader' => false];
    return view('/content/service-master/party-edit', ['pageConfigs' => $pageConfigs,'pty' => $pty]);


  }

  public function party_delete(Request $request)
  {

    // dd($request);

    extract($_POST);
    $p = Party::find($pty_id);
    $p->delete();
    Session::flash('success', 'Party deleted successfully !');
    return redirect('master/party/list');
  }

  public function contact_type_list()
  {
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm-master/contact-type-list', ['pageConfigs' => $pageConfigs]);
  }

  public function contact_type_list_json(Request $request)
  {
    $cont = ContactType::all();
    $data['data'] = $cont;
    return response()->json($data);
  }

  public function contact_type_store(Request $request)
  {
    extract($_POST);
    $cont = new ContactType();
    $cont->type= $type ? $type : '';
    $cont->user_id = Auth::user()->user_id;
    $cont->save();
    Session::flash('success','Contact type inserted successfully');
    return redirect('master/contact/type/list');
  }

  public function contact_type_edit(Request $request)
  {
    extract($_POST);
    $cont = ContactType::find($id);

    return response()->json($cont);
  }

  public function contact_type_update(Request $request)
  {
    extract($_POST);

    $cont = ContactType::find($id);
    $cont->type = $type;
    $cont->save();
    Session::flash('success','Contact type updated successfully');
    return redirect('master/contact/type/list');

  }

  public function contact_type_delete($id)
  {

    $cont = ContactType::find($id);
    $cont->delete();
    Session::flash('success','Contact type deleted successfully');
    return redirect('master/contact/type/list');
  }


  public function conversation_store(Request $request)
  {
    $conv = new ConversationContact();
    $conv->user_id = Auth::user()->user_id;
    $conv->user_type = Auth::user()->user_type;
    $conv->cont_id = $request->input('cont_id') ? $request->input('cont_id') : '';
    $conv->conversation = $request->input('conversation') ? $request->input('conversation') : '';
    $conv->conver_on = $request->input('conver_on') ? $request->input('conver_on') : '';
    $conv->save();

    $tm = new TimelineContactCrm();
    $tm->cont_id = $request->input('cont_id') ? $request->input('cont_id') : '';
    $tm->subject = 'Conversation created';
    $tm->insert_id = $conv->id;
    $tm->user_type = Auth::user()->user_type;
    $tm->user_id = Auth::user()->user_id;
    $tm->save();

    Session::flash('success','Conversation created successfully');
    return redirect('master/contact/view/'.$request->input('cont_id'));
  }

  public function contact_list_json_new_lead(Request $request)
  {

  $data_comp = DB::table('qr_company_contact as cont')

  ->leftjoin('qr_city as city', 'cont.city2', '=', 'city.city_id')
  ->leftjoin('qr_company as comp', 'comp.id', '=', 'cont.comp_id')
  ->leftjoin('qr_lifecycle_stage as cy', 'cy.id', '=', 'cont.lifecy_stage')
  ->leftjoin('qr_lead_stage as ld', 'ld.id', '=', 'cont.lead_stage')
  ->leftjoin('qr_country as country', 'country.country_id', '=', 'comp.country')
  ->leftjoin('qr_staff_tbl as st', 'st.staff_id', '=', 'cont.staff')
  ->leftjoin('qr_industry as ind', 'ind.id', '=', 'comp.industry')
  ->select('cont.*','comp.*','cy.name as lifecy_stage','ld.name as lead_stage','comp.id as cid','cont.id as cont_id','city.*','st.staff_fname as stf_name','st.staff_lname as stl_name','ind.name as indname','country.country_name','cont.created_at as cont_created','cont.updated_at as cont_updated')
  ->orderBy('cont.id','DESC')
  ->whereDate('cont.created_at',Carbon::today())
  ->get();

  $data['data'] = $data_comp;
  return response()->json($data);
  }

  public function contact_list_json_today_followup(Request $request)
  {

  $data_comp = DB::table('qr_company_contact as cont')

  ->leftjoin('qr_city as city', 'cont.city2', '=', 'city.city_id')
  ->leftjoin('qr_company as comp', 'comp.id', '=', 'cont.comp_id')
  ->leftjoin('qr_lifecycle_stage as cy', 'cy.id', '=', 'cont.lifecy_stage')
  ->leftjoin('qr_lead_stage as ld', 'ld.id', '=', 'cont.lead_stage')
  ->leftjoin('qr_country as country', 'country.country_id', '=', 'comp.country')
  ->leftjoin('qr_staff_tbl as st', 'st.staff_id', '=', 'cont.staff')
  ->leftjoin('qr_industry as ind', 'ind.id', '=', 'comp.industry')
  ->select('cont.*','comp.*','cy.name as lifecy_stage','ld.name as lead_stage','comp.id as cid','cont.id as cont_id','city.*','st.staff_fname as stf_name','st.staff_lname as stl_name','ind.name as indname','country.country_name','cont.created_at as cont_created','cont.updated_at as cont_updated')
  ->orderBy('cont.id','DESC')
  ->whereDate('cont.next_followup',Carbon::today())
  ->get();

  $data['data'] = $data_comp;
  return response()->json($data);
  }

  public function contact_list_json_followup_updated(Request $request)
  {

  $data_comp = DB::table('qr_company_contact as cont')

  ->leftjoin('qr_city as city', 'cont.city2', '=', 'city.city_id')
  ->leftjoin('qr_company as comp', 'comp.id', '=', 'cont.comp_id')
  ->leftjoin('qr_lifecycle_stage as cy', 'cy.id', '=', 'cont.lifecy_stage')
  ->leftjoin('qr_lead_stage as ld', 'ld.id', '=', 'cont.lead_stage')
  ->leftjoin('qr_country as country', 'country.country_id', '=', 'comp.country')
  ->leftjoin('qr_staff_tbl as st', 'st.staff_id', '=', 'cont.staff')
  ->leftjoin('qr_industry as ind', 'ind.id', '=', 'comp.industry')
  ->select('cont.*','comp.*','cy.name as lifecy_stage','ld.name as lead_stage','comp.id as cid','cont.id as cont_id','city.*','st.staff_fname as stf_name','st.staff_lname as stl_name','ind.name as indname','country.country_name','cont.created_at as cont_created','cont.updated_at as cont_updated')
  ->orderBy('cont.id','DESC')
  ->whereDate('cont.next_followup','<',Carbon::today())
  ->get();

  $data['data'] = $data_comp;
  return response()->json($data);
  }

  public function contact_list_json_next_followup(Request $request)
  {

  $data_comp = DB::table('qr_company_contact as cont')

  ->leftjoin('qr_city as city', 'cont.city2', '=', 'city.city_id')
  ->leftjoin('qr_company as comp', 'comp.id', '=', 'cont.comp_id')
  ->leftjoin('qr_lifecycle_stage as cy', 'cy.id', '=', 'cont.lifecy_stage')
  ->leftjoin('qr_lead_stage as ld', 'ld.id', '=', 'cont.lead_stage')
  ->leftjoin('qr_country as country', 'country.country_id', '=', 'comp.country')
  ->leftjoin('qr_staff_tbl as st', 'st.staff_id', '=', 'cont.staff')
  ->leftjoin('qr_industry as ind', 'ind.id', '=', 'comp.industry')
  ->select('cont.*','comp.*','cy.name as lifecy_stage','ld.name as lead_stage','comp.id as cid','cont.id as cont_id','city.*','st.staff_fname as stf_name','st.staff_lname as stl_name','ind.name as indname','country.country_name','cont.created_at as cont_created','cont.updated_at as cont_updated')
  ->orderBy('cont.id','DESC')
  ->whereDate('cont.next_followup','>',Carbon::today())
  ->get();

  $data['data'] = $data_comp;
  return response()->json($data);
  }

  public function contact_list_json_appointment(Request $request)
  {

  $data_comp = DB::table('qr_company_contact as cont')

  ->leftjoin('qr_city as city', 'cont.city2', '=', 'city.city_id')
  ->leftjoin('qr_company as comp', 'comp.id', '=', 'cont.comp_id')
  ->leftjoin('qr_lifecycle_stage as cy', 'cy.id', '=', 'cont.lifecy_stage')
  ->leftjoin('qr_lead_stage as ld', 'ld.id', '=', 'cont.lead_stage')
  ->leftjoin('qr_country as country', 'country.country_id', '=', 'comp.country')
  ->leftjoin('qr_staff_tbl as st', 'st.staff_id', '=', 'cont.staff')
  ->leftjoin('qr_industry as ind', 'ind.id', '=', 'comp.industry')
  ->leftjoin('qr_crm_contact_appoint as apt', 'apt.cont_id', '=', 'cont.id')
  ->select('cont.*','comp.*','cy.name as lifecy_stage','ld.name as lead_stage','comp.id as cid','cont.id as cont_id','city.*','st.staff_fname as stf_name','st.staff_lname as stl_name','ind.name as indname','country.country_name','cont.created_at as cont_created','cont.updated_at as cont_updated')
  ->orderBy('cont.id','DESC')
  ->where('apt.id','!=',0)
  ->get();

  $data['data'] = $data_comp;
  return response()->json($data);
  }

  public function party_service_price()
  {
    $pageConfigs = ['pageHeader' => false];
    return view('/content/service-master/party-service-price', ['pageConfigs' => $pageConfigs]);
  }

  public function party_service_price_view($id)
  {
    $sp_data = DB::table('qr_service_price as sp')
    ->leftjoin('qr_service_tbl as ser', 'ser.ser_id','=','sp.sp_service')
    ->select('sp.*','ser.*')
    ->where('sp.pty_id','=',$id)
    ->get();

    $pty_data = DB::table('qr_party_tbl as pty')
    ->leftjoin('qr_country as co', 'co.country_id','=','pty.country')
    ->leftjoin('qr_states as st', 'st.id','=','pty.state')
    ->leftjoin('qr_cities as ct', 'ct.id','=','pty.city')
    ->leftjoin('qr_nationality_tbl as nt', 'nt.nat_id','=','pty.nat_id')

    ->select('pty.*','co.*','st.*','ct.*','nt.*')
    ->where('pty.pty_id','=',$id)
    ->first();


    $pageConfigs = ['pageHeader' => false];
    return view('/content/service-master/party-service-price-view', ['pageConfigs' => $pageConfigs,'sp_data' => $sp_data,'pty' => $pty_data]);
  }

  public function party_service_price_view_json(Request $request)
  {
    $pty_id = $request->input('pty_id');
    $sp_data = DB::table('qr_service_price as sp')
    ->leftjoin('qr_service_tbl as ser', 'ser.ser_id','=','sp.sp_service')
    ->leftjoin('qr_country as co', 'co.country_id','=','sp.country_id')
    ->select('sp.*','sp.created_at as sp_created_at','ser.*','co.*')
    ->where('sp.pty_id','=',$pty_id)
    ->get();

    $data['data'] = $sp_data;
    return response()->json($data);
  }

  public function party_service_price_store(Request $request)
  {
  
    $status = PartyServicePrice::where('pty_id','=',$request->input('pty_id'))->where('sp_service','=',$request->input('ser_id'))->where('country_id','=',$request->input('country_id'))->update(array('status' => 0));

    $sp = new PartyServicePrice();
    $sp->pty_id = $request->input('pty_id') ? $request->input('pty_id') :'';
    $sp->sp_service = $request->input('ser_id') ? $request->input('ser_id') :'';
    $sp->sp_price = $request->input('sp_price') ? $request->input('sp_price') :'';
    $sp->visa_stamp_price = $request->input('visa_stamp_price') ? $request->input('visa_stamp_price') :'';
    $sp->mofa_price = $request->input('mofa_price') ? $request->input('mofa_price') :'';
    $sp->country_id = $request->input('country_id') ? $request->input('country_id') :'';
    $sp->status =1;
    $sp->user_id = Auth::user()->user_id;
    $sp->save();

    Session::flash('success','Price created successfully');
    return redirect()->back();
  }

  public function party_service_price_json(Request $request)
  {
  $result = DB::table('qr_service_price as sp')
  ->leftjoin('qr_party_tbl as pty', 'sp.pty_id', '=', 'pty.pty_id')
  ->select('sp.sp_id','pty.pty_ag_name','sp.pty_id')
  ->groupBy('sp.pty_id')
  ->where('sp.pty_id','!=',0)
  ->get();

  $data['data'] =$result;
  return response()->json($data);
  }

  public function party_service_price_edit(Request $request)
  {
  $sp_id = $request->input('sp_id') ? $request->input('sp_id'): '';
  $data = PartyServicePrice::find($sp_id);
  return response()->json($data);
  }

  public function party_service_price_update(Request $request)
  {

    $sp_id = $request->input('sp_id') ? $request->input('sp_id'): '';
    $sp_service = $request->input('ser_id') ? $request->input('ser_id'): '';
    $sp_price = $request->input('sp_price') ? $request->input('sp_price'): '';
    $visa_stamp_price = $request->input('visa_stamp_price') ? $request->input('visa_stamp_price'): '';
    $mofa_price = $request->input('mofa_price') ? $request->input('mofa_price'): '';
    $country_id = $request->input('country_id') ? $request->input('country_id'): '';


    $status = PartyServicePrice::where('pty_id','=',$request->input('pty_id'))
    ->where('sp_service','=',$request->input('ser_id'))
    ->where('country_id','=',$request->input('country_id'))
    ->update(array('status' => 0));

  


    $data = PartyServicePrice::find($sp_id);
    $data->sp_service = $sp_service;
    $data->sp_price = $sp_price;
    $data->visa_stamp_price = $visa_stamp_price;
    $data->mofa_price = $mofa_price;
    $data->country_id = $country_id;
    $data->status = 1; 

    $data->update();
    Session::flash('success','Service price upadted successfully');
    return redirect()->back();

  }

  public function get_service_price(Request $request)
  {
    $pty_id = $request->input('pty_id');

    $result = DB::table('qr_service_price as sp')
    ->leftjoin('qr_service_tbl as ser', 'ser.ser_id', '=', 'sp.sp_service')
    ->select('sp.sp_price','ser.ser_name')
    ->where('sp.pty_id','=',$pty_id)
    ->get();
    if($result){
      foreach ($result as $r) {
        ?>
        <div class="col-md-4">
          <div class="form-group">
            <label><?php echo $r->ser_name?></label>
            <div class="ticket-div"><?php echo $r->sp_price?> /-</div>
          </div>
        </div>
        <?php
      }
    }

  }

  public function place_issue_list()
  {
    $pageConfigs = ['pageHeader' => false];
    return view('/content/service-master/place-issue', ['pageConfigs' => $pageConfigs]);
  }

  public function place_issue_list_json(Request $request)
  {
    $places = CandidatePlaceIssue::all();
    $data['data'] = $places;
    return response()->json($data);
  }


  public function place_issue_store(Request $request)
  {
    $result = new CandidatePlaceIssue();
    $result->place = $request->input('place') ? $request->input('place') :'';
    $result->user_id = Auth::user()->user_id;
    $result->save();
    Session::flash('success','Place of issue created successfully');
    return redirect('master/place/issue/list');
  }

  public function place_issue_edit(Request $request)
  {
    $result = CandidatePlaceIssue::find($request->input('edit_id'));
    return response()->json($result);
  }

  public function place_issue_update(Request $request)
  {
    $data = CandidatePlaceIssue::find($request->input('edit_id'));
    $data->place =  $request->input('place') ? $request->input('place') : '';
    $data->save();
    Session::flash('success','Place of issue updated successfully');
    return redirect('master/place/issue/list');

  }

  public function place_issue_delete(Request $request)
  {
    $data = CandidatePlaceIssue::find($request->input('edit_id'));
    $data->delete();
    Session::flash('success','Place of issue deleted successfully');
    return redirect()->back();
  }

  public function service_list()
  {
    $pageConfigs = ['pageHeader' => false];
    return view('/content/service-master/service-add', ['pageConfigs' => $pageConfigs]);
  }

  public function service_list_json(Request $request)
  {
    $res = Service::all();
    $data['data'] = $res;
    return response()->json($data);
  }


  public function service_store(Request $request)
  {
    $res = new Service();
    $res->ser_name  = $request->input('ser_name') ? $request->input('ser_name') : '';
    $res->type= $request->input('type') ? $request->input('type') : '';
    $res->user_id = Auth::user()->user_id;
    $res->save();

    Session::flash('success','Service created successfully');
    // return redirect('master/service/list');
    return redirect()->back();
  }

  public function service_edit(Request $request)
  {
    $res = Service::find($request->input('edit_id'));
    return response()->json($res);

  }



  public function service_update(Request $request)
  {
    $res = Service::find($request->input('edit_id'));
    $res->ser_name  = $request->input('ser_name') ? $request->input('ser_name') : '';
    $res->type= $request->input('type') ? $request->input('type') : '';
    $res->update();

    Session::flash('success','Service updated successfully');
    return redirect('master/service/list');
  }

  public function service_delete(Request $request)
  {
    $res = Service::find($request->input('edit_id'));
    $res->delete();

    $all_mofa = MofaPrice::where('ser_id','=',$request->input('edit_id'))->delete();
    

    Session::flash('success','Visa type deleted successfully');
    return redirect()->back();
  }

  public function getImport()
  {
    return view('import');
  }

  public function parseImport(CsvImportRequest $request)
  {

    $path = $request->file('csv_file')->getRealPath();

    

    if ($request->has('header')) {
      $data = Excel::load($path, function($reader) {})->get()->toArray();
    } else {
      $data = array_map('str_getcsv', file($path));
    }

    // dump($data);



    if (count($data) > 0) {
      $csv_header_fields = [];
      foreach ($data[0] as $key => $value) {
        $csv_header_fields[] = $value;
      }

      // dump($csv_header_fields);

      // foreach ($data as $key => $value) {
      //   if($key!=0){
      //     $csv_data[] = $value;
      //   }
      // }

      // $csv_data = array_slice($data, 1, 1000000);
      $csv_data = array_slice($data,1,count($data));

      // dump($csv_data[1]);

      $csv_data_file = ContactCsvData::create([
        'csv_filename' => $request->file('csv_file')->getClientOriginalName(),
        'csv_header' => json_encode($csv_header_fields),
        'csv_data' => json_encode($csv_data)
      ]);

      // dump($csv_data);

    } else {
      return redirect()->back();
    }

    return  view('content/crm/contact-import-data')->with(compact( 'csv_header_fields', 'csv_data', 'csv_data_file'));
  }

  // Whatsapp API List
  public function whatsappilist(){
    $pageConfigs = ['pageHeader' => false];
    return view('/content/crm/whatsappilist',['pageConfigs' => $pageConfigs]);
  }

  public function whatsappilist_json(Request $request){

    $perms = AccessPermissionModule2::where('user_id','=',Auth::user()->user_id)->first();

    // $user_type = Auth::user()->user_type;

    if(Auth::user()->user_type == 1 || (isset($perms) && $perms->full_access == 1)){
      $data_d = DB::table('userwhatsappapis as userD')
      ->leftjoin('users as user','user.user_id','=','userD.staff_id')
      ->select('userD.*','user.name')
      ->orderBy('id','DESC')
      ->get();
    
      $data['data'] = $data_d;

      return response()->json($data);
    }elseif(Auth::user()->user_type == 2 || (isset($perms) && $perms->full_access == 0)){


      if($perms->whatsAPIr == 1){
        $data_d = DB::table('userwhatsappapis as userD')
        ->leftjoin('users as user','user.user_id','=','userD.staff_id')
        ->select('userD.*','user.name')
        ->orderBy('id','DESC')
        ->get();
      }else{
        $data_d = DB::table('userwhatsappapis as userD')
        ->leftjoin('users as user','user.user_id','=','userD.staff_id')
        ->select('userD.*','user.name')
        ->where('userD.staff_id','=',Auth::user()->user_id)
        ->where('userD.user_for','=','0')
        ->orderBy('id','DESC')
        ->get();
      }

    

      $data['data'] = $data_d;

      return response()->json($data);
    }


          
  }

  public function whatsappilist_view(Request $request){
    $id =  $request->input('id');
    $post = Userwhatsappapi::find($id);
    return response()->json($post);
  }

  public function whatsappilist_edit(Request $request){
    $id = $request->input('id');
    $post = Userwhatsappapi::find($id);
    return response()->json($post);
  }

  public function whatsappilist_update(Request $request){
    $id = $request->input('wedit_id');
    $post = Userwhatsappapi::find($id);
    $post->instance_key = $request->input('instance_key');
    $post->api_key = $request->input('api_key2');
    $post->text_message_url = $request->input('text_message_url2');
    $post->media_message_url = $request->input('media_message_url2');
    $post->notes = $request->input('notes');
    $post->updateby_id = Auth::user()->user_id;
    $post->mobile_no = $request->input('mobile_no');
    $post->staff_id = $request->input('staff_id');
    $post->api_for = $request->input('api_for');
    $post->company_name = $request->company_name;
    if(Auth::user()->user_id == 1){
      $post->user_for = $request->user_for;
    }
    $post->save();

    Session::flash('success','API Details updated Successfully!');
    return redirect()->back();

  }

  public function whatsappilist_store(Request $request){

    $numberadd = Numberadd::find($request->assoc_number_id);

    $post = new Userwhatsappapi();
    $post->user_id = Auth::user()->user_id;
    $post->instance_key = $request->input('instance_key');
    $post->api_key = $request->input('api_key');
    $post->text_message_url = $request->input('text_message_url');
    $post->media_message_url = $request->input('media_message_url');
    $post->notes = $request->input('notes');
    // $post->mobile_no = $request->input('mobile_no');
    $post->mobile_no = $numberadd->number;
    $post->staff_id = $request->staff_id;    
    $post->api_for = $request->api_for;
    $post->company_name = $request->company_name;
    $post->assoc_number_id = $request->assoc_number_id;

    if(Auth::user()->user_id == '1'){
      $post->user_for = $request->user_for;
    }
    $post->save();

    Session::flash('success','API Details updated Successfully!');
    return redirect()->back();
  }


  public function processImport(Request $request)
  {

    $data = ContactCsvData::find($request->csv_data_file_id);
    $csv_data = json_decode($data->csv_data, true);
    // $fields = $request->input('fields');

    $csv_header = json_decode($data->csv_header, true);

    $reqField = $request->fields;

    // dd($request);
    // dd($reqField);
    // dd(array_values($reqField));
    // dd($csv_header);
    // dd($data->csv_data);
    // dd($csv_data[5][5]);
    // dd(config('app.db_fields'));
    $i=0;

    $data_field = config('app.db_fields');
    // dd($data_field[9]);

    $search_value = array_search('13',$reqField);
    // dd($search_value);

    // Flip Request
    // $getFlipreq = array_flip($request->fields);
    // dd($getFlipreq);

    $ext = array_key_exists(29,$reqField);
    $keyVal = in_array(2,$reqField);

    // dd($keyVal);
    // in All Contact 
    
    // $checkDB  = AllContact::where('mobile_no','=',$csv_data[0][5])->first();
    // dd($checkDB);
      
    foreach ($csv_data as $row) {
      $post = new AllContact();

      foreach (config('app.db_fields') as $key => $field) {


        // get value of request
        $getReqVal = array_search($key, $request->fields);
        // Get true and false
        $checkDetail = in_array($key,$request->fields); 
        // Flip array

        // Check if data are exists on database
        $checkDb = AllContact::where($field,'=',$row[$getReqVal])->first();

        $getNewField = array_flip($request->fields);
        
        if ($data->csv_header) {
          if($checkDetail == true){
    
            $post->$field = $row[$getReqVal];
            // $post->$field = $row[$getNewField[$key]];
          }else{
            $post->$field = '';
          }

        } else {
          if($checkDetail == true){
            $post->$field = $row[$getNewField[$key]];
            // $post->$field = $row[$getReqVal];
          }else{
            $post->$field = '';
          }
        }

        $post->lead_type = $request->lead_type;
        $post->user_id = Auth::user()->user_id;
        $post->assign_id = Auth::user()->user_id;
        $post->lcs_id = '4';
        $post->ls_id = '2';
        $post->save();

        

      }
    }
    

    // if(count($csv_header) > 0){
    //   foreach($csv_data as $row){
    //     // $importData = new ImportExport();
    //     $importData = new AllContact();
    //     foreach(config('app.db_fields') as $index => $field){
    //       $existKey = array_key_exists($index,$request->fields);
    //       $existValue = in_array($index,$request->fields);
    //       $search_value = array_search($index,$request->fields);
    //       if($data->csv_header){
    //         if($existValue != false){
    //           if($search_value != false){
    //             $importData->$field = $row[$search_value];
    //           }
    //         }else{
    //           $importData->$field = null;
    //         }   
                          
    //       }else{

    //         if($existValue != false){
    //           if($search_value != false){
    //             $importData->$field = $row[$search_value];
    //           }

    //         }else{
    //           $importData->$field = null;
    //         }     
    //       }
    //     }
    //     $importData->user_id = Auth::user()->user_id;
    //     $importData->assign_id = Auth::user()->user_id;
    //     $importData->save();
    //   }
    // }


    // foreach ($csv_data as $row) {
    //   $contact = new CompanyContact();

        

    //   if ($row[0]) {

    //     $contact->full_name = $row[0];

    //   }

    //   if($row[1]){
    //     $contact->phone = $row[1].','.$row[2].','.$row[3].','.$row[4];
    //   }

    //   if($row[5]){
    //     $contact->email = $row[5].','.$row[6].','.$row[7].','.$row[8].','.$row[9].','.$row[10];
    //   }
    //   if($row[11])
    //   {
    //     $contact->source = $row[11];
    //   }

    //   if($row[12])
    //   {
    //     $chk_st = Staff::Where('staff_fname', 'like', '%' . $row[12] . '%')->first();
    //     if(isset($chk_st)){
    //       $contact->staff =  $chk_st->staff_id;
    //     }else{
    //       $st = new Staff();
    //       $st->staff_fname= $row[12];
    //       $st->save();

    //       $contact->staff =  $st->staff_id;
    //     }
    //   }

    //   if($row[14])
    //   {
    //     $chk_co = Company::Where('comp_name', 'like', '%' . $row[14] . '%')->first();
    //     if(isset($chk_co))
    //     {
    //       $contact->comp_id =  $chk_co->id;
    //     }else{
    //       $chk_co = new Company();
    //       $chk_co->comp_name= $row[14];
    //       $chk_co->save();
    //       $contact->comp_id = $chk_co->id;
    //     }
    //   }

    //   if($row[13]){
    //     $chk_ind = Industry::Where('name', 'like', '%' . $row[13] . '%')->first();
    //     if(isset($chk_ind))
    //     {
    //       $company = Company::Where('comp_name', 'like', '%' . $row[14] . '%')->first();
    //       $company->industry =  $chk_ind->id;
    //       $company->save();
    //     }
    //     else
    //     {
    //       $ind = new Industry();
    //       $ind->name= $row[13];
    //       $ind->save();
    //       $company = Company::Where('comp_name', 'like', '%' . $row[14] . '%')->first();
    //       $company->industry =  $ind->id;
    //       $company->save();
    //     }

    //   }

    //   if($row[15]){
    //     $co_chk = Country::Where('country_name', 'like', '%' .$row[15] . '%')->first();
    //     if(isset($co_chk)) {
    //       $country_data = $co_chk->country_id;
    //     }else {

    //       $co_chk = new  Country();
    //       $co_chk->country_name =$row[15];
    //       $co_chk->save();
    //       $country_data =  $co_chk->country_id;
    //     }

    //     $co_comp = Company::Where('comp_name', 'like', '%' . $row[14] . '%')->first();
    //     $co_comp->country =  $country_data;
    //     $co_comp->update();
    //   }
    //   if($row[16]){
    //     $company = Company::Where('comp_name', 'like', '%' . $row[14] . '%')->first();
    //     $company->tele_phone =$row[16].','.$row[17].','.$row[18].','.$row[19];
    //     $company->update();

    //   }
    //   if($row[20]){
    //     $contact->lifecy_stage = $row[20];

    //   }
    //   if($row[21]){
    //     $contact->lead_stage = $row[21];

    //   }

    //   if($row[22]){
    //     $contact->designation = $row[22];

    //   }
    //   if($row[23]){
    //     $contact->fb_link2 = $row[23];

    //   }
    //   if($row[24]){
    //     $contact->tw_link2 = $row[24];

    //   }
    //   if($row[25]){
    //     $contact->lnk_link2 = $row[25];

    //   }
    //   if($row[26]){
    //     $contact->other_link = $row[26];

    //   }

    //   if($row[27]){
    //     $company = Company::Where('comp_name', 'like', '%' . $row[14] . '%')->first();
    //     $company->comp_name_ar =$row[27];
    //     $company->update();
    //   }

    //   if($row[28]){
    //     $city_data = City::Where('city_name', 'like', '%' . $row[28] . '%')->first();
    //     if(isset($city_data)){
    //       $company = Company::Where('comp_name', 'like', '%' . $row[14] . '%')->first();
    //       $company->city =$city_data->city_id;
    //       $company->update();
    //     }else{
    //       $city = new City();
    //       $city->city_name =  $row[28];
    //       $city->save();
    //       $company = Company::Where('comp_name', 'like', '%' . $row[14] . '%')->first();
    //       $company->city =$city->city_id;
    //       $company->update();
    //     }
    //   }

    //   if($row[29]){
    //     $company = Company::Where('comp_name', 'like', '%' . $row[14] . '%')->first();
    //     $company->address =$row[29];
    //     $company->update();
    //   }
    //   if($row[30]){
    //     $company = Company::Where('comp_name', 'like', '%' . $row[14] . '%')->first();
    //     $company->website =$row[30];
    //     $company->update();
    //   }
    //   if($row[31]){
    //     $company = Company::Where('comp_name', 'like', '%' . $row[14] . '%')->first();
    //     $company->fb_link =$row[31];
    //     $company->update();
    //   }
    //   if($row[32]){
    //     $company = Company::Where('comp_name', 'like', '%' . $row[14] . '%')->first();
    //     $company->tw_link =$row[32];
    //     $company->update();
    //   }
    //   if($row[33]){
    //     $company = Company::Where('comp_name', 'like', '%' . $row[14] . '%')->first();
    //     $company->lnk_link =$row[33];
    //     $company->update();
    //   }

    //   if($row[34]){
    //     $company = Company::Where('comp_name', 'like', '%' . $row[14] . '%')->first();
    //     $company->other_link =$row[34];
    //     $company->update();
    //   }
    //   $contact->save();
    // }

    Session::flash('success',count($csv_data).' data import successfully!');
    return redirect('master/contacts/list');
  }

  public function download_csv()
  {
    $url = base_path().'/public/test3.csv';  
    $file_name = basename($url); 
    $info = pathinfo($file_name);
  
    if ($info["extension"] == "csv") {
      if(file_put_contents( $file_name,file_get_contents($url))){ }
    }
  }

  public function mofa_price_list()
  {
  
    $pageConfigs = ['pageHeader' => false];
    return view('/content/service-master/mofa-price-list', ['pageConfigs' => $pageConfigs]);    
  }

  public function mofa_price_list_json(Request $request)
  {
    $ser = Service::where('type','=','visa')->get();
    $data['data']  = $ser;
    return response()->json($data);
  }

  public function mofa_price_view_json(Request $request)
  {
    $ser_id = $request->input('ser_id') ? $request->input('ser_id') : '';
  

    $res = DB::table('qr_mofa_price as mp')
      ->leftjoin('qr_mofa_payment_category as mpc', 'mpc.id', '=', 'mp.pay_cat')
      ->select('mp.*','mpc.*','mpc.id as mpc_id','mp.id as mofa_id') 
      ->orderBy('mp.id','DESC')
      ->where('mp.ser_id','=',$ser_id)
      ->get();
      $data['data'] = $res;

  return response()->json($data);

  }

  public function mofa_price_store(Request $request)
  {

    $status = MofaPrice::where('ser_id','=',$request->input('ser_id'))->where('pay_cat','=',$request->input('pay_cat'))->update(array('status' => 0));
    $mofa = new MofaPrice();
    $mofa->ser_id = $request->input('ser_id') ? $request->input('ser_id') : '';
    $mofa->pay_cat = $request->input('pay_cat') ? $request->input('pay_cat') : '';
    $mofa->price = $request->input('price') ? $request->input('price') : '';
    $mofa->status=1;
    $mofa->save();


 

    Session::flash('success','Price added successfully');
    return redirect()->back();


  }

  public function mofa_price_edit(Request $request)
  {
    $ed_id = $request->input('ed_id') ? $request->input('ed_id') : '';
    $mofa_edit = MofaPrice::find($ed_id);
    return  response()->json($mofa_edit);
  }

  public function mofa_price_update(Request $request)
  {
    $mofa_id = $request->input('mofa_id') ? $request->input('mofa_id') : '';
    $mofa = MofaPrice::find($mofa_id);
    $mofa->pay_cat = $request->input('pay_cat') ? $request->input('pay_cat') : '';
    $mofa->price = $request->input('price') ? $request->input('price') : '';
    $mofa->save();
    Session::flash('success','Price updated successfully');
    return redirect()->back();
  }

  public function mofa_price_view($id)
  {
    $ser = Service::find($id);

    $pageConfigs = ['pageHeader' => false];
    return view('/content/service-master/mofa-price-view', ['pageConfigs' => $pageConfigs,'ser' => $ser]);   
  }

  public function mofa_pay_cat_store(Request $request)
  {
    $name = $request->input('name') ? $request->input('name') : '';
    $chk = MofaPaymentCategory::where('name','like', '%' . $name . '%')->first();
    if(!isset($chk)){
      $mo = new MofaPaymentCategory();
      $mo->name = $name;
      $mo->save();
      $id = $mo->id;
      $data = array(
        'id' => $id,
        'name' => $name 
      );
      return response()->json($data);
    }else{
      return response()->json('0');
    }
  }

  public function list_product()
  {
    $res =  Product::all();

    if(count($res) > 0){
      return response()->json($res);  
    }else{
      return response()->json("0");
    }
    

  }

  public function viewProduct(Request $request)
  {
    $id = $request->input('id') ? $request->input('id') : '';
    $data = Product::find($id);

    return response()->json($data);
  }

  public function store_product(Request $request)
  {
                                                        
    $title = $request->input('title') ? $request->input('title') : '';
    $price = $request->input('price') ? $request->input('price') : '';
    $cat_id = $request->input('cat_id') ? $request->input('cat_id') : '';
    $img_url = $request->input('img_url') ? $request->input('img_url') : '';
    if($title && $price && $cat_id && $img_url){
      $prod = new Product();
      $prod->title = $title;
      $prod->price = $price;
      $prod->cat_id = $cat_id;
      $prod->img_url = $img_url;
      $prod->save();
      return response()->json('1');  
    }else{
      return response()->json('0');
    }
  
  }

  public function edit_product(Request $request)
  { 

    $id = $request->input('id') ? $request->input('id') : '';
    $data = Product::find($id);

    return response()->json($data);

  }
  
  public function update_product(Request $request)
  {
    
    $name = $request->input('name') ? $request->input('name') : '';
    $price = $request->input('price') ? $request->input('price') : '';
    $cat_id = $request->input('cat_id') ? $request->input('cat_id') : '';
    $img_url = $request->input('img_url') ? $request->input('img_url') : '';
    $id = $request->input('id') ? $request->input('id') : '';

    $prod = Product::find($id);
    $prod->name = $name;
    $prod->price = $price;
    $prod->cat_id = $cat_id;
    $prod->img_url = $img_url;
    $prod->update();
    return response()->json('1');

  }


  public function delete_product(Request $request)
  {

    $id = $request->input('id') ? $request->input('id') : '';
    $prod = Product::find($id);
    $prod->delete();
    return response()->json('1');

  }

  public function store_category(Request $request)
  {

    $cat = new ProductCategory();
    $cat->name = $request->input('name') ? $request->input('name') : '';
    $cat->save();
    return response()->json('1');

  }

  public function list_category()
  {
  
    $res = ProductCategory::all();  
    $data['data'] = $res;
    if(count($res) > 0) {
      return response()->json($res);    
    } else {
  
      return response()->json('0');
  
    }
  
  }

  public function crmcandstore(Request $request)
  {
    $post = new CrmCandidate();

    if($request->cont_id != 'qin'){
      $post->cont_id = $request->cont_id;
    }else{
      $post->in_house = $request->cont_id;
    }
    $post->careoff_id = $request->care_of_id;
    $post->followup_id = $request->f_stage;
    $post->workstatus_id = $request->jb_status;
    $post->ser_id = $request->ser_id;
    $post->scode = $request->scode;
    if($request->position != ''){
      $post->prof_id = $request->position;
    }else{
      $post->prof_id = null;
    }

    $post->fname = $request->fname;
    $post->lname = $request->lname;
    $post->whatsapp_no = $request->whats_mob_no;
    $post->email = $request->email;
    $post->mobile_no = $request->mob_no;
    $post->expc_id = $request->country;
    $post->alt_no_mob = $request->alt_mob_no;
    $post->sources = $request->source;
    $post->user_id = Auth::user()->user_id;
    $post->save();
    Session::flash('success','Candidate created successfully!');
    return redirect()->back();
  }

  public function scodepays(Request $request){
    $post = new Crmcpay();
    $post->crmc_id = $request->crmc_id;
    $post->amt = $request->amt;
    $post->user_id = Auth::user()->user_id;
    $post->save();
    Session::flash('success','Service code created!');
    return redirect()->back();
  }

  public function sector_list($value='')
  {
    $pageConfigs = ['pageHeader' => false];
    return view('/content/service-master/sector-list', ['pageConfigs' => $pageConfigs]);  
  }

  public function sector_list_json(Request $request)
  {
    $sc = Sector::all();
    $data['data'] = $sc;
    return response()->json($data);
  }

  public function sector_store(Request $request)
  {
    $sc_name =  $request->input('sector_name');
    $sc = new Sector();
    $sc->sector_name = $sc_name;
    $sc->user_id = Auth::user()->user_id;
    $sc->save();
    
    Session::flash('success','Sector store successfully');
    return redirect()->back();
  }

  public function sector_delete(Request $request)
  {
            $id =  $request->input('id');
    $sc= Sector::find($id);
    $sc->delete();
      Session::flash('success','Sector deleted successfully');
    return redirect()->back();
  }

  public function sector_edit(Request $request)
  {
    $id =  $request->input('id');
    $sc =Sector::find($id);
    return response()->json($sc);
  }

  public function sector_update(Request $request)
  {
    $id =  $request->input('id');
    $sector_name =  $request->input('sector_name');
    $sc =Sector::find($id);
    $sc->sector_name = $sector_name;
    $sc->update();

    Session::flash('success','Sector updated successfully');
    return redirect()->back();
  }

    // Employement Service Type
    
  public function service_type_lis(Request $request){
    $pageConfigs = ['pageHeader' => false];
    return view('/content/service-master/service-type', ['pageConfigs' => $pageConfigs]);         
  }

  public function sertypexist(Request $request){
    $ser_id = $request->input('id');
    // check in Employer Table
    $data1 = DB::table('qr_employee_tbl')->where('ser_id','=',$ser_id)->first();
    // Check in Candidate Table
    $data2 = DB::table('qr_candidate_tbl')->where('ser_id','=',$ser_id)->first();

    if(isset($data1) || isset($data2)){
      return response()->json('1');
    }else{
      
    }
  }

  public function service_type_lis_json(Request $request)
  {
    $rc =  EmployeeServiceType::all();
    // $rc = DB::table('qr_emp_service_tbl as emp_ser')
    //         ->leftjoin('qr_employee_tbl as emp','emp.ser_id','=','emp_ser.ser_id')
    //         ->select('emp_ser.*','emp.emp_id')
    //         ->get();
    $data['data'] = $rc;
    return response()->json($data);
  
  }

  public function service_type_edit(Request $request){
    $sr = EmployeeServiceType::find($request->input('ser_id'));
    // $sr = EmployeeServiceType::where('ser_id','=',$request->input('ser_id'))->first();
    return response()->json($sr);
  }

  public function service_type_update(Request $request){

    // dd($request);
    $ser_id = $request->input('ser_id');
    $ser_name = $request->input('ser_name');
    $sr = EmployeeServiceType::find($ser_id);
    $sr->ser_name = $ser_name;
    $sr->save();
    Session::flash('success','Employment Service Type Store Successfully!');
    return redirect()->back();

  }

  public function service_type_delete(Request $request){
    $sr = EmployeeServiceType::find($request->input('ser_id'));
    $sr->delete();
    Session::flash('success','Service Type delete successfully');
    return redirect()->back();
  }

  public function service_type_store(Request $request){
    $sr = new EmployeeServiceType();
    $sr->ser_name = $request->input('ser_name') ? $request->input('ser_name') : '';
    $sr->user_id = Auth::user()->user_id;
    $sr->save();
    Session::flash('success','Employment Service Type Store Successfully!');
    return redirect()->back();
  }

  public function recruit_office_list(Request $request)
  {
  
    $pageConfigs = ['pageHeader' => false];
    return view('/content/service-master/recruitment-office', ['pageConfigs' => $pageConfigs]);    

  }

  public function recruit_office_list_json(Request $request)
  {
    $rc =  Recruitment::all();
    $data['data'] = $rc;
    return response()->json($data);
  
  }

  public function recruit_office_store(Request $request)
  {
    $rc = new Recruitment();
    $rc->name =  $request->input('name') ?  $request->input('name') :'';
    $rc->ar_name =  $request->input('ar_name') ?  $request->input('ar_name') :'';
    $rc->eng_name =  $request->input('eng_name') ?  $request->input('eng_name') :'';
    $rc->email =  $request->input('email') ?  $request->input('email') :'';
    $rc->phone =  $request->input('phone') ?  $request->input('phone') :'';
    $rc->country_id =  $request->input('country_id') ?  $request->input('country_id') :'';
    $rc->sc_id =  $request->input('sc_id') ?  $request->input('sc_id') :'';
    $rc->user_id =  Auth::user()->user_id;
    $rc->concern_per_ar =  $request->input('name') ?  $request->input('name') :'';
    $rc->concern_per_eng =  $request->input('concern_per_eng') ?  $request->input('concern_per_eng') :'';
    $rc->sec_phone =  $request->input('sec_phone') ?  $request->input('sec_phone') :'';
    $rc->care_off_id =  $request->input('care_off_id') ?  $request->input('care_off_id') :'';
    $rc->save();

    Session::flash('success','Recruitment office store successfully');
    return redirect()->back();
  }

  public function recruit_office_edit(Request $request)
  {
      $rc =  Recruitment::find( $request->input('id'));
      return response()->json($rc);
    
  }

  public function recruit_office_update(Request $request)
  {
    $rc =  Recruitment::find( $request->input('id'));

    $rc->name =  $request->input('name') ?  $request->input('name') :'';
    $rc->ar_name =  $request->input('ar_name') ?  $request->input('ar_name') :'';
    $rc->eng_name =  $request->input('eng_name') ?  $request->input('eng_name') :'';
    $rc->email =  $request->input('email') ?  $request->input('email') :'';
    $rc->phone =  $request->input('phone') ?  $request->input('phone') :'';
    $rc->country_id =  $request->input('country_id') ?  $request->input('country_id') :'';
    $rc->sc_id =  $request->input('sc_id') ?  $request->input('sc_id') :'';
    $rc->user_id =  Auth::user()->user_id;
    $rc->concern_per_ar =  $request->input('name') ?  $request->input('name') :'';
    $rc->concern_per_eng =  $request->input('concern_per_eng') ?  $request->input('concern_per_eng') :'';
    $rc->sec_phone =  $request->input('sec_phone') ?  $request->input('sec_phone') :'';
    $rc->sec_email =  $request->input('sec_email') ?  $request->input('sec_email') :'';
    $rc->third_phone =  $request->input('third_phone') ?  $request->input('third_phone') :'';
    $rc->care_off_id =  $request->input('care_off_id') ?  $request->input('care_off_id') :'';

    $rc->update();

    Session::flash('success','Recruitment office update successfully');
    return redirect()->back();
  }

  public function recruit_office_delete(Request $request)
  {
    $rc =  Recruitment::find( $request->input('id'));
    $rc->delete();
    Session::flash('success','Recruitment office delete successfully');
    return redirect()->back();
  }

  public function allcontplist(Request $request){
    $perms = AccessPermissionModule2::where('user_id','=',Auth::user()->user_id)->first();

      

      if (Auth::user()->user_type == 1 || (isset($perms) && $perms->full_access == 1)) {

        $data_con = DB::table('qr_all_contacts_tbl as cont')
          ->leftjoin('users as user','cont.assign_id','=','user.user_id')
          ->leftjoin('qr_lifecycle_stage as lcs','cont.lcs_id','=','lcs.id')
          ->leftjoin('lstages as ls','cont.ls_id','=','ls.id')
          ->leftjoin('qr_country as country','cont.country_id','=','country.country_id')
          ->leftjoin('sources as source','cont.source_id','=','source.id')
          ->select('cont.*','user.name as username','lcs.name as lcsname','ls.name as lsname','country.country_name','source.name as sourcename')
          ->orderBy('cont.id','DESC')
          // ->get(); 
          ->paginate(10);

      }elseif (Auth::user()->user_type == 2 || (isset($perms) && $perms->full_access == 0)) {
        if (isset($perms)) {
          
          if ($perms->r_crm_ac == 1) {
    
            $data_con = DB::table('qr_all_contacts_tbl as cont')
            -> leftjoin('users as user','cont.assign_id','=','user.user_id')
            ->leftjoin('qr_lifecycle_stage as lcs','cont.lcs_id','=','lcs.id')
            ->leftjoin('lstages as ls','cont.ls_id','=','ls.id')
            ->leftjoin('qr_country as country','cont.country_id','=','country.country_id')
            ->leftjoin('sources as source','cont.source_id','=','source.id')
            ->select('cont.*','user.name as username','lcs.name as lcsname','ls.name as lsname','country.country_name','source.name as sourcename')
            ->where('cont.contact_dl_status','=','0')
            ->orWhere('cont.assign_id','=',Auth::user()->user_id)
            ->orderBy('cont.id','DESC')
            // ->get();
            ->paginate(10);
          }else{
    
            $data_con = DB::table('qr_all_contacts_tbl as cont')
            -> leftjoin('users as user','cont.assign_id','=','user.user_id')
            ->leftjoin('qr_lifecycle_stage as lcs','cont.lcs_id','=','lcs.id')
            ->leftjoin('lstages as ls','cont.ls_id','=','ls.id')
            ->leftjoin('qr_country as country','cont.country_id','=','country.country_id')
            ->leftjoin('sources as source','cont.source_id','=','source.id')
            ->select('cont.*','user.name as username','lcs.name as lcsname','ls.name as lsname','country.country_name','source.name as sourcename')
            ->where('cont.user_id','=',Auth::user()->user_id)
            ->orWhere('cont.public_st','=','1')
            ->orWhere('cont.assign_id','=',Auth::user()->user_id)
            ->where('cont.contact_dl_status','=','0')
            ->orderBy('cont.id','DESC')
            // ->get();
            ->paginate(10);
          }
    
        }else{
    
          $data_con = DB::table('qr_all_contacts_tbl as cont')
          -> leftjoin('users as user','cont.assign_id','=','user.user_id')
          ->leftjoin('qr_lifecycle_stage as lcs','cont.lcs_id','=','lcs.id')
          ->leftjoin('lstages as ls','cont.ls_id','=','ls.id')
          ->leftjoin('qr_country as country','cont.country_id','=','country.country_id')
          ->leftjoin('sources as source','cont.source_id','=','source.id')
          ->select('cont.*','user.name as username','lcs.name as lcsname','ls.name as lsname','country.country_name','source.name as sourcename')
          ->where('cont.user_id','=',Auth::user()->user_id)
          ->orWhere('cont.public_st','=','1')
          ->orWhere('cont.assign_id','=',Auth::user()->user_id)
          ->where('cont.contact_dl_status','=','0')
          ->orderBy('cont.id','DESC')
          // ->get();
          ->paginate(10);
        }
      }

    



    

    

    if($request->ajax()){
      return view('content.crm.include.load',['data_cons' => $data_con])->render();
    }
    

    $pageConfigs = ['pageHeader' => false];
    return view('content.crm.contact-lists-paginate',['pageConfigs' => $pageConfigs,'data_cons' => $data_con]);
  }

  public function allcontpsearch(Request $request){
    $business_type = $request->business_type;
    $lead_status = $request->lead_status;
    $lead_stage = $request->lead_stage;
    $lead_priority = $request->lead_priority;
    $country = $request->country;
    $source = $request->source;
    $careoff = $request->careoff;
    $createby = $request->createby;
    $createdon = $request->createdon;
    $updatedon = $request->updatedon;
    $nxtfollowup = $request->nxtfollowup;


    $perms = AccessPermissionModule2::where('user_id','=',Auth::user()->user_id)->first();

    if (Auth::user()->user_type == 1 || (isset($perms) && $perms->full_access == 1)){
      $data_con = DB::table('qr_all_contacts_tbl as cont')
        ->leftjoin('users as user','cont.assign_id','=','user.user_id')
        ->leftjoin('qr_lifecycle_stage as lcs','cont.lcs_id','=','lcs.id')
        ->leftjoin('lstages as ls','cont.ls_id','=','ls.id')
        ->leftjoin('qr_country as country','cont.country_id','=','country.country_id')
        ->leftjoin('sources as source','cont.source_id','=','source.id')
        ->select('cont.*','user.name as username','lcs.name as lcsname','ls.name as lsname','country.country_name','source.name as sourcename');
        // ->orderBy('cont.id','DESC') 
        // ->paginate(10);

        if($business_type != ''){
          $data_con->where('cont.lead_type','=',$business_type);
        }

        if($lead_status != ''){
          $data_con->where('cont.lcs_id','=',$lead_status);
        }

        if($lead_stage != ''){
          $data_con->where('cont.ls_id','=',$lead_stage);
        }

        if($lead_priority != ''){
          $data_con->where('cont.lead_prority','=',$lead_priority);
        }

        if($country != ''){
          $data_con->where('cont.country_id','=',$country);
        }

        if($source != ''){
          $data_con->where('cont.source_id','=',$source);
        }

        if($careoff != ''){
          $data_con->where('cont.assign_id','=',$careoff);
        }

        if($createby != ''){
          $data_con->where('cont.user_id','=',$createby);
        }

        if($createdon != ''){
          $data_con->whereDate('cont.created_at','=',$createdon);
        }
        if($updatedon != ''){
          $data_con->whereDate('cont.updated_at','=',$updatedon);
        }

        if($nxtfollowup != ''){
          $data_con->whereDate('cont.followup_date','=',$nxtfollowup);
        }

        $data_con_fl = $data_con->orderBy('cont.id','DESC')->paginate(10);

    }elseif(Auth::user()->user_type == 2 || (isset($perms) && $perms->full_access == 0)){

    }

    // $data = [
    //   'first' => $data_con_fl->firstItem(),
    //   'last' => $data_con_fl->lastItem(),
    //   'total' => $data_con_fl->total()
    // ];

    // return response($data);
    

    return view('content.crm.include.load',['data_cons' => $data_con_fl])->render();

    $pageConfigs = ['pageHeader' => false];
    $data_req = [
      'business_type' => $business_type,
      'lead_status' => $lead_status,
      'lead_stage' => $lead_stage,
      'lead_priority' => $lead_priority,
      'country' => $country,
      'source' => $source,
      'careoff' => $careoff,
      'createby' => $createby,
      'createdon' => $createdon,
      'updatedon' => $updatedon,
      'nxtfollowup' => $nxtfollowup,
    ]; 

    // if($request->ajax()){
    //   return view('content.crm.include.load_s',['data_cons' => $data_con_fl])->render();
    // }

    // return view('content.crm.contact-lists-paginates_s',['pageConfigs' => $pageConfigs,'data_cons' => $data_con_fl,'data_req' => $data_req]);
    // return view('content.crm.contact-lists-paginates_s',['pageConfigs' => $pageConfigs,'data_cons' => $data_con_fl]);


  }

  public function allcontlistref(Request $request){

    // dd($request->business_type);

    $perms = AccessPermissionModule2::where('user_id','=',Auth::user()->user_id)->first();
  
    if (Auth::user()->user_type == 1 || (isset($perms) && $perms->full_access == 1)) {
        $data_con = DB::table('qr_all_contacts_tbl as cont')
        ->leftjoin('users as user','cont.assign_id','=','user.user_id')
        ->leftjoin('qr_lifecycle_stage as lcs','cont.lcs_id','=','lcs.id')
        ->leftjoin('lstages as ls','cont.ls_id','=','ls.id')
        ->leftjoin('qr_country as country','cont.country_id','=','country.country_id')
        ->leftjoin('sources as source','cont.source_id','=','source.id')
        ->select('cont.*','user.name as username','lcs.name as lcsname','ls.name as lsname','country.country_name','source.name as sourcename');

        if(isset($request->business_type) && $request->business_type != ''){
          $data_con->where('cont.lead_type','=',$request->business_type);
        }

        if(isset($request->lead_status) && $request->lead_status != ''){
          $data_con->where('cont.lcs_id','=',$request->lead_status);
        }

        if(isset($request->lead_stage) && $request->lead_stage != ''){
          $data_con->where('cont.ls_id','=',$request->lead_stage);
        }

        if(isset($request->lead_priority) && $request->lead_priority != ''){
          $data_con->where('cont.lead_prority','=',$request->lead_priority);
        }

        if(isset($request->country) && $request->country != ''){
          $data_con->where('cont.country_id','=',$request->country);
        }

        if(isset($request->source) && $request->source != ''){
          $data_con->where('cont.source_id','=',$request->source);
        }

        if(isset($request->careoff) && $request->careoff != ''){
          $data_con->where('cont.assign_id','=',$request->careoff);
        }

        if(isset($request->createby) && $request->createby != ''){
          $data_con->where('cont.user_id','=',$request->createby);
        }

        if(isset($request->optinoutD) && $request->optinoutD != ''){
          if($request->optinoutD == 'all'){
            $data_con->where('cont.optinout','=',null);
          }else{
            $data_con->where('cont.optinout','=',$request->optinoutD);            
          }
        }

        if(isset($request->createdon) && $request->createdon != ''){
          $data_con->whereDate('cont.created_at','=',$request->createdon);
        }
        if(isset($request->updatedon) && $request->updatedon != ''){
          $data_con->whereDate('cont.updated_at','=',$request->updatedon);
        }

        if(isset($request->nxtfollowup) && $request->nxtfollowup != ''){
          $data_con->whereDate('cont.followup_date','=',$request->nxtfollowup);
        }

        if(isset($request->group_id) && $request->group_id != ''){


          if($request->group_id == 'all'){
            $data_con->where('cont.group_id','=',null);
          }else{
            $data_con->where('cont.group_id','=',$request->group_id);
          }

        }

        if(isset($request->date_range) && $request->date_range != ''){
          $datenew = explode(" to ",$request->date_range);
          $startdate = $datenew[0];
          $enddate = $datenew[1];
          $data_con->whereBetween('cont.created_at',[$startdate.' 00:00:00',$enddate.' 23:59:59']);
          
        }

        if(isset($request->search_text) && $request->search_text != ''){
          $data_con->where(function($query) use($request){
            $query->orwhere('cont.full_name','like',"%".$request->search_text."%");
            $query->orwhere('cont.company_name','like',"%".$request->search_text."%");
            $query->orwhere('cont.lead_prority','like',"%".$request->search_text."%");
            $query->orwhere('cont.lead_type','like',"%".$request->search_text."%");
            $query->orwhere('cont.city','like',"%".$request->search_text."%");
            $query->orwhere('cont.followup_date','like',"%".$request->search_text."%");
            $query->orwhere('cont.created_at','like',"%".$request->search_text."%");
            $query->orwhere('cont.updated_at','like',"%".$request->search_text."%");
            $query->orwhere('cont.mobile_no','like',"%".$request->search_text."%");
            $query->orwhere('cont.phone0','like',"%".$request->search_text."%");
            $query->orwhere('cont.phone1','like',"%".$request->search_text."%");
            $query->orwhere('cont.phone2','like',"%".$request->search_text."%");
            $query->orwhere('cont.primary_contact_no','like',"%".$request->search_text."%");
            $query->orwhere('cont.email','like',"%".$request->search_text."%");
            $query->orwhere('cont.email0','like',"%".$request->search_text."%");
            $query->orwhere('cont.email1','like',"%".$request->search_text."%");
            $query->orwhere('cont.email2','like',"%".$request->search_text."%");
            $query->orwhere('cont.regarding','like',"%".$request->search_text."%");
            $query->orwhere('lcs.name','like',"%".$request->search_text."%");
            $query->orwhere('ls.name','like',"%".$request->search_text."%");
            $query->orwhere('country.country_name','like',"%".$request->search_text."%");
            $query->orwhere('source.name','like',"%".$request->search_text."%");
            $query->orwhere('user.name','like',"%".$request->search_text."%");
          });
        }

        if(isset($request->page_list) && $request->page_list != ''){
          $data_con_fl = $data_con->orderBy('cont.id','DESC')->paginate($request->page_list)->withQueryString();
        }else{
          $data_con_fl = $data_con->orderBy('cont.id','DESC')->paginate(10)->withQueryString();
        }


    }elseif(Auth::user()->user_type == 2 || (isset($perms) && $perms->full_access == 0)){
      if (isset($perms)) {
        if ($perms->r_crm_ac == 1) {
          $data_con = DB::table('qr_all_contacts_tbl as cont')
          ->leftjoin('users as user','cont.assign_id','=','user.user_id')
          ->leftjoin('qr_lifecycle_stage as lcs','cont.lcs_id','=','lcs.id')
          ->leftjoin('lstages as ls','cont.ls_id','=','ls.id')
          ->leftjoin('qr_country as country','cont.country_id','=','country.country_id')
          ->leftjoin('sources as source','cont.source_id','=','source.id')
          ->select('cont.*','user.name as username','lcs.name as lcsname','ls.name as lsname','country.country_name','source.name as sourcename')
          // ->orWhere('cont.assign_id','=',Auth::user()->user_id)
          ->where('cont.contact_dl_status','=','0');
  
          if(isset($request->business_type) && $request->business_type != ''){
            $data_con->where('cont.lead_type','=',$request->business_type);
          }
  
          if(isset($request->lead_status) && $request->lead_status != ''){
            $data_con->where('cont.lcs_id','=',$request->lead_status);
          }
  
          if(isset($request->lead_stage) && $request->lead_stage != ''){
            $data_con->where('cont.ls_id','=',$request->lead_stage);
          }
  
          if(isset($request->lead_priority) && $request->lead_priority != ''){
            $data_con->where('cont.lead_prority','=',$request->lead_priority);
          }
  
          if(isset($request->country) && $request->country != ''){
            $data_con->where('cont.country_id','=',$request->country);
          }
  
          if(isset($request->source) && $request->source != ''){
            $data_con->where('cont.source_id','=',$request->source);
          }
  
          if(isset($request->careoff) && $request->careoff != ''){
            $data_con->where('cont.assign_id','=',$request->careoff);
          }
  
          if(isset($request->createby) && $request->createby != ''){
            $data_con->where('cont.user_id','=',$request->createby);
          }

          if(isset($request->optinoutD) && $request->optinoutD != ''){
            if($request->optinoutD == 'all'){
              $data_con->where('cont.optinout','=',null);
            }else{
              $data_con->where('cont.optinout','=',$request->optinoutD);            
            }
          }
  
          if(isset($request->createdon) && $request->createdon != ''){
            $data_con->whereDate('cont.created_at','=',$request->createdon);
          }
          if(isset($request->updatedon) && $request->updatedon != ''){
            $data_con->whereDate('cont.updated_at','=',$request->updatedon);
          }
  
          if(isset($request->nxtfollowup) && $request->nxtfollowup != ''){
            $data_con->whereDate('cont.followup_date','=',$request->nxtfollowup);
          }
          
          if(isset($request->group_id) && $request->group_id != ''){
            if($request->group_id == 'all'){
              $data_con->where('cont.group_id','=',null);
            }else{
              $data_con->where('cont.group_id','=',$request->group_id);
            }
          }

          if(isset($request->date_range) && $request->date_range != ''){
            $datenew = explode(" to ",$request->date_range);
            $startdate = $datenew[0];
            $enddate = $datenew[1];
            $data_con->whereBetween('cont.created_at',[$startdate.' 00:00:00',$enddate.' 23:59:59']);
            
          }
  
          if(isset($request->search_text) && $request->search_text != ''){
            $data_con->where(function($query) use($request){
              $query->orwhere('cont.full_name','like',"%".$request->search_text."%");
              $query->orwhere('cont.company_name','like',"%".$request->search_text."%");
              $query->orwhere('cont.lead_prority','like',"%".$request->search_text."%");
              $query->orwhere('cont.lead_type','like',"%".$request->search_text."%");
              $query->orwhere('cont.city','like',"%".$request->search_text."%");
              $query->orwhere('cont.followup_date','like',"%".$request->search_text."%");
              $query->orwhere('cont.created_at','like',"%".$request->search_text."%");
              $query->orwhere('cont.updated_at','like',"%".$request->search_text."%");
              $query->orwhere('cont.mobile_no','like',"%".$request->search_text."%");
              $query->orwhere('cont.phone0','like',"%".$request->search_text."%");
              $query->orwhere('cont.phone1','like',"%".$request->search_text."%");
              $query->orwhere('cont.phone2','like',"%".$request->search_text."%");
              $query->orwhere('cont.primary_contact_no','like',"%".$request->search_text."%");
              $query->orwhere('cont.email','like',"%".$request->search_text."%");
              $query->orwhere('cont.email0','like',"%".$request->search_text."%");
              $query->orwhere('cont.email1','like',"%".$request->search_text."%");
              $query->orwhere('cont.email2','like',"%".$request->search_text."%");
              $query->orwhere('cont.regarding','like',"%".$request->search_text."%");
              $query->orwhere('lcs.name','like',"%".$request->search_text."%");
              $query->orwhere('ls.name','like',"%".$request->search_text."%");
              $query->orwhere('country.country_name','like',"%".$request->search_text."%");
              $query->orwhere('source.name','like',"%".$request->search_text."%");
              $query->orwhere('user.name','like',"%".$request->search_text."%");
            });
          }
  
          if(isset($request->page_list) && $request->page_list != ''){
            $data_con_fl = $data_con->orderBy('cont.id','DESC')->paginate($request->page_list)->withQueryString();
          }else{
            $data_con_fl = $data_con->orderBy('cont.id','DESC')->paginate(10)->withQueryString();
          }
        }else{

          $data_con = DB::table('qr_all_contacts_tbl as cont')
          ->leftjoin('users as user','cont.assign_id','=','user.user_id')
          ->leftjoin('qr_lifecycle_stage as lcs','cont.lcs_id','=','lcs.id')
          ->leftjoin('lstages as ls','cont.ls_id','=','ls.id')
          ->leftjoin('qr_country as country','cont.country_id','=','country.country_id')
          ->leftjoin('sources as source','cont.source_id','=','source.id')
          ->select('cont.*','user.name as username','lcs.name as lcsname','ls.name as lsname','country.country_name','source.name as sourcename')
          ->where('cont.user_id','=',Auth::user()->user_id)
          // ->orWhere('cont.public_st','=','1')
          // ->orWhere('cont.assign_id','=',Auth::user()->user_id)
          ->where('cont.contact_dl_status','=','0');

          if(isset($request->business_type) && $request->business_type != ''){
            $data_con->where('cont.lead_type','=',$request->business_type);   
          }
  
          if(isset($request->lead_status) && $request->lead_status != ''){
            $data_con->where('cont.lcs_id','=',$request->lead_status);
          }
          
          if(isset($request->lead_stage) && $request->lead_stage != ''){
            $data_con->where('cont.ls_id','=',$request->lead_stage);
          }
  
          if(isset($request->lead_priority) && $request->lead_priority != ''){
            $data_con->where('cont.lead_prority','=',$request->lead_priority);
          }
  
          if(isset($request->country) && $request->country != ''){
            $data_con->where('cont.country_id','=',$request->country);
          }
  
          if(isset($request->source) && $request->source != ''){
            $data_con->where('cont.source_id','=',$request->source);
          }
  
          if(isset($request->careoff) && $request->careoff != ''){
            $data_con->where('cont.assign_id','=',$request->careoff);
          }
  
          if(isset($request->createby) && $request->createby != ''){
            $data_con->where('cont.user_id','=',$request->createby);
          }

          if(isset($request->optinoutD) && $request->optinoutD != ''){
            if($request->optinoutD == 'all'){
              $data_con->where('cont.optinout','=',null);
            }else{
              $data_con->where('cont.optinout','=',$request->optinoutD);            
            }
          }

          if(isset($request->group_id) && $request->group_id != ''){
            if($request->group_id == 'all'){
              $data_con->where('cont.group_id','=',null);
            }else{
              $data_con->where('cont.group_id','=',$request->group_id);
            }
          }
  
          if(isset($request->createdon) && $request->createdon != ''){
            $data_con->whereDate('cont.created_at','=',$request->createdon);
          }

          if(isset($request->updatedon) && $request->updatedon != ''){
            $data_con->whereDate('cont.updated_at','=',$request->updatedon);
          }
  
          if(isset($request->nxtfollowup) && $request->nxtfollowup != ''){
            $data_con->whereDate('cont.followup_date','=',$request->nxtfollowup);
          }
  
          if(isset($request->date_range) && $request->date_range != ''){
            $datenew = explode(" to ",$request->date_range);
            $startdate = $datenew[0];
            $enddate = $datenew[1];
            $data_con->whereBetween('cont.created_at',[$startdate.' 00:00:00',$enddate.' 23:59:59']);
          }
  
          if(isset($request->search_text) && $request->search_text != ''){
            $data_con->where(function($query) use($request){
              $query->orwhere('cont.full_name','like',"%".$request->search_text."%");
              $query->orwhere('cont.company_name','like',"%".$request->search_text."%");
              $query->orwhere('cont.lead_prority','like',"%".$request->search_text."%");
              $query->orwhere('cont.lead_type','like',"%".$request->search_text."%");
              $query->orwhere('cont.city','like',"%".$request->search_text."%");
              $query->orwhere('cont.followup_date','like',"%".$request->search_text."%");
              $query->orwhere('cont.created_at','like',"%".$request->search_text."%");
              $query->orwhere('cont.updated_at','like',"%".$request->search_text."%");
              $query->orwhere('cont.mobile_no','like',"%".$request->search_text."%");
              $query->orwhere('cont.phone0','like',"%".$request->search_text."%");
              $query->orwhere('cont.phone1','like',"%".$request->search_text."%");
              $query->orwhere('cont.phone2','like',"%".$request->search_text."%");
              $query->orwhere('cont.primary_contact_no','like',"%".$request->search_text."%");
              $query->orwhere('cont.email','like',"%".$request->search_text."%");
              $query->orwhere('cont.email0','like',"%".$request->search_text."%");
              $query->orwhere('cont.email1','like',"%".$request->search_text."%");
              $query->orwhere('cont.email2','like',"%".$request->search_text."%");
              $query->orwhere('cont.regarding','like',"%".$request->search_text."%");
              $query->orwhere('lcs.name','like',"%".$request->search_text."%");
              $query->orwhere('ls.name','like',"%".$request->search_text."%");
              $query->orwhere('country.country_name','like',"%".$request->search_text."%");
              $query->orwhere('source.name','like',"%".$request->search_text."%");
              $query->orwhere('user.name','like',"%".$request->search_text."%");          
            });
          }

          
          // $data_con->orWhere(function($qpubs){
          //   $qpubs->where('cont.public_st','=',1);
          // });

          // $data_con->orWhere(function($qassid){
          //   $qassid->where('cont.assign_id','=',Auth::user()->user_id);
          // });

          // 26/09/2023 4:29PM Start
          // $data_con->orWhere('cont.assign_id','=',Auth::user()->user_id);
          // $data_con->orWhere('cont.public_st','=',1);
          
          $data_con->orWhere(function($dataq){
            $dataq->where('cont.assign_id','=',Auth::user()->user_id)->where('cont.public_st','=',1);
          });

          // 26/09/2023 4:29PM End

          if(isset($request->page_list) && $request->page_list != ''){
            $data_con_fl = $data_con->orderBy('cont.id','DESC')->paginate($request->page_list)->withQueryString();
          }else{
            $data_con_fl = $data_con->orderBy('cont.id','DESC')->paginate(10)->withQueryString();
          }
          

        }
      }else{
        $data_con = DB::table('qr_all_contacts_tbl as cont')
        ->leftjoin('users as user','cont.assign_id','=','user.user_id')
        ->leftjoin('qr_lifecycle_stage as lcs','cont.lcs_id','=','lcs.id')
        ->leftjoin('lstages as ls','cont.ls_id','=','ls.id')
        ->leftjoin('qr_country as country','cont.country_id','=','country.country_id')
        ->leftjoin('sources as source','cont.source_id','=','source.id')
        ->select('cont.*','user.name as username','lcs.name as lcsname','ls.name as lsname','country.country_name','source.name as sourcename')
        ->where('cont.user_id','=',Auth::user()->user_id)
        ->orWhere('cont.public_st','=','1')
        ->orWhere('cont.assign_id','=',Auth::user()->user_id)
        ->where('cont.contact_dl_status','=','0');

        if(isset($request->business_type) && $request->business_type != ''){
          $data_con->where('cont.lead_type','=',$request->business_type);
        }

        if(isset($request->lead_status) && $request->lead_status != ''){
          $data_con->where('cont.lcs_id','=',$request->lead_status);
        }

        if(isset($request->lead_stage) && $request->lead_stage != ''){
          $data_con->where('cont.ls_id','=',$request->lead_stage);
        }

        if(isset($request->lead_priority) && $request->lead_priority != ''){
          $data_con->where('cont.lead_prority','=',$request->lead_priority);
        }

        if(isset($request->country) && $request->country != ''){
          $data_con->where('cont.country_id','=',$request->country);
        }

        if(isset($request->source) && $request->source != ''){
          $data_con->where('cont.source_id','=',$request->source);
        }

        if(isset($request->careoff) && $request->careoff != ''){
          $data_con->where('cont.assign_id','=',$request->careoff);
        }

        if(isset($request->createby) && $request->createby != ''){
          $data_con->where('cont.user_id','=',$request->createby);
        }

        if(isset($request->optinoutD) && $request->optinoutD != ''){
      
          if($request->optinoutD == 'all'){
            $data_con->where('cont.optinout','=',null);
          }else{
            $data_con->where('cont.optinout','=',$request->optinoutD);            
          }

        }

        if(isset($request->group_id) && $request->group_id != ''){
          if($request->group_id == 'all'){
            $data_con->where('cont.group_id','=',null);
          }else{
            $data_con->where('cont.group_id','=',$request->group_id);
          }
        }

        if(isset($request->createdon) && $request->createdon != ''){
          $data_con->whereDate('cont.created_at','=',$request->createdon);
        }
        if(isset($request->updatedon) && $request->updatedon != ''){
          $data_con->whereDate('cont.updated_at','=',$request->updatedon);
        }

        if(isset($request->nxtfollowup) && $request->nxtfollowup != ''){
          $data_con->whereDate('cont.followup_date','=',$request->nxtfollowup);
        }

        if(isset($request->date_range) && $request->date_range != ''){
          $datenew = explode(" to ",$request->date_range);
          $startdate = $datenew[0];
          $enddate = $datenew[1];
          $data_con->whereBetween('cont.created_at',[$startdate.' 00:00:00',$enddate.' 23:59:59']);
          
        }

        if(isset($request->search_text) && $request->search_text != ''){
          $data_con->where(function($query) use($request){
            $query->orwhere('cont.full_name','like',"%".$request->search_text."%");
            $query->orwhere('cont.company_name','like',"%".$request->search_text."%");
            $query->orwhere('cont.lead_prority','like',"%".$request->search_text."%");
            $query->orwhere('cont.lead_type','like',"%".$request->search_text."%");
            $query->orwhere('cont.city','like',"%".$request->search_text."%");
            $query->orwhere('cont.followup_date','like',"%".$request->search_text."%");
            $query->orwhere('cont.created_at','like',"%".$request->search_text."%");
            $query->orwhere('cont.updated_at','like',"%".$request->search_text."%");
            $query->orwhere('cont.mobile_no','like',"%".$request->search_text."%");
            $query->orwhere('cont.phone0','like',"%".$request->search_text."%");
            $query->orwhere('cont.phone1','like',"%".$request->search_text."%");
            $query->orwhere('cont.phone2','like',"%".$request->search_text."%");
            $query->orwhere('cont.primary_contact_no','like',"%".$request->search_text."%");
            $query->orwhere('cont.email','like',"%".$request->search_text."%");
            $query->orwhere('cont.email0','like',"%".$request->search_text."%");
            $query->orwhere('cont.email1','like',"%".$request->search_text."%");
            $query->orwhere('cont.email2','like',"%".$request->search_text."%");
            $query->orwhere('cont.regarding','like',"%".$request->search_text."%");
            $query->orwhere('lcs.name','like',"%".$request->search_text."%");
            $query->orwhere('ls.name','like',"%".$request->search_text."%");
            $query->orwhere('country.country_name','like',"%".$request->search_text."%");
            $query->orwhere('source.name','like',"%".$request->search_text."%");
            $query->orwhere('user.name','like',"%".$request->search_text."%");
          });
        }

        if(isset($request->page_list) && $request->page_list != ''){
          $data_con_fl = $data_con->orderBy('cont.id','DESC')->paginate($request->page_list)->withQueryString();
        }else{
          $data_con_fl = $data_con->orderBy('cont.id','DESC')->paginate(10)->withQueryString();
        }
      }
    }

    if($request->ajax()){
      return view('content.crm.include.load_s',['data_cons' => $data_con_fl])->render();
    }
    

    $pageConfigs = ['pageHeader' => false];
    return view('content.crm.include.contact',['pageConfigs' => $pageConfigs,'data_cons' => $data_con_fl]);
  }


  public function optinoutupdt(Request $request)
  {
    $post = AllContact::find($request->id);
    // $post->optinout = $request->optinout;
    // if($request->optinout == 1){
    //   $post->assoc_number_id = implode(",",$request->assoc_number_id);
    // }else{
    //   $post->assoc_number_id = '';
    // }

    if($request->assoc_number_id != ''){
      $post->optinout = 1;
      $post->assoc_number_id = implode(",",$request->assoc_number_id);
    }else{
      $post->optinout = 0;
      $post->assoc_number_id = '';
    }
    
    $post->save();

    Session::flash('success','Otp In and Opt out updated!');

    return redirect()->back();

  }

}


