<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Log;

class EppExport implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths
{
    protected $anio;

    function __construct($anio){
        $this->anio = $anio;
    }

    public function collection()
    {
        $epp = DB::table('v_epp')
            ->select(DB::raw("
                CONCAT(
                    clv_sector_publico,
                    clv_sector_publico_f,
                    clv_sector_economia,
                    clv_subsector_economia,
                    clv_ente_publico
                ) AS clas_admin,
                clv_upp,
                upp,
                clv_subsecretaria,
                subsecretaria,
                clv_ur,
                ur,
                clv_finalidad,
                finalidad,
                clv_funcion,
                funcion,
                clv_subfuncion,
                subfuncion,
                clv_eje,
                eje,
                clv_linea_accion,
                linea_accion,
                clv_programa_sectorial,
                programa_sectorial,
                clv_tipologia_conac,
                tipologia_conac,
                clv_programa,
                programa,
                clv_subprograma,
                subprograma,
                clv_proyecto,
                proyecto
            "))
            ->where('ejercicio', $this->anio)
            ->get();
        return $epp;
    }

    /**
     * Retorna un arreglo con los encabezados del excel en orden de las columnas
     * @return array Encabezados de los usuarios
     */
    public function headings(): array
    {
        return ["Clasificación Administrativa","clv upp","upp", "clv subsecretaría", "subsecretaría", "clv ur", "ur", 
        "clv finalidad", "finalidad", "clv funcion", "funcion", "clv subfuncion", "subfuncion", "clv eje", "eje",
        "clv linea accion", "linea accion", "clv programa sectorial", "programa sectorial", "clv tipologia conac", "tipologia conac",
        "clv programa", "programa", "clv subprograma", "subprograma", "clv proyecto", "proyecto"];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 15,
            'C' => 30,
            'D' => 15,
            'E' => 30,
            'F' => 15,
            'G' => 30,
            'H' => 15,
            'I' => 30,
            'J' => 15,
            'K' => 30,
            'L' => 15,
            'M' => 30,
            'N' => 15,
            'O' => 30,
            'P' => 15,
            'Q' => 30,
            'R' => 15,
            'S' => 30,
            'T' => 15,
            'U' => 30,
            'V' => 15,
            'W' => 30,
            'X' => 15,
            'Y' => 30,
            'Z' => 15,
            'AA' => 30
        ];
    }
}
