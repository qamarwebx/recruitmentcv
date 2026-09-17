<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Iptrackers;
use App\Models\Adminpermission;




use App\Models\Admin;
use App\Models\City;
use App\Models\Country;


use App\Models\Contactp;
use App\Models\Contactplus;
use App\Models\Leadstage;
use App\Models\Lifecyclestatus;
use App\Models\Whatsappapi;
use App\Models\Whatsappcamptemplate;

use App\Models\Businesstype;

use App\Models\Groupm;
use App\Models\Contactpnotes;
use App\Models\Contactreminder;
use App\Models\Metawhatsapptemplate;
use App\Models\Metawhatsappapi;
use App\Models\Contactsendwhatsapp;
use App\Models\Metawhatsappcampaignresponse;
use App\Models\Sendwhatsappresponse;
use App\Models\Contactpfilter;
use App\Models\Contactsendtag;


class IptrackerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   $countries = Country::orderBy('name')->get();
        $cities = City::orderBy('name')->get();
        $lcss = Lifecyclestatus::orderBy('id')->get();

        $adminusers = Admin::where('status', 1)->orderBy('name')->get();

        $businesstypes = Businesstype::orderBy('name')->get();
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        $contactpfilter = Contactpfilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();

        $metatemplates = Metawhatsapptemplate::where('status','=',1)->get();
        $normaltemplates = Whatsappcamptemplate::where('status','=',1)->get();
        $metawhatsappAPIs = Metawhatsappapi::where('status','=',1)->get();
        $wapis = Whatsappapi::where('api_for','=','campaign_not')->orderBy('id','DESC')->get();

        $groupms = Groupm::orderBy('name','ASC')->get();


        return view('admin.iptracker.index',compact('countries','cities','groupms','lcss','metatemplates','normaltemplates','wapis','metawhatsappAPIs','businesstypes','permission','adminusers','contactpfilter'));


         // $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
         // return view('admin.iptracker.index',['perm' => $permission]);
    }


    public function indexJson(Request $request){

        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        if(Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)){

            $post = Iptrackers::get();

        }elseif(Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_contactp == 1){


                $post = Iptrackers::get();

            }else{

                $post = Iptrackers::get();
            }
        }



        $data['data'] = $post;

        return response()->json($data);

    }



    public function getJson(Request $request)
    {
        $permission = Adminpermission::where('staff_id', '=', Auth::guard('admin')->user()->id)->first();
        $ipaddress = Iptrackers::get();



         $data['data'] = $ipaddress;
        return response()->json($data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
    public function show(c $c)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
    public function edit(c $c)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, c $c)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
public function destroy(Request $request)
{
    // Validate that 'id' is present in the request
    $request->validate([
        'contactID' => 'required|integer|exists:iptrackers,id', // Update to match the input name
    ]);

    // Retrieve the ID from the request
    $ipId = $request->input('contactID'); // Make sure to match the name of the input field

    try {
        // Find the IP record and delete it
        $ipAddress = Iptrackers::findOrFail($ipId); // Find the IP address by its ID
        $ipAddress->delete(); // Delete the IP address record

        // Redirect back with a success message
        return redirect()->back()->with('success', 'IP address deleted successfully.');
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // Redirect back with an error message if the IP address is not found
        return redirect()->back()->with('error', 'IP address not found.');
    }
}


}
