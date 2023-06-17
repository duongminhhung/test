<?php

namespace App\Exports\Warehouse_Management;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExportMaterials
{
    public function export($data)
    {
        // dd($data);
        $name = 'Phieu Xuat';
        $fileType = IOFactory::identify(public_path('template\excels\phieu_xuat.xlsx'));
        //Load data
        $loadFile = IOFactory::createReader($fileType);
        $file = $loadFile->load(public_path('template\excels\phieu_xuat.xlsx'));
		$active_sheet = $file->getActiveSheet();
        $count = 18;
        $number_excel = count($data->detail_export);

        $dem = 0;
        // insert info file import
        $active_sheet->setCellValue('D6',$data->Time_Created);
        $active_sheet->setCellValue('C11',$data->user_created ? $data->user_created->name : '');
        $active_sheet->setCellValue('C12',$data->Name);
        $active_sheet->setCellValue('C13',$data->From = 1 ? 'Kho NVL' : 'Chuyền GVC');
        $active_sheet->setCellValue('E12',$data->Time_Created);
        if($number_excel > 1 ){
            $active_sheet->insertNewRowBefore(18, $number_excel - 1);
        }
        // insert data detail
        foreach ($data->detail_export as $value) {
            $dem++;
            $active_sheet->setCellValue('A' . $count, $dem);
            $active_sheet->setCellValue('B' . $count, $value->materials ? $value->materials->Name : '');
            $active_sheet->setCellValue('C' . $count, $value->materials ? $value->materials->Symbols : '');
            $active_sheet->setCellValue('D' . $count, $value->materials ? $value->materials->Unit : '');
            $active_sheet->setCellValue('E' . $count, floatval($value->Quantity));
            $active_sheet->setCellValue('F' . $count, floatval($value->Quantity));
            $count = $count + 1;
        }
        $row_number = $number_excel = 1 ? $count : $count - 1;
        $active_sheet->setCellValue('E'.$count , '=SUM(E18:E'.$row_number.')');
        $active_sheet->setCellValue('F'.$count , '=SUM(F18:F'.$row_number.')');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($file, 'Xlsx');
        $file_name = $name . '.' . strtolower('Xlsx');
        $writer->save($file_name);
        // $writer->save('C:/Users/STI/Desktop/Rac/' . $file_name);
        header('Content-Type: application/x-www-form-urlencoded');
        header('Content-Transfer-Encoding: Binary');
        header("Content-disposition: attachment; filename=\"" . $file_name . "\"");
        readfile($file_name);
        unlink($file_name);
        exit;
    }
}
