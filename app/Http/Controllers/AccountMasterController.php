<?php
namespace App\Http\Controllers;

use App\AdminModel\AccessPermissionModule2;
use App\AdminModel\AdminStatusAcc;
use App\AdminModel\Candidate;
use App\AdminModel\DepositiAccount;
use App\AdminModel\PaymentForAcc;
use App\AdminModel\PaymentMethod;
use App\AdminModel\UserStatusAcc;
use App\AdminModel\DailyTransaction;
use App\AdminModel\Dailytxncampaignlist;
use App\AdminModel\Party;
use App\AdminModel\Staff;
use App\Jobs\DailyTransactionJob;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
// use Yajra\DataTables\Contracts\DataTable;
// use Yajra\DataTables\Facades\DataTables;
use DataTables;
use DB;

class AccountMasterController extends Controller
{
    public function paymentM()
    {
        $pageConfigs = ['pageHeader' => false];
        return view('content.service-master.payment_method',['pageConfigs' => $pageConfigs]);
    }

    public function paymentMJson(Request $request)
    {
        $post = PaymentMethod::all();
        $data['data'] = $post;
        return response()->json($data);
    }

    public function paymentMStore(Request $request)
    {
        $post = new PaymentMethod();
        $post->name = $request->name;
        $post->user_id = Auth::user()->user_id;
        $post->save();
        Session::flash('success','Payment Method store!');
        return redirect()->back();
    }

    public function paymentF()
    {
        $pageConfigs = ['pageHeader' => false];
        return view('content.service-master.payment_for',['pageConfigs' => $pageConfigs]);
    }

    public function paymentFJson(Request $request)
    {
        $post = PaymentForAcc::all();
        $data['data'] = $post;
        return response()->json($data);
    }

    public function paymentFStore(Request $request)
    {
        $post = new PaymentForAcc();
        $post->name = $request->name;
        $post->user_id = Auth::user()->user_id;
        $post->save();
        Session::flash('success','Payment For created!');
        return redirect()->back();
    }

    public function userSt()
    {
        $pageConfigs = ['pageHeader' => false];
        return view('content.service-master.user_status',['pageConfigs' => $pageConfigs]);
    }

    public function userStJson(Request $request)
    {
        $post = UserStatusAcc::all();
        $data['data'] = $post;
        return response()->json($data);
    }

    public function userStStore(Request $request)
    {
        $post = new UserStatusAcc();
        $post->name = $request->name;
        $post->user_id = Auth::user()->user_id;
        $post->save();

        Session::flash('success','User Status craeted!');
        return redirect()->back();
    }

    public function adminSt()
    {
        $pageConfigs = ['pageHeader' => false];
        return view('content.service-master.admin_status',['pageConfigs' => $pageConfigs]);
    }

    public function adminStJson(Request $request)
    {
        $post = AdminStatusAcc::all();
        $data['data'] = $post;
        return response()->json($data);
    }

    public function adminStStore(Request $request)
    {
        $post = new AdminStatusAcc();
        $post->name = $request->name;
        $post->user_id = Auth::user()->user_id;
        $post->save();

        Session::flash('success','Admin Status store!');
        return redirect()->back();
    }

    public function deposti()
    {
        $pageConfigs = ['pageHeader' => false];
        return view('content.service-master.deposti_account',['pageConfigs' => $pageConfigs]);
    }

    public function depostiJson(Request $request)
    {
        $post = DepositiAccount::all();
        $data['data'] = $post;
        return response()->json($data);
    }

    public function depostiStore(Request $request)
    {
        $post = new DepositiAccount();
        $post->account_holder_name = $request->name;
        $post->bank_name = $request->bank_name;
        $post->account_no = $request->account_no;
        $post->ifsc_code = $request->ifsc_code;
        $post->branch_name = $request->branch_name;
        $post->save();

        Session::flash('success','Deposti Account save');
        return redirect()->back();
    }

    public function dailytxn()
    {
        $pageConfigs = ['pageHeader' => false];
        return view('content.service-master.daily_txn',['pageConfigs' => $pageConfigs]);
    }

    public function dailytxnJson(Request $request)
    {
        // $post = DB::table('daily_transactions as dtxn')
        //     ->leftjoin('qr_party_tbl as party','party.pty_id','=','dtxn.pty_id')
        //     ->leftjoin('payment_methods as pmet','pmet.id','=','dtxn.pmethod_id')
        //     ->leftjoin('payment_for_accs as pmfor','pmfor.id','=','dtxn.paymentf_id')
        //     ->leftjoin('depositi_accounts as depacc','depacc.id','=','dtxn.depositi_id')
        //     ->leftjoin('user_status_accs as userSt','userSt.id','=','dtxn.userst_id')
        //     ->leftjoin('admin_status_accs as adminSt','adminSt.id','=','dtxn.adminst_id')
        //     ->leftjoin('users as userC','userC.user_id','=','dtxn.careoff_id')
        //     ->leftjoin('users as user','user.user_id','=','dtxn.user_id')
        //     ->leftjoin('user as userUp','userUp.user_id','=','dtxn.updateby_id')
        //     ->leftjoin('qr_branch_details as branch','branch.br_id','=','dtxn.br_id')
        //     ->select('dtxn.*','party.pty_ag_name','pmet.name as pmname','pmfor.name as pmfname','depacc.account_holder_name','userSt.name as ustname','adminSt.name as astname','userC.name as ucname','user.name as uname','userUp.name as upname','branch.br_name')
        //     ->get();

            // $post = DailyTransaction::all();

        $perms = AccessPermissionModule2::where('user_id','=',Auth::user()->user_id)->first();

        if(Auth::user()->user_type == 1 || (isset($perms) && $perms->full_access == 1)){

            $post = DB::table('daily_transactions as dtxn')
            ->leftjoin('qr_party_tbl as party','party.pty_id','=','dtxn.pty_id')
            ->leftjoin('payment_methods as pmet','pmet.id','=','dtxn.pmethod_id')
            ->leftjoin('payment_for_accs as pmfor','pmfor.id','=','dtxn.paymentf_id')
            ->leftjoin('depositi_accounts as depacc','depacc.id','=','dtxn.depositi_id')
            ->leftjoin('user_status_accs as userSt','userSt.id','=','dtxn.userst_id')
            ->leftjoin('admin_status_accs as adminSt','adminSt.id','=','dtxn.adminst_id')
            ->leftjoin('users as userC','userC.user_id','=','dtxn.careoff_id')
            ->leftjoin('users as user','user.user_id','=','dtxn.user_id')
            ->leftjoin('users as userUp','userUp.user_id','=','dtxn.updateby_id')
            ->leftjoin('qr_branch_details as branch','branch.br_id','=','dtxn.br_id')
            ->select('dtxn.*','party.pty_ag_name','pmet.name as pmname','pmfor.name as pmfname','depacc.account_holder_name','userSt.name as ustname','adminSt.name as astname','userC.name as ucname','user.name as uname','userUp.name as upname','branch.br_name')
            ->get();


        }elseif(Auth::user()->user_type == 2 || (isset($perms) && $perms->full_access == 0)){
            if($perms->dtxnview == 1){
                $post = DB::table('daily_transactions as dtxn')
                ->leftjoin('qr_party_tbl as party','party.pty_id','=','dtxn.pty_id')
                ->leftjoin('payment_methods as pmet','pmet.id','=','dtxn.pmethod_id')
                ->leftjoin('payment_for_accs as pmfor','pmfor.id','=','dtxn.paymentf_id')
                ->leftjoin('depositi_accounts as depacc','depacc.id','=','dtxn.depositi_id')
                ->leftjoin('user_status_accs as userSt','userSt.id','=','dtxn.userst_id')
                ->leftjoin('admin_status_accs as adminSt','adminSt.id','=','dtxn.adminst_id')
                ->leftjoin('users as userC','userC.user_id','=','dtxn.careoff_id')
                ->leftjoin('users as user','user.user_id','=','dtxn.user_id')
                ->leftjoin('users as userUp','userUp.user_id','=','dtxn.updateby_id')
                ->leftjoin('qr_branch_details as branch','branch.br_id','=','dtxn.br_id')
                ->select('dtxn.*','party.pty_ag_name','pmet.name as pmname','pmfor.name as pmfname','depacc.account_holder_name','userSt.name as ustname','adminSt.name as astname','userC.name as ucname','user.name as uname','userUp.name as upname','branch.br_name')
                ->get();
            }else{
                $post = DB::table('daily_transactions as dtxn')
                    ->leftjoin('qr_party_tbl as party','party.pty_id','=','dtxn.pty_id')
                    ->leftjoin('payment_methods as pmet','pmet.id','=','dtxn.pmethod_id')
                    ->leftjoin('payment_for_accs as pmfor','pmfor.id','=','dtxn.paymentf_id')
                    ->leftjoin('depositi_accounts as depacc','depacc.id','=','dtxn.depositi_id')
                    ->leftjoin('user_status_accs as userSt','userSt.id','=','dtxn.userst_id')
                    ->leftjoin('admin_status_accs as adminSt','adminSt.id','=','dtxn.adminst_id')
                    ->leftjoin('users as userC','userC.user_id','=','dtxn.careoff_id')
                    ->leftjoin('users as user','user.user_id','=','dtxn.user_id')
                    ->leftjoin('users as userUp','userUp.user_id','=','dtxn.updateby_id')
                    ->leftjoin('qr_branch_details as branch','branch.br_id','=','dtxn.br_id')
                    ->select('dtxn.*','party.pty_ag_name','pmet.name as pmname','pmfor.name as pmfname','depacc.account_holder_name','userSt.name as ustname','adminSt.name as astname','userC.name as ucname','user.name as uname','userUp.name as upname','branch.br_name')
                    ->where('dtxn.user_id','=',Auth::user()->user_id)
                    ->orWhere('dtxn.careoff_id','=',Auth::user()->user_id)
                    ->get();
            }
        }

        

        // $data['data'] = $post;
        // return response()->json($data);
        return DataTables::of($post)->toJson();
    }

    public function dailytxnStore(Request $request)
    {
        // check and get file
        if ($request->hasFile('slip')) {
            $file = $request->file('slip');
            $name = time().'_'.$file->getClientOriginalName();
            $file->move(base_path().'/public/image/accounts',$name);
            $slip = $name;
        }else{
            $slip = '';
        }

        // Get Br_id
        if (Auth::user()->user_type != 1) {
            $branch = Staff::where('staff_id','=',Auth::user()->user_id)->first();
            $branch_id = $branch->staff_branch;
        } else {
            $branch_id = null;
        }
        

        $post = new DailyTransaction();
        $post->pty_id = $request->pty_id;
        $post->desc = $request->desc;
        $post->candidate_name = $request->candidate_name;
        $post->cand_pass_no = $request->cand_pass_no;
        $post->invoice_no = $request->invoice_no;
        $post->amount = $request->amount;
        $post->txn_utr_no = $request->txn_utr_no;
        $post->txn_date = $request->txn_date;
        $post->pmethod_id = $request->pmethod_id;
        $post->depositi_id = $request->depositi_id;
        $post->paymentf_id = $request->paymentf_id;
        $post->careoff_id = $request->careoff_id;
        $post->br_id = $branch_id;
        $post->user_id = Auth::user()->user_id;
        $post->userst_id = $request->userst_id;
        $post->adminst_id = $request->adminst_id;
        $post->slip = $slip;
        $post->save();

        // Payment Method
        $pmethod = PaymentMethod::find($request->pmethod_id);
        // Payment For
        $paymentfor = PaymentForAcc::find($request->paymentf_id);
        // Party Details
        $partyDet = Party::where('pty_id','=',$request->pty_id)->where('pty_id','!=',200)->first();

        // Create Job and Campaign List
        $dtxnlist = new Dailytxncampaignlist();
        $dtxnlist->desc = $request->desc;
        $dtxnlist->cand_name = $request->candidate_name;
        $dtxnlist->pass_no = $request->cand_pass_no;
        $dtxnlist->amount = $request->amount;
        $dtxnlist->txn_id = $request->txn_utr_no;
        $dtxnlist->txn_date = $request->txn_date;
        $dtxnlist->file = $slip;
        
        if (isset($pmethod)) {
            $dtxnlist->paymethod = $pmethod->name;
        } 
        
        if (isset($paymentfor)) {
            $dtxnlist->payfor = $paymentfor->name;
        } 

        if (isset($partyDet)) {
            $dtxnlist->pty_name = $partyDet->pty_full_name;
            $dtxnlist->pty_ag_name = $partyDet->pty_ag_name;
        }

        

        $dtxnlist->save();

        // Staff Details
        $staff = User::where('user_id','=',Auth::user()->user_id)->first();
        

        DailyTransactionJob::dispatch($dtxnlist,$post,$staff)->onQueue('dtxn');


        Session::flash('success','Daily transaction created!');
        return redirect()->back();
    }

    public function getAdminST(Request $request){
        $post = DailyTransaction::find($request->id);
        return response()->json($post);
    }

    public function setAdminST(Request $request){
        $post = DailyTransaction::find($request->id);
        $post->adminst_id = $request->adminst_id;
        $post->save();

        Session::flash('success','Admin Status updated!');
        return redirect()->back();
    }

    public function getUserST(Request $request){
        $post = DailyTransaction::find($request->id);
        return response()->json($post);
    }

    public function setUserST(Request $request){
        $post = DailyTransaction::find($request->id);
        $post->userst_id = $request->userst_id;
        $post->save();

        Session::flash('success','User Status updated!');
        return redirect()->back();
    }

    public function checkUTRno(Request $request){
        $txn_no = $request->txn_utr_no;
        $post = DailyTransaction::where('txn_utr_no','=',$txn_no)->count();

        if($post == 0){
            echo "true";
          }else{
            echo "false";
          }
    }

    public function editTxn(Request $request)
    {
        $post = DailyTransaction::find($request->id);
        return response()->json($post);
    }

    public function delDtxn(Request $request)
    {
        $post = DailyTransaction::find($request->id);
        $post->delete();
        Session::flash('success','Daily transaction deleted!');
        return redirect()->back();
    }

    public function checkpass(Request $request)
    {
        $post = Candidate::where('cand_passport_no','=',$request->pass_no)->first();
        if (isset($post)) {
            $data = $post;
        } else {
            $data = ["none" => 1];
        }

        return response()->json($data);
        
    }

    public function dailytxnUpdate(Request $request)
    {
        $post = DailyTransaction::find($request->txn_edit_id);

        // check and get file
        if ($request->hasFile('slip')) {
            $file = $request->file('slip');
            $name = time().'_'.$file->getClientOriginalName();
            $file->move(base_path().'/public/image/accounts',$name);
            $slip = $name;
        }else{
            $slip = $post->slip;
        }

        // Update in Database

        $post->pty_id = $request->pty_id;
        $post->desc = $request->desc;
        $post->candidate_name = $request->candidate_name;
        $post->cand_pass_no = $request->cand_pass_no;
        $post->invoice_no = $request->invoice_no;
        $post->amount = $request->amount;
        $post->txn_utr_no = $request->txn_utr_no;
        $post->txn_date = $request->txn_date;
        $post->pmethod_id = $request->pmethod_id;
        $post->depositi_id = $request->depositi_id;
        $post->paymentf_id = $request->paymentf_id;
        $post->careoff_id = $request->careoff_id;
        $post->userst_id = $request->userst_id;
        $post->adminst_id = $request->adminst_id;
        $post->slip = $slip;
        $post->save();

        Session::flash('success','Daily transaction updated!');
        return redirect()->back();

    }

}
