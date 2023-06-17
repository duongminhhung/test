<?php

namespace App\Exports\MasterData;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MasterProduct
{
    public function export($data,$request)
    {
        // dd($data->toArray());
        $name = 'Master_Product';
        $fileType = IOFactory::identify(public_path('template\excels\template_master_product.xlsx'));
        //Load data
        $loadFile = IOFactory::createReader($fileType);
        $file = $loadFile->load(public_path('template\excels\template_master_product.xlsx'));
		$active_sheet = $file->getActiveSheet();
        $count = 1;
        $number_excel = $count + count($data);
        
        // insert data
        $active_sheet
            ->fromArray(
                $data->toArray(),  // The data to set
                0,        // Array values with this value will not be set
                'A2'         // Top left coordinate of the worksheet range where
            );

        // style 
        $active_sheet->getStyle('A:Ww')->getAlignment()->setHorizontal('left');
        $active_sheet->getStyle('J')->getNumberFormat()->setFormatCode('#'); 
        $active_sheet->getStyle('K')->getNumberFormat()->setFormatCode('@'); 
        $active_sheet->setAutoFilter('A:W');
            
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rbg' => '000000'],
                ],
            ],
        ];
        $styleArrayColor= [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'c3fefa',
                ],
                
            ],
        ];
        
        $active_sheet->getStyle('A2:L'.$number_excel)->applyFromArray($styleArrayColor);
        $active_sheet->getStyle('A1:W'.$number_excel)->applyFromArray($styleArray);
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
}
