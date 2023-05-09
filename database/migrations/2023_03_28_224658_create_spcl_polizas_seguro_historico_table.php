<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spcl_polizas_seguro_historico', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no_concesion',20)->nullable(false);
            $table->integer('id_aseguradora')->unsigned()->nullable(false);
            $table->string('no_poliza',50)->nullable(false);
            $table->string('otro_aseguradora',100)->nullable(false);
            $table->date('fecha_vencimiento')->nullable(false);
            $table->string('archivo_poliza',150)->nullable(false);
            $table->string('Extension_archivo_poliza',15)->nullable(false);
            $table->boolean('verificado')->nullable(false);
            $table->string('observaciones',255)->nullable(false);
            $table->string('created_by',200)->nullable(false);
            $table->string('updated_by',200)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->nullable(true)->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('id_aseguradora')->references('id')->on('cat_aseguradoras');
            $table->foreign('no_concesion')->references('no_concesion')->on('spcl_detalle_concesion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spcl_polizas_seguro_historico');
    }
};
