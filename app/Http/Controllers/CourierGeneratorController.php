<?php

namespace App\Http\Controllers;

use App\AdminModel\Branch;
use App\AdminModel\Courier;
use App\AdminModel\Courierclone;
use App\AdminModel\CourierDetails;
use App\AdminModel\Party;
use App\AdminModel\PartyAddress;
use Illuminate\Http\Request;
use TCPDF;

class CourierGeneratorController extends Controller
{
    public function printAddress(Request $request,$id)
    {
        // $id = $request->courier_id;
        
        
        // Courier
        // $courier_info = Courier::find($id);
        $courier_info = Courierclone::find($id);
        
        // Courier Service Detail
        $courier_det = CourierDetails::where('courier_id','=',$id)->first();

        // Courier From Address
        $br_det = Branch::where('br_id','=',$courier_info->courier_from)->first();

        // party address none and branch to add
        // if($courier_info->courier_to != 16 && $courier_info->pty_id != 200){
        //     // check if Address ID is available in Courier Service Details
        //     if ($courier_det->partyadd_id != '') {
        //         $party_add = PartyAddress::find($courier_det->partyadd_id);
        //         $party = Party::find($party_add->pty_id);
        //     }else{
        //         $party_add = PartyAddress::find($courier_info->courier_to);
        //         $party = Party::find($party_add->pty_id);
        //     }
        // }else{
        //     $toBranch = Branch::where('br_id','=',$courier_info->courier_to_br_id)->first();
        // }

        if($courier_info->add_type == 'branch'){
            $toBranch = Branch::where('br_id','=',$courier_info->courier_to_br_id)->first();
        }elseif ($courier_info->add_type == 'party') {
            $party = Party::find($courier_info->pty_id);
            if ($courier_info->courier_to != 16) {
                if ($courier_det->partyadd_id != '') {
                    $party_add = PartyAddress::find($courier_det->partyadd_id);
                }else{
                    $party_add = PartyAddress::find($courier_info->courier_to);
                }
            }else{
                $partyAdd = $courier_info->courier_to_add;
            }
        }elseif ($courier_info->add_type == 'candidate') {
            $cand_full_name = $courier_info->cand_fullname;
            $cand_addr = $courier_info->cand_to_add_nc;
        }


        // dd($party_add);

        // $pdf = new TCPDF('L', 'mm', [180,79], true, 'UTF-8', false);
        // $pdf = new TCPDF('L','mm',[110,79],true,'UTF-8', false);
            $pdf = new TCPDF('L','mm',[123,123],true,'UTF-8',false);
        // $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator('QAMR INTERNATIONAL');
        $pdf->SetTitle('Print To Address');
        $pdf->SetSubject('Print Address');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetFooterMargin(0);
        $pdf->SetAutoPageBreak(TRUE, 0);

        // set margins
        $pdf->SetMargins(6, 6, 6,false);

        // set font
        $pdf->SetFont('times', '', 13);

        $pdf->AddPage();

        // set cell padding
        $pdf->setCellPaddings(2, 2, 2, 2);

        // set cell margins
        $pdf->setCellMargins(1, 1, 1, 1);
        
        // set color for background
        $pdf->SetFillColor(255, 255, 255);

        // For to address
        // if($courier_info->courier_to != 16 && $courier_info->pty_id != 200){
        //     $print_to = "To,\n".$party->pty_ag_name."\n".$party_add->address."\nPostal Code - ".$party_add->pincode;            
        // }else{
        //     if (isset($toBranch)) {
        //         $print_to = "To,\n".$toBranch->br_name."\n".$toBranch->ow_address.", ".$toBranch->city."\nPostal Code - ".$toBranch->pincode;
        //     } else {
        //         $print_to = "To,\nParty or Branch address not found!";
        //     }
            

        // }

        if($courier_info->add_type == 'branch'){
            $branch_name = $toBranch->br_name;
            // $print_to = "To,\n".$toBranch->br_name."\n".$toBranch->ow_address.", ".$toBranch->city."\nPostal Code - ".$toBranch->pincode;
            $print_to = "To,\n".$branch_name."\n".$toBranch->ow_address.", ".$toBranch->city."\nPostal Code - ".$toBranch->pincode;
            
        }elseif($courier_info->add_type == 'party'){
            if($courier_info->courier_to != 16){
                if($party_add->office_name != ''){
                    $party_office_name = $party_add->office_name;
                }else{
                    $party_office_name = $party->pty_ag_name;
                }
                $print_to = "To,\n".$party_office_name."\n".$party_add->name."\n".$party_add->address."\nPostal Code - ".$party_add->pincode;
            }else{
                $print_to = "To,\n".$party->pty_ag_name."\n".$partyAdd;
            }
        }elseif($courier_info->add_type == 'candidate'){
            $print_to = "To,\n".$cand_full_name."\n".$cand_addr;
        }


    

        // From Address
        $print_from = "From,\nQamr International\n".$br_det->ow_address.", ".$br_det->city."\nPostal Code - ".$br_det->pincode;

        // Mulitcell text
        // $pdf->MultiCell(96, 5, $print_to, 1, 'L', 1, 0, '', '', true);
        // $pdf->MultiCell(96, 5, $print_from, 1, 'L', 0, 1, '', '', true);
        $pdf->StartTransform();
        $pdf->Rotate(270, 60, 68);
        $pdf->MultiCell(96, 5, $print_to, 0, 'L', 1, 0, '', '', true);
        $pdf->StopTransform();
        $pdf->ln(45);
        $pdf->StartTransform();
        $pdf->Rotate(270, 60, 68);
        $pdf->MultiCell(96, 5, $print_from, 0, 'L', 1, 0, '', '', true);
        $pdf->StopTransform();
        // Set some content to print
        // $html = <<<EOD
        //         <p><strong>To,</strong><br>$party->pty_ag_name<br>$party_add->address<br>Postal Code - $party_add->pincode</p>
        // EOD;

        // Print text using writeHTMLCell()
        // $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
        
        //Close and output PDF document
        $pdf->Output($courier_info->pass_no.'.pdf', 'I');

    }
}
