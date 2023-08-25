<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use App\Helpers\Calendarizacion\MetasHelper;
use App\Http\Controllers\Calendarizacion\MetasController;


class MetasExportErr implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths
{
    protected $filas;
    protected $err;

    function __construct($err) {

        $this->err= $err;
        

    }
    public function collection()
    {
/* 
        $query = MetasHelper::actividades($this->upp); */
       
        $this->filas = count($this->err);
        
		return collect($this->err);
    }

    /**
     * Retorna un arreglo con los encabezados del excel en orden de las columnas
     * @return array Encabezados de los usuarios
     */
    public function headings(): array
    {
        return ["FILA","TIPO","DESCRIPCION"];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 10,
            'C' => 25,
        ];
    }
    public function registerEvents():array{
        return[
            AfterSheet::class=> function(AfterSheet $event){
                $sheet = $event -> sheet;
                $event->sheet->getDelegate()
                ->getStyle('A1:S'.$this->filas)
                ->applyFromArray(['alignment'=>['wrapText'=>true]]);

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000'],
                        ],
                    ],
                ];
    
                $event->sheet->getStyle('A1:C'.$this->filas)->applyFromArray($styleArray);
               },
             
        ];
    }
}
