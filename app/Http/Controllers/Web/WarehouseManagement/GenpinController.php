<?php

namespace App\Http\Controllers\Web\WarehouseManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Libraries\WarehouseManagement\GenpinLibraries;
use App\Libraries\MasterData\MasterMaterialsLibraries;
use App\Libraries\MasterData\MasterLocationLibraries;
use App\Libraries\MasterData\MasterManufacturingLibraries;
use App\Libraries\MasterData\MasterProductLibraries;
class GenpinController extends Controller
{
    public function __construct(
        GenpinLibraries $GenpinLibraries,
        MasterMaterialsLibraries $MasterMaterialsLibraries,
        MasterManufacturingLibraries $MasterManufacturingLibraries,
        MasterProductLibraries $MasterProductLibraries,
    ){
        $this->middleware('auth');
        $this->product = $MasterProductLibraries;
        $this->genpin = $GenpinLibraries;
        $this->materials = $MasterMaterialsLibraries;
        $this->manufacturing = $MasterManufacturingLibraries;
    }

    public function genpin()
    {
        $materials = $this->materials->get_name_and_symbols_materials();
        $manufacturing  = $this->manufacturing->get_name_and_symbols_manufacturing();
        $product        = $this->product->get_name_and_symbols_product();
        return view('warehouse_management.genpin.index',
        [
            'materials'=>$materials,
            'manufacturing'=>$manufacturing,
            'product' => $product
        ]);
    }
    public function import_file_Excel(Request $request)
    {
        set_time_limit(999999999999);
        $this->genpin->import_file_Excel($request);
        return redirect()->back()->with('success',__('Success'));
    }

    public function genpin_manual(Request $request)
    {
        $data_re = $this->genpin->genpin_manual($request); 
        if($data_re->status)
        {
            return redirect()->back()->with('success',__('Success')); 
        }
        else
        {
            return redirect()->back()->with('danger',__('Fail')); 
        }
    }
}
