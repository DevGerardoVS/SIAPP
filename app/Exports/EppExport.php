<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Illuminate\Support\Facades\Auth;
use Log;

class EppExport implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths
{
    protected $anio;

    function __construct($anio){
        $this->anio = $anio;
    }

    public function collection()
    {
        $perfil = Auth::user()->id_grupo;
        $epp = '';

        if($perfil == 5) {
            $epp = DB::table('uppautorizadascpnomina as u')
            ->leftjoin('v_epp as ve', 'u.clv_upp', '=', 've.clv_upp')
            ->select(DB::raw("
                CONCAT(
                    ve.clv_sector_publico,
                    ve.clv_sector_publico_f,
                    ve.clv_sector_economia,
                    ve.clv_subsector_economia,
                    ve.clv_ente_publico
                ) AS clas_admin,
                ve.clv_upp,
                ve.upp,
                ve.clv_subsecretaria,
                ve.subsecretaria,
                ve.clv_ur,
                ve.ur,
                ve.clv_finalidad,
                ve.finalidad,
                ve.clv_funcion,
                ve.funcion,
                ve.clv_subfuncion,
                ve.subfuncion,
                ve.clv_eje,
                ve.eje,
                ve.clv_linea_accion,
                ve.linea_accion,
                ve.clv_programa_sectorial,
                ve.programa_sectorial,
                ve.clv_tipologia_conac,
                ve.tipologia_conac,
                ve.clv_programa,
                ve.programa,
                ve.clv_subprograma,
                ve.subprograma,
                ve.clv_proyecto,
                ve.proyecto
            "))
            ->where('ejercicio', $this->anio)
            ->where('ve.deleted_at')->orderBy('u.clv_upp')->get();
        }
        else if($perfil == 4) {
            $epp = DB::table('v_epp as ve')
            ->select(DB::raw("
                CONCAT(
                    ve.clv_sector_publico,
                    ve.clv_sector_publico_f,
                    ve.clv_sector_economia,
                    ve.clv_subsector_economia,
                    ve.clv_ente_publico
                ) AS clas_admin,
                ve.clv_upp,
                ve.upp,
                ve.clv_subsecretaria,
                ve.subsecretaria,
                ve.clv_ur,
                ve.ur,
                ve.clv_finalidad,
                ve.finalidad,
                ve.clv_funcion,
                ve.funcion,
                ve.clv_subfuncion,
                ve.subfuncion,
                ve.clv_eje,
                ve.eje,
                ve.clv_linea_accion,
                ve.linea_accion,
                ve.clv_programa_sectorial,
                ve.programa_sectorial,
                ve.clv_tipologia_conac,
                ve.tipologia_conac,
                ve.clv_programa,
                ve.programa,
                ve.clv_subprograma,
                ve.subprograma,
                ve.clv_proyecto,
                ve.proyecto
            "))
            ->where('ejercicio', $this->anio)
            ->where('clv_upp',Auth::user()->clv_upp)
            ->get();
        }
        else {
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
        }
        
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
