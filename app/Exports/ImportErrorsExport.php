<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ImportErrorsExport implements  FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths
{
    protected $array;

    function __construct($array) { 
        $this->array =$array;

    }

    public function collection()
    {
        $dataSet = array();
      $arrayErrores= $this->array;
        foreach ($arrayErrores as $err) {


            $ds =array($err);
            $dataSet[] = $ds;


        }

      return collect($dataSet);

    }

        /**
     * Retorna un arreglo con los encabezados del excel en orden de las columnas
     * @return array Encabezados de los usuarios
     */
    public function headings(): array {
        
        return ['Lista errores',/*,'Error' ,'Error en columna','admconac','ef','reg','mpio','loc','upp','subsecretaria','ur','finalidad','funcion','subfuncion','eg','pt','ps','sprconac','prg','spr','py','idpartida','tipogasto','año',
        'no etiquetado y etiquetado','fconac', 'ramo', 'fondo','ci','obra','total', 'enero','febrero','marzo', 'abril', 'mayo','junio','julio','agosto', 'septiembre','octubre', 'noviembre','diciembre', */
      ];
    }

    public function columnWidths(): array {
        return [
            'A' => 200,
 
        ];
    }
}
