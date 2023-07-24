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
        $tipo = $value[15];
        $auxTotal = array(
            $value[17],
            $value[18],
            $value[19],
            $value[20],
            $value[21],
            $value[22],
            $value[23],
            $value[24],
            $value[25],
            $value[26],
            $value[27],
            $value[28]
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
                    $actividad = ActividadesMir::where('deleted_at', null)->where('actividades_mir.clv_actividad', $k[13])->first();
                    if ($actividad) {
                        
                        $area= ''.strval($k[0]).strval($k[1]).strval($k[2]).strval($k[3]).strval($k[4]).strval($k[5]).strval($k[6]).strval($k[9]).strval($k[10]). strval($k[11]).'';
                        Log::debug($area);
                        $proy = DB::table('proyectos_mir')
                        ->select('*')
                        ->where('deleted_at', null)
                        ->where('id', $actividad['proyecto_mir_id'])
                        ->where('area_funcional',$area)
                        ->where('clv_upp',$k[7])
                      /*   ->where('clv_funcion', $k[1])
                        ->where('clv_subfuncion', $k[2])
                        ->where('clv_eje', $k[3])
                        ->where('clv_linea_accion', $k[4])
                        ->where('clv_programa_sectorial',$k[5])
                        ->where('clv_tipologia_conac', $k[6])
                        ->where('clv_upp',$k[7])
                        ->where('clv_ur', $k[8])
                        ->where('clv_programa', $k[9])
                        ->where('clv_subprograma', $k[10])
                        ->where('clv_proyecto', $k[11]) */
                        ->get();
                        Log::debug($proy);
                        if (count($proy)>=1) {
                            $clave =''. strval($k[0]) . '-' .strval($k[1]) . '-' . strval($k[2]) . '-' . strval($k[3]). '-' .strval($k[4]).'-'. strval($k[5]) . '-' .strval($k[6]) .'-'. strval($k[7]) . '-' .strval($k[8]) . '-' . strval($k[9]) . '-' . strval($k[10]). '-' .strval($k[11]).'';
                            $pres=FunFormats::existPP($clave);
                            if (count($pres)) {
                                $aux[] = [
                                    'clv_fondo' => $k[12],
                                    'actividad_id' => $k[13],
                                    'tipo' => $k[16],
                                    'beneficiario_id' => $k[29],
                                    'unidad_medida_id' => $k[32],
                                    'cantidad_beneficiarios' => $k[31],
                                    'enero' => $k[17],
                                    'febrero' => $k[18],
                                    'marzo' => $k[19],
                                    'abril' => $k[20],
                                    'mayo' => $k[21],
                                    'junio' => $k[22],
                                    'julio' => $k[23],
                                    'agosto' => $k[24],
                                    'septiembre' => $k[25],
                                    'octubre' => $k[26],
                                    'noviembre' => $k[27],
                                    'diciembre' => $k[28],
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
           // Log::debug($meta);
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
            if (count($datos) === 34) {
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
    public static function existPP($clave){
        $arrayclave=explode( '-', $clave);
        try {
            $activs = DB::table('programacion_presupuesto')
			->select(
				'programacion_presupuesto.id',
                DB::raw('CONCAT('.'"'.'finalidad, "-",funcion,"-",subfuncion,"-",eje,"-",linea_accion,"-",programa_sectorial,"-",tipologia_conac,"-",upp,"-",ur,"-",programa_presupuestario,"-",subprograma_presupuestario,"-",proyecto_presupuestario'.'"'.') AS clave')
			)
			->where('deleted_at', null)
		 ->where('finalidad',$arrayclave[0])
			->where('funcion',$arrayclave[1])
			->where('subfuncion',$arrayclave[2])
			->where('eje',$arrayclave[3])
			->where('linea_accion',$arrayclave[4])
			->where('programa_sectorial',$arrayclave[5])
			->where('tipologia_conac',$arrayclave[6])
			->where('upp',$arrayclave[7])
            ->where('ur', $arrayclave[8])
            ->where('programa_presupuestario', $arrayclave[9])
            ->where('subprograma_presupuestario', $arrayclave[10])
			->where('proyecto_presupuestario', $arrayclave[11])
            ->where('programacion_presupuesto.ejercicio', '=', 2023)
            ->groupByRaw('finalidad,funcion,subfuncion,eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programa_presupuestario,subprograma_presupuestario')
				->distinct()
            ->get();
        
                return $activs;
          
        } catch (\Throwable $th) {
            throw $th;
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