<?php
namespace App\Http\Controllers;

use App\Models\CompanyContact;
use Illuminate\Http\Request;
use App\Exports\ContactExport;
use App\Imports\ContactImport;
use Session;

class ExcelController extends Controller
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function importExportView()
    {
       return view('excel.index');
    }
   
    /**
    * @return \Illuminate\Support\Collection
    */
    public function exportExcel($type) 
    {
        return \Excel::download(new CompanyContact, 'transactions.'.$type);
    }
   
    /**
    * @return \Illuminate\Support\Collection
    */
    public function importExcel(Request $request) 
    {
       // dd($request->import_file);
        \Excel::import(new ContactImport,$request->import_file);

        \Session::flash('success', 'Your file is imported successfully in database.');
           
        return back();
    }
}