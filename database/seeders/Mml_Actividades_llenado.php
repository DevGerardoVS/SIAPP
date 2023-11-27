<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\MmlMir;

class Mml_Actividades_llenado extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $mml_ac=DB::table('mml_actividades')
		->select(
			"id",
			"area_funcional",
			"entidad_ejecutora"
			)->get();
		foreach ($mml_ac as $key) {
			$mmlac=MmlMir::where("id",$key->id)->first();
			$area = str_split($key->area_funcional);
			$entidad = str_split($key->entidad_ejecutora);
			$ur='' . strval($entidad[4]) . strval($entidad[5]) . '';
			$pp='' . strval($area[8]) . strval($area[9]) . '';
			if($mmlac){
			$mmlac->clv_ur = $ur;
			$mmlac->clv_pp = $pp;
			$mmlac->save();
			}
		}
    }
}
