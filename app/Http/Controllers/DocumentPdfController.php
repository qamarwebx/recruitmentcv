<?php

namespace App\Http\Controllers;
use App\AdminModel\Candidate;
use App\AdminModel\CandidateService;
use App\AdminModel\Nationality;
use App\AdminModel\Party;
use App\AdminModel\Employee;
use App\AdminModel\Profession;
use App\AdminModel\ReligionCast;
use App\AdminModel\Service;
use App\AdminModel\ServiceDetails;
use App\AdminModel\ServicePaymentStatus;
use App\AdminModel\ServiceStatus;
use App\AdminModel\EmployeeCandidate;
use App\AdminModel\DelEmpFileNo;
use App\AdminModel\DelEmpRefNo;
use App\AdminModel\Visa;
use App\AdminModel\Medicle;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Redirect;
use Session;
use PDF;
use Config;


class DocumentPdfController extends Controller {

	public function get_visa_form()
	{
		PDF::SetAuthor('Qamr International');
		PDF::SetSubject('Qamrintl');
		PDF::SetKeywords('doc, PDF, visa form, qamrintl, guide');
		PDF::SetTitle('Visa Form');

		PDF::setHeaderFont(Array('helvetica', '', '10'));

		// set default monospaced font
		PDF::SetDefaultMonospacedFont('courier');

		// set margins
		PDF::SetMargins('15', '27', '15');
		PDF::SetHeaderMargin(0);
		PDF::SetFooterMargin(0);

		// remove default footer
		PDF::setPrintFooter(false);

		// set auto page breaks
		PDF::SetAutoPageBreak(TRUE, '25');

		// set image scale factor
		PDF::setImageScale('1.25');

		PDF::AddPage();
		$img_file = base_path().'/public/tcpdf-images/submission_form.jpg';
		PDF::Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
		

		PDF::Output('Visa form.pdf');
	}

	public function emp_visa_data(Request $request)
	{
		$id = $request->input('emp_id');
		$emp = DB::table('qr_employee_tbl as emp')
			->leftjoin('qr_party_tbl as party', 'party.pty_id', '=', 'emp.pty_id')
			->leftjoin('qr_emp_visa_tbl as visa', 'emp.visa_id', '=', 'visa.visa_id')
			->leftjoin('qr_sector as sc', 'sc.sc_id', '=', 'emp.sc_id')
			->leftjoin('qr_country as ct', 'ct.country_id', '=', 'emp.country')
			->leftjoin('qr_profession_tbl', \DB::raw("FIND_IN_SET(qr_profession_tbl.prof_id,emp.profession)"),">",\DB::raw("'0'"))
			->leftjoin('qr_submission_place_tbl', \DB::raw("FIND_IN_SET(qr_submission_place_tbl.sub_pl_id,emp.sub_place)"),">",\DB::raw("'0'"))
	     	->select("emp.*","visa.*","party.*","sc.*","ct.*",\DB::raw("GROUP_CONCAT(qr_profession_tbl.prof_eng_name) as list_prof"),\DB::raw("GROUP_CONCAT(DISTINCT(qr_submission_place_tbl.sub_pl_name)) as place_name"))
	     	->groupBy('emp.emp_id')
			->where('emp.emp_id','=',$id)
			->first();

	

			PDF::SetAuthor('Qamr International');
		PDF::SetSubject('Qamrintl');
		PDF::SetKeywords('doc, PDF, visa form, qamrintl, guide');
		PDF::SetTitle('Visa Details');

		PDF::SetHeaderData('', '', 'VISA DETAILS'.' 001', 'by qamrintl', array(0,64,255), array(0,64,128));


		PDF::SetFont('helvetica', '', 10, '', false);
		PDF::AddPage();


		PDF::setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));

		PDF::SetFont('helvetica', 'BI', 18);
		PDF::Cell(0, 5, 'Visa Details', 0, 1, 'C');
		PDF::Ln(10);

		PDF::SetFont('helvetica', '', 10);

		PDF::Cell(55, 35, 'Party Name:');
		PDF::Cell(50, 35, $emp->pty_ag_name);
		PDF::Ln(10);

		PDF::Cell(55, 35, 'File No:');
		PDF::Cell(50, 35,$emp->emp_file_no);
		PDF::Ln(10);

		PDF::Cell(55, 35, 'Sponsor Full Name:');
		PDF::Cell(50, 35,$emp->spon_nm_eng);
		PDF::Ln(10);

		PDF::Cell(55, 35, 'Sponsor Arabic Name:');
		PDF::Cell(50, 35,$emp->spon_nm_arab);
		PDF::Ln(10);

		PDF::Cell(55, 35, 'Visa No:');
		PDF::Cell(50, 35,$emp->emp_visa_no);
		PDF::Ln(10);

		PDF::Cell(55, 35, 'Id No:');
		PDF::Cell(50, 35,$emp->emp_id_no);
		PDF::Ln(10);

		PDF::Cell(55, 35, 'Visa Date:');
		PDF::Cell(50, 35,$emp->emp_visa_date);
		PDF::Ln(10);

		PDF::Cell(55, 35, 'Submission Place:');
		PDF::Cell(50, 35,$emp->place_name);
		PDF::Ln(10);

		PDF::Cell(55, 35, 'Country:');
		PDF::Cell(50, 35,$emp->country_name);
		PDF::Ln(10);

		PDF::Cell(55, 35, 'Opening:');
              $op =  explode(",", $emp->openings);
              $pr = explode(",", $emp->profession);
              $openings = '';
                for($i=0;$i<count($op);$i++)
                {

                  $prname =  \App\AdminModel\Profession::find($pr[$i]);
                  PDF::Cell(50, 35, $prname->prof_eng_name.' - '.$op[$i]);
                  PDF::Ln(5);
                  PDF::Cell(55, 35, '');
                }

		
		PDF::Ln(7);

		PDF::Cell(55, 35, 'Sector Name:');
		PDF::Cell(50, 35,$emp->sector_name);
		PDF::Ln(10);

		PDF::Cell(55, 35, 'Mobile No:');
		PDF::Cell(50, 35,$emp->emp_mobile_no);
		PDF::Ln(10);

		PDF::Cell(55, 35, 'Description:');
		PDF::Cell(50, 35,$emp->emp_description);
		PDF::Ln(10);

		PDF::Cell(55, 35, 'File Created By:');
		PDF::Cell(50, 35, 'Admin');
		PDF::Ln(10);

		PDF::Cell(55, 35, 'File Created Date:');
		PDF::Cell(50, 35,date('d-m-Y', strtotime($emp->created_at)));
		PDF::Ln(10);

// ---------------------------------------------------------

//Close and output PDF document
		PDF::Output($emp->emp_file_no.'_visa_details.pdf','D');
	}
	
	public function submission_visa(Request $request)
	{
		PDF::SetAuthor('Qamr International');
		PDF::SetSubject('Qamrintl');
		PDF::SetKeywords('doc, PDF, visa form, qamrintl, guide');
		PDF::SetTitle('Visa Form');

		PDF::setHeaderFont(Array('helvetica', '', '10'));

		// set default monospaced font
		PDF::SetDefaultMonospacedFont('courier');

		// set margins
		PDF::SetMargins('15', '27', '15');
		PDF::SetHeaderMargin(0);
		PDF::SetFooterMargin(0);

		// remove default footer
		PDF::setPrintFooter(false);

		// set auto page breaks
		PDF::SetAutoPageBreak(TRUE, '25');

		// set image scale factor
		PDF::setImageScale('1.25');

		PDF::AddPage();
		$img_file = base_path().'/public/tcpdf-images/submission_form.jpg';
		PDF::Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
		

		PDF::Output('Visa form.pdf');
	}


}