<?php

namespace App\Http\Controllers;

use App\Models\Websiteconfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function updateLangpage(Request $request){
        $post = Websiteconfig::first();
        if (isset($post)) {
            $post->page_lang = $request->lang;
            $post->save();
        } else {
            $postNew = new Websiteconfig();
            $postNew->page_lang = $request->lang;
            $postNew->save();
        }
        
    }

    public function SwitchLang($locale){
        if(array_key_exists($locale,config('app.locales'))){
            Session::put('locale',$locale);
            App::setLocale($locale);
        }

        return redirect()->back();
    }
}
