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

        if($perfil == 5) {$epp = DB::select('call sp_epp(1,null,null,'.$this->anio.')');}
        else if($perfil == 4) {$epp = DB::select('call sp_epp(0,'.Auth::user()->clv_upp.',null,'.$this->anio.')');}
        else {$epp = DB::select('call sp_epp(0,null,null,'.$this->anio.')');}

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
