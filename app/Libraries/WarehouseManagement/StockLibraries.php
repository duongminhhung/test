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
use App\Models\WarehouseManagement\CommandImport;
use App\Models\WarehouseManagement\ProductInputFile;
use Illuminate\Support\Facades\DB;

class StockLibraries
{
    public function all($request)
    {
        $materials      = $request->materials;
        $type           = $request->type;
        $day = null;
        if ($type == 1) {
            $day = Carbon::now()->subMonth(3)->toDateTimeString();
        } else if ($type == 2) {
            $day = Carbon::now()->subMonth(6)->toDateTimeString();
        } else if ($type == 3) {
            $day = Carbon::now()->subMonth(12)->toDateTimeString();
        }


        $list_mater_in_import = ImportDetail::where('IsDelete', 0)
            ->when($materials, function ($query, $materials) {
                return $query->where('Materials_ID', $materials);
            })
            ->get('Materials_ID')
            ->unique('Materials_ID')
            ->pluck('Materials_ID')
            ->toArray();
        $array_maters_chunk = array_chunk($list_mater_in_import, 10);

        $list_location_in_stock = MasterLocation::where('IsDelete', 0)->where('Manufacturing_ID', 0)->get();

        $array_location_in_stock = [];
        foreach ($list_location_in_stock as $value) {
            array_push($array_location_in_stock, $value->ID);
        }
        $list_manufacturing     = MasterManufacturing::where('IsDelete', 0)->get();
        $array_locatin_with_manu = [];
        foreach ($list_manufacturing as $manufacturing) {
            $list_location_in_manu = MasterLocation::where('IsDelete', 0)->where('Manufacturing_ID', $manufacturing->ID)->get();
            $array_location_in_manu = [];
            foreach ($list_location_in_manu as $value) {
                array_push($array_location_in_manu, $value->ID);
            }
            $array_locatin_with_manu["manu-" . $manufacturing->ID] = $array_location_in_manu;
        }
        $list_materials = [];
        $materials = [];
        if (count($array_maters_chunk)  > 0) {
            for ($i = 0; $i < count($array_maters_chunk); $i++) {
                foreach ($array_maters_chunk[$i] as $val) {
                    $material = MasterMaterials::where('IsDelete', 0)->where('ID', $val)->first();
                    // dd($array_maters_chunk[$i]);
                    if ($material) {
                        $sum1 = ImportDetail::where('IsDelete', 0)
                            ->where('Materials_ID', $material->ID)
                            ->when($day, function ($query, $day) {
                                return $query->where('Time_Created', '<=', $day);
                            })
                            ->whereIn('Location', $array_location_in_stock)
                            ->sum('Inventory');
                        $sum_all = ImportDetail::where('IsDelete', 0)
                            ->where('Materials_ID', $material->ID)
                            ->when($day, function ($query, $day) {
                                return $query->where('Time_Created', '<=', $day);
                            })
                            ->sum('Inventory');
                        // dd($val);
                        $materials['CD']            = $material->CD;
                        $materials['Name']          = $material->Name;
                        $materials['Symbols']       = $material->Symbols;
                        $materials['Decription']    = $material->Decription;
                        $materials['Size']          = $material->Size;
                        $materials['Color']         = $material->Color;
                        $materials['Unit']          = $material->Unit;
                        $materials['Code']          = $material->Code;
                        $materials['stock_all']     = floatval($sum_all);
                        $materials['stock']         = floatval($sum1);
                        foreach ($array_locatin_with_manu as $key => $value) {
                            $sum2 = ImportDetail::where('IsDelete', 0)
                                ->when($day, function ($query, $day) {
                                    return $query->where('Time_Created', '<=', $day);
                                })
                                ->where('Materials_ID', $material->ID)
                                ->whereIn('Location', $value)
                                ->sum('Inventory');
                            $materials["" . $key] = floatval($sum2);
                        }
                        array_push($list_materials, $materials);
                    }
                }
            }
        }
        return collect($list_materials);
    }

    public function materials($request)
    {
        $materials  = $request->materials;
        if ($request->location) {
            $list_manufacturing     = MasterManufacturing::where('IsDelete', 0)->where('ID', $request->location)->get();
            $array_location_in_stock = [];
            foreach ($list_manufacturing as $manufacturing) {
                $list_location_in_manu = MasterLocation::where('IsDelete', 0)->where('Manufacturing_ID', $manufacturing->ID)->get();
                foreach ($list_location_in_manu as $value) {
                    array_push($array_location_in_stock, $value->ID);
                }
            }
        } else {
            $list_location_in_stock = MasterLocation::where('IsDelete', 0)->where('Manufacturing_ID', 0)->get();
            $array_location_in_stock = [];
            foreach ($list_location_in_stock as $value) {
                array_push($array_location_in_stock, $value->ID);
            }
        }
        $list_mater_in_import = ImportDetail::where('IsDelete', 0)
            ->when($materials, function ($query, $materials) {
                return $query->where('Materials_ID', $materials);
            })
            ->whereIn('Location', $array_location_in_stock)
            ->get('Materials_ID')
            ->unique('Materials_ID')
            ->pluck('Materials_ID')
            ->toArray();
        $array_maters_chunk = array_chunk($list_mater_in_import, 10);
        $list_materials = [];
        $materials = [];
        if (count($array_maters_chunk)  > 0) {
            for ($i = 0; $i < count($array_maters_chunk); $i++) {
                foreach ($array_maters_chunk[$i] as $val) {
                    $material = MasterMaterials::where('IsDelete', 0)->where('ID', $val)->first();
                    if ($material) {
                        $sum = ImportDetail::where('IsDelete', 0)
                            ->where('Materials_ID', $material->ID)
                            ->whereIn('Location', $array_location_in_stock)
                            ->sum('Inventory');
                        $materials['CD']            = $material->CD;
                        $materials['Name']          = $material->Name;
                        $materials['Symbols']       = $material->Symbols;
                        $materials['Decription']    = $material->Decription;
                        $materials['Size']          = $material->Size;
                        $materials['Color']         = $material->Color;
                        $materials['Unit']          = $material->Unit;
                        $materials['Code']          = $material->Code;
                        $materials['Lot']           = '';
                        $materials['Location']      = '';
                        $materials['stock']         = floatval($sum);
                        array_push($list_materials, $materials);
                    }
                }
            }
        }
        return collect($list_materials);
    }

    public function product_input($request)
    {
        // dd($request);

        $materials          = $request->materials ? $request->materials : "''";
        $manufacturing      = $request->manufacturing ? $request->manufacturing : "''";
        $from               = $request->from ? Carbon::create($request->from)->startOfDay()->toDateTimeString() : Carbon::now()->startOfMonth()->toDateTimeString();
        $to                 = $request->to ? Carbon::create($request->to)->endOfDay()->toDateTimeString() : Carbon::now()->endOfMonth()->toDateTimeString();
        $status             = $request->status ? $request->status : "''";
        $data_proceduce = DB::select(
            "
            EXEC dbo.get_data_product_input_file 
                @materials      = " . $materials . ",
                @manufacturing  = " . $manufacturing . ",
                @from           = '" . $from . "',
                @to		        = '" . $to . "',
                @status         = " . $status . ";"
        );

        return $data_proceduce;
    }

    private function read_file($request)
    {
        // try {

        $file     = request()->file('fileImport');
        $name     = $file->getClientOriginalName();
        $arr      = explode('.', $name);
        $fileName = strtolower(end($arr));

        if ($fileName != 'xlsx' && $fileName != 'xls' &&  $fileName != 'slk') {
            return redirect()->back();
        } else if ($fileName == 'xls') {
            $reader  = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        } else if ($fileName == 'xlsx') {

            $reader  = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        } else if ($fileName == 'slk') {

            $reader  = new \PhpOffice\PhpSpreadsheet\Reader\Slk();
        }
        // dd($file);
        // try {
        $spreadsheet = $reader->load($file);
        $data        = $spreadsheet->getActiveSheet()->toArray();
        return $data;
        // } catch (\Exception $e) {
        //     return ['danger' => __('Select The Standard ".xlsx" Or ".xls" File')];
        // }
        // } catch (\Exception $e) {
        //     return ['danger' => __('Error Something')];
        // }
    }
    public function import_file_excel($request)
    {
        
        $file     = request()->file('fileImport');
        $name     = $file->getClientOriginalName();
        $arr      = explode('.', $name);
        $fileName = strtolower(end($arr));
        $data = $this->read_file($request);
        $user_created = Auth::user()->id;
        $user_updated = Auth::user()->id;
        $dem = 0;
        $array_data_import = [];
        $error = [];

        $day = Carbon::now()->day;
        $month = Carbon::now()->month;
        $year  = Carbon::now()->year;
        $second  = Carbon::now()->second;
        if(count($data) > 1)
        {
            $data_export = ImportDetail::where('IsDelete',0)->where('Inventory','>',0)->get();
            foreach($data_export as $val)
            {
                $export_detail = ExportDetail::where('IsDelete',0)->where('Status',2)->where('Command_Import_ID',$val->ID)->first();
                if($export_detail)
                {
                    ImportDetail::where('ID',$val->ID)->update([
                        'Inventory' => 0,
                        'Quantity'  => $export_detail->Quantity_Exported
                    ]);
                }
                else
                {
                    ImportDetail::where('ID',$val->ID)->update([
                        'IsDelete'  => 1
                    ]);
                }
            }

            $command_new = CommandImport::create([
                'PO'            => 'Import File Stock '.$year.''.$month.''.$day.''.$second,
                'Name'          => 'Import File Stock '.$year.''.$month.''.$day.''.$second,
                'Symbols'       => 'Import File Stock '.$year.''.$month.''.$day.''.$second,
                'Type'          => 3,
                'User_Created'  => $user_created,
                'User_Updated'  => $user_updated,
                'IsDelete'      => 0
            ]);
            // dd($data);
            $find_command_new = CommandImport::where('IsDelete',0)
                                            ->where('PO',$command_new->PO)
                                            ->where('Name',$command_new->Name)
                                            ->where('Symbols',$command_new->Symbols)
                                            ->first();
            foreach($data as $key => $value){
                if($key > 0 && $value[1] != '')
                {
                    // dd($value);
                    $check_materials = MasterMaterials::where('IsDelete',0)
                                                        ->where('Name',strval($value[1]))
                                                        ->where('Symbols',strval($value[2]))
                                                        // ->where('Size',strval($value[4]))
                                                        // ->where('Color',strval($value[5]))
                                                        ->first();
                                                        // dd($check_materials);
                    $location = MasterLocation::where('IsDelete',0)->where('Symbols',$value[10])->first();
                    if($check_materials)
                    {
                        $obj_data = [
                            'Command_ID'    => $find_command_new->ID,
                            'Materials_ID'  => $check_materials->ID,
                            'Lot'           => 'No.' . $dem,
                            'Maker'         => $check_materials->Maker,
                            'Location'      => $location->ID,
                            'Type'          => 1,
                            'Status'        => 1,
                            'Quantity'      => floatval(str_replace(',','',$value[9])),
                            'Inventory'     => floatval(str_replace(',','',$value[9])),
                            'Time_Import'   => Carbon::now(),
                            'Time_Created'  => Carbon::now(),
                            'Time_Updated'  => Carbon::now(),
                            'User_Created'  => $user_created,
                            'User_Updated'  => $user_updated,
                            'IsDelete'      => 0
                        ];
                        $dem++;
                        array_push($array_data_import,$obj_data);
                    }
                }
            }
        }
        else
        {
            $er = 'File Không Có Dữ Liệu';
            array_push($error, $er);
        }
        if(count($error) <= 0)
        {
            $array_in_chunk = array_chunk($array_data_import, 50);
            // dd($array_in_chunk);
            for ($i = 0; $i < count($array_in_chunk); $i++) 
            {
                $maters = DB::table('Import_Detail')->insert($array_in_chunk[$i]);
            }
        }
        return $error;

        // dd($array_data_import);
        
    }
}
