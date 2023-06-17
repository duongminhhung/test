<?php

namespace App\Exports\Warehouse_Management;

use PhpOffice\PhpSpreadsheet\IOFactory;

class MaterialSupplyCertificate
{
    public function export($data,$request)
    {
        $name = 'Phiếu YC';
        $fileType = IOFactory::identify(public_path('template\excels\phieu_yeu_cau.xlsx'));
        //Load data
        $loadFile = IOFactory::createReader($fileType);
        $file = $loadFile->load(public_path('template\excels\phieu_yeu_cau.xlsx'));
		$active_sheet = $file->getActiveSheet();
        $count = 6;
        $number_excel = count($data);

        // insert info file yc
        $active_sheet->setCellValue('B2',$data[0]->Order);
        $active_sheet->setCellValue('B3',$data[0]->product->Hinban);
        $active_sheet->setCellValue('B4',$data->sum('Quantity'));

        if($number_excel > 1 ){
            $active_sheet->insertNewRowBefore(7, ($number_excel - 1));
        }

        // insert data detail
        foreach ($data as $value) {
            $location = implode('|',collect($value->Location)->pluck('Name')->toArray());
            $active_sheet->setCellValue('A' . $count, $value->materials->Name ?? '');
            $active_sheet->setCellValue('B' . $count, $value->materials ? $value->materials->Symbols : '');
            $active_sheet->setCellValue('C' . $count, $value->Quantity);
            $active_sheet->setCellValue('D' . $count, $location);
            $active_sheet->setCellValue('E' . $count, $value->materials ? $value->materials->Unit : '');
            $count = $count + 1;
        }
        $row_number = $count + 3;
        $active_sheet->setCellValue('B' . $row_number, $request->Type);
        $active_sheet->getStyle('B2')->getNumberFormat()->setFormatCode('#'); 


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
}
