<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PlantillaExport implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths
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
}
