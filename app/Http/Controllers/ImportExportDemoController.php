<?php

namespace App\Http\Controllers;

use App\AdminModel\CsvData;
use App\AdminModel\Importexport;
use App\Imports\CsvImport;
use Illuminate\Http\Request;
// use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;

class ImportExportDemoController extends Controller
{
    public function index()
    {
        return view('demo.importExport.index');
    }

    public function parseImport(CsvImport $request)
    {
        $path = $request->file('csv_file')->getRealPath();

        
        dd($path);

        $data = Excel::import(new CsvImport, $path);
        // $data = Excel::import(new CsvImport,$path,function($reader){})->get()->toArray();

        // $data = Excel::load($path,function($reader){})->get()->toArray();

        if (count($data) > 0) {
            $csv_header_fields = [];
            foreach ($data[0] as $key => $value){
                $csv_header_fields[] = $key; 
            }

            $csv_data = array_slice($data,0,2);
            $csv_data_file = CsvData::create([
                'csv_filename' => $request->file('csv_file')->getClientOriginalName(),
                'csv_header' => '0',
                'csv_data' => json_encode($data)
            ]);
            
        }else{
            return redirect()->back();
        }

        return view('demo.importExport.demoImport',compact('csv_header_fields','csv_data','csv_data_file'));
    }

    public function import_process(Request $request)
    {
        $data = CsvData::find($request->csv_data_file_id);
        $csv_data = json_decode($data->csv_data, true);
        foreach ($csv_data as $row) {
            $imprtData = new Importexport();
            foreach (config('app.db_fields') as $index => $field) {
                if ($data->csv_header) {
                    $imprtData->$field = $row[$request->fields[$field]];
                }else{
                    $imprtData->$field = $row[$request->fields[$index]];
                }
            }
            $imprtData->save();
        }

        return redirect('demotest/import-export');
    }
}
