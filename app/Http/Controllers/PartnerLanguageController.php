<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class PartnerLanguageController extends Controller
{
    public function SwitchLang($locale){
        if(array_key_exists($locale,config('app.locales'))){
            Session::put('locale',$locale);
            
            App::setLocale($locale);
        }

        return redirect()->back();
    }
}
