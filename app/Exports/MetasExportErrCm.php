<?php

namespace App\Exports;

use App\Helpers\Calendarizacion\MetasHelper;
use Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use App\Models\notificaciones;
use Auth;
use DB;



class MetasExportErrCm implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths
{
    protected $filas;
    protected $req;


    function __construct($req) {
        $this->req = $req;
    }
    public function collection()
    {
        $dataset = [];
        foreach ($this->req as $key) {
            $dataset[] =[ $key->text];
        }

        return collect($dataset);
    }

    /**
     * Retorna un arreglo con los encabezados del excel en orden de las columnas
     * @return array Encabezados de los usuarios
     */
    public function headings(): array
    {
        return [
            'Error',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 250,
        ];
     
    }
    public function registerEvents():array{
        return[
            AfterSheet::class=> function(AfterSheet $event){
                $sheet = $event -> sheet;
                $event->sheet->getDelegate()
                ->getStyle('A1:A'.$this->filas)
                ->applyFromArray(['alignment'=>['wrapText'=>true]]);

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000'],
                        ],
                    ],
                ];
    
                $event->sheet->getStyle('A1:A'.$this->filas)->applyFromArray($styleArray);
               },
             
        ];
    }
}
