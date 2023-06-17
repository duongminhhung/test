<?php

namespace App\Exports\MasterData;


use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class MasterMaterials
{
    public function export($data)
    {
        // dd($data->toArray());
        $name = 'Master_Materials';
        $fileType = IOFactory::identify(public_path('template\excels\template_master_materials.xlsx'));
        //Load data
        $loadFile = IOFactory::createReader($fileType);
        $file = $loadFile->load(public_path('template\excels\template_master_materials.xlsx'));
		$active_sheet = $file->getActiveSheet();
        $count = 1;
        $number_excel = $count + count($data);
        $dem = 0;
        $active_sheet
            ->fromArray(
                $data->toArray(),  // The data to set
                NULL,        // Array values with this value will not be set
                'A2'         // Top left coordinate of the worksheet range where
                            //    we want to set these values (default is A1)
            );
        
        // style
        $active_sheet->getStyle('A:K')->getAlignment()->setHorizontal('left');
        $active_sheet->setAutoFilter('A:K');
        $active_sheet->getStyle('G')->getNumberFormat()->setFormatCode('#');        
        $active_sheet->getStyle('I')->getNumberFormat()->setFormatCode('@');     

        $styleArray = [
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
        $active_sheet->getStyle('A1:H'.$number_excel)->applyFromArray($styleArrayColor);
        $active_sheet->getStyle('A1:K'.$number_excel)->applyFromArray($styleArray);
        $active_sheet->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('ffff00');

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
