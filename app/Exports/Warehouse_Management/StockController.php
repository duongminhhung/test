<?php

namespace App\Exports\Warehouse_Management;

use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class StockController extends Controller
{

    public function all($data)
    {
        // dd($data->toArray());
        $name = 'Stock_All';
        $file = new Spreadsheet();
        $active_sheet = $file->getActiveSheet();
        $count = 1;
        $active_sheet->setCellValue('A' . $count, 'CD');
        $active_sheet->setCellValue('B' . $count, 'NAME OF MATERIALS');
        $active_sheet->setCellValue('C' . $count, 'KEY');
        $active_sheet->setCellValue('D' . $count, 'DESCRIPTION');
        $active_sheet->setCellValue('E' . $count, 'SIZE');
        $active_sheet->setCellValue('F' . $count, 'COLOR');
        $active_sheet->setCellValue('G' . $count, 'UNIT');
        $active_sheet->setCellValue('H' . $count, 'BARCODE');
        $active_sheet->setCellValue('I' . $count, 'TỔNG TỒN');
        $active_sheet->setCellValue('J' . $count, 'TỒN NVL');
        $active_sheet->setCellValue('K' . $count, 'CHUYỀN GVC');
        $active_sheet->setCellValue('L' . $count, 'CẦN MẪN');
        $active_sheet->setCellValue('M' . $count, 'NEWTOP');
        $active_sheet->setCellValue('N' . $count, 'KL2');
        $active_sheet->setCellValue('O' . $count, 'SƠN HÀ - XUÂN LỘC');
        $number_excel = $count + count($data);

        // write file
        $active_sheet
            ->fromArray(
                $data->toArray(),  // The data to set
                NULL,        // Array values with this value will not be set
                'A2'         // Top left coordinate of the worksheet range where
            );

        // style excel
        $active_sheet->setAutoFilter('A:O');
        $active_sheet->getStyle('I:O')->getNumberFormat()->setFormatCode('@');        
        $active_sheet->getStyle('H')->getNumberFormat()->setFormatCode('#');  
        $styleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rbg' => '000000'],
                ],
            ],
        ];   
        $styleArrayColor = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'c3fefa',
                ],
                
            ],
        ];  
        $active_sheet->getStyle('A1:O'.$number_excel)->applyFromArray($styleArray);
        $active_sheet->getStyle('A1:H'.$number_excel)->applyFromArray($styleArrayColor);
        $active_sheet->getStyle('A1:I1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('ffff00');
        $active_sheet->getStyle('I1:I'.$number_excel)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('ffff00');

        foreach ($active_sheet->getColumnIterator() as $column) {
            $active_sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($file, 'Xlsx');
        $file_name = $name . '.' . strtolower('Xlsx');
        $writer->save($file_name);
        header('Content-Type: application/x-www-form-urlencoded');
        header('Content-Transfer-Encoding: Binary');
        header("Content-disposition: attachment; filename=\"" . $file_name . "\"");
        readfile($file_name);
        unlink($file_name);
        exit;
    }

    public function materials($data)
    {
        // dd($data->toArray());
        $name = 'Stock_Materials';
        $file = new Spreadsheet();
        $active_sheet = $file->getActiveSheet();
        $count = 1;
        $active_sheet->setCellValue('A' . $count, 'CD');
        $active_sheet->setCellValue('B' . $count, 'NAME OF MATERIALS');
        $active_sheet->setCellValue('C' . $count, 'KEY');
        $active_sheet->setCellValue('D' . $count, 'DESCRIPTION');
        $active_sheet->setCellValue('E' . $count, 'SIZE');
        $active_sheet->setCellValue('F' . $count, 'COLOR');
        $active_sheet->setCellValue('G' . $count, 'UNIT');
        $active_sheet->setCellValue('H' . $count, 'BARCODE');
        $active_sheet->setCellValue('I' . $count, 'LOT');
        $active_sheet->setCellValue('J' . $count, 'LOCATION');
        $active_sheet->setCellValue('K' . $count, 'KHO NVL');
        // dd($request);
        $active_sheet
            ->fromArray(
                $data->toArray(),  // The data to set
                NULL,        // Array values with this value will not be set
                'A2'         // Top left coordinate of the worksheet range where
            );
        $active_sheet->getStyle('A:K')->getAlignment()->setHorizontal('left');
        $active_sheet->setAutoFilter('A:K');
        $active_sheet->getStyle('A:K')->getNumberFormat()->setFormatCode('0');
        foreach ($active_sheet->getColumnIterator() as $column) {
            $active_sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($file, 'Xlsx');
        $file_name = $name . '.' . strtolower('Xlsx');
        $writer->save($file_name);
        header('Content-Type: application/x-www-form-urlencoded');
        header('Content-Transfer-Encoding: Binary');
        header("Content-disposition: attachment; filename=\"" . $file_name . "\"");
        readfile($file_name);
        unlink($file_name);
        exit;
    }

    public function product_input($data)
    {
        // dd($data);
        $data = collect($data)->map(function($value){
            return [
                $value->Name_File,
                $value->Manufacturing,
                $value->Materials_Name,
                $value->Materials_Symbols,
                $value->Materials_Type,
                $value->Materials_Size,
                $value->Materials_Color,
                $value->Materials_Unit,
                $value->Product_Input_File_Quantity,
                $value->Product_Input_File_Status,
                $value->Product_Input_File_Message,
                $value->user_name,
                $value->Time_Updated
            ];
        });
        // dd($data->toArray());

        $name = 'Product_Input_File';
        $file = new Spreadsheet();
        $active_sheet = $file->getActiveSheet();
        $count = 1;
        $active_sheet->setCellValue('A' . $count, 'NAME FILE');
        $active_sheet->setCellValue('B' . $count, 'MANUFACTURING');
        $active_sheet->setCellValue('C' . $count, 'NAME OF MATERIAL');
        $active_sheet->setCellValue('D' . $count, 'KEY');
        $active_sheet->setCellValue('E' . $count, 'TYPE MATERIAL');
        $active_sheet->setCellValue('F' . $count, 'SIZE');
        $active_sheet->setCellValue('G' . $count, 'COLOR');
        $active_sheet->setCellValue('H' . $count, 'UNIT');
        $active_sheet->setCellValue('I' . $count, 'QUANTITY');
        $active_sheet->setCellValue('J' . $count, 'STATUS');
        $active_sheet->setCellValue('K' . $count, 'MESSAGE');
        $active_sheet->setCellValue('L' . $count, 'USER UPDATED');
        $active_sheet->setCellValue('M' . $count, 'TIME UPDATED');
        // dd($request);
        $active_sheet
            ->fromArray(
                $data->toArray(),  // The data to set
                NULL,        // Array values with this value will not be set
                'A2'         // Top left coordinate of the worksheet range where
            );
        $active_sheet->getStyle('A:M')->getAlignment()->setHorizontal('left');
        $active_sheet->setAutoFilter('A:M');
        $active_sheet->getStyle('A:K')->getNumberFormat()->setFormatCode('@');
        foreach ($active_sheet->getColumnIterator() as $column) {
            $active_sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
    
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($file, 'Xlsx');
        $file_name = $name . '.' . strtolower('Xlsx');
        $writer->save($file_name);
        header('Content-Type: application/x-www-form-urlencoded');
        header('Content-Transfer-Encoding: Binary');
        header("Content-disposition: attachment; filename=\"" . $file_name . "\"");
        readfile($file_name);
        unlink($file_name);
        exit;
    }

    public function export_all_everyday($data)
    {
        // dd($data->toArray());
        $name = 'Stock_All';
        $file = new Spreadsheet();
        $active_sheet = $file->getActiveSheet();
        $count = 1;
        $active_sheet->setCellValue('A' . $count, 'CD');
        $active_sheet->setCellValue('B' . $count, 'NAME OF MATERIALS');
        $active_sheet->setCellValue('C' . $count, 'KEY');
        $active_sheet->setCellValue('D' . $count, 'DESCRIPTION');
        $active_sheet->setCellValue('E' . $count, 'SIZE');
        $active_sheet->setCellValue('F' . $count, 'COLOR');
        $active_sheet->setCellValue('G' . $count, 'UNIT');
        $active_sheet->setCellValue('H' . $count, 'BARCODE');
        $active_sheet->setCellValue('I' . $count, 'TỔNG TỒN');
        $active_sheet->setCellValue('J' . $count, 'TỒN NVL');
        $active_sheet->setCellValue('K' . $count, 'CHUYỀN GVC');
        $active_sheet->setCellValue('L' . $count, 'CẦN MẪN');
        $active_sheet->setCellValue('M' . $count, 'NEWTOP');
        $active_sheet->setCellValue('N' . $count, 'SƠN HÀ - XUÂN LỘC');
        $active_sheet->setCellValue('O' . $count, 'KL2');
        $number_excel = $count + count($data);

        // write file
        $active_sheet
            ->fromArray(
                $data->toArray(),  // The data to set
                NULL,        // Array values with this value will not be set
                'A2'         // Top left coordinate of the worksheet range where
            );

        // style excel
        $active_sheet->setAutoFilter('A:O');
        $active_sheet->getStyle('I:O')->getNumberFormat()->setFormatCode('@');        
        $active_sheet->getStyle('H')->getNumberFormat()->setFormatCode('#');  
        $styleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rbg' => '000000'],
                ],
            ],
        ];   
        $styleArrayColor = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'c3fefa',
                ],
                
            ],
        ];  
        $active_sheet->getStyle('A1:O'.$number_excel)->applyFromArray($styleArray);
        $active_sheet->getStyle('A1:H'.$number_excel)->applyFromArray($styleArrayColor);
        $active_sheet->getStyle('A1:I1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('ffff00');
        $active_sheet->getStyle('I1:I'.$number_excel)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('ffff00');

        foreach ($active_sheet->getColumnIterator() as $column) {
            $active_sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($file, 'Xlsx');
        $file_name = $name . '.' . strtolower('Xlsx');
        $writer->save($file_name);
        // $writer->save('C:/Users/GF65/Desktop/Rac/' . $file_name);
        $writer->save('C:/Users/GZ-PC169/Desktop/Du lieu hang ngay/ton kho/' . $file_name);

    }
}
