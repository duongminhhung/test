<?php

namespace App\Http\Controllers\Web\WarehouseManagement;

use App\Exports\Warehouse_Management\ImportMaterials;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Libraries\WarehouseManagement\ImportLibraries;
use App\Libraries\MasterData\MasterMakerLibraries;
use App\Libraries\MasterData\MasterMaterialsLibraries;
use App\Libraries\MasterData\MasterLocationLibraries;
use App\Libraries\MasterData\MasterManufacturingLibraries;

class ImportController extends Controller
{
    private $import;
    private $maker;
    private $materials;
    private $location;
    private $manufacturing;
    private $export;
    
    public function __construct(
        ImportLibraries $ImportLibraries,
        MasterMakerLibraries $MasterMakerLibraries,
        MasterMaterialsLibraries $MasterMaterialsLibraries,
        MasterLocationLibraries $MasterLocationLibraries,
        MasterManufacturingLibraries $MasterManufacturingLibraries,
        ImportMaterials $ImportMaterials
    ) {
        $this->middleware('auth');
        $this->import       = $ImportLibraries;
        $this->maker        = $MasterMakerLibraries;   
        $this->materials    = $MasterMaterialsLibraries;
        $this->location     = $MasterLocationLibraries;
        $this->export       = $ImportMaterials;
        $this->manufacturing = $MasterManufacturingLibraries;

    }

    public function import()
    {
        $data = $this->import->get_name_and_symbols_command();
        $maker = $this->maker->get_name_and_symbols_makers();
        $manufacturing  = $this->manufacturing->get_name_and_symbols_manufacturing();
        return view('warehouse_management.import.index',['data'=>$data,'maker'=>$maker,'manufacturing'=>$manufacturing]);
    }
    public function Command_import_add_or_update(Request $request)
    {
        $check = $this->import->check_data($request);
        if($check->status)
        {
            $data = $this->import->add_or_create_data($request);
            return response()->json($data);
        }
        else
        {
            return response()->json($check);
        }
    }

    public function Command_import_destroy(Request $request)
    {
        $data    = $this->import->command_destroy($request);
        $arr = [];  
        array_push($arr, $data);
        return redirect()->back()->with('success', __('Delete').' '.__('Success'));
    }

    public function import_file_excel(Request $request)
    {
        $data    = $this->import->import_file_excel($request);
        if(count($data) == 0)
        {
            return redirect()->back()->with('success', __('Success'));
        }
        else
        {
            return redirect()->back()->with('danger_array', $data);
        }
        
    }
    public function import_detail(Request $request)
    {
        $materials = $this->materials->get_name_and_symbols_materials();
        $location = $this->location->get_name_and_symbols_location_type1();
        $maker = $this->maker->get_name_and_symbols_makers();
        $command = $this->import->get_command_width_id($request);
        return view('warehouse_management.import.detail',
        [
            'request'=>$request,
            'materials'=>$materials,
            'command'=>$command,
            'location'=>$location,
            'maker' =>$maker
        ]);
    }

    public function detail_import_add_or_update_box(Request $request)
    {
        // dd(!fđf);

        $check = $this->import->check_data_create_box($request);
        if($check->status)
        {
            $data = $this->import->add_or_create_data_box($request);
            return response()->json($data);
        }
        else
        {
            return response()->json($check);
        }
    }

    public function detail_import_retype_box(Request $request)
    {
        // dd($request);

        $check = $this->import->check_data_retype($request);
        if($check->status)
        {
            $data = $this->import->retype_box($request);
            return response()->json($data);
        }
        else
        {
            return response()->json($check);
        }
    }

    public function detail_import_edit_location(Request $request)
    {
        $data = $this->import->edit_location($request);  
        return response()->json($data);
    }

    public function export_command_import(Request $request)
    {
        set_time_limit(6000000);
        $data = $this->import->export_file_excel($request);
        $this->export->export($data);

    }
}
