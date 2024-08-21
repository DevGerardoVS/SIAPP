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
         //Se arregla primero las combinaciones erroneas de definicion problema
        // 03, 001, 002  --> Región 03 idCatalogo = 17332
         DB::table('mml_definicion_problema')
            ->where('region', '00')
            ->where('municipio', '001')
            ->where('localidad', '001')
            ->where('ejercicio', 2025)
            ->update(['region' => '03', 'id_localidad' => 17333]);

        // 00, 052, 001. --> Región 09 idCatalogo = 14214
         DB::table('mml_definicion_problema')
            ->where('region', '03')
            ->where('municipio', '001')
            ->where('localidad', '001')
            ->where('ejercicio', 2025)
            ->update(['id_localidad' => 17333]);


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
            ->where('ejercicio', 2025)
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
            ->where('ejercicio', 2025)
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
