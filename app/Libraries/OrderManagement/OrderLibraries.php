<?php

namespace App\Libraries\OrderManagement;

use Illuminate\Validation\Rule;
use Validator;
use App\Models\MasterData\MasterLocation;
use App\Models\MasterData\MasterBOM;
use App\Models\MasterData\MasterMaterialsLocation;
use Illuminate\Support\Facades\Auth;
use Hash;
use Session;
use DB;
use Carbon\Carbon;
use App\Models\MasterData\MasterProduct;
use App\Models\OrderManagement\ScheduleImportFile;
/**
 * 
 */
class OrderLibraries
{
    private function read_file($request)
    {
        try {
            
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
            try {
                $spreadsheet = $reader->load($file);
                $data        = $spreadsheet->getActiveSheet()->toArray();
                return $data;
            } catch (\Exception $e) {
                return ['danger' => __('Select The Standard ".xlsx" Or ".xls" File')];
            }
        } catch (\Exception $e) {
             return ['danger' => __('Error Something')];
        }
    }
    public function import_file_excel($request)
    {
        // dd($request);
        $file     = request()->file('fileImport');
        $name     = $file->getClientOriginalName();
        $arr      = explode('.', $name);
        $fileName = strtolower(end($arr));
        $data = $this->read_file($request);
        $user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
        $array_data_input = [];
    //   dd($data);
        $error = [];
        foreach($data as $key => $value)
        {
            if($key >= 4  && isset($value[3]) && isset($value[4]) && isset($value[6]) && isset($value[12]) && (is_numeric(Intval($value[12]))))
            {
                // dd($value);
                $text_date_po =  substr($value[3],'-6',4);
                $product_hinban = $value[4];
                if($value[5] != '*')
                {
                    $product_sz = $value[5];  
                }
                else
                {
                    $text =  substr($product_hinban,'-3',2);
                    $product_sz = Floatval($text)*2;
                }
                $product_cl = $value[6];  
                $quantity =  $value[12]*(-1);
                $dem = 0;
                // dd($quantity);
                if($quantity > 0)
                {
                    // dd($product_hinban,$product_sz,$product_cl);
                    $check_product = MasterProduct::where('IsDelete',0)
                    ->where('Hinban',''.$product_hinban)
                    ->where('Size',$product_sz)
                    ->where('Color',''.$product_cl)
                        ->first();
                        // dd($check_product);
                    if($check_product)
                    {
                        
                        foreach(collect($check_product->bom_nvl) as $value_mater)
                        {
                            $bom = MasterBOM::where('Product_ID',$check_product->ID)->where('Materials_ID',$value_mater->ID)->first();
                            if($bom)
                            {
                                $dem++;
                                $quantity_use = $quantity*$bom->Quantity_Materials;
                                $value_mater['quantity_use'] = round($quantity_use,2);
                                $value_mater['stt'] = $dem;
                                ScheduleImportFile::create([
                                    'PO_NO'        => $value[3],
                                    'Materials_ID' => $value_mater->ID,
                                    'Date_PO'      => $text_date_po,
                                    'DIF'          => $quantity_use,
                                    'User_Created'     => $user_created,
                                    'User_Updated'     => $user_updated,
                                ]);
                            }
                        }
                    }
                    else
                    {
                        $er = __('Product').' '. $product_hinban.' '.__('Does Not Exist');
                        array_push($error,$er);
                    }
                }
            }
            else
            {
                $er = __('Columns').' '.($key+1).' '.__('Error');
                array_push($error,$er);
            }
        }
        return $error;
    }
}
