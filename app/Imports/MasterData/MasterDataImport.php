<?php

namespace App\Imports\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\MasterData\MasterSupplier;
use App\Models\MasterData\MasterMold;
use App\Models\MasterData\MasterAccessories;
use App\Models\MasterData\MasterMaterials;
use App\Models\MasterData\MasterUnit;
use App\Models\MasterData\MasterMoldAccessories;
use Auth;
/**
 * 
 */
class MasterDataImport
{

    public function read_file($request)
    {
        try
        {
            $file     = request()->file('fileImport');
            $name     = $file->getClientOriginalName();
            $arr      = explode('.', $name);
            $fileName = strtolower(end($arr));
            // dd($file, $name, $arr, $fileName);
            if ($fileName != 'xlsx' && $fileName != 'xls') 
            {
                return redirect()->back();
            } 
            else if($fileName == 'xls')
            {
                $reader  = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } else if($fileName == 'xlsx')
            { 
                $reader  = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }
            // $reader->setReadDataOnly(true);
            // $reader->setLoadAllSheets();
            try
            {
                $spreadsheet = $reader->load($file);
                $data        = $spreadsheet->getActiveSheet()->toArray();

                return $data;
            }  catch(\Exception $e)
            {
                return ['danger' => __('Select The Standard ".xlsx" Or ".xls" File')];
            }
        } catch (\Exception $e)
        {
            return ['danger' => __('Error Something')];
        }
    }

    public function master_mold($request)
    {
        $data       = $this->read_file($request);
        // dd($data);
        foreach ($data as $key => $value) 
        {
            // dd($data);
            if($key > 1)
            {
                //khai báo khuôn
                $mold = MasterMold::where('IsDelete',0)->where('Symbols',$value[2])->first();
                if(!$mold)
                {
                    
                    $arr     = [
                        'Name'             => $value[1],
                        'Symbols'          => $value[2],
                        'Quantity_Report'  => $value[3],
                        'Quantity_limit'   => $value[4],
                        'User_Created'     => Auth::user()->id,
                        'User_Updated'     => Auth::user()->id,
                        'IsDelete'=>0
                    ];
                    $mold = MasterMold::create($arr);
                }
                // khai báo linh kiện
                $acccessories = MasterAccessories::where('IsDelete',0)->where('Symbols',$value[6])->first();
                if(!$acccessories)
                {
                    $arr     = [
                        'Name'             => $value[5],
                        'Symbols'          => $value[6],
                        'Symbols_Input'  => $value[8],
                        'Height_Use'   => $value[9],
                        'User_Created'     => Auth::user()->id,
                        'User_Updated'     => Auth::user()->id,
                        'IsDelete'=>0
                    ];
                    $acccessories = MasterAccessories::create($arr);
                }
                $mold_acc = MasterMoldAccessories::where('Mold_ID',$mold->ID)
                ->where('Accessories_ID',$acccessories->ID)
                ->where('IsDelete',0)
                ->first();
                if($mold_acc)
                {
                    $mold_acc = MasterMoldAccessories::where('Mold_ID',$mold->ID)
                    ->where('Accessories_ID',$acccessories->ID)
                    ->where('IsDelete',0)
                    ->update([
                        'Quantity'=>$value[7],
                    ]);
                }
                else
                {
                    $arr = [
                        'Mold_ID'=>$mold->ID,
                        'Accessories_ID'=>$acccessories->ID,
                        'Quantity'=>$value[7],
                        'IsDelete'=>0
                    ];
                    MasterMoldAccessories::create($arr);
                }
                
            }
        }
        return [];
    }

    public function master_acccessories($request)
    {
        $data       = $this->read_file($request);
        foreach ($data as $key => $value) 
        {
            // dd($data);
            if($key > 0)
            {
                $mold = MasterAccessories::where('IsDelete',0)->where('Symbols',$value[2])->first();
                if($mold)
                {
                    $arr     = [
                        'Name'             => $value[1],
                        'Symbols'          => $value[2],
                        'Symbols_Input'  => $value[3],
                        'Height_Use'   => $value[4],
                        'User_Updated'     => Auth::user()->id,
                    ];
                    MasterAccessories::where('ID',$mold->ID)->update($arr);
                }
                else 
                {
                    $arr     = [
                        'Name'             => $value[1],
                        'Symbols'          => $value[2],
                        'Symbols_Input'  => $value[3],
                        'Height_Use'   => $value[4],
                        'User_Updated'     => Auth::user()->id,
                    ];
                    MasterAccessories::create($arr);
                }
            }
        }
        return true;
    }

	public function master_bom($request)
	{
        // try
        // {
            $data       = $this->read_file_bom($request);
            // dd($data);
            $dataBom    = array();
            $dataOrPart = array();
            $product_id = $request->Product_ID;
            $part_name  = '';
            $norm       = 0;
            $position_A = '';
            $face_A     = 0;
            $position_B = '';
            $face_B     = 0;
            $i          = 0;

            foreach ($data as $key => $value) 
            {
                if(intval($value[1]) != 0)
                {
                    $part_name  = '';
                    $norm       = 0;
                    $position_A = '';
                    $face_A     = 0;
                    $position_B = '';
                    $face_B     = 0;
                    $part_name  = $value[2];
                    $norm       = floatval($value[3]);
                    $materials  = $value[4];
                    $model      = $value[5];

                    if ($value[9] == 'A') 
                    {
                        if ($position_B == 'No Face') 
                        {
                            $position_B = '';
                        }
                        $position_A = $value[8];
                        $face_A     = count(explode(',', $value[8]));
                    } else if ($value[9] == 'B')
                    {
                        if ($position_A == 'No Face') 
                        {
                            $position_A = '';
                        }

                        $position_B = $value[8];
                        $face_B     = count(explode(',', $value[8]));
                    } else if ($face_A == 0 && $face_B == 0)
                    {
                        $position_A = 'No Face';
                        $face_A     = 0;
                        $position_B = 'No Face';
                        $face_B     = 0;
                    }
                }

                if (
                    $part_name != '' && 
                    $value[3] != null && 
                    $value[4] != null && 
                    $value[5] != null && 
                    $value[6] != null && 
                    $value[8] != null && 
                    $value[9] != null &&
                    $value[10] != null
                ){
                    $keyRow = $key;

                    $dataBom[$keyRow] = [
                        'Product_ID' => $product_id,
                        'Part_Name'  => $part_name,
                        'Norm'       => $norm,
                        'Face_A'     => $face_A,
                        'Position_A' => $position_A,
                        'Face_B'     => $face_B,
                        'Position_B' => $position_B
                    ];

                    $i = 0;

                    $dataBom[$keyRow]['Group'][] = [
                        'Materials'         => $materials,
                        'Model'             => $model,
                        'Materials_Replace' => $value[4],
                        'Model_Replace'     => $value[5],
                        'Level'             => $i
                    ];

                } else if(
                    $part_name != ''   && 
                    $value[4]  != null && 
                    $value[5]  != null && 
                    $value[10] != null
                ){
                    if ($value[8] != null && $value[9] != null) 
                    {
                        $face_B     = count(explode(',', $value[8]));
                        if (isset($dataBom[$keyRow]['Face_B']) && isset($dataBom[$keyRow]['Position_B'])) 
                        {
                            $dataBom[$keyRow]['Face_B'] = $face_B;
                            $dataBom[$keyRow]['Position_B'] = $position_B;
                        }
                    }

                    if ($value[9] == 'B' && $value[8] != null)
                    {
                        if ($position_A == 'No Face') 
                        {
                            $position_A = '';
                        }

                        $position_B = $value[8];
                        $face_B     = count(explode(',', $value[8]));
                        // var_dump($position_B, $face_B);
                    }

                    $i++;
                    array_push($dataBom[$keyRow]['Group'],
                    [
                        'Materials'         => $materials,
                        'Model'             => $model,
                        'Materials_Replace' => $value[4],
                        'Model_Replace'     => $value[5],
                        'Level'             => $i
                    ]);
                }

                if ($value[9] == 'B' && $value[8] != null)
                {
                    $position_B = $value[8];
                    $face_B     = count(explode(',', $value[8]));
                    if (isset($dataBom[$keyRow]['Face_B']) && isset($dataBom[$keyRow]['Position_B'])) 
                    {
                        $dataBom[$keyRow]['Face_B'] = $face_B;
                        $dataBom[$keyRow]['Position_B'] = $position_B;
                    }
                    // var_dump($position_B, $face_B);
                }
            }
            // dd($dataBom);
            return $dataBom;

        // } catch (\Exception $e)
        // {
        //     return ['danger' => __('Error Something').' '.__('In').' '.__('BOM')];
        // }
	}

    // import master materials
    public function master_materials($request)
    {
        // try
        // {
            $data          = $this->read_file($request);
            // dd($data);
            // $data          = $this->read_all_file($request);
            $dataMaterials = array();
            $name          = '';
            $symbols       = '';
            $err_ar = [];
            foreach ($data as $key => $value) 
            {
                if($key > 0)
                {
                    if ($value[0]) 
                        {
                            $name    = $value[0];
                            $symbols = $value[1];
                            if($value[2])
                            {
                                $supp = MasterSupplier::where('IsDelete',0)->where('Symbols',$value[2])->first();
                                $unit = MasterUnit::where('IsDelete',0)->where('Symbols',$value[5])->first();
                              
                                if($supp)
                                {
                                    if($unit)
                                    {
                                        $mater = MasterMaterials::where('IsDelete',0)->where('Name',$value[0])->where('Symbols',$value[1])->first();
                                        if($mater)
                                        {
                                            $arr     = [
                                                'Name'             => $name,
                                                'Symbols'          => $symbols,
                                                'Unit_ID'             => $unit->ID,
                                                'Supplier_ID'         => $supp->ID,
                                                'Difference'       => $value[7],
                                                'Spec'			   => trim($value[4]),
                                                'Wire_Type'        => $value[3],
                                                'User_Updated'     => Auth::user()->id,
                                            ];
                                            MasterMaterials::where('ID',$mater->ID)->update($arr);
                                            // array_push($dataMaterials, $arr);
                                        }
                                        else
                                        {
                                            $arr     = [
                                                'Name'             => $name,
                                                'Symbols'          => $symbols,
                                                'Unit_ID'             => $unit->ID,
                                                'Supplier_ID'         => $supp->ID,
                                                'Difference'       => $value[7],
                                                'Spec'			   => trim($value[4]),
                                                'Wire_Type'        => $value[3],
                                                'User_Created'     => Auth::user()->id,
                                                'User_Updated'     => Auth::user()->id,
                                            ];
                                            MasterMaterials::create($arr);
                                        }
                                    }
                                    else
                                    {
                                        $err = "Dòng ".($key+1)." Có Đơn Vị Tính Không Tồn Tại";
                                        array_push($err_ar,$err);
                                    }
                                    
                                    
                                }
                                else
                                {
                                    $err = "Dòng ".($key+1)." Có Nhà Cung Cấp Không Tồn Tại";
                                    array_push($err_ar,$err);
                                }
                            }
                           
                        }
                }
                
            }
           
            return $err_ar;
        // } catch (\Exception $e)
        // {
        //     return ['danger' => __('Error Something').' '.__('In').' '.__('Materials')];
        // }
    }

    // import master unitt
    public function master_unit($request)
    {
        // try
        // {
            $data          = $this->read_file($request);
            // dd($data);
            // $data          = $this->read_all_file($request);          
            $err_ar = [];
            foreach ($data as $key => $value) 
            {
                if($key > 0)
                {
                    // dd($value);
                    if($value[1]) 
                    {
                        $unit = MasterUnit::where('IsDelete',0)->where('Symbols',$value[1])->first();

                        if ($unit) {
                            $arr = [
                                'Name' => $value[0],
                                'Symbols' => $value[1],
                                'User_Updated' => Auth::user()->id
                            ];
                            MasterUnit::where('ID', $unit->ID)->update($arr);
                            
                        }
                        else 
                        {
                           $arr     = [
                                'Name'             => $value[0],
                                'Symbols'          => $value[1],
                                'User_Created'     => Auth::user()->id,
                                'User_Updated'     => Auth::user()->id
                            ];
                            MasterUnit::create($arr);                     
                        }
                    }
                }
                
            }
           
            return $err_ar;
        // } catch (\Exception $e)
        // {
        //     return ['danger' => __('Error Something').' '.__('In').' '.__('Materials')];
        // }
    }


    // import master supplier
    public function master_supplier($request)
    {
        // try
        // {
            $data          = $this->read_file($request);
            // dd($data);
            // $data          = $this->read_all_file($request);
            $err_ar = [];
            foreach ($data as $key => $value) 
            {
                if($key > 0)
                {
                    // dd($value);
                    if($value[1]) 
                    {
                        $supplier = MasterSupplier::where('IsDelete',0)->where('Symbols',$value[1])->first();

                        if ($supplier) {
                            $arr = [
                                'Name'      => $value[0],
                                'Symbols'   => $value[1],
                                'Address'   => $value[2],
                                'Contact'   => $value[3],
                                'Phone'     => $value[4],
                                'Tax_Code'  => $value[5],
                                'User_Updated' => Auth::user()->id
                            ];
                            MasterSupplier::where('ID', $supplier->ID)->update($arr);
                            
                        }
                        else 
                        {
                           $arr     = [
                                'Name'      => $value[0],
                                'Symbols'   => $value[1],
                                'Address'   => $value[2],
                                'Contact'   => $value[3],
                                'Phone'     => $value[4],
                                'Tax_Code'  => $value[5],
                                'User_Created'     => Auth::user()->id,
                                'User_Updated'     => Auth::user()->id
                            ];
                            MasterSupplier::create($arr);                     
                        }
                    }
                }
                
            }
           
            return $err_ar;
        // } catch (\Exception $e)
        // {
        //     return ['danger' => __('Error Something').' '.__('In').' '.__('Materials')];
        // }
    }

    public function sti_label($request)
    {
        $dataIm = $this->read_file($request);

        $data = array();
        $label = '';
        $brea = false;

        foreach ($dataIm as $key => $value) 
        {
            foreach ($value as $key1 => $value1) 
            {
                if ($value1 != null && $value1 != 'Tên' && !$brea) 
                {
                    array_push($data, $value1);
                    $label = $label.','.$value1;
                    // if (count($data) == 2) 
                    // {
                    //     $brea = true;
                    // }
                }
            }
        }

        return [$data, $label];
    }

    public function kitting_list($request)
    {
        $data = $this->read_file_calculate($request);
        // dd($data);
        $dataPlan = array();

        foreach ($data as $key => $value) 
        {
            if ($key > 1) 
            {

                if ($value[0] != null && $value[4] != null) 
                {
                    $part_name = explode('\\n',$value[0])[0];

                    if ($value[1] == "Ａ" || $value[1] == 'A')
                    {
                        $face = 'A';
                    } else
                    {
                        $face = 'B';
                    }
                    // $face      = $value[1];

                }

                if (
                    mb_strtolower(explode( '-',$value[3])[0]) == '計画' || 
                    mb_strtolower(explode( '-',$value[3])[0]) == 'kế hoạch' || 
                    mb_strtolower(explode( '-',$value[3])[0]) == 'plan'
                ){
                    $shift = 2;
                    if (
                        mb_strtolower(explode( '-',$value[4])[0]) == '白' || 
                        mb_strtolower(explode( '-',$value[4])[0]) == 'ca ngày' || 
                        mb_strtolower(explode( '-',$value[4])[0]) == 'day shift'
                    ){
                        $shift = 1;
                    }
                    $arr = [
                        'Part_Name'        => $part_name,
                        'Face'             => $face,
                        'Machine_Name'     => $value[2],
                        'Type'             => $value[3],
                        'Production_Shift' => $shift,
                        'Sum'              => $value[5],
                    ];

                    for ($i=1; $i <= 31; $i++) 
                    { 
                        $arr['Quantity_'.$i] = !isset($value[$i+5]) ? 0 : $value[$i+5];
                    }

                    array_push($dataPlan, $arr);
                }
            }
        }
        // dd($dataPlan);
        return $dataPlan;
    }

    // Import Command Inventory
    public function get_inventories($request)
    {
        $data     = array();
        $dataRead = $this->read_file($request);

        foreach ($dataRead as $key => $read) 
        {
            if (intval($read[3]) != 0 || intval($read[7]) != 0) 
            {
                if (intval($read[1]) != 0) 
                {
                    $position = $read[2];
                }

                if (!(
                    $read[1] == 'null' && $read[2] == 'null' && 
                    $read[3] == 'null' && $read[4] == 'null' && 
                    $read[5] == 'null' && $read[6] == 'null' && 
                    $read[7] == 'null' && $read[8] == 'null' && 
                    $read[9] == 'null' && $read[10] == 'null'
                )) 
                {
                    $arr = [
                        'position'         => $position,
                        'label_system'     => $read[3],
                        'materials_system' => $read[4],
                        'model_system'     => $read[5],
                        'quantity_system'  => $read[6],
                        'label_actual'     => $read[7],
                        'materials_actual' => $read[8],
                        'model_actual'     => $read[9],
                        'quantity_actual'  => $read[10],
                    ];

                    array_push($data, $arr);
                }
                
            }
        }
        
        return $data;
    }

    public function master_group_materials($request)
    {
        $dataRead = $this->read_file($request);
        // dd($dataRead);
        $dataSend = array();
        $error    = array();
        // dd($dataRead);
        foreach ($dataRead as $key => $data) 
        {
            // $break      = $data[0];
            $stt        = $data[0];
            $group      = $data[1];
            $unit       = $data[3];
            $materials  = $data[4];

            if (intval($stt) != 0) 
            {
                $qty = str_replace(',', '', $data[2]);
                // var_dump($qty);
                // dd($qty);
                if ($group != null) 
                {
                    $name = $group;
                    if (intval($qty) != 0 && $unit != null && $materials != null) 
                    {
                        $arr = [
                            'name'      => $name,
                            'quantity'  => intval($qty),
                            'unit'      => $unit,
                            'materials' => [$materials]
                        ];

                        if (array_search($name, array_column($dataSend, 'name')) === false) 
                        {
                            array_push($dataSend, $arr);  
                        } else
                        {
                            array_push($dataSend[array_search($name, array_column($dataSend, 'name'))]['materials'], $materials);  
                        }
                    } else
                    {
                        if (intval($qty) == 0) 
                        {
                            array_push($error, __('Materials').' '.$materials.' '.__('Wrong Quantity').' '.__('In').' '.__('Position').' '.($key + 1));
                        }

                        if ($unit == null) 
                        {
                            array_push($error, __('Materials').' '.$materials.' '.__('Wrong').' '.__('Unit').' '.__('In').' '.__('Position').' '.($key + 1));
                        }

                        if ($materials == null) 
                        {
                            array_push($error, __('Materials').' '.__('Does Not Exist').' '.__('In').' '.__('Position').' '.($key + 1));
                        }
                    }
                } else
                {
                    array_push($error, __('Materials').' '.$materials.' '.__('Wrong').' '.__('Unit').' '.__('In').' '.__('Position').' '.($key + 1));
                }

            }
        }
       // dd('ok');
        return (object)[
            'data' => $dataSend,
            'error'=> array_unique($error)
        ];
    }

    private function read_file_bom($request)
    {
        try
        {
            $file     = request()->file('fileImport');
            $name     = $file->getClientOriginalName();
            $arr      = explode('.', $name);
            $fileName = strtolower(end($arr));
            // dd($file, $name, $arr, $fileName);
            if ($fileName != 'xlsx' && $fileName != 'xls') 
            {
                return redirect()->back();
            } 
            else if($fileName == 'xls')
            {
                $reader  = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } else if($fileName == 'xlsx')
            { 
                $reader  = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }

            $reader->setReadDataOnly(true);
            // $reader->setLoadAllSheets();
            try
            {
                $spreadsheet = $reader->load($file);
                $data        = $spreadsheet->getActiveSheet()->toArray();

                return $data;
            }  catch(\Exception $e)
            {
                return ['danger' => __('Select The Standard ".xlsx" Or ".xls" File')];
            }
        } catch (\Exception $e)
        {
            return ['danger' => __('Error Something')];
        }
    }

    public function read_all_file($request)
    {
        try
        {
            $data     = array();
            $file     = request()->file('fileImport');
            $name     = $file->getClientOriginalName();
            $arr      = explode('.', $name);
            $fileName = strtolower(end($arr));
            // dd($file, $name, $arr, $fileName);
            if ($fileName != 'xlsx' && $fileName != 'xls') 
            {
                return redirect()->back();
            } 
            else if($fileName == 'xls')
            {
                $reader  = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } else if($fileName == 'xlsx')
            { 
                $reader  = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }
            try
            {
                $spreadsheet = $reader->load($file);
                $sheetCount  = $spreadsheet->getSheetCount();

                for ($i = 0; $i < $sheetCount; $i++) {
                    $sheet     = $spreadsheet->getSheet($i);
                    $sheetData = $sheet->toArray(null, true, true, true);
                    array_push($data, $sheetData);
                }

                return $data;
            }  catch(\Exception $e)
            {
                return ['danger' => __('Select The Standard ".xlsx" Or ".xls" File')];
            }
        } catch (\Exception $e)
        {
            return ['danger' => __('Error Something')];
        }

    }

    private function read_file_calculate($request)
    {
        try
        {
            $file     = request()->file('fileImport');
            $name     = $file->getClientOriginalName();
            $arr      = explode('.', $name);
            $fileName = strtolower(end($arr));
            // dd($file, $name, $arr, $fileName);
            if ($fileName != 'xlsx' && $fileName != 'xls') 
            {
                return redirect()->back();
            } 
            else if($fileName == 'xls')
            {
                $reader  = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } else if($fileName == 'xlsx')
            { 
                $reader  = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }

            $reader->setReadDataOnly(true);
            try
            {
                $spreadsheet = $reader->load($file);
                // $data     = $spreadsheet->getActiveSheet()->toArray();
                $data        = $spreadsheet->getActiveSheet();
                
                $row     = $data->getHighestRowAndColumn()['row'];
                $limit   = $data->getHighestRowAndColumn()['column'];
                $dataAll = array();
                // dd($data->getCell('F39')->getOldCalculatedValue());
                for ($i = 1; $i <= $row ; $i++) 
                {
                    $arr  = array();
                    $name = 'A';

                    for ($j = 1; $j < 100000 ; $j++) 
                    {
                        // try
                        // {
                        //     $value = $data->getCell($name.$i)->getCalculatedValue();
                        //     array_push($arr, $value);
                        // } catch(\Exception $e)
                        // {
                            $value = $data->getCell($name.$i)->getOldCalculatedValue();
                            if ($value === null) 
                            {
                                $value = $data->getCell($name.$i)->getCalculatedValue();  
                            }
                            array_push($arr, $value);
                        // }
                        // var_dump($name.$i, $value);


                        if ($name == $limit) 
                        {
                            break;
                        }
                        $name++;
                    }
                    // var_dump($arr);
                    array_push($dataAll, $arr);

                }

                return $dataAll;
            }  catch(\Exception $e)
            {
                return ['danger' => __('Select The Standard ".xlsx" Or ".xls" File')];
            }
        } catch (\Exception $e)
        {
            return ['danger' => __('Error Something')];
        }
    }
}