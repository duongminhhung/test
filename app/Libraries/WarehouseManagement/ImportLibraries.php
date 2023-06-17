<?php

namespace App\Libraries\WarehouseManagement;

use Illuminate\Validation\Rule;
use Validator;
use App\Models\WarehouseManagement\CommandImport;
use Hash;
use Session;
use Carbon\Carbon;
use App\Models\MasterData\MasterMaterials;
use App\Models\MasterData\MasterLocation;
use App\Models\WarehouseManagement\CommandExport;
use App\Models\WarehouseManagement\ImportDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\OrderManagement\FollowOrder;
class ImportLibraries
{
    public function get_name_and_symbols_command()
    {
      return  CommandImport ::where('IsDelete',0)->get(['ID','Name','Symbols']);
    }
    public function get_command_width_id($request)
    {
        return  CommandImport ::where('IsDelete',0)->where('ID',$request->ID)->first();
    }
    public function check_data($request)
    {
        // dd($request);
        $erorr = [];
 
        if($request->Type == '')
        {
            $er = __('Type cant be left blank');
            array_push($erorr,$er);
        }
        else
        {
                if($request->Type == 1)
                {
                    if($request->Maker == '')
                    {
                        $er = __('Maker cant be left blank');
                        array_push($erorr,$er);
                    }
                    
                }
                else if($request->Type == 2)
                {
                    if($request->Manufacturing == '')
                    {
                        $er = __('Manufacturing cant be left blank');
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
    public function check_data_create_box($request)
    {
        $erorr = [];
        if($request->ID_Materials == '')
        {
            $er = __('Materials cant be left blank');
            array_push($erorr,$er);
        }
        else
        {
                $check_isset =  MasterMaterials::where('IsDelete',0)->where('ID',$request->ID_Materials)->first();
                if(!$check_isset)
                {
                    $er = __('Materials that already exist');
                    array_push($erorr,$er);
                }
        }
        if($request->Quantity == '')
        {
            $er = __('Quantity cant be left blank');
            array_push($erorr,$er);
        }
        else
        {
                if($request->Quantity <= 0)
                {
                    $er = __('Quantity cant be left blank');
                    array_push($erorr,$er); 
                }
                
        }
        if($request->Lot == '')
        {
            $er = __('Lot cant be left blank');
            array_push($erorr,$er);
        }
        if($request->ID_Location == '')
        {
            $er = __('Location cant be left blank');
            array_push($erorr,$er);
        }
        else
        {
                $check_isset =  MasterLocation::where('IsDelete',0)->where('ID',$request->ID_Location)->first();
                if(!$check_isset)
                {
                    $er = __('Location that already exist');
                    array_push($erorr,$er);
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
		$user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
        $max = CommandImport::max('ID');
        // dd($max);
		$command = CommandImport::create([
			'PO'       => $request->PO,
            'Name'     => ($request->Type == 1 ? 'LNM' : 'LNL').'0000'.($max+1),
            'Symbols'  => $request->Symbols,
            'Type'     => $request->Type,
            'Maker'    => $request->Type == 1 ? $request->Maker : null,
            'Manufacturing'    => $request->Type == 2 ? $request->Manufacturing : null,
            'Note'             => $request->Note,
			'User_Created'     => $user_created,
			'User_Updated'     => $user_updated,
			'IsDelete'         => 0
		]);
		return (object)[
			'status' => true,
			'data'	 => $command
		];
    }

    public function add_or_create_data_box($request)
    {
        // dd($request);
        $user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
        // dd($max);
        $error = [];
        $quantity = $request->Quantity;
        $data_order = DB::table('Follow_Order')->where('IsDelete',0)->where('Status',0)->where('Materials_ID',$request->ID_Materials)->get();
        // $quantity_need = 0;
        $quantity_need = collect($data_order)->sum('DIF') - collect($data_order)->sum('Quantity_Input');

        if($quantity_need < $request->Quantity)
        {
            $er = __('Quantity Import Bigger Than Order');
            array_push($error,$er);

            return (object)[
                'status' => false,
                'data'	 => $error
            ];
        }
        foreach($data_order as $value)
        {
            if($quantity > 0)
            {
                DB::table('Follow_Order')->where('ID',$value->ID)->update([
                    'Quantity_Input' => $value->DIF < $quantity + $value->Quantity_Input ? $value->DIF :  $quantity + $value->Quantity_Input,
                    'Status' => $value->DIF <= $quantity + $value->Quantity_Input ? 1 :  0
                ]);
                $quantity = $quantity - ($value->Quantity_Input > 0 ? $value->DIF - $value->Quantity_Input : $value->DIF);
            }
        }
		$box = ImportDetail::create([
			'Command_ID'       => $request->Command_ID,
            'Materials_ID'     => $request->ID_Materials,
            'Lot'              => $request->Lot,
            'Maker'            => $request->Maker,
            'Manufacturing'    => $request->Manufacturing,
            'Location'         => $request->ID_Location,
            'Quantity'         => $request->Quantity,
            'Inventory'        => $request->Quantity,
            'Time_Import'      => Carbon::now(),
            'Note'             => $request->Note,
			'User_Created'     => $user_created,
			'User_Updated'     => $user_updated,
			'IsDelete'         => 0
		]);

		return (object)[
			'status' => true,
			'data'	 => $box
		];
    }

    public function command_destroy($request)
	{
		$user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
        $find_mat = CommandImport::where('ID', $request->ID)->first();
		$find = CommandImport::where('ID', $request->ID)->update([
			'IsDelete' 		=> 1,
			'User_Updated'	=> Auth::user()->id
        ]);
		return __('Delete') . '  ' . __('Success');
	}

    public function edit_location($request)
    {
        $user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
        $find_mat = ImportDetail::where('ID', $request->ID)->first();
		$find = ImportDetail::where('ID', $request->ID)->update([
			'Location' 		=> $request->ID_Location,
			'User_Updated'	=> Auth::user()->id
        ]);
		return (object)[
			'status' => true,
			'data'	 => $find
		];
    }

    // retype 
    public function check_data_retype($request)
    {
        $erorr = [];
        if($request->ID_Materials == '')
        {
            $er = __('Materials cant be left blank');
            array_push($erorr,$er);
        }
        else
        {
                $check_isset =  MasterMaterials::where('IsDelete',0)->where('ID',$request->ID_Materials)->first();
                if(!$check_isset)
                {
                    $er = __('Materials that already exist');
                    array_push($erorr,$er);
                }
        }
        if($request->Quantity == '')
        {
            $er = __('Quantity cant be left blank');
            array_push($erorr,$er);
        }
        else
        {
                if($request->Quantity <= 0)
                {
                    $er = __('Quantity cant be left blank');
                    array_push($erorr,$er); 
                }
                
        }
        if($request->Lot == '')
        {
            $er = __('Lot cant be left blank');
            array_push($erorr,$er);
        }
        if($request->ID_Location == '')
        {
            $er = __('Location cant be left blank');
            array_push($erorr,$er);
        }
        else
        {
                $check_isset =  MasterLocation::where('IsDelete',0)->where('ID',$request->ID_Location)->first();
                if(!$check_isset)
                {
                    $er = __('Location that already exist');
                    array_push($erorr,$er);
                }
        }
        $stock_manufacturing =  ImportDetail::where('IsDelete', 0)
                                            ->where('Materials_ID', $request->ID_Materials)
                                            ->where('Lot', $request->Lot)
                                            // ->where('Manufacturing', $request->Manufacturing)
                                            ->where('Status', '>', 0) // đã nhập kho
                                            ->where('Type',3) 
                                            // ->orderBy('ID', 'desc')
                                            ->first();
                                            // dd($stock_manufacturing,$request);
        if($request->Quantity > $stock_manufacturing->Inventory)
        {
            $er = __('Quantity Stock Lower Quantity Retype');
                    array_push($erorr,$er);
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
    public function retype_box($request)
    {
        // dd($request);
        $user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;

        $command = CommandImport::where('ID',$request->Command_ID)->first();

        $stock_manufacturing =  ImportDetail::where('IsDelete', 0)
                                            ->where('Materials_ID', $request->ID_Materials)
                                            ->where('Lot', $request->Lot)
                                            // ->where('Manufacturing', $request->Manufacturing)
                                            ->where('Status', '>', 0) // đã nhập kho
                                            ->where('Type',3) 
                                            ->orderBy('Time_Import')
                                            ->first();

        $stock_retype =  ImportDetail::where('IsDelete', 0)
                                    ->where('Command_ID', $request->Command_ID)
                                    ->where('Materials_ID', $request->ID_Materials)
                                    ->where('Lot', $request->Lot)
                                    ->where('Location', $request->ID_Location)
                                    ->where('Status', '>', 0) // đã nhập kho
                                    ->where('Type',2) 
                                    ->orderBy('Time_Import')
                                    ->first();
        
        $stock_first = ImportDetail::where('IsDelete',0)
                                    ->where('Materials_ID', $request->ID_Materials)
                                    ->where('Lot', $request->Lot)
                                    ->where('Location', $request->ID_Location)
                                    // ->where('Command_ID', $request->Command_ID)
                                    ->where('Status', '>', 0) // đã nhập kho
                                    ->orderBy('Time_Import')
                                    ->first();

        // dd($stock_retype,$request,$stock_first);
        ImportDetail::where('ID',$stock_manufacturing->ID)->update([
            "Inventory" => ($stock_manufacturing->Inventory - $request->Quantity),
        ]);

        // if($stock_first)
        // {
        //     $data = ImportDetail::where('ID',$stock_first->ID)->update([
        //         'Inventory'         => $stock_first->Inventory + $request->Quantity,
        //         'Quantity'          => $stock_first->Quantity + $request->Quantity,
        //     ]);
        // }
        // else
        // {
            // if($stock_retype)
            // {
            //     $data = ImportDetail::where('ID',$stock_retype->ID)->update([
            //         'Quantity'          => $stock_retype->Quantity + $request->Quantity,
            //         'Inventory'         => $stock_retype->Inventory + $request->Quantity,
            //     ]);
            // }
            // else
            // {
                $data = ImportDetail::create([
                    'Command_ID'        => $request->Command_ID,
                    'Materials_ID'      => $request->ID_Materials,
                    'Manufacturing'     => $command->Manufacturing,
                    'Lot'               => $request->Lot,
                    'Maker'             => $stock_manufacturing->Maker,
                    'Location'          => $request->ID_Location,
                    'Quantity'          => $request->Quantity,
                    'Inventory'         => $request->Quantity,
                    'Status'            => 1,
                    'Type'              => 2,
                    'Note'              => $request->Note,
                    'Time_Import'       => Carbon::now(),
                    'User_Created'      => $user_created,
                    'User_Updated'      => $user_updated,
                    'IsDelete'          => 0
                ]);
            // }
        // }
        
		return (object)[
			'status' => true,
			'data'	 => $data
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
    public function import_file_excel($request)
    {
        $file     = request()->file('fileImport');
        $name     = $file->getClientOriginalName();
        $arr      = explode('.', $name);
        $fileName = strtolower(end($arr));
        $data = $this->read_file($request);
        $user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
        // dd($data,$request);
        $po = '';
        $maker = '';
        if($request->PO_Colums && $request->PO_Row)
        {
            $po = $data[$request->PO_Row-1][$request->PO_Colums-1];
        }
        if($request->Maker_Colums && $request->Maker_Row)
        {
            $maker = $data[$request->Maker_Row-1][$request->Maker_Colums-1];
        }
        $count = 0;
        if($request->Data_Start)
        {
            $count = $request->Data_Start - 1;
        }
       
        $array_data = [];
        $error = [];
        foreach($data as $key => $value)
        {
            if($key > $count)
            {
                if($request->Materials && $request->Quantity)
                {
                    $materials = $value[$request->Materials-1];   
                    $lot = null;
                    if($request->Lot)
                    {
                        $lot = $value[$request->Lot-1];
                    }
                    $Quantity = $value[$request->Quantity-1];
                    $check_materials =  MasterMaterials::where('IsDelete',0)->where('Symbols',$materials)->first();
                    if($check_materials && count($check_materials->location) > 0)
                    {
                        // check số lượng order
                        $data_order = FollowOrder::where('IsDelete',0)->where('Materials_ID',$check_materials->ID)->where('Status',0)->get();
                        $quantity_need = $data_order->sum('DIF') - $data_order->sum('Quantity_Input');
                        if($quantity_need >= $Quantity)
                        {
                            $loca = collect($check_materials->location)->first();
                            $box = [
                                'Command_ID'       => null,
                                'Materials_ID'     => $check_materials->ID,
                                'Lot'              => $lot,
                                'Location'         => $loca->ID,
                                'Maker'            => $maker,
                                'Quantity'         => floatval($Quantity),
                                'Inventory'        => floatval($Quantity),
                                'Time_Import'      => Carbon::now(),
                                'Note'             => $request->Note,
                                'User_Created'     => $user_created,
                                'User_Updated'     => $user_updated,
                                'IsDelete'         => 0
                            ];
                            array_push($array_data,$box);
                        }
                        else
                        {
                            $er = __('NVL').' '.$materials.' '.__('Số lượng Nhập Kho lớn hơn số lượng order');
                            array_push($error, $er);
                        }
                        
                    }
                    else
                    {
                        if(!$check_materials && $check_materials != '')
                        {
                            $er = __('NVL').' '.$materials.' '.__('không tồn tại');
                            array_push($error, $er);
                        }
                        else
                        {
                            if($check_materials != '' && count($check_materials->location) <= 0 )
                            {
                                $er =  __('NVL').' '.$materials.' '.__('Chưa được cấu hình kho');
                                array_push($error, $er);
                            }
                        }
                    }
                }
                
            }
        }
        // dd($error);
        if(count($error) <= 0)
        {
            $max = CommandImport::max('ID');
            $command = CommandImport::create([
                'PO'       => $po,
                'Name'     => 'LNM0000'.($max+1),
                'Symbols'  => 'LNM0000'.($max+1),
                'Type'     => 1,
                'Maker'    =>  $maker,
                'Note'          => $request->Note,
                'User_Created'     => $user_created,
                'User_Updated'     => $user_updated,
                'IsDelete'         => 0
            ]);
            $command = CommandImport::orderBy('ID','desc')->first();
            foreach($array_data as $value)
            {
                // dd($value);
                $data_order = FollowOrder::where('IsDelete',0)->where('Materials_ID',$value['Materials_ID'])->where('Status',0)->get();
                // dd($data_order);
                $quantity = $value['Quantity'];
                foreach($data_order as $val)
                {
                    if($quantity > 0)
                    {
                        $quantity_need = $val->DIF - $val->Quantity_Input;
                        FollowOrder::where('ID',$val->ID)->update([
                            'Quantity_Input' => $quantity_need  <  $quantity ?  $val->Quantity_Input + $quantity_need : $val->Quantity_Input + $quantity,
                            'Status' =>  $quantity_need  <=  $quantity ? 1 : 0
                        ]);
                        $quantity = $quantity-$quantity_need;
                    }
                }
                $value['Command_ID'] = $command->ID;
                ImportDetail::create($value);
            }
        }
        return array_unique($error);
    }

    public function export_file_excel($request)
    {
        $command_id = $request->ID;
        $data = CommandImport::where('IsDelete',0)->where('ID',$command_id)->first();
        return $data;
    }
}
