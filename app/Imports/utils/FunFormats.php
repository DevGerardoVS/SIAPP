<?php
namespace App\Imports\utils;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\calendarizacion\ActividadesMir;
use App\Models\calendarizacion\ProyectosMir;


class FunFormats
{
    public static function typeTotal($value)
    {
        $tipo = $value['cve_cal'];
        $auxTotal = array(
            $value['enero'],
            $value['febrero'],
            $value['marzo'],
            $value['abril'],
            $value['mayo'],
            $value['junio'],
            $value['julio'],
            $value['agosto'],
            $value['septiembre'],
            $value['octubre'],
            $value['noviembre'],
            $value['diciembre']
        );
        switch ($tipo) {
            case 0:
                return FunFormats::totalAcum($auxTotal);
            case 1:

                return $auxTotal[0];
            //  return $this->totalAcum($auxTotal);
            case 2:
                return FunFormats::totalEspecial($auxTotal);
            default:
                # code...
                break;
        }

    }
    public static function totalEspecial($arreglo)
    {
        return max($arreglo);
    }
    public static function totalAcum($arreglo)
    {
        $suma = 0;
        for ($i = 0; $i < count($arreglo); $i++) {
            $suma = $suma + $arreglo[$i];
        }
        return $suma;
    }
    public static function isExists($datos)
    {
        Log::debug("isExists");
        $actividad =ActividadesMir::where('deleted_at',null)->where('actividades_mir.id', $datos['cve_act'])->firstOrFail();
        //$proy =ProyectosMir::where('deleted_at',null)->where('id',$actividad[0]->proyecto_mir_id)->firstOrFail();

        Log::debug("actividad".$actividad['proyecto_mir_id']);
        if ($actividad) {
            return true;
        } else {
            return false;
        }
    }
    /*     public function arrEquals($numeros)
        {
            $duplicados = [];
            $bool = count($numeros);

            $tempArray = [...$numeros] . sort();

            for ($i = 0; $i <= count($tempArray); $i++) {
                if ($tempArray[i + 1] === $tempArray[i]) {
                    $duplicados . push($tempArray[$i]);
                }
            }
            if ($bool != $duplicados) {
                return false;
            } else {
                return true;
            }
        } */

}