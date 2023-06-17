<?php

namespace App\Http\Controllers\Web\WarehouseManagement;

use App\Exports\Warehouse_Management\ReportMaterials;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Libraries\WarehouseManagement\ImportLibraries;
use App\Libraries\MasterData\MasterMakerLibraries;
use App\Libraries\MasterData\MasterMaterialsLibraries;
use App\Libraries\MasterData\MasterLocationLibraries;
use App\Libraries\MasterData\MasterManufacturingLibraries;
use App\Libraries\WarehouseManagement\Report\NxtLibraries;

class ReportController extends Controller
{
	private $materials;
    private $product;
    private $location;
    private $manufacturing;
    private $export_file;
    private $report;

	public function __construct(
        MasterMaterialsLibraries $MasterMaterialsLibraries,
        ReportMaterials $ReportMaterials,
        MasterManufacturingLibraries $MasterManufacturingLibraries,
        NxtLibraries $NxtLibraries
	)
    {
		$this->middleware('auth');
        $this->materials        = $MasterMaterialsLibraries;
        $this->manufacturing    = $MasterManufacturingLibraries;
        $this->export_file      = $ReportMaterials;
        $this->report           = $NxtLibraries;
	}
    
    public function index(Request $request)
    {
        // set_time_limit(9999999999);
        // $data = $this->report->get_list_entry($request);
        $list_materials = $this->materials->get_name_and_symbols_materials($request);
        $list_manufacturing = $this->manufacturing->get_name_and_symbols_manufacturing($request);
        return view('warehouse_management.report.index',
        [
            // 'data'=>$data,
            'list_materials'        =>$list_materials,
            'list_manufacturing'    =>$list_manufacturing,
            'request'               =>$request
        ]); 
    }

    public function export_file(Request $request)
    { 
        set_time_limit(6000000);

        $data = $this->report->index($request);
        $this->export_file->export($data,$request);

    }
    
}
