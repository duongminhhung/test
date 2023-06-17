<?php

namespace App\Libraries\MasterData;

use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\Auth;
use Hash;
use Session;
use DB;
use Carbon\Carbon;
use App\Models\MasterData\MasterBOM;
use App\Models\MasterData\MasterMaker;

/**
 * 
 */
class MasterMakerLibraries
{
    public function get_name_and_symbols_makers()
    {
        // dd(1);
      return  MasterMaker::where('IsDelete',0)->get(['ID','Name','Symbols']);
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
                $check_isset =  MasterMaker::where('IsDelete',0)->where('Name',$request->Name)->first();
                if($check_isset)
                {
                    $er = __('Names that already exist');
                    array_push($erorr,$er);
                }
           }
           else
           {
                $check_isset =  MasterMaker::where('IsDelete',0)->where('Name',$request->Name)->where('ID','<>',$request->ID)->first();
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
                $check_isset =  MasterMaker::where('IsDelete',0)->where('Symbols',$request->Key)->first();
                if($check_isset)
                {
                    $er = __('Key that already exist');
                    array_push($erorr,$er);
                }
           }
           else
           {
                $check_isset =  MasterMaker::where('IsDelete',0)->where('Symbols',$request->Key)->where('ID','<>',$request->ID)->first();
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
        $id = $request->ID;
		$user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
		if (isset($id) && $id != '') {
			if (!Auth::user()->checkRole('update_master') && Auth::user()->level != 9999) {
				abort(401);
			}
			$material_find = MasterMaker::where('IsDelete', 0)->where('ID', $id)->first();

			$maker = MasterMaker::where('ID', $id)->update([
				'Name'          =>  $request->Name,
                'Symbols'       =>  $request->Key,
                'Note'          =>  $request->Note,
				'User_Updated'  =>  $user_updated
			]);

			return (object)[
				'status' => true,
				'data'	 => $maker
			];
		} else {
			if (!Auth::user()->checkRole('create_master') && Auth::user()->level != 9999) {
				abort(401);
			}
			$maker = MasterMaker::create([
				'Name'          => $request->Name,
                'Symbols'       => $request->Key,
                'Note'          => $request->Note,
				'User_Created'  => $user_created,
				'User_Updated'  => $user_updated,
				'IsDelete'      => 0
			]);
			return (object)[
				'status' => true,
				'data'	 => $maker
			];
		}
    }
    public function destroy($request)
	{
		$user_created = Auth::user()->id;
		$user_updated = Auth::user()->id;
		$find = MasterMaker::where('ID', $request->ID)->update([
			'IsDelete' 		=> 1,
			'User_Updated'	=> Auth::user()->id
        ]);
        
		return __('Delete') . '  ' . __('Success');
	}
}
