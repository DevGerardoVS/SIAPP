<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\catalogos\EntidadEjecutora;
use App\Models\Epp;
use App\Models\catalogos\ClasificacionAdministrativa;
use App\Models\UppExtras;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     try {
        DB::select("CALL rellenar_tablas_intermedias(?)",["SISTEMA"]);
        DB::select("CALL llenar_v_epp(?,?)",[null,"SISTEMA"]);
        $clf_a = ClasificacionAdministrativa::where('deleted_at', null);
        foreach ($clf_a as $key ) {
            $rel = UppExtras::where('clasificacion_administrativa_id ', $key->id)->first();
            $editclf = ClasificacionAdministrativa::find($key->id);
            if (count($rel) >= 1) {
                $editclf->estatus = 1;
                $editclf->save();
            }
        }

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
     } catch (\Throwable $th) {
        throw $th;
     }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
