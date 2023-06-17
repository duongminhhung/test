<?php

namespace App\Http\Controllers\Web\OrderManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Libraries\OrderManagement\OrderLibraries;
use App\Libraries\OrderManagement\POLibraries;
use App\Libraries\MasterData\MasterMakerLibraries;
use App\Libraries\MasterData\MasterMaterialsLibraries;
use App\Libraries\MasterData\MasterLocationLibraries;
use App\Libraries\MasterData\MasterManufacturingLibraries;
class OrderController extends Controller
{
    private $order;
    private $po;
    private $maker;
    private $materials;
    private $location;
    private $manufacturing;
    public function __construct(
        OrderLibraries $OrderLibraries,
        MasterMakerLibraries $MasterMakerLibraries,
        MasterMaterialsLibraries $MasterMaterialsLibraries,
        MasterLocationLibraries $MasterLocationLibraries,
        MasterManufacturingLibraries $MasterManufacturingLibraries,
        POLibraries $POLibraries,
    ) {
        $this->middleware('auth');
        $this->order = $OrderLibraries;
        $this->po    = $POLibraries;
        $this->maker = $MasterMakerLibraries;
        $this->materials = $MasterMaterialsLibraries;
        $this->location = $MasterLocationLibraries;
        $this->manufacturing = $MasterManufacturingLibraries;

    }

    public function order(Request $request)
    {

        $maker = $this->maker->get_name_and_symbols_makers();
        $manufacturing  = $this->manufacturing->get_name_and_symbols_manufacturing();
        return view('order_management.order.index',
        [
            'request'=>$request,
            'manufacturing'=>$manufacturing,
            'maker' =>$maker
        ]);
    }
    public function order_import_file_excel(Request $request)
    {
        set_time_limit(999999999999);
        $data = $this->order->import_file_excel($request);
        
        if (count($data) > 0) {
            return redirect()->back()->with('danger_array', $data);
        } else {
            return redirect()->back()->with('success', __('Import By File Excel') . ' ' . __('Success'));
        }
    }
    public function po_management()
    {
        return view('order_management.po_management.index');
    }

    public function po_import_file_excel(Request $request)
    {
        set_time_limit(999999999999);
        $data = $this->po->import_file_excel($request);
        if (count($data) > 0) {
            return redirect()->back()->with('danger_array', $data);
        } else {
            return redirect()->back()->with('success', __('Import By File Excel') . ' ' . __('Success'));
        }
    }
    
}
