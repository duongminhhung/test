<?php

namespace App\Http\Controllers\Web\WarehouseManagement;

use App\Exports\Warehouse_Management\MaterialSupplyCertificate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Libraries\WarehouseManagement\ImportLibraries;
use App\Libraries\MasterData\MasterMakerLibraries;
use App\Libraries\MasterData\MasterMaterialsLibraries;
use App\Libraries\MasterData\MasterLocationLibraries;
use App\Libraries\MasterData\MasterManufacturingLibraries;
use App\Libraries\MasterData\MasterProductLibraries;
use App\Libraries\WarehouseManagement\MaterialSupplyCertificateLibraries;
use App\Libraries\WarehouseManagement\StockLibraries;

class MaterialSupplyCertificateController extends Controller
{
    private $materials;
    private $product;
    private $supply_certifi;
    private $export;

    public function __construct(
        MasterMaterialsLibraries $MasterMaterialsLibraries,
        MasterProductLibraries $MasterProductLibraries,
        MaterialSupplyCertificateLibraries $MaterialSupplyCertificateLibraries,
        MaterialSupplyCertificate $MaterialSupplyCertificate
    ) {
        $this->middleware('auth');
        $this->materials        = $MasterMaterialsLibraries;
        $this->product          = $MasterProductLibraries;
        $this->supply_certifi   = $MaterialSupplyCertificateLibraries;
        $this->export           = $MaterialSupplyCertificate;
    }

    public function index(Request $request)
    {
        $materials      = $this->materials->get_name_and_symbols_materials();
        $product        = $this->product->get_name_and_symbols_product();
        // $supply_certifi = $this->supply_certifi->get_all_list();

        return view('warehouse_management.supply_certificate.index',
        [   
            'request'   =>$request,
            'materials' =>$materials,
            'products'   =>$product,
        ]);
    }

    public function export_file(Request $request)
    {
        set_time_limit(6000000);
        $data = $this->supply_certifi->get_list_export($request);
        $this->export->export($data,$request);

    }
    
}
