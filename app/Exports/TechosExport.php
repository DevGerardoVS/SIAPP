<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;

use Maatwebsite\Excel\Concerns\WithColumnWidths;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TechosExport implements FromCollection, WithHeadings, WithStyles,WithEvents,WithColumnWidths
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct( int $ejercicio){
        $this->ejercicio = $ejercicio;
        return $this;
    }

    public function collection(){
        $data = DB::table('techos_financieros as tf')
            ->select('tf.clv_upp','vee.upp as descPre','tf.tipo','tf.clv_fondo','f.fondo_ramo','tf.presupuesto','tf.ejercicio')
            ->leftJoinSub('select distinct clv_upp, upp from v_epp','vee','tf.clv_upp','=','vee.clv_upp')
            ->leftJoinSub('select distinct clv_fondo_ramo, fondo_ramo from fondo','f','tf.clv_fondo','=','f.clv_fondo_ramo');
            if($this->ejercicio != 0){
                $data =  $data -> where('tf.ejercicio','=',$this->ejercicio);
            }

        $data = $data ->get();

        return collect($data);
    }

    public function headings():array {
        $headings= [];

        return [['ID UPP','Unidad Programatica Presupuestaria','Tipo', 'ID Fondo','Fondo','Presupuesto','Ejercicio']];
    }
    
    public function styles(Worksheet $sheet){
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
            1  => ['font' => ['size' => 14]],
        ];
    }

    public function columnWidths(): array{
        return [
            'A' => 10,
            'B' => 55,            
            'C' => 13,            
            'D' => 13,            
            'E' => 55,            
            'F' => 20,            
            'G' => 25,            
        ];
    }
    
    public function registerEvents(): array{
        return[
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event -> sheet;


                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000'],
                        ],
                    ],
                ];

            }
        ];
    }

}
