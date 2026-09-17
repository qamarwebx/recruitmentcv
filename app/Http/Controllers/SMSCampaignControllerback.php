// public function store(Request $request){
    //     try {
    //         $smsAPI = SmsApi::find($request->sms_api_id);
    //         $smsTemplate = SmsTemplate::find($request->sms_template_id);

    //         $audience = $request->audience;
    //         $message = trim($request->sms_msg);

    //         // 🔹 Create campaign entry
    //         $campaign = new SmsCampaign();
    //         $campaign->campaign_name = $request->campaign_name;
    //         $campaign->audience = $audience;
    //         $campaign->msg_body = $message;
    //         $campaign->admin_id = Auth::guard('admin')->user()->id;
    //         $campaign->sms_temp_id = $request->sms_template_id;
    //         $campaign->sms_api_id = $request->sms_api_id;
    //         $campaign->sch_type = $request->sch_type;

    //         if ($request->sch_type == 'Scheduled') {
    //             $campaign->date_and_time = $request->date_and_time;
    //         }

    //         // Store filters for reference
    //         if ($audience == 'allcontact') {
    //             if ($request->careoff_id2) $campaign->careoff_id = implode(",", $request->careoff_id2);
    //             if ($request->country_id) $campaign->country_id = implode(",", $request->country_id);
    //             if ($request->country_code) $campaign->country_code = implode(",", $request->country_code);
    //             if ($request->groupmallc) $campaign->group_id = implode(",", $request->groupmallc);
    //             if ($request->allcontact_subscribe) $campaign->subscribe = implode(",", $request->allcontact_subscribe);
    //             if ($request->lead_type) $campaign->business_type_contact = implode(",", $request->lead_type);
    //             $campaign->contact_type = $request->allcontact_contact_type;
    //         }

    //         $campaign->save();

    //         // 🔹 Scheduled campaigns
    //         if ($request->sch_type == 'Scheduled') {
    //             $campaign->update([
    //                 'message_status' => 'Scheduled',
    //                 'message_text' => 'Message scheduled for later sending.'
    //             ]);
    //             return redirect()->back()->with('success', 'Campaign scheduled successfully.');
    //         }

    //         // 🔹 Send Now logic
    //         $mobileNumbers = [];
    //         if ($audience == 'Leads') {
    //         }

    //         elseif ($audience == 'allcontact') {
    //             $group = $request->groupmallc;
    //             $careoff = $request->careoff_id2;
    //             $country = $request->country_id;
    //             $leadtype = $request->lead_type;
    //             $optinout = $request->allcontact_subscribe;
    //             $country_code = $request->country_code;
    //             $contact_type = $request->allcontact_contact_type;

    //             $contacts = Allcontact::where(function ($query) use ($group, $careoff, $country, $leadtype, $optinout, $country_code) {
    //                 if ($group) $query->whereIn('group_id', $group);
    //                 if ($careoff) $query->whereIn('careoff_id', $careoff);
    //                 if ($country) $query->whereIn('country_id', $country);
    //                 if ($leadtype) $query->whereIn('lead_type', $leadtype);
    //                 if ($optinout) $query->whereIn('optinout', $optinout);
    //                 if ($country_code) {
    //                     $query->where(function ($q) use ($country_code) {
    //                         foreach ($country_code as $code) {
    //                             $q->orWhere('primary_no_wsp', 'like', $code.'%')
    //                             ->orWhere('secondary_no_wsp', 'like', $code.'%')
    //                             ->orWhere('mobile_no1_wsp', 'like', $code.'%');
    //                         }
    //                     });
    //                 }
    //             })->get();

    //             foreach ($contacts as $c) {
    //                 if ($contact_type == '4' && $c->mobile_no1_wsp) {
    //                     $mobileNumbers[] = $c->mobile_no1_wsp;
    //                 } elseif ($contact_type == '3' && $c->primary_no_wsp) {
    //                     $mobileNumbers[] = $c->primary_no_wsp;
    //                 } elseif ($contact_type == '2' && $c->secondary_no_wsp) {
    //                     $mobileNumbers[] = $c->secondary_no_wsp;
    //                 } else {
    //                     if ($c->mobile_no1_wsp) $mobileNumbers[] = $c->mobile_no1_wsp;
    //                     if ($c->primary_no_wsp) $mobileNumbers[] = $c->primary_no_wsp;
    //                     if ($c->secondary_no_wsp) $mobileNumbers[] = $c->secondary_no_wsp;
    //                 }
    //             }
    //         }

    //         elseif ($audience == 'contactp') {
    //             $group = $request->contact_group;
    //             $business = $request->business_type_contact;
    //             $careoff = $request->careoff_id;
    //             $subscribe = $request->contactp_subscribe;
    //             $type = $request->contactp_contact_type;

    //             $contacts = Contactplus::where(function ($q) use ($group, $business, $careoff, $subscribe) {
    //                 if ($group) $q->whereIn('group_id', $group);
    //                 if ($business) $q->whereIn('businesstype_id', $business);
    //                 if ($careoff) $q->whereIn('careoff_id', $careoff);
    //                 if ($subscribe) $q->whereIn('subscribe', $subscribe);
    //             })->get();

    //             foreach ($contacts as $c) {
    //                 if ($type == '4' && $c->sec_contact) $mobileNumbers[] = $c->sec_contact;
    //                 elseif ($type == '3' && $c->prim_contact) $mobileNumbers[] = $c->prim_contact;
    //                 elseif ($type == '2' && $c->owner_contact) $mobileNumbers[] = $c->owner_contact;
    //                 else {
    //                     if ($c->sec_contact) $mobileNumbers[] = $c->sec_contact;
    //                     if ($c->prim_contact) $mobileNumbers[] = $c->prim_contact;
    //                     if ($c->owner_contact) $mobileNumbers[] = $c->owner_contact;
    //                 }
    //             }
    //         }

    //         elseif ($audience == 'associate') {
    //             $contacts = Associates::get();
    //             foreach ($contacts as $c) {
    //                 if ($c->mobile_no) $mobileNumbers[] = $c->mobile_no;
    //             }
    //         }

    //         elseif ($audience == 'client') {
    //             $contacts = User::get();
    //             foreach ($contacts as $c) {
    //                 if ($c->mobile_no) $mobileNumbers[] = $c->mobile_no;
    //             }
    //         }

    //         elseif ($audience == 'partner') {
    //             $contacts = Partner::get();
    //             foreach ($contacts as $c) {
    //                 if ($c->mobile_no) $mobileNumbers[] = $c->mobile_no;
    //             }
    //         }

    //         $mobileNumbers = array_unique(array_filter($mobileNumbers));

    //         if (empty($mobileNumbers)) {
    //             $campaign->update([
    //                 'message_status' => 'Failed',
    //                 'message_text' => 'No valid contacts found'
    //             ]);
    //             return redirect()->back()->with('error', 'No valid contacts found to send SMS');
    //         }

    //         // ✅ Send SMS via reusable function
    //         $responseData = $this->sendSmsViaApi($smsAPI, $message, $mobileNumbers);
    //         $totalSent = $responseData['totalSent'];
    //         $apiResponses = $responseData['apiResponses'];

    //         $campaign->update([
    //             'message_status' => $totalSent > 0 ? 'success' : 'failed',
    //             'message_text' => $totalSent > 0
    //                 ? "SMS sent to {$totalSent} contacts"
    //                 : "Failed to send SMS"
    //             // 'api_response' => json_encode($apiResponses)
    //         ]);

    //         return redirect()->back()->with('success', "{$totalSent} SMS sent successfully!");
    //     } catch (\Throwable $th) {
    //         \Log::error('SMS Campaign Error', [
    //             'error' => $th->getMessage(),
    //             'trace' => $th->getTraceAsString()
    //         ]);
            
            
    //         if (isset($campaign)) {
    //             $campaign->update([
    //                 'message_status' => 'Failed',
    //                 'message_text' => 'Exception: '.$th->getMessage()
    //             ]);
    //         }

    //         return redirect()->back()->with('error', 'Something went wrong: '.$th->getMessage());
    //     }
    // }

    // public function store(Request $request)
    // {
    //     try {
    //         $smsAPI      = SmsApi::find($request->sms_api_id);
    //         $smsTemplate = SmsTemplate::find($request->sms_template_id);
    //         $audience    = $request->audience;
    //         $message     = trim($request->sms_msg);
    //         $send_time   = $request->send_time ?? now(); // ✅ default current time
    //         $adminId     = Auth::guard('admin')->user()->id;
    
    //         // 🔹 Create campaign entry
    //         $campaign = new SmsCampaign();
    //         $campaign->campaign_name = $request->campaign_name;
    //         $campaign->audience      = $audience;
    //         $campaign->msg_body      = $message;
    //         $campaign->admin_id      = $adminId;
    //         $campaign->sms_temp_id   = $request->sms_template_id;
    //         $campaign->sms_api_id    = $request->sms_api_id;
    //         $campaign->sch_type      = $request->sch_type;
    //         $campaign->date_and_time = ($request->sch_type == 'Scheduled') ? $request->date_and_time : $send_time;
    
    //         // 🔹 Store filters for reference (for "allcontact")
    //         if ($audience == 'allcontact') {
    //             if ($request->careoff_id2) $campaign->careoff_id = implode(",", $request->careoff_id2);
    //             if ($request->country_id) $campaign->country_id = implode(",", $request->country_id);
    //             if ($request->country_code) $campaign->country_code = implode(",", $request->country_code);
    //             if ($request->groupmallc) $campaign->group_id = implode(",", $request->groupmallc);
    //             if ($request->allcontact_subscribe) $campaign->subscribe = implode(",", $request->allcontact_subscribe);
    //             if ($request->lead_type) $campaign->business_type_contact = implode(",", $request->lead_type);
    //             $campaign->contact_type = $request->allcontact_contact_type;
    //         }
    
    //         $campaign->save();
    
    //         // 🔹 Handle Scheduled Campaigns
    //         if ($request->sch_type == 'Scheduled') {
    //             $campaign->update([
    //                 'message_status' => 'Scheduled',
    //                 'message_text'   => 'Message scheduled for later sending.',
    //             ]);
    
    //             SmsSendLog::create([
    //                 'audience'       => $audience,
    //                 'campaign_id'    => $campaign->id,
    //                 'admin_id'       => $adminId,
    //                 'status'         => 'scheduled',
    //                 'message'        => 'Campaign scheduled successfully',
    //                 'send_time'      => $send_time,
    //                 'total_contacts' => 0,
    //             ]);
    
    //             return redirect()->back()->with('success', 'Campaign scheduled successfully.');
    //         }
    
    //         // 🔹 Send Now Logic
    //         $mobileNumbers = [];
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Leads Audience
    //         |--------------------------------------------------------------------------
    //         */
    //         if ($audience == 'Leads') {
    //             $lead_is_qualified = $request->lead_is_qualified;
    //             $leadassign_id     = $request->leadassign_id;
    //             $lead_contact_type = $request->lead_contact_type;
    
    //             $query = DB::table('leads')
    //                 ->when($lead_is_qualified, fn($q) => $q->whereIn('is_qualified', (array) $lead_is_qualified))
    //                 ->when($leadassign_id, fn($q) => $q->whereIn('leadassign_id', (array) $leadassign_id));
    
    //             if (empty($lead_contact_type)) {
    //                 $query->where(function ($sub) {
    //                     $sub->whereNotNull('mob_no')->where('mob_no', '!=', '')
    //                         ->orWhereNotNull('whatsapp_no')->where('whatsapp_no', '!=', '');
    //                 });
    //             } elseif ($lead_contact_type == 'mob_no') {
    //                 $query->whereNotNull('mob_no')->where('mob_no', '!=', '');
    //             } elseif ($lead_contact_type == 'whatsapp_no') {
    //                 $query->whereNotNull('whatsapp_no')->where('whatsapp_no', '!=', '');
    //             }
    
    //             $leads = $query->get(['mob_no', 'whatsapp_no']);
    
    //             foreach ($leads as $lead) {
    //                 if ($lead_contact_type == 'mob_no' && $lead->mob_no) {
    //                     $mobileNumbers[] = $lead->mob_no;
    //                 } elseif ($lead_contact_type == 'whatsapp_no' && $lead->whatsapp_no) {
    //                     $mobileNumbers[] = $lead->whatsapp_no;
    //                 } else {
    //                     if ($lead->mob_no) $mobileNumbers[] = $lead->mob_no;
    //                     if ($lead->whatsapp_no) $mobileNumbers[] = $lead->whatsapp_no;
    //                 }
    //             }
    //         }
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Allcontact Audience
    //         |--------------------------------------------------------------------------
    //         */
    //         elseif ($audience == 'allcontact') {
    //             $group        = $request->groupmallc;
    //             $careoff      = $request->careoff_id2;
    //             $country      = $request->country_id;
    //             $leadtype     = $request->lead_type;
    //             $optinout     = $request->allcontact_subscribe;
    //             $country_code = $request->country_code;
    //             $contact_type = $request->allcontact_contact_type;
    
    //             $contacts = Allcontact::where(function ($query) use ($group, $careoff, $country, $leadtype, $optinout, $country_code) {
    //                 if ($group) $query->whereIn('group_id', $group);
    //                 if ($careoff) $query->whereIn('careoff_id', $careoff);
    //                 if ($country) $query->whereIn('country_id', $country);
    //                 if ($leadtype) $query->whereIn('lead_type', $leadtype);
    //                 if ($optinout) $query->whereIn('optinout', $optinout);
    //                 if ($country_code) {
    //                     $query->where(function ($q) use ($country_code) {
    //                         foreach ($country_code as $code) {
    //                             $q->orWhere('primary_no_wsp', 'like', $code.'%')
    //                                 ->orWhere('secondary_no_wsp', 'like', $code.'%')
    //                                 ->orWhere('mobile_no1_wsp', 'like', $code.'%');
    //                         }
    //                     });
    //                 }
    //             })->get();
    
    //             foreach ($contacts as $c) {
    //                 if ($contact_type == '4' && $c->mobile_no1_wsp) $mobileNumbers[] = $c->mobile_no1_wsp;
    //                 elseif ($contact_type == '3' && $c->primary_no_wsp) $mobileNumbers[] = $c->primary_no_wsp;
    //                 elseif ($contact_type == '2' && $c->secondary_no_wsp) $mobileNumbers[] = $c->secondary_no_wsp;
    //                 else {
    //                     if ($c->mobile_no1_wsp) $mobileNumbers[] = $c->mobile_no1_wsp;
    //                     if ($c->primary_no_wsp) $mobileNumbers[] = $c->primary_no_wsp;
    //                     if ($c->secondary_no_wsp) $mobileNumbers[] = $c->secondary_no_wsp;
    //                 }
    //             }
    //         }
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Contact+, Associate, Client, Partner
    //         |--------------------------------------------------------------------------
    //         */
    //         elseif ($audience == 'contactp') {
    //             $group     = $request->contact_group;
    //             $business  = $request->business_type_contact;
    //             $careoff   = $request->careoff_id;
    //             $subscribe = $request->contactp_subscribe;
    //             $type      = $request->contactp_contact_type;
    
    //             $contacts = Contactplus::where(function ($q) use ($group, $business, $careoff, $subscribe) {
    //                 if ($group) $q->whereIn('group_id', $group);
    //                 if ($business) $q->whereIn('businesstype_id', $business);
    //                 if ($careoff) $q->whereIn('careoff_id', $careoff);
    //                 if ($subscribe) $q->whereIn('subscribe', $subscribe);
    //             })->get();
    
    //             foreach ($contacts as $c) {
    //                 if ($type == '4' && $c->sec_contact) $mobileNumbers[] = $c->sec_contact;
    //                 elseif ($type == '3' && $c->prim_contact) $mobileNumbers[] = $c->prim_contact;
    //                 elseif ($type == '2' && $c->owner_contact) $mobileNumbers[] = $c->owner_contact;
    //                 else {
    //                     if ($c->sec_contact) $mobileNumbers[] = $c->sec_contact;
    //                     if ($c->prim_contact) $mobileNumbers[] = $c->prim_contact;
    //                     if ($c->owner_contact) $mobileNumbers[] = $c->owner_contact;
    //                 }
    //             }
    //         } elseif ($audience == 'associate') {
    //             $contacts = Associates::get();
    //             foreach ($contacts as $c) {
    //                 if ($c->mobile_no) $mobileNumbers[] = $c->mobile_no;
    //             }
    //         } elseif ($audience == 'client') {
    //             $contacts = User::get();
    //             foreach ($contacts as $c) {
    //                 if ($c->mobile_no) $mobileNumbers[] = $c->mobile_no;
    //             }
    //         } elseif ($audience == 'partner') {
    //             $contacts = Partner::get();
    //             foreach ($contacts as $c) {
    //                 if ($c->mobile_no) $mobileNumbers[] = $c->mobile_no;
    //             }
    //         }
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Final Send Process
    //         |--------------------------------------------------------------------------
    //         */
    //         $mobileNumbers = array_unique(array_filter($mobileNumbers));
    
    //         if (empty($mobileNumbers)) {
    //             $campaign->update([
    //                 'message_status' => 'Failed',
    //                 'message_text'   => 'No valid contacts found'
    //             ]);
    
    //             SmsSendLog::create([
    //                 'audience'       => $audience,
    //                 'campaign_id'    => $campaign->id,
    //                 'admin_id'       => $adminId,
    //                 'total_contacts' => 0,
    //                 'status'         => 'failed',
    //                 'message'        => 'No valid contacts found',
    //                 'send_time'      => $send_time,
    //             ]);
    
    //             return redirect()->back()->with('error', 'No valid contacts found to send SMS');
    //         }
    
    //         // ✅ Send SMS via reusable function
    //         $responseData = $this->sendSmsViaApi($smsAPI, $message, $mobileNumbers);
    //         $totalSent    = $responseData['totalSent'];
    //         $apiResponses = $responseData['apiResponses'];
    
    //         // ✅ Update campaign & log
    //         $status  = $totalSent > 0 ? 'success' : 'failed';
    //         $messageText = $totalSent > 0
    //             ? "SMS sent to {$totalSent} contacts"
    //             : "Failed to send SMS";
    
    //         $campaign->update([
    //             'message_status' => $status,
    //             'message_text'   => $messageText,
    //         ]);
    
    //         SmsSendLog::create([
    //             'audience'       => $audience,
    //             'campaign_id'    => $campaign->id,
    //             'admin_id'       => $adminId,
    //             'total_contacts' => count($mobileNumbers),
    //             'status'         => $status,
    //             'message'        => $messageText,
    //             'api_response'   => json_encode($apiResponses),
    //             'send_time'      => $send_time,
    //         ]);
    
    //         return redirect()->back()->with('success', "{$totalSent} SMS sent successfully!");
    
    //     } catch (\Throwable $th) {
    //         \Log::error('SMS Campaign Error', [
    //             'error' => $th->getMessage(),
    //             'trace' => $th->getTraceAsString(),
    //         ]);
    
    //         if (isset($campaign)) {
    //             $campaign->update([
    //                 'message_status' => 'Failed',
    //                 'message_text'   => 'Exception: '.$th->getMessage(),
    //             ]);
    
    //             SmsSendLog::create([
    //                 'audience'       => $audience ?? 'unknown',
    //                 'campaign_id'    => $campaign->id ?? null,
    //                 'admin_id'       => $adminId ?? null,
    //                 'status'         => 'failed',
    //                 'message'        => 'Exception: '.$th->getMessage(),
    //                 'send_time'      => now(),
    //             ]);
    //         }
    
    //         return redirect()->back()->with('error', 'Something went wrong: '.$th->getMessage());
    //     }
    // }

    // private function sendSmsViaApi($smsAPI, $message, $mobileNumbers = []){
    //     $apiResponses = [];
    //     $totalSent = 0;

    //     try {
    //         $base_url = $smsAPI->api_base_url ?? "https://qlogin.zapim.com";
    //         $send_url = rtrim($base_url, '/') . '/api/v2/SendSMS';

    //         // ✅ Remove country codes from mobile numbers
    //         $cleanNumbers = [];
    //         foreach ($mobileNumbers as $num) {
    //             $num = trim($num);

    //             // Remove + or leading 00 and country codes (e.g., +91, 91, 971)
    //             $num = preg_replace('/^\+?91|^\+?971|^\+?966|^\+?1|^\+?44|^\+?880|^\+?94|^\+?92|^\+?60|^\+?65|^\+?81|^\+?20/', '', $num);
    //             $num = preg_replace('/\D/', '', $num); // keep digits only

    //             // Ensure it's at least 8 digits (to avoid garbage)
    //             if (strlen($num) >= 8) {
    //                 $cleanNumbers[] = $num;
    //             }
    //         }

    //         $cleanNumbers = array_unique($cleanNumbers);
    //         if (empty($cleanNumbers)) {
    //             \Log::warning('No valid cleaned mobile numbers found for SMS sending.');
    //             return [
    //                 'totalSent' => 0,
    //                 'apiResponses' => [['status' => 'failed', 'response' => 'No valid numbers after cleaning']]
    //             ];
    //         }

    //         $chunks = array_chunk($cleanNumbers, 100);

    //         foreach ($chunks as $batch) {
    //             $payload = [
    //                 "senderId" => $smsAPI->sender_id ?? "QAMR",
    //                 "is_Unicode" => false,
    //                 "is_Flash" => false,
    //                 "isRegisteredForDelivery" => true,
    //                 "validityPeriod" => 1440,
    //                 "dataCoding" => 0,
    //                 "schedTime" => "",
    //                 "groupId" => "7",
    //                 "message" => $message,
    //                 "mobileNumbers" => implode(',', $batch), // cleaned numbers
    //                 "principleEntityId" => $smsAPI->entity_id ?? "1101647460000048498",
    //                 "templateId" => $smsAPI->template_id ?? "1107176190670884363",
    //                 "apiKey" => $smsAPI->api_key ?? "+5vVPV8PYZ4UEU/4MbBDUl3ci/wal1pRnsg2/cBixfk=",
    //                 "clientId" => $smsAPI->client_id ?? "e4b5a2f4-08cd-4159-8807-cb917e74666f"
    //             ];

    //             try {
    //                 $response = Http::withHeaders([
    //                     'Content-Type' => 'application/json',
    //                     'Accept' => 'application/json'
    //                 ])->timeout(20)->post($send_url, $payload);

    //                 if ($response->successful()) {
    //                     $data = $response->json();
    //                     $totalSent += count($batch);
    //                     $apiResponses[] = [
    //                         'batch' => $batch,
    //                         'status' => 'success',
    //                         'response' => $data
    //                     ];
    //                 } else {
    //                     $apiResponses[] = [
    //                         'batch' => $batch,
    //                         'status' => 'failed',
    //                         'response' => $response->body()
    //                     ];
    //                     \Log::error('SMS API Error', [
    //                         'payload' => $payload,
    //                         'response' => $response->body()
    //                     ]);
    //                 }
    //             } catch (\Throwable $e) {
    //                 $apiResponses[] = [
    //                     'batch' => $batch,
    //                     'status' => 'exception',
    //                     'response' => $e->getMessage()
    //                 ];
    //                 \Log::error('SMS Batch Exception', [
    //                     'error' => $e->getMessage(),
    //                     'payload' => $payload
    //                 ]);
    //             }
    //         }
    //     } catch (\Throwable $ex) {
    //         \Log::error('sendSmsViaApi() Exception', [
    //             'error' => $ex->getMessage(),
    //             'trace' => $ex->getTraceAsString()
    //         ]);
    //     }

    //     return [
    //         'totalSent' => $totalSent,
    //         'apiResponses' => $apiResponses
    //     ];
    // }



    // private function sendSmsViaApi($smsAPI, $message, $mobileNumbers = [], $campaignId = null, $audience = null)
    // {
    //     $totalSent = 0;

    //     try {
    //         $base_url = $smsAPI->api_base_url ?? "https://qlogin.zapim.com";
    //         $send_url = rtrim($base_url, '/') . '/api/v2/SendSMS';

    //         dd($mobileNumbers);


    //         // ✅ Clean numbers
    //         $cleanNumbers = [];
    //         foreach ($mobileNumbers as $num) {
    //             $num = trim($num);
    //             $num = preg_replace('/^\+?91|^\+?971|^\+?966|^\+?1|^\+?44|^\+?880|^\+?94|^\+?92|^\+?60|^\+?65|^\+?81|^\+?20/', '', $num);
    //             $num = preg_replace('/\D/', '', $num);
    //             if (strlen($num) >= 8) $cleanNumbers[] = $num;
    //         }

    //         $cleanNumbers = array_unique($cleanNumbers);
    //         if (empty($cleanNumbers)) {
    //             \Log::warning('No valid cleaned numbers found for SMS sending.');
    //             return ['totalSent' => 0];
    //         }

    //         $chunks = array_chunk($cleanNumbers, 100);
    //         foreach ($chunks as $batch) {
    //             $payload = [
    //                 "senderId" => $smsAPI->sender_id ?? "QAMR",
    //                 "is_Unicode" => false,
    //                 "is_Flash" => false,
    //                 "isRegisteredForDelivery" => true,
    //                 "validityPeriod" => 1440,
    //                 "dataCoding" => 0,
    //                 "schedTime" => "",
    //                 "groupId" => "7",
    //                 "message" => $message,
    //                 "mobileNumbers" => implode(',', $batch),
    //                 "principleEntityId" => $smsAPI->entity_id ?? "1101647460000048498",
    //                 "templateId" => $smsAPI->template_id ?? "1107176190670884363",
    //                 "apiKey" => $smsAPI->api_key ?? "+5vVPV8PYZ4UEU/4MbBDUl3ci/wal1pRnsg2/cBixfk=",
    //                 "clientId" => $smsAPI->client_id ?? "e4b5a2f4-08cd-4159-8807-cb917e74666f"
    //             ];

    //             try {
    //                 $response = Http::withHeaders([
    //                     'Content-Type' => 'application/json',
    //                     'Accept' => 'application/json'
    //                 ])->timeout(20)->post($send_url, $payload);

    //                 if ($response->successful()) {
    //                     $data = $response->json();
    //                     $totalSent += count($batch);

    //                     // ✅ Save each number as success
    //                     foreach ($batch as $num) {
    //                         SmsCampaignResponse::create([
    //                             'sms_campaign_id' => $campaignId,
    //                             'message_status'  => 'success',
    //                             'message_text'    => 'SMS sent successfully',
    //                             'mobile_no'       => $num,
    //                             'main_response'   => json_encode($data),
    //                             'name'            => null,
    //                             'error_data_field'=> null,
    //                             $audience == 'Leads' ? 'lead_id' : 'allcontact_id' => null,
    //                         ]);
    //                     }
    //                 } else {
    //                     // ❌ Save each number as failed (API error)
    //                     foreach ($batch as $num) {
    //                         SmsCampaignResponse::create([
    //                             'sms_campaign_id' => $campaignId,
    //                             'message_status'  => 'failed',
    //                             'message_text'    => 'API Response Error',
    //                             'mobile_no'       => $num,
    //                             'main_response'   => $response->body(),
    //                             'error_data_field'=> 'API response error',
    //                         ]);
    //                     }

    //                     \Log::error('SMS API Error', [
    //                         'payload' => $payload,
    //                         'response' => $response->body()
    //                     ]);
    //                 }
    //             } catch (\Throwable $e) {
    //                 // ❌ Exception while sending
    //                 foreach ($batch as $num) {
    //                     SmsCampaignResponse::create([
    //                         'sms_campaign_id' => $campaignId,
    //                         'message_status'  => 'failed',
    //                         'message_text'    => $e->getMessage(),
    //                         'mobile_no'       => $num,
    //                         'error_data_field'=> 'Exception while sending',
    //                         'main_response'   => null,
    //                     ]);
    //                 }

    //                 \Log::error('SMS Batch Exception', [
    //                     'error' => $e->getMessage(),
    //                     'payload' => $payload
    //                 ]);
    //             }
    //         }
    //     } catch (\Throwable $ex) {
    //         \Log::error('sendSmsViaApi() Exception', [
    //             'error' => $ex->getMessage(),
    //             'trace' => $ex->getTraceAsString()
    //         ]);
    //     }

    //     return ['totalSent' => $totalSent];
    // }


    public function store(Request $request)
    {
        try {
            $smsAPI      = SmsApi::find($request->sms_api_id);
            $smsTemplate = SmsTemplate::find($request->sms_template_id);
            $audience    = $request->audience;
            $message     = trim($request->sms_msg);
            $send_time   = $request->send_time ?? now();
            $adminId     = Auth::guard('admin')->user()->id;

            // 🔹 Create campaign entry
            $campaign = new SmsCampaign();
            $campaign->campaign_name = $request->campaign_name;
            $campaign->audience      = $audience;
            $campaign->msg_body      = $message;
            $campaign->admin_id      = $adminId;
            $campaign->sms_temp_id   = $request->sms_template_id;
            $campaign->sms_api_id    = $request->sms_api_id;
            $campaign->sch_type      = $request->sch_type;
            $campaign->date_and_time = ($request->sch_type == 'Scheduled') ? $request->date_and_time : $send_time;
            $campaign->save();

            // 🔹 Handle Scheduled
            if ($request->sch_type == 'Scheduled') {
                $campaign->update([
                    'message_status' => 'Scheduled',
                    'message_text'   => 'Message scheduled for later sending.',
                ]);

                SmsCampaignResponse::create([
                    'message_status'  => 'Scheduled',
                    'message_text'    => 'Campaign scheduled successfully',
                    'sms_campaign_id' => $campaign->id,
                    'main_response'   => 'Scheduled for ' . $send_time,
                ]);

                return redirect()->back()->with('success', 'Campaign scheduled successfully.');
            }

            // 🔹 Collect contacts (simplified)
            $mobileNumbers = $this->collectCampaignContacts($request, $audience);

            if (empty($mobileNumbers)) {
                $campaign->update([
                    'message_status' => 'Failed',
                    'message_text'   => 'No valid contacts found'
                ]);

                SmsCampaignResponse::create([
                    'message_status'  => 'Failed',
                    'message_text'    => 'No valid contacts found',
                    'sms_campaign_id' => $campaign->id,
                ]);

                return redirect()->back()->with('error', 'No valid contacts found to send SMS');
            }

            // ✅ Send & Log Each Response
            $responseData = $this->sendSmsViaApi($smsAPI, $message, $mobileNumbers, $campaign->id, $audience);

            // ✅ Update campaign summary
            $campaign->update([
                'message_status' => $responseData['totalSent'] > 0 ? 'Partial/Success' : 'Failed',
                'message_text'   => "SMS sent to {$responseData['totalSent']} of " . count($mobileNumbers) . " contacts",
            ]);

            return redirect()->back()->with('success', "{$responseData['totalSent']} SMS sent successfully!");

        } catch (\Throwable $th) {
            \Log::error('SMS Campaign Error', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            if (isset($campaign)) {
                $campaign->update([
                    'message_status' => 'Failed',
                    'message_text'   => 'Exception: ' . $th->getMessage(),
                ]);

                SmsCampaignResponse::create([
                    'message_status'  => 'failed',
                    'message_text'    => 'Exception: ' . $th->getMessage(),
                    'sms_campaign_id' => $campaign->id,
                    'main_response'   => $th->getTraceAsString(),
                ]);
            }

            return redirect()->back()->with('error', 'Something went wrong: ' . $th->getMessage());
        }
    }

    private function collectCampaignContacts(Request $request, $audience)
    {
        $contacts = [];

        /*
        |--------------------------------------------------------------------------
        | LEADS
        |--------------------------------------------------------------------------
        */
        if ($audience == 'Leads') {
            $lead_is_qualified = $request->lead_is_qualified;
            $leadassign_id     = $request->leadassign_id;
            $lead_contact_type = $request->lead_contact_type;

            $query = DB::table('leads')
                ->when($lead_is_qualified, fn($q) => $q->whereIn('is_qualified', (array) $lead_is_qualified))
                ->when($leadassign_id, fn($q) => $q->whereIn('leadassign_id', (array) $leadassign_id));

            if (empty($lead_contact_type)) {
                $query->where(function ($sub) {
                    $sub->whereNotNull('mob_no')->where('mob_no', '!=', '')
                        ->orWhereNotNull('whatsapp_no')->where('whatsapp_no', '!=', '');
                });
            } elseif ($lead_contact_type == 'mob_no') {
                $query->whereNotNull('mob_no')->where('mob_no', '!=', '');
            } elseif ($lead_contact_type == 'whatsapp_no') {
                $query->whereNotNull('whatsapp_no')->where('whatsapp_no', '!=', '');
            }

            $leads = $query->get(['id', 'name', 'mob_no', 'whatsapp_no']);

            foreach ($leads as $lead) {
                if ($lead_contact_type == 'mob_no' && $lead->mob_no) {
                    $contacts[] = ['id' => $lead->id, 'name' => $lead->name, 'mobile' => $lead->mob_no];
                } elseif ($lead_contact_type == 'whatsapp_no' && $lead->whatsapp_no) {
                    $contacts[] = ['id' => $lead->id, 'name' => $lead->name, 'mobile' => $lead->whatsapp_no];
                } else {
                    if ($lead->mob_no) $contacts[] = ['id' => $lead->id, 'name' => $lead->name, 'mobile' => $lead->mob_no];
                    if ($lead->whatsapp_no) $contacts[] = ['id' => $lead->id, 'name' => $lead->name, 'mobile' => $lead->whatsapp_no];
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ALLCONTACT
        |--------------------------------------------------------------------------
        */
        elseif ($audience == 'allcontact') {
            $group        = $request->groupmallc;
            $careoff      = $request->careoff_id2;
            $country      = $request->country_id;
            $leadtype     = $request->lead_type;
            $optinout     = $request->allcontact_subscribe;
            $country_code = $request->country_code;
            $contact_type = $request->allcontact_contact_type;

            $query = Allcontact::query();

            if ($group) $query->whereIn('group_id', $group);
            if ($careoff) $query->whereIn('careoff_id', $careoff);
            if ($country) $query->whereIn('country_id', $country);
            if ($leadtype) $query->whereIn('lead_type', $leadtype);
            if ($optinout) $query->whereIn('optinout', $optinout);

            if ($country_code) {
                $query->where(function ($q) use ($country_code) {
                    foreach ($country_code as $code) {
                        $q->orWhere('primary_no_wsp', 'like', $code.'%')
                        ->orWhere('secondary_no_wsp', 'like', $code.'%')
                        ->orWhere('mobile_no1_wsp', 'like', $code.'%');
                    }
                });
            }

            $allcontacts = $query->get(['id', 'name', 'mobile_no1_wsp', 'primary_no_wsp', 'secondary_no_wsp']);

            foreach ($allcontacts as $c) {
                if ($contact_type == '4' && $c->mobile_no1_wsp)
                    $contacts[] = ['id' => $c->id, 'name' => $c->name, 'mobile' => $c->mobile_no1_wsp];
                elseif ($contact_type == '3' && $c->primary_no_wsp)
                    $contacts[] = ['id' => $c->id, 'name' => $c->name, 'mobile' => $c->primary_no_wsp];
                elseif ($contact_type == '2' && $c->secondary_no_wsp)
                    $contacts[] = ['id' => $c->id, 'name' => $c->name, 'mobile' => $c->secondary_no_wsp];
                else {
                    if ($c->mobile_no1_wsp) $contacts[] = ['id' => $c->id, 'name' => $c->name, 'mobile' => $c->mobile_no1_wsp];
                    if ($c->primary_no_wsp) $contacts[] = ['id' => $c->id, 'name' => $c->name, 'mobile' => $c->primary_no_wsp];
                    if ($c->secondary_no_wsp) $contacts[] = ['id' => $c->id, 'name' => $c->name, 'mobile' => $c->secondary_no_wsp];
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CONTACT+
        |--------------------------------------------------------------------------
        */
        elseif ($audience == 'contactp') {
            $group     = $request->contact_group;
            $business  = $request->business_type_contact;
            $careoff   = $request->careoff_id;
            $subscribe = $request->contactp_subscribe;
            $type      = $request->contactp_contact_type;

            $query = Contactplus::query();
            if ($group) $query->whereIn('group_id', $group);
            if ($business) $query->whereIn('businesstype_id', $business);
            if ($careoff) $query->whereIn('careoff_id', $careoff);
            if ($subscribe) $query->whereIn('subscribe', $subscribe);

            $contactsPlus = $query->get(['id', 'name', 'owner_contact', 'prim_contact', 'sec_contact']);

            foreach ($contactsPlus as $c) {
                if ($type == '4' && $c->sec_contact)
                    $contacts[] = ['id' => $c->id, 'name' => $c->name, 'mobile' => $c->sec_contact];
                elseif ($type == '3' && $c->prim_contact)
                    $contacts[] = ['id' => $c->id, 'name' => $c->name, 'mobile' => $c->prim_contact];
                elseif ($type == '2' && $c->owner_contact)
                    $contacts[] = ['id' => $c->id, 'name' => $c->name, 'mobile' => $c->owner_contact];
                else {
                    if ($c->owner_contact) $contacts[] = ['id' => $c->id, 'name' => $c->name, 'mobile' => $c->owner_contact];
                    if ($c->prim_contact)  $contacts[] = ['id' => $c->id, 'name' => $c->name, 'mobile' => $c->prim_contact];
                    if ($c->sec_contact)   $contacts[] = ['id' => $c->id, 'name' => $c->name, 'mobile' => $c->sec_contact];
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ASSOCIATES, CLIENTS, PARTNERS
        |--------------------------------------------------------------------------
        */
        elseif ($audience == 'associate') {
            $list = Associates::get(['id', 'name', 'mobile_no']);
            foreach ($list as $c) {
                if ($c->mobile_no)
                    $contacts[] = ['id' => $c->id, 'name' => $c->name, 'mobile' => $c->mobile_no];
            }
        } elseif ($audience == 'client') {
            $list = User::get(['id', 'name', 'mobile_no']);
            foreach ($list as $c) {
                if ($c->mobile_no)
                    $contacts[] = ['id' => $c->id, 'name' => $c->name, 'mobile' => $c->mobile_no];
            }
        } elseif ($audience == 'partner') {
            $list = Partner::get(['id', 'name', 'mobile_no']);
            foreach ($list as $c) {
                if ($c->mobile_no)
                    $contacts[] = ['id' => $c->id, 'name' => $c->name, 'mobile' => $c->mobile_no];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CLEAN + UNIQUE
        |--------------------------------------------------------------------------
        */
        $contacts = collect($contacts)
            ->filter(fn($c) => !empty($c['mobile']))
            ->unique('mobile')
            ->values()
            ->toArray();

        return $contacts;
    }

    private function sendSmsViaApi($smsAPI, $message, $mobileNumbers = [], $campaignId = null, $audience = null)
    {
        $totalSent = 0;

        try {
            $base_url = $smsAPI->api_base_url ?? "https://qlogin.zapim.com";
            $send_url = rtrim($base_url, '/') . '/api/v2/SendSMS';

            // ✅ Clean numbers
            $cleanNumbers = [];
            foreach ($mobileNumbers as $num) {
                $num = trim($num);
                $num = preg_replace('/^\+?91|^\+?971|^\+?966|^\+?1|^\+?44|^\+?880|^\+?94|^\+?92|^\+?60|^\+?65|^\+?81|^\+?20/', '', $num);
                $num = preg_replace('/\D/', '', $num);
                if (strlen($num) >= 8) $cleanNumbers[] = $num;
            }

            $cleanNumbers = array_unique($cleanNumbers);
            if (empty($cleanNumbers)) {
                \Log::warning('No valid cleaned numbers found for SMS sending.');
                return ['totalSent' => 0];
            }

            $chunks = array_chunk($cleanNumbers, 100);

            foreach ($chunks as $batch) {
                $payload = [
                    "senderId" => $smsAPI->sender_id ?? "QAMR",
                    "is_Unicode" => false,
                    "is_Flash" => false,
                    "isRegisteredForDelivery" => true,
                    "validityPeriod" => 1440,
                    "dataCoding" => 0,
                    "schedTime" => "",
                    "groupId" => "7",
                    "message" => $message,
                    "mobileNumbers" => implode(',', $batch),
                    "principleEntityId" => $smsAPI->entity_id ?? "1101647460000048498",
                    "templateId" => $smsAPI->template_id ?? "1107176190670884363",
                    "apiKey" => $smsAPI->api_key ?? "+5vVPV8PYZ4UEU/4MbBDUl3ci/wal1pRnsg2/cBixfk=",
                    "clientId" => $smsAPI->client_id ?? "e4b5a2f4-08cd-4159-8807-cb917e74666f"
                ];

                try {
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ])->timeout(20)->post($send_url, $payload);

                    if ($response->successful()) {
                        $data = $response->json();
                        $totalSent += count($batch);

                        // ✅ Save each number as success
                        foreach ($batch as $num) {
                            SmsCampaignResponse::create([
                                'sms_campaign_id' => $campaignId,
                                'message_status'  => 'success',
                                'message_text'    => 'SMS sent successfully',
                                'mobile_no'       => $num,
                                'main_response'   => json_encode($data),
                                'name'            => null,
                                'error_data_field'=> null,
                                $audience == 'Leads' ? 'lead_id' : 'allcontact_id' => null,
                            ]);
                        }
                    } else {
                        // ❌ Save each number as failed (API error)
                        foreach ($batch as $num) {
                            SmsCampaignResponse::create([
                                'sms_campaign_id' => $campaignId,
                                'message_status'  => 'failed',
                                'message_text'    => 'API Response Error',
                                'mobile_no'       => $num,
                                'main_response'   => $response->body(),
                                'error_data_field'=> 'API response error',
                            ]);
                        }

                        \Log::error('SMS API Error', [
                            'payload' => $payload,
                            'response' => $response->body()
                        ]);
                    }
                } catch (\Throwable $e) {
                    // ❌ Exception while sending
                    foreach ($batch as $num) {
                        SmsCampaignResponse::create([
                            'sms_campaign_id' => $campaignId,
                            'message_status'  => 'failed',
                            'message_text'    => $e->getMessage(),
                            'mobile_no'       => $num,
                            'error_data_field'=> 'Exception while sending',
                            'main_response'   => null,
                        ]);
                    }

                    \Log::error('SMS Batch Exception', [
                        'error' => $e->getMessage(),
                        'payload' => $payload
                    ]);
                }
            }
        } catch (\Throwable $ex) {
            \Log::error('sendSmsViaApi() Exception', [
                'error' => $ex->getMessage(),
                'trace' => $ex->getTraceAsString()
            ]);
        }

        return ['totalSent' => $totalSent];
    }



    // private function sendSmsViaApi($smsAPI, $message, $contacts = [], $campaignId = null, $audience = null) {
    //     $totalSent = 0;

    //     try {
    //         $base_url = $smsAPI->api_base_url ?? "https://qlogin.zapim.com";
    //         $send_url = rtrim($base_url, '/') . '/api/v2/SendSMS';

    //         // ✅ Clean & extract numbers
    //         $cleanContacts = collect($contacts)->map(function ($c) {
    //             $num = trim($c['mobile'] ?? '');
    //             $num = preg_replace('/^\+?91|^\+?971|^\+?966|^\+?1|^\+?44|^\+?880|^\+?94|^\+?92|^\+?60|^\+?65|^\+?81|^\+?20/', '', $num);
    //             $num = preg_replace('/\D/', '', $num);
    //             if (strlen($num) >= 8) {
    //                 $c['mobile'] = $num;
    //                 return $c;
    //             }
    //             return null;
    //         })->filter()->unique('mobile')->values();

    //         if ($cleanContacts->isEmpty()) {
    //             \Log::warning('No valid cleaned contacts found for SMS sending.');
    //             return ['totalSent' => 0];
    //         }

    //         $chunks = $cleanContacts->chunk(100);

    //         foreach ($chunks as $batch) {
    //             $numbers = $batch->pluck('mobile')->toArray();

    //             $payload = [
    //                 "senderId"             => $smsAPI->sender_id ?? "QAMR",
    //                 "is_Unicode"           => false,
    //                 "is_Flash"             => false,
    //                 "isRegisteredForDelivery" => true,
    //                 "validityPeriod"       => 1440,
    //                 "dataCoding"           => 0,
    //                 "schedTime"            => "",
    //                 "groupId"              => "7",
    //                 "message"              => $message,
    //                 "mobileNumbers"        => implode(',', $numbers),
    //                 "principleEntityId"    => $smsAPI->entity_id ?? "1101647460000048498",
    //                 "templateId"           => $smsAPI->template_id ?? "1107176190670884363",
    //                 "apiKey"               => $smsAPI->api_key ?? "+5vVPV8PYZ4UEU/4MbBDUl3ci/wal1pRnsg2/cBixfk=",
    //                 "clientId"             => $smsAPI->client_id ?? "e4b5a2f4-08cd-4159-8807-cb917e74666f"
    //             ];

    //             try {
    //                 $response = Http::withHeaders([
    //                     'Content-Type' => 'application/json',
    //                     'Accept'       => 'application/json'
    //                 ])->timeout(20)->post($send_url, $payload);

    //                 if ($response->successful()) {
    //                     $data = $response->json();
    //                     $totalSent += count($numbers);

    //                     // ✅ Save all batch responses in bulk
    //                     $saveData = [];
    //                     foreach ($batch as $c) {
    //                         $saveData[] = [
    //                             'sms_campaign_id' => $campaignId,
    //                             'message_status'  => 'success',
    //                             'message_text'    => 'SMS sent successfully',
    //                             'mobile_no'       => $c['mobile'],
    //                             'name'            => $c['name'] ?? null,
    //                             'main_response'   => json_encode($data),
    //                             'error_data_field'=> null,
    //                             'lead_id'         => $audience === 'Leads' ? ($c['id'] ?? null) : null,
    //                             'allcontact_id'   => $audience === 'allcontact' ? ($c['id'] ?? null) : null,
    //                             'partner_id'      => $audience === 'partner' ? ($c['id'] ?? null) : null,
    //                             'associate_id'    => $audience === 'associate' ? ($c['id'] ?? null) : null,
    //                             'client_id'       => $audience === 'client' ? ($c['id'] ?? null) : null,
    //                             'contactp_id'     => $audience === 'contactp' ? ($c['id'] ?? null) : null,
    //                             'created_at'      => now(),
    //                             'updated_at'      => now(),
    //                         ];
    //                     }
    //                     SmsCampaignResponse::insert($saveData); // ⚡ bulk insert for speed
    //                 } else {
    //                     $errorBody = $response->body();
    //                     $saveData = [];
    //                     foreach ($batch as $c) {
    //                         $saveData[] = [
    //                             'sms_campaign_id' => $campaignId,
    //                             'message_status'  => 'failed',
    //                             'message_text'    => Str::limit('API Error: '.$errorBody, 1000),
    //                             'mobile_no'       => $c['mobile'],
    //                             'name'            => $c['name'] ?? null,
    //                             'main_response'   => $errorBody,
    //                             'error_data_field'=> 'API response error',
    //                             'created_at'      => now(),
    //                             'updated_at'      => now(),
    //                         ];
    //                     }
    //                     SmsCampaignResponse::insert($saveData);

    //                     \Log::error('SMS API Error', [
    //                         'payload' => $payload,
    //                         'response' => $errorBody,
    //                     ]);
    //                 }
    //             } catch (\Throwable $e) {
    //                 $saveData = [];
    //                 foreach ($batch as $c) {
    //                     $saveData[] = [
    //                         'sms_campaign_id' => $campaignId,
    //                         'message_status'  => 'failed',
    //                         'message_text'    => Str::limit('Exception: '.$e->getMessage(), 1000),
    //                         'mobile_no'       => $c['mobile'],
    //                         'name'            => $c['name'] ?? null,
    //                         'main_response'   => null,
    //                         'error_data_field'=> 'Exception while sending',
    //                         'created_at'      => now(),
    //                         'updated_at'      => now(),
    //                     ];
    //                 }
    //                 SmsCampaignResponse::insert($saveData);

    //                 \Log::error('SMS Batch Exception', [
    //                     'error'   => $e->getMessage(),
    //                     'payload' => $payload,
    //                 ]);
    //             }
    //         }
    //     } catch (\Throwable $ex) {
    //         \Log::error('sendSmsViaApi() Exception', [
    //             'error' => $ex->getMessage(),
    //             'trace' => $ex->getTraceAsString(),
    //         ]);
    //     }

    //     return ['totalSent' => $totalSent];
    // }
