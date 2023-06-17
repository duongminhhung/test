<?php

namespace App\Exports\Warehouse_Management;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ReportMaterials 
{
    public function export($data,$request)
	{
        // dd(collect($data)->toArray());
        $name='Report_Materials';
        $file = new Spreadsheet();
		$active_sheet = $file->getActiveSheet();
        $count = 1;
        $active_sheet->setCellValue('A' . $count, 'Tên');
        $active_sheet->setCellValue('B' . $count, 'Ký Hiệu');
        $active_sheet->setCellValue('C' . $count, 'Tồn Đầu');
        $active_sheet->setCellValue('D' . $count, 'Nhập Mới');
        $active_sheet->setCellValue('E' . $count, 'Nhập Lại');
        $active_sheet->setCellValue('F' . $count, 'Xuất');
        $active_sheet->setCellValue('G' . $count, 'Tồn Cuối');
        $count = 2;
        $dem = 0;
        $active_sheet
            ->fromArray(
                collect($data)->toArray(),  // The data to set
                NULL,        // Array values with this value will not be set
                'A2'         // Top left coordinate of the worksheet range where
            );
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($file, 'Xlsx');
		$file_name = $name . '.' . strtolower('Xlsx');
		$writer->save($file_name);
		header('Content-Type: application/x-www-form-urlencoded');
		header('Content-Transfer-Encoding: Binary');
		header("Content-disposition: attachment; filename=\"".$file_name."\"");
		readfile($file_name);
		unlink($file_name);
		exit;
	}

    public function export_everyday($data,$request)
	{
        $name='Report_Materials';
        $file = new Spreadsheet();
		$active_sheet = $file->getActiveSheet();
        $count = 1;
        $active_sheet->setCellValue('A' . $count, 'Tên');
        $active_sheet->setCellValue('B' . $count, 'Ký Hiệu');
        $active_sheet->setCellValue('C' . $count, 'Tồn Đầu');
        $active_sheet->setCellValue('D' . $count, 'Nhập Mới');
        $active_sheet->setCellValue('E' . $count, 'Nhập Lại');
        $active_sheet->setCellValue('F' . $count, 'Xuất');
        $active_sheet->setCellValue('G' . $count, 'Tồn Cuối');
        $count = 2;
        $dem = 0;
        // dd($data);
		$active_sheet
            ->fromArray(
                collect($data)->toArray(),  // The data to set
                NULL,        // Array values with this value will not be set
                'A2'         // Top left coordinate of the worksheet range where
            );
        
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($file, 'Xlsx');
        $file_name = $name . '.' . strtolower('Xlsx');
        $writer->save($file_name);
        // $writer->save('C:/Users/GF65/Desktop/Rac/' . $file_name);
        // $writer->save('C:\Users\GF65\Desktop\Rac' . $file_name);
        $writer->save('C:/Users/GZ-PC169/Desktop/Du lieu hang ngay/nhap xuat ton/' . $file_name);

	}
    
    
}
