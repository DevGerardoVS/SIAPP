<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;

use App\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

/* use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;

use Maatwebsite\Excel\Concerns\WithColumnWidths;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TechosExportPDF implements FromCollection, WithHeadings, WithStyles,WithEvents, ShouldAutoSize
{

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
            1    => ['font' => ['bold' => true]]
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
} */

class TechosExportPDF implements FromView{

    use Exportable;

    public function __construct( int $ejercicio){
        $this->ejercicio = $ejercicio;
        return $this;
    }

    public function view(): View
    {
        $data = DB::table('techos_financieros as tf')
            ->select('tf.clv_upp','vee.upp as descPre','tf.tipo','tf.clv_fondo','f.fondo_ramo','tf.presupuesto','tf.ejercicio')
            ->leftJoinSub('select distinct clv_upp, upp from v_epp','vee','tf.clv_upp','=','vee.clv_upp')
            ->leftJoinSub('select distinct clv_fondo_ramo, fondo_ramo from fondo','f','tf.clv_fondo','=','f.clv_fondo_ramo');
            if($this->ejercicio != 0){
                $data =  $data -> where('tf.ejercicio','=',$this->ejercicio);
            }

        $data = $data ->get();

        return view('calendarizacion.techos.plantillaPDF', [
            'data' => $data
        ]);
    }
}