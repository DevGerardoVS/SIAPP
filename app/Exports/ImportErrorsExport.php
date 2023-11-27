<?php

namespace App\Exports;

use Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
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

           $stringprocess= explode("$", $err);

            $ds =array($stringprocess[0],$stringprocess[2]);
            $dataSet[] = $ds;


        }

      return collect($dataSet);

    }

        /**
     * Retorna un arreglo con los encabezados del excel en orden de las columnas
     * @return array Encabezados de los usuarios
     */
    public function headings(): array {
        
        return ['Filas en que fallo','Errores'];
    }

    public function columnWidths(): array {
        return [
            'A' => 30,
            'B' => 100,

        ];
    }
}
