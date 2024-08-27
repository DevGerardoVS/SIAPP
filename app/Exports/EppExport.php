<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\Auth;
use Log;

class EppExport extends \PhpOffice\PhpSpreadsheet\Cell\StringValueBinder
implements FromCollection, WithHeadings, WithColumnWidths, WithColumnFormatting, WithCustomValueBinder
{
    protected $anio;

    function __construct($request){
        $this->anio = $request->anio;
        $this->upp = $request->upp;
        $this->ur = $request->ur;
    }

    public function collection()
    {
        $perfil = Auth::user()->id_grupo;
        $epp = '';

            $cond_upp = '=';
            $cond_ur = '=';
            $upp = $this->upp;
            $ur = $this->ur;
            if($perfil == 4) $upp = Auth::user()->clv_upp;
            else if($upp == '000') $cond_upp = '!=';
            if($ur == '00') $cond_ur = '!=';

            $epp = DB::table('v_epp as ve')
            ->select(DB::raw("
                concat(
                    clv_sector_publico,
                    clv_sector_publico_f,
                    clv_sector_economia,
                    clv_subsector_economia,
                    clv_ente_publico
                ) as clas_admin,
                clv_upp,upp,
                clv_subsecretaria,subsecretaria,
                clv_ur,ur,
                clv_finalidad,finalidad,
                clv_funcion,funcion,
                clv_subfuncion,subfuncion,
                clv_eje,eje,
                clv_linea_accion,linea_accion,
                clv_programa_sectorial,programa_sectorial,
                clv_tipologia_conac,tipologia_conac,
                clv_programa,programa,
                clv_subprograma,subprograma,
                clv_proyecto,proyecto
            "))
            ->where('ejercicio', $this->anio)
            ->where('clv_upp', $cond_upp, $upp)
            ->where('clv_ur', $cond_ur, $ur)
            ->whereNull('deleted_at')
            ->orderBy(DB::raw('
                clv_upp,clv_subsecretaria,clv_ur,clv_finalidad,clv_funcion,clv_subfuncion,
                clv_eje,clv_linea_accion,clv_programa_sectorial,clv_tipologia_conac,
                clv_programa,clv_subprograma,clv_proyecto
            '));

            if($perfil == 5){
                $epp->join('uppautorizadascpnomina as u', function($join){
                    $join->on('ve.clv_upp', '=', 'u.clv_upp');
                    $join->whereNull('u.deleted_at');
                });
            }
        
        return $epp->get();
    }

    public function columnFormats(): array
    {
        return[
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
            'M' => NumberFormat::FORMAT_TEXT,
            'N' => NumberFormat::FORMAT_TEXT,
            'O' => NumberFormat::FORMAT_TEXT,
            'P' => NumberFormat::FORMAT_TEXT,
            'Q' => NumberFormat::FORMAT_TEXT,
            'R' => NumberFormat::FORMAT_TEXT,
            'S' => NumberFormat::FORMAT_TEXT,
            'T' => NumberFormat::FORMAT_TEXT,
            'U' => NumberFormat::FORMAT_TEXT,
            'V' => NumberFormat::FORMAT_TEXT,
            'W' => NumberFormat::FORMAT_TEXT,
            'X' => NumberFormat::FORMAT_TEXT,
            'Y' => NumberFormat::FORMAT_TEXT,
            'Z' => NumberFormat::FORMAT_TEXT,
            'AA' => NumberFormat::FORMAT_TEXT
        ];
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
