<?php

namespace App\Business;

use App\Models\Oee\ProductDefectiveLog;
use App\Models\Oee\RuntimeHistory;
use App\Models\Oee\OeeDay;
use Carbon\Carbon;

class Statistic
{
    public function oeeReportByDay($request) {
        $oeeDay = new OeeDay();
        $start = new Carbon($request->startDate);
        $end = new Carbon($request->endDate);

        if($request->machineId != 0) {
            $oeeDay = $oeeDay->where('Master_Machine_ID', $request->machineId);
        }

        $oeeDay = $oeeDay->where('Time_Created', '>=', $start->add(8, 'hours'))
                         ->where('Time_Updated', '<=', $end->add(1, 'day')->add(8, 'hours')->sub(1, 'second'));

        $oeeDay = $oeeDay->get()->groupBy(function($item) {
            return substr($item->Time_Created, 0, 10);
        });

        $oeeDay = $oeeDay->map(function($item, $key) {
            return [
                'Oee' => round($item->avg('Oee'), 2),
                'A' => round($item->avg('A'), 2),
                'P' => round($item->avg('P'), 2),
                'Q' => round($item->avg('Q'), 2),
                'Date' => $key
            ];
        });

        return $oeeDay;
    }

    public function oeeReportByMachine($request) {
        $oeeDay = new OeeDay();
        $start = new Carbon($request->startDate);
        $end = new Carbon($request->endDate);

        if($request->machineId[0] != 0) {
            $oeeDay = $oeeDay->whereIn('Master_Machine_ID', $request->machineId);
        }

        $oeeDay = $oeeDay->where('Oee_Day.Time_Created', '>=', $start->add(8, 'hours'))
                         ->where('Oee_Day.Time_Updated', '<=', $end->add(1, 'day')->add(8, 'hours')->sub(1, 'second'));

        $oeeDay = $oeeDay->join('Master_Machine', 'Oee_Day.Master_Machine_ID', '=', 'Master_Machine.ID')
                         ->select('Oee_Day.Oee', 'Oee_Day.A', 'Oee_Day.P', 'Oee_Day.Q', 'Master_Machine.Name as Machine_Name')
                         ->get()
                         ->groupBy('Machine_Name');
        
        $oeeDay = $oeeDay->map(function($item, $key) {
            return [
                'Oee' => round($item->avg('Oee'), 2),
                'A' => round($item->avg('A'), 2),
                'P' => round($item->avg('P'), 2),
                'Q' => round($item->avg('Q'), 2),
                'Machine_Name' => $key
            ];
        });

        return $oeeDay;
    }

    public function productDefectiveReport($request) {
        $start = new Carbon($request->startDate);
        $end = new Carbon($request->endDate);
        $productDefective = new ProductDefectiveLog();

        if($request->machineId[0] != 0) {
            $productDefective = $productDefective->whereIn('Product_Defective_Log.Master_Machine_ID', $request->machineId);
        }

        $productDefective = $productDefective->where('Product_Defective_Log.Time_Created', '>=', $start->add(8, 'hours'))
                                             ->where('Product_Defective_Log.Time_Updated', '<=', $end->add(1, 'day')->add(8, 'hours')->sub(1, 'second'));

        $productDefective = $productDefective->join('Master_Machine', 'Product_Defective_Log.Master_Machine_ID', '=', 'Master_Machine.ID')
                                             ->select('Product_Defective_Log.Quantity', 'Product_Defective_Log.Time_Created', 'Master_Machine.Name as Machine_Name')
                                             ->get();
        
        $processedProductDefective = $productDefective->groupBy('Machine_Name')->map(function($item, $key) {
            return $item->sum('Quantity');
        });
        
        return [
            'productDefective' => $productDefective,
            'processedProductDefective' => $processedProductDefective
        ];
    }

    public function errorAndNotError($request) {
        $start = new Carbon($request->startDate);
        $end = new Carbon($request->endDate);
        $runtimeHistory = RuntimeHistory::whereIn('IsRunning', [2, 3]);

        if($request->machineId[0] != 0) {
            $runtimeHistory = $runtimeHistory->whereIn('Master_Machine_ID', $request->machineId);
        }

        $runtimeHistory = $runtimeHistory->where('Time_Created', '>=', $start->add(8, 'hours'))
                                         ->where('Time_Updated', '<=', $end->add(1, 'day')->add(8, 'hours')->sub(1, 'second'));

        $runtimeHistory = $runtimeHistory->get()->groupBy(function($item) {
            $machineStatus = $item->IsRunning;

            if($machineStatus == 2) {
                return 'stopNotError';
            } elseif( $machineStatus == 3 ) {
                return 'stopError';
            }
        });

        $runtimeHistory = $runtimeHistory->map(function($item, $key) {
            return $item->sum('Duration');
        });

        return $runtimeHistory;
    }

    public function machineError($request) {
        $start = new Carbon($request->startDate);
        $end = new Carbon($request->endDate);
        $runtimeHistory = RuntimeHistory::join('Master_Status', 'Runtime_History.Master_Status_ID', '=', 'Master_Status.ID')
                                        ->select('Runtime_History.*', 'Master_Status.Name as Master_Status')
                                        ->where('IsRunning', 3);
                                        
        if($request->machineId[0] != 0) {
            $runtimeHistory = $runtimeHistory->whereIn('Runtime_History.Master_Machine_ID', $request->machineId);
        }

        $runtimeHistory = $runtimeHistory->where('Runtime_History.Time_Created', '>=', $start->add(8, 'hours'))
                                         ->where('Runtime_History.Time_Updated', '<=', $end->add(1, 'day')->add(8, 'hours')->sub(1, 'second'));
        
        $runtimeHistory = $runtimeHistory->get()->groupBy('Master_Status')->map(function($item) {
            return $item->sum('Duration');
        });

        return $runtimeHistory;
    }

    public function stopNotError($request) {
        $start = new Carbon($request->startDate);
        $end = new Carbon($request->endDate);
        $runtimeHistory = RuntimeHistory::join('Master_Status', 'Runtime_History.Master_Status_ID', '=', 'Master_Status.ID')
                                        ->select('Runtime_History.*', 'Master_Status.Name as Master_Status', 'Master_Status.Master_Status_Type_ID')
                                        ->where('IsRunning', 2);
                                        
        if($request->machineId[0] != 0) {
            $runtimeHistory = $runtimeHistory->whereIn('Runtime_History.Master_Machine_ID', $request->machineId);
        }

        $runtimeHistory = $runtimeHistory->where('Runtime_History.Time_Created', '>=', $start->add(8, 'hours'))
                                         ->where('Runtime_History.Time_Updated', '<=', $end->add(1, 'day')->add(8, 'hours')->sub(1, 'second'));


        $runtimeHistory = $runtimeHistory->get()->groupBy(function($item) {
            switch($item->Master_Status_Type_ID) {
                case 2:
                    return $item->Master_Status;
                case 3:
                    return 'quality';
            }
        });

        $runtimeHistory = $runtimeHistory->map(function($item) {
            return $item->sum('Duration');
        });

        return $runtimeHistory;
    }

    public function stopQuality($request) {
        $start = new Carbon($request->startDate);
        $end = new Carbon($request->endDate);
        $runtimeHistory = RuntimeHistory::join('Master_Status', 'Runtime_History.Master_Status_ID', '=', 'Master_Status.ID')
                                        ->select('Runtime_History.*', 'Master_Status.Name as Master_Status', 'Master_Status.Master_Status_Type_ID')
                                        ->where('IsRunning', 2)
                                        ->where('Master_Status_Type_ID', 3);
                                        
        if($request->machineId[0] != 0) {
            $runtimeHistory = $runtimeHistory->whereIn('Runtime_History.Master_Machine_ID', $request->machineId);
        }

        $runtimeHistory = $runtimeHistory->where('Runtime_History.Time_Created', '>=', $start->add(8, 'hours'))
                                         ->where('Runtime_History.Time_Updated', '<=', $end->add(1, 'day')->add(8, 'hours')->sub(1, 'second'));

        $runtimeHistory = $runtimeHistory->get()->groupBy('Master_Status')->map(function($item) {
            return $item->sum('Duration');
        });
                                         

        return $runtimeHistory;
    }

    public function stopReport($request) {
        $start = new Carbon($request->startDate);
        $end = new Carbon($request->endDate);
        $runtimeHistory = RuntimeHistory::join('Master_Machine', 'Runtime_History.Master_Machine_ID', '=', 'Master_Machine.ID')
                                        ->join('Master_Status', 'Runtime_History.Master_Status_ID', '=', 'Master_Status.ID')
                                        ->join('Master_Status_Type', 'Master_Status.Master_Status_Type_ID', '=', 'Master_Status_Type.ID')
                                        ->join('Master_Shift', 'Runtime_History.Master_Shift_ID', '=', 'Master_Shift.ID')
                                        ->select(
                                            'Runtime_History.*',
                                            'Master_Machine.Name as Machine_Name',
                                            'Master_Status.Name as Status_Name',
                                            'Master_Status_Type.Name as Status_Type',
                                            'Master_Shift.Name as Shift_Name',
                                            'Master_Shift.Start_Time as Shift_Start',
                                            'Master_Shift.End_Time as Shift_End',
                                        )
                                        ->whereIn('IsRunning', [2, 3]);
     

        if($request->machineId[0] != 0) {
            $runtimeHistory = $runtimeHistory->whereIn('Runtime_History.Master_Machine_ID', $request->machineId);
        }

        $runtimeHistory = $runtimeHistory->where('Runtime_History.Time_Created', '>=', $start->add(8, 'hours'))
                                         ->where('Runtime_History.Time_Updated', '<=', $end->add(1, 'day')->add(8, 'hours')->sub(1, 'second'));

        $runtimeHistory = $runtimeHistory->get();

        $statisticData = $runtimeHistory->groupBy('Machine_Name')->map(function($item, $key) {
            return $item->sum('Duration');
        });

        return [
            'runtimeHistory' => $runtimeHistory,
            'statisticData' => $statisticData,
        ];
    }
}