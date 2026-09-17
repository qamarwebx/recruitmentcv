<?php

namespace App\Http\Controllers;
use App\AdminModel\LeadProfession;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Session;
use Auth;
use Carbon\Carbon;
use File;
Use DB;
use Illuminate\Http\Request;

class LandingController extends Controller
{
  public function index()
  {
  	$prof = LeadProfession::all();
    return view('/landing-page/landing-page',['prof' => $prof]);
  }
}