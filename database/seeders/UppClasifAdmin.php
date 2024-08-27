<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\catalogos\ClasificacionAdministrativa;
use App\Models\catalogos\EntidadEjecutora;
use App\Models\UppExtras;



class UppClasifAdmin extends Seeder
{

    public function run()
    {
        echo "\nInicializacion de transacion de Rel upp clasificacion Administrativa upp y cambio de estatus";
        DB::beginTransaction();
        try {

            $clf_a = DB::table('clasificacion_administrativa')->where('deleted_at', null)->get();
            foreach ($clf_a as $key ) {
                $rel = UppExtras::where('clasificacion_administrativa_id', $key->id)->first();
                $editclf = ClasificacionAdministrativa::find($key->id);
                if (isset($rel)) {
                    $editclf->estatus = 1;
                    $editclf->save();
                }
            }

            DB::commit();
            echo "\n    - Se aplico con exito el Seeder - Rel upp clasificacion Administrativa:\n";
        } catch (\Exception $e) {
            DB::rollback();
            echo "\n    - Ocurrio un error al ejecutar la operacion:", $e;
        }

    }
}