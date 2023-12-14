<?php

namespace App\Exports\ArchivosCarga;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use App\Helpers\ArchivosCargaHelper;
use App\Http\Controllers\Calendarizacion\MetasController;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Auth;


class ArchivosCarga extends \PhpOffice\PhpSpreadsheet\Cell\StringValueBinder implements FromCollection, ShouldAutoSize, WithColumnWidths, WithHeadings, WithCustomValueBinder
{
    protected $id;
    // protected $upp;
    // protected $anio;

    function __construct($id)
    {
        $this->id = $id;
        
    }
    public function collection()
    {
        switch ($this->id) {
            case 1:
                $dataSet = ArchivosCargaHelper::getDataAreasFuncionales();
                break;
            case 2:
                $dataSet = ArchivosCargaHelper::getDataFondos();
                break;
            case 3:
                $dataSet = ArchivosCargaHelper::getDataCostoBeneficio();
                break;
            case 4:
                $dataSet = ArchivosCargaHelper::getDataCentroGestor();
                break;
            case 5:
                $dataSet = ArchivosCargaHelper::getDataPospre();
                break;
            case 6:
                $dataSet = ArchivosCargaHelper::getDataClavesPresupuestales();
                break;
            
            default:
                $dataSet = ['error'=>'sin datos'];
                break;
        }

        return collect($dataSet);
    }
    /**
     * 
     * 
     * Retorna un arreglo con los encabezados del excel en orden de las columnas
     * @return array Encabezados de los usuarios
     */
    public function headings(): array
    {
        switch ($this->id) {
            case 1:
                return [];
                break;
            case 2:
                return ["AÑO", "FONDO","DESCRIP CORTA","DESCRIP LARGA"];
                break;
            case 3:
                return ["Entidad Federativa", "Region","Municipio","Localidad","Secretaria","Sub Secretaria","Direccion","Codigo Centro de Costos/Beneficio","Codigo CeGe","Descripción UR para DEPPs","Descripción (Municipio - Localidad - Dirección)","Descripcion Explicativa (Municipio - Dirección)","Dscripcion Breve (Dirección)"];
                break;
            case 4:
                return [];
                break;
            case 5:
                return [];
                break;
            case 6:
                return ["Fila", "Centro Gestor","PosPre","Fondo","Area Funcional","Proyecto Presupuestal","ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO",
                "AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE"
                ];
                break;
            default:
                return [];        
                break;
        }
        
    }

    public function columnWidths(): array
    {
        switch ($this->id) {
            case 1:
                return [ 'A' => 15,'B' => 30,'C' => 50,];
                break;
            case 2:
                return ['A' => 7,'B' => 12,'C' => 50,'D' => 80,];
                break;
            case 3:
                return ['A' => 8,'B' => 8,'C' => 8,'D' => 8,'E' => 8,'F' => 8,'G' => 8,'H' => 20,'I' => 30,'J' => 50,'K' => 50,'L' => 50,'M' => 50,];
                break;
            case 4:
                return ['A' => 7,'B' => 18,'C' => 60,];
                break;
            case 5:
                return ['A' => 7,'B' => 18,'C' => 60,];
                break;
            case 6:
                return ['A' => 8,'B' => 20,'C' => 15,'D' => 15,'E' => 30,'F' => 25,'G' => 20,'H' => 20,'I' => 20,'J' => 20,'K' => 20,'L' => 20,'M' => 20,'N' => 20,'O' => 20,'P' => 20,'Q' => 20,'R' => 20,];
                break;
            default:
                return [];        
                break;
        }
      
    }
    public function registerEvents():array{
        return[
            AfterSheet::class=> function(AfterSheet $event){
                $sheet = $event -> sheet;
                $event->sheet->getDelegate()
                ->getStyle('A1:S'.$this->filas)
                ->applyFromArray(['alignment'=>['wrapText'=>true]]);

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000'],
                        ],
                    ],
                ];
    
                $event->sheet->getStyle('A1:S'.$this->filas)->applyFromArray($styleArray);
               },
             
        ];
    }
}
