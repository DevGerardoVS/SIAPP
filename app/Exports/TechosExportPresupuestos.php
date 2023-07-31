<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;

use Maatwebsite\Excel\Concerns\WithColumnWidths;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TechosExportPresupuestos implements FromCollection, WithHeadings, WithStyles,WithEvents, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct( int $ejercicio){
        $this->ejercicio = $ejercicio;
        return $this;
    }

    public function collection(){
        /* ob_end_clean();
        ob_start();
        ini_set('max_execution_time', 10000);
        ini_set('memory_limit', '-1'); */
        $array_data = [];
        $data = DB::table('techos_financieros as tf')
            ->select('tf.clv_upp','tf.clv_fondo','tf.tipo','tf.presupuesto','tf.ejercicio','vee.Ej')
            ->leftJoinSub('select distinct clv_upp, upp, ejercicio as Ej from v_epp','vee','tf.clv_upp','=','vee.clv_upp')
            ->leftJoinSub('select distinct clv_fondo_ramo, fondo_ramo from fondo','f','tf.clv_fondo','=','f.clv_fondo_ramo')
            ->where('tf.ejercicio','=',$this->ejercicio)
            ->where('vee.Ej','=',$this->ejercicio)
            ->get();
        
        
        
        $repeticion = [];
        foreach($data as $d){
            array_push($repeticion,[
                "upp" => $d->clv_upp,
                "fondo" => $d->clv_fondo,
                "tipo" => $d->tipo,
                "presupuesto" => $d->presupuesto,
                "ejercicio" => $d->ejercicio,
                "Ej" => $d->Ej
            ]);
        }
        array_push($repeticion,[
            "upp" => '',
            "fondo" => '',
            "tipo" => '',
            "presupuesto" => '',
            "ejercicio" => '',
            "Ej" => ''
        ]);
        
        $bandera = false;
        foreach($data as $d){
            Log::debug("message");
            if($d->tipo == 'RH'){
                Log::debug("mRH");
                $repeticion = array_slice($repeticion,1);
                if(count($repeticion) != 0){
                    //valido la existencia en el array repeticion
                    foreach($repeticion as $r){
                        if($d->clv_upp == $r['upp'] && $d->clv_fondo == $r['fondo'] && $r['tipo'] == 'Operativo' && $r['Ej'] == $d->ejercicio){
                            array_push($array_data,[$d->clv_upp,$d->clv_fondo, $r['presupuesto'],$d->presupuesto, ($d->presupuesto + $r['presupuesto'])]);
                            $r['tipo'] = '';
                            $bandera = true; 
                            break;
                        }
                    }
                    if($bandera == false){
                        $aux = false;
                        //veo que el registro actual no se haya metido ya al array del exce
                        foreach($array_data as $a){
                            if($a[0] == $d->clv_upp && $a[1] == $d->clv_fondo){
                                $aux = true;
                                break;
                            }
                            if($aux == false){
                                array_push($array_data,[$d->clv_upp,$d->clv_fondo,' ', $d->presupuesto, $d->presupuesto]);
                            }
                        }
                    }
                }
            }else if($d->tipo == 'Operativo'){
                Log::debug("OPE");
                $repeticion = array_slice($repeticion,1);
                if(count($repeticion) != 0){
                    foreach($repeticion as $r){
                        if($d->clv_upp == $r['upp'] && $d->clv_fondo == $r['fondo'] && $r['tipo'] == 'RH' && $r['Ej'] == $d->ejercicio){
                            array_push($array_data,[$d->clv_upp,$d->clv_fondo,$d->presupuesto, $r['presupuesto'], ($d->presupuesto + $r['presupuesto'])]);
                            $r['tipo'] = '';
                            $bandera = true; 
                            break;
                        }
                    }
                    if($bandera == false){
                        $aux = false;
                        foreach($array_data as $a){
                            if($a[0] == $d->clv_upp && $a[1] == $d->clv_fondo){
                                $aux = true;
                                break;
                            }
                            if($aux == false){
                                array_push($array_data,[$d->clv_upp,$d->clv_fondo,' ', $d->presupuesto, $d->presupuesto]); 
                            }
                        }
                    }
                }
            }
            $bandera = false;
        }
        Log::debug("finish");
        return collect($array_data);
    }

    public function headings():array {
        $headings= [];

        return [['ID UPP','ID Fondo','OPERATIVO','RECURSOS HUMANOS','TECHO PRESUPUESTAL']];
    }
    
    public function styles(Worksheet $sheet){
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
            1  => ['font' => ['size' => 14]],
        ];
    }

    /* public function columnWidths(): array{
        return [
            'A' => 10,
            'B' => 10,            
            'C' => 15,            
            'D' => 15,            
            'E' => 30         
        ];
    } */
    
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
