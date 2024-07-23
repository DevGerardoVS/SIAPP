<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\catalogos\EntidadEjecutora;
use App\Models\Epp;

class Status4Ej extends Seeder
{

    public function run()
    {
        echo "\nInicializacion de transacion de EntidadEjecutora estatus 4";
        DB::beginTransaction();
        try {
            $ej = EntidadEjecutora::select('id')->get();
            foreach ($ej as $key) {
                $epp = Epp::where('entidad_ejecutora_id', $key->id)->get();
                $editEj = EntidadEjecutora::find($key->id);
                //cambiar estatus de entidad ejecutora
                if (count($epp) >= 1) {
                    $editEj->estatus = 4;
                    $editEj->save();
                    //cambiar estatus de entidad ejecutora

                } else {
                    $editEj->estatus = 3;
                    $editEj->save();
                }
            }
            DB::commit();
            echo "\n    - Se aplico con exito el Seeder - EntidadEjecutora estatus 4:\n";
        } catch (\Exception $e) {
            DB::rollback();
            echo "\n    - Ocurrio un error al ejecutar la operacion:", $e;
        }

    }
}