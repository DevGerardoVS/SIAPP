<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Log;
use Illuminate\Validation\Rule;
use App\Models\calendarizacion\Metas;
use App\Imports\utils\FunFormats;
use Auth;

class MetasSheetImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $aux = [];
        foreach ($rows as $key => $value) {
            if (FunFormats::isExists($value)) {
                $aux[] = [
                    'clv_fondo' => $value['fondo'],
                    'actividad_id' => $value['cve_act'],
                    'tipo' => $value['tipo_calendario'],
                    'beneficiario_id' => $value['cve_benef'],
                    'unidad_medida_id' => $value['cve_um'],
                    'cantidad_beneficiarios' => $value['nbeneficiarios'],
                    'enero' => $value['enero'],
                    'febrero' => $value['febrero'],
                    'marzo' => $value['marzo'],
                    'abril' => $value['abril'],
                    'mayo' => $value['mayo'],
                    'junio' => $value['junio'],
                    'julio' => $value['julio'],
                    'agosto' => $value['agosto'],
                    'septiembre' => $value['septiembre'],
                    'octubre' => $value['octubre'],
                    'noviembre' => $value['noviembre'],
                    'diciembre' => $value['diciembre'],
                    'total' => FunFormats::typeTotal($value),
                    'estatus' => 0,
                    'created_user' => auth::user()->username
                ];
            }

        }
        Log::debug($aux);
          /*    foreach ($aux as $key) {
                  $meta = Metas::create($key);
                 Log::debug($meta);
             } */

    }
}