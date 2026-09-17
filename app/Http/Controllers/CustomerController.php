<?php

namespace App\Http\Controllers;

use App\Models\Adminpermission;
use App\Models\Booking;
use App\Models\Clientfilterlist;
use App\Models\ClientAdminSaveFilter;
use App\Models\User;
use App\Models\Visadetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Country;
use App\Models\City;

class CustomerController extends Controller
{
    public function index()
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        $saveadminfilter = ClientAdminSaveFilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();

        $countryLists = Country::orderBy('name')->get();
        $cityLists = City::orderBy('name')->get();

        $clientStatusSummary = $this->buildClientStatusSummary($permission);

        return view('admin.client.index',['perm' => $permission,'countries' => $countryLists,'cities' => $cityLists,'saveadminfilter' => $saveadminfilter,'clientStatusSummary' => $clientStatusSummary]);
    }

    /**
     * Global (permission-scoped, not filter-scoped) Client Status /
     * Verification counts for the status bar — counts stay fixed while
     * the DataTable filters client-side. Mirrors indexjson()'s own
     * 2-branch permission scoping exactly (full_access/admin sees all,
     * view_client sees all, everyone else sees nothing — there is no
     * "own records" branch for this module).
     */
    private function buildClientStatusSummary($permission): array
    {
        $user = Auth::guard('admin')->user();
        $isAdmin = $user->user_type == 1;
        $canView = $isAdmin || (isset($permission) && $permission->full_access == 1) || (isset($permission) && $permission->view_client == 1);

        if (!$canView) {
            return [
                'total' => 0,
                'statuses' => [0 => 0, 1 => 0],
                'verification' => [0 => 0, 1 => 0],
            ];
        }

        $statusCounts = User::select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status');
        $verificationCounts = User::select('verification_status', DB::raw('COUNT(*) as total'))->groupBy('verification_status')->pluck('total', 'verification_status');

        return [
            'total' => (int) $statusCounts->sum(),
            'statuses' => [
                0 => (int) ($statusCounts[0] ?? 0),
                1 => (int) ($statusCounts[1] ?? 0),
            ],
            'verification' => [
                0 => (int) ($verificationCounts[0] ?? 0),
                1 => (int) ($verificationCounts[1] ?? 0),
            ],
        ];
    }

    public function saveFilter(Request $request)
    {
        $adminId = Auth::guard('admin')->user()->id;

        $filter = ClientAdminSaveFilter::firstOrNew(['admin_id' => $adminId]);
        $filter->by_country = $request->by_country ?: '';
        $filter->by_city = $request->by_city ?: '';
        $filter->by_created_date = $request->by_created_date ?: '';
        $filter->save();

        return response()->json([
            'message' => $filter->wasRecentlyCreated ? 'Filter saved successfully!' : 'Filter updated successfully!'
        ]);
    }

    public function indexjson(Request $request)
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $posts = DB::table('users as user')
            // ->leftJoin('userprofiles as userprofile','user.id','=','userprofile.user_id')
            ->leftJoin('countries as country','user.country_id','=','country.id')
            ->leftJoin('cities as city','user.city_id','=','city.id')
            ->select('user.*','country.name as country','country.country_code as country_code','city.name as city')
            ->orderBy('created_at','DESC')
            ->get();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_client == 1){
                $posts = DB::table('users as user')
                // ->leftJoin('userprofiles as userprofile','user.id','=','userprofile.user_id')
                ->leftJoin('countries as country','user.country_id','=','country.id')
                ->leftJoin('cities as city','user.city_id','=','city.id')
                ->select('user.*','country.name as country','country.country_code as country_code','city.name as city')
                ->orderBy('created_at','DESC')
                ->get();
            }else{
                $posts = '';
            }
        }

        $data['data'] = $posts;
        return response()->json($data);
    }

    public function view($id)
    {
        $customer = User::findOrFail($id);
    
        return response()->json([
            ...$customer->toArray(),
    
            'created_at' => $customer->created_at
                ? $customer->created_at->format('d M Y, h:i A')
                : null,
    
            'updated_at' => $customer->updated_at
                ? $customer->updated_at->format('d M Y, h:i A')
                : null,
        ]);
    }
    
    public function edit(Request $request){
        $post = User::find($request->id);

        return response()->json($post);
    }

    public function update(Request $request){
        $post = User::find($request->edit_id);
        $post->name = $request->name;
        $post->email = $request->email;
        $post->mobile_no = $request->mobile_no;
        $post->country_id = $request->country_id;
        $post->city_id = $request->city_id;
        $post->address = $request->address;
        $post->save();

        return redirect()->back()->with('success','Client Data updated!');
    }

    public function updateFilterList(Request $request){
        $post = Clientfilterlist::where('admin_id','=',Auth::guard('admin')->user()->id)->count();

        if ($post > 0) {
            $updateF = Clientfilterlist::where('admin_id','=',Auth::guard('admin')->user()->id)->first();

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

            if($request->statusf == 1){
				$updateF->status_filter = true;
			}else{
				$updateF->status_filter = false;
			}

            if($request->created_datef == 1){
				$updateF->created_date_filter = true;
			}else{
				$updateF->created_date_filter = false;
			}

            $updateF->save();
    		return response()->json('success');

        } else {
            $newFilter = new Clientfilterlist();

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

            if($request->statusf == 1){
				$newFilter->status_filter = true;
			}else{
				$newFilter->status_filter = false;
			}

            if($request->created_datef == 1){
				$newFilter->created_date_filter = true;
			}else{
				$newFilter->created_date_filter = false;
			}

            $newFilter->save();

            return response()->json('success');
        }
    }

    public function delete(Request $request){
        $user = User::find($request->client_id);
        $bookings = Booking::where('user_id','=',$request->client_id)->get();
        $visaDetail = Visadetails::where('user_id','=',$request->client_id)->get();

        $user->delete();
        if (isset($bookings)) {
            foreach ($bookings as $booking) {
                $booking->delete();
            }
        }

        if (isset($visaDetail)) {
            foreach ($visaDetail as $visaDetai) {
                $visaDetai->delete();
            }
        }

        return redirect()->back()->with('success','Customer and all related field delete!');
    }

    public function checkemailexist(Request $request){
        $post = User::where('email','=',$request->email)->where('id','!=',$request->id)->count();
        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function checkmobileexists(Request $request){
        $post = User::where('mobile_no','=',$request->mobile_no)->where('id','!=',$request->id)->count();
        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'status' => 'required|in:0,1'
        ]);

        User::where('id', $request->id)
            ->update(['status' => $request->status]);

            return response()->json([
                'success' => true
            ]);    
    }


}
