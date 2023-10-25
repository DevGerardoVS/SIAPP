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
        
        return ['Lista errores',];
    }

    public function columnWidths(): array {
        return [
            'A' => 200,
 
        ];
    }
}
