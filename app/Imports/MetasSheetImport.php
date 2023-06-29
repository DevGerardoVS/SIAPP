<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Log;
use App\Models\calendarizacion\Metas;
class MetasSheetImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        
        mb_http_output('UTF-8');
        mb_internal_encoding('UTF-8');
        $aux = [];
        for ($i=1; $i <count($rows); $i++) {
            $a=[];
            Log::debug($rows[$i]);
            foreach ($rows[$i] as $key => $value) {
                if($key==0 || $key == 5){
                    $text=preg_replace('([^0-9])', '', $value);
                    $a[] = $text;
                }else{
                    if ($key != 28 && $key != 24 && $key !=9 && $key !=7) {
                        $a[] = $value;
                    } 
                }
                
            }
            $aux[] = $a;
        }

        for ($i = 0; $i < count($aux); $i++) {
            
            $meta=Metas::create([
                'actividad_id' =>$aux[$i][6] ,
                'clv_fondo' => $aux[$i][5],
                'estatus' => 0,
                'tipo' => $aux[$i][8],
                'beneficiario_id' => $aux[$i][21],
                'unidad_medidad_id' =>$aux[$i][23] ,
                'cantidad_beneficiarios' => $aux[$i][22],
                'total' =>$aux[$i][10],
                'enero' =>$aux[$i][11],
                'febrero' =>$aux[$i][12],
                'marzo' =>$aux[$i][13],
                'abril' =>$aux[$i][14],
                'mayo' =>$aux[$i][15],
                'junio' =>$aux[$i][16],
                'julio' =>$aux[$i][17],
                'agosto' =>$aux[$i][18],
                'septiembre' =>$aux[$i][18],
                'octubre' => $aux[$i][19],
                'noviembre' => $aux[$i][20],
                'diciembre' => $aux[$i][21],
            ]);


            Log::debug($meta);

        }
    }
}
