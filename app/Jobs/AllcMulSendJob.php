<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AllcMulSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $tempDet,$cont_det,$getAPI;
    public function __construct($tempDet,$cont_det,$getAPI)
    {
        $this->tempDet = $tempDet;
        $this->cont_det = $cont_det;
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

        // $urlPath = 'http://crm.qamrintl.com';
        // $urlPath =  "http://crm.qamr.in";
        $urlPath = url('/'); 
        $filePath = 'image/whatsapp';
        $fileName = $this->tempDet['file'];
        // $fileName = '5.jpg';
        $type = "image";

        $remStr = ["[Business Type]","[Full Name]","[Mobile No]","[Email]","[ID]","[Country]","[City]","[Source]","[Job Title]","[Agency Name]","[Company Name]","[Industry]","[Created Date]"];
        $repStr = [$this->cont_det['lead_type'],$this->cont_det['full_name'],$this->cont_det['mobile_no'],$this->cont_det['email'],$this->cont_det['id'],$this->cont_det['country_id'],$this->cont_det['city'],$this->cont_det['source'],$this->cont_det['job_title'],$this->cont_det['office_name'],$this->cont_det['company_name'],$this->cont_det['indust_id'],$this->cont_det['created_at']];

        $finalmsgbody = str_replace($remStr,$repStr,$this->tempDet['msgBody']);

        if($this->tempDet['file'] != ''){
            // $media = $urlPath.'/'.$filePath.'/'.$fileName;
            $media = url('/image/whatsapp/'.$fileName);
            // $media = 'http://crm.qamrintl.com/image/whatsapp/'.$this->tempDet['file'];
            // Send Message on First Number
            if($this->cont_det['mobile_no'] !=''){
              // $data = [
              //   "number" => $this->cont_det['mobile_no'],
              //   "msg" => $whsMesg,
              //   "media" => $media,
              //   "type" => $type,
              //   "instance" => $ins,
              //   "apikey" => $api,
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
  
  
              $url = $txt_url."?number=".$this->cont_det['mobile_no']."&type=media&message=".urlencode($finalmsgbody)."&media_url=".$media."&filename=".$this->tempDet['file']."&instance_id=".$ins."&access_token=".$api;
              
              $ch = curl_init();
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
              curl_setopt($ch,CURLOPT_URL,$url);
              $result =  curl_exec($ch);
              
              echo $result;
              curl_close($ch);
            }
            // Send Message on Second Number
            if($this->cont_det['phone0'] !=''){
              // $data2 = [
              //   'number' => $this->cont_det['phone0'],
              //   'msg' => $whsMesg,
              //   'media' => $media,
              //   "type" => $type,
              //   "instance" => $ins,
              //   "apikey" => $api
              // ];
    
              // $ch = curl_init();
              // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
              // curl_setopt($ch, CURLOPT_URL, $media_url);
              // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              // $result = curl_exec($ch);
              // curl_close($ch);
  
              $url = $txt_url."?number=".$this->cont_det['phone0']."&type=media&message=".urlencode($finalmsgbody)."&media_url=".$media."&filename=".$this->tempDet['file']."&instance_id=".$ins."&access_token=".$api;
              
              $ch = curl_init();
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
              curl_setopt($ch,CURLOPT_URL,$url);
              $result =  curl_exec($ch);
              
              echo $result;
              curl_close($ch);
            }
            // Send Message on Third Number
            if($this->cont_det['phone1'] !=''){
              // $data3 = [
              //   'number' => $this->cont_det['phone1'],
              //   'msg' => $whsMesg,
              //   'media' => $media,
              //   "type" => $type,
              //   "instance" => $ins,
              //   "apikey" => $api
              // ];
    
              // $ch = curl_init();
              // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data3));
              // curl_setopt($ch, CURLOPT_URL, $media_url);
              // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              // $result = curl_exec($ch);
              // curl_close($ch);
  
              $url = $txt_url."?number=".$this->cont_det['phone1']."&type=media&message=".urlencode($finalmsgbody)."&media_url=".$media."&filename=".$this->tempDet['file']."&instance_id=".$ins."&access_token=".$api;
              
              $ch = curl_init();
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
              curl_setopt($ch,CURLOPT_URL,$url);
              $result =  curl_exec($ch);
              
              echo $result;
              curl_close($ch);
            }
            // Send Message on First Number
            if($this->cont_det['phone2'] !=''){
              // $data4 = [
              //   'number' => $this->cont_det['phone2'],
              //   'msg' => $whsMesg,
              //   'media' => $media,
              //   "type" => $type,
              //   "instance" => $ins,
              //   "apikey" => $api
              // ];
    
              // $ch = curl_init();
              // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data4));
              // curl_setopt($ch, CURLOPT_URL, $media_url);
              // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              // $result = curl_exec($ch);
              // curl_close($ch);
  
              $url = $txt_url."?number=".$this->cont_det['phone2']."&type=media&message=".urlencode($finalmsgbody)."&media_url=".$media."&filename=".$this->tempDet['file']."&instance_id=".$ins."&access_token=".$api;
              
              $ch = curl_init();
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
              curl_setopt($ch,CURLOPT_URL,$url);
              $result =  curl_exec($ch);
              
              echo $result;
              curl_close($ch);
            }
            // Send Message on First Number
            // if($this->cont_det['primary_contact_no'] !=''){
            //   $data5 = [
            //     'number' => $this->cont_det['primary_contact_no'],
            //     'msg' => $whsMesg,
            //     'media' => $media,
            //     "type" => "image",
            //     "instance" => $ins,
            //     "apikey" => $api
            //   ];
    
            //   $ch = curl_init();
            //   curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            //   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //   curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data5));
            //   curl_setopt($ch, CURLOPT_URL, $mmu);
            //   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            //   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            //   $result = curl_exec($ch);
            //   curl_close($ch);
            // }
  
          }else{
               // Send Message on First Number
            if($this->cont_det['mobile_no'] !=''){
              // $data = [
              //   'number' => $this->cont_det['mobile_no'],
              //   'msg' => $whsMesg,
              //   "instance" => $ins,
              //   "apikey" => $api
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
  
              $url = $txt_url."?number=".$this->cont_det['mobile_no']."&type=text&message=".urlencode($finalmsgbody)."&instance_id=".$ins."&access_token=".$api;
              $ch = curl_init();
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
              curl_setopt($ch,CURLOPT_URL,$url);
              $result =  curl_exec($ch);
              
              echo $result;
              curl_close($ch);
            }
            // Send Message on Second Number
            if($this->cont_det['phone0'] !=''){
              // $data2 = [
              //   'number' => $this->cont_det['phone0'],
              //   'msg' => $whsMesg,
              //   "instance" => $ins,
              //   "apikey" => $api
              // ];
    
              // $ch = curl_init();
              // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
              // curl_setopt($ch, CURLOPT_URL, $tmu);
              // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              // $result = curl_exec($ch);
              // curl_close($ch);
  
              $url = $txt_url."?number=".$this->cont_det['phone0']."&type=text&message=".urlencode($finalmsgbody)."&instance_id=".$ins."&access_token=".$api;
              $ch = curl_init();
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
              curl_setopt($ch,CURLOPT_URL,$url);
              $result =  curl_exec($ch);
              
              echo $result;
              curl_close($ch);
            }
            // Send Message on Third Number
            if($this->cont_det['phone1'] !=''){
              // $data3 = [
              //   'number' => $this->cont_det['phone1'],
              //   'msg' => $whsMesg,
              //   "instance" => $ins,
              //   "apikey" => $api
              // ];
    
              // $ch = curl_init();
              // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data3));
              // curl_setopt($ch, CURLOPT_URL, $tmu);
              // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              // $result = curl_exec($ch);
              // curl_close($ch);
  
              $url = $txt_url."?number=".$this->cont_det['phone1']."&type=text&message=".urlencode($finalmsgbody)."&instance_id=".$ins."&access_token=".$api;
              $ch = curl_init();
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
              curl_setopt($ch,CURLOPT_URL,$url);
              $result =  curl_exec($ch);
              
              echo $result;
              curl_close($ch);
            }
            // Send Message on First Number
            if($this->cont_det['phone2'] !=''){
              // $data4 = [
              //   'number' => $this->cont_det['phone2'],
              //   'msg' => $whsMesg,
              //   "instance" => $ins,
              //   "apikey" => $api
              // ];
    
              // $ch = curl_init();
              // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
              // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data4));
              // curl_setopt($ch, CURLOPT_URL, $tmu);
              // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
              // $result = curl_exec($ch);
              // curl_close($ch);
  
              $url = $txt_url."?number=".$this->cont_det['phone2']."&type=text&message=".urlencode($finalmsgbody)."&instance_id=".$ins."&access_token=".$api;
              $ch = curl_init();
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
              curl_setopt($ch,CURLOPT_URL,$url);
              $result =  curl_exec($ch);
             
              echo $result;
              curl_close($ch);
            }
            // Send Message on First Number
            // if($this->cont_det['primary_contact_no'] !=''){
            //   $data5 = [
            //     'number' => $this->cont_det['primary_contact_no'],
            //     'msg' => $whsMesg,
            //     "instance" => $ins,
            //     "apikey" => $api
            //   ];
    
            //   $ch = curl_init();
            //   curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            //   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //   curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data5));
            //   curl_setopt($ch, CURLOPT_URL, $tmu);
            //   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            //   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            //   $result = curl_exec($ch);
            //   curl_close($ch);
            // }
          }
    }
}
