<?php

namespace App\Libraries\MasterData;

use Illuminate\Validation\Rule;
use Validator;
use App\Models\MasterData\MasterMaterials;
use Illuminate\Support\Facades\Auth;
use Hash;
use Session;
use Carbon\Carbon;
use App\Models\MasterData\MasterBOM;
use App\Models\MasterData\MasterMaker;
use Illuminate\Support\Facades\DB;

/**
 * 
 */
class MasterMaterialsLibraries
{
    public function get_name_and_symbols_materials()
    {
      return  MasterMaterials ::where('IsDelete',0)->get();
    }
    public function get_data_materials_export($request)
    {
        // dd
        // dd($request);
        $name       = $request->Name;
        $symbols    = $request->Key;
        $data = MasterMaterials::where('Master_Materials.IsDelete', 0);
        // dd($data);
        if( $name || $symbols)
        {
            $data = $data
            // ->where('Master_Materials.IsDelete',0)
            ->when($name, function ($query, $name) {
                return $query->where('Master_Materials.Name', $name);
            })->when($symbols, function ($query, $symbols) {
                return $query->where('Master_Materials.Symbols', $symbols);
            })
            ->get([
                'Master_Materials.CD as Materials_ID',
                'Master_Materials.Name as Materials_Name',
                'Master_Materials.Symbols as Materials_Symbols',
                'Master_Materials.Decription as Materials_Decription',
                'Master_Materials.Size as Materials_Size',
                'Master_Materials.Color as Materials_Color',
                'Master_Materials.Code as Materials_Code',
                'Master_Materials.Unit as Materials_Unit',
                'Master_Materials.Unit_Price as Materials_Unit_Price',
                'Master_Materials.Maker as Maker_Name'
            ]);
            // dd($data);
        }
        else
        {
            $data = $data
            // ->where('Master_Maker.IsDelete',0)
            ->select(
                'Master_Materials.CD as Materials_ID',
                'Master_Materials.Name as Materials_Name',
                'Master_Materials.Symbols as Materials_Symbols',
                'Master_Materials.Decription as Materials_Decription',
                'Master_Materials.Size as Materials_Size',
                'Master_Materials.Color as Materials_Color',
                'Master_Materials.Code as Materials_Code',
                'Master_Materials.Unit as Materials_Unit',
                'Master_Materials.Unit_Price as Materials_Unit_Price',
                'Master_Materials.Maker as Maker_Name'
            )
            ->get();
        }
        return  $data;
    }
    public function check_data($request)
    {
        // dd($request);
        $erorr = [];
        if($request->Name == '')
        {
            $er = __('Names cant be left blank');
            array_push($erorr,$er);
        }
        else
        {
           if(!$request->ID)
           {
                $check_isset =  MasterMaterials::where('IsDelete',0)->where('Name',$request->Name)->first();
                if($check_isset)
                {
                    $er = __('Names that already exist');
                    array_push($erorr,$er);
                }
           }
           else
           {
                $check_isset =  MasterMaterials::where('IsDelete',0)->where('Name',$request->Name)->where('Symbols',$request->Key)->where('ID','<>',$request->ID)->first();
                // dd($check_isset);
                if($check_isset)
                {
                    $er = __('Names that already exist');
                    array_push($erorr,$er);
                }
           }
        }
        if($request->Key == '')
        {
            $er = __('Key cant be left blank');
            array_push($erorr,$er);
        }
        else
        {
           if(!$request->ID)
           {
                $check_isset =  MasterMaterials::where('IsDelete',0)->where('Symbols',$request->Key)->first();
                if($check_isset)
                {
                    $er = __('Key that already exist');
                    array_push($erorr,$er);
                }
           }
           else
           {
                $check_isset =  MasterMaterials::where('IsDelete',0)->where('Name',$request->Name)->where('Symbols',$request->Key)->where('ID','<>',$request->ID)->first();
                if($check_isset)
                {
                    $er = __('Key that already exist');
                    array_push($erorr,$er);
                }
           }
        }
        if($request->Code == '')
        {
            $er = __('Code cant be left blank');
            array_push($erorr,$er);
        }
        else
        {
           if(!$request->ID)
           {
                $check_isset =  MasterMaterials::where('IsDelete',0)->where('Code',$request->Code)->first();
                if($check_isset)
                {
                    $er = __('Code that already exist');
                    array_push($erorr,$er);
                }
           }
           else
           {
                $check_isset =  MasterMaterials::where('IsDelete',0)->where('Name',$request->Name)->where('Symbols',$request->Key)->where('Code',$request->Code)->where('ID','<>',$request->ID)->first();
                if($check_isset)
                {
                    $er = __('Code that already exist');
                    array_push($erorr,$er);
                }
           }
        }
        if(count($erorr) > 0)
        {
            return (object)[
                'status'      => false,
                'data'        => $erorr
            ];
        }
        else
        {
            return (object)[
                'status'      => true,
                'data'        => []
            ];
        }
    }

    public function add_or_create_data($request)
    {
        // dd($request);
        $id = $request->ID;
		$user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
		$id_materials_max = MasterMaterials::max('ID');
        
		if (isset($id) && $id != '') {
			if (!Auth::user()->checkRole('update_master') && Auth::user()->level != 9999) {
				abort(401);
			}
			$materials = MasterMaterials::where('ID', $id)->update([
				'Name'          => $request->Name,
                'Symbols'       => $request->Key,
                'Size'          => $request->Size,
                'Color'         => $request->Color,
                'Unit'          => $request->Unit,
                'Min_Of_Order'  => $request->Min_Of_Order,
                'Maker'         => $request->Maker,
                'Unit_Price'    => $request->Unit_Price,
                'Process'       => $request->Process,
                'Type'          => $request->Type,
                'Code'          => $request->Code,
                'CD'            => $request->CD,
                'Decription'    => $request->Decription,
                'Note'          => $request->Note,
				'User_Updated'  => $user_updated
			]);

			return (object)[
				'status' => true,
				'data'	 => $materials
			];
		} else {
			if (!Auth::user()->checkRole('create_master') && Auth::user()->level != 9999) {
				abort(401);
			}
            $id_materials_max++;

			$materials = MasterMaterials::create([
                'ID'            => intval($id_materials_max),
				'Name'          => $request->Name,
                'Symbols'       => $request->Key,
                'Size'          => $request->Size,
                'Color'         => $request->Color,
                'Unit'          => $request->Unit,
                'Min_Of_Order'  => $request->Min_Of_Order,
                'Maker'         => $request->Maker,
                'Unit_Price'    => $request->Unit_Price,
                'Decription'    => $request->Decription,
                'Process'       => $request->Process,
                'Type'          => $request->Type,
                'CD'            => $request->CD,
                'Code'          => $request->Code,
                'Note'          => $request->Note,
				'User_Created'  => $user_created,
				'User_Updated'  => $user_updated,
				'IsDelete'      => 0
			]);
			return (object)[
				'status' => true,
				'data'	 => $materials
			];
		}
    }

    public function destroy($request)
	{
		$user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
        $find_mat = MasterMaterials::where('ID', $request->ID)->first();
		$find = MasterMaterials::where('ID', $request->ID)->update([
			'IsDelete' 		=> 1,
			'User_Updated'	=> Auth::user()->id
        ]);
        MasterBOM::where('Materials_ID',$find_mat->ID)->update([
            'IsDelete'=>1
        ]);
		return __('Delete') . '  ' . __('Success');
	}

    private function read_file($request)
	{
		// dd('run');
		try {
			$file     = request()->file('fileImport');
			$name     = $file->getClientOriginalName();
			$arr      = explode('.', $name);
			$fileName = strtolower(end($arr));
			// dd($file, $name, $arr, $fileName);
			if ($fileName != 'xlsx' && $fileName != 'xls') {
				return redirect()->back();
			} else if ($fileName == 'xls') {
				$reader  = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
			} else if ($fileName == 'xlsx') {
				$reader  = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			}
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

	public function import_file($request)
	{
		$data = $this->read_file($request);
		$err = [];
		$user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
        $array_maters = [];
		$id_materials_max = MasterMaterials::max('ID');

		foreach ($data as $key => $value) {
			if ($key > 0) {
				// dd($value);
				if ($value[1] != '' ) {
					$mater = MasterMaterials::where('IsDelete', 0)->where('Name', strval($value[1]))->where('Symbols', strval($value[2]))->first();
					
					if (!$mater) {
                        $check_materials = collect($array_maters)->where('IsDelete', 0)->where('Name', strval($value[1]))->where('Symbols', strval($value[2]))->first();
						if(!$check_materials)
						{
                            $id_materials_max++;
							$array_mater = [
                                'ID'            => intval($id_materials_max),
                                'Name' 			=> strval($value[1]),
                                'Symbols' 		=> strval($value[2]),
                                'Decription'    => strval($value[3]),
                                'Size' 			=> strval($value[4]),
                                'Color' 		=> strval($value[5]),
                                'CD' 		    => strval($value[0]),
                                'Code' 			=> strval(trim($value[6]," ")),
                                'Unit' 			=> strval($value[7]),
                                'Unit_Price' 	=> $value[8],
                                'Maker' 	    => strval($value[9]),
                                'User_Created' 	=> $user_created,
                                'User_Updated' 	=> $user_updated,
                                'Time_Created' => Carbon::now(),
                                'Time_Updated' => Carbon::now(),
                                'IsDelete' 		=> 0
                            ];
                            array_push($array_maters, $array_mater);
						}        
					}
                    else
                    {
                        $mater->update([
                            'Size'          => $mater->Size != strval($value[4]) ? strval($value[4]) : $mater->Size,
                            'Color'         => $mater->Color != strval($value[5]) ? strval($value[5]) : $mater->Color,
                            'Code'          => strval(trim($value[6]," ")),
                            'CD' 		    => strval($value[0]),
                            'Unit'          => $mater->Unit != strval($value[7]) ? strval($value[7]) : $mater->Unit,
                            'Unit_Price'    => $mater->Unit_Price != $value[8] ? $value[8] : $mater->Unit_Price,
                            'Maker'         => strval($value[9])
                        ]);
                    }
				}
			}
		}

        $array_materials_chunk = array_chunk($array_maters, 50);
		for ($i = 0; $i < count($array_materials_chunk); $i++) {
			DB::table('Master_Materials')->insert($array_materials_chunk[$i]);
		}
		return $err;
	}

    public function import_file_process($request)
	{
		$data = $this->read_file($request);
		$err = [];
		$user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
        $array_maters = [];
		foreach ($data as $key => $value) {
			if ($key > 0) {
				// dd($value);
				if ($value[1] != '' ) {
					$mater = MasterMaterials::where('IsDelete', 0)->where('Name', strval($value[0]))->where('Symbols', strval($value[1]))->first();
                    // dd($mater);
                    if($mater)
                    {
                        $mater->update([
                            'Process' => strval($value[2]),
                            'User_Updated' => Auth::user()->id
                        ]);
                    }
				}
			}
		}

        // $array_materials_chunk = array_chunk($array_maters, 50);
		// for ($i = 0; $i < count($array_materials_chunk); $i++) {
		// 	DB::table('Master_Materials')->insert($array_materials_chunk[$i]);
		// }
		return $err;
	}
}
