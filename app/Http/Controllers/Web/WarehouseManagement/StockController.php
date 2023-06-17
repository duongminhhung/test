<?php

namespace App\Http\Controllers\Web\WarehouseManagement;

use App\Exports\Warehouse_Management\StockController as Warehouse_ManagementStockController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Libraries\WarehouseManagement\ImportLibraries;
use App\Libraries\MasterData\MasterMakerLibraries;
use App\Libraries\MasterData\MasterMaterialsLibraries;
use App\Libraries\MasterData\MasterLocationLibraries;
use App\Libraries\MasterData\MasterManufacturingLibraries;
use App\Libraries\WarehouseManagement\StockLibraries;

class StockController extends Controller
{
    private $import;
    private $materials;
    private $location;
    private $manufacturing;
    private $export;
    private $stock;
    public function __construct(
        ImportLibraries $ImportLibraries,
        MasterMaterialsLibraries $MasterMaterialsLibraries,
        MasterLocationLibraries $MasterLocationLibraries,
        MasterManufacturingLibraries $MasterManufacturingLibraries,
        Warehouse_ManagementStockController  $Warehouse_ManagementStockController,
        StockLibraries $StockLibraries
    ) {
        $this->middleware('auth');
        $this->import = $ImportLibraries;
        $this->materials = $MasterMaterialsLibraries;
        $this->location = $MasterLocationLibraries;
        $this->manufacturing = $MasterManufacturingLibraries;
        $this->export = $Warehouse_ManagementStockController;
        $this->stock = $StockLibraries;

    }

    public function index(Request $request)
    {
        $materials = $this->materials->get_name_and_symbols_materials();
        $location = $this->location->get_name_and_symbols_location_type1();
        $manufacturing  = $this->manufacturing->get_name_and_symbols_manufacturing();

        return view('warehouse_management.stock.index',
        [
            'request'=>$request,
            'materials'=>$materials,
            'location'=>$location,
            'manufacturing'=>$manufacturing,
        ]);
    }

    public function export_all(Request $request)
    {
        set_time_limit(6000000);
        $data = $this->stock->all($request);
        $this->export->all($data);

    }

    public function export_materials(Request $request)
    {
        set_time_limit(6000000);
        $data = $this->stock->materials($request);
        $this->export->materials($data);

    }

    public function export_product_input_file(Request $request)
    {
        set_time_limit(6000000);
        $data = $this->stock->product_input($request);
        $this->export->product_input($data);

    }

    public function import_file_stock(Request $request)
    {
        set_time_limit(6000000);

        $data = $this->stock->import_file_excel($request);
        if(count($data) == 0)
        {
            return redirect()->back()->with('success', __('Success'));
        }
        else
        {
            return redirect()->back()->with('danger_array', $data);
        }
    }
    
}
