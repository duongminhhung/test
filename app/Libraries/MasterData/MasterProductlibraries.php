<?php

namespace App\Libraries\MasterData;

use Illuminate\Validation\Rule;
use Validator;
use App\Models\MasterData\MasterProduct;
use App\Models\MasterData\MasterBOM;
use App\Models\MasterData\MasterMaker;
use App\Models\MasterData\MasterMaterials;
use App\Models\WarehouseManagement\ImportDetail;
use Illuminate\Support\Facades\Auth;
use Hash;
use Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * 
 */
class MasterProductLibraries
{
	public function get_name_and_symbols_product()
	{
		return  MasterProduct::where('IsDelete', 0)->get(['ID', 'Hinban', 'Size', 'Color','Code']);
	}
	public function get_size_product()
	{
		return  MasterProduct::where('IsDelete', 0)->get(['Size'])->Unique('Size');
	}
	public function get_colro_product()
	{
		return  MasterProduct::where('IsDelete', 0)->get(['Color'])->Unique('Color');
	}
	public function get_data_export($request)
	{
		// dd($request);
		$name       = $request->Hinban;
		if($name)
		{
			$product 		= MasterProduct::where('IsDelete', 0)
			->when($name, function ($query, $name) {
				return $query->where('ID', $name);
			})
			->get();
			$product_id = $product->map(function($data){
				return $data->ID;
			});
			
			// $data = MasterBOM::where('IsDelete',0)->whereIn('Product_ID',$product_id)->get();
            $data = MasterBOM::where('Master_BOM.IsDelete',0)
			->where('Master_Product.IsDelete',0)
			->where('Master_Materials.IsDelete',0)
            ->whereIn('Product_ID',$product_id)
            ->join('Master_Product', 'Master_Product.ID', '=', 'Master_BOM.Product_ID')
            ->join('Master_Materials', 'Master_Materials.ID', '=', 'Master_BOM.Materials_ID')
            ->select(
                'Master_Product.Hinban as Hinban',
                'Master_Product.Size as Product_Size',
                'Master_Product.Color as Product_Color',
                'Master_Materials.CD as Materials_ID',
                'Master_Materials.Name as Materials_Name',
                'Master_Materials.Symbols as Materials_Symbols',
                'Master_Materials.Decription as Materials_Decription',
                'Master_Materials.Size as Materials_Size',
                'Master_Materials.Color as Materials_Color',
                'Master_Materials.Code as Materials_Code',
                'Master_BOM.Quantity_Materials',
                'Master_Materials.Unit as Materials_Unit',
                "Master_BOM.IsDelete as A",
                "Master_BOM.IsDelete as B",
                "Master_BOM.IsDelete as C",
                "Master_BOM.IsDelete as D",
                "Master_BOM.IsDelete as E",
                "Master_BOM.IsDelete as F",
                "Master_BOM.IsDelete as G",
                "Master_BOM.IsDelete as H",
                "Master_BOM.IsDelete as J",
                "Master_BOM.IsDelete as K",
                "Master_Materials.Process as Materials_Process",
            )
            ->get();
			// dd($data);
		}
		else
		{
			$data = MasterBOM::where('Master_BOM.IsDelete',0)
			->where('Master_Product.IsDelete',0)
			->where('Master_Materials.IsDelete',0)
            ->join('Master_Product', 'Master_Product.ID', '=', 'Master_BOM.Product_ID')
            ->join('Master_Materials', 'Master_Materials.ID', '=', 'Master_BOM.Materials_ID')
            ->select(
                'Master_Product.Hinban as Hinban',
                // 'Master_Product.ID',
                // 'Master_Materials.ID',
                'Master_Product.Size as Product_Size',
                'Master_Product.Color as Product_Color',
                'Master_Materials.ID as ID',
                'Master_Materials.Name as Materials_Name',
                'Master_Materials.Symbols as Materials_Symbols',
                'Master_Materials.Decription as Materials_Decription',
                'Master_Materials.Size as Materials_Size',
                'Master_Materials.Color as Materials_Color',
                'Master_Materials.Code as Materials_Code',
                'Master_BOM.Quantity_Materials',
                'Master_Materials.Unit as Materials_Unit',
                // 'Master_Product.Code as Product_Code',
				"Master_BOM.IsDelete as A",
                "Master_BOM.IsDelete as B",
                "Master_BOM.IsDelete as C",
                "Master_BOM.IsDelete as D",
                "Master_BOM.IsDelete as E",
                "Master_BOM.IsDelete as F",
                "Master_BOM.IsDelete as G",
                "Master_BOM.IsDelete as H",
                "Master_BOM.IsDelete as J",
                "Master_BOM.IsDelete as K",
                "Master_Materials.Process as Materials_Process",

            )
            ->orderBy('Master_BOM.ID')
            ->get();
		}
		// dd($data);
      	return  $data;
	}
	public function check_data($request)
	{
		$erorr = [];
		if ($request->Hinban == '') {
			$er = __('Hinban cant be left blank');
			array_push($erorr, $er);
		} else {
			if (!$request->ID) {
				$check_isset =  MasterProduct::where('IsDelete', 0)->where('Hinban', $request->Hinban)->first();
				if ($check_isset) {
					$er = __('Hinban that already exist');
					array_push($erorr, $er);
				}
			} else {
				$check_isset =  MasterProduct::where('IsDelete', 0)->where('Hinban', $request->Hinban)->where('ID', '<>', $request->ID)->first();
				if ($check_isset) {
					$er = __('Hinban that already exist');
					array_push($erorr, $er);
				}
			}
		}
		// dd($request->array_materials);

		// if(count($request->array_materials) > 0)
		// {
		// 	if($request->ID )
		// 	{
		// 		foreach ($request->array_materials as $v) {
		// 			$v_explode 				= explode('-', $v);
		// 			$check_materials_stock 	= ImportDetail::where('IsDelete',0)->where('Materials_ID',$v_explode[0])->get();
		// 			$bom 					= MasterBOM::where('IsDelete',0)->where('Materials_ID',$v_explode[0])->where('Product_ID',$request->ID)->first();
		// 			$materials 				= MasterMaterials::where('IsDelete',0)->where('ID',$v_explode[0])->first();
		// 			dd($bom->Quantity_Materials , $v_explode[1],$bom->Quantity_Materials == $v_explode[1]);
		// 			if($check_materials_stock->sum('Inventory') > 0)
		// 			{
		// 				$er = __('Materials Still Stocking') .' : ' . $materials->Name .'('.$materials->Symbols.')';
		// 				array_push($erorr, $er);
		// 			}
		// 			// dd($check_materials_stock);
		// 		}
		// 	}
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
	}

	public function add_or_create_data($request)
	{
		// dd($request);
		$id = $request->ID;
		$user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
		$id_product_max = MasterProduct::max('ID');

		if (isset($id) && $id != '') {
			if (!Auth::user()->checkRole('update_master') && Auth::user()->level != 9999) {
				abort(401);
			}
			$Product_find = MasterProduct::where('IsDelete', 0)->where('ID', $id)->first();

			$Product = MasterProduct::where('ID', $id)->update([
				'Hinban'     => $request->Hinban,
				'Size'       => $request->Size,
				'Code'       => $request->Code,
				'Color'      => $request->Color,
				'Note'       => $request->Note,
				'User_Updated'     => $user_updated
			]);
			MasterBOM::where('Product_ID', $Product_find->ID)->update([
				'IsDelete' => 1
			]);
			foreach ($request->array_materials as $v) {
				$v_explode = explode('-', $v);
				MasterBOM::create([
					'Product_ID'   => $Product_find->ID,
					'Materials_ID' => $v_explode[0],
					'Quantity_Materials' => $v_explode[1],
					'User_Created'     => $user_created,
					'User_Updated'     => $user_updated,
					'IsDelete'         => 0
				]);
			}
			return (object)[
				'status' => true,
				'data'	 => $Product
			];
		} else {
			if (!Auth::user()->checkRole('create_master') && Auth::user()->level != 9999) {
				abort(401);
			}
			$id_product_max++;
			$Product = MasterProduct::create([
				'ID'     			=> $id_product_max,
				'Hinban'     		=> $request->Hinban,
				'Size'       		=> $request->Size,
				'Color'      		=> $request->Color,
				'Code'      		=> $request->Code,
				'Note'          	=> $request->Note,
				'User_Created'     	=> $user_created,
				'User_Updated'     	=> $user_updated,
				'IsDelete'         	=> 0
			]);
			$Product_find = MasterProduct::where('IsDelete', 0)->where('Hinban', $request->Hinban)->first();
			foreach ($request->array_materials as $v) {
				$v_explode = explode('-', $v);
				MasterBOM::create([
					'Product_ID'   => $Product_find->ID,
					'Materials_ID' => $v_explode[0],
					'Quantity_Materials' => $v_explode[1],
					'User_Created'     => $user_created,
					'User_Updated'     => $user_updated,
					'IsDelete'         => 0
				]);
			}
			return (object)[
				'status' => true,
				'data'	 => $Product
			];
		}
	}

	public function destroy($request)
	{
		$user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
		$find_mat = MasterProduct::where('ID', $request->ID)->first();
		$find = MasterProduct::where('ID', $request->ID)->update([
			'IsDelete' 		=> 1,
			'User_Updated'	=> Auth::user()->id
		]);
		MasterBOM::where('Product_ID', $find_mat->ID)->delete();
		
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
		$err = [];
		$user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;

		foreach ($data as $key => $value) {
			if ($key > 0) {
				// dd($value);
				if ($value[0] != '') {

					$pro =   MasterProduct::where('IsDelete', 0)->where('Hinban', $value[0])->where('Size', $value[1])->where('Color', $value[2])->first();
					if (!$pro) {
						$pro = MasterProduct::create([
							'Hinban' 		=> $value[0],
							'Size' 			=> $value[1],
							'Color' 		=> $value[2],
							'User_Created'	=> $user_created,
							'User_Updated'	=> $user_updated,
							'IsDelete' 		=> 0
						]);
						$pro = MasterProduct::where('IsDelete', 0)->where('Hinban', $pro->Hinban)->where('Size', $pro->Size)->where('Color', $pro->Color)->first();
					}
					$mater = MasterMaterials::where('IsDelete', 0)->where('Name', $value[3])->where('Symbols', $value[4])->first();
					if (!$mater) {
						$mater = MasterMaterials::create([
							'Name' 			=> $value[3],
							'Symbols' 		=> $value[4],
							'User_Created' 	=> $user_created,
							'User_Updated' 	=> $user_updated,
							'IsDelete' 		=> 0
						]);
						$mater =  MasterMaterials::where('IsDelete', 0)->where('Name', $mater->Name)->where('Symbols', $mater->Symbols)->first();
					}
					// dd($mater, $pro);

					if ($mater && $pro) {
						$bom = MasterBOM::where('IsDelete', 0)->where('Product_ID', $pro->ID)->where('Materials_ID', $mater->ID)->first();
						if (!$bom) {
							MasterBOM::create([
								'Product_ID'         => $pro->ID,
								'Materials_ID'       => $mater->ID,
								'Quantity_Materials' => $value[5],
								'User_Created'	     => $user_created,
								'User_Updated'	     => $user_updated,
								'IsDelete'           => 0
							]);
						}
					}
				}
			}
		}
		return $err;
	}


	public function import_file2($request)
	{
		$data = $this->read_file($request);
		$err = [];
		$user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
		$array_pros = [];
		$array_maters = [];
		$array_bom = [];

		$id_product_max = MasterProduct::max('ID');
		$id_materials_max = MasterMaterials::max('ID');
		// dd($data);
		foreach ($data as $key => $value) {
			if ($key > 0) {
				if($value[0] != '')
				{
					$process_replace = str_replace('XUẤT ','',$value[19]);

					if($process_replace == 'DÁN BAO'){
						$process_replace = 'DB';
					}
					if($process_replace == 'BAO BÌ')
					{
						$process_replace = 'BB';
					}
					// dd($value[17]);
					$pro =   MasterProduct::where('IsDelete', 0)->where('Hinban', strval($value[0]))->where('Size', strval($value[1]))->where('Color', strval($value[2]))->first();
					if (!$pro) {
						$check_pro = collect($array_pros)->where('IsDelete', 0)->where('Hinban', strval($value[0]))->where('Size', strval($value[1]))->where('Color', strval($value[2]))->first();
						if (!$check_pro) {
							$id_product_max++;
							$id_product 	= $id_product_max;
							$pro = [
								'ID'			=> intval($id_product_max),
								'Hinban' 		=> strval($value[0]),
								'Size' 			=> strval($value[1]),
								'Color' 		=> strval($value[2]),
								'Code'			=> strval($value[15]),
								'User_Created'	=> $user_created,
								'User_Updated'	=> $user_updated,
								'Time_Created'	=> Carbon::now(),
								'Time_Updated'	=> Carbon::now(),
								'IsDelete' 		=> 0
							];
							array_push($array_pros, $pro);
						} else {
							$id_product		= $check_pro['ID'];
						}
					} else {
						MasterBOM::where('Product_ID', $pro->ID)->delete();
						$id_product		= $pro->ID;
					}
					// foreach ($value as $key1 => $value1) {
					$mater = MasterMaterials::where('IsDelete', 0)->where('Name', strval($value[4]))->where('Symbols', strval($value[6]))->first();
					// dd($mater);
					if (!$mater) {
						$check_materials = collect($array_maters)->where('IsDelete', 0)->where('Name', strval($value[4]))->where('Symbols', strval($value[6]))->first();
						if (!$check_materials) {
							$id_materials_max++;
							$id_materials   = $id_materials_max;
							$array_mater = [
								'ID' 			=> intval($id_materials_max),
								'Name' 			=> strval($value[4]),
								'Symbols' 		=> strval($value[6]),
								'Decription'    => strval($value[7]),
								'Size' 			=> strval($value[8]),
								'CD' 			=> strval($value[3]),
								'Color' 		=> strval($value[9]),
								'Code' 			=> strval($value[10]),
								'Unit' 			=> strval($value[12]),
								'Maker' 		=> strval($value[13]),
								'Unit_Price' 	=> strval($value[14]),
								'Process' 		=> $process_replace,
								'Type' 			=> strval($value[5]),
								'User_Created' 	=> $user_created,
								'User_Updated' 	=> $user_updated,
								'Time_Created'	=> Carbon::now(),
								'Time_Updated'	=> Carbon::now(),
								'IsDelete' 		=> 0
							];
							array_push($array_maters, $array_mater);
						} else {							
							$id_materials		= $check_materials['ID'];
						}
					} else {
						$id_materials		= $mater->ID;
						if(
						$mater->Size != strval($value[8]) || $mater->CD != strval($value[3]) 
						|| $mater->Color != strval($value[9]) || $mater->Code != strval($value[10]) 
						|| $mater->Unit != strval($value[12]) || $mater->Maker != strval($value[13]) 
						|| $mater->Unit_Price != strval($value[14]) || $mater->Process 	!= $process_replace || $mater->Type != strval($value[5])
						)
						{
							$mater->update([
								'Size' 			=> $mater->Size 	== strval($value[8]) ? $mater->Size : strval($value[8]),
								'CD' 			=> $mater->CD 		== strval($value[3]) ? $mater->CD : strval($value[3]),
								'Color' 		=> $mater->Color 	== strval($value[9]) ? $mater->Color : strval($value[9]),
								'Code' 			=> $mater->Code 	== strval($value[10]) ? $mater->Code : strval($value[10]),
								'Unit' 			=> $mater->Unit 	== strval($value[12]) ? $mater->Unit : strval($value[12]),
								'Maker' 		=> $mater->Maker 	== strval($value[13]) ? $mater->Maker : strval($value[13]),
								'Unit_Price' 	=> $mater->Unit_Price == strval($value[14]) ? $mater->Unit_Price : strval($value[14]),
								'Process' 		=> $mater->Process 	== $process_replace ? $mater->Process : $process_replace,
								'Type' 			=> $mater->Type 	== strval($value[5]) ? $mater->Type : strval($value[5]),
							]);
						}
					}
	
					$bom =
						[
							'Materials_ID' => $id_materials,
							'Product_ID' => $id_product,
							'Quantity_Materials' => floatval($value[11]),
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

		$array_pros_chunk = array_chunk($array_pros, 50);
		$array_maters_chunk = array_chunk($array_maters, 50);

		for ($i = 0; $i < count($array_maters_chunk); $i++) {
			$maters = DB::table('Master_Materials')->insert($array_maters_chunk[$i]);
		}
		for ($j = 0; $j < count($array_pros_chunk); $j++) {
			$pros = DB::table('Master_Product')->insert($array_pros_chunk[$j]);
		}

		$array_bom_chunk = array_chunk($array_bom, 50);
		for ($l = 0; $l < count($array_bom_chunk); $l++) {
			DB::table('Master_BOM')->insert($array_bom_chunk[$l]);
		}

		return $err;
	}
}
