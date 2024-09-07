<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class InicioExport implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths, WithStyles, WithColumnFormatting
{

    protected $yr;

    function __construct($yr) {
        $this->yr = $yr;
    }

    public function collection()
    {
        if($this->yr== null || $this->yr== "" || $this->yr == "null") $this->yr = date('Y');

        $data = DB::select('CALL inicio_b('.$this->yr.')');

        return collect($data);

        /*$data = DB::table('inicio_b')
            ->select(DB::raw('
            ejercicio,
            clave,
            fondo,
            asignado,
            programado,
            FORMAT(avance, 2) as avance '))
            ->where("ejercicio", "=", function($query){
                $query->from("inicio_b")
                ->select("ejercicio")
                ->limit(1)
                ->orderBy("ejercicio","desc")
                ->groupBy("ejercicio");})
            ->get();
        return $data;*/
    }

    /**
     * Retorna un arreglo con los encabezados del excel en orden de las columnas
     * @return array Encabezados de los usuarios
     */
    public function headings(): array
    {
        $title = '                                                                         Presupuesto por fondo                                                                   ';
        return [
            [$title],           
            ["Ejercicio",
            "Clave fondo",
            "Fondo",
            "$ Asignado",
            "$ Programado",
            "% Avance"]
        ];
    }

    public function styles(Worksheet $sheet){

        return [
            1    => ['font' => array(
                'size'      =>  15,
                'bold'      =>  true
            )],
            2    => ['font' => array(
                'size'      =>  12,
                'bold'      =>  true,
                'startColor' => Color::COLOR_RED,
            )],
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 75,
            'C' => 25,
            'D' => 25,
            'E' => 25,
            'F' => 15
        ];
    }
 
    public function columnFormats(): array{
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_NUMBER,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER ,
        ];
    }
}