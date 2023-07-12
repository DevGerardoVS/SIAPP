<?php

namespace App\Exports;

use App\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use DB;

class PlantillaTechosExport implements FromView, WithColumnWidths, WithColumnFormatting 
{
    protected $filas;
    public function view(): View
    {
        $ejercicio = DB::table('epp')->max('ejercicio');
        $upps = DB::table('epp')->select('catalogo.clave')
            ->join('catalogo', 'catalogo.id', '=', 'epp.upp_id')
            ->where('epp.ejercicio', $ejercicio)->groupBy('catalogo.clave')->get();
        return view('calendarizacion.techos.plantillaCargaTechos', [
            "upps" => $upps,
            "ejercicio" => $ejercicio,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 8,
            'C' => 8,
            'D' => 20,
            'E' => 20
        ];
    }

    public function columnFormats(): array{
        return [
            'A' =>  NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' =>  NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER ,
        ];
    }
}