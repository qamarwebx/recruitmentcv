<?php

namespace App\Jobs;

use App\AdminModel\Branch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StaffSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $staff;
    protected $get_camp_list;
    protected $getAPI;

    public function __construct($staff,$get_camp_list,$getAPI)
    {
        $this->staff = $staff;
        $this->get_camp_list = $get_camp_list;
        $this->getAPI = $getAPI;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // API Details
        $ins = $this->getAPI['instance_key'];
        $api = $this->getAPI['api_key'];

        $txt_url = $this->getAPI['text_message_url']; // https://qamr.in/api/send.php?

        $tmu = $this->getAPI['text_message_url'];
        $mmu = $this->getAPI['media_message_url'];

        // API Details
        // $ins = $this->getAPI['instance_key'];
        // $api = $this->getAPI['api_key'];
        // $tmu = $this->getAPI['text_message_url'];
        // $mmu = $this->getAPI['media_message_url'];

        // media URL
        $media_url = 'http://whatsapi.smsinsta.com/api/send-media';
        // $urlPath = url('/');
        // $urlPath = 'http://crm.qamrintl.com';
        // $urlPath =  "http://crm.qamr.in";
        $urlPath = url('/');
        $filePath = 'image/whatsapp';
        $fileName = $this->get_camp_list['file'];
        // $fileName = '5.jpg';
        $type = "image";

        $staff_name = $this->staff['staff_fname'].' '.$this->staff['staff_lname'];

        

        $phone1 = $this->staff['mobile_no'];

        $branch = Branch::where('br_id','=',$this->staff['staff_branch'])->first();
        if(isset($branch)){
            $branch_name = $branch->br_name;
        }else{
            $branch_name = "None";
        }

        $remStr = ["[Name]","[Designation]","[Branch]","[ID]","[Email ID By Company]","[Personal Email ID]","[Mobile No]"];
        $repStr = [$staff_name,$this->staff['staff_designation'],$branch_name,$this->staff['staff_id'],$this->staff['staff_comp_email'],$this->staff['staff_pers_email'],$phone1];

        // $whsMesg = "Dear ".$staff_name."\n".$this->get_camp_list['msgBody'];

        $whsMesg = str_replace($remStr,$repStr,$this->get_camp_list['msgBody']);


        if($this->get_camp_list['file'] != ''){
            // $media = $urlPath.'/'.$filePath.'/'.$fileName;
            $media = url('/image/whatsapp/'.$fileName);

            // Send Message on First Number
            if($this->staff['mobile_no'] !=''){
                // $data = [
                // "number" => '91'.$this->staff['mobile_no'],
                // "msg" => $whsMesg,
                // "media" => $media,
                // "type" => $type,
                // "instance" => $ins,
                // "apikey" => $api,
                // ];
    
                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                // curl_setopt($ch, CURLOPT_URL, $media_url);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                // $result = curl_exec($ch);
                // curl_close($ch);

                $url = $txt_url."?number=".$phone1."&type=media&message=".urlencode($whsMesg)."&media_url=".$media."&filename=".$this->get_camp_list['file']."&instance_id=".$ins."&access_token=".$api;
            
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                echo $result;
            }
        }else{
            // Send Message on First Number
            if($this->staff['mobile_no'] !=''){
                // $data = [
                //     'number' => '91'.$this->staff['mobile_no'],
                //     'msg' => $whsMesg,
                //     "instance" => $ins,
                //     "apikey" => $api
                // ];

                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                // curl_setopt($ch, CURLOPT_URL, $tmu);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                // $result = curl_exec($ch);
                // curl_close($ch);

                $url = $txt_url."?number=".$phone1."&type=text&message=".urlencode($whsMesg)."&instance_id=".$ins."&access_token=".$api;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }
        }

    }
}
