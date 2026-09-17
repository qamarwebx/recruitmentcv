<?php

namespace App\Http\Controllers;

use App\AdminModel\Employee;
use App\AdminModel\EmployeeCandidate;
use App\AdminModel\ServiceDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Milon\Barcode\DNS1D;
// use TCPDF;
use PDF;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;

use TCPDF;

class BarcodeGeneratorController extends Controller
{
    public function barcodegen(Request $request)
    {
        // dd($request);

        $cand_id = $request->barcode_visa_id;

        // Get Mofa No
        $mofa_d = ServiceDetails::where('cand_id','=',$cand_id)->first();
        // Get Visa Det
        $emp_c = EmployeeCandidate::where('cand_id','=',$cand_id)->first();
        $visa_d = Employee::where('emp_id','=',$emp_c->emp_id)->first();
        
        // $cand_info = DB::table('qr_candidate_tbl as cand')
        // ->leftJoin('qr_services_details as ser_det','cand.cand_id','=','ser_det.cand_id')
        // ->leftJoin('qr_employee_tbl as emp','cand.pty_id','=','emp.pty_id')
        // ->select('cand.cand_id','ser_det.mofa','emp.emp_visa_no')
        // ->first();

        $mofa_data = unserialize($mofa_d->mofa);
        $mofa_no = $mofa_data['mofa_no'];
        $visa_no = $visa_d->emp_visa_no;

        // create new PDF document
        // $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // $pdf = new TCPDF('L', 'mm', [190,99], true, 'UTF-8', false);
        $pdf = new TCPDF('L', 'mm', [203,101], true, 'UTF-8', false);

    
        
        // set document information
        $pdf->SetCreator('QAMR INTERNATIONAL');
        $pdf->SetTitle($mofa_no);
        $pdf->SetSubject('Barcode');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');



        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetFooterMargin(0);
        $pdf->SetAutoPageBreak(TRUE, 0);
    

        // set margins

        $pdf->SetMargins(6, 6, 6,false);
        // $pdf->SetHeaderMargin(0);
        // $pdf->SetFooterMargin(0);

        

        // PRINT VARIOUS 1D BARCODES

        // define barcode style
        $style = array(
            'position' => '',
            'align' => '',
            'stretch' => true,
            'fitwidth' => false,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => '',
            'vpadding' => '',
            // 'fgcolor' => array(0,0,128),
            // 'bgcolor' => array(255,255,128),
            'text' => true,
            'label' => $mofa_no,
            'font' => 'helvetica',
            'fontsize' => 30,
            'stretchtext' => 0,

        );

        


        // add a page ----------
        $pdf->AddPage();

        

        // CODE 39 EXTENDED + CHECKSUM
        $style['label'] = 'Mofa No '.$mofa_no;
        $pdf->write1DBarcode($mofa_no, 'C128A', '', '', '', 45, '0.4', $style, 'N');
        $pdf->Ln(2);
        $style['label'] = 'Visa No '.$visa_no;
        $style['fontweight'] = 'bold';
        $pdf->write1DBarcode($visa_no, 'C128A', '', '', '', 45, '0.4', $style, '');
        
        
    

        // ---------------------------------------------------------

        //Close and output PDF document
        $pdf->Output($mofa_no.'.pdf', 'I');



        // return view('barcode.barcode',compact('cand_info','mofa_data'));
    }
}
