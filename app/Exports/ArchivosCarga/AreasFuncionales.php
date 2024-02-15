<?php

namespace App\Exports\ArchivosCarga;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use App\Helpers\Calendarizacion\MetasHelper;
use App\Http\Controllers\Calendarizacion\MetasController;
use Illuminate\Support\Facades\Log;
use Auth;


class AreasFuncionales implements FromCollection, ShouldAutoSize, WithColumnWidths
{
    // protected $filas;
    // protected $upp;
    // protected $anio;

    // function __construct($upp, $anio)
    // {
    //     $this->upp = $upp;
    //     $this->anio = $anio;
    // }
    public function collection()
    {
        $areasFun = [];
        $areas = DB::table('epp')
        ->SELECT('epp.ejercicio',
        (DB::raw('CONCAT(c09.clave,c10.clave,c11.clave,c12.clave,c13.clave,c14.clave,c15.clave,c16.clave,c17.clave,c18.clave) area_funcional')),
        (DB::raw("CONCAT((epp.ejercicio-2000),c06.clave,' ',c18.descripcion) col_3"))
        )
        ->leftJoin('catalogo as c06', 'epp.upp_id', '=', 'c06.id')  
        ->leftJoin('catalogo as c09', 'epp.finalidad_id', '=', 'c09.id')  
        ->leftJoin('catalogo as c10', 'epp.funcion_id', '=', 'c10.id')  
        ->leftJoin('catalogo as c11', 'epp.subfuncion_id', '=', 'c11.id') 
        ->leftJoin('catalogo as c12', 'epp.eje_id', '=', 'c12.id')  
        ->leftJoin('catalogo as c13', 'epp.linea_accion_id', '=', 'c13.id')  
        ->leftJoin('catalogo as c14', 'epp.programa_sectorial_id', '=', 'c14.id')  
        ->leftJoin('catalogo as c15', 'epp.tipologia_conac_id', '=', 'c15.id')  
        ->leftJoin('catalogo as c16', 'epp.programa_id', '=', 'c16.id')  
        ->leftJoin('catalogo as c17', 'epp.subprograma_id', '=', 'c17.id')  
        ->leftJoin('catalogo as c18', 'epp.proyecto_id', '=', 'c18.id') 
        ->where('epp.ejercicio',2024)
        ->orderByRaw('epp.upp_id,epp.ur_id')
        ->get();

        foreach ($areas as $key => $value) {
            $cadena = $value->col_3;
            $rest = substr($cadena,0, 25);
            $value->col_3 = $rest;
            array_push($areasFun, ['ejercicio'=>$value->ejercicio,
                                    'area_funcional'=>$value->area_funcional,
                                    'col_3'=>$rest]);

        }
        return collect($areasFun);
    }
    /**
     * 
     * 
     * Retorna un arreglo con los encabezados del excel en orden de las columnas
     * @return array Encabezados de los usuarios
     */
    // public function headings(): array
    // {
    //     return ["Año", "Área funcional","FUNCION","SUBFUNCION","EJE","L ACCION","PRG SECTORIAL","TIPO CONAC","UPP","UR", "Programa", "Subprograma", "proyecto",'Fondo', "Actividad","Tipo Actividad","Meta anual", "# Beneficiarios", "beneficiarios","U de medida"];
    // }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 30,
            'C' => 50,
        ];
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
