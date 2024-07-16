<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mml_definicion_problema', function (Blueprint $table) {
            $table->integer('id_region')->unsigned()->nullable();
            $table->integer('id_municipio')->unsigned()->nullable();
            $table->integer('id_localidad')->unsigned()->nullable();
            $table->foreign('id_region')->references('id')->on('catalogo');
            $table->foreign('id_municipio')->references('id')->on('catalogo');
            $table->foreign('id_localidad')->references('id')->on('catalogo');
        });

         //Se arregla primero las combinaciones erroneas de definicion problema
        // 00, 001, 001. --> Región 03 = idCatalogo = 15255
         DB::table('mml_definicion_problema')
            ->where('region', '00')
            ->where('municipio', '001')
            ->where('localidad', '001')
            ->update(['region' => '03', 'id_localidad' => 15255 ]);
        // 00, 026, 020. ---> Región 09 idCatalogo = 13487
         DB::table('mml_definicion_problema')
            ->where('region', '00')
            ->where('municipio', '026')
            ->where('localidad', '020')
            ->update(['region' => '09', 'id_localidad' => 13487]);

        // 03, 001, 002  --> Región 03 idCatalogo = 15254
         DB::table('mml_definicion_problema')
            ->where('region', '03')
            ->where('municipio', '001')
            ->where('localidad', '002')
            ->update(['region' => '03', 'id_localidad' => 15254]);

        // 00, 052, 001. --> Región 09 idCatalogo = 12136
         DB::table('mml_definicion_problema')
            ->where('region', '00')
            ->where('municipio', '052')
            ->where('localidad', '001')
            ->update(['region' => '09', 'id_localidad' => 12136]);


        $getIdRegion = DB::table('catalogo as c')
                       ->distinct()
                       ->select('c.id as region_id', 'mdp.region')
                       ->join('mml_definicion_problema as mdp', 'mdp.region', 'c.clave')
                       ->where('mdp.region', '!=', '')
                       ->where('c.ejercicio', 2024)
                       ->where('c.grupo_id', 23)->get();

        for ($x = 0; $x < sizeof($getIdRegion); $x ++) {
            DB::table('mml_definicion_problema')
            ->where('region', $getIdRegion[$x]->region)
            ->update(['id_region' => $getIdRegion[$x]->region_id]);
        }

        $getIdMunicipio = DB::table('catalogo as c')
                       ->distinct()
                       ->select('c.id as municipio_id', 'mdp.municipio')
                       ->join('mml_definicion_problema as mdp', 'mdp.municipio', 'c.clave')
                       ->where('mdp.municipio', '!=', '')
                       ->where('c.ejercicio', 2024)
                       ->where('c.grupo_id', 24)->get();

        for ($x = 0; $x < sizeof($getIdMunicipio); $x ++) {
            DB::table('mml_definicion_problema')
            ->where('municipio', $getIdMunicipio[$x]->municipio)
            ->update(['id_municipio' => $getIdMunicipio[$x]->municipio_id]);
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
