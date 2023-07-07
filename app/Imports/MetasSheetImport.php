<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Validator;
use Log;
use Illuminate\Validation\Rule;
use App\Models\calendarizacion\Metas;
use App\Imports\utils\FunFormats;
use App\Models\calendarizacion\ActividadesMir;
use App\Models\calendarizacion\ProyectosMir;
use Auth;

class MetasSheetImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $validator =Validator::make($rows->toArray(),
        [
            '*.upp'=>'required',
            '*.ur'=>'required',
            '*.prg'=>'required',
            '*.spr'=>'required',
            '*.py'=>'required',
            '*.fondo'=>'required',
            '*.cve_act'=>'required',
            '*.cve_cal'=>'required',
            '*.enero'=>'required',
            '*.febrero'=>'required',
            '*.marzo'=>'required',
            '*.abril'=>'required',
            '*.mayo'=>'required',
            '*.junio'=>'required',
            '*.julio'=>'required',
            '*.agosto'=>'required',
            '*.septiembre'=>'required',
            '*.octubre'=>'required',
            '*.noviembre'=>'required',
            '*.diciembre'=>'required',
            '*.cve_benef'=>'required',
            '*.nbeneficiarios'=>'required',
            '*.cve_um'=>'required',
        ]
        )->validate();

        Log::debug($validator);
       /*  $validator->after(function ($validator) {

            foreach ($validator as $key) {
                Log::debug()
                $actividad = ActividadesMir::where('deleted_at', null)->where('actividades_mir.id', $key['cve_act'])->firstOrFail();
                if ($actividad) {
                    $proy = ProyectosMir::where('deleted_at', null)
                        ->where('id', $actividad['proyecto_mir_id'])
                        ->where('clv_upp', preg_replace('([^0-9])', '', $actividad['upp']))
                        ->where('clv_ur', $actividad['ur'])
                        ->where('clv_programa', $actividad['prg'])
                        ->where('clv_subprograma', $actividad['spr'])
                        ->where('clv_proyecto', $actividad['py'])
                        ->get();
                    Log::debug($proy);
                    if (count($proy)) {
                        $validator->errors()->add('py', 'El proyecto no existe');
                    }
                }else{
                    $validator->errors()->add('cve_act', 'La actividad no existe');
    
                }
            }
     
        }); */

     /*    $aux = [];
        foreach ($rows as $key) {
            Log::debug($key);
            if (FunFormats::isExists($key)) {
                $aux[] = [
                    'clv_fondo' => $key['fondo'],
                    'actividad_id' => $key['cve_act'],
                    'tipo' => $key['tipo_calendario'],
                    'beneficiario_id' => $key['cve_benef'],
                    'unidad_medida_id' => $key['cve_um'],
                    'cantidad_beneficiarios' => $key['nbeneficiarios'],
                    'enero' => $key['enero'],
                    'febrero' => $key['febrero'],
                    'marzo' => $key['marzo'],
                    'abril' => $key['abril'],
                    'mayo' => $key['mayo'],
                    'junio' => $key['junio'],
                    'julio' => $key['julio'],
                    'agosto' => $key['agosto'],
                    'septiembre' => $key['septiembre'],
                    'octubre' => $key['octubre'],
                    'noviembre' => $key['noviembre'],
                    'diciembre' => $key['diciembre'],
                    'total' => FunFormats::typeTotal($key),
                    'estatus' => 0,
                    'created_user' => auth::user()->username
                ];
            }
            

        } */
   
          /*    foreach ($aux as $key) {
                  $meta = Metas::create($key);
                 Log::debug($meta);
             } */

    }
}