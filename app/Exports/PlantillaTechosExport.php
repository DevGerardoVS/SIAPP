<?php

namespace App\Exports;

use App\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use DB;

class PlantillaTechosExport implements FromView,WithColumnWidths
{
    protected $filas;
    public function view(): View
    {
        $upps = DB::table('v_entidad_ejecutora')->select('clv_upp','upp')->distinct()->get();
        return view('calendarización.techos.plantillaCargaTechos',[
            "upps" => $upps
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

}