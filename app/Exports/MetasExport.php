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


class MetasExport implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths
{
    protected $filas;
    protected $upp;

    function __construct($upp) {

        $this->upp = $upp;
        

    }
    public function collection()
    {
/* 
        $query = MetasHelper::actividades($this->upp); */
        $dataSet = MetasController::getActiv($this->upp);
        $this->filas = count($dataSet);
        for ($i=0; $i <count($dataSet); $i++) { 
			unset($dataSet[$i][19]);
			$dataSet=array_values($dataSet);
		}
		return collect($dataSet);
    }

    /**
     * Retorna un arreglo con los encabezados del excel en orden de las columnas
     * @return array Encabezados de los usuarios
     */
    public function headings(): array
    {
        return ["FINALIDAD","FUNCION","SUBFUNCION","EJE","L ACCION","PRG SECTORIAL","TIPO CONAC","UPP","UR", "Programa", "Subprograma", "proyecto", "Actividad", "Tipo Actividad", "Calendario","Meta anual", "# Beneficiarios", "beneficiarios","U de medida"];
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
    
                $event->sheet->getStyle('A1:S'.$this->filas)->applyFromArray($styleArray);
               },
             
        ];
    }
}
