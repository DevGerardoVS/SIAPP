<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\calendarizacion\CierreMetas;
use App\Models\Catalogo;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('cierre_ejercicio_metas', 'confirmado')) {
            Schema::table('cierre_ejercicio_metas', function (Blueprint $table) {
                $table->tinyInteger('confirmado', 10)->nullable(true)->default(0)->after('estatus')->comment('estatus de confirmacion de metas')->change();
            });         
        }
        $Cat = Catalogo::where(['grupo_id' => 'ACTIVIDADES ADMON', 'ejercicio' => 2025])
        ->whereIn('clave',['UUU','21B'])->get();
        if(count( $Cat)==2){
            echo "\nYa excisten las ACTIVIDADES ADMON para el año 2025";
        }else{
            DB::unprepared("INSERT INTO `catalogo`( `padre_id`, `ejercicio`, `grupo_id`, `clave`, `descripcion`, `descripcion_larga`, `descripcion_corta`, `created_user` ) VALUES( NULL, 2025, 'ACTIVIDADES ADMON', 'UUU', 'Cumplimiento de obligaciones patronales', NULL, NULL, 'ADMIN' ),( NULL, 2025, 'ACTIVIDADES ADMON', '21B', 'Cumplimiento de resoluciones emitidas por autoridad judicial y laudos', NULL, NULL, 'ADMIN' );");
        }
        CierreMetas::where('ejercicio','!=',2025)
        ->update(['confirmado' =>1]);
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
