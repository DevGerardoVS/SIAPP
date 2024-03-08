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
        Schema::create('ramo_33', function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('id_ramo_fondo');
            $table->unsignedInteger('id_fondo_federal');
            $table->unsignedInteger('id_programa');
            $table->integer('ejercicio');
            $table->string('created_user',45)->nullable(true);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->foreign('id_ramo_fondo')->references('id')->on('fondo');
            $table->foreign('id_fondo_federal')->references('id')->on('catalogo');
            $table->foreign('id_programa')->references('id')->on('catalogo');
        });

        DB::unprepared("CREATE VIEW v_ramo_33 AS
        select 
            r33.id,
            f.clv_ramo,
            f.ramo,
            c1.clave clv_fondo_federal,
            c1.descripcion fondo_federal,
            f.clv_fondo_ramo,
            f.fondo_ramo,
            c2.clave clv_programa,
            c2.descripcion programa,
            r33.ejercicio
        from ramo_33 r33
        join fondo f on r33.id_ramo_fondo = f.id
        join catalogo c1 on r33.id_fondo_federal = c1.id
        join catalogo c2 on r33.id_programa = c2.id;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ramo_33');
        DB::unprepared("DROP VIEW IF EXISTS v_ramo_33");
    }
};
