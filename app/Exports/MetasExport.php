<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use App\Helpers\MetasHelper;


class MetasExport implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths
{
    protected $filas;
    public function collection()
    {

        $query = MetasHelper::actividades();
        $this->filas = count($query);

        return $query;
    }

    /**
     * Retorna un arreglo con los encabezados del excel en orden de las columnas
     * @return array Encabezados de los usuarios
     */
    public function headings(): array
    {
        return ["UR", "Programa", "Subprograma", "proyecto", "Actividad", "Tipo Actividad", "Meta anual", "# Beneficiarios", "beneficiarios","U de medida"];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 10,
            'C' => 10,
            'D' => 10,
            'E' => 10,
            'F' => 25,
            'G' => 20,
            'H' => 10,
            'I' => 10,
            'J' => 10
        ];
    }
    public function registerEvents():array{
        return[
            AfterSheet::class=> function(AfterSheet $event){
                $sheet = $event -> sheet;
                $event->sheet->getDelegate()
                ->getStyle('A1:k12')
                ->applyFromArray(['alignment'=>['wrapText'=>true]]);

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000'],
                        ],
                    ],
                ];
    
                $event->sheet->getStyle('A1:K'.$this->filas)->applyFromArray($styleArray);
               },
             
        ];
    }
}
