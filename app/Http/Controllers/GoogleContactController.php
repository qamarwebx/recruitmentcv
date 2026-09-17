<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Google\Client;
use Google\Service\PeopleService;
use Laravel\Socialite\Two\GoogleProvider;
use App\Models\Allcontact;
use App\Models\GoogleAccount;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Admin;
use Session;
use Illuminate\Support\Facades\Auth;

class GoogleContactController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $accounts = GoogleAccount::with('admin')->orderBy('last_synced_at','DESC');

            if (auth()->guard('admin')->user()->user_type != 1) {
                $accounts->where('admin_id', auth()->guard('admin')->id());
            }

            $accounts = $accounts->get();

            return DataTables::of($accounts)

                ->addIndexColumn()

                 ->addColumn('careoff', function ($row) {
                    return $row->admin->name ?? '-';
                })

                ->editColumn('last_synced_at', function ($row) {
                    return $row->last_synced_at
                        ? $row->last_synced_at->format('d M Y h:i A')
                        : '-';
                })
               ->addColumn('action', function ($row) {

                    $buttons = '';

                    // Sync
                  $buttons .= '<a href="javascript:;"
                            class="text-body syncGoogleContact"
                            data-email="'.$row->email.'"
                            title="Sync Contacts">
                            <i class="ti ti-refresh ti-sm mx-2"></i>
                        </a>';

                    // Delete
                   $buttons .= '<a href="javascript:;"
                class="text-body deleteGoogleAccount"
                data-bs-toggle="modal"
                data-bs-target="#deleteGoogleAccount"
                data-id="'.$row->id.'"
                title="Delete">
                <i class="ti ti-trash ti-sm mx-2"></i>
            </a>';

                    return $buttons;
                })
                ->rawColumns(['action'])

                ->make(true);
        }

        return view('admin.allcontact.sync-with-google');
    }

    public function redirect(Request $request)
    {

        if(isset($request->sync_contact_by_careof_id)){
            
           $user =  Admin::find($request->sync_contact_by_careof_id);
           
           $email = $user->email;

           Session::put('careoff_user', $user->id);

           return Socialite::buildProvider(
            GoogleProvider::class,
            config('services.google_contact')
            )
            ->scopes([
                'https://www.googleapis.com/auth/contacts.readonly'
            ])
            ->with([
                'login_hint'   => $email,
                'prompt'       => 'select_account',
                'access_type'  => 'offline',
            ])
            ->redirect();

        }
        elseif(isset($request->sync_contact_by_gmail_id) && $request->sync_contact_by_gmail_id == true){

        $email = $request->email;

          return Socialite::buildProvider(
            GoogleProvider::class,
            config('services.google_contact')
            )
            ->scopes([
                'https://www.googleapis.com/auth/contacts.readonly'
            ])
            ->with([
                'login_hint'   => $email,
                'prompt'       => 'select_account',
                'access_type'  => 'offline',
            ])
            ->redirect();

        }
        else{

            Session::put('careoff_user', auth()->guard('admin')->id());
            return Socialite::buildProvider(
                GoogleProvider::class,
                config('services.google_contact')
            )
            ->scopes([
                'https://www.googleapis.com/auth/contacts.readonly'
            ])
            ->redirect();

        }

      
    }

    public function callback()
    {

        $googleUser = Socialite::buildProvider(
            GoogleProvider::class,
            config('services.google_contact')
        )->user();

        $googleAccount = GoogleAccount::updateOrCreate(
            [
                'google_id'        => $googleUser->getId()
            ],
            [
                'admin_id' => auth()->guard('admin')->id(),
                'name'             => $googleUser->getName(),
                'email'            => $googleUser->getEmail(),
                'access_token'     => $googleUser->token,
                'refresh_token'    => $googleUser->refreshToken,
                'token_expires_at' => now()->addSeconds(
                    $googleUser->expiresIn ?? 3600
                ),
                'is_active'        => 1,
            ]
        );

        $totalContacts = $this->syncContacts(
            $googleUser->token
        );

        $googleAccount->update([
            'total_contacts' => $totalContacts,
            'last_synced_at' => now(),
        ]);

        return redirect('admin/allcontact-list')->with(
            'success',
            "{$totalContacts} contacts synced successfully."
        );
    }

    public function syncContacts($accessToken)
    {
        set_time_limit(0);

        $client = new Client();
        $client->setAccessToken($accessToken);

        $service = new PeopleService($client);

        $pageToken = null;
        $totalInserted = 0;

        $careoffUser = Session::get('careoff_user');

        do {

            $response = $service->people_connections->listPeopleConnections(
                'people/me',
                [
                    'personFields' => 'names,emailAddresses,phoneNumbers',
                    'pageSize'     => 1000,
                    'pageToken'    => $pageToken,
                ]
            );

            foreach ($response->getConnections() ?? [] as $person) {

                $name = optional($person->getNames()[0] ?? null)->getDisplayName();

                $email = optional($person->getEmailAddresses()[0] ?? null)->getValue();

                $phone = optional($person->getPhoneNumbers()[0] ?? null)->getCanonicalForm();

                if (empty($phone)) {
                    continue;
                }

                // Keep only digits
                $phone = preg_replace('/\D+/', '', $phone);

                if (empty($phone)) {
                    continue;
                }

                $contact = Allcontact::updateOrCreate(
                    [
                        'primary_no_wsp' => $phone,
                    ],
                    [
                        'source' => 'google',
                        'user_id'  => Auth::guard('admin')->user()->id ?? null,
                        'full_name'  => $name,
                        'email'      => $email,
                        'careoff_id' => $careoffUser,
                    ]
                );

                if ($contact->wasRecentlyCreated) {
                    $totalInserted++;
                }
            }

            $pageToken = $response->getNextPageToken();

        } while ($pageToken);

        Session::forget('careoff_user');

        return $totalInserted;
    }

    public function delete(Request $request)
    {
        GoogleAccount::findOrFail($request->google_account_id)->delete();

        return redirect()
            ->back()
            ->with('success', 'Google account deleted successfully.');
    }


    // public function syncContacts($accessToken)
    // {
    //     set_time_limit(0);

    //     $client = new Client();
    //     $client->setAccessToken($accessToken);

    //     $service = new PeopleService($client);

    //     $pageToken = null;

    //     $totalInserted = 0;

    //     $careoffUser = Session::get('careoff_user');

    //     do {

    //         $response = $service->people_connections
    //             ->listPeopleConnections(
    //                 'people/me',
    //                 [
    //                     'personFields' => 'names,emailAddresses,phoneNumbers',
    //                     'pageSize'     => 1000,
    //                     'pageToken'    => $pageToken,
    //                 ]
    //             );

    //         $insertData = [];

    //         foreach ($response->getConnections() ?? [] as $person) {

    //             $name = optional(
    //                 $person->getNames()[0] ?? null
    //             )->getDisplayName();

    //             $email = optional(
    //                 $person->getEmailAddresses()[0] ?? null
    //             )->getValue();

    //             $phone = optional(
    //                 $person->getPhoneNumbers()[0] ?? null
    //             )->getCanonicalForm();

    //             if (empty($phone)) {
    //                 continue;
    //             }

    //             $phone = preg_replace('/\D+/', '', $phone);

    //             if (empty($phone)) {
    //                 continue;
    //             }

    //             $insertData[] = [
    //                 'primary_no_wsp' => $phone,
    //                 'full_name'      => $name,
    //                 'email'          => $email,
    //                 'careoff_id'     => $careoffUser ?? null,
    //                 'created_at'     => now(),
    //                 'updated_at'     => now(),
    //             ];
    //         }

    //         if (!empty($insertData)) {

    //             $before = Allcontact::count();

    //             Allcontact::insertOrIgnore($insertData);

    //             $after = Allcontact::count();

    //             $totalInserted += ($after - $before);
    //         }

    //         $pageToken = $response->getNextPageToken();

    //     } while ($pageToken);

    //     Session::forget('careoff_user');

    //     return $totalInserted;
    // }

    // public function syncContacts($accessToken)
    // {
    //     set_time_limit(0);

    //     $client = new Client();

    //     $client->setAccessToken($accessToken);

    //     $service = new PeopleService($client);

    //     $pageToken = null;

    //     $insertData = [];

    //     $totalGoogleContacts = 0;

    //     $careoffUser = Session::get('careoff_user');

    //     do {

    //         $response = $service->people_connections
    //             ->listPeopleConnections(
    //                 'people/me',
    //                 [
    //                     'personFields' => 'names,emailAddresses,phoneNumbers',
    //                     'pageSize'     => 1000,
    //                     'pageToken'    => $pageToken,
    //                 ]
    //             );

    //         foreach ($response->getConnections() as $person) {

    //             $totalGoogleContacts++;

    //             $name = optional(
    //                 $person->getNames()[0] ?? null
    //             )->getDisplayName();

    //             $email = optional(
    //                 $person->getEmailAddresses()[0] ?? null
    //             )->getValue();

    //             $phone = optional(
    //                 $person->getPhoneNumbers()[0] ?? null
    //             )->getCanonicalForm();

    //             if (empty($phone)) {
    //                 continue;
    //             }

    //             $phone = preg_replace('/\D+/', '', $phone);

    //             if (empty($phone)) {
    //                 continue;
    //             }

    //             $insertData[] = [
    //                 'primary_no_wsp' => $phone,
    //                 'full_name'      => $name,
    //                 'email'          => $email,
    //                 'careoff_id'     => $careoffUser ?? null,
    //                 'created_at'     => now(),
    //                 'updated_at'     => now(),
    //             ];
    //         }

    //         $pageToken = $response->getNextPageToken();

    //     } while ($pageToken);

    //     if (!empty($insertData)) {

    //         $existingPhones = Allcontact::whereIn(
    //             'primary_no_wsp',
    //             collect($insertData)
    //                 ->pluck('primary_no_wsp')
    //                 ->unique()
    //                 ->toArray()
    //         )
    //         ->pluck('primary_no_wsp')
    //         ->toArray();

    //         $insertData = array_filter(
    //             $insertData,
    //             fn ($contact) =>
    //                 !in_array(
    //                     $contact['primary_no_wsp'],
    //                     $existingPhones
    //                 )
    //         );

    //         if (!empty($insertData)) {

    //             Allcontact::insert(
    //                 array_values($insertData)
    //             );
    //         }
    //     }

    //     Session::forget('careoff_user');
        
    //     return count($insertData);
    // }

    // public function callback()
    // {
    //     try {

    //         $googleUser = Socialite::buildProvider(
    //             GoogleProvider::class,
    //             config('services.google_contact')
    //         )->user();

    //         $this->syncContacts($googleUser->token);

    //     } catch (\Exception $e) {

    //         dd([
    //             'message' => $e->getMessage(),
    //             'line'    => $e->getLine(),
    //             'file'    => $e->getFile(),
    //         ]);
    //     }
    // }
    

    // public function syncContacts($accessToken)
    // {
    //     $client = new Client();
    
    //     $client->setAccessToken($accessToken);
    
    //     $service = new PeopleService($client);
    
    //     $pageToken = null;
    
    //     do {
    
    //         $response = $service->people_connections
    //             ->listPeopleConnections(
    //                 'people/me',
    //                 [
    //                     'personFields' => 'names,emailAddresses,phoneNumbers',
    //                     'pageSize'     => 1000,
    //                     'pageToken'    => $pageToken,
    //                 ]
    //             );
    
    //         foreach ($response->getConnections() as $person) {
    
    //             $name = optional(
    //                 $person->getNames()[0] ?? null
    //             )->getDisplayName();
    
    //             $email = optional(
    //                 $person->getEmailAddresses()[0] ?? null
    //             )->getValue();
    
    //             $phone = optional(
    //                 $person->getPhoneNumbers()[0] ?? null
    //             )->getcanonicalForm();
                
                
    //             if (empty($phone)) {
    //                 continue;
    //             }

    //             $phone = preg_replace('/\D+/', '', $phone);

    //             Allcontact::updateOrCreate(
    //                 [
    //                     'primary_no_wsp' => $phone
    //                 ],
    //                 [
    //                     'full_name' => $name,
    //                     'email'     => $email,
    //                 ]
    //             );
    //         }
    
    //         $pageToken = $response->getNextPageToken();
    
    //     } while ($pageToken);
    // }   

}
