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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\MasterData\MasterProduct;
use App\Models\MasterData\MasterMaterials;
use App\Models\OrderManagement\FollowOrder;

/**
 * 
 */
class POLibraries
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
            } else if ($fileName == 'xls') {
                $reader  = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } else if ($fileName == 'xlsx') {

                $reader  = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            } else if ($fileName == 'slk') {

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
        $file     = request()->file('fileImport');
        $name     = $file->getClientOriginalName();
        $arr      = explode('.', $name);
        $fileName = strtolower(end($arr));
        $data = $this->read_file($request);
        $user_created = Auth::user()->id;
        $user_updated = Auth::user()->id;
        $array_data_input = [];
        $error = [];
        $name_materials = '';
        // dd($data,$request);
        if ($request->form == 1) {
            foreach ($data as $key => $value) {
                // dd($value);
                // if($key > 0 && isset($value[9]) && isset($value[10]) && ( floatval($value[9] ? $value[9] : 0) - floatval($value[10] ? $value[10] : 0) > 0 ) )
                if ($key > 0 && isset($value[9]) && (floatval($value[9] ? $value[9] : 0) - floatval($value[10] ? $value[10] : 0) > 0)) {
                    // dd($value);
                    if (isset($value[9])) {
                        if ((floatval($value[9] ? $value[9] : 0) - floatval($value[10] ? $value[10] : 0) > 0)) {
                            // dd($value);
                            $po_name_explode = explode('-', $value[0]);
                            // dd($po_name_explode);
                            if (count($po_name_explode) > 1) {
                                // dd($po_name_explode);
                                $po_name_explode_year_month = explode('/', $po_name_explode[1]);
                                // dd($po_name_explode);
                                if (count($po_name_explode_year_month) > 1) {
                                    $month = $po_name_explode_year_month[0];
                                    $year = $po_name_explode_year_month[1];
                                    $quan = floatval($value[9] ? $value[9] : 0) - floatval($value[10] ? $value[10] : 0);
                                    $materials = MasterMaterials::where('IsDelete', 0)->where('Symbols', $value[4])->first();
                                    // dd('run');
                                    if ($materials) {
                                        $check_exits_po = FollowOrder::where('IsDelete', 0)->where('PO_NO', $value[0])->where('Materials_ID', $materials->ID)->first();
                                        if (!$check_exits_po) {
                                            $obj = [
                                                'PO_NO'         => $value[0],
                                                'Date_PO'       => Carbon::create($value[1]),
                                                'Date_Order'    => Carbon::create($month. '/' .'1/' . $year),
                                                'Materials_ID'  => $materials->ID,
                                                'DIF'           => $quan,
                                                'Maker'         => $value[13],
                                                'Time_Created'  => Carbon::now(),
                                                'Time_Updated'  => Carbon::now(),
                                                'User_Created'     => $user_created,
                                                'User_Updated'     => $user_updated,
                                            ];
                                            array_push($array_data_input, $obj);
                                        } else {
                                            $er = __('PO') . ' ' . $value[0] . ' | ' . $materials->Symbols . ' ' . __('that already exist');
                                            array_push($error, $er);
                                        }
                                    } else {
                                        $er = __('Materials that already exist');
                                        array_push($error, $er);
                                    }
                                } else {
                                    $er = __('Columns') . ' ' . ($key + 1) . ' ' . __('Error');
                                    array_push($error, $er);
                                }
                            } else {
                                $er = __('Columns') . ' ' . ($key + 1) . ' ' . __('Error');
                                array_push($error, $er);
                            }
                        }
                    } else {
                        $er = __('Columns') . ' ' . ($key + 1) . ' ' . __('Error');
                        array_push($error, $er);
                    }
                }
            }

            if (count($error) <= 0) {
                $array_in_chunk = array_chunk($array_data_input, 50);
                // dd($array_in_chunk);
                for ($i = 0; $i < count($array_in_chunk); $i++) {
                    $maters = DB::table('Follow_Order')->insert($array_in_chunk[$i]);
                }
            }
            return $error;
        } else {
            foreach ($data as $key => $value) {
                if ($key > 10 && $value[0] != '') {
                    $name_materials = $value[0];
                }
                if ($name_materials) {
                    if ($name_materials && $value[1]) {
                        $name_materials_explode  = explode('.',$name_materials);        
                        $trim_name_materials     = trim($name_materials_explode[1]);
                        $po_name_explode         = explode('-', $data[3][8]);
                        if (count($po_name_explode) > 1) {
                            $po_name_explode_year_month = explode('/', $po_name_explode[1]);
                            if (count($po_name_explode_year_month) > 1) {
                                $month = $po_name_explode_year_month[0];
                                $year = $po_name_explode_year_month[1];
                                $materials = MasterMaterials::where('IsDelete', 0)->where('Name',$trim_name_materials)->where('Symbols', $value[1])->first();
                                // dd(Carbon::create($month. '/' .'1/' . $year),$month,$year);
                                // dd($materials);
                                if ($materials) {
                                    $check_exits_po = FollowOrder::where('IsDelete', 0)->where('PO_NO', $data[3][8])->where('Materials_ID', $materials->ID)->first();
                                    if (!$check_exits_po) {
                                        $obj = [
                                            'PO_NO'         => $data[3][8],
                                            'Date_PO'       => Carbon::create($data[4][8]),
                                            'Date_Order'    => Carbon::create($month. '/' .'1/' . $year),
                                            'Materials_ID'  => $materials->ID,
                                            'DIF'           => str_replace(',','',$value[6]),
                                            'Maker'         => $data[0][8],
                                            'Time_Created'  => Carbon::now(),
                                            'Time_Updated'  => Carbon::now(),
                                            'User_Created'     => $user_created,
                                            'User_Updated'     => $user_updated,
                                        ];
                                        array_push($array_data_input, $obj);
                                    } else {
                                        $er = __('PO') . ' ' . $data[3][8] . ' | ' . $materials->Symbols . ' ' . __('that already exist');
                                        array_push($error, $er);
                                    }
                                } else {
                                    $er = __('Materials') . ' ' . __('Does Not Exist');
                                    array_push($error, $er);
                                }
                            } else {
                                $er = __('Columns') . ' ' . ($key + 1) . ' ' . __('Error');
                                array_push($error, $er);
                            }
                        } else {
                            $er = __('Columns') . ' ' . ($key + 1) . ' ' . __('Error');
                            array_push($error, $er);
                        }
                    }
                }
            }
            // dd($array_data_input);

            if(count($error) <= 0)
            {
                $array_in_chunk = array_chunk($array_data_input, 50);
                // dd($array_in_chunk);
                for ($i = 0; $i < count($array_in_chunk); $i++) 
                {
                    $maters = DB::table('Follow_Order')->insert($array_in_chunk[$i]);
                }
            }
            return $error;
        }
    }
}
