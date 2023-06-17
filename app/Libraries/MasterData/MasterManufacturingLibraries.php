<?php

namespace App\Libraries\MasterData;

use App\Models\MasterData\ManufacturingCode;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\Auth;
use Hash;
use Session;
use DB;
use Carbon\Carbon;
use App\Models\MasterData\MasterBOM;
use App\Models\MasterData\MasterLocation;
use App\Models\MasterData\MasterManufacturing;
use App\Models\WarehouseManagement\ImportDetail;

/**
 * 
 */
class MasterManufacturingLibraries
{
    public function get_name_and_symbols_manufacturing()
    {
        return  MasterManufacturing::where('IsDelete', 0)->get(['ID', 'Name', 'Symbols']);
    }
    public function check_data($request)
    {
        // dd($request);
        $erorr = [];
        if ($request->array_code) {
            foreach($request->array_code as $value)
            {
                if (!$request->ID) {
                    $check_isset =  ManufacturingCode::where('IsDelete', 0)->where('Code', $value)->first();
                    if ($check_isset) {
                        $er = __('Code that already exist') . ' : ' . $value;
                        array_push($erorr, $er);
                    }
                } else {
                    $check_isset =  ManufacturingCode::where('IsDelete', 0)->where('Code', $value)->where('Manufacturing_ID', '<>', $request->ID)->first();
                    if ($check_isset) {
                        $er = __('Code that already exist'). ' : ' . $value;
                        array_push($erorr, $er);
                    }   
                }
            }
        }
        if ($request->Name == '') {
            $er = __('Names cant be left blank');
            array_push($erorr, $er);
        } else {
            if (!$request->ID) {
                $check_isset =  MasterManufacturing::where('IsDelete', 0)->where('Name', $request->Name)->first();
                if ($check_isset) {
                    $er = __('Names that already exist');
                    array_push($erorr, $er);
                }
            } else {
                $check_isset =  MasterManufacturing::where('IsDelete', 0)->where('Name', $request->Name)->where('ID', '<>', $request->ID)->first();
                if ($check_isset) {
                    $er = __('Names that already exist');
                    array_push($erorr, $er);
                }
            }
        }
        if ($request->Key == '') {
            $er = __('Key cant be left blank');
            array_push($erorr, $er);
        } else {
            if (!$request->ID) {
                $check_isset =  MasterManufacturing::where('IsDelete', 0)->where('Symbols', $request->Key)->first();
                if ($check_isset) {
                    $er = __('Key that already exist');
                    array_push($erorr, $er);
                }
            } else {
                $check_isset =  MasterManufacturing::where('IsDelete', 0)->where('Symbols', $request->Key)->where('ID', '<>', $request->ID)->first();
                if ($check_isset) {
                    $er = __('Key that already exist');
                    array_push($erorr, $er);
                }
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
            $manufacturing_find = MasterManufacturing::where('IsDelete', 0)->where('ID', $id)->first();

            $manufacturing_find->update([
                'Name'          =>  $request->Name,
                'Symbols'       =>  $request->Key,
                'Note'          =>  $request->Note,
                'User_Updated'  =>  $user_updated
            ]);

            if ($request->array_code) {
                ManufacturingCode::where('Manufacturing_ID', $manufacturing_find->ID)->update([
                    'IsDelete' => 1
                ]);

                foreach ($request->array_code as $value) {
                    ManufacturingCode::create([
                        'Code'          => $value,
                        'Manufacturing_ID' => $manufacturing_find->ID
                    ]);
                }
            }

            $location = MasterLocation::where('IsDelete', 0)->where('Manufacturing_ID', $id)->first();
            if ($location) {

                $location->update([
                    'Name'          =>  $request->Name,
                    'Symbols'       =>  $request->Key,
                    // 'Manufacturing_ID'  => $id,
                ]);
            } else {
                $location = MasterLocation::create([
                    'Name'              => $request->Name,
                    'Symbols'           => $request->Key,
                    'Manufacturing_ID'  => $id,
                    'Note'              => $request->Note,
                    'User_Created'      => $user_created,
                    'User_Updated'      => $user_updated,
                    'IsDelete'          => 0
                ]);
            }

            return (object)[
                'status' => true,
                'data'     => $manufacturing_find
            ];
        } else {
            if (!Auth::user()->checkRole('create_master') && Auth::user()->level != 9999) {
                abort(401);
            }
            $manufacturing = MasterManufacturing::create([
                'Name'          => $request->Name,
                'Symbols'       => $request->Key,
                'Note'          => $request->Note,
                'User_Created'  => $user_created,
                'User_Updated'  => $user_updated,
                'IsDelete'      => 0
            ]);

            $manufacturing = MasterManufacturing::where('IsDelete', 0)->where('Name', $manufacturing->Name)->where('Symbols', $manufacturing->Symbols)->first();
            if ($request->array_code) {
                foreach ($request->array_code as $value) {
                    ManufacturingCode::create([
                        'Code'          => $value,
                        'Manufacturing_ID' => $manufacturing->ID
                    ]);
                }
            }
            $location = MasterLocation::create([
                'Name'              => $request->Name,
                'Symbols'           => $request->Key,
                'Manufacturing_ID'  => $manufacturing->ID,
                'Note'              => $request->Note,
                'User_Created'      => $user_created,
                'User_Updated'      => $user_updated,
                'IsDelete'          => 0
            ]);
            return (object)[
                'status' => true,
                'data'     => $manufacturing
            ];
        }
    }

    public function check_destroy($request)
    {

        $erorr = [];
        $find = MasterManufacturing::where('ID',$request->ID)->first();
        $check_isset = ImportDetail::where('IsDelete',0)->where('Manufacturing',$find->ID)->first();

        if($check_isset)
        {
            $er = __('Manufacturing In Stock');
            array_push($erorr, $er);
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

    public function destroy($request)
    {
        $user_created = Auth::user()->id;
        $user_updated = Auth::user()->id;
        $find = MasterManufacturing::where('ID',$request->ID)->first();
        $find->update([
            'IsDelete'         => 1,
            'User_Updated'    => Auth::user()->id
        ]);

        MasterLocation::where('Manufacturing_ID', $find->ID)->update([
            'IsDelete'         => 1,
        ]);
        ManufacturingCode::where('Manufacturing_ID', $find->ID)->update([
            'IsDelete'         => 1,
        ]);

        // return (object)[
        //     'status' => true,
        //     'data'     => $find
        // ];

        return __('Delete') . '  ' . __('Success');
    }
}
