<?php

namespace App\Exports\OrderManagement;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use App\Models\MasterData\MasterLocation;
use App\Models\MasterData\MasterMaterials;
use App\Models\MasterData\MasterProduct;
use App\Models\MasterData\MasterManufacturing;
use App\Models\MasterData\MasterBOM ;
use App\Models\WarehouseManagement\CommandExport;
use App\Models\WarehouseManagement\ExportDetail;
use Illuminate\Http\Request;
use Auth;
use App\Models\WarehouseManagement\ImportDetail;
use DB;
use App\Http\Controllers\Api\ApiSystemController;
use App\Models\WarehouseManagement\ProductInputFile;
use App\Models\OrderManagement\ScheduleImportFile;
use App\Models\OrderManagement\FollowOrder;

class OrderExport
{
    public function export()
    {
        // dd($data);
        set_time_limit(6000000);

        $list_mater_in_import =ScheduleImportFile::where('IsDelete',0)
        ->get('Materials_ID')
        ->unique('Materials_ID')
        ->pluck('Materials_ID')
        ->toArray();
        $array_maters_chunk = array_chunk($list_mater_in_import,count($list_mater_in_import));
        $list_location_in_stock = MasterLocation::where('IsDelete',0)->where('Manufacturing_ID',0)->get();
        $list_manufacturing     = MasterManufacturing::where('IsDelete',0)->get();
        $array_location_in_stock = [];    
        foreach($list_location_in_stock as $value)
        {
            array_push($array_location_in_stock,$value->ID);
        }
        $array_locatin_with_manu = [];
        foreach($list_manufacturing as $manufacturing)
        {
            $list_location_in_manu = MasterLocation::where('IsDelete',0)->where('Manufacturing_ID',$manufacturing->ID)->get();
            $array_location_in_manu = [];
            foreach($list_location_in_manu as $value)
            {
                array_push($array_location_in_manu,$value->ID);
            }
            $array_locatin_with_manu["manu-".$manufacturing->ID] = $array_location_in_manu;
        }
        $list_materials = [];
        if(count($array_maters_chunk)  > 0 )
        {
            foreach($array_maters_chunk[0] as $val )
            {
                $material = MasterMaterials::where('IsDelete',0)->where('ID',$val)->with(['user_created','user_updated'])->first();
                if($material)
                {
                    $sum3 = 0;
                    $sum1 = ImportDetail::where('IsDelete',0)
                    ->where('Materials_ID',$material->ID)
                    ->whereIn('Location',$array_location_in_stock)
                    ->sum('Inventory');
                    $material['stock'] = floatval($sum1); 
                    $sum3 += $sum1; 
                    foreach( $array_locatin_with_manu as $key => $value)
                    { 
                        $sum2 = ImportDetail::where('IsDelete',0)
                        ->where('Materials_ID',$material->ID)
                        ->whereIn('Location',$value)
                        ->sum('Inventory');
                        $material["".$key] = floatval($sum2);
                        $sum3 += $sum2;
                    }
                    $start = 0;
                    $start_day = 0;
                    $sum_stock = $sum3;
                    // dd($sum_stock);
                    for($i = 0 ; $i <= 5 ;$i++)
                    {
                        $day_now = Carbon::now()->addMonths($i)->endOfMonth()->day;
                        $month_now = Carbon::now()->addMonths($i)->endOfMonth()->month;
                        $year_now = Carbon::now()->addMonths($i)->endOfMonth()->year;
                        $text_year =  substr($year_now,'-2',2);
                        $text_date = $text_year.''.($month_now < 10 ? '0'.$month_now : $month_now);
                        $end = intval($text_date);
                        $date_end = $year_now.'-'.$month_now.'-'.$day_now;
                        $sum = ScheduleImportFile::where('IsDelete',0)->where('Materials_ID',$material->ID)->where('Date_PO','>',$start)->where('Date_PO','<=',$end)->sum('DIF');
                        $start = $end;
                        $material['order-'.$i] = floatval(round($sum,2));
                        if($start_day == 0 )
                        {
                            $sum = FollowOrder::where('IsDelete',0)->where('Materials_ID',$material->ID)->where('Status',0)->where('Date_Order','<=',Carbon::create($date_end))->sum('DIF');
                            $sum_inp = FollowOrder::where('IsDelete',0)->where('Materials_ID',$material->ID)->where('Status',0)->where('Date_Order','<=',Carbon::create($date_end))->sum('Quantity_Input');
                        }
                        else
                        {
                            $sum = FollowOrder::where('IsDelete',0)->where('Materials_ID',$material->ID)->where('Status',0)->where('Date_Order','>', Carbon::create($start_day))->where('Date_Order','<=',Carbon::create($date_end))->sum('DIF');
                            $sum_inp = FollowOrder::where('IsDelete',0)->where('Materials_ID',$material->ID)->where('Status',0)->where('Date_Order','>', Carbon::create($start_day))->where('Date_Order','<=',Carbon::create($date_end))->sum('Quantity_Input');
                        }
                        $start_day = $date_end; 
                        $material['po-'.$i] = floatval(round($sum,2) - round($sum_inp,2));
                        $material['dif-'.$i] = round( $sum_stock - $material['order-'.$i] + $material['po-'.$i] , 2);
                        $sum_stock =  $material['dif-'.$i];
                    }
                    array_push($list_materials,$material);
                }
            }
        }
        // dd($list_materials);
        $this->export_file($list_materials);
        return response()->json([
            'data'=>true
        ]); 
    }
    public function export_file($data)
    {
        // dd($data);
        $manufacturing  = MasterManufacturing::where('IsDelete', 0)->get(['ID', 'Name', 'Symbols']);
        $name = 'Order_Managemet';
        $fileType = IOFactory::identify(public_path('template\excels\order_management.xlsx'));
        //Load data
        $loadFile = IOFactory::createReader($fileType);
        $file = $loadFile->load(public_path('template\excels\order_management.xlsx'));
		$active_sheet = $file->getActiveSheet();
        $row = 'Q';
        for($i = 0 ; $i <= 5 ;$i++)
        {
           
            $month_now = Carbon::now()->addMonths($i)->endOfMonth()->month;
            $active_sheet->setCellValue($row.'2', 'Tháng '.$month_now); 
            $row ++;
            $row ++;
            $row ++;
        }

        $count = 3;
        foreach($data as $key => $value)
        {
            $count++;
            $active_sheet->setCellValue('A' . $count, $value->CD);
            $active_sheet->setCellValue('B' . $count, $value->Name);
            $active_sheet->setCellValue('C' . $count, $value->Symbols);
            $active_sheet->setCellValue('D' . $count, $value->Size);
            $active_sheet->setCellValue('E' . $count, $value->Key);
            $active_sheet->setCellValue('F' . $count, $value->Color);
            $active_sheet->setCellValue('G' . $count, $value->Unit);
            $active_sheet->setCellValue('H' . $count, $value->Maker);
            $quan_sum  = 0;
            foreach($manufacturing as $manu)
            {
                $quan_sum = $quan_sum + $value['manu-'.$manu->ID];
            }
            $active_sheet->setCellValue('I' . $count,  $quan_sum);
            $active_sheet->setCellValue('J' . $count, $value->stock);
            $row1 = 'K';
            foreach($manufacturing as $manu)
            {
                $active_sheet->setCellValue($row1.'' . $count,  $value['manu-'.$manu->ID]);
                $row1++;
            }
            $row2 = 'Q';
            for($i = 0 ;$i <= 5 ; $i ++ ) 
            {  
                $active_sheet->setCellValue($row2.''.$count, $value['order-'.$i]); 
                $row2++;
                $active_sheet->setCellValue($row2.''.$count, $value['po-'.$i]); 
                $row2++;
                $active_sheet->setCellValue($row2.''.$count, $value['dif-'.$i]); 
                $row2++;
            }
        }
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($file, 'Xlsx');
        $file_name = $name . '.' . strtolower('Xlsx');
        $writer->save($file_name);
        // $writer->save('C:/Users/STI/Desktop/Rac/' . $file_name);
        $writer->save('C:/Users/GZ-PC169/Desktop/Du lieu hang ngay/ke hoach dat hang/' . $file_name);

    }
}
