<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Todo;
use App\Models\Metawhatsappapi;
use App\Models\Admin;
use App\Models\Todoreminderresponse;
use App\Mail\TodoReminderMail;
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

        $todoLists = Todo::where('finish_on','<=',$todaydate)->where('task_status','!=','New Task')->where('is_completed',false)->where('scheduled_status',false)->where('type','!=','Always')->get();

        if ($todoLists->count() > 0) {

            foreach ($todoLists as $todoList) {
                $task_title = "Your (".$todoList->task_title.") task is not marked as completed.";
                $task_desc = "Please mark as completed within 24 hours, otherwise your login system temporary inactive.";
                $task_duedate = $todoList->finish_on;
                // Get Users Details
                $todoUsers = Admin::wherein('id',explode(",",$todoList->assignto_id))->where('status',1)->get();
                // Get Meta API
                // $metaAPI = Metawhatsappapi::where('status','=',1)->first();
                $metaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['campaign'])->first();
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
                        $data['field_2'] = $task_title;
                        $data['field_3'] = $task_desc;
                        $data['field_4'] = $task_duedate;


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


                        $msg = "Dear ".$todoUser->name."\n\n".$task_title."\n\nDue date ".$task_duedate."\n\nQamr International Hr consultancy\n\nDon`t ignore this reminder";
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

        return Command::SUCCESS;
    }
}
