<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\Carnknown;
use App\Models\Personaliseclass;
use App\Models\Templatecampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Candidate;
use App\Models\Orderrecivepanel;
use App\Models\Basepathstatus;
use App\Models\Customercost;
use App\Models\RequirementInfo;
use App\Models\Profession;
use App\Models\Groupm;
use DataTables;
use Carbon\Carbon;
use App\Models\ExportAllContactHistory;
use App\Models\ExportContactPlusHistory;


class BackEndController extends Controller
{
    public function index(Request $request)
    {

        $current_ip = $request->ip();

        return view('admin.dashboard');
    }

    public function permissionIndex()
    {
        $user = Admin::where('status', 1)->where('user_type','!=',1)->orderBy('name','ASC')->get();
        return view('admin.permission',['users' => $user]);
    }

    public function getPermission(Request $request)
    {
        $post = Adminpermission::where('staff_id','=',$request->staff_id)->first();

        return response()->json($post);
    }

    public function strPermission(Request $request)
    {
        $post = Adminpermission::where('staff_id','=',$request->staff_id)->first();
        if (isset($post)) {

            if($request->full_access == 1){
                $post->full_access = true;
                $post->bookings = true;
                $post->booking_confirm = true;
                $post->add_visa_details = true;
                $post->view_booking = true;
                $post->add_payment = true;
                $post->cancel_booking = true;
                $post->replace_candidate = true;
                $post->employer = true;
                $post->view_employer = true;
                $post->delete_employer = true;
                $post->candidate = true;
                $post->add_candidate = true;
                $post->edit_candidate = true;
                $post->view_candidate = true;
                $post->delete_candidate = true;
                $post->publish_candidate = true;
                $post->testimonial = true;
                $post->add_testimonial = true;
                $post->edit_testimonial = true;
                $post->view_testimonial = true;
                $post->copy_link_testimonial = true;
                $post->delete_testimonial = true;
                $post->approval_testimonial = true;
                $post->payment_testimonial = true;
                $post->video_received_testimonial = true;
                $post->social_media_testimonial = true;
                $post->google_review = true;
                $post->add_google_review = true;
                $post->edit_google_review = true;
                $post->view_google_review = true;
                $post->delete_google_review = true;
                $post->approval_google_review = true;
                $post->payment_google_review = true;
                $post->cv_b2b = true;
                $post->cv_b2c = true;
                $post->client = true;
                $post->view_client = true;
                $post->delete_client = true;
                $post->partner = true;
                $post->add_partner = true;
                $post->edit_partner = true;
                $post->view_partner = true;
                $post->delete_partner = true;
                $post->dynamic = true;
                $post->settings = true;
                $post->staff = true;
                $post->add_staff = true;
                $post->edit_staff = true;
                $post->delete_staff = true;
                $post->view_staff = true;
                $post->branch = true;
                $post->add_branch = true;
                $post->edit_branch = true;
                $post->view_branch = true;
                $post->delete_branch = true;
                $post->profession = true;
                $post->add_profession = true;
                $post->edit_profession = true;
                $post->delete_profession = true;
                $post->view_profession = true;
                $post->placeofissue = true;
                $post->add_placeofissue = true;
                $post->edit_placeofissue = true;
                $post->delete_placeofissue = true;
                $post->view_placeofissue = true;
                $post->country = true;
                $post->add_country = true;
                $post->edit_country = true;
                $post->view_country = true;
                $post->delete_country = true;
                $post->region = true;
                $post->add_region = true;
                $post->edit_region = true;
                $post->delete_region = true;
                $post->view_region = true;
                $post->city = true;
                $post->add_city = true;
                $post->edit_city = true;
                $post->view_city = true;
                $post->delete_city = true;
                $post->websiteconfig = true;
                $post->mailsetup = true;
                $post->add_mailsetup = true;
                $post->edit_mailsetup = true;
                $post->delete_mailsetup = true;
                $post->view_mailsetup = true;
                $post->carknown = true;
                $post->add_car_known = true;
                $post->edit_car_known = true;
                $post->delete_car_known = true;
                $post->view_car_known = true;
                $post->personalise_class = true;
                $post->add_personalise_class = true;
                $post->edit_personalise_class = true;
                $post->delete_personalise_class = true;
                $post->view_personalise_class = true;
                $post->storage_usage_setting = true;
                $post->db_backup_setting = true;
                $post->template = true;
                $post->add_template = true;
                $post->edit_template = true;
                $post->delete_template = true;
                $post->view_template = true;
                $post->associate = true;
                $post->add_associate = true;
                $post->edit_associate = true;
                $post->view_associate = true;
                $post->delete_associate = true;
                $post->publish_associate = true;
                $post->orderStatus = true;
                $post->add_orderStatus = true;
                $post->edit_orderStatus = true;
                $post->delete_orderStatus = true;
                $post->view_orderStatus = true;
                $post->upload_docs = true;
                $post->add_service_charge = true;
                $post->edit_service_charge = true;
                $post->delete_service_charge = true;
                $post->updt_status_service_charge = true;
                $post->delete_cand_payment = true;
                $post->edit_cand_payment = true;
                $post->candidate_status = true;
                $post->candidate_reset_status = true;
                $post->cand_delete_file = true;
                $post->cand_edit_associate = true;
                $post->cand_edit_careoff = true;
                $post->activedeactive_associate = true;
                $post->contactp = true;
                $post->add_contactp = true;
                $post->edit_contactp = true;
                $post->view_contactp = true;
                $post->delete_contactp = true;
                $post->lead_owner_transfer = true;
                $post->careoff_transfer = true;
                $post->whatsapp_plus = true;

                $post->sms_campaign_module = true;
                $post->sms_api = true;
                $post->add_sms_api = true;
                $post->edit_sms_api = true;
                $post->delete_sms_api = true;
                $post->change_status_sms_api = true;
                $post->assign_sms_api = true;
                $post->sms_template = true;
                $post->add_sms_template = true;
                $post->edit_sms_template = true;
                $post->delete_sms_template = true;
                $post->change_status_sms_template = true;
                $post->sms_campaign = true;
                $post->add_sms_campaign = true;
                $post->view_sms_campaign = true;
                $post->delete_sms_campaign = true;

                $post->image_host = true;
                $post->expworklocation = true;
                $post->add_expworklocation = true;
                $post->edit_expworklocation = true;
                $post->view_expworklocation = true;
                $post->delete_expworklocation = true;
                $post->businesstype = true;
                $post->add_businesstype = true;
                $post->edit_businesstype = true;
                $post->delete_businesstype = true;
                $post->view_businesstype = true;
                $post->groupcp = true;
                $post->add_groupcp = true;
                $post->edit_groupcp = true;
                $post->view_groupcp = true;
                $post->delete_groupcp = true;

                $post->meta_whatsapp_template = true;
                $post->add_meta_whatsapp_template = true;
                $post->edit_meta_whatsapp_template = true;
                $post->delete_meta_whatsapp_template = true;
                $post->status_meta_whatsapp_template = true;
                $post->public_meta_whatsapp_template = true;
                $post->meta_whatsapp_campaign = true;
                $post->meta_whatsapp_campaign_view = true;
                $post->whatsapp_nromal = true;
                $post->whatsapp_template = true;
                $post->add_whatsapp_template = true;
                $post->edit_whatsapp_template = true;
                $post->view_whatsapp_template = true;
                $post->delete_whatsapp_template = true;
                $post->status_whatsapp_template = true;
                $post->public_whatsapp_template = true;
                $post->whatsapp_campaign = true;
                $post->whatsapp_campaign_view = true;
                $post->contact_bulk_whatsapp_send = true;
                $post->whatsapp_campaign_add = true;
                $post->meta_whatsapp_campaign_add = true;
                $post->view_meta_whatsapp_template = true;


                $post->access_setting = true;
                $post->access_allowed_ip = true;
                $post->access_allowed_ip_revoke = true;
                $post->access_allowed_ip_approved_by = true;
                $post->access_allowed_ip_delete = true;

                $post->todo_setting = true;
                $post->todo_label = true;
                $post->todo_label_add = true;
                $post->todo_label_view = true;
                $post->todo_label_edit = true;
                $post->todo_label_delete = true;
                $post->department = true;
                $post->department_add = true;
                $post->department_view = true;
                $post->department_edit = true;
                $post->department_delete = true;

                $post->todo = true;
                $post->todo_add = true;
                $post->todo_view = true;
                $post->todo_edit = true;
                $post->todo_delete = true;
                $post->todo_achieved = true;

                $post->bulk_todo_update_status = true;
                $post->bulk_todo_update_priority = true;
                $post->bulk_todo_update_assignto = true;
                $post->bulk_todo_delete = true;

                $post->deal_pipeline = true;
                $post->deal_add = true;
                $post->deal_view = true;
                $post->deal_edit = true;
                $post->deal_delete = true;
                $post->deal_update_stage = true;
                $post->deal_update_recruit_status = true;
                $post->deal_delete_files = true;


                $post->employerplus = true;
                $post->employerplus_add = true;
                $post->employerplus_view = true;
                $post->employerplus_edit = true;
                $post->employerplus_delete = true;



                $post->permission_setting = true;
                $post->contact_plus_setting = true;

                $post->finance = true;
                $post->candidate_finance = true;
                $post->transaction_finance = true;
                $post->transaction_client = true;
                $post->finance_sale_invoices = true;
                $post->finance_sale_invoices_create = true;
                $post->finance_sale_invoices_edit = true;
                $post->finance_sale_invoices_view = true;
                $post->finance_sale_invoices_delete = true;
                $post->finance_payment = true;
                $post->finance_payment_add = true;
                $post->finance_payment_edit = true;
                $post->finance_payment_delete = true;

                $post->hr_management = true;
                $post->hr_dashboard = true;
                $post->hr_attendance = true;
                $post->hr_attendance_view_all = true;
                $post->hr_payroll = true;
                $post->hr_settings = true;

                $post->fund_advance = true;
                $post->fund_advance_create = true;
                $post->fund_advance_edit = true;
                $post->fund_advance_delete = true;
                $post->fund_advance_settlement_create = true;
                $post->fund_advance_settlement_view = true;
                $post->fund_advance_adjustment_create = true;
                $post->fund_advance_ledger_view = true;
                $post->fund_advance_reports_view = true;
                $post->fund_advance_export = true;

                $post->industries = true;
                $post->add_industries = true;
                $post->edit_industries = true;
                $post->view_industries = true;
                $post->delete_industries = true;

                $post->leads = true;
                $post->leads_view = true;
                $post->leads_delete = true;
                $post->leads_assignto = true;
                $post->leads_bulk_assignto = true;
                $post->leads_bulk_delete = true;

                $post->setup = true;
                $post->whatsapp_setup = true;
                $post->whatsapp_api = true;
                $post->add_whatsapp_api = true;
                $post->edit_whatsapp_api = true;
                $post->view_whatsapp_api = true;
                $post->delete_whatsapp_api = true;
                $post->whatsapp_api_test = true;
                $post->whatsapp_meta_api = true;
                $post->add_whatsapp_meta_api = true;
                $post->edit_whatsapp_meta_api = true;
                $post->view_whatsapp_meta_api = true;
                $post->delete_whatsapp_meta_api = true;
                $post->add_whatsapp_api_for_user = true;

                $post->whatsapp_url = true;
                $post->add_whatsapp_url = true;
                $post->edit_whatsapp_url = true;
                $post->view_whatsapp_url = true;
                $post->delete_whatsapp_url = true;
                
                $post->allcontact = true;
                $post->allcontact_add = true;
                $post->allcontact_edit = true;
                $post->allcontact_view = true;
                $post->allcontact_bulk_transfer_group = true;
                $post->allcontact_bulk_transfer_careoff = true;
                // $post->allcontact_bulk_transfer_leadowner = true;
                $post->allcontact_bulk_send_whatsapp = true;
                $post->allcontact_delete = true;

                $post->expense = true;
                $post->add_expense = true;
                $post->edit_expense = true;
                $post->view_expense = true;
                $post->delete_expense = true;

                $post->file_manager = true;
                $post->add_file_manager = true;
                $post->upload_file_manager = true;
                $post->download_file_manager = true;
                $post->preview_file_manager = true;
                $post->rename_file_manager = true;
                $post->move_file_manager = true;
                $post->delete_file_manager = true;
                $post->manage_all_file_manager = true;

                $post->verified_notverified_associate = true;
                $post->primary_no_val_associate = true;
                $post->secondary_no_val_associate = true;
                $post->email_associate = true;
                $post->meta_whatsapp_campaign_create_user = true;

                $post->meta_automation = true;
                $post->add_meta_automation = true;
                $post->view_meta_automation = true;
                $post->edit_meta_automation = true;
                $post->delete_meta_automation = true;

                $post->cand_add_edit = true;
                $post->cand_sourcing_date = true;

                $post->allcontact_bulk_delete = true;
                $post->allcontact_bulk_country_update = true;
                $post->allcontact_bulk_country_code_update = true;

                $post->leads_candidate_view = true;
                $post->leads_employer_view = true;

                $post->allcontact_company_column = true;
                $post->leads_company_column = true;

                $post->update_status_achieved = true;

                $post->chat = true;

                $post->save();
            }else{
                $post->full_access = false;
                if($request->chat == 1){
                    $post->chat = true;
                }else{
                    $post->chat = false;
                }
                if($request->bookings == 1){
                    $post->bookings = true;
                }else{
                    $post->bookings = false;
                }
                if($request->booking_confirm == 1){
                    $post->booking_confirm = true;
                }else{
                    $post->booking_confirm = false;
                }
                if($request->add_visa_details == 1){
                    $post->add_visa_details = true;
                }else{
                    $post->add_visa_details = false;
                }
                if($request->view_booking == 1){
                    $post->view_booking = true;
                }else{
                    $post->view_booking = false;
                }
                if($request->add_payment == 1){
                    $post->add_payment = true;
                }else{
                    $post->add_payment = false;
                }
                if($request->cancel_booking == 1){
                    $post->cancel_booking = true;
                }else{
                    $post->cancel_booking = false;
                }
                if($request->replace_candidate == 1){
                    $post->replace_candidate = true;
                }else{
                    $post->replace_candidate = false;
                }
                if($request->employer == 1){
                    $post->employer = true;
                }else{
                    $post->employer = false;
                }
                if($request->view_employer == 1){
                    $post->view_employer = true;
                }else{
                    $post->view_employer = false;
                }
                if($request->delete_employer == 1){
                    $post->delete_employer = true;
                }else{
                    $post->delete_employer = false;
                }
                if($request->candidate == 1){
                    $post->candidate = true;
                }else{
                    $post->candidate = false;
                }
                if($request->add_candidate == 1){
                    $post->add_candidate = true;
                }else{
                    $post->add_candidate = false;
                }
                if($request->edit_candidate == 1){
                    $post->edit_candidate = true;
                }else{
                    $post->edit_candidate = false;
                }
                if($request->view_candidate == 1){
                    $post->view_candidate = true;
                }else{
                    $post->view_candidate = false;
                }
                if($request->delete_candidate == 1){
                    $post->delete_candidate = true;
                }else{
                    $post->delete_candidate = false;
                }
                if($request->publish_candidate == 1){
                    $post->publish_candidate = true;
                }else{
                    $post->publish_candidate = false;
                }
                if($request->testimonial == 1){
                    $post->testimonial = true;
                }else{
                    $post->testimonial = false;
                }
                if($request->add_testimonial == 1){
                    $post->add_testimonial = true;
                }else{
                    $post->add_testimonial = false;
                }
                if($request->edit_testimonial == 1){
                    $post->edit_testimonial = true;
                }else{
                    $post->edit_testimonial = false;
                }
                if($request->view_testimonial == 1){
                    $post->view_testimonial = true;
                }else{
                    $post->view_testimonial = false;
                }
                if($request->copy_link_testimonial == 1){
                    $post->copy_link_testimonial = true;
                }else{
                    $post->copy_link_testimonial = false;
                }
                if($request->delete_testimonial == 1){
                    $post->delete_testimonial = true;
                }else{
                    $post->delete_testimonial = false;
                }
                if($request->approval_testimonial == 1){
                    $post->approval_testimonial = true;
                }else{
                    $post->approval_testimonial = false;
                }
                if($request->payment_testimonial == 1){
                    $post->payment_testimonial = true;
                }else{
                    $post->payment_testimonial = false;
                }
                if($request->video_received_testimonial == 1){
                    $post->video_received_testimonial = true;
                }else{
                    $post->video_received_testimonial = false;
                }
                if($request->social_media_testimonial == 1){
                    $post->social_media_testimonial = true;
                }else{
                    $post->social_media_testimonial = false;
                }
                if($request->google_review == 1){
                    $post->google_review = true;
                }else{
                    $post->google_review = false;
                }
                if($request->add_google_review == 1){
                    $post->add_google_review = true;
                }else{
                    $post->add_google_review = false;
                }
                if($request->edit_google_review == 1){
                    $post->edit_google_review = true;
                }else{
                    $post->edit_google_review = false;
                }
                if($request->view_google_review == 1){
                    $post->view_google_review = true;
                }else{
                    $post->view_google_review = false;
                }
                if($request->delete_google_review == 1){
                    $post->delete_google_review = true;
                }else{
                    $post->delete_google_review = false;
                }
                if($request->approval_google_review == 1){
                    $post->approval_google_review = true;
                }else{
                    $post->approval_google_review = false;
                }
                if($request->payment_google_review == 1){
                    $post->payment_google_review = true;
                }else{
                    $post->payment_google_review = false;
                }
                if($request->cv_b2b == 1){
                    $post->cv_b2b = true;
                }else{
                    $post->cv_b2b = false;
                }
                if($request->cv_b2c == 1){
                    $post->cv_b2c = true;
                }else{
                    $post->cv_b2c = false;
                }
                if($request->client == 1){
                    $post->client = true;
                }else{
                    $post->client = false;
                }
                if($request->view_client == 1){
                    $post->view_client = true;
                }else{
                    $post->view_client = false;
                }
                if($request->delete_client == 1){
                    $post->delete_client = true;
                }else{
                    $post->delete_client = false;
                }
                if($request->partner == 1){
                    $post->partner = true;
                }else{
                    $post->partner = false;
                }
                if($request->add_partner == 1){
                    $post->add_partner = true;
                }else{
                    $post->add_partner = false;
                }
                if($request->edit_partner == 1){
                    $post->edit_partner = true;
                }else{
                    $post->edit_partner = false;
                }
                if($request->view_partner == 1){
                    $post->view_partner = true;
                }else{
                    $post->view_partner = false;
                }
                if($request->delete_partner == 1){
                    $post->delete_partner = true;
                }else{
                    $post->delete_partner = false;
                }
                if($request->settings == 1){
                    $post->settings = true;
                }else{
                    $post->settings = false;
                }
                if($request->dynamic == 1){
                    $post->dynamic = true;
                }else{
                    $post->dynamic = false;
                }
                if($request->staff == 1){
                    $post->staff = true;
                }else{
                    $post->staff = false;
                }
                if($request->add_staff == 1){
                    $post->add_staff = true;
                }else{
                    $post->add_staff = false;
                }
                if($request->edit_staff == 1){
                    $post->edit_staff = true;
                }else{
                    $post->edit_staff = false;
                }
                if($request->delete_staff == 1){
                    $post->delete_staff = true;
                }else{
                    $post->delete_staff = false;
                }
                if($request->view_staff == 1){
                    $post->view_staff = true;
                }else{
                    $post->view_staff = false;
                }
                if($request->branch == 1){
                    $post->branch = true;
                }else{
                    $post->branch = false;
                }
                if($request->add_branch == 1){
                    $post->add_branch = true;
                }else{
                    $post->add_branch = false;
                }
                if($request->edit_branch == 1){
                    $post->edit_branch = true;
                }else{
                    $post->edit_branch = false;
                }
                if($request->delete_branch == 1){
                    $post->delete_branch = true;
                }else{
                    $post->delete_branch = false;
                }
                if($request->view_branch == 1){
                    $post->view_branch = true;
                }else{
                    $post->view_branch = false;
                }
                if($request->profession == 1){
                    $post->profession = true;
                }else{
                    $post->profession = false;
                }
                if($request->add_profession == 1){
                    $post->add_profession = true;
                }else{
                    $post->add_profession = false;
                }
                if($request->edit_profession == 1){
                    $post->edit_profession = true;
                }else{
                    $post->edit_profession = false;
                }
                if($request->delete_profession == 1){
                    $post->delete_profession = true;
                }else{
                    $post->delete_profession = false;
                }
                if($request->view_profession == 1){
                    $post->view_profession = true;
                }else{
                    $post->view_profession = false;
                }
                if($request->placeofissue == 1){
                    $post->placeofissue = true;
                }else{
                    $post->placeofissue = false;
                }
                if($request->add_placeofissue == 1){
                    $post->add_placeofissue = true;
                }else{
                    $post->add_placeofissue = false;
                }
                if($request->edit_placeofissue == 1){
                    $post->edit_placeofissue = true;
                }else{
                    $post->edit_placeofissue = false;
                }
                if($request->delete_placeofissue == 1){
                    $post->delete_placeofissue = true;
                }else{
                    $post->delete_placeofissue = false;
                }
                if($request->view_placeofissue == 1){
                    $post->view_placeofissue = true;
                }else{
                    $post->view_placeofissue = false;
                }
                if($request->country == 1){
                    $post->country = true;
                }else{
                    $post->country = false;
                }
                if($request->add_country == 1){
                    $post->add_country = true;
                }else{
                    $post->add_country = false;
                }
                if($request->edit_country == 1){
                    $post->edit_country = true;
                }else{
                    $post->edit_country = false;
                }
                if($request->view_country == 1){
                    $post->view_country = true;
                }else{
                    $post->view_country = false;
                }
                if($request->delete_country == 1){
                    $post->delete_country = true;
                }else{
                    $post->delete_country = false;
                }
                if($request->region == 1){
                    $post->region = true;
                }else{
                    $post->region = false;
                }
                if($request->add_region == 1){
                    $post->add_region = true;
                }else{
                    $post->add_region = false;
                }
                if($request->edit_region == 1){
                    $post->edit_region = true;
                }else{
                    $post->edit_region = false;
                }
                if($request->delete_region == 1){
                    $post->delete_region = true;
                }else{
                    $post->delete_region = false;
                }
                if($request->view_region == 1){
                    $post->view_region = true;
                }else{
                    $post->view_region = false;
                }
                if($request->city == 1){
                    $post->city = true;
                }else{
                    $post->city = false;
                }
                if($request->add_city == 1){
                    $post->add_city = true;
                }else{
                    $post->add_city = false;
                }
                if($request->edit_city == 1){
                    $post->edit_city = true;
                }else{
                    $post->edit_city = false;
                }
                if($request->view_city == 1){
                    $post->view_city = true;
                }else{
                    $post->view_city = false;
                }
                if($request->delete_city == 1){
                    $post->delete_city = true;
                }else{
                    $post->delete_city = false;
                }
                if($request->websiteconfig == 1){
                    $post->websiteconfig = true;
                }else{
                    $post->websiteconfig = false;
                }
                if($request->mailsetup == 1){
                    $post->mailsetup = true;
                }else{
                    $post->mailsetup = false;
                }
                if($request->add_mailsetup == 1){
                    $post->add_mailsetup = true;
                }else{
                    $post->add_mailsetup = false;
                }
                if($request->edit_mailsetup == 1){
                    $post->edit_mailsetup = true;
                }else{
                    $post->edit_mailsetup = false;
                }
                if($request->delete_mailsetup == 1){
                    $post->delete_mailsetup = true;
                }else{
                    $post->delete_mailsetup = false;
                }
                if($request->view_mailsetup == 1){
                    $post->view_mailsetup = true;
                }else{
                    $post->view_mailsetup = false;
                }
                if($request->carknown == 1){
                    $post->carknown = true;
                }else{
                    $post->carknown = false;
                }
                if($request->add_car_known == 1){
                    $post->add_car_known = true;
                }else{
                    $post->add_car_known = false;
                }
                if($request->edit_car_known == 1){
                    $post->edit_car_known = true;
                }else{
                    $post->edit_car_known = false;
                }
                if($request->delete_car_known == 1){
                    $post->delete_car_known = true;
                }else{
                    $post->delete_car_known = false;
                }
                if($request->view_car_known == 1){
                    $post->view_car_known = true;
                }else{
                    $post->view_car_known = false;
                }
                if($request->personalise_class == 1){
                    $post->personalise_class = true;
                }else{
                    $post->personalise_class = false;
                }
                if($request->add_personalise_class == 1){
                    $post->add_personalise_class = true;
                }else{
                    $post->add_personalise_class = false;
                }
                if($request->edit_personalise_class == 1){
                    $post->edit_personalise_class = true;
                }else{
                    $post->edit_personalise_class = false;
                }
                if($request->delete_personalise_class == 1){
                    $post->delete_personalise_class = true;
                }else{
                    $post->delete_personalise_class = false;
                }
                if($request->view_personalise_class == 1){
                    $post->view_personalise_class = true;
                }else{
                    $post->view_personalise_class = false;
                }
                if($request->storage_usage_setting == 1){
                    $post->storage_usage_setting = true;
                }else{
                    $post->storage_usage_setting = false;
                }
                if($request->db_backup_setting == 1){
                    $post->db_backup_setting = true;
                }else{
                    $post->db_backup_setting = false;
                }
                if($request->template == 1){
                    $post->template = true;
                }else{
                    $post->template = false;
                }
                if($request->add_template == 1){
                    $post->add_template = true;
                }else{
                    $post->add_template = false;
                }
                if($request->edit_template == 1){
                    $post->edit_template = true;
                }else{
                    $post->edit_template = false;
                }
                if($request->delete_template == 1){
                    $post->delete_template = true;
                }else{
                    $post->delete_template = false;
                }
                if($request->view_template == 1){
                    $post->view_template = true;
                }else{
                    $post->view_template = false;
                }

                if($request->associate == 1){
                    $post->associate = true;
                }else{
                    $post->associate = false;
                }
                if($request->add_associate == 1){
                    $post->add_associate = true;
                }else{
                    $post->add_associate = false;
                }
                if($request->edit_associate == 1){
                    $post->edit_associate = true;
                }else{
                    $post->edit_associate = false;
                }
                if($request->view_associate == 1){
                    $post->view_associate = true;
                }else{
                    $post->view_associate = false;
                }
                if($request->delete_associate == 1){
                    $post->delete_associate = true;
                }else{
                    $post->delete_associate = false;
                }
                if($request->publish_associate == 1){
                    $post->publish_associate = true;
                }else{
                    $post->publish_associate = false;
                }

                if($request->orderStatus == 1){
                    $post->orderStatus = true;
                }else{
                    $post->orderStatus = false;
                }
                if($request->add_orderStatus == 1){
                    $post->add_orderStatus = true;
                }else{
                    $post->add_orderStatus = false;
                }
                if($request->edit_orderStatus == 1){
                    $post->edit_orderStatus = true;
                }else{
                    $post->edit_orderStatus = false;
                }
                if($request->delete_orderStatus == 1){
                    $post->delete_orderStatus = true;
                }else{
                    $post->delete_orderStatus = false;
                }
                if($request->view_orderStatus == 1){
                    $post->view_orderStatus = true;
                }else{
                    $post->view_orderStatus = false;
                }
                if($request->upload_docs == 1){
                    $post->upload_docs = true;
                }else{
                    $post->upload_docs = false;
                }

                if($request->add_service_charge == 1){
                    $post->add_service_charge = true;
                }else{
                    $post->add_service_charge = false;
                }

                if($request->edit_service_charge == 1){
                    $post->edit_service_charge = true;
                }else{
                    $post->edit_service_charge = false;
                }

                if($request->updt_status_service_charge == 1){
                    $post->updt_status_service_charge = true;
                }else{
                    $post->updt_status_service_charge = false;
                }

                if($request->delete_service_charge == 1){
                    $post->delete_service_charge = true;
                }else{
                    $post->delete_service_charge = false;
                }

                if($request->candidate_status == 1){
                    $post->candidate_status = true;
                }else{
                    $post->candidate_status = false;
                }

                if($request->candidate_reset_status == 1){
                    $post->candidate_reset_status = true;
                }else{
                    $post->candidate_reset_status = false;
                }
                
                if($request->cand_delete_file == 1){
                    $post->cand_delete_file = true;
                }else{
                    $post->cand_delete_file = false;
                }
                if($request->delete_cand_payment == 1){
                    $post->delete_cand_payment = true;
                }else{
                    $post->delete_cand_payment = false;
                }

                if($request->edit_cand_payment == 1){
                    $post->edit_cand_payment = true;
                }else{
                    $post->edit_cand_payment = false;
                }

                if($request->cand_edit_careoff == 1){
                    $post->cand_edit_careoff = true;
                }else{
                    $post->cand_edit_careoff = false;
                }

                if($request->cand_edit_associate == 1){
                    $post->cand_edit_associate = true;
                }else{
                    $post->cand_edit_associate = false;
                }

                if($request->activedeactive_associate == 1){
                    $post->activedeactive_associate = true;
                }else{
                    $post->activedeactive_associate = false;
                }

                if($request->contactp == 1){
                    $post->contactp = true;
                }else{
                    $post->contactp = false;
                }

                if($request->add_contactp == 1){
                    $post->add_contactp = true;
                }else{
                    $post->add_contactp = false;
                }

                if($request->edit_contactp == 1){
                    $post->edit_contactp = true;
                }else{
                    $post->edit_contactp = false;
                }

                if($request->view_contactp == 1){
                    $post->view_contactp = true;
                }else{
                    $post->view_contactp = false;
                }

                if($request->delete_contactp == 1){
                    $post->delete_contactp = true;
                }else{
                    $post->delete_contactp = false;
                }

                if($request->careoff_transfer == 1){
                    $post->careoff_transfer = true;
                }else{
                    $post->careoff_transfer = false;
                }

                if($request->lead_owner_transfer == 1){
                    $post->lead_owner_transfer = true;
                }else{
                    $post->lead_owner_transfer = false;
                }

                if($request->whatsapp_plus == 1){
                    $post->whatsapp_plus = true;
                }else{
                    $post->whatsapp_plus = false;
                }

                if($request->sms_campaign_module == 1){
                    $post->sms_campaign_module = true;
                }else{
                    $post->sms_campaign_module = false;
                }

                if($request->sms_api == 1){
                    $post->sms_api = true;
                }else{
                    $post->sms_api = false;
                }

                if($request->add_sms_api == 1){
                    $post->add_sms_api = true;
                }else{
                    $post->add_sms_api = false;
                }

                if($request->edit_sms_api == 1){
                    $post->edit_sms_api = true;
                }else{
                    $post->edit_sms_api = false;
                }

                if($request->delete_sms_api == 1){
                    $post->delete_sms_api = true;
                }else{
                    $post->delete_sms_api = false;
                }

                if($request->change_status_sms_api == 1){
                    $post->change_status_sms_api = true;
                }else{
                    $post->change_status_sms_api = false;
                }

                if($request->assign_sms_api == 1){
                    $post->assign_sms_api = true;
                }else{
                    $post->assign_sms_api = false;
                }

                if($request->sms_template == 1){
                    $post->sms_template = true;
                }else{
                    $post->sms_template = false;
                }

                if($request->add_sms_template == 1){
                    $post->add_sms_template = true;
                }else{
                    $post->add_sms_template = false;
                }

                if($request->edit_sms_template == 1){
                    $post->edit_sms_template = true;
                }else{
                    $post->edit_sms_template = false;
                }

                if($request->delete_sms_template == 1){
                    $post->delete_sms_template = true;
                }else{
                    $post->delete_sms_template = false;
                }

                if($request->change_status_sms_template == 1){
                    $post->change_status_sms_template = true;
                }else{
                    $post->change_status_sms_template = false;
                }

                if($request->sms_campaign == 1){
                    $post->sms_campaign = true;
                }else{
                    $post->sms_campaign = false;
                }

                if($request->add_sms_campaign == 1){
                    $post->add_sms_campaign = true;
                }else{
                    $post->add_sms_campaign = false;
                }

                if($request->view_sms_campaign == 1){
                    $post->view_sms_campaign = true;
                }else{
                    $post->view_sms_campaign = false;
                }

                if($request->delete_sms_campaign == 1){
                    $post->delete_sms_campaign = true;
                }else{
                    $post->delete_sms_campaign = false;
                }

                if($request->image_host == 1){
                    $post->image_host = true;
                }else{
                    $post->image_host = false;
                }

                if($request->expworklocation == 1){
                    $post->expworklocation = true;
                }else{
                    $post->expworklocation = false;
                }

                if($request->add_expworklocation == 1){
                    $post->add_expworklocation = true;
                }else{
                    $post->add_expworklocation = false;
                }

                if($request->edit_expworklocation == 1){
                    $post->edit_expworklocation = true;
                }else{
                    $post->edit_expworklocation = false;
                }

                if($request->view_expworklocation == 1){
                    $post->view_expworklocation = true;
                }else{
                    $post->view_expworklocation = false;
                }

                if($request->delete_expworklocation == 1){
                    $post->delete_expworklocation = true;
                }else{
                    $post->delete_expworklocation = false;
                }

                if($request->businesstype == 1){
                    $post->businesstype = true;
                }else{
                    $post->businesstype = false;
                }

                if($request->add_businesstype == 1){
                    $post->add_businesstype = true;
                }else{
                    $post->add_businesstype = false;
                }

                if($request->edit_businesstype == 1){
                    $post->edit_businesstype = true;
                }else{
                    $post->edit_businesstype = false;
                }

                if($request->view_businesstype == 1){
                    $post->view_businesstype = true;
                }else{
                    $post->view_businesstype = false;
                }

                if($request->delete_businesstype == 1){
                    $post->delete_businesstype = true;
                }else{
                    $post->delete_businesstype = false;
                }

                if($request->groupcp == 1){
                    $post->groupcp = true;
                }else{
                    $post->groupcp = false;
                }

                if($request->add_groupcp == 1){
                    $post->add_groupcp = true;
                }else{
                    $post->add_groupcp = false;
                }

                if($request->edit_groupcp == 1){
                    $post->edit_groupcp = true;
                }else{
                    $post->edit_groupcp = false;
                }

                if($request->view_groupcp == 1){
                    $post->view_groupcp = true;
                }else{
                    $post->view_groupcp = false;
                }

                if($request->delete_groupcp == 1){
                    $post->delete_groupcp = true;
                }else{
                    $post->delete_groupcp = false;
                }

                if($request->meta_whatsapp_template == 1){
                    $post->meta_whatsapp_template = true;
                }else{
                    $post->meta_whatsapp_template = false;
                }

                if($request->add_meta_whatsapp_template == 1){
                    $post->add_meta_whatsapp_template = true;
                }else{
                    $post->add_meta_whatsapp_template = false;
                }

                if($request->edit_meta_whatsapp_template == 1){
                    $post->edit_meta_whatsapp_template = true;
                }else{
                    $post->edit_meta_whatsapp_template = false;
                }

                if($request->delete_meta_whatsapp_template == 1){
                    $post->delete_meta_whatsapp_template = true;
                }else{
                    $post->delete_meta_whatsapp_template = false;
                }

                if($request->status_meta_whatsapp_template == 1){
                    $post->status_meta_whatsapp_template = true;
                }else{
                    $post->status_meta_whatsapp_template = false;
                }

                if($request->public_meta_whatsapp_template == 1){
                    $post->public_meta_whatsapp_template = true;
                }else{
                    $post->public_meta_whatsapp_template = false;
                }

                if($request->meta_whatsapp_campaign == 1){
                    $post->meta_whatsapp_campaign = true;
                }else{
                    $post->meta_whatsapp_campaign = false;
                }

                if($request->meta_whatsapp_campaign_view == 1){
                    $post->meta_whatsapp_campaign_view = true;
                }else{
                    $post->meta_whatsapp_campaign_view = false;
                }

                if($request->whatsapp_nromal == 1){
                    $post->whatsapp_nromal = true;
                }else{
                    $post->whatsapp_nromal = false;
                }

                if($request->whatsapp_template == 1){
                    $post->whatsapp_template = true;
                }else{
                    $post->whatsapp_template = false;
                }

                if($request->add_whatsapp_template == 1){
                    $post->add_whatsapp_template = true;
                }else{
                    $post->add_whatsapp_template = false;
                }

                if($request->edit_whatsapp_template == 1){
                    $post->edit_whatsapp_template = true;
                }else{
                    $post->edit_whatsapp_template = false;
                }

                if($request->view_whatsapp_template == 1){
                    $post->view_whatsapp_template = true;
                }else{
                    $post->view_whatsapp_template = false;
                }

                if($request->delete_whatsapp_template == 1){
                    $post->delete_whatsapp_template = true;
                }else{
                    $post->delete_whatsapp_template = false;
                }

                if($request->status_whatsapp_template == 1){
                    $post->status_whatsapp_template = true;
                }else{
                    $post->status_whatsapp_template = false;
                }

                if($request->public_whatsapp_template == 1){
                    $post->public_whatsapp_template = true;
                }else{
                    $post->public_whatsapp_template = false;
                }

                if($request->whatsapp_campaign == 1){
                    $post->whatsapp_campaign = true;
                }else{
                    $post->whatsapp_campaign = false;
                }

                if($request->whatsapp_campaign_view == 1){
                    $post->whatsapp_campaign_view = true;
                }else{
                    $post->whatsapp_campaign_view = false;
                }

                if($request->contact_bulk_whatsapp_send == 1){
                    $post->contact_bulk_whatsapp_send = true;
                }else{
                    $post->contact_bulk_whatsapp_send = false;
                }

                if($request->whatsapp_campaign_add == 1){
                    $post->whatsapp_campaign_add = true;
                }else{
                    $post->whatsapp_campaign_add = false;
                }

                if($request->meta_whatsapp_campaign_add == 1){
                    $post->meta_whatsapp_campaign_add = true;
                }else{
                    $post->meta_whatsapp_campaign_add = false;
                }

                if($request->view_meta_whatsapp_template == 1){
                    $post->view_meta_whatsapp_template = true;
                }else{
                    $post->view_meta_whatsapp_template = false;
                }

                if($request->access_setting == 1){
                    $post->access_setting = true;
                }else{
                    $post->access_setting = false;
                }

                if($request->access_allowed_ip == 1){
                    $post->access_allowed_ip = true;
                }else{
                    $post->access_allowed_ip = false;
                }

                if($request->access_allowed_ip_revoke == 1){
                    $post->access_allowed_ip_revoke = true;
                }else{
                    $post->access_allowed_ip_revoke = false;
                }

                if($request->access_allowed_ip_approved_by == 1){
                    $post->access_allowed_ip_approved_by = true;
                }else{
                    $post->access_allowed_ip_approved_by = false;
                }

                if($request->access_allowed_ip_delete == 1){
                    $post->access_allowed_ip_delete = true;
                }else{
                    $post->access_allowed_ip_delete = false;
                }


                if($request->todo_setting == 1){
                    $post->todo_setting = true;
                }else{
                    $post->todo_setting = false;
                }

                if($request->todo_label == 1){
                    $post->todo_label = true;
                }else{
                    $post->todo_label = false;
                }

                if($request->todo_label_add == 1){
                    $post->todo_label_add = true;
                }else{
                    $post->todo_label_add = false;
                }

                if($request->todo_label_view == 1){
                    $post->todo_label_view = true;
                }else{
                    $post->todo_label_view = false;
                }

                if($request->todo_label_edit == 1){
                    $post->todo_label_edit = true;
                }else{
                    $post->todo_label_edit = false;
                }

                if($request->todo_label_delete == 1){
                    $post->todo_label_delete = true;
                }else{
                    $post->todo_label_delete = false;
                }

                if($request->department == 1){
                    $post->department = true;
                }else{
                    $post->department = false;
                }

                if($request->department_add == 1){
                    $post->department_add = true;
                }else{
                    $post->department_add = false;
                }

                if($request->department_view == 1){
                    $post->department_view = true;
                }else{
                    $post->department_view = false;
                }

                if($request->department_edit == 1){
                    $post->department_edit = true;
                }else{
                    $post->department_edit = false;
                }

                if($request->department_delete == 1){
                    $post->department_delete = true;
                }else{
                    $post->department_delete = false;
                }

                if($request->todo == 1){
                    $post->todo = true;
                }else{
                    $post->todo = false;
                }

                if($request->todo_add == 1){
                    $post->todo_add = true;
                }else{
                    $post->todo_add = false;
                }

                if($request->todo_view == 1){
                    $post->todo_view = true;
                }else{
                    $post->todo_view = false;
                }

                if($request->todo_edit == 1){
                    $post->todo_edit = true;
                }else{
                    $post->todo_edit = false;
                }

                if($request->todo_delete == 1){
                    $post->todo_delete = true;
                }else{
                    $post->todo_delete = false;
                }

                if($request->todo_achieved == 1){
                    $post->todo_achieved = true;
                }else{
                    $post->todo_achieved = false;
                }

                if($request->bulk_todo_update_status == 1){
                    $post->bulk_todo_update_status = true;
                }else{
                    $post->bulk_todo_update_status = false;
                }

                if($request->bulk_todo_update_priority == 1){
                    $post->bulk_todo_update_priority = true;
                }else{
                    $post->bulk_todo_update_priority = false;
                }

                if($request->bulk_todo_update_assignto == 1){
                    $post->bulk_todo_update_assignto = true;
                }else{
                    $post->bulk_todo_update_assignto = false;
                }

                if($request->bulk_todo_delete == 1){
                    $post->bulk_todo_delete = true;
                }else{
                    $post->bulk_todo_delete = false;
                }

                if($request->deal_pipeline == 1){
                    $post->deal_pipeline = true;
                }else{
                    $post->deal_pipeline = false;
                }

                if($request->deal_add == 1){
                    $post->deal_add = true;
                }else{
                    $post->deal_add = false;
                }

                if($request->deal_view == 1){
                    $post->deal_view = true;
                }else{
                    $post->deal_view = false;
                }

                if($request->deal_edit == 1){
                    $post->deal_edit = true;
                }else{
                    $post->deal_edit = false;
                }

                if($request->deal_delete == 1){
                    $post->deal_delete = true;
                }else{
                    $post->deal_delete = false;
                }

                if($request->deal_update_stage == 1){
                    $post->deal_update_stage = true;
                }else{
                    $post->deal_update_stage = false;
                }

                if($request->deal_update_recruit_status == 1){
                    $post->deal_update_recruit_status = true;
                }else{
                    $post->deal_update_recruit_status = false;
                }

                if($request->deal_delete_files == 1){
                    $post->deal_delete_files = true;
                }else{
                    $post->deal_delete_files = false;
                }
                
                if($request->employerplus == 1){
                    $post->employerplus = true;
                }else{
                    $post->employerplus = false;
                }
                if($request->employerplus_add == 1){
                    $post->employerplus_add = true;
                }else{
                    $post->employerplus_add = false;
                }
                if($request->employerplus_view == 1){
                    $post->employerplus_view = true;
                }else{
                    $post->employerplus_view = false;
                }
                if($request->employerplus_edit == 1){
                    $post->employerplus_edit = true;
                }else{
                    $post->employerplus_edit = false;
                }
                if($request->employerplus_delete == 1){
                    $post->employerplus_delete = true;
                }else{
                    $post->employerplus_delete = false;
                }



                if($request->permission_setting == 1){
                    $post->permission_setting = true;
                }else{
                    $post->permission_setting = false;
                }

                if($request->contact_plus_setting == 1){
                    $post->contact_plus_setting = true;
                }else{
                    $post->contact_plus_setting = false;
                }


                if($request->finance == 1){
                    $post->finance = true;
                }else{
                    $post->finance = false;
                }

                if($request->candidate_finance == 1){
                    $post->candidate_finance = true;
                }else{
                    $post->candidate_finance = false;
                }

                if($request->transaction_finance == 1){
                    $post->transaction_finance = true;
                }else{
                    $post->transaction_finance = false;
                }

                if($request->transaction_client == 1){
                    $post->transaction_client = true;
                }else{
                    $post->transaction_client = false;
                }

                if($request->finance_sale_invoices == 1){
                    $post->finance_sale_invoices = true;
                }else{
                    $post->finance_sale_invoices = false;
                }


                if($request->finance_sale_invoices_create == 1){
                    $post->finance_sale_invoices_create = true;
                }else{
                    $post->finance_sale_invoices_create = false;
                }

                if($request->finance_sale_invoices_edit == 1){
                    $post->finance_sale_invoices_edit = true;
                }else{
                    $post->finance_sale_invoices_edit = false;
                }

                if($request->finance_sale_invoices_view == 1){
                    $post->finance_sale_invoices_view = true;
                }else{
                    $post->finance_sale_invoices_view = false;
                }

                if($request->finance_sale_invoices_delete == 1){
                    $post->finance_sale_invoices_delete = true;
                }else{
                    $post->finance_sale_invoices_delete = false;
                }

                if($request->finance_payment == 1){
                    $post->finance_payment = true;
                }else{
                    $post->finance_payment = false;
                }

                if($request->finance_payment_add == 1){
                    $post->finance_payment_add = true;
                }else{
                    $post->finance_payment_add = false;
                }

                if($request->finance_payment_edit == 1){
                    $post->finance_payment_edit = true;
                }else{
                    $post->finance_payment_edit = false;
                }

                if($request->finance_payment_delete == 1){
                    $post->finance_payment_delete = true;
                }else{
                    $post->finance_payment_delete = false;
                }

                if($request->hr_management == 1){
                    $post->hr_management = true;
                }else{
                    $post->hr_management = false;
                }

                if($request->hr_dashboard == 1){
                    $post->hr_dashboard = true;
                }else{
                    $post->hr_dashboard = false;
                }

                if($request->hr_attendance == 1){
                    $post->hr_attendance = true;
                }else{
                    $post->hr_attendance = false;
                }

                if($request->hr_attendance_view_all == 1){
                    $post->hr_attendance_view_all = true;
                }else{
                    $post->hr_attendance_view_all = false;
                }

                if($request->hr_payroll == 1){
                    $post->hr_payroll = true;
                }else{
                    $post->hr_payroll = false;
                }

                if($request->hr_settings == 1){
                    $post->hr_settings = true;
                }else{
                    $post->hr_settings = false;
                }

                if($request->fund_advance == 1){
                    $post->fund_advance = true;
                }else{
                    $post->fund_advance = false;
                }

                if($request->fund_advance_create == 1){
                    $post->fund_advance_create = true;
                }else{
                    $post->fund_advance_create = false;
                }

                if($request->fund_advance_edit == 1){
                    $post->fund_advance_edit = true;
                }else{
                    $post->fund_advance_edit = false;
                }

                if($request->fund_advance_delete == 1){
                    $post->fund_advance_delete = true;
                }else{
                    $post->fund_advance_delete = false;
                }

                if($request->fund_advance_settlement_create == 1){
                    $post->fund_advance_settlement_create = true;
                }else{
                    $post->fund_advance_settlement_create = false;
                }

                if($request->fund_advance_settlement_view == 1){
                    $post->fund_advance_settlement_view = true;
                }else{
                    $post->fund_advance_settlement_view = false;
                }

                if($request->fund_advance_adjustment_create == 1){
                    $post->fund_advance_adjustment_create = true;
                }else{
                    $post->fund_advance_adjustment_create = false;
                }

                if($request->fund_advance_ledger_view == 1){
                    $post->fund_advance_ledger_view = true;
                }else{
                    $post->fund_advance_ledger_view = false;
                }

                if($request->fund_advance_reports_view == 1){
                    $post->fund_advance_reports_view = true;
                }else{
                    $post->fund_advance_reports_view = false;
                }

                if($request->fund_advance_export == 1){
                    $post->fund_advance_export = true;
                }else{
                    $post->fund_advance_export = false;
                }

                if($request->industries == 1){
                    $post->industries = true;
                }else{
                    $post->industries = false;
                }

                if($request->add_industries == 1){
                    $post->add_industries = true;
                }else{
                    $post->add_industries = false;
                }

                if($request->edit_industries == 1){
                    $post->edit_industries = true;
                }else{
                    $post->edit_industries = false;
                }

                if($request->view_industries == 1){
                    $post->view_industries = true;
                }else{
                    $post->view_industries = false;
                }

                if($request->delete_industries == 1){
                    $post->delete_industries = true;
                }else{
                    $post->delete_industries = false;
                }

                if($request->leads == 1){
                    $post->leads = true;
                }else{
                    $post->leads = false;
                }

                if($request->leads_view == 1){
                    $post->leads_view = true;
                }else{
                    $post->leads_view = false;
                }

                if($request->leads_delete == 1){
                    $post->leads_delete = true;
                }else{
                    $post->leads_delete = false;
                }

                if($request->leads_assignto == 1){
                    $post->leads_assignto = true;
                }else{
                    $post->leads_assignto = false;
                }

                if($request->leads_bulk_delete == 1){
                    $post->leads_bulk_delete = true;
                }else{
                    $post->leads_bulk_delete = false;
                }

                if($request->leads_bulk_assignto == 1){
                    $post->leads_bulk_assignto = true;
                }else{
                    $post->leads_bulk_assignto = false;
                }


                if($request->setup == 1){
                    $post->setup = true;
                }else{
                    $post->setup = false;
                }

                if($request->whatsapp_setup == 1){
                    $post->whatsapp_setup = true;
                }else{
                    $post->whatsapp_setup = false;
                }

                if($request->whatsapp_api == 1){
                    $post->whatsapp_api = true;
                }else{
                    $post->whatsapp_api = false;
                }

                if($request->add_whatsapp_api == 1){
                    $post->add_whatsapp_api = true;
                }else{
                    $post->add_whatsapp_api = false;
                }

                if($request->edit_whatsapp_api == 1){
                    $post->edit_whatsapp_api = true;
                }else{
                    $post->edit_whatsapp_api = false;
                }

                if($request->view_whatsapp_api == 1){
                    $post->view_whatsapp_api = true;
                }else{
                    $post->view_whatsapp_api = false;
                }

                if($request->delete_whatsapp_api == 1){
                    $post->delete_whatsapp_api = true;
                }else{
                    $post->delete_whatsapp_api = false;
                }

                if($request->whatsapp_api_test == 1){
                    $post->whatsapp_api_test = true;
                }else{
                    $post->whatsapp_api_test = false;
                }

                if($request->whatsapp_meta_api == 1){
                    $post->whatsapp_meta_api = true;
                }else{
                    $post->whatsapp_meta_api = false;
                }

                if($request->add_whatsapp_meta_api == 1){
                    $post->add_whatsapp_meta_api = true;
                }else{
                    $post->add_whatsapp_meta_api = false;
                }

                if($request->edit_whatsapp_meta_api == 1){
                    $post->edit_whatsapp_meta_api = true;
                }else{
                    $post->edit_whatsapp_meta_api = false;
                }

                if($request->view_whatsapp_meta_api == 1){
                    $post->view_whatsapp_meta_api = true;
                }else{
                    $post->view_whatsapp_meta_api = false;
                }

                if($request->delete_whatsapp_meta_api == 1){
                    $post->delete_whatsapp_meta_api = true;
                }else{
                    $post->delete_whatsapp_meta_api = false;
                }

                // Whatsapp URL
                if ($request->whatsapp_url == 1) {
                    $post->whatsapp_url = true;
                } else {
                    $post->whatsapp_url = false;
                }

                // Add Whatsapp Meta API
                if ($request->add_whatsapp_url == 1) {
                    $post->add_whatsapp_url = true;
                } else {
                    $post->add_whatsapp_url = false;
                }

                // Edit Whatsapp Meta API
                if ($request->edit_whatsapp_url == 1) {
                    $post->edit_whatsapp_url = true;
                } else {
                    $post->edit_whatsapp_url = false;
                }

                // View Whatsapp Meta API
                if ($request->view_whatsapp_url == 1) {
                    $post->view_whatsapp_url = true;
                } else {
                    $post->view_whatsapp_url = false;
                }

                // Delete Whatsapp Meta API
                if ($request->delete_whatsapp_url == 1) {
                    $post->delete_whatsapp_url = true;
                } else {
                    $post->delete_whatsapp_url = false;
                }


                if($request->add_whatsapp_api_for_user == 1){
                    $post->add_whatsapp_api_for_user = true;
                }else{
                    $post->add_whatsapp_api_for_user = false;
                }


                if($request->allcontact == 1){
                    $post->allcontact = true;
                }else{
                    $post->allcontact = false;
                }

                if($request->allcontact_add == 1){
                    $post->allcontact_add = true;
                }else{
                    $post->allcontact_add = false;
                }

                if($request->allcontact_edit == 1){
                    $post->allcontact_edit = true;
                }else{
                    $post->allcontact_edit = false;
                }

                if($request->allcontact_delete == 1){
                    $post->allcontact_delete = true;
                }else{
                    $post->allcontact_delete = false;
                }

                if($request->allcontact_view == 1){
                    $post->allcontact_view = true;
                }else{
                    $post->allcontact_view = false;
                }

                if($request->allcontact_bulk_transfer_group == 1){
                    $post->allcontact_bulk_transfer_group = true;
                }else{
                    $post->allcontact_bulk_transfer_group = false;
                }

                if($request->allcontact_bulk_transfer_careoff == 1){
                    $post->allcontact_bulk_transfer_careoff = true;
                }else{
                    $post->allcontact_bulk_transfer_careoff = false;
                }

                // if($request->allcontact_bulk_transfer_leadowner == 1){
                //     $post->allcontact_bulk_transfer_leadowner = true;
                // }else{
                //     $post->allcontact_bulk_transfer_leadowner = false;
                // }

                if($request->allcontact_bulk_send_whatsapp == 1){
                    $post->allcontact_bulk_send_whatsapp = true;
                }else{
                    $post->allcontact_bulk_send_whatsapp = false;
                }


                if($request->expense == 1){
                    $post->expense = true;
                }else{
                    $post->expense = false;
                }

                if($request->add_expense == 1){
                    $post->add_expense = true;
                }else{
                    $post->add_expense = false;
                }

                if($request->edit_expense == 1){
                    $post->edit_expense = true;
                }else{
                    $post->edit_expense = false;
                }

                if($request->view_expense == 1){
                    $post->view_expense = true;
                }else{
                    $post->view_expense = false;
                }

                if($request->delete_expense == 1){
                    $post->delete_expense = true;
                }else{
                    $post->delete_expense = false;
                }

                if($request->file_manager == 1){
                    $post->file_manager = true;
                }else{
                    $post->file_manager = false;
                }

                if($request->add_file_manager == 1){
                    $post->add_file_manager = true;
                }else{
                    $post->add_file_manager = false;
                }

                if($request->upload_file_manager == 1){
                    $post->upload_file_manager = true;
                }else{
                    $post->upload_file_manager = false;
                }

                if($request->download_file_manager == 1){
                    $post->download_file_manager = true;
                }else{
                    $post->download_file_manager = false;
                }

                if($request->preview_file_manager == 1){
                    $post->preview_file_manager = true;
                }else{
                    $post->preview_file_manager = false;
                }

                if($request->rename_file_manager == 1){
                    $post->rename_file_manager = true;
                }else{
                    $post->rename_file_manager = false;
                }

                if($request->move_file_manager == 1){
                    $post->move_file_manager = true;
                }else{
                    $post->move_file_manager = false;
                }

                if($request->delete_file_manager == 1){
                    $post->delete_file_manager = true;
                }else{
                    $post->delete_file_manager = false;
                }

                if($request->manage_all_file_manager == 1){
                    $post->manage_all_file_manager = true;
                }else{
                    $post->manage_all_file_manager = false;
                }

                if($request->verified_notverified_associate == 1){
                    $post->verified_notverified_associate = true;
                }else{
                    $post->verified_notverified_associate = false;
                }

                if($request->primary_no_val_associate == 1){
                    $post->primary_no_val_associate = true;
                }else{
                    $post->primary_no_val_associate = false;
                }

                if($request->secondary_no_val_associate == 1){
                    $post->secondary_no_val_associate = true;
                }else{
                    $post->secondary_no_val_associate = false;
                }

                if($request->email_associate == 1){
                    $post->email_associate = true;
                }else{
                    $post->email_associate = false;
                }

                if($request->meta_whatsapp_campaign_create_user == 1){
                    $post->meta_whatsapp_campaign_create_user = true;
                }else{
                    $post->meta_whatsapp_campaign_create_user = false;
                }

                if($request->meta_automation == 1){
                    $post->meta_automation = true;
                }else{
                    $post->meta_automation = false;
                }

                if($request->add_meta_automation == 1){
                    $post->add_meta_automation = true;
                }else{
                    $post->add_meta_automation = false;
                }

                if($request->view_meta_automation == 1){
                    $post->view_meta_automation = true;
                }else{
                    $post->view_meta_automation = false;
                }

                if($request->edit_meta_automation == 1){
                    $post->edit_meta_automation = true;
                }else{
                    $post->edit_meta_automation = false;
                }

                if($request->delete_meta_automation == 1){
                    $post->delete_meta_automation = true;
                }else{
                    $post->delete_meta_automation = false;
                }

                if($request->cand_add_edit == 1){
                    $post->cand_add_edit = true;
                }else{
                    $post->cand_add_edit = false;
                }

                if($request->cand_sourcing_date == 1){
                    $post->cand_sourcing_date = true;
                }else{
                    $post->cand_sourcing_date = false;
                }


                if($request->allcontact_bulk_delete == 1){
                    $post->allcontact_bulk_delete = true;
                }else{
                    $post->allcontact_bulk_delete = false;
                }

                if($request->allcontact_bulk_country_update == 1){
                    $post->allcontact_bulk_country_update = true;
                }else{
                    $post->allcontact_bulk_country_update = false;
                }

                if($request->allcontact_bulk_country_code_update == 1){
                    $post->allcontact_bulk_country_code_update = true;
                }else{
                    $post->allcontact_bulk_country_code_update = false;
                }

                if($request->leads_candidate_view == 1){
                    $post->leads_candidate_view = true;
                }else{
                    $post->leads_candidate_view = false;
                }

                if($request->leads_employer_view == 1){
                    $post->leads_employer_view = true;
                }else{
                    $post->leads_employer_view = false;
                }

                if($request->allcontact_company_column == 1){
                    $post->allcontact_company_column = true;
                }else{
                    $post->allcontact_company_column = false;
                }

                if($request->leads_company_column == 1){
                    $post->leads_company_column = true;
                }else{
                    $post->leads_company_column = false;
                }

                if($request->update_status_achieved == 1){
                    $post->update_status_achieved = true;
                }else{
                    $post->update_status_achieved = false;
                }


                $post->save();
            }

        } else {
            $newPost = new Adminpermission();
            $newPost->staff_id = $request->staff_id;

            if($request->full_access == 1){
                $newPost->full_access = true;
                $newPost->bookings = true;
                $newPost->booking_confirm = true;
                $newPost->add_visa_details = true;
                $newPost->view_booking = true;
                $newPost->add_payment = true;
                $newPost->cancel_booking = true;
                $newPost->replace_candidate = true;
                $newPost->employer = true;
                $newPost->view_employer = true;
                $newPost->delete_employer = true;
                $newPost->candidate = true;
                $newPost->add_candidate = true;
                $newPost->edit_candidate = true;
                $newPost->view_candidate = true;
                $newPost->delete_candidate = true;
                $newPost->publish_candidate = true;
                $newPost->testimonial = true;
                $newPost->add_testimonial = true;
                $newPost->edit_testimonial = true;
                $newPost->view_testimonial = true;
                $newPost->copy_link_testimonial = true;
                $newPost->delete_testimonial = true;
                $newPost->approval_testimonial = true;
                $newPost->payment_testimonial = true;
                $newPost->video_received_testimonial = true;
                $newPost->social_media_testimonial = true;
                $newPost->google_review = true;
                $newPost->add_google_review = true;
                $newPost->edit_google_review = true;
                $newPost->view_google_review = true;
                $newPost->delete_google_review = true;
                $newPost->approval_google_review = true;
                $newPost->payment_google_review = true;
                $newPost->cv_b2b = true;
                $newPost->cv_b2c = true;
                $newPost->client = true;
                $newPost->view_client = true;
                $newPost->delete_client = true;
                $newPost->partner = true;
                $newPost->add_partner = true;
                $newPost->edit_partner = true;
                $newPost->view_partner = true;
                $newPost->delete_partner = true;
                $newPost->settings = true;
                $newPost->dynamic = true;
                $newPost->staff = true;
                $newPost->add_staff = true;
                $newPost->edit_staff = true;
                $newPost->delete_staff = true;
                $newPost->view_staff = true;
                $newPost->branch = true;
                $newPost->add_branch = true;
                $newPost->edit_branch = true;
                $newPost->view_branch = true;
                $newPost->delete_branch = true;
                $newPost->profession = true;
                $newPost->add_profession = true;
                $newPost->edit_profession = true;
                $newPost->delete_profession = true;
                $newPost->view_profession = true;
                $newPost->placeofissue = true;
                $newPost->add_placeofissue = true;
                $newPost->edit_placeofissue = true;
                $newPost->delete_placeofissue = true;
                $newPost->view_placeofissue = true;
                $newPost->country = true;
                $newPost->add_country = true;
                $newPost->edit_country = true;
                $newPost->view_country = true;
                $newPost->delete_country = true;
                $newPost->region = true;
                $newPost->add_region = true;
                $newPost->edit_region = true;
                $newPost->delete_region = true;
                $newPost->view_region = true;
                $newPost->city = true;
                $newPost->add_city = true;
                $newPost->edit_city = true;
                $newPost->view_city = true;
                $newPost->delete_city = true;
                $newPost->websiteconfig = true;
                $newPost->mailsetup = true;
                $newPost->add_mailsetup = true;
                $newPost->edit_mailsetup = true;
                $newPost->delete_mailsetup = true;
                $newPost->view_mailsetup = true;
                $newPost->carknown = true;
                $newPost->add_car_known = true;
                $newPost->edit_car_known = true;
                $newPost->delete_car_known = true;
                $newPost->view_car_known = true;
                $newPost->personalise_class = true;
                $newPost->add_personalise_class = true;
                $newPost->edit_personalise_class = true;
                $newPost->delete_personalise_class = true;
                $newPost->view_personalise_class = true;
                $newPost->storage_usage_setting = true;
                $newPost->template = true;
                $newPost->add_template = true;
                $newPost->edit_template = true;
                $newPost->delete_template = true;
                $newPost->view_template = true;
                $newPost->associate = true;
                $newPost->add_associate = true;
                $newPost->edit_associate = true;
                $newPost->view_associate = true;
                $newPost->delete_associate = true;
                $newPost->publish_associate = true;
                $newPost->orderStatus = true;
                $newPost->add_orderStatus = true;
                $newPost->edit_orderStatus = true;
                $newPost->delete_orderStatus = true;
                $newPost->view_orderStatus = true;
                $newPost->upload_docs = true;
                $newPost->add_service_charge = true;
                $newPost->edit_service_charge = true;
                $newPost->updt_status_service_charge = true;
                $newPost->delete_service_charge = true;
                $newPost->candidate_status = true;
                $newPost->candidate_reset_status = true;
                $newPost->delete_cand_payment = true;
                $newPost->edit_cand_payment = true;
                $newPost->cand_delete_file = true;
                $newPost->cand_edit_associate = true;
                $newPost->cand_edit_careoff = true;
                $newPost->activedeactive_associate = true;
                $newPost->contactp = true;
                $newPost->add_contactp = true;
                $newPost->edit_contactp = true;
                $newPost->view_contactp = true;
                $newPost->delete_contactp = true;
                $newPost->lead_owner_transfer = true;
                $newPost->careoff_transfer = true;
                $newPost->whatsapp_plus = true;

                $newPost->sms_campaign_module = true;
                $newPost->sms_api = true;
                $newPost->add_sms_api = true;
                $newPost->edit_sms_api = true;
                $newPost->delete_sms_api = true;
                $newPost->change_status_sms_api = true;
                $newPost->assign_sms_api = true;
                $newPost->sms_template = true;
                $newPost->add_sms_template = true;
                $newPost->edit_sms_template = true;
                $newPost->delete_sms_template = true;
                $newPost->change_status_sms_template = true;
                $newPost->sms_campaign = true;
                $newPost->add_sms_campaign = true;
                $newPost->view_sms_campaign = true;
                $newPost->delete_sms_campaign = true;


                $newPost->image_host = true;
                $newPost->expworklocation = true;
                $newPost->add_expworklocation = true;
                $newPost->edit_expworklocation = true;
                $newPost->view_expworklocation = true;
                $newPost->delete_expworklocation = true;

                $newPost->businesstype = true;
                $newPost->add_businesstype = true;
                $newPost->edit_businesstype = true;
                $newPost->view_businesstype = true;
                $newPost->delete_businesstype = true;
                $newPost->groupcp = true;
                $newPost->add_groupcp = true;
                $newPost->edit_groupcp = true;
                $newPost->view_groupcp = true;
                $newPost->delete_groupcp = true;

                $newPost->meta_whatsapp_template = true;
                $newPost->add_meta_whatsapp_template = true;
                $newPost->edit_meta_whatsapp_template = true;
                $newPost->delete_meta_whatsapp_template = true;
                $newPost->status_meta_whatsapp_template = true;
                $newPost->public_meta_whatsapp_template = true;
                $newPost->meta_whatsapp_campaign = true;
                $newPost->meta_whatsapp_campaign_view = true;
                $newPost->whatsapp_nromal = true;
                $newPost->whatsapp_template = true;
                $newPost->add_whatsapp_template = true;
                $newPost->edit_whatsapp_template = true;
                $newPost->view_whatsapp_template = true;
                $newPost->delete_whatsapp_template = true;
                $newPost->status_whatsapp_template = true;
                $newPost->public_whatsapp_template = true;
                $newPost->whatsapp_campaign = true;
                $newPost->whatsapp_campaign_view = true;
                $newPost->contact_bulk_whatsapp_send = true;
                $newPost->whatsapp_campaign_add = true;
                $newPost->meta_whatsapp_campaign_add = true;
                $newPost->view_meta_whatsapp_template = true;

                $newPost->access_setting = true;
                $newPost->access_allowed_ip = true;
                $newPost->access_allowed_ip_revoke = true;
                $newPost->access_allowed_ip_approved_by = true;
                $newPost->access_allowed_ip_delete = true;

                $newPost->todo_setting = true;
                $newPost->todo_label = true;
                $newPost->todo_label_add = true;
                $newPost->todo_label_view = true;
                $newPost->todo_label_edit = true;
                $newPost->todo_label_delete = true;
                $newPost->department = true;
                $newPost->department_add = true;
                $newPost->department_view = true;
                $newPost->department_edit = true;
                $newPost->department_delete = true;

                $newPost->todo = true;
                $newPost->todo_add = true;
                $newPost->todo_view = true;
                $newPost->todo_edit = true;
                $newPost->todo_delete = true;
                $newPost->todo_achieved = true;

                $newPost->bulk_todo_update_status = true;
                $newPost->bulk_todo_update_priority = true;
                $newPost->bulk_todo_update_assignto = true;
                $newPost->bulk_todo_delete = true;

                $newPost->deal_pipeline = true;
                $newPost->deal_add = true;
                $newPost->deal_view = true;
                $newPost->deal_edit = true;
                $newPost->deal_delete = true;
                $newPost->deal_update_stage = true;
                $newPost->deal_update_recruit_status = true;
                $newPost->deal_delete_files = true;


                $newPost->employerplus = true;
                $newPost->employerplus_add = true;
                $newPost->employerplus_view = true;
                $newPost->employerplus_edit = true;
                $newPost->employerplus_delete = true;

                $newPost->permission_setting = true;
                $newPost->contact_plus_setting = true;

                $newPost->finance = true;
                $newPost->candidate_finance = true;
                $newPost->transaction_finance = true;
                $newPost->transaction_client = true;
                $newPost->finance_sale_invoices = true;
                $newPost->finance_sale_invoices_create = true;
                $newPost->finance_sale_invoices_edit = true;
                $newPost->finance_sale_invoices_view = true;
                $newPost->finance_sale_invoices_delete = true;
                $newPost->finance_payment = true;
                $newPost->finance_payment_add = true;
                $newPost->finance_payment_edit = true;
                $newPost->finance_payment_delete = true;

                $newPost->hr_management = true;
                $newPost->hr_dashboard = true;
                $newPost->hr_attendance = true;
                $newPost->hr_attendance_view_all = true;
                $newPost->hr_payroll = true;
                $newPost->hr_settings = true;

                $newPost->fund_advance = true;
                $newPost->fund_advance_create = true;
                $newPost->fund_advance_edit = true;
                $newPost->fund_advance_delete = true;
                $newPost->fund_advance_settlement_create = true;
                $newPost->fund_advance_settlement_view = true;
                $newPost->fund_advance_adjustment_create = true;
                $newPost->fund_advance_ledger_view = true;
                $newPost->fund_advance_reports_view = true;
                $newPost->fund_advance_export = true;

                $newPost->industries = true;
                $newPost->add_industries = true;
                $newPost->edit_industries = true;
                $newPost->view_industries = true;
                $newPost->delete_industries = true;

                $newPost->leads = true;
                $newPost->leads_view = true;
                $newPost->leads_delete = true;
                $newPost->leads_assignto = true;
                $newPost->leads_bulk_delete = true;
                $newPost->leads_bulk_assignto = true;


                $newPost->setup = true;
                $newPost->whatsapp_setup = true;
                $newPost->whatsapp_api = true;
                $newPost->add_whatsapp_api = true;
                $newPost->edit_whatsapp_api = true;
                $newPost->view_whatsapp_api = true;
                $newPost->delete_whatsapp_api = true;
                $newPost->whatsapp_api_test = true;
                $newPost->whatsapp_meta_api = true;
                $newPost->add_whatsapp_meta_api = true;
                $newPost->edit_whatsapp_meta_api = true;
                $newPost->view_whatsapp_meta_api = true;
                $newPost->delete_whatsapp_meta_api = true;
                $newPost->add_whatsapp_api_for_user = true;

                $newPost->whatsapp_url = true;
                $newPost->add_whatsapp_url = true;
                $newPost->edit_whatsapp_url = true;
                $newPost->view_whatsapp_url = true;
                $newPost->delete_whatsapp_url = true;

                $newPost->allcontact = true;
                $newPost->allcontact_add = true;
                $newPost->allcontact_edit = true;
                $newPost->allcontact_view = true;
                $newPost->allcontact_bulk_transfer_group = true;
                $newPost->allcontact_bulk_transfer_careoff = true;
                // $newPost->allcontact_bulk_transfer_leadowner = true;
                $newPost->allcontact_bulk_send_whatsapp = true;
                $newPost->allcontact_delete = true;

                $newPost->expense = true;
                $newPost->add_expense = true;
                $newPost->edit_expense = true;
                $newPost->view_expense = true;
                $newPost->delete_expense = true;

                $newPost->file_manager = true;
                $newPost->add_file_manager = true;
                $newPost->upload_file_manager = true;
                $newPost->download_file_manager = true;
                $newPost->preview_file_manager = true;
                $newPost->rename_file_manager = true;
                $newPost->move_file_manager = true;
                $newPost->delete_file_manager = true;
                $newPost->manage_all_file_manager = true;

                $newPost->verified_notverified_associate = true;
                $newPost->primary_no_val_associate = true;
                $newPost->secondary_no_val_associate = true;
                $newPost->email_associate = true;
                $newPost->meta_whatsapp_campaign_create_user = true;

                $newPost->meta_automation = true;
                $newPost->add_meta_automation = true;
                $newPost->view_meta_automation = true;
                $newPost->edit_meta_automation = true;
                $newPost->delete_meta_automation = true;

                $newPost->cand_add_edit = true;
                $newPost->cand_sourcing_date = true;

                $newPost->allcontact_bulk_delete = true;
                $newPost->allcontact_bulk_country_update = true;
                $newPost->allcontact_bulk_country_code_update = true;

                $newPost->leads_candidate_view = true;
                $newPost->leads_employer_view = true;

                $newPost->allcontact_company_column = true;
                $newPost->leads_company_column = true;

                $newPost->update_status_achieved = true;

                $newPost->chat = true;

                $newPost->save();
            }else{
                $newPost->full_access = false;
                if($request->chat == 1){
                    $newPost->chat = true;
                }else{
                    $newPost->chat = false;
                }
                if($request->bookings == 1){
                    $newPost->bookings = true;
                }else{
                    $newPost->bookings = false;
                }
                if($request->booking_confirm == 1){
                    $newPost->booking_confirm = true;
                }else{
                    $newPost->booking_confirm = false;
                }
                if($request->add_visa_details == 1){
                    $newPost->add_visa_details = true;
                }else{
                    $newPost->add_visa_details = false;
                }
                if($request->view_booking == 1){
                    $newPost->view_booking = true;
                }else{
                    $newPost->view_booking = false;
                }
                if($request->add_payment == 1){
                    $newPost->add_payment = true;
                }else{
                    $newPost->add_payment = false;
                }
                if($request->cancel_booking == 1){
                    $newPost->cancel_booking = true;
                }else{
                    $newPost->cancel_booking = false;
                }
                if($request->replace_candidate == 1){
                    $newPost->replace_candidate = true;
                }else{
                    $newPost->replace_candidate = false;
                }
                if($request->employer == 1){
                    $newPost->employer = true;
                }else{
                    $newPost->employer = false;
                }
                if($request->view_employer == 1){
                    $newPost->view_employer = true;
                }else{
                    $newPost->view_employer = false;
                }
                if($request->delete_employer == 1){
                    $newPost->delete_employer = true;
                }else{
                    $newPost->delete_employer = false;
                }
                if($request->candidate == 1){
                    $newPost->candidate = true;
                }else{
                    $newPost->candidate = false;
                }
                if($request->add_candidate == 1){
                    $newPost->add_candidate = true;
                }else{
                    $newPost->add_candidate = false;
                }
                if($request->edit_candidate == 1){
                    $newPost->edit_candidate = true;
                }else{
                    $newPost->edit_candidate = false;
                }
                if($request->view_candidate == 1){
                    $newPost->view_candidate = true;
                }else{
                    $newPost->view_candidate = false;
                }
                if($request->delete_candidate == 1){
                    $newPost->delete_candidate = true;
                }else{
                    $newPost->delete_candidate = false;
                }
                if($request->publish_candidate == 1){
                    $newPost->publish_candidate = true;
                }else{
                    $newPost->publish_candidate = false;
                }
                if($request->testimonial == 1){
                    $newPost->testimonial = true;
                }else{
                    $newPost->testimonial = false;
                }
                if($request->add_testimonial == 1){
                    $newPost->add_testimonial = true;
                }else{
                    $newPost->add_testimonial = false;
                }
                if($request->edit_testimonial == 1){
                    $newPost->edit_testimonial = true;
                }else{
                    $newPost->edit_testimonial = false;
                }
                if($request->view_testimonial == 1){
                    $newPost->view_testimonial = true;
                }else{
                    $newPost->view_testimonial = false;
                }
                if($request->copy_link_testimonial == 1){
                    $newPost->copy_link_testimonial = true;
                }else{
                    $newPost->copy_link_testimonial = false;
                }
                if($request->delete_testimonial == 1){
                    $newPost->delete_testimonial = true;
                }else{
                    $newPost->delete_testimonial = false;
                }
                if($request->approval_testimonial == 1){
                    $newPost->approval_testimonial = true;
                }else{
                    $newPost->approval_testimonial = false;
                }
                if($request->payment_testimonial == 1){
                    $newPost->payment_testimonial = true;
                }else{
                    $newPost->payment_testimonial = false;
                }
                if($request->video_received_testimonial == 1){
                    $newPost->video_received_testimonial = true;
                }else{
                    $newPost->video_received_testimonial = false;
                }
                if($request->social_media_testimonial == 1){
                    $newPost->social_media_testimonial = true;
                }else{
                    $newPost->social_media_testimonial = false;
                }
                if($request->google_review == 1){
                    $newPost->google_review = true;
                }else{
                    $newPost->google_review = false;
                }
                if($request->add_google_review == 1){
                    $newPost->add_google_review = true;
                }else{
                    $newPost->add_google_review = false;
                }
                if($request->edit_google_review == 1){
                    $newPost->edit_google_review = true;
                }else{
                    $newPost->edit_google_review = false;
                }
                if($request->view_google_review == 1){
                    $newPost->view_google_review = true;
                }else{
                    $newPost->view_google_review = false;
                }
                if($request->delete_google_review == 1){
                    $newPost->delete_google_review = true;
                }else{
                    $newPost->delete_google_review = false;
                }
                if($request->approval_google_review == 1){
                    $newPost->approval_google_review = true;
                }else{
                    $newPost->approval_google_review = false;
                }
                if($request->payment_google_review == 1){
                    $newPost->payment_google_review = true;
                }else{
                    $newPost->payment_google_review = false;
                }
                if($request->cv_b2b == 1){
                    $newPost->cv_b2b = true;
                }else{
                    $newPost->cv_b2b = false;
                }
                if($request->cv_b2c == 1){
                    $newPost->cv_b2c = true;
                }else{
                    $newPost->cv_b2c = false;
                }
                if($request->client == 1){
                    $newPost->client = true;
                }else{
                    $newPost->client = false;
                }
                if($request->view_client == 1){
                    $newPost->view_client = true;
                }else{
                    $newPost->view_client = false;
                }
                if($request->delete_client == 1){
                    $newPost->delete_client = true;
                }else{
                    $newPost->delete_client = false;
                }
                if($request->partner == 1){
                    $newPost->partner = true;
                }else{
                    $newPost->partner = false;
                }
                if($request->add_partner == 1){
                    $newPost->add_partner = true;
                }else{
                    $newPost->add_partner = false;
                }
                if($request->edit_partner == 1){
                    $newPost->edit_partner = true;
                }else{
                    $newPost->edit_partner = false;
                }
                if($request->view_partner == 1){
                    $newPost->view_partner = true;
                }else{
                    $newPost->view_partner = false;
                }
                if($request->delete_partner == 1){
                    $newPost->delete_partner = true;
                }else{
                    $newPost->delete_partner = false;
                }
                if($request->settings == 1){
                    $newPost->settings = true;
                }else{
                    $newPost->settings = false;
                }
                if($request->dynamic == 1){
                    $newPost->dynamic = true;
                }else{
                    $newPost->dynamic = false;
                }
                if($request->staff == 1){
                    $newPost->staff = true;
                }else{
                    $newPost->staff = false;
                }
                if($request->add_staff == 1){
                    $newPost->add_staff = true;
                }else{
                    $newPost->add_staff = false;
                }
                if($request->edit_staff == 1){
                    $newPost->edit_staff = true;
                }else{
                    $newPost->edit_staff = false;
                }
                if($request->delete_staff == 1){
                    $newPost->delete_staff = true;
                }else{
                    $newPost->delete_staff = false;
                }
                if($request->view_staff == 1){
                    $newPost->view_staff = true;
                }else{
                    $newPost->view_staff = false;
                }
                if($request->branch == 1){
                    $newPost->branch = true;
                }else{
                    $newPost->branch = false;
                }
                if($request->add_branch == 1){
                    $newPost->add_branch = true;
                }else{
                    $newPost->add_branch = false;
                }
                if($request->edit_branch == 1){
                    $newPost->edit_branch = true;
                }else{
                    $newPost->edit_branch = false;
                }
                if($request->delete_branch == 1){
                    $newPost->delete_branch = true;
                }else{
                    $newPost->delete_branch = false;
                }
                if($request->view_branch == 1){
                    $newPost->view_branch = true;
                }else{
                    $newPost->view_branch = false;
                }
                if($request->profession == 1){
                    $newPost->profession = true;
                }else{
                    $newPost->profession = false;
                }
                if($request->add_profession == 1){
                    $newPost->add_profession = true;
                }else{
                    $newPost->add_profession = false;
                }
                if($request->edit_profession == 1){
                    $newPost->edit_profession = true;
                }else{
                    $newPost->edit_profession = false;
                }
                if($request->delete_profession == 1){
                    $newPost->delete_profession = true;
                }else{
                    $newPost->delete_profession = false;
                }
                if($request->view_profession == 1){
                    $newPost->view_profession = true;
                }else{
                    $newPost->view_profession = false;
                }
                if($request->placeofissue == 1){
                    $newPost->placeofissue = true;
                }else{
                    $newPost->placeofissue = false;
                }
                if($request->add_placeofissue == 1){
                    $newPost->add_placeofissue = true;
                }else{
                    $newPost->add_placeofissue = false;
                }
                if($request->edit_placeofissue == 1){
                    $newPost->edit_placeofissue = true;
                }else{
                    $newPost->edit_placeofissue = false;
                }
                if($request->delete_placeofissue == 1){
                    $newPost->delete_placeofissue = true;
                }else{
                    $newPost->delete_placeofissue = false;
                }
                if($request->view_placeofissue == 1){
                    $newPost->view_placeofissue = true;
                }else{
                    $newPost->view_placeofissue = false;
                }
                if($request->country == 1){
                    $newPost->country = true;
                }else{
                    $newPost->country = false;
                }
                if($request->add_country == 1){
                    $newPost->add_country = true;
                }else{
                    $newPost->add_country = false;
                }
                if($request->edit_country == 1){
                    $newPost->edit_country = true;
                }else{
                    $newPost->edit_country = false;
                }
                if($request->view_country == 1){
                    $newPost->view_country = true;
                }else{
                    $newPost->view_country = false;
                }
                if($request->delete_country == 1){
                    $newPost->delete_country = true;
                }else{
                    $newPost->delete_country = false;
                }
                if($request->region == 1){
                    $newPost->region = true;
                }else{
                    $newPost->region = false;
                }
                if($request->add_region == 1){
                    $newPost->add_region = true;
                }else{
                    $newPost->add_region = false;
                }
                if($request->edit_region == 1){
                    $newPost->edit_region = true;
                }else{
                    $newPost->edit_region = false;
                }
                if($request->delete_region == 1){
                    $newPost->delete_region = true;
                }else{
                    $newPost->delete_region = false;
                }
                if($request->view_region == 1){
                    $newPost->view_region = true;
                }else{
                    $newPost->view_region = false;
                }
                if($request->city == 1){
                    $newPost->city = true;
                }else{
                    $newPost->city = false;
                }
                if($request->add_city == 1){
                    $newPost->add_city = true;
                }else{
                    $newPost->add_city = false;
                }
                if($request->edit_city == 1){
                    $newPost->edit_city = true;
                }else{
                    $newPost->edit_city = false;
                }
                if($request->view_city == 1){
                    $newPost->view_city = true;
                }else{
                    $newPost->view_city = false;
                }
                if($request->delete_city == 1){
                    $newPost->delete_city = true;
                }else{
                    $newPost->delete_city = false;
                }
                if($request->websiteconfig == 1){
                    $newPost->websiteconfig = true;
                }else{
                    $newPost->websiteconfig = false;
                }
                if($request->mailsetup == 1){
                    $newPost->mailsetup = true;
                }else{
                    $newPost->mailsetup = false;
                }
                if($request->add_mailsetup == 1){
                    $newPost->add_mailsetup = true;
                }else{
                    $newPost->add_mailsetup = false;
                }
                if($request->edit_mailsetup == 1){
                    $newPost->edit_mailsetup = true;
                }else{
                    $newPost->edit_mailsetup = false;
                }
                if($request->delete_mailsetup == 1){
                    $newPost->delete_mailsetup = true;
                }else{
                    $newPost->delete_mailsetup = false;
                }
                if($request->view_mailsetup == 1){
                    $newPost->view_mailsetup = true;
                }else{
                    $newPost->view_mailsetup = false;
                }
                if($request->carknown == 1){
                    $newPost->carknown = true;
                }else{
                    $newPost->carknown = false;
                }
                if($request->add_car_known == 1){
                    $newPost->add_car_known = true;
                }else{
                    $newPost->add_car_known = false;
                }
                if($request->edit_car_known == 1){
                    $newPost->edit_car_known = true;
                }else{
                    $newPost->edit_car_known = false;
                }
                if($request->delete_car_known == 1){
                    $newPost->delete_car_known = true;
                }else{
                    $newPost->delete_car_known = false;
                }
                if($request->view_car_known == 1){
                    $newPost->view_car_known = true;
                }else{
                    $newPost->view_car_known = false;
                }
                if($request->personalise_class == 1){
                    $newPost->personalise_class = true;
                }else{
                    $newPost->personalise_class = false;
                }
                if($request->add_personalise_class == 1){
                    $newPost->add_personalise_class = true;
                }else{
                    $newPost->add_personalise_class = false;
                }
                if($request->edit_personalise_class == 1){
                    $newPost->edit_personalise_class = true;
                }else{
                    $newPost->edit_personalise_class = false;
                }
                if($request->delete_personalise_class == 1){
                    $newPost->delete_personalise_class = true;
                }else{
                    $newPost->delete_personalise_class = false;
                }
                if($request->view_personalise_class == 1){
                    $newPost->view_personalise_class = true;
                }else{
                    $newPost->view_personalise_class = false;
                }
                if($request->storage_usage_setting == 1){
                    $newPost->storage_usage_setting = true;
                }else{
                    $newPost->storage_usage_setting = false;
                }
                if($request->template == 1){
                    $newPost->template = true;
                }else{
                    $newPost->template = false;
                }
                if($request->add_template == 1){
                    $newPost->add_template = true;
                }else{
                    $newPost->add_template = false;
                }
                if($request->edit_template == 1){
                    $newPost->edit_template = true;
                }else{
                    $newPost->edit_template = false;
                }
                if($request->delete_template == 1){
                    $newPost->delete_template = true;
                }else{
                    $newPost->delete_template = false;
                }
                if($request->view_template == 1){
                    $newPost->view_template = true;
                }else{
                    $newPost->view_template = false;
                }
                if($request->associate == 1){
                    $newPost->associate = true;
                }else{
                    $newPost->associate = false;
                }
                if($request->add_associate == 1){
                    $newPost->add_associate = true;
                }else{
                    $newPost->add_associate = false;
                }
                if($request->edit_associate == 1){
                    $newPost->edit_associate = true;
                }else{
                    $newPost->edit_associate = false;
                }
                if($request->view_associate == 1){
                    $newPost->view_associate = true;
                }else{
                    $newPost->view_associate = false;
                }
                if($request->delete_associate == 1){
                    $newPost->delete_associate = true;
                }else{
                    $newPost->delete_associate = false;
                }
                if($request->publish_associate == 1){
                    $newPost->publish_associate = true;
                }else{
                    $newPost->publish_associate = false;
                }

                if($request->orderStatus == 1){
                    $newPost->orderStatus = true;
                }else{
                    $newPost->orderStatus = false;
                }
                if($request->add_orderStatus == 1){
                    $newPost->add_orderStatus = true;
                }else{
                    $newPost->add_orderStatus = false;
                }
                if($request->edit_orderStatus == 1){
                    $newPost->edit_orderStatus = true;
                }else{
                    $newPost->edit_orderStatus = false;
                }
                if($request->delete_orderStatus == 1){
                    $newPost->delete_orderStatus = true;
                }else{
                    $newPost->delete_orderStatus = false;
                }
                if($request->view_orderStatus == 1){
                    $newPost->view_orderStatus = true;
                }else{
                    $newPost->view_orderStatus = false;
                }
                if($request->upload_docs == 1){
                    $newPost->upload_docs = true;
                }else{
                    $newPost->upload_docs = false;
                }
                if($request->add_service_charge == 1){
                    $newPost->add_service_charge = true;
                }else{
                    $newPost->add_service_charge = false;
                }
                if($request->edit_service_charge == 1){
                    $newPost->edit_service_charge = true;
                }else{
                    $newPost->edit_service_charge = false;
                }
                if($request->updt_status_service_charge == 1){
                    $newPost->updt_status_service_charge = true;
                }else{
                    $newPost->updt_status_service_charge = false;
                }
                if($request->delete_service_charge == 1){
                    $newPost->delete_service_charge = true;
                }else{
                    $newPost->delete_service_charge = false;
                }
                if($request->candidate_status == 1){
                    $newPost->candidate_status = true;
                }else{
                    $newPost->candidate_status = false;
                }
                if($request->candidate_reset_status == 1){
                    $newPost->candidate_reset_status = true;
                }else{
                    $newPost->candidate_reset_status = false;
                }
                if($request->delete_cand_payment == 1){
                    $newPost->delete_cand_payment = true;
                }else{
                    $newPost->delete_cand_payment = false;
                }
                if($request->edit_cand_payment == 1){
                    $newPost->edit_cand_payment = true;
                }else{
                    $newPost->edit_cand_payment = false;
                }
                if($request->cand_delete_file == 1){
                    $newPost->cand_delete_file = true;
                }else{
                    $newPost->cand_delete_file = false;
                }
                if($request->cand_edit_careoff == 1){
                    $newPost->cand_edit_careoff = true;
                }else{
                    $newPost->cand_edit_careoff = false;
                }
                if($request->cand_edit_associate == 1){
                    $newPost->cand_edit_associate = true;
                }else{
                    $newPost->cand_edit_associate = false;
                }
                if($request->activedeactive_associate == 1){
                    $newPost->activedeactive_associate = true;
                }else{
                    $newPost->activedeactive_associate = false;
                }
                if($request->contactp == 1){
                    $newPost->contactp = true;
                }else{
                    $newPost->contactp = false;
                }
                if($request->add_contactp == 1){
                    $newPost->add_contactp = true;
                }else{
                    $newPost->add_contactp = false;
                }
                if($request->edit_contactp == 1){
                    $newPost->edit_contactp = true;
                }else{
                    $newPost->edit_contactp = false;
                }
                if($request->view_contactp == 1){
                    $newPost->view_contactp = true;
                }else{
                    $newPost->view_contactp = false;
                }
                if($request->delete_contactp == 1){
                    $newPost->delete_contactp = true;
                }else{
                    $newPost->delete_contactp = false;
                }

                if($request->careoff_transfer == 1){
                    $newPost->careoff_transfer = true;
                }else{
                    $newPost->careoff_transfer = false;
                }

                if($request->lead_owner_transfer == 1){
                    $newPost->lead_owner_transfer = true;
                }else{
                    $newPost->lead_owner_transfer = false;
                }

                if($request->whatsapp_plus == 1){
                    $newPost->whatsapp_plus = true;
                }else{
                    $newPost->whatsapp_plus = false;
                }

                if($request->sms_campaign_module == 1){
                    $newPost->sms_campaign_module = true;
                }else{
                    $newPost->sms_campaign_module = false;
                }

                if($request->sms_api == 1){
                    $newPost->sms_api = true;
                }else{
                    $newPost->sms_api = false;
                }

                if($request->add_sms_api == 1){
                    $newPost->add_sms_api = true;
                }else{
                    $newPost->add_sms_api = false;
                }

                if($request->edit_sms_api == 1){
                    $newPost->edit_sms_api = true;
                }else{
                    $newPost->edit_sms_api = false;
                }

                if($request->delete_sms_api == 1){
                    $newPost->delete_sms_api = true;
                }else{
                    $newPost->delete_sms_api = false;
                }

                if($request->change_status_sms_api == 1){
                    $newPost->change_status_sms_api = true;
                }else{
                    $newPost->change_status_sms_api = false;
                }

                if($request->assign_sms_api == 1){
                    $newPost->assign_sms_api = true;
                }else{
                    $newPost->assign_sms_api = false;
                }

                if($request->sms_template == 1){
                    $newPost->sms_template = true;
                }else{
                    $newPost->sms_template = false;
                }

                if($request->add_sms_template == 1){
                    $newPost->add_sms_template = true;
                }else{
                    $newPost->add_sms_template = false;
                }

                if($request->edit_sms_template == 1){
                    $newPost->edit_sms_template = true;
                }else{
                    $newPost->edit_sms_template = false;
                }

                if($request->delete_sms_template == 1){
                    $newPost->delete_sms_template = true;
                }else{
                    $newPost->delete_sms_template = false;
                }

                if($request->change_status_sms_template == 1){
                    $newPost->change_status_sms_template = true;
                }else{
                    $newPost->change_status_sms_template = false;
                }

                if($request->sms_campaign == 1){
                    $newPost->sms_campaign = true;
                }else{
                    $newPost->sms_campaign = false;
                }

                if($request->add_sms_campaign == 1){
                    $newPost->add_sms_campaign = true;
                }else{
                    $newPost->add_sms_campaign = false;
                }

                if($request->view_sms_campaign == 1){
                    $newPost->view_sms_campaign = true;
                }else{
                    $newPost->view_sms_campaign = false;
                }

                if($request->delete_sms_campaign == 1){
                    $newPost->delete_sms_campaign = true;
                }else{
                    $newPost->delete_sms_campaign = false;
                }

                if($request->image_host == 1){
                    $newPost->image_host = true;
                }else{
                    $newPost->image_host = false;
                }

                if($request->expworklocation == 1){
                    $newPost->expworklocation = true;
                }else{
                    $newPost->expworklocation = false;
                }

                if($request->add_expworklocation == 1){
                    $newPost->add_expworklocation = true;
                }else{
                    $newPost->add_expworklocation = false;
                }

                if($request->edit_expworklocation == 1){
                    $newPost->edit_expworklocation = true;
                }else{
                    $newPost->edit_expworklocation = false;
                }

                if($request->view_expworklocation == 1){
                    $newPost->view_expworklocation = true;
                }else{
                    $newPost->view_expworklocation = false;
                }

                if($request->delete_expworklocation == 1){
                    $newPost->delete_expworklocation = true;
                }else{
                    $newPost->delete_expworklocation = false;
                }

                if($request->businesstype == 1){
                    $newPost->businesstype = true;
                }else{
                    $newPost->businesstype = false;
                }

                if($request->add_businesstype == 1){
                    $newPost->add_businesstype = true;
                }else{
                    $newPost->add_businesstype = false;
                }

                if($request->edit_businesstype == 1){
                    $newPost->edit_businesstype = true;
                }else{
                    $newPost->edit_businesstype = false;
                }

                if($request->view_businesstype == 1){
                    $newPost->view_businesstype = true;
                }else{
                    $newPost->view_businesstype = false;
                }

                if($request->delete_businesstype == 1){
                    $newPost->delete_businesstype = true;
                }else{
                    $newPost->delete_businesstype = false;
                }

                if($request->groupcp == 1){
                    $newPost->groupcp = true;
                }else{
                    $newPost->groupcp = false;
                }

                if($request->add_groupcp == 1){
                    $newPost->add_groupcp = true;
                }else{
                    $newPost->add_groupcp = false;
                }

                if($request->edit_groupcp == 1){
                    $newPost->edit_groupcp = true;
                }else{
                    $newPost->edit_groupcp = false;
                }

                if($request->view_groupcp == 1){
                    $newPost->view_groupcp = true;
                }else{
                    $newPost->view_groupcp = false;
                }

                if($request->delete_groupcp == 1){
                    $newPost->delete_groupcp = true;
                }else{
                    $newPost->delete_groupcp = false;
                }

                if($request->meta_whatsapp_template == 1){
                    $newPost->meta_whatsapp_template = true;
                }else{
                    $newPost->meta_whatsapp_template = false;
                }

                if($request->add_meta_whatsapp_template == 1){
                    $newPost->add_meta_whatsapp_template = true;
                }else{
                    $newPost->add_meta_whatsapp_template = false;
                }

                if($request->edit_meta_whatsapp_template == 1){
                    $newPost->edit_meta_whatsapp_template = true;
                }else{
                    $newPost->edit_meta_whatsapp_template = false;
                }

                if($request->delete_meta_whatsapp_template == 1){
                    $newPost->delete_meta_whatsapp_template = true;
                }else{
                    $newPost->delete_meta_whatsapp_template = false;
                }

                if($request->status_meta_whatsapp_template == 1){
                    $newPost->status_meta_whatsapp_template = true;
                }else{
                    $newPost->status_meta_whatsapp_template = false;
                }

                if($request->public_meta_whatsapp_template == 1){
                    $newPost->public_meta_whatsapp_template = true;
                }else{
                    $newPost->public_meta_whatsapp_template = false;
                }

                if($request->meta_whatsapp_campaign == 1){
                    $newPost->meta_whatsapp_campaign = true;
                }else{
                    $newPost->meta_whatsapp_campaign = false;
                }

                if($request->meta_whatsapp_campaign_view == 1){
                    $newPost->meta_whatsapp_campaign_view = true;
                }else{
                    $newPost->meta_whatsapp_campaign_view = false;
                }

                if($request->whatsapp_nromal == 1){
                    $newPost->whatsapp_nromal = true;
                }else{
                    $newPost->whatsapp_nromal = false;
                }

                if($request->whatsapp_template == 1){
                    $newPost->whatsapp_template = true;
                }else{
                    $newPost->whatsapp_template = false;
                }

                if($request->add_whatsapp_template == 1){
                    $newPost->add_whatsapp_template = true;
                }else{
                    $newPost->add_whatsapp_template = false;
                }

                if($request->edit_whatsapp_template == 1){
                    $newPost->edit_whatsapp_template = true;
                }else{
                    $newPost->edit_whatsapp_template = false;
                }

                if($request->view_whatsapp_template == 1){
                    $newPost->view_whatsapp_template = true;
                }else{
                    $newPost->view_whatsapp_template = false;
                }

                if($request->delete_whatsapp_template == 1){
                    $newPost->delete_whatsapp_template = true;
                }else{
                    $newPost->delete_whatsapp_template = false;
                }

                if($request->status_whatsapp_template == 1){
                    $newPost->status_whatsapp_template = true;
                }else{
                    $newPost->status_whatsapp_template = false;
                }

                if($request->public_whatsapp_template == 1){
                    $newPost->public_whatsapp_template = true;
                }else{
                    $newPost->public_whatsapp_template = false;
                }

                if($request->whatsapp_campaign == 1){
                    $newPost->whatsapp_campaign = true;
                }else{
                    $newPost->whatsapp_campaign = false;
                }

                if($request->whatsapp_campaign_view == 1){
                    $newPost->whatsapp_campaign_view = true;
                }else{
                    $newPost->whatsapp_campaign_view = false;
                }

                if($request->contact_bulk_whatsapp_send == 1){
                    $newPost->contact_bulk_whatsapp_send = true;
                }else{
                    $newPost->contact_bulk_whatsapp_send = false;
                }

                if($request->whatsapp_campaign_add == 1){
                    $newPost->whatsapp_campaign_add = true;
                }else{
                    $newPost->whatsapp_campaign_add = false;
                }

                if($request->meta_whatsapp_campaign_add == 1){
                    $newPost->meta_whatsapp_campaign_add = true;
                }else{
                    $newPost->meta_whatsapp_campaign_add = false;
                }

                if($request->view_meta_whatsapp_template == 1){
                    $newPost->view_meta_whatsapp_template = true;
                }else{
                    $newPost->view_meta_whatsapp_template = false;
                }

                if($request->access_setting == 1){
                    $newPost->access_setting = true;
                }else{
                    $newPost->access_setting = false;
                }

                if($request->access_allowed_ip == 1){
                    $newPost->access_allowed_ip = true;
                }else{
                    $newPost->access_allowed_ip = false;
                }

                if($request->access_allowed_ip_revoke == 1){
                    $newPost->access_allowed_ip_revoke = true;
                }else{
                    $newPost->access_allowed_ip_revoke = false;
                }

                if($request->access_allowed_ip_approved_by == 1){
                    $newPost->access_allowed_ip_approved_by = true;
                }else{
                    $newPost->access_allowed_ip_approved_by = false;
                }

                if($request->access_allowed_ip_delete == 1){
                    $newPost->access_allowed_ip_delete = true;
                }else{
                    $newPost->access_allowed_ip_delete = false;
                }

                if($request->todo_setting == 1){
                    $newPost->todo_setting = true;
                }else{
                    $newPost->todo_setting = false;
                }

                if($request->todo_label == 1){
                    $newPost->todo_label = true;
                }else{
                    $newPost->todo_label = false;
                }

                if($request->todo_label_add == 1){
                    $newPost->todo_label_add = true;
                }else{
                    $newPost->todo_label_add = false;
                }

                if($request->todo_label_view == 1){
                    $newPost->todo_label_view = true;
                }else{
                    $newPost->todo_label_view = false;
                }

                if($request->todo_label_edit == 1){
                    $newPost->todo_label_edit = true;
                }else{
                    $newPost->todo_label_edit = false;
                }

                if($request->todo_label_delete == 1){
                    $newPost->todo_label_delete = true;
                }else{
                    $newPost->todo_label_delete = false;
                }

                if($request->department == 1){
                    $newPost->department = true;
                }else{
                    $newPost->department = false;
                }

                if($request->department_add == 1){
                    $newPost->department_add = true;
                }else{
                    $newPost->department_add = false;
                }

                if($request->department_view == 1){
                    $newPost->department_view = true;
                }else{
                    $newPost->department_view = false;
                }

                if($request->department_edit == 1){
                    $newPost->department_edit = true;
                }else{
                    $newPost->department_edit = false;
                }

                if($request->department_delete == 1){
                    $newPost->department_delete = true;
                }else{
                    $newPost->department_delete = false;
                }

                if($request->todo == 1){
                    $newPost->todo = true;
                }else{
                    $newPost->todo = false;
                }

                if($request->todo_add == 1){
                    $newPost->todo_add = true;
                }else{
                    $newPost->todo_add = false;
                }

                if($request->todo_view == 1){
                    $newPost->todo_view = true;
                }else{
                    $newPost->todo_view = false;
                }

                if($request->todo_edit == 1){
                    $newPost->todo_edit = true;
                }else{
                    $newPost->todo_edit = false;
                }

                if($request->todo_delete == 1){
                    $newPost->todo_delete = true;
                }else{
                    $newPost->todo_delete = false;
                }

                if($request->todo_achieved == 1){
                    $newPost->todo_achieved = true;
                }else{
                    $newPost->todo_achieved = false;
                }

                if($request->deal_pipeline == 1){
                    $newPost->deal_pipeline = true;
                }else{
                    $newPost->deal_pipeline = false;
                }

                if($request->deal_add == 1){
                    $newPost->deal_add = true;
                }else{
                    $newPost->deal_add = false;
                }

                if($request->deal_view == 1){
                    $newPost->deal_view = true;
                }else{
                    $newPost->deal_view = false;
                }

                if($request->deal_edit == 1){
                    $newPost->deal_edit = true;
                }else{
                    $newPost->deal_edit = false;
                }

                if($request->deal_delete == 1){
                    $newPost->deal_delete = true;
                }else{
                    $newPost->deal_delete = false;
                }

                if($request->deal_update_stage == 1){
                    $newPost->deal_update_stage = true;
                }else{
                    $newPost->deal_update_stage = false;
                }

                if($request->deal_update_recruit_status == 1){
                    $newPost->deal_update_recruit_status = true;
                }else{
                    $newPost->deal_update_recruit_status = false;
                }

                if($request->deal_delete_files == 1){
                    $newPost->deal_delete_files = true;
                }else{
                    $newPost->deal_delete_files = false;
                }


                if($request->bulk_todo_update_status == 1){
                    $newPost->bulk_todo_update_status = true;
                }else{
                    $newPost->bulk_todo_update_status = false;
                }

                if($request->bulk_todo_update_priority == 1){
                    $newPost->bulk_todo_update_priority = true;
                }else{
                    $newPost->bulk_todo_update_priority = false;
                }

                if($request->bulk_todo_update_assignto == 1){
                    $newPost->bulk_todo_update_assignto = true;
                }else{
                    $newPost->bulk_todo_update_assignto = false;
                }
                if($request->bulk_todo_delete == 1){
                    $newPost->bulk_todo_delete = true;
                }else{
                    $newPost->bulk_todo_delete = false;
                }

                if($request->employerplus == 1){
                    $newPost->employerplus = true;
                }else{
                    $newPost->employerplus = false;
                }
                if($request->employerplus_add == 1){
                    $newPost->employerplus_add = true;
                }else{
                    $newPost->employerplus_add = false;
                }
                if($request->employerplus_view == 1){
                    $newPost->employerplus_view = true;
                }else{
                    $newPost->employerplus_view = false;
                }
                if($request->employerplus_edit == 1){
                    $newPost->employerplus_edit = true;
                }else{
                    $newPost->employerplus_edit = false;
                }
                if($request->employerplus_delete == 1){
                    $newPost->employerplus_delete = true;
                }else{
                    $newPost->employerplus_delete = false;
                }

                if($request->permission_setting == 1){
                    $newPost->permission_setting = true;
                }else{
                    $newPost->permission_setting = false;
                }

                if($request->contact_plus_setting == 1){
                    $newPost->contact_plus_setting = true;
                }else{
                    $newPost->contact_plus_setting = false;
                }

                if($request->finance == 1){
                    $newPost->finance = true;
                }else{
                    $newPost->finance = false;
                }

                if($request->candidate_finance == 1){
                    $newPost->candidate_finance = true;
                }else{
                    $newPost->candidate_finance = false;
                }

                if($request->transaction_finance == 1){
                    $newPost->transaction_finance = true;
                }else{
                    $newPost->transaction_finance = false;
                }

                if($request->transaction_client == 1){
                    $newPost->transaction_client = true;
                }else{
                    $newPost->transaction_client = false;
                }

                if($request->finance_sale_invoices == 1){
                    $newPost->finance_sale_invoices = true;
                }else{
                    $newPost->finance_sale_invoices = false;
                }

                if($request->finance_sale_invoices_create == 1){
                    $newPost->finance_sale_invoices_create = true;
                }else{
                    $newPost->finance_sale_invoices_create = false;
                }

                if($request->finance_sale_invoices_edit == 1){
                    $newPost->finance_sale_invoices_edit = true;
                }else{
                    $newPost->finance_sale_invoices_edit = false;
                }

                if($request->finance_sale_invoices_view == 1){
                    $newPost->finance_sale_invoices_view = true;
                }else{
                    $newPost->finance_sale_invoices_view = false;
                }

                if($request->finance_sale_invoices_delete == 1){
                    $newPost->finance_sale_invoices_delete = true;
                }else{
                    $newPost->finance_sale_invoices_delete = false;
                }

                if($request->finance_payment == 1){
                    $newPost->finance_payment = true;
                }else{
                    $newPost->finance_payment = false;
                }

                if($request->finance_payment_add == 1){
                    $newPost->finance_payment_add = true;
                }else{
                    $newPost->finance_payment_add = false;
                }

                if($request->finance_payment_edit == 1){
                    $newPost->finance_payment_edit = true;
                }else{
                    $newPost->finance_payment_edit = false;
                }

                if($request->finance_payment_delete == 1){
                    $newPost->finance_payment_delete = true;
                }else{
                    $newPost->finance_payment_delete = false;
                }

                if($request->hr_management == 1){
                    $newPost->hr_management = true;
                }else{
                    $newPost->hr_management = false;
                }

                if($request->hr_dashboard == 1){
                    $newPost->hr_dashboard = true;
                }else{
                    $newPost->hr_dashboard = false;
                }

                if($request->hr_attendance == 1){
                    $newPost->hr_attendance = true;
                }else{
                    $newPost->hr_attendance = false;
                }

                if($request->hr_attendance_view_all == 1){
                    $newPost->hr_attendance_view_all = true;
                }else{
                    $newPost->hr_attendance_view_all = false;
                }

                if($request->hr_payroll == 1){
                    $newPost->hr_payroll = true;
                }else{
                    $newPost->hr_payroll = false;
                }

                if($request->hr_settings == 1){
                    $newPost->hr_settings = true;
                }else{
                    $newPost->hr_settings = false;
                }

                if($request->fund_advance == 1){
                    $newPost->fund_advance = true;
                }else{
                    $newPost->fund_advance = false;
                }

                if($request->fund_advance_create == 1){
                    $newPost->fund_advance_create = true;
                }else{
                    $newPost->fund_advance_create = false;
                }

                if($request->fund_advance_edit == 1){
                    $newPost->fund_advance_edit = true;
                }else{
                    $newPost->fund_advance_edit = false;
                }

                if($request->fund_advance_delete == 1){
                    $newPost->fund_advance_delete = true;
                }else{
                    $newPost->fund_advance_delete = false;
                }

                if($request->fund_advance_settlement_create == 1){
                    $newPost->fund_advance_settlement_create = true;
                }else{
                    $newPost->fund_advance_settlement_create = false;
                }

                if($request->fund_advance_settlement_view == 1){
                    $newPost->fund_advance_settlement_view = true;
                }else{
                    $newPost->fund_advance_settlement_view = false;
                }

                if($request->fund_advance_adjustment_create == 1){
                    $newPost->fund_advance_adjustment_create = true;
                }else{
                    $newPost->fund_advance_adjustment_create = false;
                }

                if($request->fund_advance_ledger_view == 1){
                    $newPost->fund_advance_ledger_view = true;
                }else{
                    $newPost->fund_advance_ledger_view = false;
                }

                if($request->fund_advance_reports_view == 1){
                    $newPost->fund_advance_reports_view = true;
                }else{
                    $newPost->fund_advance_reports_view = false;
                }

                if($request->fund_advance_export == 1){
                    $newPost->fund_advance_export = true;
                }else{
                    $newPost->fund_advance_export = false;
                }


                if($request->industries == 1){
                    $newPost->industries = true;
                }else{
                    $newPost->industries = false;
                }

                if($request->add_industries == 1){
                    $newPost->add_industries = true;
                }else{
                    $newPost->add_industries = false;
                }

                if($request->edit_industries == 1){
                    $newPost->edit_industries = true;
                }else{
                    $newPost->edit_industries = false;
                }

                if($request->view_industries == 1){
                    $newPost->view_industries = true;
                }else{
                    $newPost->view_industries = false;
                }

                if($request->delete_industries == 1){
                    $newPost->delete_industries = true;
                }else{
                    $newPost->delete_industries = false;
                }


                if($request->leads == 1){
                    $newPost->leads = true;
                }else{
                    $newPost->leads = false;
                }

                if($request->leads_view == 1){
                    $newPost->leads_view = true;
                }else{
                    $newPost->leads_view = false;
                }

                if($request->leads_delete == 1){
                    $newPost->leads_delete = true;
                }else{
                    $newPost->leads_delete = false;
                }

                if($request->leads_assignto == 1){
                    $newPost->leads_assignto = true;
                }else{
                    $newPost->leads_assignto = false;
                }

                if($request->leads_bulk_delete == 1){
                    $newPost->leads_bulk_delete = true;
                }else{
                    $newPost->leads_bulk_delete = false;
                }

                if($request->leads_bulk_assignto == 1){
                    $newPost->leads_bulk_assignto = true;
                }else{
                    $newPost->leads_bulk_assignto = false;
                }


                if($request->setup == 1){
                    $newPost->setup = true;
                }else{
                    $newPost->setup = false;
                }

                if($request->whatsapp_setup == 1){
                    $newPost->whatsapp_setup = true;
                }else{
                    $newPost->whatsapp_setup = false;
                }

                if($request->whatsapp_api == 1){
                    $newPost->whatsapp_api = true;
                }else{
                    $newPost->whatsapp_api = false;
                }

                if($request->add_whatsapp_api == 1){
                    $newPost->add_whatsapp_api = true;
                }else{
                    $newPost->add_whatsapp_api = false;
                }

                if($request->edit_whatsapp_api == 1){
                    $newPost->edit_whatsapp_api = true;
                }else{
                    $newPost->edit_whatsapp_api = false;
                }

                if($request->view_whatsapp_api == 1){
                    $newPost->view_whatsapp_api = true;
                }else{
                    $newPost->view_whatsapp_api = false;
                }

                if($request->delete_whatsapp_api == 1){
                    $newPost->delete_whatsapp_api = true;
                }else{
                    $newPost->delete_whatsapp_api = false;
                }

                if($request->whatsapp_api_test == 1){
                    $newPost->whatsapp_api_test = true;
                }else{
                    $newPost->whatsapp_api_test = false;
                }

                if($request->whatsapp_meta_api == 1){
                    $newPost->whatsapp_meta_api = true;
                }else{
                    $newPost->whatsapp_meta_api = false;
                }

                if($request->add_whatsapp_meta_api == 1){
                    $newPost->add_whatsapp_meta_api = true;
                }else{
                    $newPost->add_whatsapp_meta_api = false;
                }

                if($request->edit_whatsapp_meta_api == 1){
                    $newPost->edit_whatsapp_meta_api = true;
                }else{
                    $newPost->edit_whatsapp_meta_api = false;
                }

                if($request->view_whatsapp_meta_api == 1){
                    $newPost->view_whatsapp_meta_api = true;
                }else{
                    $newPost->view_whatsapp_meta_api = false;
                }

                if($request->delete_whatsapp_meta_api == 1){
                    $newPost->delete_whatsapp_meta_api = true;
                }else{
                    $newPost->delete_whatsapp_meta_api = false;
                }

                // Whatsapp URL
                if ($request->whatsapp_url == 1) {
                    $newPost->whatsapp_url = true;
                } else {
                    $newPost->whatsapp_url = false;
                }

                // Add Whatsapp Meta API
                if ($request->add_whatsapp_url == 1) {
                    $newPost->add_whatsapp_url = true;
                } else {
                    $newPost->add_whatsapp_url = false;
                }

                // Edit Whatsapp Meta API
                if ($request->edit_whatsapp_url == 1) {
                    $newPost->edit_whatsapp_url = true;
                } else {
                    $newPost->edit_whatsapp_url = false;
                }

                // View Whatsapp Meta API
                if ($request->view_whatsapp_url == 1) {
                    $newPost->view_whatsapp_url = true;
                } else {
                    $newPost->view_whatsapp_url = false;
                }

                // Delete Whatsapp Meta API
                if ($request->delete_whatsapp_url == 1) {
                    $newPost->delete_whatsapp_url = true;
                } else {
                    $newPost->delete_whatsapp_url = false;
                }


                if($request->add_whatsapp_api_for_user == 1){
                    $newPost->add_whatsapp_api_for_user = true;
                }else{
                    $newPost->add_whatsapp_api_for_user = false;
                }

                if($request->allcontact == 1){
                    $newPost->allcontact = true;
                }else{
                    $newPost->allcontact = false;
                }

                if($request->allcontact_add == 1){
                    $newPost->allcontact_add = true;
                }else{
                    $newPost->allcontact_add = false;
                }

                if($request->allcontact_edit == 1){
                    $newPost->allcontact_edit = true;
                }else{
                    $newPost->allcontact_edit = false;
                }

                if($request->allcontact_view == 1){
                    $newPost->allcontact_view = true;
                }else{
                    $newPost->allcontact_view = false;
                }

                if($request->allcontact_delete == 1){
                    $newPost->allcontact_delete = true;
                }else{
                    $newPost->allcontact_delete = false;
                }

                if($request->allcontact_bulk_transfer_group == 1){
                    $newPost->allcontact_bulk_transfer_group = true;
                }else{
                    $newPost->allcontact_bulk_transfer_group = false;
                }

                if($request->allcontact_bulk_transfer_careoff == 1){
                    $newPost->allcontact_bulk_transfer_careoff = true;
                }else{
                    $newPost->allcontact_bulk_transfer_careoff = false;
                }

                // if($request->allcontact_bulk_transfer_leadowner == 1){
                //     $newPost->allcontact_bulk_transfer_leadowner = true;
                // }else{
                //     $newPost->allcontact_bulk_transfer_leadowner = false;
                // }

                if($request->allcontact_bulk_send_whatsapp == 1){
                    $newPost->allcontact_bulk_send_whatsapp = true;
                }else{
                    $newPost->allcontact_bulk_send_whatsapp = false;
                }

                if($request->allcontact_delete == 1){
                    $newPost->allcontact_delete = true;
                }else{
                    $newPost->allcontact_delete = false;
                }

                if($request->expense == 1){
                    $newPost->expense = true;
                }else{
                    $newPost->expense = false;
                }

                if($request->add_expense == 1){
                    $newPost->add_expense = true;
                }else{
                    $newPost->add_expense = false;
                }

                if($request->edit_expense == 1){
                    $newPost->edit_expense = true;
                }else{
                    $newPost->edit_expense = false;
                }

                if($request->view_expense == 1){
                    $newPost->view_expense = true;
                }else{
                    $newPost->view_expense = false;
                }

                if($request->delete_expense == 1){
                    $newPost->delete_expense = true;
                }else{
                    $newPost->delete_expense = false;
                }

                if($request->file_manager == 1){
                    $newPost->file_manager = true;
                }else{
                    $newPost->file_manager = false;
                }

                if($request->add_file_manager == 1){
                    $newPost->add_file_manager = true;
                }else{
                    $newPost->add_file_manager = false;
                }

                if($request->upload_file_manager == 1){
                    $newPost->upload_file_manager = true;
                }else{
                    $newPost->upload_file_manager = false;
                }

                if($request->download_file_manager == 1){
                    $newPost->download_file_manager = true;
                }else{
                    $newPost->download_file_manager = false;
                }

                if($request->preview_file_manager == 1){
                    $newPost->preview_file_manager = true;
                }else{
                    $newPost->preview_file_manager = false;
                }

                if($request->rename_file_manager == 1){
                    $newPost->rename_file_manager = true;
                }else{
                    $newPost->rename_file_manager = false;
                }

                if($request->move_file_manager == 1){
                    $newPost->move_file_manager = true;
                }else{
                    $newPost->move_file_manager = false;
                }

                if($request->delete_file_manager == 1){
                    $newPost->delete_file_manager = true;
                }else{
                    $newPost->delete_file_manager = false;
                }

                if($request->manage_all_file_manager == 1){
                    $newPost->manage_all_file_manager = true;
                }else{
                    $newPost->manage_all_file_manager = false;
                }

                if($request->verified_notverified_associate == 1){
                    $newPost->verified_notverified_associate = true;
                }else{
                    $newPost->verified_notverified_associate = false;
                }

                if($request->primary_no_val_associate == 1){
                    $newPost->primary_no_val_associate = true;
                }else{
                    $newPost->primary_no_val_associate = false;
                }

                if($request->secondary_no_val_associate == 1){
                    $newPost->secondary_no_val_associate = true;
                }else{
                    $newPost->secondary_no_val_associate = false;
                }

                if($request->email_associate == 1){
                    $newPost->email_associate = true;
                }else{
                    $newPost->email_associate = false;
                }

                if($request->meta_whatsapp_campaign_create_user == 1){
                    $newPost->meta_whatsapp_campaign_create_user = true;
                }else{
                    $newPost->meta_whatsapp_campaign_create_user = false;
                }

                if($request->meta_automation == 1){
                    $newPost->meta_automation = true;
                }else{
                    $newPost->meta_automation = false;
                }

                if($request->add_meta_automation == 1){
                    $newPost->add_meta_automation = true;
                }else{
                    $newPost->add_meta_automation = false;
                }

                if($request->view_meta_automation == 1){
                    $newPost->view_meta_automation = true;
                }else{
                    $newPost->view_meta_automation = false;
                }

                if($request->edit_meta_automation == 1){
                    $newPost->edit_meta_automation = true;
                }else{
                    $newPost->edit_meta_automation = false;
                }

                if($request->delete_meta_automation == 1){
                    $newPost->delete_meta_automation = true;
                }else{
                    $newPost->delete_meta_automation = false;
                }

                if($request->cand_add_edit == 1){
                    $newPost->cand_add_edit = true;
                }else{
                    $newPost->cand_add_edit = false;
                }

                if($request->cand_sourcing_date == 1){
                    $newPost->cand_sourcing_date = true;
                }else{
                    $newPost->cand_sourcing_date = false;
                }

                if($request->allcontact_bulk_delete == 1){
                    $newPost->allcontact_bulk_delete = true;
                }else{
                    $newPost->allcontact_bulk_delete = false;
                }

                if($request->allcontact_bulk_country_update == 1){
                    $newPost->allcontact_bulk_country_update = true;
                }else{
                    $newPost->allcontact_bulk_country_update = false;
                }

                if($request->allcontact_bulk_country_code_update == 1){
                    $newPost->allcontact_bulk_country_code_update = true;
                }else{
                    $newPost->allcontact_bulk_country_code_update = false;
                }

                if($request->leads_candidate_view == 1){
                    $newPost->leads_candidate_view = true;
                }else{
                    $newPost->leads_candidate_view = false;
                }

                if($request->leads_employer_view == 1){
                    $newPost->leads_employer_view = true;
                }else{
                    $newPost->leads_employer_view = false;
                }

                if($request->allcontact_company_column == 1){
                    $newPost->allcontact_company_column = true;
                }else{
                    $newPost->allcontact_company_column = false;
                }

                if($request->leads_company_column == 1){
                    $newPost->leads_company_column = true;
                }else{
                    $newPost->leads_company_column = false;
                }

                if($request->update_status_achieved == 1){
                    $newPost->update_status_achieved = true;
                }else{
                    $newPost->update_status_achieved = false;
                }


                $newPost->save();
            }
        }

        return redirect()->back()->with([
            'success' => 'Permission granted to the staff!',
            'staffid' => $request->staff_id,
        ]);
        
    }

    public function carknown()
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        return view('admin.carknown',['perm' => $permission]);
    }

    public function carknownjson(Request $request)
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $post = Carnknown::orderBy('id',"DESC")->get();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_car_known == 1){
                $post = Carnknown::orderBy('id',"DESC")->get();
            }else{
                $post = Carnknown::where('admin_id','=',Auth::guard('admin')->user()->id)->orderBy('id',"DESC")->get();
            }
        }

        $data['data'] = $post;
        return response()->json($data);

    }

    public function checkcarknown(Request $request)
    {
        $post = Carnknown::where('name','=',$request->name)->count();
        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edcheckcarknown(Request $request)
    {
        $post = Carnknown::where('name','=',$request->name)->where('id','!=',$request->id)->count();
        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function carknownUpdate(Request $request){
        $post = Carnknown::find($request->edit_id);
        $post->name = $request->name;
        $post->save();

        return redirect()->back()->with('success','Car name updated!');
    }

    public function carknownStore(Request $request)
    {
        $post = new Carnknown();
        $post->name = $request->name;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Car added!');
    }

    public function editcar(Request $request){
        $post = Carnknown::find($request->id);

        return response()->json($post);
    }

    public function personaliseclass()
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        return view('admin.personalise.index',['perm' => $permission]);
    }


    public function personaliseclassJson(Request $request)
    {

        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $post = Personaliseclass::all();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_personalise_class == 1){
                $post = Personaliseclass::all();
            }else{
                $post = Personaliseclass::where('admin_id','=',Auth::guard('admin')->user()->id)->get();
            }
        }



        $data['data'] = $post;
        return response()->json($data);
    }

    public function personaliseclassEdit(Request $request){
        $post = Personaliseclass::find($request->id);

        return response()->json($post);
    }



    public function checkPersonalise(Request $request)
    {
        $post = Personaliseclass::where('name','=',$request->name)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));

    }

    public function edcheckPersonalise(Request $request)
    {
        $post = Personaliseclass::where('name','=',$request->name)->where('id','!=',$request->id)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));

    }

    public function personaliseStr(Request $request)
    {
        $post = new Personaliseclass();
        $post->name = $request->name;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Personalise class stored!');
    }

    public function personaliseUpdate(Request $request){
        $post = Personaliseclass::find($request->edit_id);
        $post->name = $request->name;
        $post->save();

        return redirect()->back()->with('success','Personalise class updated!');
    }

    public function personaliseDelete(Request $request){
        $post = Personaliseclass::find($request->proff_ids);
        $post->delete();

        return redirect()->back()->with('success','Personalise class deleted!');
    }

    public function templateList()
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        $personalises = Personaliseclass::orderBy('name','ASC')->get();
        return view('admin.template.index',['personalises' => $personalises,'perm' => $permission]);
    }

    public function templateDel(Request $request){
        $post = Templatecampaign::find($request->proff_ids);
        $post->delete();

        return redirect()->back()->with('success','Template deleted!');
    }

    public function templateActive(Request $request){
        $post = Templatecampaign::find($request->tempactID);
        $post->status = true;
        $post->save();

        return redirect()->back()->with('success','Status active!');
    }

    public function templateDeactive(Request $request){
        $post = Templatecampaign::find($request->tempdeactID);
        $post->status = false;
        $post->save();

        return redirect()->back()->with('success','Status deacttive!');
    }

    public function temppubget(Request $request){
        $post = Templatecampaign::find($request->id);
        return response()->json($post);
    }

    public function tempstatusget(Request $request){
        $post = Templatecampaign::find($request->id);
        return response()->json($post);
    }

    public function templatepubStatus(Request $request){
        $post = Templatecampaign::find($request->temppubID);
        $post->public = $request->publish_st;
        $post->save();
        return redirect()->back()->with('success','Publish status has been changed!');
    }

    public function tempchangestatus(Request $request){
        $post = Templatecampaign::find($request->tempchstID);
        $post->status = $request->status;
        $post->save();

        return redirect()->back()->with('success','Statys has been changed!');
    }


    public function templateListJson(Request $request)
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $post = DB::table('templatenotification as temp')
            ->leftJoin('admins as admin','temp.staff_id','=','admin.id')
            // ->where('temp.template_for','!=',2)
            ->select('temp.*','admin.name as uname')
            ->get();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_template == 1){
                $post = DB::table('templatenotification as temp')
                ->leftJoin('admins as admin','temp.staff_id','=','admin.id')
                // ->where('temp.template_for','!=',2)
                ->select('temp.*','admin.name as uname')
                ->get();
            }else{
                $post = DB::table('templatenotification as temp')
                ->leftJoin('admins as admin','temp.staff_id','=','admin.id')
                ->select('temp.*','admin.name as uname')
                ->where('temp.staff_id','=',Auth::guard('admin')->user()->id)
                // ->where('temp.template_for','!=',2)
                ->get();
            }
        }



        $data['data'] = $post;
        return response()->json($data);
    }

    public function templateStore(Request $request)
    {
        $basepathstatus = Basepathstatus::first();
        if ($request->has('photo')) {
            $file = $request->file('photo');
            $name = $file->getClientOriginalName();
            // remove space from image
            $filename_ren = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ","_",$filename_ren);
            $new_file = $repspfilename.'.'.$fileext_ren;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/template',$new_file);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/template',$new_file);
            }

            $temp_file = $new_file;
        } else {
            $temp_file = '';
        }

        $post = new Templatecampaign();
        $post->template_name = $request->template_name;
        $post->subject_name = $request->subject_name;
        $post->msg_whatsapp = $request->msg_whatsapp;
        $post->msg_whatsapp_ar = $request->msg_whatsapp_ar;
        if($request->msg_email != ''){
            $post->msg_email = html_entity_decode($request->msg_email);
        }
        $post->msg_sms = $request->msg_sms;
        $post->file = $temp_file;
        if ($request->public == 1) {
            $post->public = true;
        } else {
            $post->public = false;
        }

        $post->staff_id = Auth::guard('admin')->user()->id;
        $post->template_for = 1;
        $post->save();

        return redirect()->back()->with('success','Template created!');

    }

    public function templateEdit(Request $request)
    {
        $post = Templatecampaign::find($request->id);

        return response()->json($post);
    }


    public function templateUpdt(Request $request){
        $post = Templatecampaign::find($request->edit_id);
        $basepathstatus = Basepathstatus::first();

        if ($request->has('photo')) {
            $file = $request->file('photo');
            $name = $file->getClientOriginalName();
            // remove space from image
            $filename_ren = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ","_",$filename_ren);
            $new_file = $repspfilename.'.'.$fileext_ren;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/template',$new_file);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/template',$new_file);
            }

            $temp_file = $new_file;
        } else {
            $temp_file = $post->file;
        }

        $post->template_name = $request->template_name;
        $post->subject_name = $request->subject_name;
        $post->msg_whatsapp = $request->msg_whatsapp;
        $post->msg_whatsapp_ar = $request->msg_whatsapp_ar;
        if($request->msg_email != ''){
            $post->msg_email = html_entity_decode($request->msg_email);
        }
        // $post->msg_email = $request->msg_email;
        $post->msg_sms = $request->msg_sms;
        $post->file = $temp_file;
        if ($request->public == 1) {
            $post->public = true;
        } else {
            $post->public = false;
        }

        $post->staff_id = Auth::guard('admin')->user()->id;
        $post->template_for = 1;
        $post->save();

        return redirect()->back()->with('success','Template updated!');

    }

    public function cardelexist(Request $request){
        $data1 = Candidate::whereRaw('FIND_IN_SET("'.$request->id.'",carknown_id)')->count();

        if($data1 > 0){
            return response()->json('1');
        }else{

        }

    }

    public function cardelete(Request $request){
        $post = Carnknown::find($request->proff_ids);
        $post->delete();

        return redirect()->back()->with('success','Car deleted!');
    }

    public function orderrep(){
        $staff = Admin::where('status', 1)->orderBy('name','ASC')->get();
        return view('admin.orderrp',['users' => $staff]);
    }

    public function orderrepJson(Request $request){
        $post = DB::table('orderrecivepanels as orp')
            ->leftjoin('admins as admin','orp.staff_id','=','admin.id')
            ->select('orp.*','admin.name as uname','admin.profile as avatar')
            ->get();

        $data['data'] = $post;
        return response()->json($data);
    }

    public function orderrepcheck(Request $request){
        $post = Orderrecivepanel::where('staff_id','=',$request->staff_id)->first();
        return response()->json($post);
    }

    public function orderrepupdate(Request $request){
        if($request->orderrc_id != ''){
            $post = Orderrecivepanel::find($request->orderrc_id);
            $post->staff_id = $request->staff_id;
            $post->email = $request->email;
            $post->mobile = $request->mobile;
            $post->status = $request->status;
            $post->save();
        }else{
            $post = new Orderrecivepanel();
            $post->staff_id = $request->staff_id;
            $post->email = $request->email;
            $post->mobile = $request->mobile;
            $post->assignby_id = Auth::guard('admin')->user()->id;
            $post->status = $request->status;
            $post->save();
        }

        return redirect()->back()->with('success','Order receieved panel assign to staff');
    }

    public function custcostdepList(){
        $profesions = Profession::orderBy('eng_name')->get();
        return view('admin.customer.index',compact('profesions'));
    }

    public function custcostdepListJson(Request $request){
        $post = DB::table('customercosts as custcost')
        ->leftjoin('professions as proff','custcost.proff_id','=','proff.id')
        ->leftjoin('admins as admin','custcost.admin_id','=','admin.id')
        ->select('custcost.*','admin.name as uname','proff.eng_name as pengname')
        ->get();

        $data['data'] = $post;

        return response()->json($data);
    }

    public function custcostdepListStr(Request $request){

        // $existRecord = Customercost::where('status','=',1)->first();
        $existRecord = Customercost::where('exp_type','=',$request->exp_type)->where('proff_id','=',$request->proff_id)->first();

        if($existRecord){
            $existRecord->status = false;
            $existRecord->save();
        }

        $post = new Customercost();
        $post->cost = $request->cost;
        $post->days = $request->days;
        $post->exp_type = $request->exp_type;
        $post->proff_id = $request->proff_id;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Cost has been created!');
    }

    public function custcostdepListEdit(Request $request){
        $post = Customercost::find($request->id);

        return response()->json($post);
    }

    public function custcostdepListUpdt(Request $request){
        $post = Customercost::find($request->edit_id);
        $post->cost = $request->cost;
        $post->days = $request->days;
        $post->exp_type = $request->exp_type;
        $post->proff_id = $request->proff_id;
        $post->save();

        return redirect()->back()->with('success','Cost has been updated!');
    }

    public function bookingRequirement(){
        return view('admin.booking.requirement');
    }

    public function bookingRequirementStr(Request $request){
        $post = new RequirementInfo();
        if($request->language_type == 1){
            $post->requirement_text = $request->requirement_eng;
            $post->language_type = 1;
        }else{
            $post->requirement_text = $request->requirement_ar;
            $post->language_type = 2;
        }


        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Requirement Added Succesfully!');
    }

    public function checkRequirementEng(Request $request){
        $post = DB::table('requirement_info')->where('requirement_text','=',$request->requirement_text)->where('language_type','=',1)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function checkRequirementAr(Request $request){
        $post = DB::table('requirement_info')->where('requirement_text','=',$request->requirement_text)->where('language_type','=',2)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function groupmList(){
        return view('admin.groupm.index');
    }

    public function groupmListJson(Request $request){
        $post = Groupm::orderBy('name')->get();
        $data['data'] = $post;
        return response()->json($data);
    }

    public function groupmListstore(Request $request){
        $post = new Groupm();
        $post->name = $request->name;
        $post->max_limit = $request->max_limit;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Groupm created!');
    }

    public function groupmListedit(Request $request){
        $post = Groupm::find($request->id);

        return response()->json($post);
    }

    public function groupmListupdate(Request $request){

        $post = Groupm::find($request->edit_id);
        $post->name = $request->name;
        $post->max_limit = $request->max_limit;
        $post->save();

        return redirect()->back()->with('success','Groupm updated!');

    }

    public function groupmcheckname(Request $request){
        $name = $request->name;
        if(isset($request->id) && $request->id != ''){
            $post = Groupm::where('name','=',$name)->where('id','!=',$request->id)->count();

            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));

        }else{
            $post = Groupm::where('name','=',$name)->count();
            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));

        }

    }


    public function sync_contact_to_lead(Request $request)
    {
        return view('admin.sync_contact_to_lead.index');
    }

    public function jsonData(Request $request)
    {
        $posts = DB::table('sync_allcontact_to_lead')
            ->select('allcontact_id', 'lead_id', 'primary_no_wsp')
            ->orderByDesc('id');

        return DataTables::of($posts)->make(true);
    }


    public function contact_export_history(Request $request)
    {
        return view('admin.contact_export_history.index');
    }

    public function contact_export_history_json(Request $request)
    {
        $posts = DB::table('export_all_contact_histories');

        if(Auth::guard('admin')->user()->user_type == 1){
            $posts->orderByDesc('id');
        }else{
            $posts->where('admin_id', Auth::guard('admin')->user()->id)->orderByDesc('id');
        }

        return DataTables::of($posts)

            ->addColumn('progress', function ($row) {
                return $row->exported_records . ' / ' . $row->total_records;
            })

            ->editColumn('status', function ($row) {

                if ($row->status == 'pending') {
                    return '<span class="badge bg-label-warning">Pending</span>';
                }

                if ($row->status == 'processing') {
                    return '<span class="badge bg-label-info">Processing</span>';
                }

                if ($row->status == 'completed') {
                    return '<span class="badge bg-label-success">Completed</span>';
                }

                if ($row->status == 'failed') {
                    return '<span class="badge bg-label-danger">Failed</span>';
                }

                return $row->status;
            })

            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('d M Y h:i A');
            })

           ->addColumn('action', function ($row) {

                $buttons = '';
                if ($row->status == 'completed' && !empty($row->file_path)) {

                    $buttons .= '<a href="'.route('admin.contact_export_history.download', $row->id).'"
                                    class="btn btn-sm btn-primary me-1">
                                    <i class="ti ti-download"></i>
                                </a>';
                }

                $buttons .= '<button type="button"
                                class="btn btn-sm btn-danger delete-export"
                                data-id="'.$row->id.'">
                                <i class="ti ti-trash"></i>
                            </button>';

                return $buttons;
            })

            ->rawColumns(['status', 'action'])

            ->make(true);
    }

    public function deleteExportHistory($id)
    {
        $export = ExportAllContactHistory::findOrFail($id);

        if (!empty($export->file_path)) {

            $basepathstatus = Basepathstatus::first();

            $basePath = ($basepathstatus && $basepathstatus->base_path_status == 1)
                ? base_path('public')
                : base_path('public_html');

            $fullPath = rtrim($basePath, '/') . '/' . ltrim($export->file_path, '/');

            if (is_file($fullPath)) {
                unlink($fullPath);
            }
        }

        $export->delete();

        return response()->json([
            'status' => true,
            'message' => 'Export deleted successfully.'
        ]);
    }

    public function downloadExport($id)
    {

        $export = ExportAllContactHistory::findOrFail($id);

        $basepathstatus = Basepathstatus::first();

        $basePath = ($basepathstatus && $basepathstatus->base_path_status == 1)
            ? base_path('public')
            : base_path('public_html');

        $file = $basePath.'/'.$export->file_path;

        if (!file_exists($file)) {
            abort(404);
        }

        return response()->download(
            $file,
            $export->file_name,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }

    
    public function contact_plus_export_history(Request $request){
        
        return view('admin.contact_plus_export_history.index');
    }

    public function contact_plus_export_history_json(Request $request)
    {
        $posts = DB::table('export_contact_plus_histories');

        if(Auth::guard('admin')->user()->user_type == 1){
            $posts->orderByDesc('id');
        }else{
            $posts->where('admin_id', Auth::guard('admin')->user()->id)->orderByDesc('id');
        }

        return DataTables::of($posts)

            ->addColumn('progress', function ($row) {
                return $row->exported_records . ' / ' . $row->total_records;
            })

            ->editColumn('status', function ($row) {

                if ($row->status == 'pending') {
                    return '<span class="badge bg-label-warning">Pending</span>';
                }

                if ($row->status == 'processing') {
                    return '<span class="badge bg-label-info">Processing</span>';
                }

                if ($row->status == 'completed') {
                    return '<span class="badge bg-label-success">Completed</span>';
                }

                if ($row->status == 'failed') {
                    return '<span class="badge bg-label-danger">Failed</span>';
                }

                return $row->status;
            })

            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('d M Y h:i A');
            })

           ->addColumn('action', function ($row) {

                $buttons = '';
                if ($row->status == 'completed' && !empty($row->file_path)) {

                    $buttons .= '<a href="'.route('admin.contact_plus_export_history.download', $row->id).'"
                                    class="btn btn-sm btn-primary me-1">
                                    <i class="ti ti-download"></i>
                                </a>';
                }

                $buttons .= '<button type="button"
                                class="btn btn-sm btn-danger delete-export"
                                data-id="'.$row->id.'">
                                <i class="ti ti-trash"></i>
                            </button>';

                return $buttons;
            })

            ->rawColumns(['status', 'action'])

            ->make(true);
    }

      public function deleteExportPlusHistory($id)
    {
        $export = ExportContactPlusHistory::findOrFail($id);

        if (!empty($export->file_path)) {

            $basepathstatus = Basepathstatus::first();

            $basePath = ($basepathstatus && $basepathstatus->base_path_status == 1)
                ? base_path('public')
                : base_path('public_html');

            $fullPath = rtrim($basePath, '/') . '/' . ltrim($export->file_path, '/');

            if (is_file($fullPath)) {
                unlink($fullPath);
            }
        }

        $export->delete();

        return response()->json([
            'status' => true,
            'message' => 'Export deleted successfully.'
        ]);
    }

    public function downloadExportPlus($id)
    {
        
        $export = ExportContactPlusHistory::findOrFail($id);

        $basepathstatus = Basepathstatus::first();

        $basePath = ($basepathstatus && $basepathstatus->base_path_status == 1)
            ? base_path('public')
            : base_path('public_html');

        $file = $basePath.'/'.$export->file_path;

        if (!file_exists($file)) {
            abort(404);
        }

        return response()->download(
            $file,
            $export->file_name,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }




}
