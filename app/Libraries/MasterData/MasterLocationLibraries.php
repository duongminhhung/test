<?php

namespace App\Libraries\MasterData;

use Illuminate\Validation\Rule;
use Validator;
use App\Models\MasterData\MasterLocation;
use App\Models\MasterData\MasterBOM;
use App\Models\MasterData\MasterMaterials;
use App\Models\MasterData\MasterMaterialsLocation;
use Illuminate\Support\Facades\Auth;
use Hash;
use Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * 
 */
class MasterLocationLibraries
{
    public function get_name_and_symbols_location()
    {
      return  MasterLocation::where('IsDelete',0)->where('Manufacturing_ID',0)->get();
    }
    public function get_name_and_symbols_location_type1()
    {
        return  MasterLocation::where('IsDelete',0)->where('Manufacturing_ID',0)->get(['ID','Name','Symbols']);
    }
    public function check_data($request)
    {
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
                $check_isset =  MasterLocation::where('IsDelete',0)->where('Name',$request->Name)->first();
                if($check_isset)
                {
                    $er = __('Names that already exist');
                    array_push($erorr,$er);
                }
           }
           else
           {
                $check_isset =  MasterLocation::where('IsDelete',0)->where('Name',$request->Name)->where('ID','<>',$request->ID)->first();
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
                $check_isset =  MasterLocation::where('IsDelete',0)->where('Symbols',$request->Key)->first();
                if($check_isset)
                {
                    $er = __('Key that already exist');
                    array_push($erorr,$er);
                }
           }
           else
           {
                $check_isset =  MasterLocation::where('IsDelete',0)->where('Symbols',$request->Key)->where('ID','<>',$request->ID)->first();
                if($check_isset)
                {
                    $er = __('Key that already exist');
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
		if (isset($id) && $id != '') {
			if (!Auth::user()->checkRole('update_master') && Auth::user()->level != 9999) {
				abort(401);
			}
			$Location_find = MasterLocation::where('IsDelete', 0)->where('ID', $id)->first();

			$Location = MasterLocation::where('ID', $id)->update([
                'Name'          => $request->Name,
                'Symbols'       => $request->Key,
                'Max'           => $request->Max,
                'Unit'          => $request->Unit,
                'Note'          => $request->Note,
				'User_Updated'  => $user_updated
			]);
            MasterMaterialsLocation::where('Location_ID',$Location_find->ID)->update([
                'IsDelete'=>1
            ]);
            foreach($request->array_materials as $v )
            {
            //    $v_explode = explode('-',$v);
               MasterMaterialsLocation::create([
                    'Location_ID'           =>  $Location_find->ID,
                    'Materials_ID'          =>  $v,
                    // 'Quantity_Materials'    =>  $v_explode[1],
                    'User_Created'          =>  $user_created,
                    'User_Updated'          =>  $user_updated,
                    'IsDelete'              =>  0
               ]);
            }
			return (object)[
				'status' => true,
				'data'	 => $Location
			];
		} else {
			if (!Auth::user()->checkRole('create_master') && Auth::user()->level != 9999) {
				abort(401);
			}
			$Location = MasterLocation::create([
				'Name'              => $request->Name,
                'Symbols'           => $request->Key,
                'Max'               => $request->Max,
                'Unit'              => $request->Unit,
                'Note'              => $request->Note,
				'User_Created'      => $user_created,
				'User_Updated'      => $user_updated,
				'IsDelete'          => 0
			]);
            $Location_find = MasterLocation::where('IsDelete', 0)->where('Name', $Location->Name)->where('Symbols',$Location->Symbols)->first();
            foreach($request->array_materials as $v )
            {
            //    $v_explode = explode('-',$v);
               MasterMaterialsLocation::create([
                'Location_ID'           =>  $Location_find->ID,
                'Materials_ID'          =>  $v,
                // 'Quantity_Materials'    =>  $v_explode[1],
                'User_Created'          =>  $user_created,
                'User_Updated'          =>  $user_updated,
                'IsDelete'              =>  0
               ]);
            }
			return (object)[
				'status' => true,
				'data'	 => $Location
			];
		}
    }
    public function destroy($request)
	{
		$user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
        $find_mat = MasterLocation::where('ID', $request->ID)->first();
		$find_mat->update([
			'IsDelete' 		=> 1,
			'User_Updated'	=> Auth::user()->id
        ]);
        MasterMaterialsLocation::where('Location_ID',$find_mat->ID)->update([
            'IsDelete'=>1
        ]);
		return __('Delete') . ' ' . __('Success');
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
        // dd($data);
		$err = [];
		$user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
		$array_locations = [];
		$array_maters = [];
		$array_bom = [];

		$id_location_max = MasterLocation::max('ID');
		$id_materials_max = MasterMaterials::max('ID');
		foreach ($data as $key => $value) {
			if ($key > 0) {
				if($value[8] != '')
				{

					$location =   MasterLocation::where('IsDelete', 0)->where('Symbols',  strval($value[8]))->first();
					if (!$location) {
						$check_location = collect($array_locations)->where('IsDelete', 0)->where('Symbols',  strval($value[8]))->first();
						if (!$check_location) {
							$id_location_max++;
							$id_location 	= $id_location_max;
							$location = [
								'ID'			=> intval($id_location_max),
								'Name' 		    => strval($value[8]),
								'Symbols' 		=> strval($value[8]),
								'User_Created'	=> $user_created,
								'User_Updated'	=> $user_updated,
								'Time_Created'	=> Carbon::now(),
								'Time_Updated'	=> Carbon::now(),
								'IsDelete' 		=> 0
							];
							array_push($array_locations, $location);
						} else {
							$id_location		= $check_location['ID'];
						}
					} else {
						MasterMaterialsLocation::where('Location_ID', $location->ID)->delete();
						$id_location		= $location->ID;
					}
	
					// foreach ($value as $key1 => $value1) {
					$mater = MasterMaterials::where('IsDelete', 0)->where('Name', strval($value[1]))->where('Symbols', strval($value[2]))->first();
					if (!$mater) {
						$check_materials = collect($array_maters)->where('IsDelete', 0)->where('Name', $value[1])->where('Symbols', $value[2])->first();
						if (!$check_materials) {
							$id_materials_max++;
							$id_materials   = $id_materials_max;
							$array_mater = [
								'ID' 			=> intval($id_materials_max),
								'CD' 			=> strval($value[0]),
								'Name' 			=> strval($value[1]),
								'Symbols' 		=> strval($value[2]),
								'Decription'    => strval($value[3]),
								'Size' 			=> strval($value[4]),
								'Color' 		=> strval($value[5]),
								'Unit' 			=> strval($value[6]),
								'Code' 			=> strval($value[7]),
								'User_Created' 	=> $user_created,
								'User_Updated' 	=> $user_updated,
								'Time_Created'	=> Carbon::now(),
								'Time_Updated'	=> Carbon::now(),
								'IsDelete' 		=> 0
							];
							array_push($array_maters, $array_mater);
						} else {
							// dd($check_materials);
							$id_materials		= $check_materials['ID'];
						}
					} else {
						$id_materials		= $mater->ID;
					}
	
					$bom =
						[
							'Materials_ID' => $id_materials,
							'Location_ID'  => $id_location,
							'Time_Created' => Carbon::now(),
							'Time_Updated' => Carbon::now(),
							'User_Created' => Auth::user()->id,
							'User_Updated' => Auth::user()->id,
						];
	
					array_push($array_bom, $bom);
					// }
				}
				// $value_pro = collect($value)->first();
			}
		}

		$array_locations_chunk = array_chunk($array_locations, 50);
		$array_maters_chunk = array_chunk($array_maters, 50);

		for ($i = 0; $i < count($array_maters_chunk); $i++) {
			$maters = DB::table('Master_Materials')->insert($array_maters_chunk[$i]);
		}
		for ($j = 0; $j < count($array_locations_chunk); $j++) {
			$pros = DB::table('Master_Location')->insert($array_locations_chunk[$j]);
		}

		$array_bom_chunk = array_chunk($array_bom, 50);
		for ($l = 0; $l < count($array_bom_chunk); $l++) {
			DB::table('Master_Materials_Location')->insert($array_bom_chunk[$l]);
		}

		return $err;
	}
}
