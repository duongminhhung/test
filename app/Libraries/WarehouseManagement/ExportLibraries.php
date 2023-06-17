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

class ExportLibraries
{
    private $api;

    public function __construct(
        ApiSystemController $ApiSystemController,
    ) {
        $this->api = $ApiSystemController;
    }
    public function get_all_list()
    {
        return  CommandExport::where('IsDelete', 0)->get(['ID', 'Name']);
    }

    public function get_command_width_id($request)
    {
        return  CommandExport::where('IsDelete', 0)->where('ID', $request->ID)->first();
    }

    public function get_list_export($request)
    {
        // dd($request);
        $command_id = $request->ID;
        $data = CommandExport::where('IsDelete',0)->where('ID',$command_id)->first();
        return $data;
    }

    public function check_data($request)
    {
        // dd($request);
        $erorr = [];
        if ($request->Name) {
            $check_isset =  CommandExport::where('IsDelete', 0)->where('Name', $request->Name)->first();
            if ($check_isset) {
                $er = __('Names that already exist');
                array_push($erorr, $er);
            }
        }
        if ($request->From == '') {
            $er = __('From cant be left blank');
            array_push($erorr, $er);
        }
        if ($request->To == '') {
            $er = __('To cant be left blank');
            array_push($erorr, $er);
        }
        if ($request->Quantity == '') {
            $er = __('Quantity cant be left blank');
            array_push($erorr, $er);
        }
        if ($request->Type == 1) {
            if ($request->Materials_ID == '') {
                $er = __('Materials cant be left blank');
                array_push($erorr, $er);
            }
        } else {
            if ($request->Product_ID == '') {
                $er = __('Product cant be left blank');
                array_push($erorr, $er);
            }
        }

        if (count($erorr) > 0) {
            return (object)[
                'status'      => false,
                'data'        => $erorr
            ];
        } else {
            return (object)[
                'status'      => true,
                'data'        => []
            ];
        }
    }

    public function export_add($request)
    {
        $date =  date_format(Carbon::now(), "YmdHsi");
        // dd($date);
        $data = CommandExport::create([
            'Name'          => $request->Name ? $request->Name : 'LX-' . $date,
            'From'          => $request->From,
            'To'            => $request->To,
            'Materials'     => $request->Materials_ID,
            'Product'       => $request->Product_ID,
            'Quantity'      => $request->Quantity,
            'Type'          => $request->Type,
            'Status'        => 1,
            'Note'          => $request->Note,
            'Time_Created'  => Carbon::now(),
            'Time_Updated'  => Carbon::now(),
            'User_Created'  => Auth::user()->id,
            'User_Updated'  => Auth::user()->id,
            'IsDelete'      => 0
        ]);

        $this->accept((object)[
            'ID' => $data->ID,

        ]);

        return (object)[
            'status' => true,
            'data'     => $data
        ];
    }
    public function cancel($request)
    {
        // dd($request);
        return CommandExport::where('IsDelete', 0)
            ->where('ID', $request->ID)
            ->update([
                'User_Updated'     => Auth::user()->id,
                'Status'         => 3
            ]);
    }

    public function success($request)
    {
        return CommandExport::where('IsDelete', 0)
            ->where('ID', $request->ID)
            ->update([
                'User_Updated'     => Auth::user()->id,
                'Status'         => 2
            ]);
    }

    public function accept($request)
    {
        // dd($request);
        $data =  CommandExport::where('IsDelete', 0)
            ->where('ID', $request->ID)
            ->first();
        $location = MasterLocation::where('Manufacturing_ID', 0)->get()->map(function ($item) {
            return $item->ID;
        });
        $quantity_need = $data->Quantity;
        // dd($location);
        // export materials
        if ($data->Materials) {
            $request->Materials_ID = $data->Materials;
            $list = ImportDetail::where('IsDelete', 0)->whereIn('Location', $location)->where('Materials_ID', $request->Materials_ID)->Where('Inventory', '>', 0)->get();
            $arr = [];
            foreach ($list as $value) {
                if ($quantity_need > 0) {
                    $arr1 = [
                        'Command_ID'            => $data->ID,
                        'Command_Import_ID'     => $value->ID,
                        'Materials_ID'          => $value->Materials_ID,
                        'Quantity'              => ($quantity_need > $value->Inventory ? $value->Inventory : $quantity_need),
                        'Location_ID'           => $value->Location,
                        'Lot'                   => $value->Lot,
                        'Maker'                 => $value->Maker,
                        'Status'                => 1,
                        'Type'                  => 1,
                        'User_Created'          => Auth::user()->id,
                        'User_Updated'          => Auth::user()->id,
                        'Time_Created'          => Carbon::now(),
                        'Time_Updated'          => Carbon::now(),
                        'IsDelete'              => 0
                    ];
                    array_push($arr, $arr1);
                    $quantity_need = $quantity_need - $value->Inventory;
                }
            }
            foreach ($arr as $value) {
                ExportDetail::Create($value);
            }
        }
        // export product
        if ($data->Product) {
            $product_bom = MasterBOM::where('IsDelete', 0)->where('Product_ID', $data->Product)->get('Materials_ID');
            $product_bom = $product_bom->map(function ($value) {
                return $value->Materials_ID;
            });
            $list = ImportDetail::where('IsDelete', 0)->where('Manufacturing', null)->whereIn('Materials_ID', $product_bom)->Where('Inventory', '>', 0)->get();
            $arr = [];
            foreach ($list as $value) {
                // dd($value);
                $arr1 = [
                    'Command_ID'     => $data->ID,
                    'Materials_ID'   => $value->Materials_ID,
                    'Command_Import_ID'   => $value->ID,
                    'Quantity'       => $value->Inventory,
                    'Location_ID'    => $value->Location,
                    'Lot'            => $value->Lot,
                    'Maker'          => $value->Maker,
                    'Status'         => 1,
                    'Type'           => 1,
                    'User_Created'   => Auth::user()->id,
                    'User_Updated'   => Auth::user()->id,
                    'Time_Created'   => Carbon::now(),
                    'Time_Updated'   => Carbon::now(),
                    'IsDelete'       => 0
                ];
                array_push($arr, $arr1);
            }
            foreach ($arr as $value) {
                ExportDetail::Create($value);
            }
        }
    }

    public function get_all_list_detail($request)
    {

        return ExportDetail::where('IsDelete', 0)
            ->where('Export_ID', $request->ID)
            ->with('materials', 'location', 'product')
            ->get();
    }

    // export select
    public function check_export($request)
    {
        $erorr = [];
        $data  = ExportDetail::where('IsDelete', 0)->where('ID', $request->ID)->first();
        $data1 =  ImportDetail::where('IsDelete', 0)->where('ID', $data ? $data->Command_Import_ID : '')->first();
        
        if($request->Array_ID)
        {
            foreach($request->Array_ID as $value)
            {
                $data  = ExportDetail::where('IsDelete', 0)->where('ID', $value)->first();
                $data1 =  ImportDetail::where('IsDelete', 0)->where('ID', $data ? $data->Command_Import_ID : '')->first();
                if (($data1 ? $data1->Inventory : '') < $data->Quantity) {
                    $er = $data1->materials->Symbols . ' '.__('Quantity Stock Lower Quantity Export');
                    array_push($erorr, $er);
                }
            }
        }
        // if(!$request->Array_ID)
        // {
            // dd($erorr);
            if (($data1 ? $data1->Inventory : '') < $request->Quantity_Export_Change) {
                $er = $data1->materials->Symbols . ' '.__('Quantity Stock Lower Quantity Export');
                array_push($erorr, $er);
            }
    
            if (($data ? $data->export->Materials : '') && $data->Quantity < $data->Quantity_Exported + $request->Quantity_Export_Change) {
                $er = __('Quantity Export Bigger Quantity Request In Command');
                array_push($erorr, $er);
            }
        // }

        // dd($erorr);
        if (count($erorr) > 0) {
            return (object)[
                'status'      => false,
                'data'        => $erorr
            ];
        } else {
            return (object)[
                'status'      => true,
                'data'        => []
            ];
        }
        // dd($erorr);
    }

    public function export($request)
    {
        // dd($request->Array_ID);
        // $quantityyyy = $request->Quantity_Export_Change;

        $command_export     = CommandExport::where('IsDelete', 0)->where('ID', $request->Command_ID)->first();
        $location           = MasterLocation::where('IsDelete', 0)->where('Manufacturing_ID', $command_export->To)->first();

        if ($request->Array_ID) {
            // dd('run');
            foreach ($request->Array_ID as $value) {
                // dd($value);
                $data               = ExportDetail::where('IsDelete', 0)->where('ID', $value)->first();
                if ($data) {
                    $data_stock         = ImportDetail::where('IsDelete', 0)->where('ID', $data->Command_Import_ID)->first();
                    $data_stock_manu    = ImportDetail::where('IsDelete', 0)
                        ->where('Maker', $data->Maker)
                        ->where('Materials_ID', $data->Materials_ID)
                        ->where('Lot', $data->Lot)
                        ->where('Location', $location->ID)
                        ->first();

                    ExportDetail::where('IsDelete', 0)
                        ->where('ID', $data->ID)
                        ->update([
                            'Status'           => 2,
                            'Quantity_Exported'=> $data->Quantity,
                            'User_Updated'     => Auth::user()->id
                        ]);

                    ImportDetail::where('IsDelete', 0)
                        ->where('ID', $data_stock->ID)
                        ->update([
                            'Inventory'        => $data_stock->Inventory - $data->Quantity,
                            'User_Updated'     => Auth::user()->id
                        ]);
                    if ($data_stock_manu) {
                        ImportDetail::where('ID', $data_stock_manu->ID)->update([
                            'Quantity' => $data_stock_manu->Quantity + $data->Quantity,
                            'Inventory' => $data_stock_manu->Inventory + $data->Quantity,
                        ]);
                    } else {
                        if ($command_export->Type != 3) {

                            ImportDetail::create([
                                'Materials_ID'  => $data->Materials_ID,
                                'Location'      => $location->ID,
                                'Lot'           => $data->Lot,
                                'Maker'         => $data_stock->Maker,
                                'Type'          => 3,
                                'Status'        => 1,
                                'Quantity'      => $data->Quantity,
                                'Time_Import'   => Carbon::now(),
                                'Inventory'     => $data->Quantity,
                                'User_Created'  => Auth::user()->id,
                                'User_Updated'  => Auth::user()->id,
                                'Time_Created'  => Carbon::now(),
                                'Time_Updated'  => Carbon::now(),

                            ]);
                        }
                    }
                }
            }
        } else {
            $data    = ExportDetail::where('IsDelete', 0)->where('ID', $request->ID)->first();
            // dd($command_export);
            if ($data) {
                $data_stock         = ImportDetail::where('IsDelete', 0)->where('ID', $data->Command_Import_ID)->first();
                $data_stock_manu    = ImportDetail::where('IsDelete', 0)
                    ->where('Maker', $data->Maker)
                    ->where('Materials_ID', $data->Materials_ID)
                    ->where('Lot', $data->Lot)
                    ->where('Location', $location->ID)
                    ->first();

                ExportDetail::where('IsDelete', 0)
                    ->where('ID', $data->ID)
                    ->update([
                        'Quantity_Exported' => $data->Quantity_Exported + $request->Quantity_Export_Change,
                        'Status'            => 2,
                        'User_Updated'      => Auth::user()->id
                    ]);

                ImportDetail::where('IsDelete', 0)
                    ->where('ID', $data_stock->ID)
                    ->update([
                        'Inventory'        => $data_stock->Inventory - $request->Quantity_Export_Change,
                        'User_Updated'     => Auth::user()->id
                    ]);
                if ($data_stock_manu) {
                    ImportDetail::where('ID', $data_stock_manu->ID)->update([
                        'Quantity' => $data_stock_manu->Quantity + $request->Quantity_Export_Change,
                        'Inventory' => $data_stock_manu->Inventory + $request->Quantity_Export_Change,
                    ]);
                } else {
                    if ($command_export->Type != 3) {
                        ImportDetail::create([
                            'Materials_ID'  => $data->Materials_ID,
                            'Location'      => $location->ID,
                            'Lot'           => $data->Lot,
                            'Maker'         => $data_stock->Maker,
                            'Type'          => 3,
                            'Status'        => 1,
                            'Quantity'      => $request->Quantity_Export_Change,
                            'Time_Import'   => Carbon::now(),
                            'Inventory'     => $request->Quantity_Export_Change,
                            'Time_Created'  => Carbon::now(),
                            'Time_Updated'  => Carbon::now(),
                            'User_Created'  => Auth::user()->id,
                            'User_Updated'  => Auth::user()->id

                        ]);
                    }
                }
            }
        }

        return (object)[
            'status' => true,
            'data'     => $data
        ];
    }

    // export scan
    public function check_export_scan($request)
    {
        // dd($request);
        $erorr = [];
        if (!$request->Location) {
            $er = __('Location cant be left blank');
            array_push($erorr, $er);
        }
        if (!$request->Quantity) {
            $er = __('Quantity cant be left blank');
            array_push($erorr, $er);
        }
        if (!$request->Materials_ID) {
            $er = __('Materials cant be left blank');
            array_push($erorr, $er);
        }
        // if (!$request->Lot) {
        //     $er = __('Lot cant be left blank');
        //     array_push($erorr, $er);
        // }
        $location = MasterLocation::where('IsDelete', 0)
                                ->where('Symbols', $request->Location)
                                ->first();

        if ($request->Type_Action == 2) {
            $location     = MasterLocation::where('IsDelete', 0)->where('ID', $request->Location)->first();
        }
        // dd($request);
        $data =  ExportDetail::where('IsDelete', 0)
            ->where('Command_ID', $request->Command_ID)
            ->where('Materials_ID', $request->Materials_ID)
            ->where('Lot', $request->Lot)
            ->where('Location_ID', $location->ID)
            // ->where('Status', 1)
            ->orderBy('ID', 'desc')
            ->first();
        // dd($request);

        if (!$location || ($location ? $location->ID : '') != $data->Location_ID) {
            $er = __('Location Not Define');
            array_push($erorr, $er);
        }
        if (!$data) {
            $er = __('Materials Exported');
            array_push($erorr, $er);
        }
        $data1 =  ImportDetail::where('IsDelete', 0)
            ->where('Materials_ID', $data ? $data->Materials_ID : '')
            ->where('Lot', $data ? $data->Lot : '')
            ->where('Location', $location->ID)
            // ->where('Manufacturing', null)
            ->orderBy('ID', 'DESC')
            ->where('Inventory', '>', 0)
            ->get();

        if (($data1 ? $data1->sum('Inventory') : '') < 0) {
            $er = __('Materials Not Define');
            array_push($erorr, $er);
        }

        $command = CommandExport::where('IsDelete', 0)
            ->where('ID', $request->Command_ID)
            ->first();
        if (!$command) {
            $er = __('Command Not Define');
            array_push($erorr, $er);
        }
        // dd($command, $location);
        // if($command && $location)
        // {
        //     if ($command->To != $location->Manufacturing_ID) {
        //         $er = __('Location Import Wrong With Command');
        //         array_push($erorr, $er);
        //     }
        // }
        $quan1 = floatval(collect($command->detail->where('Status', 2))->sum('Quantity'));

        if (($data1 ? $data1->sum('Inventory') : '') < $request->Quantity) {
            $er = __('Quantity Stock Lower Quantity Export');
            array_push($erorr, $er);
        }


        // if (($data ? $data->export->Materials : '') && $quan1 >= ($data ? $data->export->Quantity : '')) {
        //     $er = __('Quantity Export Bigger Quantity Request In Command');
        //     array_push($erorr, $er);
        // }
        if (($data ? $data->export->Materials : '') && $data->Quantity < $data->Quantity_Exported + $request->Quantity) {
            $er = __('Quantity Export Bigger Quantity Request In Command');
            array_push($erorr, $er);
        }

        // dd($erorr);
        if (count($erorr) > 0) {
            return (object)[
                'status'      => false,
                'data'        => $erorr
            ];
        } else {
            return (object)[
                'status'      => true,
                'data'        => []
            ];
        }
        // dd($erorr);
    }

    public function export_scan($request)
    {
        // dd($request);
        $location = MasterLocation::where('IsDelete', 0)->where('Symbols', $request->Location)->first();
        if ($request->Type_Action == 2) {
            $location     = MasterLocation::where('IsDelete', 0)->where('ID', $request->Location)->first();
        }
        $command_export = CommandExport::where('IsDelete', 0)->where('ID', $request->Command_ID)->first();
        $location_manu  = MasterLocation::where('IsDelete', 0)->where('Manufacturing_ID', $command_export->To)->first();

        $data1 =  ImportDetail::where('IsDelete', 0)
            ->where('Materials_ID', $request->Materials_ID)
            ->where('Location', $location->ID)
            ->where('Lot', $request->Lot)
            ->orderBy('Time_Import')
            ->where('Inventory', '>', 0)
            ->first();

        $data =  ExportDetail::where('IsDelete', 0)
            ->where('Command_ID', $request->Command_ID)
            ->where('Materials_ID', $request->Materials_ID)
            ->where('Location_ID', $location->ID)
            ->where('Lot', $request->Lot)
            ->where('Command_Import_ID', $data1->ID)
            ->orderBy('ID')
            ->first();
        // dd($data1);

        $data_stock_manu = ImportDetail::where('IsDelete', 0)
            ->where('Materials_ID', $data1->Materials_ID)
            ->where('Manufacturing', $location_manu->Manufacturing_ID)
            ->where('Location', $location_manu->ID)
            ->where('Lot', $data1->Lot)
            ->where('Maker', $data1->Maker)
            ->where('Type', 3)
            ->orderBy('Time_Import')
            ->where('Inventory', '>', 0)
            ->first();


        ExportDetail::where('IsDelete', 0)
            ->where('ID', $data->ID)
            ->update([
                'Quantity_Exported' => $data->Status == 2 ? $data->Quantity_Exported + $request->Quantity : $request->Quantity,
                'Status'            => 2,
                'User_Updated'      => Auth::user()->id
            ]);

        // ExportDetail::where('IsDelete', 0)
        //     ->where('Status', 1)
        //     ->where('Lot', $data->Lot)
        //     ->where('Location_ID', $location->ID)
        //     ->Where('Materials_ID', $data->Materials_ID)
        //     ->Where('Command_Import_ID', $data->Command_Import_ID)
        //     ->update([
        //         'Quantity' => $data->Quantity_Exported - $request->Quantity
        //     ]);

        ImportDetail::where('IsDelete', 0)
            ->where('ID', $data1->ID)
            ->update([
                'Inventory'        => $data1->Inventory - $request->Quantity,
                'User_Updated'     => Auth::user()->id
            ]);
        if ($data_stock_manu) {
            ImportDetail::where('ID', $data_stock_manu->ID)->update([
                'Quantity' => $data_stock_manu->Quantity + $request->Quantity,
                'Inventory' => $data_stock_manu->Inventory + $request->Quantity,
            ]);
        } else {
            if ($command_export->Type != 3) {
                ImportDetail::create([
                    'Materials_ID'  => $data->Materials_ID,
                    // 'Manufacturing' => $location_manu->Manufacturing_ID,
                    'Location'      => $location_manu->ID,
                    'Lot'           => $data->Lot,
                    'Maker'         => $data1->Maker,
                    'Type'          => 3,
                    'Status'        => 1,
                    'Quantity'      => $request->Quantity,
                    'Time_Import'   => Carbon::now(),
                    'Inventory'     => $request->Quantity,
                    'Time_Created'  => Carbon::now(),
                    'Time_Updated'  => Carbon::now(),
                    'User_Created'  => Auth::user()->id,
                    'User_Updated'  => Auth::user()->id

                ]);
            }
        }

        return (object)[
            'status' => true,
            'data'     => $data
        ];
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
        // dd($data);
        $error = [];
        $obj = (object)[
            "Name" => $request->Name_Command,
            "From" => $request->From,
            "To"   => $request->To,
            "Materials" => [],
            "Arr_ID_Materials" => [],
        ];
        // dd(collect($data)->GroupBy('1'));

        if ($request->Type == 1) {
            foreach (collect($data)->GroupBy('1') as $key => $value) {
                if ($key != 'MÃ NVL') {
                    $value1 = collect($value)->first();
                    $check_materials = MasterMaterials::where('IsDelete', 0)->where('Name', '' . $value1[0])->where('Symbols', '' . $value1[1])->first();
                    if ($check_materials) {
                        $quan = 0;
                        foreach ($value as $value1) {
                            $quan += floatval($value1[2]);
                        }
                        $mater = (object)[
                            "Materials_ID" => $check_materials->ID,
                            "Quantity" =>  $quan
                        ];
                        array_push($obj->Materials, $mater);
                        array_push($obj->Arr_ID_Materials, $check_materials->ID);
                    } else {
                        $er = __('NVL') . ' ' . $value1[0] . ' ' . __('không tồn tại');
                        array_push($error, $er);
                    }
                }
            }
            if (count($error) <= 0) {

                $data = $this->api->create_command_export_materials($obj);
                if ($data->status) {
                    return [];
                } else {
                    return $data->message;
                }
            } else {
                return $error;
            }
        } else {
            $array_data_input = [];
            $dem = 0;
            foreach ($data as $key => $value) {
                if ($key >= 5) {
                    $check_product = MasterProduct::where('IsDelete', 0)
                        ->where('Code', '' . $value[0])
                        ->first();
                    // dd($check_product,$check_product->bom_nvl);
                    if ($check_product) {
                        $quantity = floatval($value[1]);
                        foreach (collect($check_product->bom_nvl) as $value_mater) {
                            $bom = MasterBOM::where('Product_ID', $check_product->ID)->where('Materials_ID', $value_mater->ID)->first();
                            if ($bom) {
                                $dem++;
                                $quantity_use = $quantity * $bom->Quantity_Materials;
                                $value_mater['quantity_use'] = round($quantity_use, 2);
                                $value_mater['stt'] = $dem;
                                array_push($array_data_input, $value_mater);
                            }
                        }
                    }
                }
            }
            foreach (collect($array_data_input)->groupBy('ID') as $value) {
                // dd($array_data_input);
                $value1 = collect($value)->first();
                $quan = 0;
                foreach ($value as $value1) {
                    $quan += floatval($value1['quantity_use']);
                }
                $mater = (object)[
                    "Materials_ID" => $value1->ID,
                    "Quantity" =>  $quan
                ];
                array_push($obj->Materials, $mater);
                array_push($obj->Arr_ID_Materials, $value1->ID);
            }
            if (count($error) <= 0) {
                $data = $this->api->create_command_export_materials($obj);
                if ($data->status) {
                    return [];
                } else {
                    return $data->message;
                }
            } else {
                return $error;
            }
        }
    }
}
