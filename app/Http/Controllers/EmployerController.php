<?php

namespace App\Http\Controllers;

use App\Models\Adminpermission;
use App\Models\City;
use App\Models\Employefilterlist;
use App\Models\Expecworkcity;
use App\Models\Partner;
use App\Models\Profession;
use App\Models\Visadetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Candidate;
use App\Models\Admin;
use App\Models\Basepathstatus;
use App\Models\Employercandidate;
use App\Models\Employer;
use App\Models\Employeradminsavefilter;
use App\Models\Employerplusadminsavefilter;
use App\Models\Employerplus;
use App\Helpers\Helper;
use TCPDF_FONTS;
use PDF;

class EmployerController extends Controller
{
    // For Employer Section Start
    public function index(Request $request) {
        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id','=',$user->id)->first();
        $filter_user = Employefilterlist::where('admin_id','=',$user->id)->first();
        $staffs = Admin::where('status','=',1)->orderBy('name')->get();

        $profession = Profession::orderBy('eng_name')->get();
        $expworkcity = Expecworkcity::orderBy('name')->get();
        $partner = Partner::orderBy('rec_off_name')->where('status','=',1)->get();

        // Filter List Start
        $saveEmployeradminsavefilter = Employeradminsavefilter::where('admin_id','=',$user->id)->first();
        $businesstypeFilters = Employer::whereNotNull('booking_id')->select('businesstype')->groupBy('businesstype')->whereNotNull('businesstype')->get();
        $wakalastatusFilters = Employer::whereNotNull('booking_id')->select('wakala_status')->groupBy('wakala_status')->whereNotNull('wakala_status')->get();
        $createByfilters = DB::table('employers as employer')
        ->leftjoin('admins as admin','admin.id','=','employer.admin_id')
        ->select('employer.admin_id','admin.name as adminName')
        ->groupBy('employer.admin_id','adminName')
        ->whereNotNull('employer.admin_id')
        ->whereNotNull('employer.booking_id')
        ->get();



        $careofffilters = DB::table('employers as employer')
        ->leftjoin('admins as admin','admin.id','=','employer.careoff_id')
        ->select('employer.careoff_id','admin.name as careoffName')
        ->groupBy('employer.careoff_id','careoffName')
        ->whereNotNull('employer.careoff_id')
        ->whereNotNull('employer.booking_id')
        ->get();

        $createByPartnerfilters = DB::table('employers as employer')
        ->leftjoin('partners as partner','partner.id','=','employer.partner_id')
        ->select('employer.partner_id','partner.rec_off_name as partnerName')
        ->groupBy('employer.partner_id','partnerName')
        ->whereNotNull('employer.partner_id')
        ->whereNotNull('employer.booking_id')
        ->get();

        $partnerofficefilters = DB::table('employers as employer')
        ->leftjoin('partners as partner','partner.id','=','employer.partneroffice_id')
        ->select('employer.partneroffice_id','partner.rec_off_name as partnerName')
        ->groupBy('employer.partneroffice_id','partnerName')
        ->whereNotNull('employer.partneroffice_id')
        ->whereNotNull('employer.booking_id')
        ->get();

        $cityworkfilters = DB::table('employers as employer')
        ->leftjoin('expecworkcities as expecworkcity','expecworkcity.id','=','employer.wpcity_id')
        ->select('employer.wpcity_id','expecworkcity.name as expworkname')
        ->groupBy('employer.wpcity_id','expworkname')
        ->whereNotNull('employer.booking_id')
        ->whereNotNull('employer.wpcity_id')
        ->get();



        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $posts = Employer::with(['partneroffice','wpcity','admin','careoff']);

            if ($request->ajax()) {
                $posts->filterProfession($request->by_profession)
                ->filterVisaIssuingAuthority($request->by_visa_issuing_authority)
                ->filterBusinessType($request->by_businesstype)
                ->filterCreatedBy($request->by_created_by)
                ->filterCareoff($request->by_careoff)
                ->filterWakalaStatus($request->by_wakalastatus)
                ->filterCreateByPartner($request->by_created_by_partner)
                ->filterpartnerOffice($request->by_partner_office)
                ->filterCityOfWork($request->by_city_work)
                ->filterStatus($request->by_status)
                ->filterDateRange('visa_date', $request->visa_date_range_picket)
                ->filterDateRange('visa_received_date', $request->visa_received_date_range_picket)
                ->filterSearchText($request->search_text);


                $employerLists = $posts->whereNotNull('booking_id')->orderBy('id', 'DESC')->paginate($request->page_list ?? 10)->withQueryString();

                return view('admin.employer.load2',['perm' => $permission,'employerLists' => $employerLists]);
            }

            if ($saveEmployeradminsavefilter) {
                $posts->filterProfession($saveEmployeradminsavefilter->proff_id)
                ->filterVisaIssuingAuthority($saveEmployeradminsavefilter->issuing_authority)
                ->filterBusinessType($saveEmployeradminsavefilter->businesstype)
                ->filterCreatedBy($saveEmployeradminsavefilter->createbyadmin)
                ->filterCareoff($saveEmployeradminsavefilter->careoff)
                ->filterWakalaStatus($saveEmployeradminsavefilter->wakala_status)
                ->filterCreateByPartner($saveEmployeradminsavefilter->createbypartner)
                ->filterpartnerOffice($saveEmployeradminsavefilter->partneroffice)
                ->filterCityOfWork($saveEmployeradminsavefilter->wpcity_id)
                ->filterDateRange('visa_date', $saveEmployeradminsavefilter->visa_date_range)
                ->filterDateRange('visa_received_date', $saveEmployeradminsavefilter->visa_received_date_range);
            }


            $employerLists = $posts->whereNotNull('booking_id')->orderBy('id', 'DESC')->paginate($request->page_list ?? 10)->withQueryString();

            $employerStatusSummary = $this->buildEmployerStatusSummary($permission);

            return view('admin.employer.index2',['employerLists' => $employerLists,'wakalastatusFilters' => $wakalastatusFilters,'cityworkfilters' => $cityworkfilters,'careofffilters' => $careofffilters,'partnerofficefilters' => $partnerofficefilters,'createByfilters' => $createByfilters,'createByPartnerfilters' => $createByPartnerfilters,'businesstypeFilters' => $businesstypeFilters,'saveEmployeradminsavefilter' => $saveEmployeradminsavefilter,'perm' => $permission,'staffs' => $staffs,'partners' => $partner,'professions' => $profession,'expworkcities' => $expworkcity,'filter_user' => $filter_user,'employerStatusSummary' => $employerStatusSummary]);
        }elseif (Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)) {
            if($permission->view_employer == 1){
                $posts = Employer::with(['partneroffice','wpcity','admin','careoff']);

                if ($request->ajax()) {
                    $posts->filterProfession($request->by_profession)
                    ->filterVisaIssuingAuthority($request->by_visa_issuing_authority)
                    ->filterBusinessType($request->by_businesstype)
                    ->filterCreatedBy($request->by_created_by)
                    ->filterCareoff($request->by_careoff)
                    ->filterWakalaStatus($request->by_wakalastatus)
                    ->filterCreateByPartner($request->by_created_by_partner)
                    ->filterpartnerOffice($request->by_partner_office)
                    ->filterCityOfWork($request->by_city_work)
                    ->filterStatus($request->by_status)
                    ->filterDateRange('visa_date', $request->visa_date_range_picket)
                    ->filterDateRange('visa_received_date', $request->visa_received_date_range_picket)
                    ->filterSearchText($request->search_text);

                    $employerLists = $posts->whereNotNull('booking_id')->orderBy('id', 'DESC')->paginate($request->page_list ?? 10)->withQueryString();

                    return view('admin.employer.load2',['perm' => $permission,'employerLists' => $employerLists]);
                }

                if ($saveEmployeradminsavefilter) {
                    $posts->filterProfession($saveEmployeradminsavefilter->proff_id)
                    ->filterVisaIssuingAuthority($saveEmployeradminsavefilter->issuing_authority)
                    ->filterBusinessType($saveEmployeradminsavefilter->businesstype)
                    ->filterCreatedBy($saveEmployeradminsavefilter->createbyadmin)
                    ->filterCareoff($saveEmployeradminsavefilter->careoff)
                    ->filterWakalaStatus($saveEmployeradminsavefilter->wakala_status)
                    ->filterCreateByPartner($saveEmployeradminsavefilter->createbypartner)
                    ->filterpartnerOffice($saveEmployeradminsavefilter->partneroffice)
                    ->filterCityOfWork($saveEmployeradminsavefilter->wpcity_id)
                    ->filterDateRange('visa_date', $saveEmployeradminsavefilter->visa_date_range)
                    ->filterDateRange('visa_received_date', $saveEmployeradminsavefilter->visa_received_date_range);
                }


                $employerLists = $posts->whereNotNull('booking_id')->orderBy('id', 'DESC')->paginate($request->page_list ?? 10)->withQueryString();

                $employerStatusSummary = $this->buildEmployerStatusSummary($permission);

                return view('admin.employer.index2',['employerLists' => $employerLists,'wakalastatusFilters' => $wakalastatusFilters,'cityworkfilters' => $cityworkfilters,'careofffilters' => $careofffilters,'partnerofficefilters' => $partnerofficefilters,'createByfilters' => $createByfilters,'createByPartnerfilters' => $createByPartnerfilters,'businesstypeFilters' => $businesstypeFilters,'saveEmployeradminsavefilter' => $saveEmployeradminsavefilter,'perm' => $permission,'staffs' => $staffs,'partners' => $partner,'professions' => $profession,'expworkcities' => $expworkcity,'filter_user' => $filter_user,'employerStatusSummary' => $employerStatusSummary]);
            }else{
                $posts = Employer::with(['partneroffice','wpcity','admin','careoff']);

                if ($request->ajax()) {
                    $posts->filterProfession($request->by_profession)
                    ->filterVisaIssuingAuthority($request->by_visa_issuing_authority)
                    ->filterBusinessType($request->by_businesstype)
                    ->filterCreatedBy($request->by_created_by)
                    ->filterCareoff($request->by_careoff)
                    ->filterWakalaStatus($request->by_wakalastatus)
                    ->filterCreateByPartner($request->by_created_by_partner)
                    ->filterpartnerOffice($request->by_partner_office)
                    ->filterCityOfWork($request->by_city_work)
                    ->filterStatus($request->by_status)
                    ->filterDateRange('visa_date', $request->visa_date_range_picket)
                    ->filterDateRange('visa_received_date', $request->visa_received_date_range_picket)
                    ->filterSearchText($request->search_text);

                    $employerLists = $posts->where('admin_id','=',$user->id)->whereNotNull('booking_id')->orderBy('id', 'DESC')->paginate($request->page_list ?? 10)->withQueryString();

                    return view('admin.employer.load2',['perm' => $permission,'employerLists' => $employerLists]);
                }

                if ($saveEmployeradminsavefilter) {
                    $posts->filterProfession($saveEmployeradminsavefilter->proff_id)
                    ->filterVisaIssuingAuthority($saveEmployeradminsavefilter->issuing_authority)
                    ->filterBusinessType($saveEmployeradminsavefilter->businesstype)
                    ->filterCreatedBy($saveEmployeradminsavefilter->createbyadmin)
                    ->filterCareoff($saveEmployeradminsavefilter->careoff)
                    ->filterWakalaStatus($saveEmployeradminsavefilter->wakala_status)
                    ->filterCreateByPartner($saveEmployeradminsavefilter->createbypartner)
                    ->filterpartnerOffice($saveEmployeradminsavefilter->partneroffice)
                    ->filterCityOfWork($saveEmployeradminsavefilter->wpcity_id)
                    ->filterDateRange('visa_date', $saveEmployeradminsavefilter->visa_date_range)
                    ->filterDateRange('visa_received_date', $saveEmployeradminsavefilter->visa_received_date_range);
                }


                $employerLists = $posts->where('admin_id','=',$user->id)->whereNotNull('booking_id')->orderBy('id', 'DESC')->paginate($request->page_list ?? 10)->withQueryString();

                $employerStatusSummary = $this->buildEmployerStatusSummary($permission);

                return view('admin.employer.index2',['employerLists' => $employerLists,'wakalastatusFilters' => $wakalastatusFilters,'cityworkfilters' => $cityworkfilters,'careofffilters' => $careofffilters,'partnerofficefilters' => $partnerofficefilters,'createByfilters' => $createByfilters,'createByPartnerfilters' => $createByPartnerfilters,'businesstypeFilters' => $businesstypeFilters,'saveEmployeradminsavefilter' => $saveEmployeradminsavefilter,'perm' => $permission,'staffs' => $staffs,'partners' => $partner,'professions' => $profession,'expworkcities' => $expworkcity,'filter_user' => $filter_user,'employerStatusSummary' => $employerStatusSummary]);
            }
        }


    }

    /**
     * Global (permission-scoped, not filter-scoped) Employer status counts
     * for the status bar — same "counts stay fixed while the table
     * filters" principle used by the Employer+ / Todo / Booking summary
     * cards (see buildEmployerPlusStatusSummary() below for the sibling
     * page's equivalent). One GROUP BY aggregate, not one query per
     * bucket. Scoped exactly like index()'s own list query (full_access /
     * view_employer / admin_id), always whereNotNull booking_id since
     * that's what makes this the plain Employer list vs Employer+.
     */
    private function buildEmployerStatusSummary($permission): array
    {
        $user = Auth::guard('admin')->user();
        $isAdmin = $user->user_type == 1;

        $base = Employer::query()->whereNotNull('booking_id');

        if (!$isAdmin && !(isset($permission) && $permission->full_access == 1)) {
            if (!isset($permission) || $permission->view_employer != 1) {
                $base->where('admin_id', '=', $user->id);
            }
        }

        $statusCounts = (clone $base)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'total' => (int) $statusCounts->sum(),
            'statuses' => [
                0 => (int) ($statusCounts[0] ?? 0),
                1 => (int) ($statusCounts[1] ?? 0),
            ],
        ];
    }

    public function saveEmpFilter(Request $request) {
        $checkEmployeradminsavefilter = Employeradminsavefilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();
        if (isset($checkEmployeradminsavefilter)) {
            if ($request->by_profession != '') {
                $checkEmployeradminsavefilter->proff_id = implode(",",$request->by_profession);
            } else {
                $checkEmployeradminsavefilter->proff_id = "";
            }

            if ($request->by_visa_issuing_authority != '') {
                $checkEmployeradminsavefilter->issuing_authority = implode(",",$request->by_visa_issuing_authority);
            } else {
                $checkEmployeradminsavefilter->issuing_authority = "";
            }

            if ($request->by_city_work != '') {
                $checkEmployeradminsavefilter->wpcity_id = implode(",",$request->by_city_work);
            } else {
                $checkEmployeradminsavefilter->wpcity_id = "";
            }

            if ($request->by_businesstype != '') {
                $checkEmployeradminsavefilter->businesstype = implode(",",$request->by_businesstype);
            } else {
                $checkEmployeradminsavefilter->businesstype = "";
            }

            if ($request->by_created_by != '') {
                $checkEmployeradminsavefilter->createbyadmin = implode(",",$request->by_created_by);
            } else {
                $checkEmployeradminsavefilter->createbyadmin = "";
            }

            if ($request->by_careoff != '') {
                $checkEmployeradminsavefilter->careoff = implode(",",$request->by_careoff);
            } else {
                $checkEmployeradminsavefilter->careoff = "";
            }

            if ($request->by_wakalastatus != '') {
                $checkEmployeradminsavefilter->wakala_status = implode(",",$request->by_wakalastatus);
            } else {
                $checkEmployeradminsavefilter->wakala_status = "";
            }

            if ($request->by_created_by_partner != '') {
                $checkEmployeradminsavefilter->createbypartner = implode(",",$request->by_created_by_partner);
            } else {
                $checkEmployeradminsavefilter->createbypartner = "";
            }

            if ($request->by_partner_office != '') {
                $checkEmployeradminsavefilter->partneroffice = implode(",",$request->by_partner_office);
            } else {
                $checkEmployeradminsavefilter->partneroffice = "";
            }

            if ($request->visa_date_range_picket != '') {
                $checkEmployeradminsavefilter->visa_date_range = implode(",",$request->visa_date_range_picket);
            } else {
                $checkEmployeradminsavefilter->visa_date_range = "";
            }

            if ($request->visa_received_date_range_picket != '') {
                $checkEmployeradminsavefilter->visa_received_date_range = implode(",",$request->visa_received_date_range_picket);
            } else {
                $checkEmployeradminsavefilter->visa_received_date_range = "";
            }

            $checkEmployeradminsavefilter->save();
            $data = [
                'res' => 'Filter update successfully!'
            ];

        } else {
            $saveFilter = new Employeradminsavefilter();
            $saveFilter->admin_id = Auth::guard('admin')->user()->id;

            if ($request->by_profession != '') {
                $saveFilter->proff_id = implode(",",$request->by_profession);
            } else {
                $saveFilter->proff_id = "";
            }

            if ($request->by_visa_issuing_authority != '') {
                $saveFilter->issuing_authority = implode(",",$request->by_visa_issuing_authority);
            } else {
                $saveFilter->issuing_authority = "";
            }

            if ($request->by_city_work != '') {
                $saveFilter->wpcity_id = implode(",",$request->by_city_work);
            } else {
                $saveFilter->wpcity_id = "";
            }

            if ($request->by_businesstype != '') {
                $saveFilter->businesstype = implode(",",$request->by_businesstype);
            } else {
                $saveFilter->businesstype = "";
            }

            if ($request->by_created_by != '') {
                $saveFilter->createbyadmin = implode(",",$request->by_created_by);
            } else {
                $saveFilter->createbyadmin = "";
            }

            if ($request->by_careoff != '') {
                $saveFilter->careoff = implode(",",$request->by_careoff);
            } else {
                $saveFilter->careoff = "";
            }

            if ($request->by_wakalastatus != '') {
                $saveFilter->wakala_status = implode(",",$request->by_wakalastatus);
            } else {
                $saveFilter->wakala_status = "";
            }

            if ($request->by_created_by_partner != '') {
                $saveFilter->createbypartner = implode(",",$request->by_created_by_partner);
            } else {
                $saveFilter->createbypartner = "";
            }

            if ($request->by_partner_office != '') {
                $saveFilter->partneroffice = implode(",",$request->by_partner_office);
            } else {
                $saveFilter->partneroffice = "";
            }

            if ($request->visa_date_range_picket != '') {
                $saveFilter->visa_date_range = implode(",",$request->visa_date_range_picket);
            } else {
                $saveFilter->visa_date_range = "";
            }

            if ($request->visa_received_date_range_picket != '') {
                $saveFilter->visa_received_date_range = implode(",",$request->visa_received_date_range_picket);
            } else {
                $saveFilter->visa_received_date_range = "";
            }

            $saveFilter->save();
            $data = [
                'res' => 'Filter save successfully!'
            ];
        }

        return response()->json($data);

    }

    public function resetEmpFilter(Request $request){
        $checkFilter = Employeradminsavefilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();

        if (isset($checkFilter)) {
            $checkFilter->proff_id = "";
            $checkFilter->issuing_authority = "";
            $checkFilter->wpcity_id = "";
            $checkFilter->businesstype = "";
            $checkFilter->visa_date_range = "";
            $checkFilter->createbyadmin = "";
            $checkFilter->createbypartner = "";
            $checkFilter->partneroffice = "";
            $checkFilter->careoff = "";
            $checkFilter->wakala_status = "";
            $checkFilter->visa_received_date_range = "";
            $checkFilter->save();

            $data = [
                'res' => 'Filter reset successfully!'
            ];
        }else{
            $data = [
                'res' => 'Filter reset successfully!'
            ];
        }

        return response()->json($data);
    }

    public function empVisaEdit(Request $request){
        $post = Employer::find($request->id);
        $professions = Profession::orderBy('eng_name')->get();
        $data = [
            'post' => $post,
            'professions' => $professions
        ];
        // return response()->json($post);
        return response()->json($data);
    }

    public function empVisaUpdate(Request $request){


        // Update Into Employer Table
        $post = Employer::find($request->edit_id);

        if(empty($post)){
         $post = Employerplus::find($request->edit_id);
        }

        if(empty($post)){
            return redirect()->back()->with('error','Invalid Employer');
        }
        // dd($request->edit_id);
        $post->businesstype = $request->businesstype;
        $post->visa_no = $request->visa_no;
        $post->id_no = $request->id_no;
        if ($request->proff_id != '') {
            $post->proff_id = implode(",",$request->proff_id);
        }

        if ($request->openings != '') {
            $post->openings = implode(",",$request->openings);
        }

        $post->employer_name = $request->employer_name;
        $post->employer_ar_name = $request->employer_ar_name;

        $post->issuing_authority = $request->issuing_authority;


        $post->visa_date = $request->visa_date;
        $post->visa_received_date = $request->visa_received_date;
        $post->wpcity_id = $request->wpcity_id;
        $post->salary = $request->salary;
        $post->partneroffice_id = $request->partner_office_id;
        $post->notes = $request->notes;
        $post->mobile_no = $request->mobile_no;
        $post->careoff_id = $request->careoff_id;
        $post->wakala_status = $request->wakala_status;
        $post->save();


        return redirect()->back()->with('success','Visa Details updated!');
    }

    public function empStatusUpdate(Request $request)  {
        $post = Employer::find($request->emp_id);
        $post->status = $request->emp_status;
        $post->save();

        return redirect()->back()->with('success','Status updated!');
    }

    public function VisaDetView($id){

        $post2 = Employer::find($id);

        $post = DB::table('employers as employer')
            ->leftJoin('partners as partner','employer.partneroffice_id','=','partner.id')
            ->leftJoin('bookings as booking','employer.booking_id','=','booking.id')
            ->leftJoin('users as user','employer.user_id','=','user.id')
            ->leftjoin('candidates as cand','employer.cand_id','=','cand.id')
            ->leftJoin('professions as proff','employer.proff_id','=','proff.id')
            ->leftJoin('professions as candproff','cand.jobtype_id','=','candproff.id')
            ->leftJoin('expecworkcities as expwork','employer.wpcity_id','=','expwork.id')
            ->leftJoin('admins as admin','employer.admin_id','=','admin.id')
            ->leftJoin('admins as careoff','careoff.id','=','employer.careoff_id')
            ->select(
                'employer.*',
                'user.mobile_no as umobno',
                'user.avatar_url as uavatar_url',
                'user.photo as uphoto',
                'cand.cand_name',
                'cand.pass_no',
                'cand.dob as candob',
                'candproff.eng_name as candprofession',
                'cand.experience as candexperience',
                'cand.pass_file',
                'cand.pass_back_file',
                'cand.lic_file',
                'cand.cv_file',
                'cand.photo_file',
                'cand.overall_exp',
                'proff.eng_name as pengname',
                'expwork.name as expworkname',
                'admin.name as createBy',
                'careoff.name as careoffby'
                )
            ->where('employer.id','=',$id)
            ->first();



            $total_exp = array_sum(explode(',',$post->candexperience));

            // $candempLists = Employercandidate::where('emp_id','=',$id)->where('status',1)->get();
            $candempLists = DB::table('employercandidates as employercandidate')
                ->leftJoin('candidates as cand22','cand22.id','=','employercandidate.cand_id')
                ->leftJoin('professions as profff','profff.id','=','employercandidate.proff_id')
                ->select('employercandidate.*','cand22.cand_name as candname','cand22.pass_no as candpassno','cand22.dob as candob','cand22.overall_exp as canoverall_exp','profff.eng_name as profffengname')
                ->where('employercandidate.status',1)
                ->where('employercandidate.emp2_id',$id)
                ->get();


            $partners = Partner::where('status','=',1)->orderBy('rec_off_name')->get();
            $professions = Profession::orderBy('eng_name')->get();
            $expworkcities = Expecworkcity::orderBy('name')->get();
            $candidates = Candidate::orderBy('cand_name')->where('status',1)->get();





            // Fetch Visa Professions
            $visaprofessions = Profession::whereIn('id', explode(',', $post->proff_id))->get();
            $visaProfession = $visaprofessions->pluck('eng_name')->toArray();


            // foreach ($visaprofessions as $visaprofessionde) {
            //     $visaProfession[] = $visaprofessionde->eng_name;
            // }

            // dd($visaProfession);

            // Get Visa quantity and balance quantity
            $visa_openings = explode(",",$post->openings);
            $openings = array_sum($visa_openings);
            $assign_visa_quantity = $candempLists->count();
            $balance_visa = $openings - $assign_visa_quantity;
            $visaProfessionQty = [];
            $getAvailableProfessionID = [];
            foreach ($visaprofessions as $key => $visaprofessionde) {
                if (isset($visa_openings[$key]) && $visa_openings[$key] != '' ) {
                    $visaQ = $visa_openings[$key];
                    $getAvailableProfessionID[] = $visaprofessionde->id;
                } else {
                    $visaQ = "0";
                }

                $visaProfessionQty[] = $visaprofessionde->eng_name.' : '.$visaQ;
            }

            $visaBalanceData = [
                'visa_quantity' => $openings,
                'balance_visa'  => $balance_visa,
                'visaProfessionQty' => $visaProfessionQty
            ];


            // Get Availanle Profession for Assigning
            $getAvailableProfessions = Profession::wherein('id',$getAvailableProfessionID)->get();

            // dd($candempLists);

            $staffs = Admin::where('status','=',1)->orderBy('name')->get();

        return view('admin.employer.visa.show2',compact('post2','post','staffs','getAvailableProfessions','visaBalanceData','visaProfession','total_exp','candempLists','partners','professions','expworkcities','candidates'));
    }

    public function deleteEmployer(Request $request){
        $visaDetal = Employer::find($request->delete_id);
        // $booking = Booking::find($visaDetal->booking_id);
        // $candidate = Candidate::find($visaDetal->cand_id);
        $emp_cand = Employercandidate::where('emp2_id','=',$visaDetal->id)->get();

        // Action perform
        if (isset($emp_cand)) {
            foreach ($emp_cand as $emp_cand2) {
                $candidate = Candidate::find($emp_cand2->cand_id);
                $candidate->status = true;
                $candidate->save();
            }


            $emp_cand->delete();
        }



        $visaDetal->delete();

        return redirect()->back()->with('success','Employer Deleted!');


    }

    // For Employer Section End

    // For Employer Plus Section Start
    public function indexp(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id','=',$user->id)->first();
        $filter_user = Employefilterlist::where('admin_id','=',$user->id)->first();
        $staffs = Admin::where('status','=',1)->orderBy('name')->get();

        $profession = Profession::orderBy('eng_name')->get();
        $expworkcity = Expecworkcity::orderBy('name')->get();
        $partner = Partner::orderBy('rec_off_name')->where('status','=',1)->get();

        // Filter List Start
        $saveEmployeradminsavefilter = Employerplusadminsavefilter::where('admin_id','=',$user->id)->first();
        $businesstypeFilters = Employerplus::whereNull('booking_id')->select('businesstype')->groupBy('businesstype')->whereNotNull('businesstype')->get('businesstype');
        $wakalastatusFilters = Employerplus::whereNull('booking_id')->select('wakala_status')->groupBy('wakala_status')->whereNotNull('wakala_status')->get();
        $createByfilters = DB::table('employerpluses as employer')
        ->leftjoin('admins as admin','admin.id','=','employer.admin_id')
        ->select('employer.admin_id','admin.name as adminName')
        ->groupBy('employer.admin_id','adminName')
        ->whereNull('employer.booking_id')
        ->whereNotNull('employer.admin_id')
        ->get();

        // $careofffilters = DB::table('employerpluses as employer')
        // ->leftjoin('admins as admin','admin.id','=','employer.careoff_id')
        // ->select('employer.careoff_id','admin.name as careoffName')
        // ->groupBy('employer.careoff_id','careoffName')
        // ->whereNull('employer.booking_id')
        // ->whereNotNull('employer.careoff_id')
        // ->get();
        $careofffilters = DB::table('admins')->where('status',1)->get();


        $createByPartnerfilters = DB::table('employerpluses as employer')
        ->leftjoin('partners as partner','partner.id','=','employer.partner_id')
        ->select('employer.partner_id','partner.rec_off_name as partnerName')
        ->groupBy('employer.partner_id','partnerName')
        ->whereNull('employer.booking_id')
        ->whereNotNull('employer.partner_id')
        ->get();

        $partnerofficefilters = DB::table('employerpluses as employer')
        ->leftjoin('partners as partner','partner.id','=','employer.partneroffice_id')
        ->select('employer.partneroffice_id','partner.rec_off_name as partnerName')
        ->groupBy('employer.partneroffice_id','partnerName')
        ->whereNull('employer.booking_id')
        ->whereNotNull('employer.partneroffice_id')
        ->get();

        $cityworkfilters = DB::table('employerpluses as employer')
        ->leftjoin('expecworkcities as expecworkcity','expecworkcity.id','=','employer.wpcity_id')
        ->select('employer.wpcity_id','expecworkcity.name as expworkname')
        ->groupBy('employer.wpcity_id','expworkname')
        ->whereNull('employer.booking_id')
        ->whereNotNull('employer.wpcity_id')
        ->get();




        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $posts = Employerplus::with(['partneroffice','wpcity','admin','careoff']);

            if ($request->ajax()) {
                $posts->filterProfessionP($request->by_profession)
                ->filterVisaIssuingAuthorityP($request->by_visa_issuing_authority)
                ->filterBusinessTypeP($request->by_businesstype)
                ->filterCreatedByP($request->by_created_by)
                ->filterCareoffP($request->by_careoff)
                ->filterWakalaStatusP($request->by_wakalastatus)
                ->filterCreateByPartnerP($request->by_created_by_partner)
                ->filterpartnerOfficeP($request->by_partner_office)
                ->filterCityOfWorkP($request->by_city_work)
                ->filterStatusP($request->by_status)
                ->filterPaymentStatusP($request->by_payment_status)
                ->filterDateRangeP('visa_date', $request->visa_date_range_picket)
                ->filterDateRangeP('visa_received_date', $request->visa_received_date_range_picket)
                ->filterSearchTextP($request->search_text);

                $employerLists = $posts->whereNull('booking_id')->orderBy('id', 'DESC')->paginate($request->page_list ?? 10)->withQueryString();

                return view('admin.employer.load',['perm' => $permission,'employerLists' => $employerLists]);
            }

            if ($saveEmployeradminsavefilter) {

                $proff_id = $saveEmployeradminsavefilter->proff_id ? explode(",",$saveEmployeradminsavefilter->proff_id) :'';
                $issuing_authority = $saveEmployeradminsavefilter->issuing_authority ? explode(",",$saveEmployeradminsavefilter->issuing_authority) : '';
                $businesstype = $saveEmployeradminsavefilter->businesstype ? explode(",",$saveEmployeradminsavefilter->businesstype):'';
                $createbyadmin = $saveEmployeradminsavefilter->createbyadmin ? explode(",",$saveEmployeradminsavefilter->createbyadmin) :'';
                $careoff = $saveEmployeradminsavefilter->careoff ? explode(",",$saveEmployeradminsavefilter->careoff) : '';
                $wakala_status = $saveEmployeradminsavefilter->wakala_status ? explode(",",$saveEmployeradminsavefilter) :'';
                $createbypartner = $saveEmployeradminsavefilter->createbypartner ? explode(",", $saveEmployeradminsavefilter->createbypartner) : '';
                $partneroffice = $saveEmployeradminsavefilter->partneroffice ? explode(",",$saveEmployeradminsavefilter->partneroffice) : '';
                $wpcity_id = $saveEmployeradminsavefilter->wpcity_id ? explode(",",$saveEmployeradminsavefilter->wpcity_id) :'';

                $posts->filterProfessionP($proff_id)
                ->filterVisaIssuingAuthorityP($issuing_authority)
                ->filterBusinessTypeP($businesstype)
                ->filterCreatedByP($createbyadmin)
                ->filterCareoffP($careoff)
                ->filterWakalaStatusP($wakala_status)
                ->filterCreateByPartnerP($createbypartner)
                ->filterpartnerOfficeP($partneroffice)
                ->filterCityOfWorkP($wpcity_id)
                ->filterDateRangeP('visa_date', $saveEmployeradminsavefilter->visa_date_range)
                ->filterDateRangeP('visa_received_date', $saveEmployeradminsavefilter->visa_received_date_range);
            }


            $employerLists = $posts->whereNull('booking_id')->orderBy('id', 'DESC')->paginate($request->page_list ?? 10)->withQueryString();

            $employerPlusStatusSummary = $this->buildEmployerPlusStatusSummary($permission);

            return view('admin.employer.index',['employerLists' => $employerLists,'wakalastatusFilters' => $wakalastatusFilters,'cityworkfilters' => $cityworkfilters,'careofffilters' => $careofffilters,'partnerofficefilters' => $partnerofficefilters,'createByfilters' => $createByfilters,'createByPartnerfilters' => $createByPartnerfilters,'businesstypeFilters' => $businesstypeFilters,'saveEmployeradminsavefilter' => $saveEmployeradminsavefilter,'perm' => $permission,'staffs' => $staffs,'partners' => $partner,'professions' => $profession,'expworkcities' => $expworkcity,'filter_user' => $filter_user,'employerPlusStatusSummary' => $employerPlusStatusSummary]);
        }elseif (Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)) {
            if($permission->employerplus_view == 1){
                $posts = Employerplus::with(['partneroffice','wpcity','admin','careoff']);

                if ($request->ajax()) {
                    $posts->filterProfessionP($request->by_profession)
                    ->filterVisaIssuingAuthorityP($request->by_visa_issuing_authority)
                    ->filterBusinessTypeP($request->by_businesstype)
                    ->filterCreatedByP($request->by_created_by)
                    ->filterCareoffP($request->by_careoff)
                    ->filterWakalaStatusP($request->by_wakalastatus)
                    ->filterCreateByPartnerP($request->by_created_by_partner)
                    ->filterpartnerOfficeP($request->by_partner_office)
                    ->filterCityOfWorkP($request->by_city_work)
                    ->filterStatusP($request->by_status)
                    ->filterPaymentStatusP($request->by_payment_status)
                    ->filterDateRangeP('visa_date', $request->visa_date_range_picket)
                    ->filterDateRangeP('visa_received_date', $request->visa_received_date_range_picket)
                    ->filterSearchTextP($request->search_text);

                    $employerLists = $posts->whereNull('booking_id')->orderBy('id', 'DESC')->paginate($request->page_list ?? 10)->withQueryString();

                    return view('admin.employer.load',['perm' => $permission,'employerLists' => $employerLists]);
                }

                if ($saveEmployeradminsavefilter) {

                    $proff_id = $saveEmployeradminsavefilter->proff_id ? explode(",",$saveEmployeradminsavefilter->proff_id) :'';
                    $issuing_authority = $saveEmployeradminsavefilter->issuing_authority ? explode(",",$saveEmployeradminsavefilter->issuing_authority) : '';
                    $businesstype = $saveEmployeradminsavefilter->businesstype ? explode(",",$saveEmployeradminsavefilter->businesstype):'';
                    $createbyadmin = $saveEmployeradminsavefilter->createbyadmin ? explode(",",$saveEmployeradminsavefilter->createbyadmin) :'';
                    $careoff = $saveEmployeradminsavefilter->careoff ? explode(",",$saveEmployeradminsavefilter->careoff) : '';
                    $wakala_status = $saveEmployeradminsavefilter->wakala_status ? explode(",",$saveEmployeradminsavefilter) :'';
                    $createbypartner = $saveEmployeradminsavefilter->createbypartner ? explode(",", $saveEmployeradminsavefilter->createbypartner) : '';
                    $partneroffice = $saveEmployeradminsavefilter->partneroffice ? explode(",",$saveEmployeradminsavefilter->partneroffice) : '';
                    $wpcity_id = $saveEmployeradminsavefilter->wpcity_id ? explode(",",$saveEmployeradminsavefilter->wpcity_id) :'';

                    $posts->filterProfessionP($proff_id)
                    ->filterVisaIssuingAuthorityP($issuing_authority)
                    ->filterBusinessTypeP($businesstype)
                    ->filterCreatedByP($createbyadmin)
                    ->filterCareoffP($careoff)
                    ->filterWakalaStatusP($wakala_status)
                    ->filterCreateByPartnerP($createbypartner)
                    ->filterpartnerOfficeP($partneroffice)
                    ->filterCityOfWorkP($wpcity_id)
                    ->filterDateRangeP('visa_date', $saveEmployeradminsavefilter->visa_date_range)
                    ->filterDateRangeP('visa_received_date', $saveEmployeradminsavefilter->visa_received_date_range);
                }


                $employerLists = $posts->whereNull('booking_id')->orderBy('id', 'DESC')->paginate($request->page_list ?? 10)->withQueryString();

                $employerPlusStatusSummary = $this->buildEmployerPlusStatusSummary($permission);

                return view('admin.employer.index',['employerLists' => $employerLists,'wakalastatusFilters' => $wakalastatusFilters,'cityworkfilters' => $cityworkfilters,'careofffilters' => $careofffilters,'partnerofficefilters' => $partnerofficefilters,'createByfilters' => $createByfilters,'createByPartnerfilters' => $createByPartnerfilters,'businesstypeFilters' => $businesstypeFilters,'saveEmployeradminsavefilter' => $saveEmployeradminsavefilter,'perm' => $permission,'staffs' => $staffs,'partners' => $partner,'professions' => $profession,'expworkcities' => $expworkcity,'filter_user' => $filter_user,'employerPlusStatusSummary' => $employerPlusStatusSummary]);
            }else{
                $posts = Employerplus::with(['partneroffice','wpcity','admin','careoff']);

                if ($request->ajax()) {
                    $posts->filterProfessionP($request->by_profession)
                    ->filterVisaIssuingAuthorityP($request->by_visa_issuing_authority)
                    ->filterBusinessTypeP($request->by_businesstype)
                    ->filterCreatedByP($request->by_created_by)
                    ->filterCareoffP($request->by_careoff)
                    ->filterWakalaStatusP($request->by_wakalastatus)
                    ->filterCreateByPartnerP($request->by_created_by_partner)
                    ->filterpartnerOfficeP($request->by_partner_office)
                    ->filterCityOfWorkP($request->by_city_work)
                    ->filterStatusP($request->by_status)
                    ->filterPaymentStatusP($request->by_payment_status)
                    ->filterDateRangeP('visa_date', $request->visa_date_range_picket)
                    ->filterDateRangeP('visa_received_date', $request->visa_received_date_range_picket)
                    ->filterSearchTextP($request->search_text);

                    $employerLists = $posts->where('admin_id','=',$user->id)->whereNull('booking_id')->orderBy('id', 'DESC')->paginate($request->page_list ?? 10)->withQueryString();

                    return view('admin.employer.load',['perm' => $permission,'employerLists' => $employerLists]);
                }

                if ($saveEmployeradminsavefilter) {

                    $proff_id = $saveEmployeradminsavefilter->proff_id ? explode(",",$saveEmployeradminsavefilter->proff_id) :'';
                    $issuing_authority = $saveEmployeradminsavefilter->issuing_authority ? explode(",",$saveEmployeradminsavefilter->issuing_authority) : '';
                    $businesstype = $saveEmployeradminsavefilter->businesstype ? explode(",",$saveEmployeradminsavefilter->businesstype):'';
                    $createbyadmin = $saveEmployeradminsavefilter->createbyadmin ? explode(",",$saveEmployeradminsavefilter->createbyadmin) :'';
                    $careoff = $saveEmployeradminsavefilter->careoff ? explode(",",$saveEmployeradminsavefilter->careoff) : '';
                    $wakala_status = $saveEmployeradminsavefilter->wakala_status ? explode(",",$saveEmployeradminsavefilter) :'';
                    $createbypartner = $saveEmployeradminsavefilter->createbypartner ? explode(",", $saveEmployeradminsavefilter->createbypartner) : '';
                    $partneroffice = $saveEmployeradminsavefilter->partneroffice ? explode(",",$saveEmployeradminsavefilter->partneroffice) : '';
                    $wpcity_id = $saveEmployeradminsavefilter->wpcity_id ? explode(",",$saveEmployeradminsavefilter->wpcity_id) :'';

                    $posts->filterProfessionP($proff_id)
                    ->filterVisaIssuingAuthorityP($issuing_authority)
                    ->filterBusinessTypeP($businesstype)
                    ->filterCreatedByP($createbyadmin)
                    ->filterCareoffP($careoff)
                    ->filterWakalaStatusP($wakala_status)
                    ->filterCreateByPartnerP($createbypartner)
                    ->filterpartnerOfficeP($partneroffice)
                    ->filterCityOfWorkP($wpcity_id)
                    ->filterDateRangeP('visa_date', $saveEmployeradminsavefilter->visa_date_range)
                    ->filterDateRangeP('visa_received_date', $saveEmployeradminsavefilter->visa_received_date_range);
                }


                $employerLists = $posts->where('admin_id','=',$user->id)->whereNull('booking_id')->orderBy('id', 'DESC')->paginate($request->page_list ?? 10)->withQueryString();

                $employerPlusStatusSummary = $this->buildEmployerPlusStatusSummary($permission);

                return view('admin.employer.index',['employerLists' => $employerLists,'wakalastatusFilters' => $wakalastatusFilters,'cityworkfilters' => $cityworkfilters,'careofffilters' => $careofffilters,'partnerofficefilters' => $partnerofficefilters,'createByfilters' => $createByfilters,'createByPartnerfilters' => $createByPartnerfilters,'businesstypeFilters' => $businesstypeFilters,'saveEmployeradminsavefilter' => $saveEmployeradminsavefilter,'perm' => $permission,'staffs' => $staffs,'partners' => $partner,'professions' => $profession,'expworkcities' => $expworkcity,'filter_user' => $filter_user,'employerPlusStatusSummary' => $employerPlusStatusSummary]);
            }
        }


    }

    /**
     * Global (permission-scoped, not filter-scoped) Employer+ Status /
     * Payment Status counts for the status bar — same "counts stay fixed
     * while the table filters" principle used by the Todo/Booking/Deal
     * Pipeline summary cards. A couple of GROUP BY aggregates, not one
     * query per bucket. Scoped exactly like indexp()'s own list query
     * (full_access / employerplus_view / admin_id), always whereNull
     * booking_id since that's what makes this "Employer+" vs Employer.
     */
    private function buildEmployerPlusStatusSummary($permission): array
    {
        $user = Auth::guard('admin')->user();
        $isAdmin = $user->user_type == 1;

        $base = Employerplus::query()->whereNull('booking_id');

        if (!$isAdmin && !(isset($permission) && $permission->full_access == 1)) {
            if (!isset($permission) || $permission->employerplus_view != 1) {
                $base->where('admin_id', '=', $user->id);
            }
        }

        $statusCounts = (clone $base)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $paymentStatusCounts = (clone $base)
            ->whereIn('payment_status', ['Paid', 'Unpaid', 'To Collect'])
            ->select('payment_status', DB::raw('COUNT(*) as total'))
            ->groupBy('payment_status')
            ->pluck('total', 'payment_status');

        return [
            'total' => (int) $statusCounts->sum(),
            'statuses' => [
                0 => (int) ($statusCounts[0] ?? 0),
                1 => (int) ($statusCounts[1] ?? 0),
            ],
            'paymentStatuses' => [
                'Paid' => (int) ($paymentStatusCounts['Paid'] ?? 0),
                'Unpaid' => (int) ($paymentStatusCounts['Unpaid'] ?? 0),
                'To Collect' => (int) ($paymentStatusCounts['To Collect'] ?? 0),
            ],
        ];
    }

    public function getpaymentstatus(Request $request){
        $post = Employerplus::find($request->id);

        return response()->json($post);
    }

    public function paymentstatusupdate(Request $request) {
        $post = Employerplus::find($request->emp_id);
        $post->payment_status = $request->payment_status;
        $post->save();

        return redirect()->back()->with('success','Payment Status updated!');
    }

    public function saveEmpPFilter(Request $request) {
        $checkEmployeradminsavefilter = Employerplusadminsavefilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();
        if (isset($checkEmployeradminsavefilter)) {
            if ($request->by_profession != '') {
                $checkEmployeradminsavefilter->proff_id = implode(",",$request->by_profession);
            } else {
                $checkEmployeradminsavefilter->proff_id = "";
            }

            if ($request->by_visa_issuing_authority != '') {
                $checkEmployeradminsavefilter->issuing_authority = implode(",",$request->by_visa_issuing_authority);
            } else {
                $checkEmployeradminsavefilter->issuing_authority = "";
            }

            if ($request->by_city_work != '') {
                $checkEmployeradminsavefilter->wpcity_id = implode(",",$request->by_city_work);
            } else {
                $checkEmployeradminsavefilter->wpcity_id = "";
            }

            if ($request->by_businesstype != '') {
                $checkEmployeradminsavefilter->businesstype = implode(",",$request->by_businesstype);
            } else {
                $checkEmployeradminsavefilter->businesstype = "";
            }

            if ($request->by_created_by != '') {
                $checkEmployeradminsavefilter->createbyadmin = implode(",",$request->by_created_by);
            } else {
                $checkEmployeradminsavefilter->createbyadmin = "";
            }

            if ($request->by_careoff != '') {
                $checkEmployeradminsavefilter->careoff = implode(",",$request->by_careoff);
            } else {
                $checkEmployeradminsavefilter->careoff = "";
            }

            if ($request->by_wakalastatus != '') {
                $checkEmployeradminsavefilter->wakala_status = implode(",",$request->by_wakalastatus);
            } else {
                $checkEmployeradminsavefilter->wakala_status = "";
            }

            if ($request->by_created_by_partner != '') {
                $checkEmployeradminsavefilter->createbypartner = implode(",",$request->by_created_by_partner);
            } else {
                $checkEmployeradminsavefilter->createbypartner = "";
            }

            if ($request->by_partner_office != '') {
                $checkEmployeradminsavefilter->partneroffice = implode(",",$request->by_partner_office);
            } else {
                $checkEmployeradminsavefilter->partneroffice = "";
            }

            // dd($request->visa_date_range_picket);

            if (!empty($request->visa_date_range_picket)) {
                $checkEmployeradminsavefilter->visa_date_range = $request->visa_date_range_picket;
            } else {
                $checkEmployeradminsavefilter->visa_date_range = '';
            }            

            if ($request->visa_received_date_range_picket != '') {
                $checkEmployeradminsavefilter->visa_received_date_range = $request->visa_received_date_range_picket;
            } else {
                $checkEmployeradminsavefilter->visa_received_date_range = "";
            }

            $checkEmployeradminsavefilter->save();
            $data = [
                'res' => 'Filter update successfully!'
            ];

        } else {
            $saveFilter = new Employerplusadminsavefilter();
            $saveFilter->admin_id = Auth::guard('admin')->user()->id;

            if ($request->by_profession != '') {
                $saveFilter->proff_id = implode(",",$request->by_profession);
            } else {
                $saveFilter->proff_id = "";
            }

            if ($request->by_visa_issuing_authority != '') {
                $saveFilter->issuing_authority = implode(",",$request->by_visa_issuing_authority);
            } else {
                $saveFilter->issuing_authority = "";
            }

            if ($request->by_city_work != '') {
                $saveFilter->wpcity_id = implode(",",$request->by_city_work);
            } else {
                $saveFilter->wpcity_id = "";
            }

            if ($request->by_businesstype != '') {
                $saveFilter->businesstype = implode(",",$request->by_businesstype);
            } else {
                $saveFilter->businesstype = "";
            }

            if ($request->by_created_by != '') {
                $saveFilter->createbyadmin = implode(",",$request->by_created_by);
            } else {
                $saveFilter->createbyadmin = "";
            }

            if ($request->by_careoff != '') {
                $saveFilter->careoff = implode(",",$request->by_careoff);
            } else {
                $saveFilter->careoff = "";
            }

            if ($request->by_wakalastatus != '') {
                $saveFilter->wakala_status = implode(",",$request->by_wakalastatus);
            } else {
                $saveFilter->wakala_status = "";
            }

            if ($request->by_created_by_partner != '') {
                $saveFilter->createbypartner = implode(",",$request->by_created_by_partner);
            } else {
                $saveFilter->createbypartner = "";
            }

            if ($request->by_partner_office != '') {
                $saveFilter->partneroffice = implode(",",$request->by_partner_office);
            } else {
                $saveFilter->partneroffice = "";
            }

            if ($request->visa_date_range_picket != '') {
                $saveFilter->visa_date_range = implode(",",$request->visa_date_range_picket);
            } else {
                $saveFilter->visa_date_range = "";
            }

            if ($request->visa_received_date_range_picket != '') {
                $saveFilter->visa_received_date_range = implode(",",$request->visa_received_date_range_picket);
            } else {
                $saveFilter->visa_received_date_range = "";
            }

            $saveFilter->save();
            $data = [
                'res' => 'Filter save successfully!'
            ];
        }

        return response()->json($data);

    }

    public function resetEmpPFilter(Request $request){
        $checkFilter = Employerplusadminsavefilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();

        if (isset($checkFilter)) {
            $checkFilter->proff_id = "";
            $checkFilter->issuing_authority = "";
            $checkFilter->wpcity_id = "";
            $checkFilter->businesstype = "";
            $checkFilter->visa_date_range = "";
            $checkFilter->createbyadmin = "";
            $checkFilter->createbypartner = "";
            $checkFilter->partneroffice = "";
            $checkFilter->careoff = "";
            $checkFilter->wakala_status = "";
            $checkFilter->visa_received_date_range = "";
            $checkFilter->save();

            $data = [
                'res' => 'Filter reset successfully!'
            ];
        }else{
            $data = [
                'res' => 'Filter reset successfully!'
            ];
        }

        return response()->json($data);
    }

    public function empVisaStore(Request $request){

        // Store in Employer Table
        $post = new Employerplus();
        $post->businesstype = $request->businesstype;
        $post->visa_no = $request->visa_no;
        $post->id_no = $request->id_no;
        if ($request->proff_id != '') {
            $post->proff_id = implode(",",$request->proff_id);
        }

        if ($request->openings != '') {
            $post->openings = implode(",",$request->openings);
        }

        $post->employer_name = $request->employer_name;
        $post->employer_ar_name = $request->employer_ar_name;

        $post->issuing_authority = $request->issuing_authority;

        $post->visa_date = $request->visa_date;
        $post->visa_received_date = $request->visa_received_date;
        $post->wpcity_id = $request->wpcity_id;
        $post->salary = $request->salary;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->partneroffice_id = $request->partner_office_id;
        $post->notes = $request->notes;
        $post->careoff_id = $request->careoff_id;
        $post->wakala_status = $request->wakala_status;
        $post->save();

        return redirect()->back()->with('Visa details add successfully!');
    }

    public function empVisaEditp(Request $request){
        $post = Employerplus::find($request->id);
        $professions = Profession::orderBy('eng_name')->get();
        $data = [
            'post' => $post,
            'professions' => $professions
        ];
        // return response()->json($post);
        return response()->json($data);
    }

    public function empVisaUpdateP(Request $request){

        // Update Into Employer Table

        $post = Employerplus::find($request->edit_id);
        $post->businesstype = $request->businesstype;
        $post->visa_no = $request->visa_no;
        $post->id_no = $request->id_no;
        if ($request->proff_id != '') {
            $post->proff_id = implode(",",$request->proff_id);
        }

        if ($request->openings != '') {
            $post->openings = implode(",",$request->openings);
        }

        $post->employer_name = $request->employer_name;
        $post->employer_ar_name = $request->employer_ar_name;

        $post->issuing_authority = $request->issuing_authority;


        $post->visa_date = $request->visa_date;
        $post->visa_received_date = $request->visa_received_date;
        $post->wpcity_id = $request->wpcity_id;
        $post->salary = $request->salary;
        $post->partneroffice_id = $request->partner_office_id;
        $post->notes = $request->notes;
        $post->mobile_no = $request->mobile_no;
        $post->careoff_id = $request->careoff_id;
        $post->wakala_status = $request->wakala_status;
        $post->save();


        return redirect()->back()->with('success','Visa Details updated!');
    }

    public function empStatusUpdatep(Request $request)  {
        $post = Employerplus::find($request->emp_id);
        $post->status = $request->emp_status;
        $post->save();

        return redirect()->back()->with('success','Status updated!');
    }

    public function VisaDetViewp($id){

        $post2 = Employerplus::find($id);




        $post = DB::table('employerpluses as employer')
            ->leftJoin('partners as partner','employer.partneroffice_id','=','partner.id')
            ->leftJoin('bookings as booking','employer.booking_id','=','booking.id')
            ->leftJoin('users as user','employer.user_id','=','user.id')
            ->leftjoin('candidates as cand','employer.cand_id','=','cand.id')
            ->leftJoin('professions as proff','employer.proff_id','=','proff.id')
            ->leftJoin('professions as candproff','cand.jobtype_id','=','candproff.id')
            ->leftJoin('expecworkcities as expwork','employer.wpcity_id','=','expwork.id')
            ->leftJoin('admins as admin','employer.admin_id','=','admin.id')
            ->leftJoin('admins as careoff','careoff.id','=','employer.careoff_id')
            ->select(
                'employer.*',
                'user.mobile_no as umobno',
                'user.avatar_url as uavatar_url',
                'user.photo as uphoto',
                'cand.cand_name',
                'cand.pass_no',
                'cand.dob as candob',
                'candproff.eng_name as candprofession',
                'cand.experience as candexperience',
                'cand.pass_file',
                'cand.pass_back_file',
                'cand.lic_file',
                'cand.cv_file',
                'cand.photo_file',
                'cand.overall_exp',
                'proff.eng_name as pengname',
                'expwork.name as expworkname',
                'admin.name as createBy',
                'careoff.name as careoffby'
                )
            ->where('employer.id','=',$id)
            ->first();


            if ($post->candexperience != '') {
                $total_exp = array_sum(explode(',',$post->candexperience));
            } else {
                $total_exp = "";
            }




            // $candempLists = Employercandidate::where('emp_id','=',$id)->where('status',1)->get();
            $candempLists = DB::table('employercandidates as employercandidate')
                ->leftJoin('candidates as cand22','cand22.id','=','employercandidate.cand_id')
                ->leftJoin('professions as profff','profff.id','=','employercandidate.proff_id')
                ->select('employercandidate.*','cand22.cand_name as candname','cand22.pass_no as candpassno','cand22.dob as candob','cand22.overall_exp as canoverall_exp','profff.eng_name as profffengname')
                ->where('employercandidate.status',1)
                ->where('employercandidate.emp_id',$id)
                ->get();


            $partners = Partner::where('status','=',1)->orderBy('rec_off_name')->get();
            $professions = Profession::orderBy('eng_name')->get();
            $expworkcities = Expecworkcity::orderBy('name')->get();
            $candidates = Candidate::orderBy('cand_name')->where('status',1)->get();

            // Fetch Visa Professions
            $visaprofessions = Profession::whereIn('id', explode(',', $post->proff_id))->get();
            $visaProfession = $visaprofessions->pluck('eng_name')->toArray();


            // foreach ($visaprofessions as $visaprofessionde) {
            //     $visaProfession[] = $visaprofessionde->eng_name;
            // }

            // dd($visaProfession);

            // Get Visa quantity and balance quantity
            $visa_openings = explode(",",$post->openings);
            $openings = array_sum($visa_openings);
            $assign_visa_quantity = $candempLists->count();
            $balance_visa = $openings - $assign_visa_quantity;
            $visaProfessionQty = [];
            $getAvailableProfessionID = [];

            foreach (explode(',', $post->proff_id) as $key => $proff_id) {
                if (isset($visa_openings[$key]) && $visa_openings[$key] != '') {
                    $visaQ = $visa_openings[$key];
                    // Get Profession Data
                    $getProfData = Profession::find($proff_id);
                    $getAvailableProfessionID[] = $proff_id;
                } else {
                    $visaQ = "0";
                }
                $visaProfessionQty[] = $getProfData->eng_name.' : '.$visaQ;
            }

            // foreach ($visaprofessions as $key => $visaprofessionde) {
            //     if (isset($visa_openings[$key]) && $visa_openings[$key] != '' ) {
            //         $visaQ = $visa_openings[$key];
            //         $getAvailableProfessionID[] = $visaprofessionde->id;
            //     } else {
            //         $visaQ = "0";
            //     }

            //     $visaProfessionQty[] = $visaprofessionde->eng_name.' : '.$visaQ;
            // }


            // dd($visaProfessionQty);

            $visaBalanceData = [
                'visa_quantity' => $openings,
                'balance_visa'  => $balance_visa,
                'visaProfessionQty' => $visaProfessionQty
            ];


            // Get Availanle Profession for Assigning
            $getAvailableProfessions = Profession::wherein('id',$getAvailableProfessionID)->get();

            // dd($candempLists);

            $staffs = Admin::where('status','=',1)->orderBy('name')->get();
            $employercands = Employercandidate::where('emp_id','=',$id)->where('status','=',1)->get();


        return view('admin.employer.visa.show',compact('post2','employercands','post','staffs','getAvailableProfessions','visaBalanceData','visaProfession','total_exp','candempLists','partners','professions','expworkcities','candidates'));
    }



    public function deleteEmployerp(Request $request){
        // $visaDetal = Employer::find($request->delete_id);
        $visaDetal = Employerplus::find($request->delete_id);
        // $booking = Booking::find($visaDetal->booking_id);
        // $candidate = Candidate::find($visaDetal->cand_id);
        $emp_cand = Employercandidate::where('emp_id','=',$visaDetal->id)->get();

        // dd($emp_cand);

        // Action perform
        // if (isset($emp_cand)) {
        //     foreach ($emp_cand as $emp_cand2) {
        //         $candidate = Candidate::find($emp_cand2->cand_id);
        //         $candidate->status = true;
        //         $candidate->save();
        //     }


        //     $emp_cand->delete();
        // }


        if (isset($emp_cand)) {
            foreach ($emp_cand as $emp_cand2) {
                $candidate = Candidate::find($emp_cand2->cand_id);
                $candidate->status = true;
                $candidate->save();


                $emp_cand2->delete();
            }


        }


        $visaDetal->delete();

        return redirect()->back()->with('success','Employer Deleted!');


    }

    // For Employer Plus Section End

    public function indexJson(Request $request) {

    }





    public function indexpJson(Request $request)
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            // Disable 03-10-2023
            $posts = DB::table('bookings as booking')
            ->leftjoin('candidates as cand','booking.cand_id','=','cand.id')
            ->leftjoin('users as user','booking.user_id','=','user.id')
            // ->leftjoin('userprofiles as profile','user.id','=','profile.user_id')
            ->leftjoin('cities as city','user.city_id','=','city.id')
            ->select('booking.id','booking.cand_id as bkcandID','booking.reference_no','booking.booking_date','booking.booking_status','user.name as cuname','cand.cand_name','cand.pass_no','cand.job_type','cand.photo_file','user.mobile_no','user.photo','user.avatar_url','user.country_id as ContID','user.city_id as CityID','city.name as city')
            // ->where('booking.visa_status','=','1')
            ->get();

            // New Change 03-10-2023
            // $posts = DB::table('users as user')
            //     ->leftJoin('bookings as booking', function($qp){
            //         $qp->on('booking.user_id','=','user.id')->orderBy('booking.id','DESC')->limit(1);
            //     })
            //     ->leftJoin('candidates as cand','cand.id','=','booking.cand_id')
            //     ->leftJoin('userprofiles as profile','user.id','=','profile.user_id')
            //     ->leftJoin('cities as city','profile.city_id','=','city.id')
            //     ->select('user.*','booking.cand_id as bkcandID','booking.reference_no','booking.booking_date','booking.booking_status','user.name as cuname','cand.cand_name','cand.pass_no','cand.job_type','cand.photo_file','profile.mobile_no','profile.photo','profile.country_id as ContID','profile.city_id as CityID','city.name as city')
            //     ->where('booking.status','!=','0')
            //     ->get();


        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_employer == 1){
                $posts = DB::table('bookings as booking')
                ->leftjoin('candidates as cand','booking.cand_id','=','cand.id')
                ->leftjoin('users as user','booking.user_id','=','user.id')
                // ->leftjoin('userprofiles as profile','user.id','=','profile.user_id')
                ->leftjoin('cities as city','user.city_id','=','city.id')
                ->select('booking.id','booking.cand_id as bkcandID','booking.reference_no','booking.booking_date','booking.booking_status','user.name as cuname','cand.cand_name','cand.pass_no','cand.job_type','cand.photo_file','user.mobile_no','user.country_id as ContID','user.city_id as CityID','user.photo','user.avatar_url','city.name as city')
                // ->where('booking.visa_status','=','1')
                ->get();
            }else{
                $posts = DB::table('bookings as booking')
                ->leftjoin('candidates as cand','booking.cand_id','=','cand.id')
                ->leftjoin('users as user','booking.user_id','=','user.id')
                // ->leftjoin('userprofiles as profile','user.id','=','profile.user_id')
                ->leftjoin('cities as city','user.city_id','=','city.id')
                ->select('booking.id','booking.cand_id as bkcandID','booking.reference_no','booking.booking_date','booking.booking_status','user.name as cuname','cand.cand_name','cand.pass_no','cand.job_type','cand.photo_file','user.mobile_no','user.avatar_url','user.photo','user.country_id as ContID','user.city_id as CityID','city.name as city')
                // ->where('booking.visa_status','=','1')
                ->where('cand.admin_id','=',Auth::guard('admin')->user()->id)
                ->get();
            }
        }



        $data['data'] = $posts;
        return response()->json($data);
    }



    public function updateFilterList(Request $request){
        $post = Employefilterlist::where('admin_id','=',Auth::guard('admin')->user()->id)->count();

        if ($post > 0) {
            $updateF = Employefilterlist::where('admin_id','=',Auth::guard('admin')->user()->id)->first();

            if($request->countryf == 1){
                $updateF->country_filter = true;
            }else{
                $updateF->country_filter = false;
            }

            if($request->cityf == 1){
                $updateF->city_filter = true;
            }else{
                $updateF->city_filter = false;
            }

            if($request->booking_statusf == 1){
                $updateF->booking_status_filter = true;
            }else{
                $updateF->booking_status_filter = false;
            }

            if($request->booking_datef == 1){
                $updateF->booking_date_filter = true;
            }else{
                $updateF->booking_date_filter = false;
            }

            if($request->candidate_namef == 1){
                $updateF->candidate_name_filter = true;
            }else{
                $updateF->candidate_name_filter = false;
            }

            $updateF->save();
            return response()->json('success');

        } else {
            $newFilter = new Employefilterlist();

            $newFilter->admin_id = Auth::guard('admin')->user()->id;

            if($request->countryf == 1){
                $newFilter->country_filter = true;
            }else{
                $newFilter->country_filter = false;
            }

            if($request->cityf == 1){
                $newFilter->city_filter = true;
            }else{
                $newFilter->city_filter = false;
            }

            if($request->booking_statusf == 1){
                $newFilter->booking_status_filter = true;
            }else{
                $newFilter->booking_status_filter = false;
            }

            if($request->booking_datef == 1){
                $newFilter->booking_date_filter = true;
            }else{
                $newFilter->booking_date_filter = false;
            }

            if($request->candidate_namef == 1){
                $newFilter->candidate_name_filter = true;
            }else{
                $newFilter->candidate_name_filter = false;
            }

            $newFilter->save();

            return response()->json('success');
        }

    }

    public function showp($id){
        $post = DB::table('bookings as booking')
        ->leftjoin('candidates as cand','booking.cand_id','=','cand.id')
        ->leftjoin('users as user','booking.user_id','=','user.id')
        // ->leftjoin('userprofiles as profile','user.id','=','profile.user_id')
        ->leftjoin('cities as city','user.city_id','=','city.id')
        ->leftJoin('expecworkcities as expecs','expecs.id','=','booking.worklocation')
        ->leftJoin('professions as proff','proff.id','=','cand.jobtype_id')
        ->leftJoin('visadetails as visa','booking.id','=','visa.booking_id')
        ->leftJoin('professions as vproff','vproff.id','=','visa.proff_id')
        ->select(
            'booking.*',
            'user.name as cuname',
            'cand.cand_name',
            'cand.pass_no',
            'cand.pass_file',
            'cand.pass_back_file',
            'cand.lic_file',
            'cand.cv_file',
            'cand.photo_file',
            'proff.eng_name as pengname',
            'expecs.name as worklocationname',
            'vproff.eng_name as vpengname',
            'cand.dob as candob',
            'cand.exp_sal',
            'cand.marital_status',
            'cand.experience',
            'cand.overall_exp',
            'cand.expcity_id',
            'cand.photo_file',
            'user.mobile_no',
            'user.avatar_url',
            'user.photo',
            'city.name as citname',
            'user.address as cuaddr',
            'user.email as uemail',
            'visa.visa_no','visa.id_no',
            'visa.employer_name',
            'visa.employer_ar_name',
            'visa.issuing_authority',

            )
        ->where('booking.id','=',$id)
        ->first();

        $total_exp = array_sum(explode(',',$post->experience));
        $expcities = Expecworkcity::wherein('id',explode(',',$post->expcity_id))->get();
        $mycity = [];
        foreach ($expcities as $expcity) {
            $mycity[] =  $expcity->name;
        }

        $relBookings = DB::table('bookings as booking2')
        ->leftjoin('candidates as cand','booking2.cand_id','=','cand.id')
        ->leftjoin('users as user','booking2.user_id','=','user.id')
        // ->leftjoin('userprofiles as profile','user.id','=','profile.user_id')
        ->leftjoin('cities as city','user.city_id','=','city.id')
        ->select('booking2.id','booking2.cand_id as bkcandID','booking2.reference_no','booking2.booking_date','booking2.booking_status','user.name as cuname','cand.cand_name','cand.pass_no','cand.job_type','cand.photo_file','user.mobile_no','user.photo','city.name as city')
        ->where('booking2.user_id','=',$post->user_id)
        ->get();

        return view('admin.employer.show',compact('post','total_exp','mycity','relBookings'));

    }

    public function VisaIndexJson(Request $request){

        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $post = DB::table('visadetails as visadetail')
            ->leftJoin('partners as partner','visadetail.partner_office_id','=','partner.id')
            ->leftjoin('professions as profess','visadetail.proff_id','=','profess.id')
            ->leftjoin('expecworkcities as expwork','visadetail.wpcity_id','=','expwork.id')
            ->leftjoin('admins as admin','visadetail.admin_id','=','admin.id')
            ->select('visadetail.*','partner.rec_off_name','profess.eng_name as peng_name','profess.ar_name as par_name','expwork.name as expworkname','expwork.arname as expworkarname')
            ->whereNotNull('visadetail.booking_id')
            ->get();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_employer == 1){
                $post = DB::table('visadetails as visadetail')
                ->leftJoin('partners as partner','visadetail.partner_office_id','=','partner.id')
                ->leftjoin('professions as profess','visadetail.proff_id','=','profess.id')
                ->leftjoin('expecworkcities as expwork','visadetail.wpcity_id','=','expwork.id')
                ->leftjoin('admins as admin','visadetail.admin_id','=','admin.id')
                ->select('visadetail.*','partner.rec_off_name','profess.eng_name as peng_name','profess.ar_name as par_name','expwork.name as expworkname','expwork.arname as expworkarname')
                ->whereNotNull('visadetail.booking_id')
                ->get();
            }else{
                $post = DB::table('visadetails as visadetail')
                ->leftJoin('partners as partner','visadetail.partner_office_id','=','partner.id')
                ->leftjoin('professions as profess','visadetail.proff_id','=','profess.id')
                ->leftjoin('expecworkcities as expwork','visadetail.wpcity_id','=','expwork.id')
                ->leftjoin('admins as admin','visadetail.admin_id','=','admin.id')
                ->select('visadetail.*','partner.rec_off_name','profess.eng_name as peng_name','profess.ar_name as par_name','expwork.name as expworkname','expwork.arname as expworkarname')
                ->where('visadetail.admin_id','=',Auth::guard('admin')->user()->id)
                ->whereNotNull('visadetail.booking_id')
                ->get();
            }
        }



        $data['data'] = $post;
        return response()->json($data);
    }





    public function VisaIndexpJson(Request $request){

        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $post = DB::table('visadetails as visadetail')
            ->leftJoin('partners as partner','visadetail.partner_office_id','=','partner.id')
            ->leftjoin('professions as profess','visadetail.proff_id','=','profess.id')
            ->leftjoin('expecworkcities as expwork','visadetail.wpcity_id','=','expwork.id')
            ->leftjoin('admins as admin','visadetail.admin_id','=','admin.id')
            ->select('visadetail.*','partner.rec_off_name','profess.eng_name as peng_name','profess.ar_name as par_name','expwork.name as expworkname','expwork.arname as expworkarname')
            ->whereNull('visadetail.booking_id')
            ->get();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_employer == 1){
                $post = DB::table('visadetails as visadetail')
                ->leftJoin('partners as partner','visadetail.partner_office_id','=','partner.id')
                ->leftjoin('professions as profess','visadetail.proff_id','=','profess.id')
                ->leftjoin('expecworkcities as expwork','visadetail.wpcity_id','=','expwork.id')
                ->leftjoin('admins as admin','visadetail.admin_id','=','admin.id')
                ->select('visadetail.*','partner.rec_off_name','profess.eng_name as peng_name','profess.ar_name as par_name','expwork.name as expworkname','expwork.arname as expworkarname')
                ->whereNull('visadetail.booking_id')
                ->get();
            }else{
                $post = DB::table('visadetails as visadetail')
                ->leftJoin('partners as partner','visadetail.partner_office_id','=','partner.id')
                ->leftjoin('professions as profess','visadetail.proff_id','=','profess.id')
                ->leftjoin('expecworkcities as expwork','visadetail.wpcity_id','=','expwork.id')
                ->leftjoin('admins as admin','visadetail.admin_id','=','admin.id')
                ->select('visadetail.*','partner.rec_off_name','profess.eng_name as peng_name','profess.ar_name as par_name','expwork.name as expworkname','expwork.arname as expworkarname')
                ->where('visadetail.admin_id','=',Auth::guard('admin')->user()->id)
                ->whereNull('visadetail.booking_id')
                ->get();
            }
        }



        $data['data'] = $post;
        return response()->json($data);
    }







    // public function VisaDetView($id){
    //     $post = DB::table('visadetails as visadetail')
    //         ->leftJoin('partners as partner','visadetail.partner_office_id','=','partner.id')
    //         ->leftJoin('bookings as booking','visadetail.booking_id','=','booking.id')
    //         ->leftJoin('users as user','visadetail.user_id','=','user.id')
    //         ->leftjoin('candidates as cand','visadetail.cand_id','=','cand.id')
    //         ->leftJoin('professions as proff','visadetail.proff_id','=','proff.id')
    //         ->leftJoin('professions as candproff','cand.jobtype_id','=','candproff.id')
    //         ->leftJoin('expecworkcities as expwork','visadetail.wpcity_id','=','expwork.id')
    //         ->leftJoin('admins as admin','visadetail.admin_id','=','admin.id')
    //         ->select(
    //             'visadetail.*',
    //             'user.mobile_no as umobno',
    //             'user.avatar_url as uavatar_url',
    //             'user.photo as uphoto',
    //             'cand.cand_name',
    //             'cand.pass_no',
    //             'cand.dob as candob',
    //             'candproff.eng_name as candprofession',
    //             'cand.experience as candexperience',
    //             'cand.pass_file',
    //             'cand.pass_back_file',
    //             'cand.lic_file',
    //             'cand.cv_file',
    //             'cand.photo_file',
    //             'cand.overall_exp',
    //             'proff.eng_name as pengname',
    //             'expwork.name as expworkname'
    //             )
    //         ->where('visadetail.id','=',$id)
    //         ->first();

    //         $total_exp = array_sum(explode(',',$post->candexperience));

    //         // $candempLists = Employercandidate::where('emp_id','=',$id)->where('status',1)->get();
    //         $candempLists = DB::table('employercandidates as employercandidate')
    //             ->leftJoin('candidates as cand22','cand22.id','=','employercandidate.cand_id')
    //             ->leftJoin('professions as profff','profff.id','=','employercandidate.proff_id')
    //             ->select('employercandidate.*','cand22.cand_name as candname','cand22.pass_no as candpassno','cand22.dob as candob','cand22.overall_exp as canoverall_exp','profff.eng_name as profffengname')
    //             ->where('employercandidate.status',1)
    //             ->where('employercandidate.id',$id)
    //             ->get();


    //         $partners = Partner::where('status','=',1)->orderBy('rec_off_name')->get();
    //         $professions = Profession::orderBy('eng_name')->get();
    //         $expworkcities = Expecworkcity::orderBy('name')->get();
    //         $candidates = Candidate::orderBy('cand_name')->where('status',1)->get();



    //         $visaprofessions = Profession::wherein('id',explode(',',$post->proff_id))->get();
    //         $visaProfession = [];
    //         foreach ($visaprofessions as $visaprofessionde) {
    //             $visaProfession[] = $visaprofessionde->eng_name;
    //         }

    //     return view('admin.employer.visa.show',compact('post','visaProfession','total_exp','candempLists','partners','professions','expworkcities','candidates'));
    // }

    public function assigngetdata(Request $request){
        $post = DB::table('employercandidates as empcand')
            ->leftJoin('candidates as cand','cand.id','=','empcand.cand_id')
            ->select('cand.*')
            ->where('empcand.id','=',$request->id)
            ->first();

        return response()->json($post);

    }

    public function deassigngetdata(Request $request) {

        try {
            // Update status false in employer section
            $postDeass = Employercandidate::find($request->candemp_id);
            $postDeass->status = false;
            $postDeass->save();
            // Release candidate
            $postCand = Candidate::find($postDeass->cand_id);

            $postCand->status = true;
            $postCand->save();

            $data = [
                'status' => 1,
                'message' => 'Candidate successfully Deassign'
            ];

        } catch (\Throwable $th) {
            $data = [
                'status' => 0,
                'message' => 'Something went wrong!'
            ];
        }


        return response()->json($data);


    }


    public function assigncandtoemp(Request $request)
    {
        try {
            $isEmployerPlus = $request->fromreq == 'employerplus';

            // Determine the appropriate models and columns based on employer type
            $employerModel = $isEmployerPlus ? Employerplus::class : Employer::class;
            $employerColumn = $isEmployerPlus ? 'emp_id' : 'emp2_id';

            // Check if visa slots are available
            $checkEmployersOpening = $employerModel::whereRaw("FIND_IN_SET(?, proff_id)", [$request->proff_id])
                ->where('id', $request->visaeditid)
                ->count();


            $checkAssignEmp = Employercandidate::where($employerColumn, $request->visaeditid)
                ->where('proff_id', $request->proff_id)
                ->where('status', 1)
                ->count();

            // Get Partner office Data
            $partner = $employerModel::find($request->visaeditid);

            $proffIds = explode(',', $partner->proff_id);
            $proffOpenings = explode(',', $partner->openings);
            $currentOpenings = [];
            foreach ($proffIds as $key => $proffId) {
                if ($proffId == $request->proff_id) {
                    $currentOpenings[] = $proffOpenings[$key];
                }
            }

            // if ($checkEmployersOpening > $checkAssignEmp) {
            if($currentOpenings[0] > $checkAssignEmp){
                // Assign candidate to employer
                $postAssign = new Employercandidate();
                $postAssign->{$employerColumn} = $request->visaeditid;
                $postAssign->cand_id = $request->cand_id;
                $postAssign->proff_id = $request->proff_id;
                $postAssign->partneroffice_id = $partner->partneroffice_id;
                $postAssign->assignbystaff_id = Auth::guard('admin')->user()->id;
                $postAssign->assignbydate = now(); // Use Laravel's `now()` helper
                $postAssign->save();

                // Update candidate status
                // Candidate::where('id', $request->cand_id)->update(['status' => false]);
                $candidate_list = Candidate::find($request->cand_id);
                $candidate_list->status = false;
                if ($candidate_list->cand_payment_status == '') {
                    $candidate_list->cand_payment_status = 'Unpaid';
                }
                $candidate_list->save();

                $data = [
                    'status' => 1,
                    'message' => 'Candidate successfully assigned'
                ];
            } else {
                $data = [
                    'status' => 0,
                    'message' => 'Visa slot not available for this candidate!'
                ];
            }
        } catch (\Throwable $th) {
            // Log the error for debugging
            \Log::error('Error assigning candidate to employer: ' . $th->getMessage());

            $data = [
                'status' => 0,
                'message' => $th->getMessage()
                // 'message' => 'Something went wrong!'
            ];
        }

        return response()->json($data);
    }

    // public function assigncandtoemp(Request $request)
    // {
    //     $result = Helper::assignCandidateToEmployer(
    //         $request->only([
    //             'visaeditid',
    //             'fromreq',
    //             'cand_id',
    //             'proff_id'
    //         ])
    //     );

    //     return response()->json($result);
    // }

    // Upgrade Code For Assign candidate to employer End


    public function checkEmpVisa(Request $request){
        $post = Employerplus::where('visa_no','=',$request->visa_no)->count();
        $post2 = Employer::where('visa_no','=',$request->visa_no)->count();

        if ($post == 0 && $post2 == 0) {
            $isAvailable = 'true';
        } else {
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));

    }

    public function edcheckEmpVisa(Request $request){
        $post = Employerplus::where('visa_no','=',$request->visa_no)->where('id','!=',$request->id)->count();
        $post2 = Employer::where('visa_no','=',$request->visa_no)->count();
        if($post == 0 && $post2 == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function empCheckVisa(Request $request){
        $post = Visadetails::where('visa_no','=',$request->visa_no)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }


    public function empedcheckVisa(Request $request){
        $post = Visadetails::where('visa_no','=',$request->visa_no)->where('id','!=',$request->id)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }






    public function AddVisaCandidate(Request $request)
{
    // Check if the candidate already exists
    $data = Visadetails::where('cand_id', $request->cand_id)->first();

    if ($data) {
        return response()->json(['success' => false, 'message' => 'Candidate Already Exists']);
    } else {
        // Find the record by visaeditid
        $post = Visadetails::find($request->visaeditid);

        if ($post) { // Ensure the record exists
            $post->cand_id = $request->cand_id;
            $post->save();

            // Retrieve the candidate data
            $postDetails = DB::table('visadetails as visadetail')
                ->leftJoin('partners as partner', 'visadetail.partner_office_id', '=', 'partner.id')
                ->leftJoin('bookings as booking', 'visadetail.booking_id', '=', 'booking.id')
                ->leftJoin('users as user', 'visadetail.user_id', '=', 'user.id')
                ->leftJoin('candidates as cand', 'visadetail.cand_id', '=', 'cand.id')
                ->leftJoin('professions as proff', 'visadetail.proff_id', '=', 'proff.id')
                ->leftJoin('professions as candproff', 'cand.jobtype_id', '=', 'candproff.id')
                ->leftJoin('expecworkcities as expwork', 'visadetail.wpcity_id', '=', 'expwork.id')
                ->leftJoin('admins as admin', 'visadetail.admin_id', '=', 'admin.id')
                ->select(
                    'visadetail.*',
                    'user.mobile_no as umobno',
                    'user.avatar_url as uavatar_url',
                    'user.photo as uphoto',
                    'cand.cand_name',
                    'cand.pass_no',
                    'cand.dob as candob',
                    'candproff.eng_name as candprofession',
                    'cand.experience as candexperience',
                    'cand.pass_file',
                    'cand.pass_back_file',
                    'cand.lic_file',
                    'cand.cv_file',
                    'cand.photo_file',
                    'cand.overall_exp',
                    'proff.eng_name as pengname',
                    'expwork.name as expworkname'
                )
                ->where('visadetail.id', '=', $request->visaeditid)
                ->first();

            // Calculate total experience
            $total_exp = array_sum(explode(',', $postDetails->candexperience));

            // Get other necessary data
            $partners = Partner::where('status', '=', 1)->orderBy('rec_off_name')->get();
            $professions = Profession::orderBy('eng_name')->get();
            $expworkcities = Expecworkcity::orderBy('name')->get();
            $candidates = Candidate::orderBy('cand_name')->get();

            // Return the response
            return response()->json([
                'success' => true,
                'post' => $postDetails,
                'total_exp' => $total_exp,
                'partners' => $partners,
                'professions' => $professions,
                'expworkcities' => $expworkcities,
                'candidates' => $candidates,
                'message' => 'Candidate Details updated successfully!'
            ]);

        } else {
            return response()->json(['success' => false, 'message' => 'Record not found.']);
        }
    }
}



    public function RemoveVisaCandidate($id)
    {
        // Logic to remove the visa candidate by ID
        // For example:

        $candidate = Visadetails::find($id);
        if ($candidate) {
            $candidate->cand_id = null;
            $candidate->save();
            return response()->json(['success' => true]);
        }
         return response()->json(['success' => false], 404);
    }





}
