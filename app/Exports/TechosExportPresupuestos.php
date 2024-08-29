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
         ob_end_clean();
        ob_start();

        $array_data = array();
        $final_data= array();
        /* $data = DB::table('techos_financieros as tf')
            ->select('tf.clv_upp','tf.clv_fondo','tf.tipo','tf.presupuesto','tf.ejercicio','vee.Ej')
            ->leftJoinSub('select distinct clv_upp, upp, ejercicio as Ej from v_epp','vee','tf.clv_upp','=','vee.clv_upp')
            ->leftJoinSub('select distinct clv_fondo_ramo, fondo_ramo from fondo','f','tf.clv_fondo','=','f.clv_fondo_ramo')
            ->where('tf.deleted_at','=',null)
            ->where('tf.ejercicio','=',$this->ejercicio)
            ->where('vee.Ej','=',$this->ejercicio)
        ->get(); */

        $data = DB::table('techos_financieros as tf')
            ->select('tf.clv_upp','tf.clv_fondo','tf.tipo','tf.presupuesto','tf.ejercicio','ve.ejercicio as Ej')
            ->leftJoin('catalogo as c','tf.clv_fondo','=','c.clave')
            ->leftJoin('v_epp as ve','tf.clv_upp','=','ve.clv_upp')
            ->where('tf.deleted_at','=',null)
            ->where('tf.ejercicio','=',intval($this->ejercicio))
            ->where('ve.ejercicio','=',intval($this->ejercicio))
            ->where('c.grupo_id','=','FONDO DEL RAMO')
            ->orderBy('tf.clv_upp','asc')
            ->distinct()
        ->get();

            $arr = json_decode(json_encode ( $data ) , true);
            foreach($arr as $d){
                 $keyname=$d['clv_upp'].$d['clv_fondo'];
                //buscar en el array de totales 
                if(array_key_exists($keyname, $array_data)){
    
                    if($d['tipo']=='RH'){
                        $array_data[$keyname] =  $array_data[$keyname].','.$d['presupuesto']; 
                       }
                       else{
                        $array_data[$keyname] =  $d['presupuesto'].','.$array_data[$keyname]; 

                       }
        
                   }else{
                            $array_data[$keyname] = $d['presupuesto'];                           
                   }
            }
            foreach ($array_data as $key => $value) {
                $llavesplit = str_split($key, 3);
               $presupuestos = explode(",", $value);
               if(array_key_exists(1,$presupuestos)){
                array_push($final_data,[$llavesplit[0],$llavesplit[1],$presupuestos[0],$presupuestos[1],$presupuestos[0]+$presupuestos[1]]);
               }
               else{
                array_push($final_data,[$llavesplit[0],$llavesplit[1],$presupuestos[0],'',$presupuestos[0]]);
               }
            }

        return collect($final_data);
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
