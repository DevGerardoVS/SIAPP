<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Catalogo;
use App\Models\Beneficiarios;
use App\Models\UnidadesMedida;
use App\Models\catalogos\EntidadEjecutora;
use App\Models\Epp;
use App\Models\catalogos\ClasificacionAdministrativa;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
          //  DB::beginTransaction();
            DB::select("CALL rellenar_tablas_intermedias(?)",["SISTEMA"]);
            DB::select("CALL llenar_v_epp(?,?)",[null,"SISTEMA"]);

            DB::unprepared("UPDATE mml_actividades SET id_catalogo =NULL WHERE id in(1188,1191,1192,1198,1199,1200,1229,1230,1231,1232,1233,1234,1235);");
            $cat = Catalogo::where(['clave' => "UUU", 'ejercicio' => 2024,'grupo_id'=>'ACTIVIDADES ADMON'])->first();
            if(isset($cat)){
                DB::unprepared("UPDATE mml_actividades SET id_catalogo = $cat->id WHERE id_catalogo=2093;");
            }
            $cat = Catalogo::select('id')->where(['clave' => "21B", 'ejercicio' => 2024,'grupo_id'=>'ACTIVIDADES ADMON'])->first();
            if(isset($cat)){
                DB::unprepared("UPDATE mml_actividades SET id_catalogo =$cat->id WHERE id_catalogo=2094;");
            }

            $ben = Beneficiarios::where('beneficiario',"Personas")->first();
            $uni = UnidadesMedida::where('unidad_medida', "Pago")->first();
            if(isset($ben) && isset( $uni)){
                DB::unprepared("UPDATE metas SET beneficiario_id =   $ben->id, unidad_medida_id = $uni->id, cantidad_beneficiarios = 1, enero = 0, febrero = 0, marzo = 0, abril = 0, mayo = 0, junio = 1, julio = 0, agosto = 0, septiembre = 0, octubre = 0, noviembre = 0, diciembre = 0, total = 1 WHERE id in(4305,4306);");
            }
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

            //DB::commit();
        } catch (\Throwable $th) {
         //   DB::rollBack();
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
