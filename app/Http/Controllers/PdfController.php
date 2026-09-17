<?php

namespace App\Http\Controllers;

use App\User;
use App\AdminModel\Staff;
use App\AdminModel\Lead;
use App\AdminModel\City;
use App\AdminModel\Country;
use App\AdminModel\Note;
use App\AdminModel\Task;
use App\AdminModel\Profession;
use App\AdminModel\Nationality;
use App\AdminModel\ReligionCast;
use App\AdminModel\Appoint;
use App\AdminModel\CandidateService;
use App\AdminModel\Medicle;
use App\AdminModel\ServiceStatus;
use App\AdminModel\ServiceDetails;
use App\AdminModel\ServicePaymentStatus;
use App\AdminModel\Files;
use App\AdminModel\Type;
use App\AdminModel\CandidateViewNotes;
use App\AdminModel\Timeline;
use App\AdminModel\Label;
use App\AdminModel\Candidate;
use App\AdminModel\Emigration;
use App\AdminModel\Service;
use App\AdminModel\Party;
use App\AdminModel\Todo;
use App\AdminModel\JobSeeker;
use Illuminate\Support\Facades\Hash;
use Session;
use Auth;
use Config;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PDF;
use TCPDF_FONTS;

class PdfController extends Controller
{

	public function nationalIDCR($id){
		$sign_path_01 = base_path().'/public/image/emigration/employer';
		$emig_data = Emigration::find($id);

		PDF::SetCreator('Qamr International');
		PDF::SetAuthor('Qamr International');
		PDF::SetTitle('National ID with CR Copy');
		PDF::SetSubject('Declaration Form');
		PDF::SetKeywords('Qamr, PDF, visa, form, guide');
		PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		PDF::SetHeaderMargin(0);
		PDF::SetFooterMargin(0);
		PDF::setPrintFooter(false);
		PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
		PDF::SetTextColor(61, 62, 64);

		PDF::AddPage('','A4');


		// PDF::setJPEGQuality(75);
	
		if($emig_data->crcopy != ''){
			PDF::Image($sign_path_01.'/'.$emig_data->crcopy, 30, 30, '150', '', '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
			// PDF::ImageSVG($sign_path_01.'/'.$data_cand->cand_sign,15,185,'28','16','','','',0,false);
		}

		PDF::AddPage('','A4');

		// PDF::setJPEGQuality(75);

		

		if($emig_data->national_id != ''){
			PDF::Image($sign_path_01.'/'.$emig_data->national_id, 30, 30, '150', '', '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
			// PDF::ImageSVG($sign_path_01.'/'.$data_cand->cand_sign,15,185,'28','16','','','',0,false);
		}

		ob_end_clean();
		PDF::Output($emig_data->pass_no.'_natinalid.pdf');
	}

	public function nationalID($id){
		$sign_path_01 = base_path().'/public/image/emigration/employer';
		$emig_data = Emigration::find($id);

		PDF::SetCreator('Qamr International');
		PDF::SetAuthor('Qamr International');
		PDF::SetTitle('National ID');
		PDF::SetSubject('Declaration Form');
		PDF::SetKeywords('Qamr, PDF, visa, form, guide');
		PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		PDF::SetHeaderMargin(0);
		PDF::SetFooterMargin(0);
		PDF::setPrintFooter(false);
		PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
		PDF::SetTextColor(61, 62, 64);

		PDF::AddPage('','A4');


		// PDF::setJPEGQuality(75);
	
		if($emig_data->national_id != ''){
			PDF::Image($sign_path_01.'/'.$emig_data->national_id, 30, 30, '150', '', '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
			// PDF::ImageSVG($sign_path_01.'/'.$data_cand->cand_sign,15,185,'28','16','','','',0,false);
		}

		ob_end_clean();
		PDF::Output($emig_data->pass_no.'_natinalid.pdf');
	}

	public function passcopy($id){
		$sign_path_01 = base_path().'/public/image/emigration/employer';
		$emig_data = Emigration::find($id);

		PDF::SetCreator('Qamr International');
		PDF::SetAuthor('Qamr International');
		PDF::SetTitle('Passport Copy');
		PDF::SetSubject('Passport Copy');
		PDF::SetKeywords('Qamr, PDF, visa, form, guide');
		PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		PDF::SetHeaderMargin(0);
		PDF::SetFooterMargin(0);
		PDF::setPrintFooter(false);
		PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
		PDF::SetTextColor(61, 62, 64);

		PDF::AddPage('','A4');


		// PDF::setJPEGQuality(75);
	
		if($emig_data->pass_copy != ''){
			PDF::Image($sign_path_01.'/'.$emig_data->pass_copy, 30, 30, '150', '', '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
			// PDF::ImageSVG($sign_path_01.'/'.$data_cand->cand_sign,15,185,'28','16','','','',0,false);
		}

		ob_end_clean();
		PDF::Output($emig_data->pass_no.'_passport.pdf');
	}

	public function visacopy($id){
		$sign_path_01 = base_path().'/public/image/emigration/employer';
		$emig_data = Emigration::find($id);

		PDF::SetCreator('Qamr International');
		PDF::SetAuthor('Qamr International');
		PDF::SetTitle('Visa Copy');
		PDF::SetSubject('Visa Copy');
		PDF::SetKeywords('Qamr, PDF, visa, form, guide');
		PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		PDF::SetHeaderMargin(0);
		PDF::SetFooterMargin(0);
		PDF::setPrintFooter(false);
		PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
		PDF::SetTextColor(61, 62, 64);

		PDF::AddPage('','A4');


		// PDF::setJPEGQuality(75);
	
		if($emig_data->visa_copy != ''){
			PDF::Image($sign_path_01.'/'.$emig_data->visa_copy, 30, 30, '150', '', '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
			// PDF::ImageSVG($sign_path_01.'/'.$data_cand->cand_sign,15,185,'28','16','','','',0,false);
		}

		ob_end_clean();
		PDF::Output($emig_data->pass_no.'_visa_copy.pdf');
	}

	public function workagreement($id){
		
		$image1 = base_path().'/public/tcpdf-images/agreement_01.jpg';
		$image2 = base_path().'/public/tcpdf-images/agreement_02.jpg';	
		$image3 = base_path().'/public/tcpdf-images/agreement_03.jpg';
		$image4 = base_path().'/public/tcpdf-images/agreement_04.jpg';	
		
		$sign_path_01 = base_path().'/public/image/emigration/employer';

		$data_cand = DB::table('emigrations as emigr')
		->join('emigration_statuses as emigst','emigst.cand_id','=','emigr.cand_id')
		->leftjoin('qr_candidate_tbl as cand','cand.cand_id','=','emigr.cand_id')
		->leftjoin('qr_services_details as ser_d','ser_d.cand_id','=','emigr.cand_id')
		->leftjoin('qr_employee_candidate as emp_cand','emp_cand.cand_id','=','emigr.cand_id')
		->leftjoin('qr_employee_tbl as emp','emp.emp_id','=','emp_cand.emp_id')
		->leftjoin('qr_party_tbl as pty','pty.pty_id','=','cand.pty_id')
		->leftjoin('qr_sector as sector','sector.sc_id','=','emp.sc_id')
		->leftjoin('qr_country as country','country.country_id','=','emp.country')
		->leftjoin('qr_submission_place_tbl as submpl','submpl.sub_pl_id','=','emp.sub_place')
		->leftjoin('qr_candidate_place_issue as candpl','candpl.id','=','cand.cand_place_issue')
		->leftjoin('qr_profession_tbl as proff','proff.prof_id','=','emp_cand.cand_profession')
		->select('emigr.*','proff.prof_eng_name','country.country_name','emp.emp_id_no as IDNO','emp.emp_visa_no as visaNO','cand.cand_date_issue','ser_d.visa_stamped_date as visa_date','emp.wakala_type as waktype','sector.sector_name','submpl.sub_pl_name','candpl.place as candplace')
		->where('emigr.id','=',$id)
		->first();

		$issue_date = date('d/m/Y',strtotime($data_cand->cand_date_issue));

		if($data_cand->agreement_date != ''){
			$visa_issue_date = date('d-m-Y',strtotime($data_cand->agreement_date));
		}else{
			$visa_issue_date = date('d/m/Y');

			// update in emigration details
			$post = Emigration::find($id);
			$post->agreement_date = date('Y-m-d');
			$post->save();
		}

		// $visa_issue_date = date('d/m/y',strtotime($data_cand->visa_date));

		$profession = $data_cand->prof_eng_name;
		$salary = $data_cand->salary;

		PDF::SetCreator('Qamr International');
		PDF::SetAuthor('Qamr International');
		PDF::SetTitle('Work Agreement Form');
		PDF::SetSubject('Declaration Form');
		PDF::SetKeywords('Qamr, PDF, visa, form, guide');
		PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		PDF::SetHeaderMargin(0);
		PDF::SetFooterMargin(0);
		PDF::setPrintFooter(false);
		PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
		// PDF::SetTextColor(61, 62, 64);

		PDF::AddPage('P','A4');
		// PDF::AddPage('','mm',array(215.9,279.4),true, 'UTF-8', false);

		PDF::setImageScale(1);
		PDF::SetAutoPageBreak(false, 0);
		PDF::Image($image1, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);

		if($data_cand->waktype == 'company'){
			$employer_name = $data_cand->company_name;
		}else{
			$employer_name = $data_cand->emp_name_eng;
		}

		PDF::SetFont('helvetica', 'b', 10);
		PDF::SetXY((2) ,(74));
		PDF::Cell(30,3,strtoupper($employer_name),'0','1','L');

		PDF::SetFont('helvetica', 'b', 10);
		PDF::SetXY((25) ,(82));
		PDF::Cell(30,3,strtoupper($data_cand->contact_number),'0','1','L');

		PDF::SetFont('helvetica', 'b', 10);
		PDF::SetXY((15) ,(86));
		PDF::Cell(30,3,strtoupper($data_cand->visaNO),'0','1','L');

		PDF::SetFont('helvetica', 'b', 10);
		PDF::SetXY((23) ,(91));
		PDF::Cell(30,3,strtoupper($visa_issue_date),'0','1','L');

		PDF::SetFont('helvetica', 'b', 10);
		PDF::SetXY((8) ,(99));
		PDF::Cell(30,3,strtoupper($data_cand->cand_name),'0','1','L');

		PDF::SetFont('helvetica', 'b', 10);
		PDF::SetXY((64) ,(103));
		PDF::Cell(30,3,strtoupper($data_cand->pass_no),'0','1','L');

		PDF::SetFont('helvetica', 'b', 10);
		PDF::SetXY((18) ,(108));
		PDF::Cell(30,3,strtoupper($data_cand->candplace),'0','1','L');

		PDF::SetFont('helvetica', 'b', 10);
		PDF::SetXY((47) ,(108));
		PDF::Cell(30,3,strtoupper($issue_date),'0','1','L');


		// break address if greater than 30 chars
		// $add1 = substr($data_cand->cand_address,0,30);
		// $add2 = substr($data_cand->cand_address,30);

		PDF::SetFont('helvetica', 'b', 10);
		PDF::SetXY((21) ,(112));
		PDF::Cell(30,3,strtoupper($data_cand->cand_address1),'0','1','L');
		
		PDF::SetFont('helvetica', 'b', 10);
		PDF::SetXY((2) ,(116));
		PDF::Cell(30,3,strtoupper($data_cand->cand_address2),'0','1','L');

		PDF::SetFont('helvetica', 'b', 10);
		PDF::SetXY((44) ,(141));
		PDF::Cell(30,3,strtoupper($profession),'0','1','L');

		PDF::SetFont('helvetica', 'b', 10);
		PDF::SetXY((14) ,(197));
		PDF::Cell(30,3,strtoupper($salary),'0','1','L');

		// PDF::AddPage('','mm',array(215.9,279.4),true, 'UTF-8', false);
		PDF::AddPage('','A4');

		PDF::setImageScale(1);
		PDF::SetAutoPageBreak(false, 0);
		PDF::Image($image2, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);

		// PDF::AddPage('P','mm',array(215.9,279.4),true, 'UTF-8', false);
		PDF::AddPage('','A4');
		PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);

		PDF::setImageScale(1);
		PDF::SetAutoPageBreak(false, 0);
		PDF::Image($image3, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);

		// PDF::AddPage('P','mm',array(215.9,279.4),true, 'UTF-8', false);
		PDF::AddPage('','A4');

		PDF::setImageScale(1);
		PDF::SetAutoPageBreak(false, 0);
		PDF::Image($image4, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);

		// signature
		// PDF::setJPEGQuality(75);
	
		if($data_cand->cand_sign != ''){
			PDF::Image($sign_path_01.'/'.$data_cand->cand_sign, 10, 178, 28, 16, '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
			// PDF::ImageSVG($sign_path_01.'/'.$data_cand->cand_sign,10,185,'28','16','','','',0,false);
		}
		PDF::SetFont('helvetica', '', 11);
		PDF::SetXY((7) ,(204));
		PDF::Cell(30,3,strtoupper($data_cand->cand_name),'0','1','L');

		if($data_cand->sign != ''){
			PDF::Image($sign_path_01.'/'.$data_cand->sign, 10, 143, 40, 20, '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
			// PDF::ImageSVG($sign_path_01.'/'.$data_cand->sign,10,145,'40','20','','','',0,false);
		}

		if($data_cand->waktype == 'company'){
			PDF::Image($sign_path_01.'/'.$data_cand->stamp, 40, 147, 40, 20, '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
			// PDF::ImageSVG($sign_path_01.'/'.$data_cand->stamp,40,150,'40','20','','','',0,false);
		}

		PDF::SetFont('helvetica', '', 11);
		PDF::SetXY((7) ,(171));
		PDF::Cell(30,3,strtoupper($data_cand->emp_name_eng),'0','1','L');

		ob_end_clean();
		PDF::Output($data_cand->pass_no.'_workagreement.pdf');
	}

	public function declaration($id){
		$image1 = base_path().'/public/tcpdf-images/declaration_1.jpg';
		$image2 = base_path().'/public/tcpdf-images/declaration_2.jpg';		
		$sign_path_01 = base_path().'/public/image/emigration/employer';		

		// $myfont = base_path().'/public/fonts/mangal_regular/mangal_regular.ttf';
		$myfont = base_path().'/public/fonts/custom_fonts/Devanagari.ttf';

		$data_cand = DB::table('emigrations as emigr')
			->join('emigration_statuses as emigst','emigst.cand_id','=','emigr.cand_id')
			->leftjoin('qr_candidate_tbl as cand','cand.cand_id','=','emigr.cand_id')
			->leftjoin('qr_employee_candidate as emp_cand','emp_cand.cand_id','=','emigr.cand_id')
			->leftjoin('qr_employee_tbl as emp','emp.emp_id','=','emp_cand.emp_id')
			->leftjoin('qr_party_tbl as pty','pty.pty_id','=','cand.pty_id')
			->leftjoin('qr_sector as sector','sector.sc_id','=','emp.sc_id')
			->leftjoin('qr_country as country','country.country_id','=','emp.country')
			->leftjoin('qr_submission_place_tbl as submpl','submpl.sub_pl_id','=','emp.sub_place')
			->select('emigr.*','country.country_name','emp.emp_id_no as IDNO','emp.wakala_type as waktype','sector.sector_name','submpl.sub_pl_name')
			->where('emigr.id','=',$id)
			->first();

		PDF::SetCreator('Qamr International');
		PDF::SetAuthor('Qamr International');
		PDF::SetTitle('Declaration Form');
		PDF::SetSubject('Declaration Form');
		PDF::SetKeywords('Qamr, PDF, visa, form, guide');
		PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		PDF::SetHeaderMargin(0);
		PDF::SetFooterMargin(0);
		PDF::setPrintFooter(false);
		PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
		// PDF::setFontSubsetting(true);

		// $font_name =  TCPDF_FONTS::addTTFfont($myfont,'TrueTypeUnicode','',32);

		$hind_font = TCPDF_FONTS::addTTFfont($myfont,'TrueTypeUnicode','',96);

		// $html = 

		PDF::AddPage('','A4');
		PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);

		PDF::setImageScale(1);
		PDF::SetAutoPageBreak(false, 0);
		PDF::Image($image1, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);

		// PDF::SetFont($font_name, '', 13);
		// PDF::SetFont($hind_font,'',13);
		PDF::SetFont('freesans','',13);
		PDF::SetXY((41) ,(90));
		// PDF::writeHTML($html,true,false,true,false,'');
		PDF::Cell(30,3,$data_cand->cand_hindi_name,'0','1','L');

		PDF::SetFont('helvetica', '', 12);
		PDF::SetXY((100) ,(90));
		PDF::Cell(30,3,strtoupper($data_cand->pass_no),'0','1','L');


		// signature
		// PDF::setJPEGQuality(75);
	
		if($data_cand->cand_sign != ''){
			PDF::Image($sign_path_01.'/'.$data_cand->cand_sign, 15, 185, 28, 16, '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
			// PDF::ImageSVG($sign_path_01.'/'.$data_cand->cand_sign,15,185,'28','16','','','',0,false);
		}

		PDF::AddPage('','A4');
		PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);

		PDF::setImageScale(1);
		PDF::SetAutoPageBreak(false, 0);
		PDF::Image($image2, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);

		PDF::SetFont('helvetica', '', 10);
		PDF::SetXY((57) ,(90));
		PDF::Cell(30,3,strtoupper($data_cand->cand_name),'0','1','L');

		PDF::SetFont('helvetica', '', 10);
		PDF::SetXY((130) ,(90));
		PDF::Cell(30,3,strtoupper($data_cand->pass_no),'0','1','L');


		// signature
		// PDF::setJPEGQuality(75);
	
		if($data_cand->cand_sign != ''){
			PDF::Image($sign_path_01.'/'.$data_cand->cand_sign, 15, 174, 28, 16, '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
			// PDF::ImageSVG($sign_path_01.'/'.$data_cand->cand_sign,15,174,'28','16','','','',0,false);
		}

		ob_end_clean();
		PDF::Output($data_cand->pass_no.'_declaration.pdf');

	}

	public function ferequest($id){
		$image1 = base_path().'/public/tcpdf-images/FEI-01.jpg';
		$image2 = base_path().'/public/tcpdf-images/FEI-02.jpg';

		$image21 = base_path().'/public/tcpdf-images/FEC-01.jpg';
		$image22 = base_path().'/public/tcpdf-images/FEC-02.jpg';
		$image23 = base_path().'/public/tcpdf-images/FEC-03.jpg';


		$sign_path_01 = base_path().'/public/image/emigration/employer';
		$myfont = base_path().'/public/fonts/zeenatf/zeenat.ttf';

		$data_cand = DB::table('emigrations as emigr')
			->join('emigration_statuses as emigst','emigst.cand_id','=','emigr.cand_id')
			->leftjoin('qr_candidate_tbl as cand','cand.cand_id','=','emigr.cand_id')
			->leftjoin('qr_employee_candidate as emp_cand','emp_cand.cand_id','=','emigr.cand_id')
			->leftjoin('qr_employee_tbl as emp','emp.emp_id','=','emp_cand.emp_id')
			->leftjoin('qr_party_tbl as pty','pty.pty_id','=','cand.pty_id')
			->leftjoin('qr_sector as sector','sector.sc_id','=','emp.sc_id')
			->leftjoin('qr_country as country','country.country_id','=','emp.country')
			->leftjoin('qr_submission_place_tbl as submpl','submpl.sub_pl_id','=','emp.sub_place')
			->select('emigr.*','country.country_name','emp.emp_id_no as IDNO','emp.wakala_type as waktype','sector.sector_name','submpl.sub_pl_name')
			->where('emigr.id','=',$id)
			->first();
			
		if($data_cand->fe_date_emp != ''){
			$today = date('d,m,Y',strtotime($data_cand->fe_date_emp));
		}else{
			$today = date('d,m,Y');
			// store current date
			$post = Emigration::find($id);
			$post->fe_date_emp = date('Y-m-d');
			$post->save();
		}

		

		PDF::SetCreator('Qamr International');
		PDF::SetAuthor('Qamr International');
		PDF::SetTitle('FE Form');
		PDF::SetSubject('FE Form');
		PDF::SetKeywords('Qamr, PDF, visa, form, guide');
		PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		PDF::SetHeaderMargin(0);
		PDF::SetFooterMargin(0);
		PDF::setPrintFooter(false);
		PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
		PDF::SetTextColor(42, 67, 215);

		// $fontname = TCPDF_FONTS::addTTFfont($myfont.'/zeenat.ttf','TrueTypeUnicode','',96);
		$font_name =  TCPDF_FONTS::addTTFfont($myfont,'TrueTypeUnicode','',32);
		// city with country
		$place_and_country = $data_cand->sector_name.', '.$data_cand->country_name;
		$place_and_country_add = str_replace(' ','     ',$place_and_country);

		if($data_cand->sector_name == 'JEDDAH' || $data_cand->sector_name == 'MAKKAH' || $data_cand->sector_name == 'MADINAH MANUWARA'){
			$consulate = 'Jeddah';
		}else{	
			$consulate = 'Riyadh';
		}

		$emp_address1 = $data_cand->emp_address.', '.$data_cand->emp_city;
		$emp_address2 = $data_cand->emp_pincode.', '.$data_cand->country_name;

		// Add Space
		$emp_name_adds = str_replace(' ','     ',$data_cand->emp_name_eng);
		$country_name_adds = str_replace(' ','     ',$data_cand->country_name);
		$emp_address1_adds = str_replace(' ','     ',$emp_address1);
		$emp_address2_adds = str_replace(' ','     ',$emp_address2);
		$sector_name_adds = str_replace(' ','     ',$data_cand->sector_name);

		if($data_cand->waktype == 'individual'){
			// Add Space
			PDF::AddPage('','A4');
			PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
	
			PDF::setImageScale(1);
			PDF::SetAutoPageBreak(false, 0);
			PDF::Image($image1, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
	
			PDF::SetFont($font_name, '', 14);
			PDF::SetXY((29) ,(33));
			PDF::Cell(30,3,strtoupper($emp_name_adds),'0','1','L');
	
			PDF::SetFont($font_name, '', 16);
			PDF::SetXY((45) ,(49));
			PDF::Cell(30,3,strtoupper($country_name_adds),'0','1','L');
	
			PDF::SetFont($font_name, '', 10);
			PDF::SetXY((74) ,(68));
			PDF::Cell(30,3,strtoupper($emp_address1_adds),'0','1','L');

			PDF::SetFont($font_name, '', 10);
			PDF::SetXY((25) ,(75));
			PDF::Cell(30,3,strtoupper($emp_address2_adds),'0','1','L');
	
			PDF::SetFont($font_name, '', 15);
			PDF::SetXY((90) ,(80));
			PDF::Cell(30,3,strtoupper($data_cand->pid),'0','1','L');
	
			PDF::SetFont($font_name, '', 16);
			PDF::SetXY((80) ,(97));
			PDF::Cell(30,3,strtoupper($sector_name_adds),'0','1','L');
	
			PDF::SetFont($font_name, '', 16);
			PDF::SetXY((80) ,(130));
			PDF::Cell(30,3,strtoupper($consulate),'0','1','L');
	
			// signature
			// PDF::setJPEGQuality(75);
	
			if($data_cand->sign != ''){
				PDF::Image($sign_path_01.'/'.$data_cand->sign, 130, 255, 40, 20, '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
				// PDF::ImageSVG($sign_path_01.'/'.$data_cand->sign,130,255,'40','20','','','',0,false);
			}

			PDF::SetFont($font_name, '', 13);
			PDF::SetXY((30) ,(279));
			PDF::Cell(30,3,strtoupper($today),'0','1','L');
	
			PDF::AddPage('','A4');
			PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
	
			PDF::setImageScale(1);
			PDF::SetAutoPageBreak(false, 0);
			PDF::Image($image2, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
	
			PDF::SetFont($font_name, '', 16);
			PDF::SetXY((30) ,(81));
			PDF::Cell(30,3,strtoupper($today),'0','1','L');
	
			// signature
			// PDF::setJPEGQuality(75);
	
			if($data_cand->sign != ''){
				PDF::Image($sign_path_01.'/'.$data_cand->sign, 130, 60, 40, 20, '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
				// PDF::ImageSVG($sign_path_01.'/'.$data_cand->sign,140,45,'40','20','','','',0,false);
			}
	

	
			PDF::SetFont($font_name, '', 16);
			PDF::SetXY((31) ,(95));
			PDF::Cell(30,3,strtoupper($place_and_country_add),'0','1','L');
	
			PDF::SetFont($font_name, '', 11);
			PDF::SetXY((110) ,(92));
			PDF::Cell(30,3,strtoupper($emp_name_adds),'0','1','L');
	
			PDF::SetFont($font_name, '', 14);
			PDF::SetXY((135) ,(112));
			PDF::Cell(30,3,strtoupper($data_cand->contact_number),'0','1','L');

		}else{
			// Add Space
			$company_name_adds = str_replace(' ','     ',$data_cand->company_name);
			$emp_addr_adds = str_replace(' ','     ',$data_cand->emp_address);
			
		
			PDF::AddPage('','A4');
			PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
	
			PDF::setImageScale(1);
			PDF::SetAutoPageBreak(false, 0);
			PDF::Image($image21, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);

			PDF::SetFont($font_name, '', 14);
			PDF::SetXY((30) ,(61));
			PDF::Cell(30,3,strtoupper($emp_name_adds),'0','1','L');

			PDF::SetFont($font_name, '', 16);
			PDF::SetXY((50) ,(78));
			PDF::Cell(30,3,strtoupper($country_name_adds),'0','1','L');

			PDF::SetFont($font_name, '', 14);
			PDF::SetXY((68) ,(98));
			PDF::Cell(30,3,strtoupper($company_name_adds),'0','1','L');

			PDF::SetFont($font_name, '', 16);
			PDF::SetXY((68) ,(119));
			PDF::Cell(30,3,strtoupper($data_cand->contact_number),'0','1','L');

			PDF::SetFont($font_name, '', 16);
			PDF::SetXY((120) ,(129));
			PDF::Cell(30,3,strtoupper($data_cand->comp_reg_no),'0','1','L');

			PDF::SetFont($font_name, '', 10);
			PDF::SetXY((70) ,(148));
			PDF::Cell(30,3,strtoupper($emp_addr_adds),'0','1','L');

			PDF::SetFont($font_name, '', 15);
			PDF::SetXY((93) ,(159));
			PDF::Cell(30,3,strtoupper($data_cand->pid),'0','1','L');

			PDF::SetFont($font_name, '', 16);
			PDF::SetXY((80) ,(180));
			PDF::Cell(30,3,strtoupper($sector_name_adds),'0','1','L');


			if($data_cand->sign != ''){
				PDF::Image($sign_path_01.'/'.$data_cand->sign, 155, 245, 40, 20, '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
				// PDF::ImageSVG($sign_path_01.'/'.$data_cand->sign,142,235,'40','20','','','',0,false);
			}

			if($data_cand->stamp != ''){
				PDF::Image($sign_path_01.'/'.$data_cand->stamp, 112, 245, 40, 20, '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
				// PDF::ImageSVG($sign_path_01.'/'.$data_cand->stamp,100,235,'40','20','','','',0,false);
			}

			PDF::SetFont($font_name, '', 13);
			PDF::SetXY((28) ,(266));
			PDF::Cell(30,3,strtoupper($today),'0','1','L');


			// Second Page
			PDF::AddPage('','A4');
			PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
	
			PDF::setImageScale(1);
			PDF::SetAutoPageBreak(false, 0);
			PDF::Image($image22, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);

			PDF::SetFont($font_name, '', 16);
			PDF::SetXY((25) ,(22));
			PDF::Cell(30,3,strtoupper($consulate),'0','1','L');

			PDF::SetFont($font_name, '', 14);
			PDF::SetXY((32) ,(191));
			PDF::Cell(30,3,strtoupper($today),'0','1','L');


			// signature
			// PDF::setJPEGQuality(75);
	
			// if($data_cand->sign != ''){
			// 	PDF::Image($sign_path_01.'/'.$data_cand->sign, 140, 169, 28, 16, '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
			// }

			PDF::SetFont($font_name, '', 16);
			PDF::SetXY((32) ,(205));
			PDF::Cell(30,3,strtoupper($place_and_country_add),'0','1','L');

			if($data_cand->stamp != ''){
				PDF::Image($sign_path_01.'/'.$data_cand->stamp, 130, 225, 40, 20, '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
				// PDF::ImageSVG($sign_path_01.'/'.$data_cand->stamp,107,205,'40','20','','','',0,false);
			}

			PDF::SetFont($font_name, '', 10);
			PDF::SetXY((109) ,(200));
			PDF::Cell(30,3,strtoupper($company_name_adds),'0','1','L');

			PDF::SetFont($font_name, '', 13);
			PDF::SetXY((135) ,(258));
			PDF::Cell(30,3,strtoupper($data_cand->contact_number),'0','1','L');

			if($data_cand->sign != ''){
				PDF::Image($sign_path_01.'/'.$data_cand->sign, 130, 170, 40, 20, '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
				// PDF::ImageSVG($sign_path_01.'/'.$data_cand->sign,130,260,'40','20','','','',0,false);
			}

			// Third Page
			PDF::AddPage('','A4');
			PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
	
			PDF::setImageScale(1);
			PDF::SetAutoPageBreak(false, 0);
			PDF::Image($image23, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);

			if($data_cand->sign != ''){
				PDF::Image($sign_path_01.'/'.$data_cand->sign, 160, 240, 40, 20, '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
				// PDF::ImageSVG($sign_path_01.'/'.$data_cand->sign,140,260,'40','20','','','',0,false);
			}

			if($data_cand->stamp != ''){
				PDF::Image($sign_path_01.'/'.$data_cand->stamp, 121, 245, 40, 20, '', 'PNG', '', true, 150, '', false, false, false, false, false, false);
				// PDF::ImageSVG($sign_path_01.'/'.$data_cand->stamp,103,257,'40','20','','','',0,false);
			}

		}



		

		ob_end_clean();
		PDF::Output($data_cand->pass_no.'_fe-form.pdf');
	}

	public function visa_form(Request $request)
	{
		$path =  base_path().'/public/tcpdf-images/submission_form.jpg';
		$candidatePhotoPath =  base_path().'/public/image/service-candidate';
		$blanckImagePath =  base_path().'/public/images/Blank_image_frame.jpg';

		PDF::SetCreator('Qamr International');
		PDF::SetAuthor('Qamr International');
		PDF::SetTitle('Visa Form');
		PDF::SetSubject('Candidate Visa Form');
		PDF::SetKeywords('Qamr, PDF, visa, form, guide');
		PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		PDF::SetHeaderMargin(0);
		PDF::SetFooterMargin(0);
		PDF::setPrintFooter(false);
		PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);

		$cand_id = $request->input('visa_cand_id') ? $request->input('visa_cand_id') : '';
		$cand_id = explode(",", $cand_id);
	
		$len_id = count($cand_id);
		for($i=0; $i<$len_id; $i++)
			if($cand_id[$i]!=''){
				$result =  DB::table('qr_candidate_tbl as cand')
					->leftjoin('qr_employee_candidate as emp_cands','emp_cands.cand_id','=','cand.cand_id')
					->leftjoin('qr_employee_tbl as emp','emp.emp_id','=','emp_cands.emp_id')
					->leftjoin('qr_nationality_tbl as nt','nt.nat_id','=','cand.nat_id')
					->leftjoin('qr_religion_cast_tbl as rlg','rlg.rel_cst_id','=','cand.rel_cst_id')
					->leftjoin('qr_emp_visa_tbl as emp_visa','emp_visa.visa_id','=','emp.visa_id')
					->leftjoin('qr_sector as sc','sc.sc_id','=','emp.sc_id')
					->leftjoin('qr_services_details as ser','ser.cand_id','=','cand.cand_id')
					->leftjoin('qr_service_tbl as ser_tbl','ser_tbl.ser_id','=','emp.visa_id')
					->leftjoin('qr_candidate_place_issue as cand_pl_iss','cand_pl_iss.id','=','cand.cand_place_issue')
					->leftjoin('qr_profession_tbl as prof','prof.prof_id','=','emp_cands.cand_profession')
					->select('cand.*','emp_cands.*','ser_tbl.ser_name as visa_type','cand_pl_iss.place as cand_pl_issue','emp.*','nt.*','prof.*','ser.mofa as mofa','rlg.*','emp_visa.*','sc.*')
					->where('cand.cand_id','=',$cand_id[$i])
					->first();
			
			 	if(isset($result->mofa)){
			 		$ser_data = unserialize($result->mofa);
			 		$mofa = $ser_data['mofa_no'];
				}else{
					$mofa = '';
				}

				$dob_c = date('d-m-Y',strtotime($result->cand_dob));
				$doi_c = date('d-m-Y',strtotime($result->cand_date_issue));
				$doe_c = date('d-m-Y',strtotime($result->cand_date_expiry));

				PDF::AddPage('','A4');
				PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);

				PDF::setImageScale(1);
				PDF::SetAutoPageBreak(false, 0);
				PDF::Image($path, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
			
				PDF::SetFont('helvetica', '', 11);
				PDF::SetXY((165) ,(6));
				PDF::Cell(30,3,strtoupper($result->emp_file_no),'0','1','R');
				// profile pic 
				PDF::setJPEGQuality(75);
				
				if($result->cand_photo!=''){
					PDF::Image($candidatePhotoPath.'/'.$result->cand_photo, 20, 26, 32, 34, '', 'candidate photo', '', true, 150, '', false, false, false, false, false, false);
				}else{
					PDF::Image($blanckImagePath, 18, 7, 20, 20, 'JPG', 'http://www.tcpdf.org', '', true, 150, '', false, false, 1, false, false, false);
				}

				PDF::SetFont('helvetica', 'B', 11);
				PDF::SetXY((167) ,(46));
				PDF::Cell(30,3,strtoupper($mofa),'0','1','L');
			
				PDF::SetFont('aefurat', '', 14);
				PDF::SetXY((19) ,(75));
				PDF::Cell(30,3,$result->prof_arabic_name,'0','1','L');

				PDF::SetFont('helvetica', 'B', 14);
				PDF::SetXY((71) ,(85));
				PDF::Cell(30,3,'QAMR INTERNATIONAL','0','1','L');
			
				PDF::SetFont('helvetica', 'B', 13);
				PDF::SetXY((30) ,(106));
				PDF::Cell(30,3,strtoupper($result->cand_fname),'0','1','C');

				PDF::SetXY((100) ,(106));
				PDF::Cell(30,3,strtoupper($result->cand_mname),'0','1','C');

				PDF::SetXY((147) ,(106));
				PDF::Cell(30,3,strtoupper($result->cand_lname),'0','1','L');

				PDF::SetXY((60) ,(114));
				PDF::Cell(30,3,strtoupper($result->cand_place_birth),'0','1','L');

				PDF::SetXY((135) ,(114));
				PDF::Cell(30,3,strtoupper($dob_c),'0','1','L');

				PDF::SetXY((105) ,(122));
				PDF::Cell(30,3,strtoupper($result->nat_name),'0','1','L');

				PDF::SetXY((105) ,(129));
				
				if($result->rel_cst_name == 'Muslim' || $result->rel_cst_name == 'muslim'){
					PDF::Cell(30,3,strtoupper($result->rel_cst_name),'0','1','L');
				
				}else{
					PDF::Cell(30,3,strtoupper('Non-Muslim'),'0','1','L');
				}
				

				$string = $result->cand_pre_address.', '.$result->pincode;
				$chk = $this->breakStr($string,48);

				PDF::SetXY((38) ,(137));
				PDF::Cell(30,3,strtoupper($chk[0]),'0','1','L'); 
				if(isset($chk[1])){
					PDF::SetXY((38) ,(144));
					PDF::Cell(30,3,strtoupper($chk[1]),'0','1','L'); //to cut the address
				}

				PDF::SetXY((75) ,(153));
				PDF::Cell(30,3,strtoupper($result->cand_passport_no),'0','1','L');

				PDF::SetXY((123) ,(153));
				PDF::Cell(30,3,strtoupper($result->cand_pl_issue),'0','1','L');

				PDF::SetXY((75) ,(161));
				PDF::Cell(30,3,strtoupper($doi_c),'0','1','L');

				PDF::SetXY((123) ,(161));
				PDF::Cell(30,3,strtoupper($doe_c),'0','1','L');

				PDF::SetXY((103) ,(169));
				PDF::Cell(30,3,strtoupper($result->cand_gender),'0','1','L');

				PDF::SetXY((66) ,(192));
				PDF::Cell(30,3,strtoupper(chop(strtolower($result->visa_type),'visa')),'0','1','L');

				PDF::SetXY((124) ,(192));
				PDF::Cell(30,3,'90 DAYS','0','1','L');

				PDF::SetXY((66) ,(201));
				PDF::Cell(30,3,'SOON','0','1','L');

				PDF::SetXY((124) ,(201));
				PDF::Cell(30,3,strtoupper($result->sector_name),'0','1','L');

				PDF::SetXY((65) ,(208));
				PDF::Cell(30,3,strtoupper($result->emp_visa_no),'0','1','L');

				PDF::SetXY((120) ,(208));
				PDF::Cell(30,3,strtoupper($result->emp_visa_date),'0','1','L');

				//aefurat
				PDF::SetFont('aefurat', 'B', 18);
				PDF::SetXY((92) ,(213.5));
				PDF::Cell(30,3,$result->spon_nm_arab,'0','1','C');
			
				PDF::SetFont('helvetica', 'B', 13);
				PDF::SetXY((77) ,(223));
				PDF::Cell(30,3,strtoupper('kingdom of saudi arabia'),'0','1','L');

			
			}
			ob_end_clean();
			PDF::Output('Visa-Form.pdf');
		}



	public function submissionList(){
		$path = base_path().'/public/tcpdf-images/sublist.jpg';

		PDF::SetCreator('Qamr International');
		PDF::SetAuthor('Qamr International');
		PDF::SetTitle('Submission List');
		PDF::SetSubject('Candidate Submission List');
		PDF::SetKeywords('Qamr, PDF, Dl, form, guide');
		PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		PDF::SetHeaderMargin(0);
		PDF::SetFooterMargin(0);
		PDF::setPrintFooter(false);
		PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);

		PDF::AddPage('','A4');
		PDF::setImageScale(1);
		PDF::SetAutoPageBreak(false, 0);
		PDF::Image($path, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);

		ob_end_clean();
		PDF::Output(time().'_sublist.pdf');
	}

	public function dl_undertaking_form(Request $request)
	{
		$path =  base_path().'/public/tcpdf-images/dl_undertaking.jpg';

		PDF::SetCreator('Qamr International');
		PDF::SetAuthor('Qamr International');
		PDF::SetTitle('Dl Undertaking Form');
		PDF::SetSubject('Candidate Dl Undertaking Form');
		PDF::SetKeywords('Qamr, PDF, Dl, form, guide');
		PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		PDF::SetHeaderMargin(0);
		PDF::SetFooterMargin(0);
		PDF::setPrintFooter(false);
		PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);

		$cand_id = $request->input('dl_cand_id') ? $request->input('dl_cand_id') : '';
		$cand_id = explode(",", $cand_id);
	
		$len_id = count($cand_id);
		for($i=0; $i<$len_id; $i++)
			if($cand_id[$i]!='')
		{
			$result =  DB::table('qr_candidate_tbl as cand')
			->leftjoin('qr_employee_candidate as emp_cands','emp_cands.cand_id','=','cand.cand_id')
			->leftjoin('qr_employee_tbl as emp','emp.emp_id','=','emp_cands.emp_id')
			->leftjoin('qr_emp_visa_tbl as emp_visa','emp_visa.visa_id','=','emp.visa_id')
			->select('cand.*','emp_cands.*','emp.*','emp_visa.*')
			->where('cand.cand_id','=',$cand_id[$i])
			->first();

			 if(isset($result->mofa)){
			 	$ser_data = unserialize($result->mofa);
			 	$mofa = $ser_data['mofa_no'];
			}else{
				$mofa = '';
			}
			PDF::AddPage('','A4');

			PDF::setImageScale(1);
			PDF::SetAutoPageBreak(false, 0);
			PDF::Image($path, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
			
			PDF::SetFont('helvetica', 'B', 12);
			// PDF::SetXY((63) ,(174));
			PDF::SetXY((68) ,(174));
			PDF::Cell(30,3,strtoupper($result->cand_fname.' '.$result->cand_mname.' '.$result->cand_lname),'0','1','R');

			PDF::SetFont('helvetica', 'B', 15);
			PDF::SetXY((111) ,(174));
			PDF::Cell(30,3,strtoupper($result->emp_visa_no),'0','1','L');

			
		}
			ob_end_clean();
			PDF::Output('Dl-Undertaking-Form.pdf');
	}

	public function visa_cancel_form(Request $request)
	{
			$path =  base_path().'/public/tcpdf-images/visa_cancel.jpg';

		PDF::SetCreator('Qamr International');
		PDF::SetAuthor('Qamr International');
		PDF::SetTitle('Visa Cancel Form');
		PDF::SetSubject('Candidate Visa Cancel Form');
		PDF::SetKeywords('Qamr, PDF, visa, form, guide');
		PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		PDF::SetHeaderMargin(0);
		PDF::SetFooterMargin(0);
		PDF::setPrintFooter(false);
		PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);

		$cand_id = $request->input('cancel_cand_id') ? $request->input('cancel_cand_id') : '';
		$cand_id = explode(",", $cand_id);

		$len_id = count($cand_id);
		for($i=0; $i<$len_id; $i++)
			if($cand_id[$i]!='')
		{
			$result =  DB::table('qr_candidate_tbl as cand')
			->leftjoin('qr_employee_candidate as emp_cands','emp_cands.cand_id','=','cand.cand_id')
			->leftjoin('qr_employee_tbl as emp','emp.emp_id','=','emp_cands.emp_id')
			->leftjoin('qr_emp_visa_tbl as emp_visa','emp_visa.visa_id','=','emp.visa_id')
			->leftjoin('qr_party_tbl as pty','pty.pty_id','=','emp.pty_id')
			->select('cand.*','emp_cands.*','emp.*','emp_visa.*','pty.pty_ag_name')
			->where('cand.cand_id','=',$cand_id[$i])
			->first();

			 if(isset($result->mofa)){
			 	$ser_data = unserialize($result->mofa);
			 	$mofa = $ser_data['mofa_no'];
			}else{
				$mofa = '';
			}
			
			PDF::AddPage('','A4');

			PDF::setImageScale(1);
			PDF::SetAutoPageBreak(false, 0);
			PDF::Image($path, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
			
			PDF::SetFont('helvetica', '', 11);
			PDF::SetXY((34) ,(65));
			PDF::Cell(30,3,strtoupper('qamr international'),'0','1','R');


			PDF::SetXY((21) ,(81));
			PDF::Cell(30,3,strtoupper($result->emp_visa_no),'0','1','L');

			PDF::SetXY((61) ,(81));
			PDF::Cell(30,3,strtoupper($result->emp_visa_date),'0','1','L');

			PDF::SetXY((12) ,(97));
			PDF::Cell(30,3,strtoupper($result->pty_ag_name),'0','1','L');

			PDF::SetXY((45) ,(104));
			PDF::Cell(30,3,strtoupper($result->emp_id_no),'0','1','L');

			
		}
			ob_end_clean();
			PDF::Output('Visa-Cancel.pdf');
	}

	function breakStr($string,$length){
    $stringAry      =   explode("||",wordwrap($string, $length, "||"));
    foreach($stringAry as $key=>$val){
        print '['.$key.'] Character length <strong>('.strlen($val).')</strong><br />';
    }
    return $stringAry;
}
}
