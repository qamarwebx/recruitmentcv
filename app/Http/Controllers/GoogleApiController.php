<?php

namespace App\Http\Controllers;

use Google\Client;
use Illuminate\Http\Request;

class GoogleApiController extends Controller
{
    public function index(){
        $pageConfigs = ['pageHeader' => false];
        return view('content.googleapi.index',['pageConfigs' => $pageConfigs]);
    }

    public function getList(){

        $people_service = new Client();
        $people = $people_service->people_connections->listPeopleConnections(
            'people/me', array('personFields' => 'names,emailAddresses')
        );

        
        
    }

    public function storeC(Request $request){
        



        
    }
}
