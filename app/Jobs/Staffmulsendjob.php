<?php

namespace App\Jobs;

use App\AdminModel\Branch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Staffmulsendjob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $tempDet,$staff,$getAPI;
    public function __construct($tempDet,$staff,$getAPI)
    {
        $this->tempDet = $tempDet;
        $this->staff = $staff;
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

        // $urlPath = url('/');
        // $urlPath = 'http://crm.qamrintl.com';
        // $urlPath =  "http://crm.qamr.in";
        $urlPath = url('/');
        $filePath = 'image/whatsapp';
        $fileName = $this->tempDet['file'];

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

        $whsMesg = str_replace($remStr,$repStr,$this->tempDet['msgBody']);

        if($this->tempDet['file'] != ''){
            // $media = $urlPath.'/'.$filePath.'/'.$fileName;
            $media = url('/image/whatsapp/'.$fileName);

            if($this->staff['mobile_no'] !=''){
                $url = $txt_url."?number=".$phone1."&type=media&message=".urlencode($whsMesg)."&media_url=".$media."&filename=".$this->tempDet['file']."&instance_id=".$ins."&access_token=".$api;
            
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                echo $result;
            }

        }else{
            if($this->staff['mobile_no'] !=''){
                
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
