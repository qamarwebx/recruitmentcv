<?php

namespace App\Http\Controllers;

use App\AdminModel\ModuleP;
use App\AdminModel\Permission;
use App\AdminModel\SubModuleP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    public function index()
    {
        $modulePs = ModuleP::all();
        $submodulePs = DB::table('qr_sub_module_tbl as submodule')
            ->join('qr_module_tbl as module','module.id','=','submodule.module_id')
            ->select('submodule.*','module.module_name')
            ->get();
        $permissions = Permission::all();

        $pageConfigs = ['pageHeader' => false];
        return view('content.perm.index',['pageConfigs' => $pageConfigs,'modulePs' => $modulePs,'submodulePs' => $submodulePs,'permissions' => $permissions]);
    }

    


    public function mStore(Request $request)
    {
        $moduleP = new ModuleP();
        $moduleP->module_name = $request->input('module_name');
        // $moduleP->module_type = $request->input('module_type');
        $moduleP->save();
        return redirect()->back();
    }

    public function smStore(Request $request)
    {
        $subModuleP = new SubModuleP();
        $subModuleP->module_id = $request->module_id;
        $subModuleP->sub_module_name = $request->sub_module_name;
        $subModuleP->save();
        return redirect()->back();
    }

    public function pstore(Request $request)
    {
        $permission = new Permission();
        $permission->name = $request->name;
        $permission->prefix = $request->prefix;
        $permission->save();
        return redirect()->back();
    }
}
