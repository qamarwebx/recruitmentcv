<?php

namespace App\Http\Controllers;

use App\Models\Adminpermission;
use App\Models\Basepathstatus;
use App\Models\Expense;
use App\Models\Expensecategory;
use App\Models\Expensefor;
use App\Models\Expensesaveadminfilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{

    // Expense Section

    public function index(Request $request){
        $user_id = Auth::guard('admin')->user()->id;
        $categories = Expensecategory::orderBy('name')->where('status',true)->get();
        $expensefors = Expensefor::orderBy('name')->get();
        $adminpermission = Adminpermission::where('staff_id','=',$user_id)->first();
        $expensesaveadminfilter = Expensesaveadminfilter::where('admin_id','=',$user_id)->first();

        // Filter Field
        $expenseforFields = Expense::with('expensefor')->groupBy('expensefor_id')->where('expensefor_id','!=','')->select('expensefor_id')->get();
        $expensetypeFields = Expense::with('expensecat')->groupBy('expensecat_id')->where('expensecat_id','!=','')->select('expensecat_id')->get();
        // Fixed canonical list (matches the Add/Edit Payment Mode options) rather
        // than distinct-in-use values, so the filter (and its status cards) always
        // offer every mode even if it has no expenses logged against it yet.
        $paymentmodeFields = collect(['Cash', 'UPI', 'Netbanking', 'Cheque'])
            ->map(fn ($mode) => (object) ['payment_mode' => $mode]);
        $createbyFields = Expense::with('admin')->groupBy('admin_id')->select('admin_id')->get();

        // dd($expenseforFields);

        $post = Expense::with(['expensecat','expensefor','admin']);

        if (Auth::guard('admin')->user()->user_type == 1 || (isset($adminpermission) && $adminpermission->full_access == 1)) {

            if ($request->ajax()) {
                $post->FilterExpenseFor($request->expensefor)
                ->FilterExpenseCat($request->expensecat)
                ->FilterPaymentFor($request->paymentmode)
                ->FilterCreateBy($request->createby_id)
                ->FilterDateRange('expense_date',$request->payment_date)
                ->FilterSearchText($request->search_text);

                $posts = $post->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();
                return view('admin.expense.indexload',compact('posts','adminpermission'));
            }

            if ($expensesaveadminfilter) {
                $expensefor = $request->expensefor ? explode(",",$expensesaveadminfilter->expensefor) :'';
                $expensetype = $expensesaveadminfilter->expensetype ? explode(",",$expensesaveadminfilter->expensetype) :'';
                $payment_mode = $expensesaveadminfilter->payment_mode ? explode(",",$expensesaveadminfilter->payment_mode) :'';
                $createby = $expensesaveadminfilter->createby ? explode(",",$expensesaveadminfilter->createby) :'';

                $post->FilterExpenseFor($expensefor)
                ->FilterExpenseCat($expensetype)
                ->FilterPaymentFor($payment_mode)
                ->FilterCreateBy($createby)
                ->FilterDateRange('expense_date',$expensesaveadminfilter->payment_date);
            }

            $posts = $post->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

            $summaryCounts = $this->buildSummaryCounts(true, $user_id);

            return view('admin.expense.index',compact('categories','paymentmodeFields','expenseforFields','createbyFields','expensetypeFields','posts','expensefors','adminpermission','expensesaveadminfilter','summaryCounts'));

        }elseif (Auth::guard('admin')->user()->user_type == 2 && (isset($adminpermission) && $adminpermission->full_access == 0)) {
            

                if ($request->ajax()) {
                    $post->FilterExpenseFor($request->expensefor)
                    ->FilterExpenseCat($request->expensecat)
                    ->FilterPaymentFor($request->paymentmode)
                    ->FilterCreateBy($request->createby_id)
                    ->FilterDateRange('expense_date',$request->payment_date)
                    ->FilterSearchText($request->search_text);

                    if ($adminpermission->view_expense == 1) { // view All
                        $posts = $post->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();
                    }else{
                        $posts = $post->where('admin_id',$user_id)->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();
                    }
    
                    return view('admin.expense.indexload',compact('posts','adminpermission'));
                }
    
                if ($expensesaveadminfilter) {
                    $expensefor = $request->expensefor ? explode(",",$expensesaveadminfilter->expensefor) :'';
                    $expensetype = $expensesaveadminfilter->expensetype ? explode(",",$expensesaveadminfilter->expensetype) :'';
                    $payment_mode = $expensesaveadminfilter->payment_mode ? explode(",",$expensesaveadminfilter->payment_mode) :'';
                    $createby = $expensesaveadminfilter->createby ? explode(",",$expensesaveadminfilter->createby) :'';
    
                    $post->FilterExpenseFor($expensefor)
                    ->FilterExpenseCat($expensetype)
                    ->FilterPaymentFor($payment_mode)
                    ->FilterCreateBy($createby)
                    ->FilterDateRange('expense_date',$expensesaveadminfilter->payment_date);
                }
             
                $canViewAll = $adminpermission->view_expense == 1;

                if ($canViewAll) { // view All
                    $posts = $post->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();
                }else{
                    $posts = $post->where('admin_id',$user_id)->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();
                }

                $summaryCounts = $this->buildSummaryCounts($canViewAll, $user_id);

                return view('admin.expense.index',compact('categories','paymentmodeFields','expenseforFields','createbyFields','expensetypeFields','posts','expensefors','adminpermission','expensesaveadminfilter','summaryCounts'));
        }

    }

    /**
     * Global expense summary counts (Total / This Month / Recently Added / per
     * Payment Mode) - scoped only by visibility permission, never by the active
     * table filters, so the status cards always show fixed/global totals.
     */
    private function buildSummaryCounts(bool $canViewAll, int $userId): array
    {
        $query = Expense::query();

        if (!$canViewAll) {
            $query->where('admin_id', $userId);
        }

        // "Recently Added" is scoped to expense_date (not created_at) so its count
        // always matches what the Payment Date range filter returns when the card
        // sets that same range - the only date filter this list actually has.
        $row = (clone $query)
            ->selectRaw(
                "COUNT(*) as total,
                SUM(CASE WHEN MONTH(expense_date) = ? AND YEAR(expense_date) = ? THEN 1 ELSE 0 END) as this_month,
                SUM(CASE WHEN expense_date >= ? THEN 1 ELSE 0 END) as recently_added",
                [now()->month, now()->year, now()->subDays(6)->startOfDay()]
            )
            ->first();

        $paymentModeCounts = (clone $query)
            ->whereNotNull('payment_mode')
            ->where('payment_mode', '!=', '')
            ->selectRaw('payment_mode, COUNT(*) as total')
            ->groupBy('payment_mode')
            ->pluck('total', 'payment_mode');

        return [
            'total' => (int) $row->total,
            'this_month' => (int) $row->this_month,
            'recently_added' => (int) $row->recently_added,
            'payment_mode' => $paymentModeCounts,
        ];
    }

    // public function store(Request $request){

    //     $basepathstatus = Basepathstatus::first();

    //     if ($request->hasFile('receipt_file')) {
    //         $file = $request->file('receipt_file');
    //         $name = $file->getClientOriginalName();
    //         // remove space from image
    //         $filename_ren1 = pathinfo($name,PATHINFO_FILENAME);
    //         $fileext_ren1 = pathinfo($name,PATHINFO_EXTENSION);
    //         $repspfilename = str_replace(" ","_",$filename_ren1);
    //         $new_file1 = $repspfilename.'.'.$fileext_ren1;
    //         if($basepathstatus->base_path_status == 0){
	//             $file->move(base_path().'/public/admin/assets/images/payment/expense',$new_file1);
    //         }else{
    //             $file->move(base_path().'/public_html/admin/assets/images/payment/expense',$new_file1);
    //         }

    //         $payment_slip = $new_file1;
    //     } else {
    //         $payment_slip = '';
    //     }

    //     $post = new Expense();
    //     $post->name = $request->name;
    //     $post->expensecat_id = $request->expensecat_id;
    //     $post->paid_to = $request->paid_to;
    //     $post->amount = $request->amount;
    //     $post->expense_date = $request->expense_date;
    //     $post->payment_mode = $request->payment_mode;
    //     $post->payment_from = $request->payment_from;
    //     $post->admin_id = Auth::guard('admin')->user()->id;
    //     $post->tax = $request->tax;
    //     $post->reference_no = $request->reference_no;
    //     // $post->expense_for = $request->expense_for;
    //     $post->expensefor_id = $request->expensefor_id;
    //     if ($request->subtract_tax == 1) {
    //         $post->subtract_tax = true;
    //     } else {
    //         $post->subtract_tax = false;
    //     }


    //     $post->notes = $request->notes;
    //     $post->receipt_file = $payment_slip;
    //     $post->save();

    //     return redirect()->back()->with('success','Expense added successfully!');
    // }

    public function store(Request $request)
    {
        $basepathstatus = Basepathstatus::first();
        $uploadedFiles = [];

        if ($request->hasFile('receipt_file')) {
            foreach ($request->file('receipt_file') as $file) {

                $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $ext  = $file->getClientOriginalExtension();

                $safeName = str_replace(' ', '_', $name)
                            . '_' . time() . '_' . rand(100,999)
                            . '.' . $ext;

                $path = $basepathstatus->base_path_status == 1
                    ? base_path('public/admin/assets/images/payment/expense')
                    : base_path('public_html/admin/assets/images/payment/expense');

                $file->move($path, $safeName);

                $uploadedFiles[] = $safeName;
            }
        }

        $post = new Expense();
        $post->name = $request->name;
        $post->expensecat_id = $request->expensecat_id;
        $post->paid_to = $request->paid_to;
        $post->amount = $request->amount;
        $post->expense_date = $request->expense_date;
        $post->payment_mode = $request->payment_mode;
        $post->payment_from = $request->payment_from;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->tax = $request->tax;
        $post->reference_no = $request->reference_no;
        $post->expensefor_id = $request->expensefor_id;
        $post->subtract_tax = $request->subtract_tax == 1;
        $post->notes = $request->notes;

        // ✅ store as JSON string in VARCHAR
        $post->receipt_file = json_encode($uploadedFiles);

        $post->save();

        return redirect()->back()->with('success','Expense added successfully!');
    }


    public function edit(Request $request)
    {
        $post = Expense::findOrFail($request->id);
    
        $files = [];
        if (!empty($post->receipt_file)) {
            $files = json_decode($post->receipt_file, true) ?? [];
        }
    
        return response()->json([
            'expense' => $post,
            'files'   => $files
        ]);
    }
    

    public function show($id)
    {
        $expense = Expense::with(['expensecat', 'expensefor'])->findOrFail($id);

        $basepathstatus = Basepathstatus::first();

        $imgPath = $basepathstatus->base_path_status == 0
            ? asset('admin/assets/images/payment/expense/'.$expense->receipt_file)
            : asset('admin/assets/images/payment/expense/'.$expense->receipt_file);


        return view('admin.expense.view', compact('expense', 'imgPath'));
    }

    public function update(Request $request)
    {
        $post = Expense::findOrFail($request->editID);
        $basepathstatus = Basepathstatus::first();

        // existing files
        $existingFiles = [];
        if (!empty($post->receipt_file)) {
            $existingFiles = json_decode($post->receipt_file, true) ?? [];
        }

        $newFiles = [];

        if ($request->hasFile('receipt_file')) {
            foreach ($request->file('receipt_file') as $file) {

                $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $ext  = $file->getClientOriginalExtension();

                $safeName = str_replace(' ', '_', $name)
                            . '_' . time() . '_' . rand(100,999)
                            . '.' . $ext;

                $path = $basepathstatus->base_path_status == 1
                    ? base_path('public/admin/assets/images/payment/expense')
                    : base_path('public_html/admin/assets/images/payment/expense');

                $file->move($path, $safeName);

                $newFiles[] = $safeName;
            }
        }

        $post->name = $request->name;
        $post->expensecat_id = $request->expensecat_id;
        $post->paid_to = $request->paid_to;
        $post->amount = $request->amount;
        $post->expense_date = $request->expense_date;
        $post->payment_mode = $request->payment_mode;
        $post->payment_from = $request->payment_from;
        $post->tax = $request->tax;
        $post->reference_no = $request->reference_no;
        $post->expensefor_id = $request->expensefor_id;
        $post->subtract_tax = $request->subtract_tax == 1;
        $post->notes = $request->notes;

        // ✅ merge old + new
        $post->receipt_file = json_encode(array_merge($existingFiles, $newFiles));
        $post->save();

        return redirect()->back()->with('success','Expense updated successfully!');
    }

    public function removeReceipt(Request $request)
    {
        $expense = Expense::findOrFail($request->expense_id);

        $files = json_decode($expense->receipt_file, true) ?? [];

        // remove filename
        $files = array_values(array_filter($files, function ($f) use ($request) {
            return $f !== $request->file;
        }));

        // delete physical file
        $basepathstatus = Basepathstatus::first();
        $filePath = $basepathstatus->base_path_status == 1
            ? base_path('public/admin/assets/images/payment/expense/'.$request->file)
            : base_path('public_html/admin/assets/images/payment/expense/'.$request->file);

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // update DB
        $expense->receipt_file = json_encode($files);
        $expense->save();

        return response()->json(['status' => 1]);
    }



    // public function update(Request $request){
    //     $post = Expense::find($request->editID);

    //     $basepathstatus = Basepathstatus::first();

    //     if ($request->hasFile('receipt_file')) {
    //         $file = $request->file('receipt_file');
    //         $name = $file->getClientOriginalName();
    //         // remove space from image
    //         $filename_ren1 = pathinfo($name,PATHINFO_FILENAME);
    //         $fileext_ren1 = pathinfo($name,PATHINFO_EXTENSION);
    //         $repspfilename = str_replace(" ","_",$filename_ren1);
    //         $new_file1 = $repspfilename.'.'.$fileext_ren1;
    //         if($basepathstatus->base_path_status == 0){
	//             $file->move(base_path().'/public/admin/assets/images/payment/expense',$new_file1);
    //         }else{
    //             $file->move(base_path().'/public_html/admin/assets/images/payment/expense',$new_file1);
    //         }

    //         $payment_slip = $new_file1;
    //     } else {
    //         $payment_slip = $post->receipt_file;
    //     }

    //     $post->name = $request->name;
    //     $post->expensecat_id = $request->expensecat_id;
    //     $post->paid_to = $request->paid_to;
    //     $post->amount = $request->amount;
    //     $post->expense_date = $request->expense_date;
    //     $post->payment_mode = $request->payment_mode;
    //     $post->payment_from = $request->payment_from;
    //     $post->tax = $request->tax;
    //     $post->reference_no = $request->reference_no;
    //     // $post->expense_for = $request->expense_for;
    //     $post->expensefor_id = $request->expensefor_id;
    //     if ($request->subtract_tax == 1) {
    //         $post->subtract_tax = true;
    //     } else {
    //         $post->subtract_tax = false;
    //     }

    //     $post->notes = $request->notes;
    //     $post->receipt_file = $payment_slip;
    //     $post->save();

    //     return redirect()->back()->with('success','Expense updated successfully!');

    // }

    public function delete(Request $request){
        $post = Expense::find($request->delete_id);

        $post->delete();

        return redirect()->back()->with('success','Expense deleted!');
    }

    public function checkcategoryedit(Request $request){
        $post = Expensecategory::find($request->id);

        return response()->json($post);
    }

    public function checkcategoryupdt(Request $request){
        $post = Expensecategory::find($request->edit_id);
        $post->name = $request->name;
        $post->save();

        return redirect()->back()->with('success','Category updated!');
    }

    // Expemse Category Section
    public function expensecatlist(Request $request){
        $posts = Expensecategory::orderBy('name')->get();

        return view('admin.expense.category.index',compact('posts'));
    }

    public function expensecatstore(Request $request) {
        $post = new Expensecategory();
        $post->name = $request->name;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Expense category added!');
    }

    public function getexpensedata(Request $request){
        $post = Expense::where('expensecat_id','=',$request->id)->count();

        if ($post > 0) {
            $data = [
                'status' => 1,
                'message' => $post.' data are found, if yes all data associate with this will also be delete!'
            ];
        }else{
            $data = [
                'status' => 0,
                'message' => 'Avaialble For Delete!'
            ];
        }

        return response()->json($data);
    }

    public function getexpensefordata(Request $request){
        $post = Expense::where('expensefor_id','=',$request->id)->count();

        if ($post > 0) {
            $data = [
                'status' => 1,
                'message' => $post.' data are found, if yes all data associate with this will also be delete!'
            ];
        }else{
            $data = [
                'status' => 0,
                'message' => 'Avaialble For Delete!'
            ];
        }

        return response()->json($data);
    }

    public function expensecatdelete(Request $request) {
        $post = Expensecategory::find($request->cat_id);
        $expPosts = Expense::where('expensecat_id','=',$request->cat_id)->get();

        if ($expPosts->count() > 0) {
            foreach ($expPosts as $expPost) {
                $expPost->delete();
            }
        }

        $post->delete();

        return redirect()->back()->with('success',$post->name.' deleted!');

    }

    public function  checkcategoryname(Request $request) {
        $categoryname = $request->name;

        if (isset($request->id) && $request->id != '') {
            $post = Expensecategory::where('name','=',$categoryname)->where('id','!=',$request->id)->count();
            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        } else {
            $post = Expensecategory::where('name','=', $categoryname)->count();

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

    public function expenseforlist(){
        $posts = Expensefor::orderBy('name')->get();

        return view('admin.expense.expensefor.index',compact('posts'));
    }

    public function expenseforstore(Request $request){
        $post = new Expensefor();
        $post->name = $request->name;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Expensefor added!');
    }

    public function checkexpenseforname(Request $request){
        $expenseforname = $request->name;
        if (isset($request->id) && $request->id != '') {
            $post = Expensefor::where('name','=',$expenseforname)->where('id','!=',$request->id)->count();
            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        } else {
            $post = Expensefor::where('name','=', $expenseforname)->count();

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

    public function expenseforedit(Request $request){
        $post = Expensefor::find($request->id);
        return response()->json($post);
    }

    public function expenseforupdate(Request $request){
        $post = Expensefor::find($request->edit_id);
        $post->name = $request->name;
        $post->save();

        return redirect()->back()->with('success','Expensefor updated!');
    }

    public function expensefordelete(Request $request){
        $post = Expensefor::find($request->cat_id);
        $exppost = Expense::where('expensefor_id','=',$request->cat_id)->get();

        if ($exppost->count() > 0) {
            foreach ($exppost as $expPost) {
                $expPost->delete();
            }
        }

        $post->delete();
        return redirect()->back()->with('success',$post->name.' deleted!');

    }

    public function saveadminfilter(Request $request) {
        $checkFilter = Expensesaveadminfilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();

        if (isset($checkFilter)) {
            if ($request->expense_for != '') {
                $checkFilter->expensefor = implode(",",$request->expense_for);
            } else {
                $checkFilter->expensefor = "";
            }

            if ($request->expense_type != '') {
                $checkFilter->expensetype = implode(",",$request->expense_type);
            }else {
                $checkFilter->expensetype = "";
            }

            if ($request->payment_mode != '') {
                $checkFilter->payment_mode = implode(",",$request->payment_mode);
            } else {
                $checkFilter->payment_mode = "";
            }

            if ($request->createby_id != '') {
                $checkFilter->createby = implode(",",$request->createby_id);
            }else {
                $checkFilter->createby = "";
            }

            if ($request->payment_date != '') {
                $checkFilter->payment_date = $request->payment_date;
            } else {
                $checkFilter->payment_date = "";
            }


            $checkFilter->save();
            $data = [
                'res' => 'Filter update successfully!'
            ];




        } else {
            $saveFilter = new Expensesaveadminfilter();
            $saveFilter->admin_id = Auth::guard('admin')->user()->id;

            if ($request->expense_for != '') {
                $saveFilter->expensefor = implode(",",$request->expense_for);
            } else {
                $saveFilter->expensefor = "";
            }

            if ($request->expense_type != '') {
                $saveFilter->expensetype = implode(",",$request->expense_type);
            }else {
                $saveFilter->expensetype = "";
            }

            if ($request->payment_mode != '') {
                $saveFilter->payment_mode = implode(",",$request->payment_mode);
            } else {
                $saveFilter->payment_mode = "";
            }

            if ($request->createby_id != '') {
                $saveFilter->createby = implode(",",$request->createby_id);
            }else {
                $saveFilter->createby = "";
            }

            if ($request->payment_date != '') {
                $saveFilter->payment_date = $request->payment_date;
            } else {
                $saveFilter->payment_date = "";
            }

            $saveFilter->save();
            $data = [
                'res' => 'Filter save successfully!'
            ];






        }

        return response()->json($data);
    }

    public function resetadminfilter(Request $request) {
        $checkFilter = Expensesaveadminfilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();
        if (isset($checkFilter)) {
            $checkFilter->expensefor = "";
            $checkFilter->expensetype = "";
            $checkFilter->payment_mode = "";
            $checkFilter->createby = "";
            $checkFilter->payment_date = "";

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
}
