<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Todo;
use App\Models\Metawhatsappapi;
use App\Models\Admin;
use App\Models\Todoreminderresponse;
use App\Mail\TodoReminderMail;
use Illuminate\Support\Facades\Mail;

class TodoReminderUrgent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:todoreminderurgent';

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
        $todoLists1 = Todo::where('start_on','<=',$todayDate)->where('task_status','!=','New Task')->where('reminder_cycle','=','Urgent')->whereNull('scheduled_time')->where('finish_on','>=',$todayDate)->where('type','!=','Always')->where('is_completed',false)->where('scheduled_status','=',false)->get();
        $todoLists2 = Todo::where('start_on','<=',$todayDate)->where('task_status','!=','New Task')->where('reminder_cycle','=','Urgent')->whereNull('scheduled_time')->whereNull('finish_on')->where('type','!=','Always')->where('is_completed',false)->where('scheduled_status','=',false)->get();

        $todoLists3 = Todo::where('start_on','<=',$todayDate)->where('task_status','!=','New Task')->where('reminder_cycle','=','Urgent')->whereNull('scheduled_time')->where('type','=','Always')->where('scheduled_status',false)->get();

        // If Type is not Recruiting, Send message when finish on selected or is_completed status true

        // If Type is Recruiting

        if ($todoLists1->count() > 0) {

            foreach ($todoLists1 as $todoList) {
                // remove \n\t from text description
                $final_desc = preg_replace('/[\n\t]+/', ' ', $todoList->task_description);
                // Get Users Details
                $todoUsers = Admin::wherein('id',explode(",",$todoList->assignto_id))->where('status',1)->get();
                // Get Meta API
                $metaAPI = Metawhatsappapi::where('status','=',1)->first();
                foreach ($todoUsers as $todoUser) {
                    // Send Whatsapp
                    if (isset($metaAPI)) {
                        // API Detai
                        $base_url = $metaAPI->api_base_url;
                        $vendor_id = $metaAPI->vendor_uid;
                        $access_token = $metaAPI->api_access_token;
                        $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                        $token = "Authorization: Bearer ".$access_token;

                        // Meta Whatsapp Data
                        $data = [];
                        $data['template_name'] = "reminder_staff_1";
                        $data['template_language'] = "en";
                        $data['phone_number'] = $todoUser->phone;
                        $data['field_1'] = $todoUser->name;
                        $data['field_2'] = $todoList->task_title;
                        if ($todoList->task_description != '') {
                            // $data['field_3'] = $todoList->task_description;
                            $data['field_3'] = $final_desc;
                            // $data['field_3'] = urlencode($todoList->task_description);
                        } else {
                            $data['field_3'] = "Description Not Available";
                        }
                        if ($todoList->finish_on != '') {
                            $data['field_4'] = date('d-m-Y',strtotime($todoList->finish_on));
                        } else {
                            $data['field_4'] = "Due Date Not Available!";
                        }

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

                    // Send Mail
                    if ($todoUser->email != '') {
                        if ($todoList->finish_on != '') {
                            $finish_date = date('d-m-Y',strtotime($todoList->finish_on));
                        } else {
                            $finish_date = "Due Date Not Available!";
                        }

                        $msg = "Dear ".$todoUser->name."\n\nYour task ".$todoList->task_title."\n\nDue date ".$finish_date."\n\nQamr International Hr consultancy\n\nDon`t ignore this reminder";
                        $mailData =  [
                            'email' => $todoUser->email,
                            'body_msg' => $msg
                        ];

                        try {
                            Mail::to($todoUser->email)->send(new TodoReminderMail($mailData));
                            // Upload Whatsapp Response
                            $respost = new Todoreminderresponse();
                            $respost->todo_id = $todoList->id;
                            $respost->response_for = "Email Reminder";
                            $respost->name = $todoUser->name;
                            $respost->email = $todoUser->email;
                            $respost->message_status = "success";
                            $respost->message_text = "Email sent successfully";
                            $respost->save();
                        } catch (\Throwable $exception) {
                            // Upload Whatsapp Response
                            $respost = new Todoreminderresponse();
                            $respost->todo_id = $todoList->id;
                            $respost->response_for = "Email Reminder";
                            $respost->name = $todoUser->name;
                            $respost->email = $todoUser->email;
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
                        $respost->email = $todoUser->email;
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
                // Get Meta API
                $metaAPI = Metawhatsappapi::where('status','=',1)->first();
                foreach ($todoUsers as $todoUser) {
                    // Send Whatsapp
                    if (isset($metaAPI)) {
                        // API Detai
                        $base_url = $metaAPI->api_base_url;
                        $vendor_id = $metaAPI->vendor_uid;
                        $access_token = $metaAPI->api_access_token;
                        $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                        $token = "Authorization: Bearer ".$access_token;

                        // Meta Whatsapp Data
                        $data = [];
                        $data['template_name'] = "reminder_staff_1";
                        $data['template_language'] = "en";
                        $data['phone_number'] = $todoUser->phone;
                        $data['field_1'] = $todoUser->name;
                        $data['field_2'] = $todoList2->task_title;
                        if ($todoList2->task_description != '') {
                            // $data['field_3'] = $todoList2->task_description;
                            $data['field_3'] = $final_desc;
                            // $data['field_3'] = urlencode($todoList2->task_description);
                        } else {
                            $data['field_3'] = "Description Not Available";
                        }
                        if ($todoList2->finish_on != '') {
                            $data['field_4'] = date('d-m-Y',strtotime($todoList2->finish_on));
                        } else {
                            $data['field_4'] = "Due Date Not Available!";
                        }

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

                    // Send Mail
                    if ($todoUser->email != '') {
                        if ($todoList2->finish_on != '') {
                            $finish_date = date('d-m-Y',strtotime($todoList2->finish_on));
                        } else {
                            $finish_date = "Due Date Not Available!";
                        }

                        $msg = "Dear ".$todoUser->name."\n\nYour task ".$todoList2->task_title."\n\nDue date ".$finish_date."\n\nQamr International Hr consultancy\n\nDon`t ignore this reminder";
                        $mailData =  [
                            'email' => $todoUser->email,
                            'body_msg' => $msg
                        ];

                        try {
                            Mail::to($todoUser->email)->send(new TodoReminderMail($mailData));
                            // Upload Whatsapp Response
                            $respost = new Todoreminderresponse();
                            $respost->todo_id = $todoList2->id;
                            $respost->response_for = "Email Reminder";
                            $respost->name = $todoUser->name;
                            $respost->email = $todoUser->email;
                            $respost->message_status = "success";
                            $respost->message_text = "Email sent successfully";
                            $respost->save();
                        } catch (\Throwable $exception) {
                            // Upload Whatsapp Response
                            $respost = new Todoreminderresponse();
                            $respost->todo_id = $todoList2->id;
                            $respost->response_for = "Email Reminder";
                            $respost->name = $todoUser->name;
                            $respost->email = $todoUser->email;
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
                        $respost->email = $todoUser->email;
                        $respost->message_status = "Failed";
                        $respost->message_text = "Email is required!";
                        $respost->save();
                    }


                }
            }

        }

        if ($todoLists3->count() > 0) {

            foreach ($todoLists3 as $todoList3) {
                // remove \n\t from text description
                $final_desc = preg_replace('/[\n\t]+/', ' ', $todoList3->task_description);
                // Get Users Details
                $todoUsers = Admin::wherein('id',explode(",",$todoList3->assignto_id))->where('status',1)->get();
                // Get Meta API
                $metaAPI = Metawhatsappapi::where('status','=',1)->first();
                foreach ($todoUsers as $todoUser) {
                    // Send Whatsapp
                    if (isset($metaAPI)) {
                        // API Detai
                        $base_url = $metaAPI->api_base_url;
                        $vendor_id = $metaAPI->vendor_uid;
                        $access_token = $metaAPI->api_access_token;
                        $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                        $token = "Authorization: Bearer ".$access_token;

                        // Meta Whatsapp Data
                        $data = [];
                        $data['template_name'] = "reminder_staff_1";
                        $data['template_language'] = "en";
                        $data['phone_number'] = $todoUser->phone;
                        $data['field_1'] = $todoUser->name;
                        $data['field_2'] = $todoList3->task_title;
                        if ($todoList3->task_description != '') {
                            // $data['field_3'] = $todoList3->task_description;
                            $data['field_3'] = $final_desc;
                            // $data['field_3'] = urlencode($todoList3->task_description);
                        } else {
                            $data['field_3'] = "Description Not Available";
                        }
                        if ($todoList3->finish_on != '') {
                            $data['field_4'] = date('d-m-Y',strtotime($todoList3->finish_on));
                        } else {
                            $data['field_4'] = "Due Date Not Available!";
                        }

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

                    // Send Mail
                    if ($todoUser->email != '') {
                        if ($todoList3->finish_on != '') {
                            $finish_date = date('d-m-Y',strtotime($todoList3->finish_on));
                        } else {
                            $finish_date = "Due Date Not Available!";
                        }

                        $msg = "Dear ".$todoUser->name."\n\nYour task ".$todoList3->task_title."\n\nDue date ".$finish_date."\n\nQamr International Hr consultancy\n\nDon`t ignore this reminder";
                        $mailData =  [
                            'email' => $todoUser->email,
                            'body_msg' => $msg
                        ];

                        try {
                            Mail::to($todoUser->email)->send(new TodoReminderMail($mailData));
                            // Upload Whatsapp Response
                            $respost = new Todoreminderresponse();
                            $respost->todo_id = $todoList3->id;
                            $respost->response_for = "Email Reminder";
                            $respost->name = $todoUser->name;
                            $respost->email = $todoUser->email;
                            $respost->message_status = "success";
                            $respost->message_text = "Email sent successfully";
                            $respost->save();
                        } catch (\Throwable $exception) {
                            // Upload Whatsapp Response
                            $respost = new Todoreminderresponse();
                            $respost->todo_id = $todoList3->id;
                            $respost->response_for = "Email Reminder";
                            $respost->name = $todoUser->name;
                            $respost->email = $todoUser->email;
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
                        $respost->email = $todoUser->email;
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
