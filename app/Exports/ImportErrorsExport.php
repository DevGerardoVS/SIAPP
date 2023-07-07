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

            $data = str_replace('There was an error on row', 'Hay un error en la fila: ', $err[0]);

            $ds =array($data);
            $dataSet[] = $ds;
/*             $valuesar=$err->values();
            if($valuesar['total']){
                $ds = array($err->row(), $err->errors(), $err->attribute(),$valuesar['admconac'],$valuesar['ef'],$valuesar['reg'],$valuesar['mpio'],
                $valuesar['loc'],$valuesar['upp'],$valuesar['subsecretaria'],$valuesar['ur'],$valuesar['finalidad'],$valuesar['funcion'],$valuesar['subfuncion'],
                $valuesar['eg'],$valuesar['pt'],$valuesar['ps'],$valuesar['sprconac'],$valuesar['prg'],$valuesar['spr'],$valuesar['py'],
                $valuesar['idpartida'],$valuesar['tipogasto'],$valuesar['ano'],$valuesar['no_etiquetado_y_etiquetado'],$valuesar['fconac'],$valuesar['ramo'],$valuesar['fondo'],
                $valuesar['ci'],$valuesar['obra'],$valuesar['total'],$valuesar['enero'],$valuesar['febrero'],$valuesar['marzo'],$valuesar['abril'],$valuesar['mayo'],
                $valuesar['junio'],$valuesar['julio'],$valuesar['agosto'],$valuesar['septiembre'],$valuesar['octubre'],$valuesar['noviembre'],$valuesar['diciembre']);
                $dataSet[] = $ds;
            } */

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
