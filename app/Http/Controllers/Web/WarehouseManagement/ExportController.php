<?php

namespace App\Http\Controllers\Web\WarehouseManagement;

use App\Exports\Warehouse_Management\ExportMaterials;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Libraries\MasterData\MasterMakerLibraries;
use App\Libraries\MasterData\MasterMaterialsLibraries;
use App\Libraries\MasterData\MasterLocationLibraries;
use App\Libraries\MasterData\MasterManufacturingLibraries;
use App\Libraries\MasterData\MasterProductLibraries;
use App\Libraries\WarehouseManagement\ExportLibraries;
use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    private $maker;
    private $materials;
    private $location;
    private $export;
    private $manufacturing;
    private $product;
    private $export_file;

    public function __construct(
        MasterMaterialsLibraries $MasterMaterialsLibraries,
        ExportLibraries $ExportLibraries,
        MasterManufacturingLibraries $MasterManufacturingLibraries,
        MasterProductLibraries $MasterProductLibraries,
        MasterLocationLibraries $MasterLocationLibraries,
        ExportMaterials $ExportMaterials
    ) {
        $this->middleware('auth');
        $this->product              = $MasterProductLibraries;
        $this->materials            = $MasterMaterialsLibraries;
        $this->export               = $ExportLibraries;
        $this->manufacturing        = $MasterManufacturingLibraries;
        $this->location             = $MasterLocationLibraries;
        $this->export_file          = $ExportMaterials;

    }
    public function export()
    {
        $data           = $this->export->get_all_list();
        $manufacturing  = $this->manufacturing->get_name_and_symbols_manufacturing();
        $materials      = $this->materials->get_name_and_symbols_materials();
        $product        = $this->product->get_name_and_symbols_product();
        return view('warehouse_management.export.index',
        [
            'manufacturing' => $manufacturing,
            'materials'     => $materials,
            'data'          => $data,
            'products'      => $product,
        ]);
    }
    public function Command_export_add_or_update(Request $request)
    {
        // dd($request);
        $check = $this->export->check_data($request);
        if($check->status)
        {
            $data = $this->export->export_add($request);
            return response()->json($data);
        }
        else
        {
            return response()->json($check);
        }
    }
    public function import_file_excel(Request $request)
    {
        $data = $this->export->import_file_excel($request);
        if(count($data) == 0)
        {
            return redirect()->back()->with('success', __('Success'));
        }
        else
        {
            return redirect()->back()->with('danger_array', $data);
        }
    }
    public function export_data(Request $request)
    {
        // dd($request);
        $check = $this->export->check_export($request);
        if($check->status)
        {
            $data = $this->export->export($request);
            return response()->json($data);
        }
        else
        {
            return response()->json($check);
        }
    }

    public function export_data_scan(Request $request)
    {
        // dd($request);
        $check = $this->export->check_export_scan($request);
        if($check->status)
        {
            $data = $this->export->export_scan($request);
            return response()->json($data);
        }
        else
        {
            return response()->json($check);
        }
    }

    public function export_detail(Request $request)
    {
        $materials     = $this->materials->get_name_and_symbols_materials();
        $location      = $this->location->get_name_and_symbols_location_type1();
        $command       = $this->export->get_command_width_id($request);
        return view('warehouse_management.export.detail',[
            'request'=>$request,
            'location'=> $location,
            'materials' =>$materials,
            'command' => $command
        ]);
    }

    public function Command_export_destroy(Request $request)
    {
        $data    = $this->export->cancel($request);
        $arr = [];
        array_push($arr, $data);
        return redirect()->route('warehouse_management.export')->with('success', __('Delete').' '.__('Success'));
    }

    public function Command_export_success(Request $request)
    {
        $data    = $this->export->success($request);
        $arr = [];
        array_push($arr, $data);
        return redirect()->route('warehouse_management.export')->with('success', __('Success'));
    }

    public function export_file(Request $request)
    {
        $data_all_fill = $this->export->get_list_export($request);
        $this->export_file->export($data_all_fill);
    }
}
