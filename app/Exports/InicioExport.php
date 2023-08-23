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


class InicioExport implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths, WithStyles, WithColumnFormatting
{
    public function collection()
    {
        $data = DB::table('inicio_b')
            ->select(DB::raw('
            clave,
            fondo,
            asignado,
            programado,
            FORMAT(avance, 2) as avance '))
            ->get();
        return $data;
    }

    /**
     * Retorna un arreglo con los encabezados del excel en orden de las columnas
     * @return array Encabezados de los usuarios
     */
    public function headings(): array
    {
        $title = "                                                                   Presupuesto por fondo ".date('Y')."                                                        ";
        return [
            [$title],           
            ["Clave fondo",
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
            'A' => 14,
            'B' => 53,
            'C' => 23,
            'D' => 23,
            'E' => 12
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