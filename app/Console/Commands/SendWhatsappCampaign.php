<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\AdminModel\Campaignlist;
use App\AdminModel\Userwhatsappapi;
use App\AdminModel\Staff;
use App\AdminModel\Party;
use App\AdminModel\AllContact;
use App\AdminModel\Branch;
use App\AdminModel\Partywakalacard;
use App\AdminModel\WhatsappTemplate;

class SendWhatsappCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:whatsappcampaign';

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
        $dateC = date('Y-m-d');
        $timeC = date('H:i'); 
        $posts = Campaignlist::where('sch_type','=',2)->where('camp_date','=',$dateC)->where('camp_time','=',$timeC)->where('stop','=',0)->get();
        if(isset($posts)){
            foreach($posts as $post){

                if($post->wTemp == 'multipe_temp'){

                    if($post->module_name == 'staff'){
                        $temp_id2 = explode(",",$post->temp_id_text);
                        $api_id2 = explode(",",$post->api_id_text);

                        foreach($api_id2 as $api_id){
                            // Get API details
                            $getAPI = Userwhatsappapi::find($api_id);
                            $ins = $getAPI->instance_key;
                            $api = $getAPI->api_key;
                            $txt_url = $getAPI->text_message_url;
                            // $urlPath = 'http://crm.qamrintl.com';
                            // $urlPath =  "http://crm.qamr.in";
                            $urlPath = url('/');
                            $filePath = 'image/whatsapp';



                            // Get all staff data
                            $staffs = Staff::where('staff_status','!=','0')->where('staff_id','!=','200')->get();
                            $totalcontact [] = count($staffs);

                            $counter = 0;
                            $tempC = count($temp_id2);
                            $tempCD = $tempC - 1;

                            $remStr = ["[Name]","[Designation]","[Branch]","[ID]","[Email ID By Company]","[Personal Email ID]","[Mobile No]"];                            

                            if(count($staffs) > $tempC){
                                foreach($staffs as $staff){
                                    // Get Template Details
                                    $tempDet = WhatsappTemplate::find($temp_id2[$counter]);
                                    
                                    $branch = Branch::where('br_id','=',$staff->staff_branch)->first();
                                    if(isset($branch)){
                                        $branch_name = $branch->br_name;
                                    }else{
                                        $branch_name = "None";
                                    }

                                    $staff_name = $staff->staff_fname.' '.$staff->staff_lname;
                                    $phone1 = $staff->mobile_no;

                                    $repSTr = [$staff_name,$staff->staff_designation,$branch_name,$staff->staff_id,$staff->staff_comp_email,$staff->staff_pers_email,$phone1];

                                    $stfmsg = str_replace($remStr,$repSTr,$tempDet->msgBody);

                                    $fileName = $tempDet->file;
                                    $media = $urlPath.'/'.$filePath.'/'.$fileName;

                                    if($fileName != ''){
                                        $url = $txt_url."?number=".$phone1."&type=media&message=".urlencode($stfmsg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                                        $ch = curl_init();
                                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                        curl_setopt($ch,CURLOPT_URL,$url);
                                        $result =  curl_exec($ch);
                                        echo $result;
                                    }else{
                                        $url = $txt_url."?number=".$phone1."&type=text&message=".urlencode($stfmsg)."&instance_id=".$ins."&access_token=".$api;
                                        $ch = curl_init();
                                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                        curl_setopt($ch,CURLOPT_URL,$url);
                                        $result =  curl_exec($ch);
                                        echo $result;
                                        curl_close($ch);
                                    }

                                    if($counter == $tempCD){
                                        $counter = 0;
                                      }else{
                                        $counter++;
                                    }


                                }
                            }else{
                                foreach($staffs as $staff){
                                    // Get Template Details
                                    $tempDet = WhatsappTemplate::find($temp_id2[$counter]);
                                    
                                    $branch = Branch::where('br_id','=',$staff->staff_branch)->first();
                                    if(isset($branch)){
                                        $branch_name = $branch->br_name;
                                    }else{
                                        $branch_name = "None";
                                    }

                                    $staff_name = $staff->staff_fname.' '.$staff->staff_lname;
                                    $phone1 = $staff->mobile_no;

                                    $repSTr = [$staff_name,$staff->staff_designation,$branch_name,$staff->staff_id,$staff->staff_comp_email,$staff->staff_pers_email,$phone1];

                                    $stfmsg = str_replace($remStr,$repSTr,$tempDet->msgBody);

                                    $fileName = $tempDet->file;
                                    $media = $urlPath.'/'.$filePath.'/'.$fileName;

                                    if($fileName != ''){
                                        $url = $txt_url."?number=".$phone1."&type=media&message=".urlencode($stfmsg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                                        $ch = curl_init();
                                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                        curl_setopt($ch,CURLOPT_URL,$url);
                                        $result =  curl_exec($ch);
                                        echo $result;
                                    }else{
                                        $url = $txt_url."?number=".$phone1."&type=text&message=".urlencode($stfmsg)."&instance_id=".$ins."&access_token=".$api;
                                        $ch = curl_init();
                                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                        curl_setopt($ch,CURLOPT_URL,$url);
                                        $result =  curl_exec($ch);
                                        echo $result;
                                        curl_close($ch);
                                    }

                                    $counter++;
                                }
                            }

                        }

                    }elseif($post->module_name == 'party'){
                        $temp_id2 = explode(",",$post->temp_id_text);
                        $api_id2 = explode(",",$post->api_id_text);

                        foreach($api_id2 as $api_id){
                            // Get API details
                            $getAPI = Userwhatsappapi::find($api_id);
                            $ins = $getAPI->instance_key;
                            $api = $getAPI->api_key;
                            $txt_url = $getAPI->text_message_url;
                            // $urlPath = 'http://crm.qamrintl.com';
                            // $urlPath =  "http://crm.qamr.in";
                            $urlPath = url('/');
                            $filePath = 'image/whatsapp';

                            $rel = $post->religion;
                            $p_status = explode(',',$post->party_status);
                            $contact_type = $post->party_contact_type;
                            $parties = Party::where('pty_id','!=',200)->where(function($query) use($rel,$p_status){
                                if($rel != 'All'){
                                    $query->where('pty_rel','=',$rel);
                                }
                                // if($p_status != 'All'){
                                //     $query->where('act_status','=',$p_status);
                                // }
                                $query->wherein('act_status',$p_status);
                            })->get();

                            $repstr = ["[Party Name]","[Agency Name]","[City]","[State]","[ID]","[Email]","[Primary Contact]","[Secondary Contact]","[DOB]","[Wakala Card]","[Membership]"];
                        
                            $counter = 0;
                            $tempC = count($temp_id2);
                            $tempcd = $tempC - 1;

                            if(count($parties) > $tempC){
                               
                                foreach($parties as $party){
                                    // Get Template Details
                                    $tempDet = WhatsappTemplate::find($temp_id2[$counter]);

                                    $fileName = $tempDet->file;
                                    $media = $urlPath.'/'.$filePath.'/'.$fileName;

                                    $repstr = [$party->pty_full_name,$party->pty_ag_name,$party->city,$party->state,$party->pty_id,$party->pty_email,$party->pty_comp_contact,$party->pty_contact_no,$party->pty_dob,"",$party->member_id];
        
                                    $ptyfinalmsgbody = str_replace($repstr,$repstr,$tempDet->msgBody);
    
                                    $primary_c = $party->pty_comp_contact;
                                    $personal_c = $party->p_mobile;
                                    $secondary_c = $party->pty_contact_no;

                                    if ($fileName != '') {

                                        if ($contact_type == '3') {
                                            if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                                                $url = $txt_url."?number=".$secondary_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                        }elseif($contact_type == '2'){
                                            if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                                                $url = $txt_url."?number=".$primary_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
    
                                            if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                                                $url = $txt_url."?number=".$personal_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                        }else{
                                            if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                                                $url = $txt_url."?number=".$primary_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
    
                                            if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                                                $url = $txt_url."?number=".$personal_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                            if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                                                $url = $txt_url."?number=".$secondary_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                        }
                                    } else {
                                        if ($contact_type == '3') {
                                            if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                                                $url = $txt_url."?number=".$secondary_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                        }elseif($contact_type == '2'){
                                            if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                                                $url = $txt_url."?number=".$primary_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
    
                                            if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                                                $url = $txt_url."?number=".$personal_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                        }else{
                                            if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                                                $url = $txt_url."?number=".$primary_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                            if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                                                $url = $txt_url."?number=".$personal_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                            if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                                                $url = $txt_url."?number=".$secondary_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                        }
                                    }
    
                                    if($post->wakala_st == 1){
                                        $getwak = Partywakalacard::where('pty_id','=',$party->pty_id)->first();
                                        $filepathw = "image/service-master";
                                        $media = $urlPath.'/'.$filepathw.'/'.$getwak->file;
    
                                        if ($contact_type == '3') {
                                            if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                                                $url = $txt_url."?number=".$secondary_c."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                        }elseif($contact_type == '2'){
                                            if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                                                $url = $txt_url."?number=".$primary_c."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
    
                                            if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                                                $url = $txt_url."?number=".$personal_c."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                        }else{
                                            if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                                                $url = $txt_url."?number=".$primary_c."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
    
                                            if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                                                $url = $txt_url."?number=".$personal_c."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                            if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                                                $url = $txt_url."?number=".$secondary_c."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                        }
    
                                    }


                                    if($counter == $tempcd){
                                        $counter = 0;
                                    }else{
                                        $counter++;
                                    }
                                }

                            }else{
                                foreach($parties as $party){
                                    // Get Template Details
                                    $tempDet = WhatsappTemplate::find($temp_id2[$counter]);

                                    $fileName = $tempDet->file;
                                    $media = $urlPath.'/'.$filePath.'/'.$fileName;

                                    $repstr = [$party->pty_full_name,$party->pty_ag_name,$party->city,$party->state,$party->pty_id,$party->pty_email,$party->pty_comp_contact,$party->pty_contact_no,$party->pty_dob,"",$party->member_id];
        
                                    $ptyfinalmsgbody = str_replace($repstr,$repstr,$tempDet->msgBody);
    
                                    $primary_c = $party->pty_comp_contact;
                                    $personal_c = $party->p_mobile;
                                    $secondary_c = $party->pty_contact_no;

                                    if ($fileName != '') {

                                        if ($contact_type == '3') {
                                            if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                                                $url = $txt_url."?number=".$secondary_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                        }elseif($contact_type == '2'){
                                            if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                                                $url = $txt_url."?number=".$primary_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
    
                                            if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                                                $url = $txt_url."?number=".$personal_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                        }else{
                                            if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                                                $url = $txt_url."?number=".$primary_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
    
                                            if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                                                $url = $txt_url."?number=".$personal_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                            if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                                                $url = $txt_url."?number=".$secondary_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                        }
                                    } else {
                                        if ($contact_type == '3') {
                                            if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                                                $url = $txt_url."?number=".$secondary_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                        }elseif($contact_type == '2'){
                                            if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                                                $url = $txt_url."?number=".$primary_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
    
                                            if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                                                $url = $txt_url."?number=".$personal_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                        }else{
                                            if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                                                $url = $txt_url."?number=".$primary_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                            if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                                                $url = $txt_url."?number=".$personal_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                            if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                                                $url = $txt_url."?number=".$secondary_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                        }
                                    }
    
                                    if($post->wakala_st == 1){
                                        $getwak = Partywakalacard::where('pty_id','=',$party->pty_id)->first();
                                        $filepathw = "image/service-master";
                                        $media = $urlPath.'/'.$filepathw.'/'.$getwak->file;
    
                                        if ($contact_type == '3') {
                                            if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                                                $url = $txt_url."?number=".$secondary_c."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                        }elseif($contact_type == '2'){
                                            if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                                                $url = $txt_url."?number=".$primary_c."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
    
                                            if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                                                $url = $txt_url."?number=".$personal_c."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                        }else{
                                            if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                                                $url = $txt_url."?number=".$primary_c."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
    
                                            if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                                                $url = $txt_url."?number=".$personal_c."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                            if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                                                $url = $txt_url."?number=".$secondary_c."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                                $ch = curl_init();
                                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                                curl_setopt($ch,CURLOPT_URL,$url);
                                                $result =  curl_exec($ch);
                                                
                                                echo $result;
                                                curl_close($ch);
                                            }
                                        }
    
                                    }


                                    $counter++;
                                }
                            }

                        }


                    }elseif($post->module_name == 'contact'){
                        $temp_id2 = explode(",",$post->temp_id_text);
                        $api_id2 = explode(",",$post->api_id_text);
                        $lead_type = $post->lead_type;
                        $lcs_id = $post->lcs_id;
                        $optinout = $post->optinout;
                        $group_id = $post->group_id;
                        $remStr = ["[Business Type]","[Full Name]","[Mobile No]","[Email]","[ID]","[Country]","[City]","[Source]","[Job Title]","[Agency Name]","[Company Name]","[Industry]","[Created Date]"];

                        foreach($api_id2 as $api_id){
                            // Get API details
                            $getAPI = Userwhatsappapi::find($api_id);
                            $ins = $getAPI->instance_key;
                            $api = $getAPI->api_key;
                            $txt_url = $getAPI->text_message_url;
                            // $urlPath = 'http://crm.qamrintl.com';
                            // $urlPath =  "http://crm.qamr.in";
                            $urlPath = url('/');
                            $filePath = 'image/whatsapp';

                            $cont_dets = AllContact::where('user_id','=',$getAPI->staff_id)->where(function($query) use($lead_type,$lcs_id,$optinout,$group_id){
                                if($lead_type != ''){
                                    $query->where('lead_type','=',$lead_type);
                                }
                                if($lcs_id != ''){
                                    $query->where('lcs_id','=',$lcs_id);
                                }
                                if($optinout != 'All'){
                                    $query->where('optinout','=',$optinout);
                                }
                                if($group_id != ''){
                                    $query->where('group_id','=',$group_id);
                                }
                            })->get();
                            
                            $counter = 0;
                            $tempC = count($temp_id2);
                            $tempcd = $tempC - 1;

                            if(count($cont_dets) > $tempC){
                                foreach($cont_dets as $cont_det){
                                    // Get Template Details
                                    $tempDet = WhatsappTemplate::find($temp_id2[$counter]);

                                    $fileName = $tempDet->file;
                                    $media = $urlPath.'/'.$filePath.'/'.$fileName;

                                    $repStr = [$cont_det->lead_type,$cont_det->full_name,$cont_det->mobile_no,$cont_det->email,$cont_det->id,$cont_det->country_id,$cont_det->city,$cont_det->source,$cont_det->job_title,$cont_det->office_name,$cont_det->company_name,$cont_det->indust_id,$cont_det->created_at];
                                    $final_msg = str_replace($remStr,$repStr,$tempDet->msgBody);
                                    // $whsMesg = "Dear ".$cont_det->full_name."\n".$post->msgBody;
                                    if($fileName != ''){
                                        if($cont_det->mobile_no != ''){
                                            $url = $txt_url."?number=".$cont_det->mobile_no."&type=media&message=".urlencode($final_msg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
            
                                        if($cont_det->phone0 != ''){
                                            $url = $txt_url."?number=".$cont_det->phone0."&type=media&message=".urlencode($final_msg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
            
                                        if($cont_det->phone1 != ''){
                                            $url = $txt_url."?number=".$cont_det->phone1."&type=media&message=".urlencode($final_msg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
            
                                        if($cont_det->phone2 != ''){
                                            $url = $txt_url."?number=".$cont_det->phone2."&type=media&message=".urlencode($final_msg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
            
                                    }else{
                                        if($cont_det->mobile_no != ''){
                                            $url = $txt_url."?number=".$cont_det->mobile_no."&type=text&message=".urlencode($final_msg)."&instance_id=".$ins."&access_token=".$api;
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
            
                                        if($cont_det->phone0 != ''){
                                            $url = $txt_url."?number=".$cont_det->phone0."&type=text&message=".urlencode($final_msg)."&instance_id=".$ins."&access_token=".$api;
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
            
                                        if($cont_det->phone1 != ''){
                                            $url = $txt_url."?number=".$cont_det->phone1."&type=text&message=".urlencode($final_msg)."&instance_id=".$ins."&access_token=".$api;
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
            
                                        if($cont_det->phone2 != ''){
                                            $url = $txt_url."?number=".$cont_det->phone2."&type=text&message=".urlencode($final_msg)."&instance_id=".$ins."&access_token=".$api;
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
            
                                    }


                                    if($counter == $tempcd){
                                        $counter = 0;
                                    }else{
                                        $counter++;
                                    }
                                }
                            }else{
                                foreach($cont_dets as $cont_det){
                                    // Get Template Details
                                    $tempDet = WhatsappTemplate::find($temp_id2[$counter]);

                                    $fileName = $tempDet->file;
                                    $media = $urlPath.'/'.$filePath.'/'.$fileName;

                                    $repStr = [$cont_det->lead_type,$cont_det->full_name,$cont_det->mobile_no,$cont_det->email,$cont_det->id,$cont_det->country_id,$cont_det->city,$cont_det->source,$cont_det->job_title,$cont_det->office_name,$cont_det->company_name,$cont_det->indust_id];
                                    $final_msg = str_replace($remStr,$repStr,$tempDet->msgBody);
                                    // $whsMesg = "Dear ".$cont_det->full_name."\n".$post->msgBody;
                                    if($fileName != ''){
                                        if($cont_det->mobile_no != ''){
                                            $url = $txt_url."?number=".$cont_det->mobile_no."&type=media&message=".urlencode($final_msg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
            
                                        if($cont_det->phone0 != ''){
                                            $url = $txt_url."?number=".$cont_det->phone0."&type=media&message=".urlencode($final_msg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
            
                                        if($cont_det->phone1 != ''){
                                            $url = $txt_url."?number=".$cont_det->phone1."&type=media&message=".urlencode($final_msg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
            
                                        if($cont_det->phone2 != ''){
                                            $url = $txt_url."?number=".$cont_det->phone2."&type=media&message=".urlencode($final_msg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
            
                                    }else{
                                        if($cont_det->mobile_no != ''){
                                            $url = $txt_url."?number=".$cont_det->mobile_no."&type=text&message=".urlencode($final_msg)."&instance_id=".$ins."&access_token=".$api;
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
            
                                        if($cont_det->phone0 != ''){
                                            $url = $txt_url."?number=".$cont_det->phone0."&type=text&message=".urlencode($final_msg)."&instance_id=".$ins."&access_token=".$api;
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
            
                                        if($cont_det->phone1 != ''){
                                            $url = $txt_url."?number=".$cont_det->phone1."&type=text&message=".urlencode($final_msg)."&instance_id=".$ins."&access_token=".$api;
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
            
                                        if($cont_det->phone2 != ''){
                                            $url = $txt_url."?number=".$cont_det->phone2."&type=text&message=".urlencode($final_msg)."&instance_id=".$ins."&access_token=".$api;
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
            
                                    }



                                    $counter++;

                                }
                            }

                        }
                    }

                }else{
                    if($post->module_name == 'staff'){
                        foreach(explode(",",$post->api_id_text) as $api_id){
                            // Get API details
                            $getAPI = Userwhatsappapi::find($api_id);
                            $ins = $getAPI->instance_key;
                            $api = $getAPI->api_key;
                            $txt_url = $getAPI->text_message_url;
                            // $urlPath = 'http://crm.qamrintl.com';
                            // $urlPath =  "http://crm.qamr.in";
                            $urlPath = url('/');
                            $filePath = 'image/whatsapp';
                            $fileName = $post->file;
                            $media = $urlPath.'/'.$filePath.'/'.$fileName;

                            // Get all staff data
                            $staffs = Staff::where('staff_status','!=','0')->where('staff_id','!=','200')->get();

                            $remStr = ["[Name]","[Designation]","[Branch]","[ID]","[Email ID By Company]","[Personal Email ID]","[Mobile No]"];

                            foreach ($staffs as $staff) {
                                // Get Branch Name
                                $branch = Branch::where('br_id','=',$staff->staff_branch)->first();
                                if(isset($branch)){
                                    $branch_name = $branch->br_name;
                                }else{
                                    $branch_name = "None";
                                }

                                $staff_name = $staff->staff_fname.' '.$staff->staff_lname;
                                $phone1 = $staff->mobile_no;

                                $repSTr = [$staff_name,$staff->staff_designation,$branch_name,$staff->staff_id,$staff->staff_comp_email,$staff->staff_pers_email,$phone1];

                                $stfmsg = str_replace($remStr,$repSTr,$post->msgBody);

                                if($fileName != ''){
                                    $url = $txt_url."?number=".$phone1."&type=media&message=".urlencode($stfmsg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                                    $ch = curl_init();
                                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                    curl_setopt($ch,CURLOPT_URL,$url);
                                    $result =  curl_exec($ch);
                                    echo $result;
                                }else{
                                    $url = $txt_url."?number=".$phone1."&type=text&message=".urlencode($stfmsg)."&instance_id=".$ins."&access_token=".$api;
                                    $ch = curl_init();
                                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                    curl_setopt($ch,CURLOPT_URL,$url);
                                    $result =  curl_exec($ch);
                                    echo $result;
                                    curl_close($ch);
                                }
                            }
                            

                        }
                    }elseif($post->module_name == 'party'){

                        foreach (explode(",",$post->api_id_text) as $api_id) {
                            // Get API details
                            $getAPI = Userwhatsappapi::find($api_id);
                            $ins = $getAPI->instance_key;
                            $api = $getAPI->api_key;
                            $txt_url = $getAPI->text_message_url;
                            // $urlPath = 'http://crm.qamrintl.com';
                            // $urlPath =  "http://crm.qamr.in";
                            $urlPath = url('/');
                            $filePath = 'image/whatsapp';
                            $fileName = $post->file;
                            $media = $urlPath.'/'.$filePath.'/'.$fileName;

                            $rel = $post->religion;
                            $p_status = explode(',',$post->party_status);
                            $contact_type = $post->party_contact_type;
                            $parties = Party::where('pty_id','!=',200)->where(function($query) use($rel,$p_status){
                                if($rel != 'All'){
                                    $query->where('pty_rel','=',$rel);
                                }
                                // if($p_status != 'All'){
                                //     $query->where('act_status','=',$p_status);
                                // }
                                $query->wherein('act_status',$p_status);
                            })->get();

                            $repstr = ["[Party Name]","[Agency Name]","[City]","[State]","[ID]","[Email]","[Primary Contact]","[Secondary Contact]","[DOB]","[Wakala Card]","[Membership]"];
                            foreach($parties as $party){
                                $repstr = [$party->pty_full_name,$party->pty_ag_name,$party->city,$party->state,$party->pty_id,$party->pty_email,$party->pty_comp_contact,$party->pty_contact_no,$party->pty_dob,"",$party->member_id];
        
                                $ptyfinalmsgbody = str_replace($repstr,$repstr,$post->msgBody);

                                $primary_c = $party->pty_comp_contact;
                                $personal_c = $party->p_mobile;
                                $secondary_c = $party->pty_contact_no;
                                if ($fileName != '') {

                                    if ($contact_type == '3') {
                                        if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                                            $url = $txt_url."?number=".$secondary_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                    
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
                                    }elseif($contact_type == '2'){
                                        if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                                            $url = $txt_url."?number=".$primary_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                    
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }

                                        if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                                            $url = $txt_url."?number=".$personal_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                    
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
                                    }else{
                                        if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                                            $url = $txt_url."?number=".$primary_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                    
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }

                                        if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                                            $url = $txt_url."?number=".$personal_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                    
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
                                        if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                                            $url = $txt_url."?number=".$secondary_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                    
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
                                    }
                                } else {
                                    if ($contact_type == '3') {
                                        if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                                            $url = $txt_url."?number=".$secondary_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
                                    }elseif($contact_type == '2'){
                                        if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                                            $url = $txt_url."?number=".$primary_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }

                                        if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                                            $url = $txt_url."?number=".$personal_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
                                    }else{
                                        if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                                            $url = $txt_url."?number=".$primary_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
                                        if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                                            $url = $txt_url."?number=".$personal_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
                                        if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                                            $url = $txt_url."?number=".$secondary_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
                                    }
                                }

                                if($post->wakala_st == 1){
                                    $getwak = Partywakalacard::where('pty_id','=',$party->pty_id)->first();
                                    $filepathw = "image/service-master";
                                    $media = $urlPath.'/'.$filepathw.'/'.$getwak->file;

                                    if ($contact_type == '3') {
                                        if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                                            $url = $txt_url."?number=".$secondary_c."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                    
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
                                    }elseif($contact_type == '2'){
                                        if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                                            $url = $txt_url."?number=".$primary_c."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                    
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }

                                        if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                                            $url = $txt_url."?number=".$personal_c."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                    
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
                                    }else{
                                        if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                                            $url = $txt_url."?number=".$primary_c."&type=media&message=".urlencode($whsMesg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                    
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }

                                        if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                                            $url = $txt_url."?number=".$personal_c."&type=media&message=".urlencode($whsMesg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                    
                                            $ch = curl_init();
                                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                            curl_setopt($ch,CURLOPT_URL,$url);
                                            $result =  curl_exec($ch);
                                            
                                            echo $result;
                                            curl_close($ch);
                                        }
                                        if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                                            $url = $txt_url."?number=".$secondary_c."&type=media&message=".urlencode($whsMesg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                    
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
                        }


                    }elseif($post->module_name == 'contact'){
                        $lead_type = $post->lead_type;
                        $lcs_id = $post->lcs_id;
                        $optinout = $post->optinout;
                        $group_id = $post->group_id;

                        $remStr = ["[Business Type]","[Full Name]","[Mobile No]","[Email]","[ID]","[Country]","[City]","[Source]","[Job Title]","[Agency Name]","[Company Name]","[Industry]","[Created Date]"];

                        foreach (explode(",",$post->api_id_text) as $api_id) {

                            $getAPI = Userwhatsappapi::find($api_id);
                            $ins = $getAPI->instance_key;
                            $api = $getAPI->api_key;
                            $txt_url = $getAPI->text_message_url;
                            // $urlPath = 'http://crm.qamrintl.com';
                            // $urlPath =  "http://crm.qamr.in";
                            $urlPath = url('/');
                            $filePath = 'image/whatsapp';
                            $fileName = $post->file;
                            $media = $urlPath.'/'.$filePath.'/'.$fileName;

                            $cont_dets = AllContact::where('user_id','=',$getAPI->staff_id)->where(function($query) use($lead_type,$lcs_id,$optinout,$group_id){
                                if($lead_type != ''){
                                    $query->where('lead_type','=',$lead_type);
                                }
                                if($lcs_id != ''){
                                    $query->where('lcs_id','=',$lcs_id);
                                }
                                if($optinout != 'All'){
                                    $query->where('optinout','=',$optinout);
                                  }
                                  if($group_id != ''){
                                    $query->where('group_id','=',$group_id);
                                  }
                            })->get();

                            foreach ($cont_dets as $cont_det) {

                                $repStr = [$cont_det->lead_type,$cont_det->full_name,$cont_det->mobile_no,$cont_det->email,$cont_det->id,$cont_det->country_id,$cont_det->city,$cont_det->source,$cont_det->job_title,$cont_det->office_name,$cont_det->company_name,$cont_det->indust_id,$cont_det->created_at];
                                $final_msg = str_replace($remStr,$repStr,$post->msgBody);
                                // $whsMesg = "Dear ".$cont_det->full_name."\n".$post->msgBody;
                                if($fileName != ''){
                                    if($cont_det->mobile_no != ''){
                                        $url = $txt_url."?number=".$cont_det->mobile_no."&type=media&message=".urlencode($final_msg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                    
                                        $ch = curl_init();
                                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                        curl_setopt($ch,CURLOPT_URL,$url);
                                        $result =  curl_exec($ch);
                                        
                                        echo $result;
                                        curl_close($ch);
                                    }
        
                                    if($cont_det->phone0 != ''){
                                        $url = $txt_url."?number=".$cont_det->phone0."&type=media&message=".urlencode($final_msg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                    
                                        $ch = curl_init();
                                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                        curl_setopt($ch,CURLOPT_URL,$url);
                                        $result =  curl_exec($ch);
                                        
                                        echo $result;
                                        curl_close($ch);
                                    }
        
                                    if($cont_det->phone1 != ''){
                                        $url = $txt_url."?number=".$cont_det->phone1."&type=media&message=".urlencode($final_msg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                    
                                        $ch = curl_init();
                                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                        curl_setopt($ch,CURLOPT_URL,$url);
                                        $result =  curl_exec($ch);
                                        
                                        echo $result;
                                        curl_close($ch);
                                    }
        
                                    if($cont_det->phone2 != ''){
                                        $url = $txt_url."?number=".$cont_det->phone2."&type=media&message=".urlencode($final_msg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                    
                                        $ch = curl_init();
                                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                        curl_setopt($ch,CURLOPT_URL,$url);
                                        $result =  curl_exec($ch);
                                        
                                        echo $result;
                                        curl_close($ch);
                                    }
        
                                }else{
                                    if($cont_det->mobile_no != ''){
                                        $url = $txt_url."?number=".$cont_det->mobile_no."&type=text&message=".urlencode($final_msg)."&instance_id=".$ins."&access_token=".$api;
                                        $ch = curl_init();
                                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                        curl_setopt($ch,CURLOPT_URL,$url);
                                        $result =  curl_exec($ch);
                                        
                                        echo $result;
                                        curl_close($ch);
                                    }
        
                                    if($cont_det->phone0 != ''){
                                        $url = $txt_url."?number=".$cont_det->phone0."&type=text&message=".urlencode($final_msg)."&instance_id=".$ins."&access_token=".$api;
                                        $ch = curl_init();
                                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                        curl_setopt($ch,CURLOPT_URL,$url);
                                        $result =  curl_exec($ch);
                                        
                                        echo $result;
                                        curl_close($ch);
                                    }
        
                                    if($cont_det->phone1 != ''){
                                        $url = $txt_url."?number=".$cont_det->phone1."&type=text&message=".urlencode($final_msg)."&instance_id=".$ins."&access_token=".$api;
                                        $ch = curl_init();
                                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                        curl_setopt($ch,CURLOPT_URL,$url);
                                        $result =  curl_exec($ch);
                                        
                                        echo $result;
                                        curl_close($ch);
                                    }
        
                                    if($cont_det->phone2 != ''){
                                        $url = $txt_url."?number=".$cont_det->phone2."&type=text&message=".urlencode($final_msg)."&instance_id=".$ins."&access_token=".$api;
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
                    }
                }

                // $getAPI = Userwhatsappapi::find($post->whatsappi_id);
                // $ins = $getAPI->instance_key;
                // $api = $getAPI->api_key;
                // $txt_url = $getAPI->text_message_url;
                // $urlPath = 'http://crm.qamrintl.com';
                // $filePath = 'image/whatsapp';
                // $fileName = $post->file;
                // $media = $urlPath.'/'.$filePath.'/'.$fileName;

                
                // if($post->module_name == 'staff'){
                //     $staffs = Staff::where('staff_status','!=','0')->where('staff_id','!=','200')->get();

                //     $remStr = ["[Name]","[Designation]","[Branch]","[ID]","[Email ID By Company]","[Personal Email ID]","[Mobile No]"];

                //     foreach ($staffs as $staff) {

                //         // Get Branch Name
                //         $branch = Branch::where('br_id','=',$staff->staff_branch)->first();
                //         if(isset($branch)){
                //             $branch_name = $branch->br_name;
                //         }else{
                //             $branch_name = "None";
                //         }

                //         $staff_name = $staff->staff_fname.' '.$staff->staff_lname;
                //         $phone1 = $staff->mobile_no;

                //         $repSTr = [$staff_name,$staff->staff_designation,$branch_name,$staff->staff_id,$staff->staff_comp_email,$staff->staff_pers_email,$phone1];

                //         $stfmsg = str_replace($remStr,$repSTr,$post->msgBody); 

                //         // $whsMesg = "Dear ".$staff_name."\n".$post->msgBody;
                //         if($fileName != ''){
                //             $url = $txt_url."?number=".$phone1."&type=media&message=".urlencode($stfmsg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                //             $ch = curl_init();
                //             curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //             curl_setopt($ch,CURLOPT_URL,$url);
                //             $result =  curl_exec($ch);
                //             echo $result;
                //         }else{
                //             $url = $txt_url."?number=".$phone1."&type=text&message=".urlencode($stfmsg)."&instance_id=".$ins."&access_token=".$api;
                //             $ch = curl_init();
                //             curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //             curl_setopt($ch,CURLOPT_URL,$url);
                //             $result =  curl_exec($ch);
                //             echo $result;
                //             curl_close($ch);
                //         }
                //     }

                // }elseif($post->module_name == 'party'){
                //     $rel = $post->religion;
                //     $p_status = explode(',',$post->party_status);
                //     $contact_type = $post->party_contact_type;
                //     $parties = Party::where('pty_id','!=',200)->where(function($query) use($rel,$p_status){
                //         if($rel != 'All'){
                //             $query->where('pty_rel','=',$rel);
                //         }
                //         // if($p_status != 'All'){
                //         //     $query->where('act_status','=',$p_status);
                //         // }
                //         $query->wherein('act_status',$p_status);
                //     })->get();

                //     foreach ($parties as $party) {

                //         $repstr = ["[Party Name]","[Agency Name]","[City]","[State]","[ID]","[Email]","[Primary Contact]","[Secondary Contact]","[DOB]","[Wakala Card]","[Membership]"];
                //         $repstr = [$party->pty_full_name,$party->pty_ag_name,$party->city,$party->state,$party->pty_id,$party->pty_email,$party->pty_comp_contact,$party->pty_contact_no,$party->pty_dob,"",$party->member_id];
                
                //         $ptyfinalmsgbody = str_replace($repstr,$repstr,$post->msgBody);

                //         // $whsMesg = "Dear ".$party->pty_full_name."\n".$party->pty_ag_name."\n".$finalmsgbody;
                //         $primary_c = $party->pty_comp_contact;
                //         $personal_c = $party->p_mobile;
                //         $secondary_c = $party->pty_contact_no;
                //         if ($fileName != '') {

                //             if ($contact_type == '3') {
                //                 if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                //                     $url = $txt_url."?number=".$secondary_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
            
                //                     $ch = curl_init();
                //                     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                     curl_setopt($ch,CURLOPT_URL,$url);
                //                     $result =  curl_exec($ch);
                                    
                //                     echo $result;
                //                     curl_close($ch);
                //                 }
                //             }elseif($contact_type == '2'){
                //                 if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                //                     $url = $txt_url."?number=".$primary_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
            
                //                     $ch = curl_init();
                //                     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                     curl_setopt($ch,CURLOPT_URL,$url);
                //                     $result =  curl_exec($ch);
                                    
                //                     echo $result;
                //                     curl_close($ch);
                //                 }

                //                 if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                //                     $url = $txt_url."?number=".$personal_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
            
                //                     $ch = curl_init();
                //                     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                     curl_setopt($ch,CURLOPT_URL,$url);
                //                     $result =  curl_exec($ch);
                                    
                //                     echo $result;
                //                     curl_close($ch);
                //                 }
                //             }else{
                //                 if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                //                     $url = $txt_url."?number=".$primary_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
            
                //                     $ch = curl_init();
                //                     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                     curl_setopt($ch,CURLOPT_URL,$url);
                //                     $result =  curl_exec($ch);
                                    
                //                     echo $result;
                //                     curl_close($ch);
                //                 }

                //                 if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                //                     $url = $txt_url."?number=".$personal_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
            
                //                     $ch = curl_init();
                //                     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                     curl_setopt($ch,CURLOPT_URL,$url);
                //                     $result =  curl_exec($ch);
                                    
                //                     echo $result;
                //                     curl_close($ch);
                //                 }
                //                 if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                //                     $url = $txt_url."?number=".$secondary_c."&type=media&message=".urlencode($ptyfinalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
            
                //                     $ch = curl_init();
                //                     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                     curl_setopt($ch,CURLOPT_URL,$url);
                //                     $result =  curl_exec($ch);
                                    
                //                     echo $result;
                //                     curl_close($ch);
                //                 }
                //             }
                //         } else {
                //             if ($contact_type == '3') {
                //                 if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                //                     $url = $txt_url."?number=".$secondary_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                //                     $ch = curl_init();
                //                     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                     curl_setopt($ch,CURLOPT_URL,$url);
                //                     $result =  curl_exec($ch);
                                    
                //                     echo $result;
                //                     curl_close($ch);
                //                 }
                //             }elseif($contact_type == '2'){
                //                 if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                //                     $url = $txt_url."?number=".$primary_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                //                     $ch = curl_init();
                //                     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                     curl_setopt($ch,CURLOPT_URL,$url);
                //                     $result =  curl_exec($ch);
                                    
                //                     echo $result;
                //                     curl_close($ch);
                //                 }

                //                 if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                //                     $url = $txt_url."?number=".$personal_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                //                     $ch = curl_init();
                //                     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                     curl_setopt($ch,CURLOPT_URL,$url);
                //                     $result =  curl_exec($ch);
                                    
                //                     echo $result;
                //                     curl_close($ch);
                //                 }
                //             }else{
                //                 if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                //                     $url = $txt_url."?number=".$primary_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                //                     $ch = curl_init();
                //                     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                     curl_setopt($ch,CURLOPT_URL,$url);
                //                     $result =  curl_exec($ch);
                                    
                //                     echo $result;
                //                     curl_close($ch);
                //                 }
                //                 if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                //                     $url = $txt_url."?number=".$personal_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                //                     $ch = curl_init();
                //                     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                     curl_setopt($ch,CURLOPT_URL,$url);
                //                     $result =  curl_exec($ch);
                                    
                //                     echo $result;
                //                     curl_close($ch);
                //                 }
                //                 if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                //                     $url = $txt_url."?number=".$secondary_c."&type=text&message=".urlencode($ptyfinalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                //                     $ch = curl_init();
                //                     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                     curl_setopt($ch,CURLOPT_URL,$url);
                //                     $result =  curl_exec($ch);
                                    
                //                     echo $result;
                //                     curl_close($ch);
                //                 }
                //             }
                //         }

                //         if($post->wakala_st == 1){
                //             $getwak = Partywakalacard::where('pty_id','=',$party->pty_id)->first();
                //             $filepathw = "image/service-master";
                //             $media = $urlPath.'/'.$filepathw.'/'.$getwak->file;

                //             if ($contact_type == '3') {
                //                 if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                //                     $url = $txt_url."?number=".$secondary_c."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
            
                //                     $ch = curl_init();
                //                     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                     curl_setopt($ch,CURLOPT_URL,$url);
                //                     $result =  curl_exec($ch);
                                    
                //                     echo $result;
                //                     curl_close($ch);
                //                 }
                //             }elseif($contact_type == '2'){
                //                 if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                //                     $url = $txt_url."?number=".$primary_c."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
            
                //                     $ch = curl_init();
                //                     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                     curl_setopt($ch,CURLOPT_URL,$url);
                //                     $result =  curl_exec($ch);
                                    
                //                     echo $result;
                //                     curl_close($ch);
                //                 }

                //                 if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                //                     $url = $txt_url."?number=".$personal_c."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
            
                //                     $ch = curl_init();
                //                     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                     curl_setopt($ch,CURLOPT_URL,$url);
                //                     $result =  curl_exec($ch);
                                    
                //                     echo $result;
                //                     curl_close($ch);
                //                 }
                //             }else{
                //                 if($party->pty_comp_contact != '' || $party->pty_comp_contact != '--None--'){
                //                     $url = $txt_url."?number=".$primary_c."&type=media&message=".urlencode($whsMesg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
            
                //                     $ch = curl_init();
                //                     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                     curl_setopt($ch,CURLOPT_URL,$url);
                //                     $result =  curl_exec($ch);
                                    
                //                     echo $result;
                //                     curl_close($ch);
                //                 }

                //                 if($party->p_mobile != '' || $party->p_mobile != '--None--'){
                //                     $url = $txt_url."?number=".$personal_c."&type=media&message=".urlencode($whsMesg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
            
                //                     $ch = curl_init();
                //                     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                     curl_setopt($ch,CURLOPT_URL,$url);
                //                     $result =  curl_exec($ch);
                                    
                //                     echo $result;
                //                     curl_close($ch);
                //                 }
                //                 if($party->pty_contact_no != '' || $party->pty_contact_no != '--None--'){
                //                     $url = $txt_url."?number=".$secondary_c."&type=media&message=".urlencode($whsMesg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
            
                //                     $ch = curl_init();
                //                     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                     curl_setopt($ch,CURLOPT_URL,$url);
                //                     $result =  curl_exec($ch);
                                    
                //                     echo $result;
                //                     curl_close($ch);
                //                 }
                //             }

                //         }

                //     }

                // }elseif($post->module_name == 'contact'){
                //     $lead_type = $post->lead_type;
                //     $lcs_id = $post->lcs_id;

                //     $remStr = ["[Business Type]","[Full Name]","[Mobile No]","[Email]","[ID]","[Country]","[City]","[Source]","[Job Title]","[Agency Name]","[Company Name]","[Industry]","[Created Date]"];

                //     $cont_dets = AllContact::where('user_id','=',$getAPI->staff_id)->where(function($query) use($lead_type,$lcs_id){
                //         if($lead_type != ''){
                //             $query->where('lead_type','=',$lead_type);
                //         }
                //         if($lcs_id != ''){
                //             $query->where('lcs_id','=',$lcs_id);
                //         }
                //     })->get();

                //     foreach ($cont_dets as $cont_det) {

                //         $repStr = [$cont_det->lead_type,$cont_det->full_name,$cont_det->mobile_no,$cont_det->email,$cont_det->id,$cont_det->country_id,$cont_det->city,$cont_det->source,$cont_det->job_title,$cont_det->office_name,$cont_det->company_name,$cont_det->indust_id,$cont_det->created_at];
                //         $final_msg = str_replace($remStr,$repStr,$post->msgBody);
                //         // $whsMesg = "Dear ".$cont_det->full_name."\n".$post->msgBody;
                //         if($fileName != ''){
                //             if($cont_det->mobile_no != ''){
                //                 $url = $txt_url."?number=".$cont_det->mobile_no."&type=media&message=".urlencode($final_msg)."&media_url=".$media."&filename=".$this->get_camp_list['file']."&instance_id=".$ins."&access_token=".$api;
            
                //                 $ch = curl_init();
                //                 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                 curl_setopt($ch,CURLOPT_URL,$url);
                //                 $result =  curl_exec($ch);
                                
                //                 echo $result;
                //                 curl_close($ch);
                //             }

                //             if($cont_det->phone0 != ''){
                //                 $url = $txt_url."?number=".$cont_det->phone0."&type=media&message=".urlencode($final_msg)."&media_url=".$media."&filename=".$this->get_camp_list['file']."&instance_id=".$ins."&access_token=".$api;
            
                //                 $ch = curl_init();
                //                 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                 curl_setopt($ch,CURLOPT_URL,$url);
                //                 $result =  curl_exec($ch);
                                
                //                 echo $result;
                //                 curl_close($ch);
                //             }

                //             if($cont_det->phone1 != ''){
                //                 $url = $txt_url."?number=".$cont_det->phone1."&type=media&message=".urlencode($final_msg)."&media_url=".$media."&filename=".$this->get_camp_list['file']."&instance_id=".$ins."&access_token=".$api;
            
                //                 $ch = curl_init();
                //                 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                 curl_setopt($ch,CURLOPT_URL,$url);
                //                 $result =  curl_exec($ch);
                                
                //                 echo $result;
                //                 curl_close($ch);
                //             }

                //             if($cont_det->phone2 != ''){
                //                 $url = $txt_url."?number=".$cont_det->phone2."&type=media&message=".urlencode($final_msg)."&media_url=".$media."&filename=".$this->get_camp_list['file']."&instance_id=".$ins."&access_token=".$api;
            
                //                 $ch = curl_init();
                //                 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                 curl_setopt($ch,CURLOPT_URL,$url);
                //                 $result =  curl_exec($ch);
                                
                //                 echo $result;
                //                 curl_close($ch);
                //             }

                //         }else{
                //             if($cont_det->mobile_no != ''){
                //                 $url = $txt_url."?number=".$cont_det->mobile_no."&type=text&message=".urlencode($final_msg)."&instance_id=".$ins."&access_token=".$api;
                //                 $ch = curl_init();
                //                 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                 curl_setopt($ch,CURLOPT_URL,$url);
                //                 $result =  curl_exec($ch);
                                
                //                 echo $result;
                //                 curl_close($ch);
                //             }

                //             if($cont_det->phone0 != ''){
                //                 $url = $txt_url."?number=".$cont_det->phone0."&type=text&message=".urlencode($final_msg)."&instance_id=".$ins."&access_token=".$api;
                //                 $ch = curl_init();
                //                 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                 curl_setopt($ch,CURLOPT_URL,$url);
                //                 $result =  curl_exec($ch);
                                
                //                 echo $result;
                //                 curl_close($ch);
                //             }

                //             if($cont_det->phone1 != ''){
                //                 $url = $txt_url."?number=".$cont_det->phone1."&type=text&message=".urlencode($final_msg)."&instance_id=".$ins."&access_token=".$api;
                //                 $ch = curl_init();
                //                 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                 curl_setopt($ch,CURLOPT_URL,$url);
                //                 $result =  curl_exec($ch);
                                
                //                 echo $result;
                //                 curl_close($ch);
                //             }

                //             if($cont_det->phone2 != ''){
                //                 $url = $txt_url."?number=".$cont_det->phone2."&type=text&message=".urlencode($final_msg)."&instance_id=".$ins."&access_token=".$api;
                //                 $ch = curl_init();
                //                 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                //                 curl_setopt($ch,CURLOPT_URL,$url);
                //                 $result =  curl_exec($ch);
                                
                //                 echo $result;
                //                 curl_close($ch);
                //             }

                //         }
                //     }

                // }
            }
        }

        return 0;
    }
}
