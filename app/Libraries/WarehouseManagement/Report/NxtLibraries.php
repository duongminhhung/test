<?php

namespace App\Libraries\WarehouseManagement\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterData\MasterMaterials;
use App\Models\MasterData\MasterSupplier;
use Auth;
use Carbon\Carbon;
use App\Models\MasterData\MasterLocation;
use App\Models\MasterData\MasterMaker;
use App\Models\WarehouseManagement\ImportDetail;
use Illuminate\Support\Facades\DB;

class NxtLibraries
{

    public function index(Request $request)
    {
        $mater  = $request->Materials;
        $from   = $request->from ? Carbon::create($request->from)->startOfDay()->toDateTimeString() : Carbon::now()->startOfMonth()->toDateTimeString();
        $to     = $request->to ? Carbon::create($request->to)->endOfDay()->toDateTimeString() : Carbon::now()->endOfMonth()->toDateTimeString();

        $list_ware = MasterLocation::where('IsDelete', 0)
            ->where('Manufacturing_ID', $request->Manufacturing ? $request->Manufacturing : 0)
            ->get()
            ->map(function ($item) {
                return $item->ID;
            });

        $all_mater = ImportDetail::where('IsDelete', 0)->where('Status', 1)
            ->whereIn('Location', $list_ware)
            ->where(function ($query) use ($from) {
                $query->where('Inventory', '>', 0)
                    ->orWhere('Time_Updated', '>=', $from);
            })
            ->when($mater, function ($query, $mater) {
                return $query->where('Materials_ID', $mater);
            })
            ->get()
            ->unique('Materials_ID')
            ->pluck('Materials_ID')
            ->toArray();

            // dd($all_mater);
        $array_all_mater_chunk = array_chunk($all_mater,15);
        $array = [];
        // for ($i = 0; $i < count($array_all_mater_chunk); $i++) {
            foreach ($all_mater as $val) {
            // foreach ($array_all_mater_chunk[$i] as $val) {
                $material = MasterMaterials::where('IsDelete', 0)
                    ->where('ID', $val)
                    ->first();
                    // dd($material);
                $arr = [];
                for ($i = 1; $i <= 6; $i++) {
                    $data_proceduce = DB::select(
                        "EXEC dbo.get_import_export_inventory_with_materials 
                                                @materials      = " . $material->ID . ",
                                                @manufacturing  = " . ($request->Manufacturing ? $request->Manufacturing : 0) . ",
                                                @from           = '" . $from . "',
                                                @to		        ='" . $to . "',
                                                @func           = " . $i . ";"
                    );

                    $data = $data_proceduce[0];
                    switch ($i) {
                        case 1:
                            $first_quan = $data->dt;
                            break;
                        case 2:
                            $im1_quan = $data->dt;
                            break;
                        case 3:
                            $im2_quan = $data->dt;
                            break;
                        case 4:
                            $first_quan -= $data->dt;
                            break;
                        case 5:
                            $ex1_quan = $data->dt;
                            break;
                        case 6:
                            $ex2_quan = $data->dt;
                            break;
                    }
                }
                $arr['Name']      = $material->Name;
                $arr['Symbols']   = $material->Symbols;
                $arr['first1']    = $first_quan > 0 ?  floatval($first_quan) : 0;
                $arr['imm1']      = $im1_quan > 0 ?  floatval($im1_quan) : 0;
                $arr['imm2']      = $im2_quan > 0 ?  floatval($im2_quan) : 0;
                $arr['exx1']      = $ex1_quan > 0 ?  floatval($ex1_quan) : 0;
                // $arr['exx2']      = $ex2_quan > 0 ?  floatval($ex2_quan) : 0;
                $arr['stock_end']      = ($first_quan + $im1_quan + $im2_quan  - $ex1_quan - $ex2_quan ) < 0.000000001 ? 0 : ($first_quan + $im1_quan + $im2_quan - $ex1_quan -$ex2_quan );
                if ($first_quan > 0 || $im1_quan > 0 || $im2_quan > 0 ||  $ex1_quan > 0 || $ex2_quan > 0) {
                    array_push($array, collect($arr));
                }
            }
        // }
    
        return ($array);
    }
}