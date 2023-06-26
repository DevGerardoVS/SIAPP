<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Log;
use app\Models\calendarizacion\Metas;

class MetasSheetImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        
        mb_http_output('UTF-8');
        mb_internal_encoding('UTF-8');
        $aux = [];
        for ($i=1; $i <count($rows); $i++) {
            $a=[];
            foreach ($rows[$i] as $key => $value) {
                if($key==0 || $key == 5){
                    $text=preg_replace('([^0-9])', '', $value);
                    $a[] = $text;
                }else{
                    $a[] = $value;
                   /*  if ($key != 26 && $key != 24 && $key !=9 && $key !=7) {
                        $a[] = $value;
                    } */
                }
                
            }
            $aux[] = $a;
            Log::debug($aux);
        }
    /*     Metas::create([

        ]); */

        

    }
}
