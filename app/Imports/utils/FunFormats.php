<?php
namespace App\Imports\utils;

use App\Models\calendarizacion\Metas;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\calendarizacion\ActividadesMir;
use App\Models\calendarizacion\ProyectosMir;


class FunFormats
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public static function typeTotal($value)
    {
        $tipo = $value[8];
        $auxTotal = array(
            $value[10],
            $value[11],
            $value[12],
            $value[13],
            $value[14],
            $value[15],
            $value[16],
            $value[17],
            $value[18],
            $value[19],
            $value[20],
            $value[21]
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
    public static function saveImport($filearray)
    {
        if (count($filearray) >= 1) {
            
            $index = 2;
            foreach ($filearray as $k) {
                $status =FunFormats::isNULLOrEmpy($k,$index);
                if ($status['status']) {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => $status['error']
                    );
                    return $error;
                } else {
                    $actividad = ActividadesMir::where('deleted_at', null)->where('actividades_mir.id', $k[6])->firstOrFail();
                    if ($actividad) {
                        $proy = ProyectosMir::where('deleted_at', null)
                            ->where('id', $actividad['proyecto_mir_id'])
                            ->where('clv_upp', preg_replace('([^0-9])', '', $k[0]))
                            ->where('clv_ur', $k[1])
                            ->where('clv_programa', $k[2])
                            ->where('clv_subprograma', $k[3])
                            ->where('clv_proyecto', $k[4])
                            ->get();
                        if (count($proy)) {
                            $pres=FunFormats::existPP( $k[0], $k[1], $k[2], $k[3], $k[4]);
                            if (count($pres)) {
                                $aux[] = [
                                    'clv_fondo' => $k[5],
                                    'actividad_id' => $k[6],
                                    'tipo' => $k[8],
                                    'beneficiario_id' => $k[22],
                                    'unidad_medida_id' => $k[25],
                                    'cantidad_beneficiarios' => $k[24],
                                    'enero' => $k[10],
                                    'febrero' => $k[11],
                                    'marzo' => $k[12],
                                    'abril' => $k[13],
                                    'mayo' => $k[14],
                                    'junio' => $k[15],
                                    'julio' => $k[16],
                                    'agosto' => $k[17],
                                    'septiembre' => $k[18],
                                    'octubre' => $k[19],
                                    'noviembre' => $k[20],
                                    'diciembre' => $k[21],
                                    'total' => FunFormats::typeTotal($k),
                                    'estatus' => 0,
                                    'created_user' => auth::user()->username
                                ];
                            }else{
                                $error = array(
                                    "icon" => 'error',
                                    "title" => 'Error',
                                    "text" => 'El Proyecto Ingresado no tiene presupuesto en la fila: ' . $index
                                );
                                return $error;
                            }


                        } else {
                            $error = array(
                                "icon" => 'error',
                                "title" => 'Error',
                                "text" => 'El Proyecto Ingresado no existe en la fila: ' . $index
                            );
                            return $error;
                        }
                    } else {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'Actividad Ingresada no existe en la fila: ' . $index
                        );
                        return $error;
                    }
                }
                $index++;
            }
        } else {
            $error = array(
                "icon" => 'error',
                "title" => 'Error',
                "text" => 'El documento esta vacio'
            );
            return $error;
        }
        foreach ($aux as $key) {
            $meta = Metas::create($key);
            Log::debug($meta);
        }
        $success = array(
            "icon" => 'success',
            "title" => 'Exito',
            "text" => 'Las metas se registraron correctamente'
        );
        return $success;
    }
    public static function isNULLOrEmpy($datos,$index){
		for ($i=0; $i <count($datos) ; $i++) {
            Log::debug($datos);
            if (count($datos) === 27) {
                if ($datos[$i] == '') {
                    return ["status" => true, "error" => 'El documento contiene campos vacios en la columna:' . FunFormats::abc($i) . ' fila:' . $index . ''];
                }
            }else{
            
                return ["status" => true, "error" => 'No se debe modificar la plantilla'];;
            }
		}
		return ["status" => false, "error" => null];

	}
	public static function abc($i){
		$columns=['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA'];
		return $columns[$i];
	}
    public static function existPP($upp,$ur,$prg,$sprg,$py){
        $activs = DB::table("programacion_presupuesto")
				->leftJoin('v_epp', 'v_epp.clv_proyecto', '=', 'programacion_presupuesto.proyecto_presupuestario')
				->select(
					'programacion_presupuesto.id',
					'programa_presupuestario as programa',
					'subprograma_presupuestario as subprograma',
					'v_epp.proyecto as proyecto'
				)
                ->where('programacion_presupuesto.upp', '=', $upp)
				->where('programacion_presupuesto.ur', '=', $ur)
                ->where('programa_presupuestario','=', $prg)
                ->where('subprograma_presupuestario', '=',$sprg)
                ->where('v_epp.proyecto','=' ,$py)->get();
                return $activs;
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