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

            $epp = DB::table('epp as e')
            ->join('catalogo as c01', 'e.sector_publico_id', '=', 'c01.id')
            ->join('catalogo as c02', 'e.sector_publico_f_id', '=', 'c02.id')
            ->join('catalogo as c03', 'e.sector_economia_id', '=', 'c03.id')
            ->join('catalogo as c04', 'e.subsector_economia_id', '=', 'c04.id')
            ->join('catalogo as c05', 'e.ente_publico_id', '=', 'c05.id')
            ->join('catalogo as c06', 'e.upp_id', '=', 'c06.id')
            ->join('catalogo as c07', 'e.subsecretaria_id', '=', 'c07.id')
            ->join('catalogo as c08', 'e.ur_id', '=', 'c08.id')
            ->join('catalogo as c09', 'e.finalidad_id', '=', 'c09.id')
            ->join('catalogo as c10', 'e.funcion_id', '=', 'c10.id')
            ->join('catalogo as c11', 'e.subfuncion_id', '=', 'c11.id')
            ->join('catalogo as c12', 'e.eje_id', '=', 'c12.id')
            ->join('catalogo as c13', 'e.linea_accion_id', '=', 'c13.id')
            ->join('catalogo as c14', 'e.programa_sectorial_id', '=', 'c14.id')
            ->join('catalogo as c15', 'e.tipologia_conac_id', '=', 'c15.id')
            ->join('catalogo as c16', 'e.programa_id', '=', 'c16.id')
            ->join('catalogo as c17', 'e.subprograma_id', '=', 'c17.id')
            ->join('catalogo as c18', 'e.proyecto_id', '=', 'c18.id')
            ->select(DB::raw("
                CONCAT(
                    c01.clave,
                    c02.clave,
                    c03.clave,
                    c04.clave,
                    c05.clave
                ) AS clas_admin,
                c06.clave clv_upp,
                c06.descripcion upp,
                c07.clave clv_subsecretaria,
                c07.descripcion subsecretaria,
                c08.clave clv_ur,
                c08.descripcion ur,
                c09.clave clv_finalidad,
                c09.descripcion finalidad,
                c10.clave clv_funcion,
                c10.descripcion funcion,
                c11.clave clv_subfuncion,
                c11.descripcion subfuncion,
                c12.clave clv_eje,
                c12.descripcion eje,
                c13.clave clv_linea_accion,
                c13.descripcion linea_accion,
                c14.clave clv_programa_sectorial,
                c14.descripcion programa_sectorial,
                c15.clave clv_tipologia_conac,
                c15.descripcion tipologia_conac,
                c16.clave clv_programa,
                c16.descripcion programa,
                c17.clave clv_subprograma,
                c17.descripcion subprograma,
                c18.clave clv_proyecto,
                c18.descripcion proyecto
            "))
            ->where('e.ejercicio', $this->anio)
            ->where('c06.clave', $cond_upp, $upp)
            ->where('c08.clave', $cond_ur, $ur)
            ->whereNull('e.deleted_at')
            ->orderBy(DB::raw('
                c06.clave,c07.clave,c08.clave,c09.clave,
                c10.clave,c11.clave,c12.clave,c13.clave,
                c14.clave,c15.clave,c16.clave,c17.clave,c18.clave
            '));

            if($perfil == 5){
                $epp->join('uppautorizadascpnomina as u', function($join){
                    $join->on('c06.clave', '=', 'u.clv_upp');
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
