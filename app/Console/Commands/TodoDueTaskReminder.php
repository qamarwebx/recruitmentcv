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

class TodoDueTaskReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:todoreminderdue';

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
        $todaydate = Date('Y-m-d');

        $todoLists = Todo::with('admin')->where('finish_on','<=',$todaydate)
            ->where('task_status','!=','New Task')
            ->where('is_completed',false)
            ->where('scheduled_status',false)
            ->where('type','!=','Always')
            ->get();


        // $metaAPI = Metawhatsappapi::where('status','=',1)->first();
        $metaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['todo_notification'])->first();
        $normalAPI = Whatsappapi::where('status','=',1)->first();


        if ($todoLists->count() > 0) {
            foreach ($todoLists as $todoList) {
                $task_title = "Your (".$todoList->task_title.") task is not marked as completed.";
                $task_desc = "Please mark as completed within 24 hours, otherwise your login system temporary inactive.";
                $task_duedate = $todoList->finish_on;

                $final_desc = $task_title.'\n'.$task_desc.'\n'.$task_duedate;

                // Get Users Details
                $todoUsers = Admin::wherein('id',explode(",",$todoList->assignto_id))->where('status',1)->get();
                $todo_assigned_by = $todoList->admin->name ?? 'N/A';

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
                            "message" =>  $final_desc,
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
                                $respost->todo_id = $todoList->id;
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
                                    $data['field_2'] = $task_title;
                                    $data['field_3'] = $task_desc;
                                    $data['field_4'] = $task_duedate;
                                    $data['field_5'] = $todo_assigned_by;
                                    $data['button_0'] = "admin/todo?open_todo_model=true&todo_model_task_id=".$todoList->id;

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
                                    $respost->todo_id = $todoList->id;
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
                                    $respost->todo_id = $todoList->id;
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
                                $data['field_2'] = $task_title;
                                $data['field_3'] = $task_desc;
                                $data['field_4'] = $task_duedate;
                                $data['field_5'] = $todo_assigned_by;
                                $data['button_0'] = "admin/todo?open_todo_model=true&todo_model_task_id=".$todoList->id;

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
                                $respost->todo_id = $todoList->id;
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
                                $respost->todo_id = $todoList->id;
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
                        $data['field_2'] = $task_title;
                        $data['field_3'] = $task_desc;
                        $data['field_4'] = $task_duedate;
                        $data['field_5'] = $todo_assigned_by;
                        $data['button_0'] = "admin/todo?open_todo_model=true&todo_model_task_id=".$todoList->id;

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
                        $respost->todo_id = $todoList->id;
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
                        $respost->todo_id = $todoList->id;
                        $respost->response_for = "Whatsapp Reminder";
                        $respost->name = $todoUser->name;
                        $respost->mobile_no = $todoUser->phone;
                        $respost->message_status = "Failed";
                        $respost->message_text = "Whatsapp API Not Connected!";
                        $respost->save();
                    }


                    // Send Mail
                    if ($todoUser->working_email != '') {
                        if ($todoList->finish_on != '') {
                            $finish_date = date('d-m-Y',strtotime($todoList->finish_on));
                        } else {
                            $finish_date = "Due Date Not Available!";
                        }

                        $msg = "Dear ".$todoUser->name."\n\nYour task ".$todoList->task_title."\n\nDue date ".$finish_date."\n\nQamr International Hr consultancy\n\nDon`t ignore this reminder";
                        $mailData =  [
                            'email' => $todoUser->working_email,
                            'body_msg' => $msg
                        ];

                        try {
                            Mail::to($todoUser->email)->send(new TodoReminderMail($mailData));
                            // Upload Whatsapp Response
                            $respost = new Todoreminderresponse();
                            $respost->todo_id = $todoList->id;
                            $respost->response_for = "Email Reminder";
                            $respost->name = $todoUser->name;
                            $respost->email = $todoUser->working_email;
                            $respost->message_status = "success";
                            $respost->message_text = "Email sent successfully";
                            $respost->save();
                        } catch (\Throwable $exception) {
                            // Upload Whatsapp Response
                            $respost = new Todoreminderresponse();
                            $respost->todo_id = $todoList->id;
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
                        $respost->todo_id = $todoList->id;
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
