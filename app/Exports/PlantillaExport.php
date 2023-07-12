<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PlantillaExport implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths,WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $dataSet = array();

        return collect($dataSet);

    }

    public function headings(): array {
        
        return ['admconac','ef','reg','mpio','loc','upp','subsecretaria','ur','finalidad','funcion','subfuncion','eg','pt','ps','sprconac','prg','spr','py','idpartida','tipogasto','año',
        'no etiquetado y etiquetado','fconac', 'ramo', 'fondo','ci','obra','total', 'enero','febrero','marzo', 'abril', 'mayo','junio','julio','agosto', 'septiembre','octubre', 'noviembre','diciembre', 
      ];
    }

    public function columnWidths(): array {
        return [
 
        ];

        
    }
        public function columnFormats(): array{
        return [
            'A' =>  NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' =>  NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' =>  NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' =>  NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
            'M' =>  NumberFormat::FORMAT_TEXT,
            'N' => NumberFormat::FORMAT_TEXT,
            'O' => NumberFormat::FORMAT_TEXT,
            'P' =>  NumberFormat::FORMAT_TEXT,
            'Q' => NumberFormat::FORMAT_TEXT,
            'R' => NumberFormat::FORMAT_TEXT,
            'S' => NumberFormat::FORMAT_TEXT,
            'T' =>  NumberFormat::FORMAT_TEXT,
            'U' => NumberFormat::FORMAT_TEXT,
            'V' => NumberFormat::FORMAT_TEXT,
            'W' =>  NumberFormat::FORMAT_TEXT,
            'X' => NumberFormat::FORMAT_TEXT,
            'Y' => NumberFormat::FORMAT_TEXT,
            'Z' => NumberFormat::FORMAT_TEXT,
            'AA' => NumberFormat::FORMAT_TEXT,

        ];
    }


}
