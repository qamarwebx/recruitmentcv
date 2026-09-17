<?php

namespace App\Console\Commands;

use App\AdminModel\EmigrationStatus;
use App\AdminModel\Party;
use App\AdminModel\Userwhatsappapi;
use App\Mail\WaitingForEmpDocs as MailWaitingForEmpDocs;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class WaitingForEMPDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:waitfempc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $posts = EmigrationStatus::where('pty_id','!=',200)->where('waiting_for_emp_docs','=',1)->get();

        if($posts->count() > 0){
            // Whatsapp API Details
            $getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=','1')->first();
            $ins_id = $getAPI->instance_key;
            $api_key = $getAPI->api_key;
            $text_url = $getAPI->text_message_url;


            foreach ($posts as $post) {
                // Party Details
                $party = Party::where('pty_id','=',$post->pty_id)->first();

                // Emigration Details
                $emig_det = DB::table('emigrations as emig')
                    ->leftjoin('qr_employee_tbl as emp','emp.emp_id','=','emig.emp_id')
                    ->select('emig.*','emp.emp_id_no','emp.spon_nm_eng')
                    ->where('emig.id','=',$post->emig_id)
                    ->first();

                // Send Whatsapp Message
                $whmsg = "Dear ".$party->pty_ag_name."\n".$emig_det->cand_name." holding  passport No. ".$emig_det->pass_no." and ".$emig_det->spon_nm_eng." and his ID NO ".$emig_det->emp_id_no." for applying emigration clearance The documents required is  Sponsor nation id, and Address proof ,also CR copy.it is Our humble request please submit all the mentioned documents So We can do the further process\n\nडिअर ".$party->pty_ag_name."\n".$emig_det->cand_name." उनका पासपोर्ट नंबर ".$emig_det->pass_no." और स्पोंसर का नाम ".$emig_det->spon_nm_eng." और उनकी आईडी नंबर ".$emig_det->emp_id_no." एमिग्रेशन क्लेअरन्स करने के लिएआवश्यक स्पोंसर नेशन आईडी एड्रेस प्रूफ और सी आर कॉपी। यह हमारा विनम्र अनुरोध है कि कृपया सभी उल्लेखित दस्तावेज जमा करें, उसके बाद हम आगे की प्रक्रिया कर सकते हैं";
                
                if($party->pty_comp_contact != ''){
                    $url = $text_url."?number=".$party->pty_comp_contact."&type=text&message=".urlencode($whmsg)."&instance_id=".$ins_id."&access_token=".$getAPI->api_key;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result = curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }

                if($party->pty_contact_no != ''){
                    $url = $text_url."?number=".$party->pty_contact_no."&type=text&message=".urlencode($whmsg)."&instance_id=".$ins_id."&access_token=".$getAPI->api_key;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result = curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }

                // Send Email to party

                if ($party->pty_email) {
                    Mail::to($party->pty_email)->send(new MailWaitingForEmpDocs($party,$emig_det));
                }

                if($party->sec_email){

                }
                
            }
            
        }

        return 0;
    }
}
