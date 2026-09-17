<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Todo;
use App\Models\Metawhatsappapi;
use App\Models\Admin;
use App\Models\Todoreminderresponse;
use App\Mail\TodoReminderMail;
use App\Models\Whatsappapi;
use Illuminate\Support\Facades\Mail;

class TodoReminderMedium extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:todoremindermedium';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // Send Mail and Whatsapp on High Priority
        $todayDate = date('Y-m-d');

        // Get Data for send Reminder based collecting data
        $todoLists1 = Todo::with('admin')->where('start_on','<=',$todayDate)
            ->where('task_status','!=','New Task')
            ->where('reminder_cycle','=','Medium')
            ->whereNull('scheduled_date_time')
            ->where('finish_on','>=',$todayDate)
            ->where('type','!=','Always')
            ->where('is_completed',false)
            ->where('scheduled_status','=',false)
            ->get();
        $todoLists2 = Todo::with('admin')->where('start_on','<=',$todayDate)
            ->where('task_status','!=','New Task')
            ->where('reminder_cycle','=','Medium')
            ->whereNull('scheduled_date_time')
            ->whereNull('finish_on')
            ->where('type','!=','Always')
            ->where('is_completed',false)
            ->where('scheduled_status','=',false)
            ->get();

        $todoLists3 = Todo::with('admin')->where('start_on','<=',$todayDate)
            ->where('task_status','!=','New Task')
            ->where('reminder_cycle','=','Medium')
            ->whereNull('scheduled_date_time')
            ->where('type','=','Always')
            ->where('scheduled_status',false)
            ->get();

        // If Type is not Recruiting, Send message when finish on selected or is_completed status true

        // $metaAPI = Metawhatsappapi::where('status','=',1)->first();
        $metaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['todo_notification'])->first();
        $normalAPI = Whatsappapi::where('status','=',1)->first();

        // If Type is not Recruiting, Send message when finish on selected or is_completed status true

        if ($todoLists1->count() > 0) {
            foreach ($todoLists1 as $todoList1) {
                // remove \n\t from text description
                $final_desc = preg_replace('/[\n\t]+/', ' ', $todoList1->task_description);
                // Get Users Details
                $todoUsers = Admin::wherein('id',explode(",",$todoList1->assignto_id))->where('status',1)->get();
                $todo_assigned_by = $todoList1->admin->name ?? 'N/A';
                // Get Meta API
                foreach ($todoUsers as $todoUser) {
                    // Send Whatsapp through Normal Whatsapp
                    if (isset($normalAPI)) {
                        // Get API Data
                        $normal_api_endpoint = $normalAPI['api_url'];
                        $normal_instance_id = $normalAPI['instance_id'];
                        $normal_access_token = $normalAPI['access_token'];

                        $data_send = [
                            "number" => $todoUser->phone,
                            "type" => "text",
                            "message" =>  $todoList1->task_description,
                            "instance_id" => $normal_instance_id,
                            "access_token" => $normal_access_token
                        ];

                        $curl = curl_init();

                        curl_setopt_array($curl,[
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json','Accept: application/json'],
                            CURLOPT_URL => $normal_api_endpoint,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data_send),
                        ]);

                        $response = curl_exec($curl);

                        curl_close($curl);
                        $responseGet = json_decode($response);



                        if (isset($responseGet)) {
                            if ($responseGet->status == 'success') {
                                // Upload Whatsapp Response
                                $respost = new Todoreminderresponse();
                                $respost->todo_id = $todoList1->id;
                                $respost->response_for = "Whatsapp Reminder";
                                $respost->name = $todoUser->name;
                                $respost->mobile_no = $todoUser->phone;
                                $respost->message_status = $responseGet->result;
                                $respost->message_text = "Message are processed to be sent!";
                                $respost->save();
                            } else {
                                // Send Whatsapp through Meta WHatsapp
                                if (isset($metaAPI)) {
                                    // API Detai
                                    $base_url = $metaAPI->api_base_url;
                                    $vendor_id = $metaAPI->vendor_uid;
                                    $access_token = $metaAPI->api_access_token;
                                    $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                                    $token = "Authorization: Bearer ".$access_token;

                                    // Meta Whatsapp Data
                                    $data = [];
                                    // $data['template_name'] = "reminder_staff_1";
                                    $data['template_name'] = "reminder_2";
                                    $data['template_language'] = "en";
                                    $data['phone_number'] = $todoUser->phone;
                                    $data['field_1'] = $todoUser->name;
                                    $data['field_2'] = $todoList1->task_title;

                                    if ($todoList1->task_description != '') {
                                        // $data['field_3'] = urlencode($todoList1->task_description);
                                        // $data['field_3'] = $todoList1->task_description;
                                        $data['field_3'] = $final_desc;

                                    } else {
                                        $data['field_3'] = "Description Not Available";
                                    }

                                    if ($todoList1->finish_on != '') {
                                        $data['field_4'] = date('d-m-Y',strtotime($todoList1->finish_on));
                                    } else {
                                        $data['field_4'] = "Due Date Not Available!";
                                    }
                                    $data['field_5'] = $todo_assigned_by;
                                    $data['button_0'] = "admin/todo?open_todo_model=true&todo_model_task_id=".$todoList1->id;

                                    $data['contact'] =  [
                                        'first_name' => $todoUser->phone,
                                        'last_name' => "--",
                                        "email" => $todoUser->email,
                                        "country" => "India",
                                        "language_code" => "en"
                                    ];

                                    $curl = curl_init();
                                    curl_setopt_array($curl, array(
                                        CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                        CURLOPT_URL => $endpoint_api,
                                        CURLOPT_RETURNTRANSFER => true,
                                        CURLOPT_ENCODING => '',
                                        CURLOPT_MAXREDIRS => 10,
                                        CURLOPT_TIMEOUT => 0,
                                        CURLOPT_FOLLOWLOCATION => true,
                                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                        CURLOPT_CUSTOMREQUEST => 'POST',
                                        CURLOPT_POSTFIELDS => json_encode($data)
                                    ));
                                    $response = curl_exec($curl);
                                    curl_close($curl);
                                    $responseGet = json_decode($response);

                                    // Upload Whatsapp Response
                                    $respost = new Todoreminderresponse();
                                    $respost->todo_id = $todoList1->id;
                                    $respost->response_for = "Whatsapp Reminder";
                                    $respost->name = $todoUser->name;
                                    $respost->mobile_no = $todoUser->phone;
                                    if (isset($responseGet->result)){
                                        $respost->message_status = $responseGet->result;
                                        $respost->message_text = $responseGet->message;
                                    }else{
                                        $respost->message_status = "Failed";
                                        $respost->message_text = $responseGet->message;
                                        $respost->error_data_field = json_encode($responseGet->errors);
                                    }
                                    $respost->save();


                                } else {
                                    // Upload Whatsapp Response
                                    $respost = new Todoreminderresponse();
                                    $respost->todo_id = $todoList1->id;
                                    $respost->response_for = "Whatsapp Reminder";
                                    $respost->name = $todoUser->name;
                                    $respost->mobile_no = $todoUser->phone;
                                    $respost->message_status = "Failed";
                                    $respost->message_text = "Whatsapp API Not Connected!";
                                    $respost->save();
                                }

                            }

                        }else{
                            // Send Whatsapp through Meta WHatsapp
                            if (isset($metaAPI)) {
                                // API Detai
                                $base_url = $metaAPI->api_base_url;
                                $vendor_id = $metaAPI->vendor_uid;
                                $access_token = $metaAPI->api_access_token;
                                $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                                $token = "Authorization: Bearer ".$access_token;

                                // Meta Whatsapp Data
                                $data = [];
                                // $data['template_name'] = "reminder_staff_1";
                                $data['template_name'] = "reminder_2";
                                $data['template_language'] = "en";
                                $data['phone_number'] = $todoUser->phone;
                                $data['field_1'] = $todoUser->name;
                                $data['field_2'] = $todoList1->task_title;

                                if ($todoList1->task_description != '') {
                                    // $data['field_3'] = urlencode($todoList1->task_description);
                                    // $data['field_3'] = $todoList1->task_description;
                                    $data['field_3'] = $final_desc;

                                } else {
                                    $data['field_3'] = "Description Not Available";
                                }

                                if ($todoList1->finish_on != '') {
                                    $data['field_4'] = date('d-m-Y',strtotime($todoList1->finish_on));
                                } else {
                                    $data['field_4'] = "Due Date Not Available!";
                                }
                                $data['field_5'] = $todo_assigned_by;
                                $data['button_0'] = "admin/todo?open_todo_model=true&todo_model_task_id=".$todoList1->id;

                                $data['contact'] =  [
                                    'first_name' => $todoUser->phone,
                                    'last_name' => "--",
                                    "email" => $todoUser->email,
                                    "country" => "India",
                                    "language_code" => "en"
                                ];

                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                    CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                    CURLOPT_URL => $endpoint_api,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS => json_encode($data)
                                ));
                                $response = curl_exec($curl);
                                curl_close($curl);
                                $responseGet = json_decode($response);

                                // Upload Whatsapp Response
                                $respost = new Todoreminderresponse();
                                $respost->todo_id = $todoList1->id;
                                $respost->response_for = "Whatsapp Reminder";
                                $respost->name = $todoUser->name;
                                $respost->mobile_no = $todoUser->phone;
                                if (isset($responseGet->result)){
                                    $respost->message_status = $responseGet->result;
                                    $respost->message_text = $responseGet->message;
                                }else{
                                    $respost->message_status = "Failed";
                                    $respost->message_text = $responseGet->message;
                                    $respost->error_data_field = json_encode($responseGet->errors);
                                }
                                $respost->save();


                            } else {
                                // Upload Whatsapp Response
                                $respost = new Todoreminderresponse();
                                $respost->todo_id = $todoList1->id;
                                $respost->response_for = "Whatsapp Reminder";
                                $respost->name = $todoUser->name;
                                $respost->mobile_no = $todoUser->phone;
                                $respost->message_status = "Failed";
                                $respost->message_text = "Whatsapp API Not Connected!";
                                $respost->save();
                            }
                        }



                    }elseif (isset($metaAPI)) {
                        // API Detai
                        $base_url = $metaAPI->api_base_url;
                        $vendor_id = $metaAPI->vendor_uid;
                        $access_token = $metaAPI->api_access_token;
                        $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                        $token = "Authorization: Bearer ".$access_token;

                        // Meta Whatsapp Data
                        $data = [];
                        // $data['template_name'] = "reminder_staff_1";
                        $data['template_name'] = "reminder_2";
                        $data['template_language'] = "en";
                        $data['phone_number'] = $todoUser->phone;
                        $data['field_1'] = $todoUser->name;
                        $data['field_2'] = $todoList1->task_title;

                        if ($todoList1->task_description != '') {
                            // $data['field_3'] = urlencode($todoList1->task_description);
                            // $data['field_3'] = $todoList1->task_description;
                            $data['field_3'] = $final_desc;

                        } else {
                            $data['field_3'] = "Description Not Available";
                        }

                        if ($todoList1->finish_on != '') {
                            $data['field_4'] = date('d-m-Y',strtotime($todoList1->finish_on));
                        } else {
                            $data['field_4'] = "Due Date Not Available!";
                        }
                        $data['field_5'] = $todo_assigned_by;
                        $data['button_0'] = "admin/todo?open_todo_model=true&todo_model_task_id=".$todoList1->id;

                        $data['contact'] =  [
                            'first_name' => $todoUser->phone,
                            'last_name' => "--",
                            "email" => $todoUser->email,
                            "country" => "India",
                            "language_code" => "en"
                        ];

                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                            CURLOPT_URL => $endpoint_api,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data)
                        ));
                        $response = curl_exec($curl);
                        curl_close($curl);
                        $responseGet = json_decode($response);

                        // Upload Whatsapp Response
                        $respost = new Todoreminderresponse();
                        $respost->todo_id = $todoList1->id;
                        $respost->response_for = "Whatsapp Reminder";
                        $respost->name = $todoUser->name;
                        $respost->mobile_no = $todoUser->phone;
                        if (isset($responseGet->result)){
                            $respost->message_status = $responseGet->result;
                            $respost->message_text = $responseGet->message;
                        }else{
                            $respost->message_status = "Failed";
                            $respost->message_text = $responseGet->message;
                            $respost->error_data_field = json_encode($responseGet->errors);
                        }
                        $respost->save();

                    }else{
                        // Upload Whatsapp Response
                        $respost = new Todoreminderresponse();
                        $respost->todo_id = $todoList1->id;
                        $respost->response_for = "Whatsapp Reminder";
                        $respost->name = $todoUser->name;
                        $respost->mobile_no = $todoUser->phone;
                        $respost->message_status = "Failed";
                        $respost->message_text = "Whatsapp API Not Connected!";
                        $respost->save();
                    }


                    // Send Mail
                    if ($todoUser->working_email != '') {
                        if ($todoList1->finish_on != '') {
                            $finish_date = date('d-m-Y',strtotime($todoList1->finish_on));
                        } else {
                            $finish_date = "Due Date Not Available!";
                        }

                        $msg = "Dear ".$todoUser->name."\n\nYour task ".$todoList1->task_title."\n\nDue date ".$finish_date."\n\nQamr International Hr consultancy\n\nDon`t ignore this reminder";
                        $mailData =  [
                            'email' => $todoUser->working_email,
                            'body_msg' => $msg
                        ];

                        try {
                            Mail::to($todoUser->working_email)->send(new TodoReminderMail($mailData));
                            // Upload Whatsapp Response
                            $respost = new Todoreminderresponse();
                            $respost->todo_id = $todoList1->id;
                            $respost->response_for = "Email Reminder";
                            $respost->name = $todoUser->name;
                            $respost->email = $todoUser->working_email;
                            $respost->message_status = "success";
                            $respost->message_text = "Email sent successfully";
                            $respost->save();
                        } catch (\Throwable $exception) {
                            // Upload Whatsapp Response
                            $respost = new Todoreminderresponse();
                            $respost->todo_id = $todoList1->id;
                            $respost->response_for = "Email Reminder";
                            $respost->name = $todoUser->name;
                            $respost->email = $todoUser->working_email;
                            $respost->message_status = "Failed";
                            $respost->message_text = "Email not send!";
                            $respost->save();
                        }



                    } else {
                        // Upload Whatsapp Response
                        $respost = new Todoreminderresponse();
                        $respost->todo_id = $todoList1->id;
                        $respost->response_for = "Email Reminder";
                        $respost->name = $todoUser->name;
                        $respost->email = $todoUser->working_email;
                        $respost->message_status = "Failed";
                        $respost->message_text = "Email is required!";
                        $respost->save();
                    }


                }

            }
        }

        if ($todoLists2->count() > 0) {
            foreach ($todoLists2 as $todoList2) {
                // remove \n\t from text description
                $final_desc = preg_replace('/[\n\t]+/', ' ', $todoList2->task_description);
                // Get Users Details
                $todoUsers = Admin::wherein('id',explode(",",$todoList2->assignto_id))->where('status',1)->get();
                $todo_assigned_by = $todoList2->admin->name ?? 'N/A';

                foreach ($todoUsers as $todoUser) {
                    // Send Whatsapp
                    if (isset($normalAPI)) {
                        // Get API Data
                        $normal_api_endpoint = $normalAPI['api_url'];
                        $normal_instance_id = $normalAPI['instance_id'];
                        $normal_access_token = $normalAPI['access_token'];

                        $data_send = [
                            "number" => $todoUser->phone,
                            "type" => "text",
                            "message" =>  $todoList2->task_description,
                            "instance_id" => $normal_instance_id,
                            "access_token" => $normal_access_token
                        ];

                        $curl = curl_init();

                        curl_setopt_array($curl,[
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json','Accept: application/json'],
                            CURLOPT_URL => $normal_api_endpoint,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data_send),
                        ]);

                        $response = curl_exec($curl);

                        curl_close($curl);
                        $responseGet = json_decode($response);



                        if (isset($responseGet)) {
                            if ($responseGet->status == 'success') {
                                // Upload Whatsapp Response
                                $respost = new Todoreminderresponse();
                                $respost->todo_id = $todoList2->id;
                                $respost->response_for = "Whatsapp Reminder";
                                $respost->name = $todoUser->name;
                                $respost->mobile_no = $todoUser->phone;
                                $respost->message_status = $responseGet->result;
                                $respost->message_text = "Message are processed to be sent!";
                                $respost->save();
                            } else {
                                // Send Whatsapp through Meta WHatsapp
                                if (isset($metaAPI)) {
                                    // API Detai
                                    $base_url = $metaAPI->api_base_url;
                                    $vendor_id = $metaAPI->vendor_uid;
                                    $access_token = $metaAPI->api_access_token;
                                    $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                                    $token = "Authorization: Bearer ".$access_token;

                                    // Meta Whatsapp Data
                                    $data = [];
                                    // $data['template_name'] = "reminder_staff_1";
                                    $data['template_name'] = "reminder_2";
                                    $data['template_language'] = "en";
                                    $data['phone_number'] = $todoUser->phone;
                                    $data['field_1'] = $todoUser->name;
                                    $data['field_2'] = $todoList2->task_title;

                                    if ($todoList2->task_description != '') {
                                        // $data['field_3'] = urlencode($todoList2->task_description);
                                        // $data['field_3'] = $todoList2->task_description;
                                        $data['field_3'] = $final_desc;

                                    } else {
                                        $data['field_3'] = "Description Not Available";
                                    }

                                    if ($todoList2->finish_on != '') {
                                        $data['field_4'] = date('d-m-Y',strtotime($todoList2->finish_on));
                                    } else {
                                        $data['field_4'] = "Due Date Not Available!";
                                    }
                                    $data['field_5'] = $todo_assigned_by;
                                    $data['button_0'] = "admin/todo?open_todo_model=true&todo_model_task_id=".$todoList2->id;

                                    $data['contact'] =  [
                                        'first_name' => $todoUser->phone,
                                        'last_name' => "--",
                                        "email" => $todoUser->email,
                                        "country" => "India",
                                        "language_code" => "en"
                                    ];

                                    $curl = curl_init();
                                    curl_setopt_array($curl, array(
                                        CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                        CURLOPT_URL => $endpoint_api,
                                        CURLOPT_RETURNTRANSFER => true,
                                        CURLOPT_ENCODING => '',
                                        CURLOPT_MAXREDIRS => 10,
                                        CURLOPT_TIMEOUT => 0,
                                        CURLOPT_FOLLOWLOCATION => true,
                                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                        CURLOPT_CUSTOMREQUEST => 'POST',
                                        CURLOPT_POSTFIELDS => json_encode($data)
                                    ));
                                    $response = curl_exec($curl);
                                    curl_close($curl);
                                    $responseGet = json_decode($response);

                                    // Upload Whatsapp Response
                                    $respost = new Todoreminderresponse();
                                    $respost->todo_id = $todoList2->id;
                                    $respost->response_for = "Whatsapp Reminder";
                                    $respost->name = $todoUser->name;
                                    $respost->mobile_no = $todoUser->phone;
                                    if (isset($responseGet->result)){
                                        $respost->message_status = $responseGet->result;
                                        $respost->message_text = $responseGet->message;
                                    }else{
                                        $respost->message_status = "Failed";
                                        $respost->message_text = $responseGet->message;
                                        $respost->error_data_field = json_encode($responseGet->errors);
                                    }
                                    $respost->save();


                                } else {
                                    // Upload Whatsapp Response
                                    $respost = new Todoreminderresponse();
                                    $respost->todo_id = $todoList2->id;
                                    $respost->response_for = "Whatsapp Reminder";
                                    $respost->name = $todoUser->name;
                                    $respost->mobile_no = $todoUser->phone;
                                    $respost->message_status = "Failed";
                                    $respost->message_text = "Whatsapp API Not Connected!";
                                    $respost->save();
                                }

                            }

                        }else{
                            // Send Whatsapp through Meta WHatsapp
                            if (isset($metaAPI)) {
                                // API Detai
                                $base_url = $metaAPI->api_base_url;
                                $vendor_id = $metaAPI->vendor_uid;
                                $access_token = $metaAPI->api_access_token;
                                $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                                $token = "Authorization: Bearer ".$access_token;

                                // Meta Whatsapp Data
                                $data = [];
                                // $data['template_name'] = "reminder_staff_1";
                                $data['template_name'] = "reminder_2";
                                $data['template_language'] = "en";
                                $data['phone_number'] = $todoUser->phone;
                                $data['field_1'] = $todoUser->name;
                                $data['field_2'] = $todoList2->task_title;

                                if ($todoList2->task_description != '') {
                                    // $data['field_3'] = urlencode($todoList2->task_description);
                                    // $data['field_3'] = $todoList2->task_description;
                                    $data['field_3'] = $final_desc;

                                } else {
                                    $data['field_3'] = "Description Not Available";
                                }

                                if ($todoList2->finish_on != '') {
                                    $data['field_4'] = date('d-m-Y',strtotime($todoList2->finish_on));
                                } else {
                                    $data['field_4'] = "Due Date Not Available!";
                                }
                                $data['field_5'] = $todo_assigned_by;
                                $data['button_0'] = "admin/todo?open_todo_model=true&todo_model_task_id=".$todoList2->id;

                                $data['contact'] =  [
                                    'first_name' => $todoUser->phone,
                                    'last_name' => "--",
                                    "email" => $todoUser->email,
                                    "country" => "India",
                                    "language_code" => "en"
                                ];

                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                    CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                    CURLOPT_URL => $endpoint_api,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS => json_encode($data)
                                ));
                                $response = curl_exec($curl);
                                curl_close($curl);
                                $responseGet = json_decode($response);

                                // Upload Whatsapp Response
                                $respost = new Todoreminderresponse();
                                $respost->todo_id = $todoList2->id;
                                $respost->response_for = "Whatsapp Reminder";
                                $respost->name = $todoUser->name;
                                $respost->mobile_no = $todoUser->phone;
                                if (isset($responseGet->result)){
                                    $respost->message_status = $responseGet->result;
                                    $respost->message_text = $responseGet->message;
                                }else{
                                    $respost->message_status = "Failed";
                                    $respost->message_text = $responseGet->message;
                                    $respost->error_data_field = json_encode($responseGet->errors);
                                }
                                $respost->save();


                            } else {
                                // Upload Whatsapp Response
                                $respost = new Todoreminderresponse();
                                $respost->todo_id = $todoList2->id;
                                $respost->response_for = "Whatsapp Reminder";
                                $respost->name = $todoUser->name;
                                $respost->mobile_no = $todoUser->phone;
                                $respost->message_status = "Failed";
                                $respost->message_text = "Whatsapp API Not Connected!";
                                $respost->save();
                            }
                        }



                    }elseif (isset($metaAPI)) {
                        // API Detai
                        $base_url = $metaAPI->api_base_url;
                        $vendor_id = $metaAPI->vendor_uid;
                        $access_token = $metaAPI->api_access_token;
                        $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                        $token = "Authorization: Bearer ".$access_token;

                        // Meta Whatsapp Data
                        $data = [];
                        // $data['template_name'] = "reminder_staff_1";
                        $data['template_name'] = "reminder_2";
                        $data['template_language'] = "en";
                        $data['phone_number'] = $todoUser->phone;
                        $data['field_1'] = $todoUser->name;
                        $data['field_2'] = $todoList2->task_title;

                        if ($todoList2->task_description != '') {
                            // $data['field_3'] = urlencode($todoList2->task_description);
                            // $data['field_3'] = $todoList2->task_description;
                            $data['field_3'] = $final_desc;

                        } else {
                            $data['field_3'] = "Description Not Available";
                        }

                        if ($todoList2->finish_on != '') {
                            $data['field_4'] = date('d-m-Y',strtotime($todoList2->finish_on));
                        } else {
                            $data['field_4'] = "Due Date Not Available!";
                        }
                        $data['field_5'] = $todo_assigned_by;
                        $data['button_0'] = "admin/todo?open_todo_model=true&todo_model_task_id=".$todoList2->id;

                        $data['contact'] =  [
                            'first_name' => $todoUser->phone,
                            'last_name' => "--",
                            "email" => $todoUser->email,
                            "country" => "India",
                            "language_code" => "en"
                        ];

                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                            CURLOPT_URL => $endpoint_api,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data)
                        ));
                        $response = curl_exec($curl);
                        curl_close($curl);
                        $responseGet = json_decode($response);

                        // Upload Whatsapp Response
                        $respost = new Todoreminderresponse();
                        $respost->todo_id = $todoList2->id;
                        $respost->response_for = "Whatsapp Reminder";
                        $respost->name = $todoUser->name;
                        $respost->mobile_no = $todoUser->phone;
                        if (isset($responseGet->result)){
                            $respost->message_status = $responseGet->result;
                            $respost->message_text = $responseGet->message;
                        }else{
                            $respost->message_status = "Failed";
                            $respost->message_text = $responseGet->message;
                            $respost->error_data_field = json_encode($responseGet->errors);
                        }
                        $respost->save();

                    }else{
                        // Upload Whatsapp Response
                        $respost = new Todoreminderresponse();
                        $respost->todo_id = $todoList2->id;
                        $respost->response_for = "Whatsapp Reminder";
                        $respost->name = $todoUser->name;
                        $respost->mobile_no = $todoUser->phone;
                        $respost->message_status = "Failed";
                        $respost->message_text = "Whatsapp API Not Connected!";
                        $respost->save();
                    }



                    // Send Mail
                    if ($todoUser->working_email != '') {
                        if ($todoList2->finish_on != '') {
                            $finish_date = date('d-m-Y',strtotime($todoList2->finish_on));
                        } else {
                            $finish_date = "Due Date Not Available!";
                        }

                        $msg = "Dear ".$todoUser->name."\n\nYour task ".$todoList2->task_title."\n\nDue date ".$finish_date."\n\nQamr International Hr consultancy\n\nDon`t ignore this reminder";
                        $mailData =  [
                            'email' => $todoUser->working_email,
                            'body_msg' => $msg
                        ];

                        try {
                            Mail::to($todoUser->working_email)->send(new TodoReminderMail($mailData));
                            // Upload Whatsapp Response
                            $respost = new Todoreminderresponse();
                            $respost->todo_id = $todoList2->id;
                            $respost->response_for = "Email Reminder";
                            $respost->name = $todoUser->name;
                            $respost->email = $todoUser->working_email;
                            $respost->message_status = "success";
                            $respost->message_text = "Email sent successfully";
                            $respost->save();
                        } catch (\Throwable $exception) {
                            // Upload Whatsapp Response
                            $respost = new Todoreminderresponse();
                            $respost->todo_id = $todoList2->id;
                            $respost->response_for = "Email Reminder";
                            $respost->name = $todoUser->name;
                            $respost->email = $todoUser->working_email;
                            $respost->message_status = "Failed";
                            $respost->message_text = "Email not send!";
                            $respost->save();
                        }



                    } else {
                        // Upload Whatsapp Response
                        $respost = new Todoreminderresponse();
                        $respost->todo_id = $todoList2->id;
                        $respost->response_for = "Email Reminder";
                        $respost->name = $todoUser->name;
                        $respost->email = $todoUser->working_email;
                        $respost->message_status = "Failed";
                        $respost->message_text = "Email is required!";
                        $respost->save();
                    }


                }

            }
        }

        // If Type is Recruiting
        if ($todoLists3->count() > 0) {
            foreach ($todoLists3 as $todoList3) {
                // remove \n\t from text description
                $final_desc = preg_replace('/[\n\t]+/', ' ', $todoList3->task_description);
                // Get Users Details
                $todoUsers = Admin::wherein('id',explode(",",$todoList3->assignto_id))->where('status',1)->get();
                $todo_assigned_by = $todoList3->admin->name ?? 'N/A';

                foreach ($todoUsers as $todoUser) {
                    // Send Whatsapp
                    // Send Whatsapp through Normal Whatsapp
                    if (isset($normalAPI)) {
                        // Get API Data
                        $normal_api_endpoint = $normalAPI['api_url'];
                        $normal_instance_id = $normalAPI['instance_id'];
                        $normal_access_token = $normalAPI['access_token'];

                        $data_send = [
                            "number" => $todoUser->phone,
                            "type" => "text",
                            "message" =>  $todoList3->task_description,
                            "instance_id" => $normal_instance_id,
                            "access_token" => $normal_access_token
                        ];

                        $curl = curl_init();

                        curl_setopt_array($curl,[
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json','Accept: application/json'],
                            CURLOPT_URL => $normal_api_endpoint,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data_send),
                        ]);

                        $response = curl_exec($curl);

                        curl_close($curl);
                        $responseGet = json_decode($response);



                        if (isset($responseGet)) {
                            if ($responseGet->status == 'success') {
                                // Upload Whatsapp Response
                                $respost = new Todoreminderresponse();
                                $respost->todo_id = $todoList3->id;
                                $respost->response_for = "Whatsapp Reminder";
                                $respost->name = $todoUser->name;
                                $respost->mobile_no = $todoUser->phone;
                                $respost->message_status = $responseGet->result;
                                $respost->message_text = "Message are processed to be sent!";
                                $respost->save();
                            } else {
                                // Send Whatsapp through Meta WHatsapp
                                if (isset($metaAPI)) {
                                    // API Detai
                                    $base_url = $metaAPI->api_base_url;
                                    $vendor_id = $metaAPI->vendor_uid;
                                    $access_token = $metaAPI->api_access_token;
                                    $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                                    $token = "Authorization: Bearer ".$access_token;

                                    // Meta Whatsapp Data
                                    $data = [];
                                    // $data['template_name'] = "reminder_staff_1";
                                    $data['template_name'] = "reminder_2";
                                    $data['template_language'] = "en";
                                    $data['phone_number'] = $todoUser->phone;
                                    $data['field_1'] = $todoUser->name;
                                    $data['field_2'] = $todoList3->task_title;

                                    if ($todoList3->task_description != '') {
                                        // $data['field_3'] = urlencode($todoList3->task_description);
                                        // $data['field_3'] = $todoList3->task_description;
                                        $data['field_3'] = $final_desc;

                                    } else {
                                        $data['field_3'] = "Description Not Available";
                                    }

                                    if ($todoList3->finish_on != '') {
                                        $data['field_4'] = date('d-m-Y',strtotime($todoList3->finish_on));
                                    } else {
                                        $data['field_4'] = "Due Date Not Available!";
                                    }
                                    $data['field_5'] = $todo_assigned_by;
                                    $data['button_0'] = "admin/todo?open_todo_model=true&todo_model_task_id=".$todoList3->id;

                                    $data['contact'] =  [
                                        'first_name' => $todoUser->phone,
                                        'last_name' => "--",
                                        "email" => $todoUser->email,
                                        "country" => "India",
                                        "language_code" => "en"
                                    ];

                                    $curl = curl_init();
                                    curl_setopt_array($curl, array(
                                        CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                        CURLOPT_URL => $endpoint_api,
                                        CURLOPT_RETURNTRANSFER => true,
                                        CURLOPT_ENCODING => '',
                                        CURLOPT_MAXREDIRS => 10,
                                        CURLOPT_TIMEOUT => 0,
                                        CURLOPT_FOLLOWLOCATION => true,
                                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                        CURLOPT_CUSTOMREQUEST => 'POST',
                                        CURLOPT_POSTFIELDS => json_encode($data)
                                    ));
                                    $response = curl_exec($curl);
                                    curl_close($curl);
                                    $responseGet = json_decode($response);

                                    // Upload Whatsapp Response
                                    $respost = new Todoreminderresponse();
                                    $respost->todo_id = $todoList3->id;
                                    $respost->response_for = "Whatsapp Reminder";
                                    $respost->name = $todoUser->name;
                                    $respost->mobile_no = $todoUser->phone;
                                    if (isset($responseGet->result)){
                                        $respost->message_status = $responseGet->result;
                                        $respost->message_text = $responseGet->message;
                                    }else{
                                        $respost->message_status = "Failed";
                                        $respost->message_text = $responseGet->message;
                                        $respost->error_data_field = json_encode($responseGet->errors);
                                    }
                                    $respost->save();


                                } else {
                                    // Upload Whatsapp Response
                                    $respost = new Todoreminderresponse();
                                    $respost->todo_id = $todoList3->id;
                                    $respost->response_for = "Whatsapp Reminder";
                                    $respost->name = $todoUser->name;
                                    $respost->mobile_no = $todoUser->phone;
                                    $respost->message_status = "Failed";
                                    $respost->message_text = "Whatsapp API Not Connected!";
                                    $respost->save();
                                }

                            }

                        }else{
                            // Send Whatsapp through Meta WHatsapp
                            if (isset($metaAPI)) {
                                // API Detai
                                $base_url = $metaAPI->api_base_url;
                                $vendor_id = $metaAPI->vendor_uid;
                                $access_token = $metaAPI->api_access_token;
                                $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                                $token = "Authorization: Bearer ".$access_token;

                                // Meta Whatsapp Data
                                $data = [];
                                // $data['template_name'] = "reminder_staff_1";
                                $data['template_name'] = "reminder_2";
                                $data['template_language'] = "en";
                                $data['phone_number'] = $todoUser->phone;
                                $data['field_1'] = $todoUser->name;
                                $data['field_2'] = $todoList3->task_title;

                                if ($todoList3->task_description != '') {
                                    // $data['field_3'] = urlencode($todoList3->task_description);
                                    // $data['field_3'] = $todoList3->task_description;
                                    $data['field_3'] = $final_desc;

                                } else {
                                    $data['field_3'] = "Description Not Available";
                                }

                                if ($todoList3->finish_on != '') {
                                    $data['field_4'] = date('d-m-Y',strtotime($todoList3->finish_on));
                                } else {
                                    $data['field_4'] = "Due Date Not Available!";
                                }
                                $data['field_5'] = $todo_assigned_by;
                                $data['button_0'] = "admin/todo?open_todo_model=true&todo_model_task_id=".$todoList3->id;

                                $data['contact'] =  [
                                    'first_name' => $todoUser->phone,
                                    'last_name' => "--",
                                    "email" => $todoUser->email,
                                    "country" => "India",
                                    "language_code" => "en"
                                ];

                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                    CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                    CURLOPT_URL => $endpoint_api,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS => json_encode($data)
                                ));
                                $response = curl_exec($curl);
                                curl_close($curl);
                                $responseGet = json_decode($response);

                                // Upload Whatsapp Response
                                $respost = new Todoreminderresponse();
                                $respost->todo_id = $todoList3->id;
                                $respost->response_for = "Whatsapp Reminder";
                                $respost->name = $todoUser->name;
                                $respost->mobile_no = $todoUser->phone;
                                if (isset($responseGet->result)){
                                    $respost->message_status = $responseGet->result;
                                    $respost->message_text = $responseGet->message;
                                }else{
                                    $respost->message_status = "Failed";
                                    $respost->message_text = $responseGet->message;
                                    $respost->error_data_field = json_encode($responseGet->errors);
                                }
                                $respost->save();


                            } else {
                                // Upload Whatsapp Response
                                $respost = new Todoreminderresponse();
                                $respost->todo_id = $todoList3->id;
                                $respost->response_for = "Whatsapp Reminder";
                                $respost->name = $todoUser->name;
                                $respost->mobile_no = $todoUser->phone;
                                $respost->message_status = "Failed";
                                $respost->message_text = "Whatsapp API Not Connected!";
                                $respost->save();
                            }
                        }



                    }elseif (isset($metaAPI)) {
                        // API Detai
                        $base_url = $metaAPI->api_base_url;
                        $vendor_id = $metaAPI->vendor_uid;
                        $access_token = $metaAPI->api_access_token;
                        $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                        $token = "Authorization: Bearer ".$access_token;

                        // Meta Whatsapp Data
                        $data = [];
                        // $data['template_name'] = "reminder_staff_1";
                        $data['template_name'] = "reminder_2";
                        $data['template_language'] = "en";
                        $data['phone_number'] = $todoUser->phone;
                        $data['field_1'] = $todoUser->name;
                        $data['field_2'] = $todoList3->task_title;

                        if ($todoList3->task_description != '') {
                            // $data['field_3'] = urlencode($todoList3->task_description);
                            // $data['field_3'] = $todoList3->task_description;
                            $data['field_3'] = $final_desc;

                        } else {
                            $data['field_3'] = "Description Not Available";
                        }

                        if ($todoList3->finish_on != '') {
                            $data['field_4'] = date('d-m-Y',strtotime($todoList3->finish_on));
                        } else {
                            $data['field_4'] = "Due Date Not Available!";
                        }
                        $data['field_5'] = $todo_assigned_by;
                        $data['button_0'] = "admin/todo?open_todo_model=true&todo_model_task_id=".$todoList3->id;

                        $data['contact'] =  [
                            'first_name' => $todoUser->phone,
                            'last_name' => "--",
                            "email" => $todoUser->email,
                            "country" => "India",
                            "language_code" => "en"
                        ];

                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                            CURLOPT_URL => $endpoint_api,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data)
                        ));
                        $response = curl_exec($curl);
                        curl_close($curl);
                        $responseGet = json_decode($response);

                        // Upload Whatsapp Response
                        $respost = new Todoreminderresponse();
                        $respost->todo_id = $todoList3->id;
                        $respost->response_for = "Whatsapp Reminder";
                        $respost->name = $todoUser->name;
                        $respost->mobile_no = $todoUser->phone;
                        if (isset($responseGet->result)){
                            $respost->message_status = $responseGet->result;
                            $respost->message_text = $responseGet->message;
                        }else{
                            $respost->message_status = "Failed";
                            $respost->message_text = $responseGet->message;
                            $respost->error_data_field = json_encode($responseGet->errors);
                        }
                        $respost->save();

                    }else{
                        // Upload Whatsapp Response
                        $respost = new Todoreminderresponse();
                        $respost->todo_id = $todoList3->id;
                        $respost->response_for = "Whatsapp Reminder";
                        $respost->name = $todoUser->name;
                        $respost->mobile_no = $todoUser->phone;
                        $respost->message_status = "Failed";
                        $respost->message_text = "Whatsapp API Not Connected!";
                        $respost->save();
                    }

                    // Send Mail
                    if ($todoUser->working_email != '') {
                        if ($todoList3->finish_on != '') {
                            $finish_date = date('d-m-Y',strtotime($todoList3->finish_on));
                        } else {
                            $finish_date = "Due Date Not Available!";
                        }

                        $msg = "Dear ".$todoUser->name."\n\nYour task ".$todoList3->task_title."\n\nDue date ".$finish_date."\n\nQamr International Hr consultancy\n\nDon`t ignore this reminder";
                        $mailData =  [
                            'email' => $todoUser->working_email,
                            'body_msg' => $msg
                        ];

                        try {
                            Mail::to($todoUser->email)->send(new TodoReminderMail($mailData));
                            // Upload Whatsapp Response
                            $respost = new Todoreminderresponse();
                            $respost->todo_id = $todoList3->id;
                            $respost->response_for = "Email Reminder";
                            $respost->name = $todoUser->name;
                            $respost->email = $todoUser->working_email;
                            $respost->message_status = "success";
                            $respost->message_text = "Email sent successfully";
                            $respost->save();
                        } catch (\Throwable $exception) {
                            // Upload Whatsapp Response
                            $respost = new Todoreminderresponse();
                            $respost->todo_id = $todoList3->id;
                            $respost->response_for = "Email Reminder";
                            $respost->name = $todoUser->name;
                            $respost->email = $todoUser->working_email;
                            $respost->message_status = "Failed";
                            $respost->message_text = "Email not send!";
                            $respost->save();
                        }



                    } else {
                        // Upload Whatsapp Response
                        $respost = new Todoreminderresponse();
                        $respost->todo_id = $todoList3->id;
                        $respost->response_for = "Email Reminder";
                        $respost->name = $todoUser->name;
                        $respost->email = $todoUser->working_email;
                        $respost->message_status = "Failed";
                        $respost->message_text = "Email is required!";
                        $respost->save();
                    }


                }

            }
        }



        return Command::SUCCESS;
    }
}
