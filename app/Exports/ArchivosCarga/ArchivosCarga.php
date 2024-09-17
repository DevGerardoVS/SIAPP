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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use Auth;


class ArchivosCarga extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithColumnWidths, WithCustomValueBinder,WithHeadings
{
    protected $id;
    // protected $upp;
    protected $ejercicio;

    function __construct($id,$ejercicio)
    {
        $this->id = $id;
        $this->ejercicio = $ejercicio;

        
    }
    public function collection()
    {
        ini_set('max_execution_time', 5000);
        switch ($this->id) {
            case 1:
                $dataSet = ArchivosCargaHelper::getDataAreasFuncionales($this->ejercicio);
                break;
            case 2:
                $dataSet = ArchivosCargaHelper::getDataFondos($this->ejercicio);
                break;
            case 3:
                $dataSet = ArchivosCargaHelper::getDataCostoBeneficio($this->ejercicio);
                break;
            case 4:
                $dataSet = ArchivosCargaHelper::getDataCentroGestor($this->ejercicio);
                break;
            case 5:
                $dataSet = ArchivosCargaHelper::getDataPospre($this->ejercicio);
                break;
            case 6:
                $dataSet = ArchivosCargaHelper::getDataClavesPresupuestales($this->ejercicio);
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
   
    public function bindValue(Cell $cell, $value)
    {
        $stringColumn = [];
        switch ($this->id) {
                case 1:
                    $stringColumn = ['A','B','C'];
                    break;
                case 2:
                    $stringColumn = ['A','B','C','D'];
                    break;
                case 3:
                    $stringColumn = ['A','B','C','D','E','F','G','H','I','J','K','L','M'];
                    break;
                case 4:
                    $stringColumn = ['A','B','C'];
                    break;
                case 5:
                    $stringColumn = ['A','B','C'];
                    break;
                case 6:
                    $stringColumn = ['B','C','D','E','F'];
                    break;
                
                default:
                    $stringColumn = [];
                    break;
            }
        if (in_array($cell->getColumn(), $stringColumn)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }
        if (!in_array($cell->getColumn(), $stringColumn) && is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);

            return true;
        }
       

        // else return default behavior
        return parent::bindValue($cell, $value);
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
