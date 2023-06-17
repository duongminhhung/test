<?php

namespace App\Libraries\WarehouseManagement;


use App\Models\WarehouseManagement\CommandExport;
use App\Models\WarehouseManagement\ExportDetail;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\MasterData\MasterWarehouseDetail;
use App\Mail\MailNotify;
use App\Models\MasterData\MasterBOM;
use App\Models\MasterData\MasterLocation;
use App\Models\MasterData\MasterMaterials;
use App\Models\MasterData\MasterProduct;
use App\Models\WarehouseManagement\ImportDetail;
use App\Http\Controllers\Api\ApiSystemController;
use App\Models\MasterData\MasterManufacturing;
use App\Models\WarehouseManagement\MaterialSupplyCertificate;
use App\Models\WarehouseManagement\ProductInputFile;
use Illuminate\Support\Facades\DB;

class MaterialSupplyCertificateLibraries
{
    public function get_all_list()
    {
        return  MaterialSupplyCertificate::where('IsDelete', 0)->get();
    }

    public function get_list_export($request)
    {
        // dd($request);
        $Order       = $request->Order;
        $type        = $request->Type;
        switch ($type) {
            case 'Bao Bì':
                $type = 1;
                break;
            case 'SHIAGE':
                $type = 2;
                break;
            case 'MISHIN';
                $type = 3;
                break;
            case 'SAIDAN':
                $type = 4;
                break;
        }
        // dd($type);
        $data        =  MaterialSupplyCertificate::where('IsDelete', 0)
            ->where('Order', $Order)
            ->where('Type', $type)
            ->orderBy('ID','DESC')
            // ->orderBy('Type','DESC')
            ->get();
            // ->paginate($request->length);
        // dd($data);
        foreach ($data as $value) {
            // lay location
            // dd($value);
            $location = MasterLocation::where('IsDelete', 0)->where('Manufacturing_ID', 0)->get();
            $manu = $location->map(function ($item) {
                return $item->ID;
            });
            // dd($manu);
            $arr_location = [];
            $stock_materials = ImportDetail::where('IsDelete', 0)->where('Materials_ID', $value->Materials_ID)->where('Inventory', '>', 0)->whereIn('Location', $manu)->get();
            // dd($manu);
            if (count($stock_materials) > 0) {
                $quantity_need = $value->Quantity;
                foreach ($stock_materials as $stock) {
                    if ($quantity_need > 0) {
                        array_push($arr_location, $stock->location);
                        $quantity_need = $quantity_need - $stock->Inventory;
                    }
                }
            }
            $value['Location'] = array_unique($arr_location);
        }
        // dd($data);
        return $data;
    }
}
