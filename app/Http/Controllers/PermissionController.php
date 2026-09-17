<?php

namespace App\Http\Controllers;

use App\AdminModel\AccessPermissionModule2;
use App\AdminModel\ModuleP;
use App\AdminModel\Staff;
use Illuminate\Http\Request;
use Session;

class PermissionController extends Controller
{

    

    public function index()
    {
        // $staff = Staff::find($id);
        // check service module
        // $services_mod = AccessPermissionModule2::where('user_id','=',$id)->where('services_mod','=','1')->first();
        // $crm_mod = AccessPermissionModule2::where('user_id','=',$id)->where('crm_mod','=','1')->first();
        // $ser_emp = AccessPermissionModule2::where('user_id','=',$id)->where('ser_emp','=','1')->first();
        // $ser_cand = AccessPermissionModule2::where('user_id','=',$id)->where('ser_cand','=','1')->first();
        
        
        $pageConfigs = ['pageHeader' => false];
        return view('content.permlist.index',[
            'pageConfigs' => $pageConfigs,
        
        ]);

    }

    public function update(Request $request)
    {
        // dd($request);        
        $accperm = new AccessPermissionModule2();        
        $allexist = AccessPermissionModule2::where('user_id','=',$request->user_id)->first();
        if (isset($allexist)) {
            $allexist->user_id = $request->user_id;
            $allexist->assign_by_id = 1;
            if ($request->full_access == 1) {
                $allexist->full_access = 1;
                $allexist->services = 1;
                $allexist->ser_employer = 1;
                $allexist->ser_emp_c = 1;
                $allexist->ser_emp_r = 1;
                $allexist->ser_emp_u = 1;
                $allexist->ser_emp_d = 1;
                $allexist->ser_emp_mkf = 1;
                $allexist->ser_candidate = 1;
                $allexist->ser_cand_c = 1;
                $allexist->ser_cand_r = 1;
                $allexist->ser_cand_u = 1;
                $allexist->ser_cand_d = 1;
                $allexist->crm = 1;
                $allexist->crm_all_contact = 1;
                $allexist->c_crm_ac = 1;
                $allexist->r_crm_ac = 1;
                $allexist->u_crm_ac = 1;
                $allexist->d_crm_ac = 1;
                $allexist->impf_crm_ac = 1;
                $allexist->crmallefs = 1;

                $allexist->crm_master = 1;
                $allexist->services_master = 1;

                $allexist->cmtype = 1;
                $allexist->cmtypec = 1;
                $allexist->cmtyper = 1;
                $allexist->cmtypeu = 1;
                $allexist->cmtyped = 1;
                $allexist->cmlcs = 1;
                $allexist->cmlcsc = 1;
                $allexist->cmlcsr = 1;
                $allexist->cmlcsu = 1;
                $allexist->cmlscd = 1;
                $allexist->cmsource = 1;
                $allexist->cmsourcec = 1;
                $allexist->cmsourcer = 1;
                $allexist->cmsourceu = 1;
                $allexist->cmsourced = 1;
                $allexist->cmconttype = 1;
                $allexist->cmconttypec = 1;
                $allexist->cmconttyper = 1;
                $allexist->cmconttypeu = 1;
                $allexist->cmconttyped = 1;
                $allexist->cmind = 1;
                $allexist->cmindc = 1;
                $allexist->cmindr = 1;
                $allexist->cmindu = 1;
                $allexist->cmindd = 1;
                $allexist->cmbranch = 1;
                $allexist->cmbranchc = 1;
                $allexist->cmbranchr = 1;
                $allexist->cmbranchu = 1;
                $allexist->cmbranchd = 1;
                $allexist->smvisap = 1;
                $allexist->smvisapc = 1;
                $allexist->smvisapr = 1;
                $allexist->smvisapu = 1;
                $allexist->smvisapd = 1;
                $allexist->smparty = 1;
                $allexist->smpartyc = 1;
                $allexist->smpartyr = 1;
                $allexist->smpartyu = 1;
                $allexist->smpartyd = 1;
                $allexist->smpartyst = 1;
                $allexist->smpartydv = 1;
                $allexist->smpartysc = 1;
                $allexist->smpartyslo = 1;
                $allexist->smsector = 1;
                $allexist->smsectorc = 1;
                $allexist->smsectorr = 1;
                $allexist->smsectoru = 1;
                $allexist->smsectord = 1;
                $allexist->smpartysp = 1;
                $allexist->smpartyspc = 1;
                $allexist->smpartyspr = 1;
                $allexist->smpartyspu = 1;
                $allexist->smpartyspd = 1;
                $allexist->smstaff = 1;
                $allexist->smstaffc = 1;
                $allexist->smstaffr = 1;
                $allexist->smstaffu = 1;
                $allexist->smstaffd = 1;
                $allexist->smmofafee = 1;
                $allexist->smmofafeec = 1;
                $allexist->smmofafeer = 1;
                $allexist->smmofafeeu = 1;
                $allexist->smmofafeed = 1;
                $allexist->smpoi = 1;
                $allexist->smpoic = 1;
                $allexist->smpoir = 1;
                $allexist->smpoiu = 1;
                $allexist->smpoid = 1;
                $allexist->smaddser = 1;
                $allexist->smaddserc = 1;
                $allexist->smaddserr = 1;
                $allexist->smaddseru = 1;
                $allexist->smaddserd = 1;
                $allexist->smrecrt = 1;
                $allexist->smrecrtc = 1;
                $allexist->smrecrtr = 1;
                $allexist->smrecrtu = 1;
                $allexist->smrecrtd = 1;
                $allexist->smsertyp = 1;
                $allexist->smsertypc = 1;
                $allexist->smsertypr = 1;
                $allexist->smsertypu = 1;
                $allexist->smsertypd = 1;

                $allexist->crmleads = 1;
                $allexist->crmleadsr = 1;
                $allexist->crmleadsu = 1;
                $allexist->crmleadsd = 1;

                $allexist->crm_cand = 1;
                $allexist->crm_cand_cr = 1;
                $allexist->crm_cand_r = 1;
                $allexist->crm_cand_u = 1;
                $allexist->crm_cand_d = 1;

                $allexist->crm_jobsk = 1;
                $allexist->crm_jobsk_cr = 1;
                $allexist->crm_jobsk_r = 1;
                $allexist->crm_jobsk_u = 1;
                $allexist->crm_jobsk_d = 1;

                $allexist->crm_company = 1;
                $allexist->crm_company_cr = 1;
                $allexist->crm_company_r = 1;
                $allexist->crm_company_u = 1;
                $allexist->crm_company_d = 1;

                $allexist->crm_assoc = 1;
                $allexist->crm_assoc_cr = 1;
                $allexist->crm_assoc_r = 1;
                $allexist->crm_assoc_u = 1;
                $allexist->crm_assoc_d = 1;


                $allexist->ser_status = 1;

                $allexist->ser_submission = 1;
                
                $allexist->ser_courier = 1;
                $allexist->ser_courier_del = 1;
                $allexist->ser_courier_add = 1;
                $allexist->ser_courier_view = 1;
                $allexist->ser_courier_edit = 1;

                // Service Whatsapp
                $allexist->whatsapp_ser = 1;
                $allexist->whatsAPI = 1;
                $allexist->whatsAPIc = 1;
                $allexist->whatsAPIr = 1;
                $allexist->whatsAPIu = 1;
                $allexist->whatsAPId = 1;

                // Whatsapp Marketing
                $allexist->marketing_whatsapp = 1;
                $allexist->whcampaignm = 1;
                $allexist->whcampaignmc = 1;
                $allexist->whcampaignmr = 1;
                $allexist->whcampaignmu = 1;
                $allexist->whcampaignmd = 1;
                $allexist->whcampaignmmul = 1;

                // Template Marketing
                $allexist->whtemplate = 1;
                $allexist->whtemplatec = 1;
                $allexist->whtemplater = 1;
                $allexist->whtemplateu = 1;
                $allexist->whtemplated = 1;

                // Emigration
                $allexist->ser_emig = 1;
                $allexist->ser_add_emig = 1;
                $allexist->ser_view_emig = 1;
                $allexist->ser_edit_emig = 1;
                $allexist->ser_del_emig = 1;
                $allexist->ser_st_emig = 1;

                // Service Payment Update
                $allexist->service_payment = 1;
                $allexist->dtxn = 1;
                $allexist->dtxnadd = 1;
                $allexist->dtxnview = 1;
                $allexist->dtxndel = 1;
                $allexist->dtxnedit = 1;

            }else{
                $allexist->full_access = 0;
                // Service Employer
                if($request->services == 2){
                    $allexist->services = 1;
                }else{
                    $allexist->services = 0;
                }
                if($request->semp == 'semp'){
                    $allexist->ser_employer = 1;
                }else{
                    $allexist->ser_employer = 0;
                }    
                   
                if($request->sempc == 'c'){
                    $allexist->ser_emp_c = 1;
                }else{
                    $allexist->ser_emp_c = 0;
                }
                if($request->sempr == 'r'){
                    $allexist->ser_emp_r = 1;
                }else{
                    $allexist->ser_emp_r = 0;
                }
                if($request->sempu == 'u'){
                    $allexist->ser_emp_u = 1;
                }else{
                    $allexist->ser_emp_u = 0;
                }
                if($request->sempd == 'd'){
                    $allexist->ser_emp_d = 1;
                }else{
                    $allexist->ser_emp_d = 0;
                }
                if($request->emf == 'emf'){
                    $allexist->ser_emp_mkf = 1;
                }else{
                    $allexist->ser_emp_mkf = 0;
                }
        
                // Service Candidate
                if($request->scand == 'scand'){
                    $allexist->ser_candidate = 1;
                }else{
                    $allexist->ser_candidate = 0;
                }    
                    
                    
                if($request->scandc == 'c'){
                    $allexist->ser_cand_c = 1;
                }else{
                    $allexist->ser_cand_c = 0;
                }
                if($request->scandr == 'r'){
                    $allexist->ser_cand_r = 1;
                }else{
                    $allexist->ser_cand_r = 0;
                }
                if($request->scandu == 'u'){
                    $allexist->ser_cand_u = 1;
                }else{
                    $allexist->ser_cand_u = 0;
                }
                if($request->scandd == 'd'){
                    $allexist->ser_cand_d = 1;
                }else{
                    $allexist->ser_cand_d = 0;
                }
                    
                
                // CRM All Contact
        
                if($request->crms == 1){
                    $allexist->crm = 1;
                }else{
                    $allexist->crm = 0;
                }
        
                if($request->crmallci == 'crmallci'){
                    $allexist->crm_all_contact = 1;
                }else{
                    $allexist->crm_all_contact = 0;
                }   
                    
                if($request->crmallc == 'c'){
                    $allexist->c_crm_ac = 1;
                }else{
                    $allexist->c_crm_ac = 0;
                }
                if($request->crmallr == 'r'){
                    $allexist->r_crm_ac = 1;
                }else{
                    $allexist->r_crm_ac = 0;
                }
                if($request->crmallu == 'u'){
                    $allexist->u_crm_ac = 1;
                }else{
                    $allexist->u_crm_ac = 0;
                }
                if($request->crmalld == 'd'){
                    $allexist->d_crm_ac = 1;
                }else{
                    $allexist->d_crm_ac = 0;
                }
                if($request->crmallimf == 'crmallimf'){
                    $allexist->impf_crm_ac = 1;
                }else{
                    $allexist->impf_crm_ac = 0;
                }

                if($request->crmallefs == 'enfs'){
                    $allexist->crmallefs = 1;
                }else{
                    $allexist->crmallefs = 0;
                }

                // CRM Master
                if($request->crm_master == 1){
                    $allexist->crm_master = 1;
                }else{
                    $allexist->crm_master = 0;
                }

                if($request->cmtype == 'cmtype'){
                    $allexist->cmtype = 1;
                }else{
                    $allexist->cmtype = 0;
                }   
                    
                if($request->cmtypec == 'c'){
                    $allexist->cmtypec = 1;
                }else{
                    $allexist->cmtypec = 0;
                }
                if($request->cmtyper == 'r'){
                    $allexist->cmtyper = 1;
                }else{
                    $allexist->cmtyper = 0;
                }
                if($request->cmtypeu == 'u'){
                    $allexist->cmtypeu = 1;
                }else{
                    $allexist->cmtypeu = 0;
                }
                if($request->cmtyped == 'd'){
                    $allexist->cmtyped = 1;
                }else{
                    $allexist->cmtyped = 0;
                }

                if($request->cmlcs == 'cmlcs'){
                    $allexist->cmlcs = 1;
                }else{
                    $allexist->cmlcs = 0;
                }      
                if($request->cmlcsc == 'c'){
                    $allexist->cmlcsc = 1;
                }else{
                    $allexist->cmlcsc = 0;
                }
                if($request->cmlcsr == 'r'){
                    $allexist->cmlcsr = 1;
                }else{
                    $allexist->cmlcsr = 0;
                }
                if($request->cmlcsu == 'u'){
                    $allexist->cmlcsu = 1;
                }else{
                    $allexist->cmlcsu = 0;
                }
                if($request->cmlscd == 'd'){
                    $allexist->cmlscd = 1;
                }else{
                    $allexist->cmlscd = 0;
                }

                if($request->cmsource == 'cmsource'){
                    $allexist->cmsource = 1;
                }else{
                    $allexist->cmsource = 0;
                }      
                if($request->cmsourcec == 'c'){
                    $allexist->cmsourcec = 1;
                }else{
                    $allexist->cmsourcec = 0;
                }
                if($request->cmsourcer == 'r'){
                    $allexist->cmsourcer = 1;
                }else{
                    $allexist->cmsourcer = 0;
                }
                if($request->cmsourceu == 'u'){
                    $allexist->cmsourceu = 1;
                }else{
                    $allexist->cmsourceu = 0;
                }
                if($request->cmsourced == 'd'){
                    $allexist->cmsourced = 1;
                }else{
                    $allexist->cmsourced = 0;
                }

                if($request->cmconttype == 'cmconttype'){
                    $allexist->cmconttype = 1;
                }else{
                    $allexist->cmconttype = 0;
                }      
                if($request->cmconttypec == 'c'){
                    $allexist->cmconttypec = 1;
                }else{
                    $allexist->cmconttypec = 0;
                }
                if($request->cmconttyper == 'r'){
                    $allexist->cmconttyper = 1;
                }else{
                    $allexist->cmconttyper = 0;
                }
                if($request->cmconttypeu == 'u'){
                    $allexist->cmconttypeu = 1;
                }else{
                    $allexist->cmconttypeu = 0;
                }
                if($request->cmconttyped == 'd'){
                    $allexist->cmconttyped = 1;
                }else{
                    $allexist->cmconttyped = 0;
                }

                if($request->cmind == 'cmind'){
                    $allexist->cmind = 1;
                }else{
                    $allexist->cmind = 0;
                }      
                if($request->cmindc == 'c'){
                    $allexist->cmindc = 1;
                }else{
                    $allexist->cmindc = 0;
                }
                if($request->cmindr == 'r'){
                    $allexist->cmindr = 1;
                }else{
                    $allexist->cmindr = 0;
                }
                if($request->cmindu == 'u'){
                    $allexist->cmindu = 1;
                }else{
                    $allexist->cmindu = 0;
                }
                if($request->cmindd == 'd'){
                    $allexist->cmindd = 1;
                }else{
                    $allexist->cmindd = 0;
                }

                if($request->cmbranch == 'cmbranch'){
                    $allexist->cmbranch = 1;
                }else{
                    $allexist->cmbranch = 0;
                }      
                if($request->cmbranchc == 'c'){
                    $allexist->cmbranchc = 1;
                }else{
                    $allexist->cmbranchc = 0;
                }
                if($request->cmbranchr == 'r'){
                    $allexist->cmbranchu = 1;
                }else{
                    $allexist->cmbranchu = 0;
                }
                if($request->cmbranchu == 'u'){
                    $allexist->cmbranchd = 1;
                }else{
                    $allexist->cmbranchd = 0;
                }
                if($request->cmbranchd == 'd'){
                    $allexist->cmbranchr = 1;
                }else{
                    $allexist->cmbranchr = 0;
                }

                // Service Master
                if($request->services_master == 1){
                    $allexist->services_master = 1;
                }else{
                    $allexist->services_master = 0;
                }
                // 01
                if($request->smvisap == 'smvisap'){
                    $allexist->smvisap = 1;
                }else{
                    $allexist->smvisap = 0;
                }   
                    
                if($request->smvisapc == 'c'){
                    $allexist->smvisapc = 1;
                }else{
                    $allexist->smvisapc = 0;
                }
                if($request->smvisapr == 'r'){
                    $allexist->smvisapr = 1;
                }else{
                    $allexist->smvisapr = 0;
                }
                if($request->smvisapu == 'u'){
                    $allexist->smvisapu = 1;
                }else{
                    $allexist->smvisapu = 0;
                }
                if($request->smvisapd == 'd'){
                    $allexist->smvisapd = 1;
                }else{
                    $allexist->smvisapd = 0;
                }
                // 02
                if($request->smparty == 'smparty'){
                    $allexist->smparty = 1;
                }else{
                    $allexist->smparty = 0;
                }      
                if($request->smpartyc == 'c'){
                    $allexist->smpartyc = 1;
                }else{
                    $allexist->smpartyc = 0;
                }
                if($request->smpartyr == 'r'){
                    $allexist->smpartyr = 1;
                }else{
                    $allexist->smpartyr = 0;
                }
                if($request->smpartyu == 'u'){
                    $allexist->smpartyu = 1;
                }else{
                    $allexist->smpartyu = 0;
                }
                if($request->smpartyd == 'd'){
                    $allexist->smpartyd = 1;
                }else{
                    $allexist->smpartyd = 0;
                }
                if($request->smpartyst == 'smpartyst'){
                    $allexist->smpartyst = 1;
                }else{
                    $allexist->smpartyst = 0;
                }

                if($request->smpartydv == 'smpartydv'){
                    $allexist->smpartydv = 1;
                }else{
                    $allexist->smpartydv = 0;
                }

                if($request->smpartysc == 'smpartysc'){
                    $allexist->smpartysc = 1;
                }else{
                    $allexist->smpartysc = 0;
                }

                if($request->smpartyslo == 1){
                    $allexist->smpartyslo = 1;
                }else{
                    $allexist->smpartyslo = 0;
                }

                // 03
                if($request->smsector == 'smsector'){
                    $allexist->smsector = 1;
                }else{
                    $allexist->smsector = 0;
                }      
                if($request->smsectorc == 'c'){
                    $allexist->smsectorc = 1;
                }else{
                    $allexist->smsectorc = 0;
                }
                if($request->smsectorr == 'r'){
                    $allexist->smsectorr = 1;
                }else{
                    $allexist->smsectorr = 0;
                }
                if($request->smsectoru == 'u'){
                    $allexist->smsectoru = 1;
                }else{
                    $allexist->smsectoru = 0;
                }
                if($request->smsectord == 'd'){
                    $allexist->smsectord = 1;
                }else{
                    $allexist->smsectord = 0;
                }
                // 04
                if($request->smpartysp == 'smpartysp'){
                    $allexist->smpartysp = 1;
                }else{
                    $allexist->smpartysp = 0;
                }      
                if($request->smpartyspc == 'c'){
                    $allexist->smpartyspc = 1;
                }else{
                    $allexist->smpartyspc = 0;
                }
                if($request->smpartyspr == 'r'){
                    $allexist->smpartyspr = 1;
                }else{
                    $allexist->smpartyspr = 0;
                }
                if($request->smpartyspu == 'u'){
                    $allexist->smpartyspu = 1;
                }else{
                    $allexist->smpartyspu = 0;
                }
                if($request->smpartyspd == 'd'){
                    $allexist->smpartyspd = 1;
                }else{
                    $allexist->smpartyspd = 0;
                }
                // 05
                if($request->smstaff == 'smstaff'){
                    $allexist->smstaff = 1;
                }else{
                    $allexist->smstaff = 0;
                }      
                if($request->smstaffc == 'c'){
                    $allexist->smstaffc = 1;
                }else{
                    $allexist->smstaffc = 0;
                }
                if($request->smstaffr == 'r'){
                    $allexist->smstaffr = 1;
                }else{
                    $allexist->smstaffr = 0;
                }
                if($request->smstaffu == 'u'){
                    $allexist->smstaffu = 1;
                }else{
                    $allexist->smstaffu = 0;
                }
                if($request->smstaffd == 'd'){
                    $allexist->smstaffd = 1;
                }else{
                    $allexist->smstaffd = 0;
                }
                // 06
                if($request->smmofafee == 'smmofafee'){
                    $allexist->smmofafee = 1;
                }else{
                    $allexist->smmofafee = 0;
                }      
                if($request->smmofafeec == 'c'){
                    $allexist->smmofafeec = 1;
                }else{
                    $allexist->smmofafeec = 0;
                }
                if($request->smmofafeer == 'r'){
                    $allexist->smmofafeer = 1;
                }else{
                    $allexist->smmofafeer = 0;
                }
                if($request->smmofafeeu == 'u'){
                    $allexist->smmofafeeu = 1;
                }else{
                    $allexist->smmofafeeu = 0;
                }
                if($request->smmofafeed == 'd'){
                    $allexist->smmofafeed = 1;
                }else{
                    $allexist->smmofafeed = 0;
                }
                // 07
                if($request->smpoi == 'smpoi'){
                    $allexist->smpoi = 1;
                }else{
                    $allexist->smpoi = 0;
                }      
                if($request->smpoic == 'c'){
                    $allexist->smpoic = 1;
                }else{
                    $allexist->smpoic = 0;
                }
                if($request->smpoir == 'r'){
                    $allexist->smpoir = 1;
                }else{
                    $allexist->smpoir = 0;
                }
                if($request->smpoiu == 'u'){
                    $allexist->smpoiu = 1;
                }else{
                    $allexist->smpoiu = 0;
                }
                if($request->smpoid == 'd'){
                    $allexist->smpoid = 1;
                }else{
                    $allexist->smpoid = 0;
                }
                // 08
                if($request->smaddser == 'smaddser'){
                    $allexist->smaddser = 1;
                }else{
                    $allexist->smaddser = 0;
                }      
                if($request->smaddserc == 'c'){
                    $allexist->smaddserc = 1;
                }else{
                    $allexist->smaddserc = 0;
                }
                if($request->smaddserr == 'r'){
                    $allexist->smaddserr = 1;
                }else{
                    $allexist->smaddserr = 0;
                }
                if($request->smaddseru == 'u'){
                    $allexist->smaddseru = 1;
                }else{
                    $allexist->smaddseru = 0;
                }
                if($request->smaddserd == 'd'){
                    $allexist->smaddserd = 1;
                }else{
                    $allexist->smaddserd = 0;
                }
                // 09
                if($request->smrecrt == 'smrecrt'){
                    $allexist->smrecrt = 1;
                }else{
                    $allexist->smrecrt = 0;
                }      
                if($request->smrecrtc == 'c'){
                    $allexist->smrecrtc = 1;
                }else{
                    $allexist->smrecrtc = 0;
                }
                if($request->smrecrtr == 'r'){
                    $allexist->smrecrtr = 1;
                }else{
                    $allexist->smrecrtr = 0;
                }
                if($request->smrecrtu == 'u'){
                    $allexist->smrecrtu = 1;
                }else{
                    $allexist->smrecrtu = 0;
                }
                if($request->smrecrtd == 'd'){
                    $allexist->smrecrtd = 1;
                }else{
                    $allexist->smrecrtd = 0;
                }
                // 10
                if($request->smsertyp == 'smsertyp'){
                    $allexist->smsertyp = 1;
                }else{
                    $allexist->smsertyp = 0;
                }      
                if($request->smsertypc == 'c'){
                    $allexist->smsertypc = 1;
                }else{
                    $allexist->smsertypc = 0;
                }
                if($request->smsertypr == 'r'){
                    $allexist->smsertypr = 1;
                }else{
                    $allexist->smsertypr = 0;
                }
                if($request->smsertypu == 'u'){
                    $allexist->smsertypu = 1;
                }else{
                    $allexist->smsertypu = 0;
                }
                if($request->smsertypd == 'd'){
                    $allexist->smsertypd = 1;
                }else{
                    $allexist->smsertypd = 0;
                }

                if($request->crmleads == 'crmleads'){
                    $allexist->crmleads = 1;
                }else{
                    $allexist->crmleads = 0;
                }
                if($request->crmleadsr == 'crmleadsr'){
                    $allexist->crmleadsr = 1;
                }else{
                    $allexist->crmleadsr = 0;
                }
                if($request->crmleadsu == 'crmleadsu'){
                    $allexist->crmleadsu = 1;
                }else{
                    $allexist->crmleadsu = 0;
                }
                if($request->crmleadsd == 'crmleadsd'){
                    $allexist->crmleadsd = 1;
                }else{
                    $allexist->crmleadsd = 0;
                }

                // CRM Candidate
                if($request->crm_cand == 'crm_cand'){
                    $allexist->crm_cand = 1;
                }else{
                    $allexist->crm_cand = 0;
                }
                if($request->crm_cand_cr == 'crm_cand_cr'){
                    $allexist->crm_cand_cr = 1;
                }else{
                    $allexist->crm_cand_cr = 0;
                }
                if($request->crm_cand_r == 'crm_cand_r'){
                    $allexist->crm_cand_r = 1;
                }else{
                    $allexist->crm_cand_r = 0;
                }
                if($request->crm_cand_u == 'crm_cand_u'){
                    $allexist->crm_cand_u = 1;
                }else{
                    $allexist->crm_cand_u = 0;
                }

                if($request->crm_cand_d == 'crm_cand_d'){
                    $allexist->crm_cand_d = 1;
                }else{
                    $allexist->crm_cand_d = 0;
                }

                // Job Seeker
                if($request->crm_jobsk == 'crm_jobsk'){
                    $allexist->crm_jobsk = 1;
                }else{
                    $allexist->crm_jobsk = 0;
                }
                if($request->crm_jobsk_cr == 'crm_jobsk_cr'){
                    $allexist->crm_jobsk_cr = 1;
                }else{
                    $allexist->crm_jobsk_cr = 0;
                }
                if($request->crm_jobsk_r == 'crm_jobsk_r'){
                    $allexist->crm_jobsk_r = 1;
                }else{
                    $allexist->crm_jobsk_r = 0;
                }
                if($request->crm_jobsk_u == 'crm_jobsk_u'){
                    $allexist->crm_jobsk_u = 1;
                }else{
                    $allexist->crm_jobsk_u = 0;
                }
                if($request->crm_jobsk_d == 'crm_jobsk_d'){
                    $allexist->crm_jobsk_d = 1;
                }else{
                    $allexist->crm_jobsk_d = 0;
                }

                if($request->crm_company == 'crm_company'){
                    $allexist->crm_company = 1;
                }else{
                    $allexist->crm_company = 0;
                }
                if($request->crm_company_cr == 'crm_company_cr'){
                    $allexist->crm_company_cr = 1;
                }else{
                    $allexist->crm_company_cr = 0;
                }
                if($request->crm_company_r == 'crm_company_r'){
                    $allexist->crm_company_r = 1;
                }else{
                    $allexist->crm_company_r = 0;
                }
                if($request->crm_company_u == 'crm_company_u'){
                    $allexist->crm_company_u = 1;
                }else{
                    $allexist->crm_company_u = 0;
                }
                if($request->crm_company_d == 'crm_company_d'){
                    $allexist->crm_company_d = 1;
                }else{
                    $allexist->crm_company_d = 0;
                }

                if($request->crm_assoc == 'crm_assoc'){
                    $allexist->crm_assoc = 1;
                }else{
                    $allexist->crm_assoc = 0;
                }
                if($request->crm_assoc_cr == 'crm_assoc_cr'){
                    $allexist->crm_assoc_cr = 1;
                }else{
                    $allexist->crm_assoc_cr = 0;
                }
                if($request->crm_assoc_r == 'crm_assoc_r'){
                    $allexist->crm_assoc_r = 1;
                }else{
                    $allexist->crm_assoc_r = 0;
                }
                if($request->crm_assoc_u == 'crm_assoc_u'){
                    $allexist->crm_assoc_u = 1;
                }else{
                    $allexist->crm_assoc_u = 0;
                }
                if($request->crm_assoc_d == 'crm_assoc_d'){
                    $allexist->crm_assoc_d = 1;
                }else{
                    $allexist->crm_assoc_d = 0;
                }

                if($request->ser_status == 'ser_status'){
                    $allexist->ser_status = 1;
                }else{
                    $allexist->ser_status = 0;
                }

                if($request->ser_submission == 'ser_submission'){
                    $allexist->ser_submission = 1;
                }else{
                    $allexist->ser_submission = 0;
                }

                if($request->ser_courier == 'ser_courier'){
                    $allexist->ser_courier = 1;
                }else{
                    $allexist->ser_courier = 0;
                }

                if($request->ser_courier_del == '1'){
                    $allexist->ser_courier_del = 1;
                }else{
                    $allexist->ser_courier_del = 0;
                }

                if($request->ser_courier_add == '1'){
                    $allexist->ser_courier_add = 1;
                }else{
                    $allexist->ser_courier_add = 0;
                }

                if($request->ser_courier_view == '1'){
                    $allexist->ser_courier_view = 1;
                }else{
                    $allexist->ser_courier_view = 0;
                }

                if($request->ser_courier_edit == '1'){
                    $allexist->ser_courier_edit = 1;
                }else{
                    $allexist->ser_courier_edit = 0;
                }


                // Whatsapp Service
                if($request->whatsapp_ser == '1'){
                    $allexist->whatsapp_ser = 1;
                }else{
                    $allexist->whatsapp_ser = 0;
                }

                if($request->whatsAPI == '1'){
                    $allexist->whatsAPI = 1;
                }else{
                    $allexist->whatsAPI = 0;
                }

                if($request->whatsAPIc == 'c'){
                    $allexist->whatsAPIc = 1;
                }else{
                    $allexist->whatsAPIc = 0;
                }

                if($request->whatsAPIr == 'r'){
                    $allexist->whatsAPIr = 1;
                }else{
                    $allexist->whatsAPIr = 0;
                }

                if($request->whatsAPIu == 'u'){
                    $allexist->whatsAPIu = 1;
                }else{
                    $allexist->whatsAPIu = 0;
                }

                if($request->whatsAPId == 'd'){
                    $allexist->whatsAPId = 1;
                }else{
                    $allexist->whatsAPId = 0;
                }

                // Marketing Whatsappp
                if($request->marketing_whatsapp == '1'){
                    $allexist->marketing_whatsapp = 1;
                }else{
                    $allexist->marketing_whatsapp = 0;
                }

                if($request->whcampaignm == 'whcampaignm'){
                    $allexist->whcampaignm = 1;
                }else{
                    $allexist->whcampaignm = 0;
                }

                if($request->whcampaignmc == 'c'){
                    $allexist->whcampaignmc = 1;
                }else{
                    $allexist->whcampaignmc = 0;
                }

                if($request->whcampaignmr == 'r'){
                    $allexist->whcampaignmr = 1;
                }else{
                    $allexist->whcampaignmr = 0;
                }

                if($request->whcampaignmu == 'u'){
                    $allexist->whcampaignmu = 1;
                }else{
                    $allexist->whcampaignmu = 0;
                }

                if($request->whcampaignmd == 'd'){
                    $allexist->whcampaignmd = 1;
                }else{
                    $allexist->whcampaignmd = 0;
                }

                if($request->whcampaignmmul == '1'){
                    $allexist->whcampaignmmul = 1;
                }else{
                    $allexist->whcampaignmmul = 0;
                }

                // Template Marketing
                if($request->whtemplate == 'whtemplate'){
                    $allexist->whtemplate = 1;
                }else{
                    $allexist->whtemplate = 0;
                }

                if($request->whtemplatec == 'c'){
                    $allexist->whtemplatec = 1;
                }else{
                    $allexist->whtemplatec = 0;
                }

                if($request->whtemplater == 'r'){
                    $allexist->whtemplater = 1;
                }else{
                    $allexist->whtemplater = 0;
                }

                if($request->whtemplateu == 'u'){
                    $allexist->whtemplateu = 1;
                }else{
                    $allexist->whtemplateu = 0;
                }

                if($request->whtemplated == 'd'){
                    $allexist->whtemplated = 1;
                }else{
                    $allexist->whtemplated = 0;
                }

                if($request->ser_emig == 'ser_emig'){
                    $allexist->ser_emig = 1;
                }else{
                    $allexist->ser_emig = 0;
                }

                if($request->ser_add_emig == 'ser_add_emig'){
                    $allexist->ser_add_emig = 1;
                }else{
                    $allexist->ser_add_emig = 0;
                }

                if($request->ser_view_emig == 'ser_view_emig'){
                    $allexist->ser_view_emig = 1;
                }else{
                    $allexist->ser_view_emig = 0;
                }

                if($request->ser_edit_emig == 'ser_edit_emig'){
                    $allexist->ser_edit_emig = 1;
                }else{
                    $allexist->ser_edit_emig = 0;
                }

                if($request->ser_del_emig == 'ser_del_emig'){
                    $allexist->ser_del_emig = 1;
                }else{
                    $allexist->ser_del_emig = 0;
                }

                if($request->ser_st_emig == 'ser_st_emig'){
                    $allexist->ser_st_emig = 1;
                }else{
                    $allexist->ser_st_emig = 0;
                }

                if($request->service_payment == '1'){
                    $allexist->service_payment = 1;
                }else{
                    $allexist->service_payment = 0;
                }

                if($request->dtxn == 'dtxn'){
                    $allexist->dtxn = 1;
                }else{
                    $allexist->dtxn = 0;
                }

                if($request->dtxnadd == 'dtxnadd'){
                    $allexist->dtxnadd = 1;
                }else{
                    $allexist->dtxnadd = 0;
                }

                if($request->dtxnview == 'dtxnview'){
                    $allexist->dtxnview = 1;
                }else{
                    $allexist->dtxnview = 0;
                }

                if($request->dtxndel == 'dtxndel'){
                    $allexist->dtxndel = 1;
                }else{
                    $allexist->dtxndel = 0;
                }

                if($request->dtxnedit == 'dtxnedit'){
                    $allexist->dtxnedit = 1;
                }else{
                    $allexist->dtxnedit = 0;
                }


            }
    
            $allexist->save();    
        }else{
            $accperm->user_id = $request->user_id;
            $accperm->assign_by_id = 1;
            // Service Employer

            if ($request->full_access == 1) {

                $accperm->full_access = 1;
                $accperm->services = 1;
                $accperm->ser_employer = 1;
                $accperm->ser_emp_c = 1;
                $accperm->ser_emp_r = 1;
                $accperm->ser_emp_u = 1;
                $accperm->ser_emp_d = 1;
                $accperm->ser_emp_mkf = 1;
                $accperm->ser_candidate = 1;
                $accperm->ser_cand_c = 1;
                $accperm->ser_cand_r = 1;
                $accperm->ser_cand_u = 1;
                $accperm->ser_cand_d = 1;
                $accperm->crm = 1;
                $accperm->crm_all_contact = 1;
                $accperm->c_crm_ac = 1;
                $accperm->r_crm_ac = 1;
                $accperm->u_crm_ac = 1;
                $accperm->d_crm_ac = 1;
                $accperm->impf_crm_ac = 1;
                $accperm->crmallefs = 1;

                $accperm->cmtype = 1;
                $accperm->cmtypec = 1;
                $accperm->cmtyper = 1;
                $accperm->cmtypeu = 1;
                $accperm->cmtyped = 1;
                $accperm->cmlcs = 1;
                $accperm->cmlcsc = 1;
                $accperm->cmlcsr = 1;
                $accperm->cmlcsu = 1;
                $accperm->cmlscd = 1;
                $accperm->cmsource = 1;
                $accperm->cmsourcec = 1;
                $accperm->cmsourcer = 1;
                $accperm->cmsourceu = 1;
                $accperm->cmsourced = 1;
                $accperm->cmconttype = 1;
                $accperm->cmconttypec = 1;
                $accperm->cmconttyper = 1;
                $accperm->cmconttypeu = 1;
                $accperm->cmconttyped = 1;
                $accperm->cmind = 1;
                $accperm->cmindc = 1;
                $accperm->cmindr = 1;
                $accperm->cmindu = 1;
                $accperm->cmindd = 1;
                $accperm->cmbranch = 1;
                $accperm->cmbranchc = 1;
                $accperm->cmbranchr = 1;
                $accperm->cmbranchu = 1;
                $accperm->cmbranchd = 1;
                $accperm->smvisap = 1;
                $accperm->smvisapc = 1;
                $accperm->smvisapr = 1;
                $accperm->smvisapu = 1;
                $accperm->smvisapd = 1;
                $accperm->smparty = 1;
                $accperm->smpartyc = 1;
                $accperm->smpartyr = 1;
                $accperm->smpartyu = 1;
                $accperm->smpartyd = 1;
                $accperm->smpartyst = 1;
                $accperm->smpartydv = 1;
                $accperm->smpartysc = 1;
                $accperm->smpartyslo = 1;
                $accperm->smsector = 1;
                $accperm->smsectorc = 1;
                $accperm->smsectorr = 1;
                $accperm->smsectoru = 1;
                $accperm->smsectord = 1;
                $accperm->smpartysp = 1;
                $accperm->smpartyspc = 1;
                $accperm->smpartyspr = 1;
                $accperm->smpartyspu = 1;
                $accperm->smpartyspd = 1;
                $accperm->smstaff = 1;
                $accperm->smstaffc = 1;
                $accperm->smstaffr = 1;
                $accperm->smstaffu = 1;
                $accperm->smstaffd = 1;
                $accperm->smmofafee = 1;
                $accperm->smmofafeec = 1;
                $accperm->smmofafeer = 1;
                $accperm->smmofafeeu = 1;
                $accperm->smmofafeed = 1;
                $accperm->smpoi = 1;
                $accperm->smpoic = 1;
                $accperm->smpoir = 1;
                $accperm->smpoiu = 1;
                $accperm->smpoid = 1;
                $accperm->smaddser = 1;
                $accperm->smaddserc = 1;
                $accperm->smaddserr = 1;
                $accperm->smaddseru = 1;
                $accperm->smaddserd = 1;
                $accperm->smrecrt = 1;
                $accperm->smrecrtc = 1;
                $accperm->smrecrtr = 1;
                $accperm->smrecrtu = 1;
                $accperm->smrecrtd = 1;
                $accperm->smsertyp = 1;
                $accperm->smsertypc = 1;
                $accperm->smsertypr = 1;
                $accperm->smsertypu = 1;
                $accperm->smsertypd = 1;

                // Leads update
                $accperm->crmleads = 1;
                $accperm->crmleadsr = 1;
                $accperm->crmleadsu = 1;
                $accperm->crmleadsd = 1;
                
                // CRM Candidate
                $accperm->crm_cand = 1;
                $accperm->crm_cand_cr = 1;
                $accperm->crm_cand_r = 1;
                $accperm->crm_cand_u = 1;
                $accperm->crm_cand_d = 1;

                // CRM Job Seeker
                $accperm->crm_jobsk = 1;
                $accperm->crm_jobsk_cr = 1;
                $accperm->crm_jobsk_r = 1;
                $accperm->crm_jobsk_u = 1;
                $accperm->crm_jobsk_d = 1;

                $accperm->crm_company = 1;
                $accperm->crm_company_cr = 1;
                $accperm->crm_company_r = 1;
                $accperm->crm_company_u = 1;
                $accperm->crm_company_d = 1;

                $accperm->crm_assoc = 1;
                $accperm->crm_assoc_cr = 1;
                $accperm->crm_assoc_r = 1;
                $accperm->crm_assoc_u = 1;
                $accperm->crm_assoc_d = 1;
                $accperm->ser_status = 1;

                
                $accperm->ser_submission = 1;

                $accperm->ser_courier = 1;
                $accperm->ser_courier_del = 1;
                $accperm->ser_courier_add = 1;
                $accperm->ser_courier_view = 1;
                $accperm->ser_courier_edit = 1;

                // Whatsapp Service Tab
                $accperm->whatsapp_ser = 1;
                $accperm->whatsAPI = 1;
                $accperm->whatsAPIc = 1;
                $accperm->whatsAPIr = 1;
                $accperm->whatsAPIu = 1;
                $accperm->whatsAPId = 1;
                $accperm->whatsAPI = 1;

                // Marketing Whatsapp
                $accperm->marketing_whatsapp = 1;
                $accperm->whcampaignm = 1;
                $accperm->whcampaignmc = 1;
                $accperm->whcampaignmr = 1;
                $accperm->whcampaignmu = 1;
                $accperm->whcampaignmd = 1;
                $accperm->whcampaignmmul = 1;

                // Template Marketing
                $accperm->whtemplate = 1;
                $accperm->whtemplatec = 1;
                $accperm->whtemplater = 1;
                $accperm->whtemplateu = 1;
                $accperm->whtemplated = 1;

                // Emigration
                $accperm->ser_emig = 1;
                $accperm->ser_add_emig = 1;
                $accperm->ser_view_emig = 1;
                $accperm->ser_edit_emig = 1;
                $accperm->ser_del_emig = 1;
                $accperm->ser_st_emig = 1;

                $accperm->service_payment = 1;
                $accperm->dtxn = 1;
                $accperm->dtxnadd = 1;
                $accperm->dtxnview = 1;
                $accperm->dtxndel = 1;
                $accperm->dtxnedit = 1;



            } else {
                $accperm->full_access = 0;

                if($request->services == 2){
                    $accperm->services = 1;
                }else{
                    $accperm->services = 0;
                }
                if($request->semp == 'semp'){
                    $accperm->ser_employer = 1;
                }else{
                    $accperm->ser_employer = 0;
                }    
                
                if($request->sempc == 'c'){
                    $accperm->ser_emp_c = 1;
                }else{
                    $accperm->ser_emp_c = 0;
                }
                if($request->sempr == 'r'){
                    $accperm->ser_emp_r = 1;
                }else{
                    $accperm->ser_emp_r = 0;
                }
                if($request->sempu == 'u'){
                    $accperm->ser_emp_u = 1;
                }else{
                    $accperm->ser_emp_u = 0;
                }
                if($request->sempd == 'd'){
                    $accperm->ser_emp_d = 1;
                }else{
                    $accperm->ser_emp_d = 0;
                }
                if($request->emf == 'emf'){
                    $accperm->ser_emp_mkf = 1;
                }else{
                    $accperm->ser_emp_mkf = 0;
                }
        
                // Service Candidate
                if($request->scand == 'scand'){
                    $accperm->ser_candidate = 1;
                }else{
                    $accperm->ser_candidate = 0;
                }    
                    
                    
                if($request->scandc == 'c'){
                    $accperm->ser_cand_c = 1;
                }else{
                    $accperm->ser_cand_c = 0;
                }
                if($request->scandr == 'r'){
                    $accperm->ser_cand_r = 1;
                }else{
                    $accperm->ser_cand_r = 0;
                }
                if($request->scandu == 'u'){
                    $accperm->ser_cand_u = 1;
                }else{
                    $accperm->ser_cand_u = 0;
                }
                if($request->scandd == 'd'){
                    $accperm->ser_cand_d = 1;
                }else{
                    $accperm->ser_cand_d = 0;
                }
                    
                
                // CRM All Contact
        
                if($request->crms == 1){
                    $accperm->crm = 1;
                }else{
                    $accperm->crm = 0;
                }
        
                if($request->crmallci == 'crmallci'){
                    $accperm->crm_all_contact = 1;
                }else{
                    $accperm->crm_all_contact = 0;
                }
                
                    
                    
                if($request->crmallc == 'c'){
                    $accperm->c_crm_ac = 1;
                }else{
                    $accperm->c_crm_ac = 0;
                }
                if($request->crmallr == 'r'){
                    $accperm->r_crm_ac = 1;
                }else{
                    $accperm->r_crm_ac = 0;
                }
                if($request->crmallu == 'u'){
                    $accperm->u_crm_ac = 1;
                }else{
                    $accperm->u_crm_ac = 0;
                }
                if($request->crmalld == 'd'){
                    $accperm->d_crm_ac = 1;
                }else{
                    $accperm->d_crm_ac = 0;
                }
                if($request->crmallimf == 'crmallimf'){
                    $accperm->impf_crm_ac = 1;
                }else{
                    $accperm->impf_crm_ac = 0;
                }

                if($request->crmallefs == 'enfs'){
                    $accperm->crmallefs = 1;
                }else{
                    $accperm->crmallefs = 0;
                }

                // CRM Master
                if($request->crm_master == 1){
                    $accperm->crm_master = 1;
                }else{
                    $accperm->crm_master = 0;
                }

                if($request->cmtype == 'cmtype'){
                    $accperm->cmtype = 1;
                }else{
                    $accperm->cmtype = 0;
                }   
                                    
                if($request->cmtypec == 'c'){
                    $accperm->cmtypec = 1;
                }else{
                    $accperm->cmtypec = 0;
                }
                if($request->cmtyper == 'r'){
                    $accperm->cmtyper = 1;
                }else{
                    $accperm->cmtyper = 0;
                }
                if($request->cmtypeu == 'u'){
                    $accperm->cmtypeu = 1;
                }else{
                    $accperm->cmtypeu = 0;
                }
                if($request->cmtyped == 'd'){
                    $accperm->cmtyped = 1;
                }else{
                    $accperm->cmtyped = 0;
                }
    
                if($request->cmlcs == 'cmlcs'){
                    $accperm->cmlcs = 1;
                }else{
                    $accperm->cmlcs = 0;
                }      
                if($request->cmlcsc == 'c'){
                    $accperm->cmlcsc = 1;
                }else{
                    $accperm->cmlcsc = 0;
                }
                if($request->cmlcsr == 'r'){
                    $accperm->cmlcsr = 1;
                }else{
                    $accperm->cmlcsr = 0;
                }
                if($request->cmlcsu == 'u'){
                    $accperm->cmlcsu = 1;
                }else{
                    $accperm->cmlcsu = 0;
                }
                if($request->cmlscd == 'd'){
                    $accperm->cmlscd = 1;
                }else{
                    $accperm->cmlscd = 0;
                }
    
                if($request->cmsource == 'cmsource'){
                    $accperm->cmsource = 1;
                }else{
                    $accperm->cmsource = 0;
                }      
                if($request->cmsourcec == 'c'){
                    $accperm->cmsourcec = 1;
                }else{
                    $accperm->cmsourcec = 0;
                }
                if($request->cmsourcer == 'r'){
                    $accperm->cmsourcer = 1;
                }else{
                    $accperm->cmsourcer = 0;
                }
                if($request->cmsourceu == 'u'){
                    $accperm->cmsourceu = 1;
                }else{
                    $accperm->cmsourceu = 0;
                }
                if($request->cmsourced == 'd'){
                    $accperm->cmsourced = 1;
                }else{
                    $accperm->cmsourced = 0;
                }

                if($request->cmconttype == 'cmconttype'){
                    $accperm->cmconttype = 1;
                }else{
                    $accperm->cmconttype = 0;
                }      
                if($request->cmconttypec == 'c'){
                    $accperm->cmconttypec = 1;
                }else{
                    $accperm->cmconttypec = 0;
                }
                if($request->cmconttyper == 'r'){
                    $accperm->cmconttyper = 1;
                }else{
                    $accperm->cmconttyper = 0;
                }
                if($request->cmconttypeu == 'u'){
                    $accperm->cmconttypeu = 1;
                }else{
                    $accperm->cmconttypeu = 0;
                }
                if($request->cmconttyped == 'd'){
                    $accperm->cmconttyped = 1;
                }else{
                    $accperm->cmconttyped = 0;
                }

                if($request->cmind == 'cmind'){
                    $accperm->cmind = 1;
                }else{
                    $accperm->cmind = 0;
                }      
                if($request->cmindc == 'c'){
                    $accperm->cmindc = 1;
                }else{
                    $accperm->cmindc = 0;
                }
                if($request->cmindr == 'r'){
                    $accperm->cmindr = 1;
                }else{
                    $accperm->cmindr = 0;
                }
                if($request->cmindu == 'u'){
                    $accperm->cmindu = 1;
                }else{
                    $accperm->cmindu = 0;
                }
                if($request->cmindd == 'd'){
                    $accperm->cmindd = 1;
                }else{
                    $accperm->cmindd = 0;
                }
                
                if($request->cmbranch == 'cmbranch'){
                    $accperm->cmbranch = 1;
                }else{
                    $accperm->cmbranch = 0;
                }      
                if($request->cmbranchc == 'c'){
                    $accperm->cmbranchc = 1;
                }else{
                    $accperm->cmbranchc = 0;
                }
                if($request->cmbranchr == 'r'){
                    $accperm->cmbranchu = 1;
                }else{
                    $accperm->cmbranchu = 0;
                }
                if($request->cmbranchu == 'u'){
                    $accperm->cmbranchd = 1;
                }else{
                    $accperm->cmbranchd = 0;
                }
                if($request->cmbranchd == 'd'){
                    $accperm->cmbranchr = 1;
                }else{
                    $accperm->cmbranchr = 0;
                }
            
                // Service Master
                if($request->services_master == 1){
                    $accperm->services_master = 1;
                }else{
                    $accperm->services_master = 0;
                }
                // 01
                if($request->smvisap == 'smvisap'){
                    $accperm->smvisap = 1;
                }else{
                    $accperm->smvisap = 0;
                }   
                        
                if($request->smvisapc == 'c'){
                    $accperm->smvisapc = 1;
                }else{
                    $accperm->smvisapc = 0;
                }
                if($request->smvisapr == 'r'){
                    $accperm->smvisapr = 1;
                }else{
                    $accperm->smvisapr = 0;
                }
                if($request->smvisapu == 'u'){
                    $accperm->smvisapu = 1;
                }else{
                    $accperm->smvisapu = 0;
                }
                if($request->smvisapd == 'd'){
                    $accperm->smvisapd = 1;
                }else{
                    $accperm->smvisapd = 0;
                }
                // 02
                if($request->smparty == 'smparty'){
                    $accperm->smparty = 1;
                }else{
                    $accperm->smparty = 0;
                }      
                if($request->smpartyc == 'c'){
                    $accperm->smpartyc = 1;
                }else{
                    $accperm->smpartyc = 0;
                }
                if($request->smpartyr == 'r'){
                    $accperm->smpartyr = 1;
                }else{
                    $accperm->smpartyr = 0;
                }
                if($request->smpartyu == 'u'){
                    $accperm->smpartyu = 1;
                }else{
                    $accperm->smpartyu = 0;
                }
                if($request->smpartyd == 'd'){
                    $accperm->smpartyd = 1;
                }else{
                    $accperm->smpartyd = 0;
                }
                if($request->smpartyst == 'smpartyst'){
                    $accperm->smpartyst = 1;
                }else{
                    $accperm->smpartyst = 0;
                }

                if($request->smpartydv == 'smpartydv'){
                    $accperm->smpartydv = 1;
                }else{
                    $accperm->smpartydv = 0;
                }

                if($request->smpartysc == 'smpartysc'){
                    $accperm->smpartysc = 1;
                }else{
                    $accperm->smpartysc = 0;
                }

                // 03
                if($request->smsector == 'smsector'){
                    $accperm->smsector = 1;
                }else{
                    $accperm->smsector = 0;
                }      
                if($request->smsectorc == 'c'){
                    $accperm->smsectorc = 1;
                }else{
                    $accperm->smsectorc = 0;
                }
                if($request->smsectorr == 'r'){
                    $accperm->smsectorr = 1;
                }else{
                    $accperm->smsectorr = 0;
                }
                if($request->smsectoru == 'u'){
                    $accperm->smsectoru = 1;
                }else{
                    $accperm->smsectoru = 0;
                }
                if($request->smsectord == 'd'){
                    $accperm->smsectord = 1;
                }else{
                    $accperm->smsectord = 0;
                }
                // 04
                if($request->smpartysp == 'smpartysp'){
                    $accperm->smpartysp = 1;
                }else{
                    $accperm->smpartysp = 0;
                }      
                if($request->smpartyspc == 'c'){
                    $accperm->smpartyspc = 1;
                }else{
                    $accperm->smpartyspc = 0;
                }
                if($request->smpartyspr == 'r'){
                    $accperm->smpartyspr = 1;
                }else{
                    $accperm->smpartyspr = 0;
                }
                if($request->smpartyspu == 'u'){
                    $accperm->smpartyspu = 1;
                }else{
                    $accperm->smpartyspu = 0;
                }
                if($request->smpartyspd == 'd'){
                    $accperm->smpartyspd = 1;
                }else{
                    $accperm->smpartyspd = 0;
                }
                // 05
                if($request->smstaff == 'smstaff'){
                    $accperm->smstaff = 1;
                }else{
                    $accperm->smstaff = 0;
                }      
                if($request->smstaffc == 'c'){
                    $accperm->smstaffc = 1;
                }else{
                    $accperm->smstaffc = 0;
                }
                if($request->smstaffr == 'r'){
                    $accperm->smstaffr = 1;
                }else{
                    $accperm->smstaffr = 0;
                }
                if($request->smstaffu == 'u'){
                    $accperm->smstaffu = 1;
                }else{
                    $accperm->smstaffu = 0;
                }
                if($request->smstaffd == 'd'){
                    $accperm->smstaffd = 1;
                }else{
                    $accperm->smstaffd = 0;
                }
                // 06
                if($request->smmofafee == 'smmofafee'){
                    $accperm->smmofafee = 1;
                }else{
                    $accperm->smmofafee = 0;
                }      
                if($request->smmofafeec == 'c'){
                    $accperm->smmofafeec = 1;
                }else{
                    $accperm->smmofafeec = 0;
                }
                if($request->smmofafeer == 'r'){
                    $accperm->smmofafeer = 1;
                }else{
                    $accperm->smmofafeer = 0;
                }
                if($request->smmofafeeu == 'u'){
                    $accperm->smmofafeeu = 1;
                }else{
                    $accperm->smmofafeeu = 0;
                }
                if($request->smmofafeed == 'd'){
                    $accperm->smmofafeed = 1;
                }else{
                    $accperm->smmofafeed = 0;
                }
                // 07
                if($request->smpoi == 'smpoi'){
                    $accperm->smpoi = 1;
                }else{
                    $accperm->smpoi = 0;
                }      
                if($request->smpoic == 'c'){
                    $accperm->smpoic = 1;
                }else{
                    $accperm->smpoic = 0;
                }
                if($request->smpoir == 'r'){
                    $accperm->smpoir = 1;
                }else{
                    $accperm->smpoir = 0;
                }
                if($request->smpoiu == 'u'){
                    $accperm->smpoiu = 1;
                }else{
                    $accperm->smpoiu = 0;
                }
                if($request->smpoid == 'd'){
                    $accperm->smpoid = 1;
                }else{
                    $accperm->smpoid = 0;
                }
                // 08
                if($request->smaddser == 'smaddser'){
                    $accperm->smaddser = 1;
                }else{
                    $accperm->smaddser = 0;
                }      
                if($request->smaddserc == 'c'){
                    $accperm->smaddserc = 1;
                }else{
                    $accperm->smaddserc = 0;
                }
                if($request->smaddserr == 'r'){
                    $accperm->smaddserr = 1;
                }else{
                    $accperm->smaddserr = 0;
                }
                if($request->smaddseru == 'u'){
                    $accperm->smaddseru = 1;
                }else{
                    $accperm->smaddseru = 0;
                }
                if($request->smaddserd == 'd'){
                    $accperm->smaddserd = 1;
                }else{
                    $accperm->smaddserd = 0;
                }
                // 09
                if($request->smrecrt == 'smrecrt'){
                    $accperm->smrecrt = 1;
                }else{
                    $accperm->smrecrt = 0;
                }      
                if($request->smrecrtc == 'c'){
                    $accperm->smrecrtc = 1;
                }else{
                    $accperm->smrecrtc = 0;
                }
                if($request->smrecrtr == 'r'){
                    $accperm->smrecrtr = 1;
                }else{
                    $accperm->smrecrtr = 0;
                }
                if($request->smrecrtu == 'u'){
                    $accperm->smrecrtu = 1;
                }else{
                    $accperm->smrecrtu = 0;
                }
                if($request->smrecrtd == 'd'){
                    $accperm->smrecrtd = 1;
                }else{
                    $accperm->smrecrtd = 0;
                }
                // 10
                if($request->smsertyp == 'smsertyp'){
                    $accperm->smsertyp = 1;
                }else{
                    $accperm->smsertyp = 0;
                }      
                if($request->smsertypc == 'c'){
                    $accperm->smsertypc = 1;
                }else{
                    $accperm->smsertypc = 0;
                }
                if($request->smsertypr == 'r'){
                    $accperm->smsertypr = 1;
                }else{
                    $accperm->smsertypr = 0;
                }
                if($request->smsertypu == 'u'){
                    $accperm->smsertypu = 1;
                }else{
                    $accperm->smsertypu = 0;
                }
                if($request->smsertypd == 'd'){
                    $accperm->smsertypd = 1;
                }else{
                    $accperm->smsertypd = 0;
                }

                if($request->smpartyslo == '1'){
                    $accperm->smpartyslo = 1;
                }else{
                    $accperm->smpartyslo = 0;
                }


                if($request->crmleads == 'crmleads'){
                    $accperm->crmleads = 1;
                }else{
                    $accperm->crmleads = 0;
                }      
                if($request->crmleadsr == 'crmleadsr'){
                    $accperm->crmleadsr = 1;
                }else{
                    $accperm->crmleadsr = 0;
                }
                if($request->crmleadsu == 'crmleadsu'){
                    $accperm->crmleadsu = 1;
                }else{
                    $accperm->crmleadsu = 0;
                }
                if($request->crmleadsd == 'crmleadsd'){
                    $accperm->crmleadsd = 1;
                }else{
                    $accperm->crmleadsd = 0;
                }

                // CRM candidate

                if($request->crm_cand == 'crm_cand'){
                    $accperm->crm_cand = 1;
                }else{
                    $accperm->crm_cand = 0;
                }      
                if($request->crm_cand_cr == 'crm_cand_cr'){
                    $accperm->crm_cand_cr = 1;
                }else{
                    $accperm->crm_cand_cr = 0;
                }
                if($request->crm_cand_r == 'crm_cand_r'){
                    $accperm->crm_cand_r = 1;
                }else{
                    $accperm->crm_cand_r = 0;
                }
                if($request->crm_cand_u == 'crm_cand_u'){
                    $accperm->crm_cand_u = 1;
                }else{
                    $accperm->crm_cand_u = 0;
                }
                if($request->crm_cand_d == 'crm_cand_d'){
                    $accperm->crm_cand_d = 1;
                }else{
                    $accperm->crm_cand_d = 0;
                }

                // Job Seeker
                if($request->crm_jobsk == 'crm_jobsk'){
                    $accperm->crm_jobsk = 1;
                }else{
                    $accperm->crm_jobsk = 0;
                }      
                if($request->crm_jobsk_cr == 'crm_jobsk_cr'){
                    $accperm->crm_jobsk_cr = 1;
                }else{
                    $accperm->crm_jobsk_cr = 0;
                }
                if($request->crm_jobsk_r == 'crm_jobsk_r'){
                    $accperm->crm_jobsk_r = 1;
                }else{
                    $accperm->crm_jobsk_r = 0;
                }
                if($request->crm_jobsk_u == 'crm_jobsk_u'){
                    $accperm->crm_jobsk_u = 1;
                }else{
                    $accperm->crm_jobsk_u = 0;
                }
                if($request->crm_jobsk_d == 'crm_jobsk_d'){
                    $accperm->crm_jobsk_d = 1;
                }else{
                    $accperm->crm_jobsk_d = 0;
                }

                // Job Seeker
                if($request->crm_company == 'crm_company'){
                    $accperm->crm_company = 1;
                }else{
                    $accperm->crm_company = 0;
                }      
                if($request->crm_company_cr == 'crm_company_cr'){
                    $accperm->crm_company_cr = 1;
                }else{
                    $accperm->crm_company_cr = 0;
                }
                if($request->crm_company_r == 'crm_company_r'){
                    $accperm->crm_company_r = 1;
                }else{
                    $accperm->crm_company_r = 0;
                }
                if($request->crm_company_u == 'crm_company_u'){
                    $accperm->crm_company_u = 1;
                }else{
                    $accperm->crm_company_u = 0;
                }
                if($request->crm_company_d == 'crm_company_d'){
                    $accperm->crm_company_d = 1;
                }else{
                    $accperm->crm_company_d = 0;
                }

                if($request->crm_assoc == 'crm_assoc'){
                    $accperm->crm_assoc = 1;
                }else{
                    $accperm->crm_assoc = 0;
                }      
                if($request->crm_assoc_cr == 'crm_assoc_cr'){
                    $accperm->crm_assoc_cr = 1;
                }else{
                    $accperm->crm_assoc_cr = 0;
                }
                if($request->crm_assoc_r == 'crm_assoc_r'){
                    $accperm->crm_assoc_r = 1;
                }else{
                    $accperm->crm_assoc_r = 0;
                }
                if($request->crm_assoc_u == 'crm_assoc_u'){
                    $accperm->crm_assoc_u = 1;
                }else{
                    $accperm->crm_assoc_u = 0;
                }
                if($request->crm_assoc_d == 'crm_assoc_d'){
                    $accperm->crm_assoc_d = 1;
                }else{
                    $accperm->crm_assoc_d = 0;
                }

                if($request->ser_status == 'ser_status'){
                    $accperm->ser_status = 1;
                }else{
                    $accperm->ser_status = 0;
                }

                if($request->ser_submission == 'ser_submission'){
                    $accperm->ser_submission = 1;
                }else{
                    $accperm->ser_submission = 0;
                }


                if($request->ser_courier == 'ser_courier'){
                    $accperm->ser_courier = 1;
                }else{
                    $accperm->ser_courier = 0;
                }

                if($request->ser_courier_del == '1'){
                    $accperm->ser_courier_del = 1;
                }else{
                    $accperm->ser_courier_del = 0;
                }

                if($request->ser_courier_add == '1'){
                    $accperm->ser_courier_add = 1;
                }else{
                    $accperm->ser_courier_add = 0;
                }

                if($request->ser_courier_view == '1'){
                    $accperm->ser_courier_view = 1;
                }else{
                    $accperm->ser_courier_view = 0;
                }

                if($request->ser_courier_edit == '1'){
                    $accperm->ser_courier_edit = 1;
                }else{
                    $accperm->ser_courier_edit = 0;
                }

                // Whatsapp Service Start
                if($request->whatsapp_ser == '1'){
                    $accperm->whatsapp_ser = 1;
                }else{
                    $accperm->whatsapp_ser = 0;
                }

                if($request->whatsAPI == '1'){
                    $accperm->whatsAPI = 1;
                }else{
                    $accperm->whatsAPI = 0;
                }

                if($request->whatsAPIc == 'c'){
                    $accperm->whatsAPIc = 1;
                }else{
                    $accperm->whatsAPIc = 0;
                }

                if($request->whatsAPIr == 'r'){
                    $accperm->whatsAPIr = 1;
                }else{
                    $accperm->whatsAPIr = 0;
                }

                if($request->whatsAPIu == 'u'){
                    $accperm->whatsAPIu = 1;
                }else{
                    $accperm->whatsAPIu = 0;
                }

                if($request->whatsAPId == 'd'){
                    $accperm->whatsAPId = 1;
                }else{
                    $accperm->whatsAPId = 0;
                }

                // Whatsapp Markeing
                if($request->marketing_whatsapp == '1'){
                    $accperm->marketing_whatsapp = 1;
                }else{
                    $accperm->marketing_whatsapp = 0;
                }

                if($request->whcampaignm == 'whcampaignm'){
                    $accperm->whcampaignm = 1;
                }else{
                    $accperm->whcampaignm = 0;
                }

                if($request->whcampaignmc == 'c'){
                    $accperm->whcampaignmc = 1;
                }else{
                    $accperm->whcampaignmc = 0;
                }

                if($request->whcampaignmr == 'r'){
                    $accperm->whcampaignmr = 1;
                }else{
                    $accperm->whcampaignmr = 0;
                }

                if($request->whcampaignmu == 'u'){
                    $accperm->whcampaignmu = 1;
                }else{
                    $accperm->whcampaignmu = 0;
                }

                if($request->whcampaignmd == 'd'){
                    $accperm->whcampaignmd = 1;
                }else{
                    $accperm->whcampaignmd = 0;
                }

                if($request->whcampaignmmul == '1'){
                    $accperm->whcampaignmmul = 1;
                }else{
                    $accperm->whcampaignmmul = 0;
                }

                // template
                if($request->whtemplate == 'whtemplate'){
                    $accperm->whtemplate = 1;
                }else{
                    $accperm->whtemplate = 0;
                }

                if($request->whtemplatec == 'c'){
                    $accperm->whtemplatec = 1;
                }else{
                    $accperm->whtemplatec = 0;
                }

                if($request->whtemplater == 'r'){
                    $accperm->whtemplater = 1;
                }else{
                    $accperm->whtemplater = 0;
                }

                if($request->whtemplateu == 'u'){
                    $accperm->whtemplateu = 1;
                }else{
                    $accperm->whtemplateu = 0;
                }

                if($request->whtemplated == 'd'){
                    $accperm->whtemplated = 1;
                }else{
                    $accperm->whtemplated = 0;
                }

                // Emigration

                if($request->ser_emig == 'ser_emig'){
                    $accperm->ser_emig = 1;
                }else{
                    $accperm->ser_emig = 0;
                }

                if($request->ser_add_emig == 'ser_add_emig'){
                    $accperm->ser_add_emig = 1;
                }else{
                    $accperm->ser_add_emig = 0;
                }

                if($request->ser_view_emig == 'ser_view_emig'){
                    $accperm->ser_view_emig = 1;
                }else{
                    $accperm->ser_view_emig = 0;
                }

                if($request->ser_edit_emig == 'ser_edit_emig'){
                    $accperm->ser_edit_emig = 1;
                }else{
                    $accperm->ser_edit_emig = 0;
                }

                if($request->ser_del_emig == 'ser_del_emig'){
                    $accperm->ser_del_emig = 1;
                }else{
                    $accperm->ser_del_emig = 0;
                }

                if($request->ser_st_emig == 'ser_st_emig'){
                    $accperm->ser_st_emig = 1;
                }else{
                    $accperm->ser_st_emig = 0;
                }

                if($request->service_payment == '1'){
                    $accperm->service_payment = 1;
                }else{
                    $accperm->service_payment = 0;
                }
                if($request->dtxn == 'dtxn'){
                    $accperm->dtxn = 1;
                }else{
                    $accperm->dtxn = 0;
                }
                if($request->dtxnadd == 'dtxnadd'){
                    $accperm->dtxnadd = 1;
                }else{
                    $accperm->dtxnadd = 0;
                }
                if($request->dtxnview == 'dtxnview'){
                    $accperm->dtxnview = 1;
                }else{
                    $accperm->dtxnview = 0;
                }
                if($request->dtxndel == 'dtxndel'){
                    $accperm->dtxndel = 1;
                }else{
                    $accperm->dtxndel = 0;
                }

                if($request->dtxnedit == 'dtxnedit'){
                    $accperm->dtxnedit = 1;
                }else{
                    $accperm->dtxnedit = 0;
                }


            }
        
            $accperm->save();
        }

        Session::flash('success','Staff Permission Assign Successfully!');
        return redirect()->back();
    }

    public function check(Request $request)
    {
        $id  = $request->input('value');
        $user = AccessPermissionModule2::where('user_id','=',$id)->first();
        return response()->json($user);
    }
}
