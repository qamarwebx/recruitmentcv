<?php

namespace App\Http\Controllers;

use App\Models\FacebookAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookAccountController extends Controller
{
    public function index()
    {
        return view('admin.facebook_accounts.index');
    }

    public function indexjson(Request $request)
    {
        $posts = FacebookAccount::orderBy('id','DESC')->get();

        $data['data'] = $posts->map(function ($row) {
            return [
                'id' => $row->id,
                'account_name' => $row->account_name,
                'pixel_id' => $row->pixel_id,
                'business_manager_id' => $row->business_manager_id,
                'status' => $row->is_active,
                'action' => '',
            ];
        });

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_name' => 'required',
            'pixel_id' => 'required|unique:facebook_accounts',
            'capi_access_token' => 'required',
            'test_event_code' => 'required',
            'meta_pixel_base_code' => 'required'
        ]);

        FacebookAccount::create([
            'account_name' => $request->account_name,
            'business_manager_id' => $request->business_manager_id,
            'pixel_id' => $request->pixel_id,
            'capi_access_token' => $request->capi_access_token,
            'test_event_code' => $request->test_event_code,
            'meta_pixel_base_code' => $request->meta_pixel_base_code,
        ]);

        return redirect()->back()->with('success','Facebook account added!');
    }

    public function editlist(Request $request)
    {
        return response()->json(
            FacebookAccount::findOrFail($request->id)
        );
    }

    public function update(Request $request)
    {
        $post = FacebookAccount::findOrFail($request->edit_id);

        $post->update([
            'account_name' => $request->account_name,
            'business_manager_id' => $request->business_manager_id,
            'pixel_id' => $request->pixel_id,
            'capi_access_token' => $request->capi_access_token,
            'test_event_code' => $request->test_event_code,
            'meta_pixel_base_code' => $request->meta_pixel_base_code,
        ]);

        return redirect()->back()->with('success','Facebook account updated!');
    }

    public function show($id)
    {
        $post = FacebookAccount::findOrFail($id);
        return view('admin.facebook_accounts.show', compact('post'));
    }

    public function delete(Request $request)
    {
        FacebookAccount::findOrFail($request->id)->delete();
        return redirect()->back()->with('success','Facebook account deleted!');
    }    

    public function activate(Request $request)
    {
        FacebookAccount::findOrFail($request->id)->update(['is_active' => true]);
        return redirect()->back()->with('success','Account activated!');
    }

    public function deactivate(Request $request)
    {
        FacebookAccount::findOrFail($request->id)->update(['is_active' => false]);
        return redirect()->back()->with('success','Account deactivated!');
    }

    public function storePageView(Request $request)
    {
        $request->validate([
            'event_id'     => 'required|string',
            'current_url'  => 'required|url',
            'event_name'   => 'nullable|string'
        ]);
    
        $eventId    = $request->event_id;
        $currentUrl = $request->current_url;
        $eventName  = $request->event_name ?? 'PageView';
    
        $pixelId     = config('services.facebook_capi.pixel_id');
        $accessToken = config('services.facebook_capi.access_token');
    
        $payload = [
            'data' => [
                [
                    'event_name'        => $eventName,
                    'event_time'        => time(),
                    'event_id'          => $eventId,
                    'action_source'     => 'website',
                    'event_source_url'  => $currentUrl,
                    'user_data' => [
                        'client_ip_address' => $request->ip(),
                        'client_user_agent' => $request->userAgent(),
                    ],
                ]
            ]
        ];
    
        $response = Http::post(
            "https://graph.facebook.com/v18.0/{$pixelId}/events",
            $payload + ['access_token' => $accessToken]
        );
    
        Log::channel('facebook_capi')->info('Meta CAPI Event Sent', [
            'event_name' => $eventName,
            'event_id'   => $eventId,
            'url'        => $currentUrl,
            'status'     => $response->status(),
            'response'   => $response->json(),
        ]);
    
        return response()->json(['status' => 'success']);
    }
    


}
