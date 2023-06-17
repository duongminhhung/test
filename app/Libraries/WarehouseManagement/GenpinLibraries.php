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
use App\Models\MasterData\MasterProduct;
use App\Models\MasterData\MasterMaterials;
use App\Models\WarehouseManagement\ImportDetail;
use App\Models\WarehouseManagement\GenpinMaterials;
use App\Http\Controllers\Api\ApiSystemController;
use Illuminate\Support\Facades\DB;

class GenpinLibraries 
{
    private $api;
    public function __construct(
        ApiSystemController $ApiSystemController,
    ){
        $this->api = $ApiSystemController;
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
            } 
            else if($fileName == 'xls')
            {
                $reader  = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } 
            else if($fileName == 'xlsx') 
            {

                $reader  = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }
            else if($fileName == 'slk') 
            {

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
    public function import_file_Excel($request)
    {
        $file     = request()->file('fileImport');
        $name     = $file->getClientOriginalName();
        $arr      = explode('.', $name);
        $fileName = strtolower(end($arr));
        $data = $this->read_file($request);
        $user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
        $array_data_input = [];
        foreach(collect($data)->where('32','>',0)->groupBy(['3','11','12','13']) as $key => $value)
        {
            $check_manufacturing = DB::table('Manufacturing_Code')
            ->where('IsDelete',0)
            ->where('Code',''.$key)
            ->first();
            if($check_manufacturing)
            {
                $array_data_input[$check_manufacturing->ID] = [];
                foreach($value as $key1 => $value1)
                {
                    $product_hinban = $key1;
                    foreach($value1 as $key2 => $value2)
                    {
                        if($key2 != '*')
                        {
                            $product_sz = $key2;  
                        }
                        else
                        {
                            $text =  substr($product_hinban,'-3',2);
                            $product_sz = Floatval($text)*2;
                        }
                        foreach($value2 as $key3 => $value3)
                        {
                            $product_cl = $key3;
                            $quantity = collect($value3)->sum(32);
                            $dem = 0;
                            if($quantity > 0)
                            {
                                $check_product = MasterProduct::where('IsDelete',0)
                                    ->where('Hinban',''.$product_hinban)
                                    ->where('Size',$product_sz)
                                    ->where('Color',''.$product_cl)
                                    ->first();
                                if($check_product && $check_manufacturing)
                                {
                                    foreach(collect($check_product->bom_nvl)->where('Type','SUB-MATERIAL') as $value_mater)
                                    {
                                        $bom = MasterBOM::where('Product_ID',$check_product->ID)->where('Materials_ID',$value_mater->ID)->first();
                                        if($bom)
                                        {
                                            $dem++;
                                            $quantity_use = $quantity*$bom->Quantity_Materials;
                                            $value_mater['product']   = $check_product;
                                            $value_mater['manufacturing'] = $check_manufacturing;
                                            $value_mater['quantity_use'] = round($quantity_use,2);
                                            $value_mater['stt'] = $dem;
                                            array_push($array_data_input[$check_manufacturing->ID],$value_mater);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        foreach($array_data_input as $key => $value)
        {
            foreach(collect($value)->groupBy('ID') as $key1 => $value1)
            {
                $quantity_use = collect($value1)->sum('quantity_use');
                if($quantity_use > 0)
                {
                    $data =  $this->api->export_stock($key1,$key,$quantity_use,2,1);
                    $genpin = GenpinMaterials::create([
                            'Manufacturing_ID' => $key,
                            'Name_File'        => $name,
                            'Materials_ID'     => $key1,
                            'Status_export'    => $data->status,
                            'Message_export'   => $data->message,
                            'Quantity'         => $quantity_use,
                            'User_Created'     => $user_created,
                            'User_Updated'     => $user_updated,
                    ]);
                }
            }
        }
    }
    public function genpin_manual($request)
    {
        $check_manufacturing = DB::table('Manufacturing_Code')
        ->where('IsDelete',0)
        ->where('ID',$request->Manufacturing)
        ->first();
        // dd($request);
        $user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
        $array_data_input = [];
        if($check_manufacturing && $request->ID_Materials)
        {
            $array_data_input[$check_manufacturing->Manufacturing_ID] = [];
            foreach($request->ID_Materials as $key=>$value)
            {
                $check_materials = MasterMaterials::where('IsDelete',0)
                ->where('ID',$value)
                ->first();
                
                $quantity = $request->Quan_Materials[$key];
                if($check_materials && $check_manufacturing)
                {
                    $check_materials['manufacturing'] = $check_manufacturing;
                    $check_materials['quantity_use'] = round($quantity,2);
                    array_push($array_data_input[$check_manufacturing->Manufacturing_ID],$check_materials);
                }
            }
        }
        else
        {
            return (object)[
                'status'      => false,
                'data'        => __('Genpin').' '.__('Fail')
            ];
        }
        foreach($array_data_input as $key => $value)
        {
           
            foreach(collect($value)->groupBy('ID') as $key1 => $value1)
            {
                $quantity_use = collect($value1)->sum('quantity_use');
                if($quantity_use > 0)
                {
                    $data =  $this->api->export_stock($key1,$key,$quantity_use,2,2);
                    $genpin = GenpinMaterials::create([
                            'Manufacturing_ID' => $key,
                            'Name_File'        => 'Manunal',
                            'Materials_ID'     => $key1,
                            'Status_export'    => $data->status,    
                            'Message_export'   => $data->message,
                            'Quantity'         => $quantity_use,
                            'User_Created'     => $user_created,
                            'User_Updated'     => $user_updated,
                    ]);
                }
            }
        }
        return (object)[
            'status'      => true,
            'data'        => __('Genpin').' '.__('Success')
        ];
    }
}