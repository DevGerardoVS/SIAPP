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
use App\Models\Mir;
use App\Http\Controllers\Calendarizacion\MetasController;

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
                    //checar si la mir esta confirmada

                        $anio = DB::table('cierre_ejercicio_metas')->where('clv_upp', '=', strval($k[7]))->select('ejercicio')->get();
                        $isMir = DB::table("mml_avance_etapas_pp")
                            ->select('id', 'estatus')
                            ->where('clv_upp', '=',strval($k[7]))
                            ->where('ejercicio', '=', $anio[0]->ejercicio)
                            ->where('estatus', 3)->get();
				if (count($isMir)) {
                    $actividad=Mir::where('deleted_at', null)->where('id', $k[13])->first();
                    if ($actividad) {
                        $area= ''.strval($k[0]).strval($k[1]).strval($k[2]).strval($k[3]).strval($k[4]).strval($k[5]).strval($k[6]).strval($k[9]).strval($k[10]). strval($k[11]).'';
                        $anio = $actividad->ejercicio;
                        if ($actividad->area_funcional==$area) {
                            $clave =''. strval($k[0]) . '-' .strval($k[1]) . '-' . strval($k[2]) . '-' . strval($k[3]). '-' .strval($k[4]).'-'. strval($k[5]) . '-' .strval($k[6]) .'-'. strval($k[7]) . '-' .strval($k[8]) . '-' . strval($k[9]) . '-' . strval($k[10]). '-' .strval($k[11]).'';
                            $entidad =''. strval($k[0]) . '-' .strval($k[1]) . '-' . strval($k[2]) . '-' . strval($k[3]). '-' .strval($k[4]).'-'. strval($k[5]) . '-' .strval($k[6]) . '-' . strval($k[9]) . '-' . strval($k[10]). '-' .strval($k[11]).'/'. strval($k[7]) . '-' .'0' . '-' . strval($k[8]) . '';

                            $pres=FunFormats::existPP($clave,$anio);
                            if (count($pres)) {
                                $s=FunFormats::validatecalendar($k[7],$k[15]);
                                if ($s["status"]) {
                                    $meses = [
                                        "enero" => $k[17],
                                        "febrero" => $k[18],
                                        "marzo" => $k[19],
                                        "abril" => $k[20],
                                        "mayo" => $k[21],
                                        "junio" => $k[22],
                                        "julio" => $k[23],
                                        "agosto" => $k[24],
                                        "septiembre" => $k[25],
                                        "octubre" => $k[26],
                                        "noviembre" => $k[27],
                                        "diciembre" => $k[28],
                                    ];
                                    $m=FunFormats::validateMonth($entidad,json_encode($meses),$anio);
                                    if($m["status"]){
                                        $e=FunFormats::isExist($entidad, $k[12],$k[13]);
                                        if($e["status"]){
                                        $unique= ''.strval($k[0]).strval($k[1]).strval($k[2]).strval($k[3]).strval($k[4]).strval($k[5]).strval($k[6]).strval($k[9]).strval($k[10]). strval($k[11]). strval($k[12]). strval($k[13]).'';

                                        DB::table('metas_temp')->insert(['clave' => $unique,'fila'=>$index,'upp'=>strval($k[7])]);
                                            $aux[] = [
                                                'meta_id'=>$e["id"],
                                                'clv_fondo' => $k[12],
                                                'mir_id' => $k[13],
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
                                                'created_user' => auth::user()->username
                                            ];
                                    }else{
                                        $error = array(
                                            "icon" => 'error',
                                            "title" => 'Error',
                                            "text" => 'La meta ya existe en la fila '. $index
                                        );
                                        return $error;

                                    }
                                    }else{
                                        $err=implode(", ", $m["errorM"]);
                                        $meses=implode(", ", $m["mV"]);
                                        if(count($m["mV"])==1){
                                            
                                                $mesaje = '. Solo puede registrar en el mes de: ' . $meses;
                                        }else{
                                            $mesaje = '. Solo puede registrar en los meses: ' . $meses;
                                        }

                                        $error = array(
                                            "icon" => 'error',
                                            "title" => 'Error',
                                            "text" => 'Los meses: '.$err. ' no coinciden en las claves presupuestales, en la fila '. $index.$mesaje
                                        );
                                        return $error;
                                    }
                                } else {
                                    $error = array(
                                        "icon" => 'error',
                                        "title" => 'Error',
                                        "text" => 'El tipo de calendario ' . $s["a"] . ' no esta autorizado para la upp  ' . $s["upp"] . ' en la fila ' . $index
                                    );
                                    return $error;
                                }
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
                                "text" => 'El areafuncional no coinciden en las claves presupuestales, en la fila' . $index
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
                } else {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => 'Los registros de la MIR no estan confirmadas en el sistema MML, acércate a CPLADEM'
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
        
        $reps = DB::table('metas_temp')
            ->select(
                DB::raw('COUNT(clave) AS rep'),
                'clave',
                'fila',
                'upp',
            )->groupBy('clave')
            ->get();
        if(count($reps)==count($aux)){
            if (Auth::user()->id_grupo == 4) {
                foreach ($reps as $key) {
                    if ($key->upp != Auth::user()->clv_upp) {
                        $error = array(
                            "icon" => 'info',
                            "title" => 'Cuidado',
                            "text" => 'Solo puedes registrar metas de la upp ' . Auth::user()->clv_upp
                        );
                        return $error;
                    }
                }
            }
            foreach ($aux as $key) {
                    try {
                        if($key->meta_id==null){
                            FunFormats::guardarMeta($key);
    
                        }else{
                            FunFormats::editarMeta($key);
                         
                        }
                      /*   $meta = Metas::create($key); */
                    } catch (\Throwable $th) {
                        //throw $th;
                    }               
               
            }
            $success = array(
                "icon" => 'success',
                "title" => 'Exito',
                "text" => 'Las metas se registraron correctamente'
            );
            return $success;

        }else{
            $filas = [];
            foreach ($reps as $key) {
                if($key->rep>1){
                    $reps = DB::table('metas_temp')->select('clave','fila')->where('clave',$key->clave)->get();
                    foreach ($reps as $a ) {
                        $filas[] = $a->fila;
                    }
                }
            }
            $f=implode(", ", $filas);
            $error = array(
                "icon" => 'info',
                "title" => 'Cuidado',
                "text" => 'Existen registros repetidos en el excel '.$f
            );
            return $error;

        }
      
    }
    public static function isNULLOrEmpy($datos,$index){
		for ($i=0; $i <count($datos) ; $i++) {
            if (count($datos) === 34) {
                if ($datos[$i] == '' ||$datos[$i]==' ') {
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
    public static function existPP($clave,$anio){
        
        $arrayclave=explode( '-', $clave);
        try {
            $activs = DB::table('programacion_presupuesto')
			->select(
				'upp',
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
            ->where('programacion_presupuesto.ejercicio', '=', $anio)
            ->groupByRaw('finalidad,funcion,subfuncion,eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programa_presupuestario,subprograma_presupuestario')
			->distinct()
            ->get();
                return $activs;
               
          
        } catch (\Throwable $th) {
            throw $th;
        }
 
	}

    public static function validateMonth($clave,$m,$anio){
        $meses = json_decode($m);
        $areaAux=explode( '/', $clave);
       $m=MetasController::meses($areaAux[0],$areaAux[1],$anio);
  
        $arrM = [];
        $arrMV = [];
        foreach ($m as $key => $value) {
            $e = $value;
            switch ($key) {
                case 'enero':
                    if ($e == 0.0 || $e == 0) {
                        if($meses->enero!=0){
                            $arrM[] ="enero";
                        }
                        
                    }else{
                        $arrMV[] ="ENERO";
                    }
                    break;
                case 'febrero':
                    if ($e != 0.0 || $e == 0) {
                        if($meses->febrero!=0){
                            $arrM[] = "febrero";
                        }
                        
                    } else{
                        $arrMV[] = "FEBRERO";
                    }
                    break;
                case 'marzo':
                    if ($e == 0.0 || $e == 0) {
                        if($meses->marzo!=0){
                            $arrM[] = "marzo";
                        }
                        
                    }else{
                        $arrMV[] = "MARZO";
                    }
                    break;
                case 'abril':
                    if ($e == 0.0 || $e == 0) {
                        if($meses->abril!=0){
                            $arrM[] = "abril";
                        }
                        

                    }else{
                        $arrMV[] = "ABRIL";
                    }
                    break;
                case 'mayo':
                    if ($e == 0.0 || $e == 0) {
                        if($meses->mayo!=0){
                            $arrM[] = "mayo";
                        }
                       
                    }else{
                        $arrMV[] = "MAYO";
                    }
                    break;
                case 'junio':
                    if ($e == 0.0 || $e == 0) {
                        if($meses->junio!=0){
                            $arrM[] = "junio";
                        }
                    }else{
                        $arrMV[] = "JUNIO";
                    }
                    break;
                case 'julio':
                    if ($e == 0.0 || $e == 0) {
                        if($meses->julio!=0){
                            $arrM[] = "julio";
                        }
                    }else{
                        $arrMV[] = "JULIO";
                    }
                    break;
                case 'agosto':
                    if ($e == 0.0 || $e == 0) {
                        if($meses->agosto!=0){
                            $arrM[] = "agosto";
                        }
                        
                    }else{
                        $arrMV[] = "AGOSTO";
                    }
                    break;
                case 'septiembre':
                    if ($e == 0.0 || $e == 0) {
                        if($meses->septiembre!=0){
                            $arrM[] = "septiembre";
                        }
                        
                    }else{
                        $arrMV[] = "SEPTIEMBRE";
                    }
                    break;
                case 'octubre':
                    if ($e == 0.0 || $e == 0) {
                        if($meses->octubre!=0){
                            $arrM[] = "octubre";
                        }
                    }else{
                        $arrMV[] = "OCTUBRE";
                    }
                    break;

                case 'noviembre':
                    if ($e == 0.0 || $e == 0) {
                        if($meses->noviembre!=0){
                            $arrM[] = "noviembre";
                        }
                    }else{
                        $arrMV[] = "NOBIEMBRE";
                    }
                    break;

                case 'diciembre':
                    if ($e == 0.0 || $e == 0) {
                        if($meses->diciembre!=0){
                            $arrM[] = "diciembre";
                        }
                    }else{
                        $arrMV[] = "DICIEMBRE";
                    }
            
                default:
                    break;
            }
            
        }

        if(count($arrM)==0){
            return ["status"=>true];
        }else{
            return ["status"=>false,"errorM"=>$arrM,"mV"=>$arrMV];
        }


    }
    public static function validatecalendar($upp,$act){
        $tipo = MetasController::getTcalendar($upp);
           switch ($act) {
            case 0:
                if($tipo->Acumulativa!=1){
                        return ["status" => false, "a"=>'Acumulativa',"upp"=>$upp];
                }else{
                    return ["status" => true];
                }
            case 1:
                if($tipo->Continua!=1){
                    return ["status" => false , "a"=>'Continua',"upp"=>$upp];
            }else{
                return ["status" => true];
            }
            case 2:
                if($tipo->Especial!=1){
                    return ["status" => false ,"a"=>'Continua',"upp"=>$upp];
            }else{
                return ["status" => true];
            }
            default:
                # code...
                break;
           }
        
	}
        public static function isExist($entidad,$fondo,$mir)
        {
            $areaAux=explode( '/', $entidad);
            $metas = DB::table('metas')
			->leftJoin('mml_mir', 'mml_mir.id', 'metas.mir_id')
			->select(
                'metas.id',
				'mml_mir.entidad_ejecutora',
				'mml_mir.area_funcional',
				'mml_mir.clv_upp'
			)
/*             ->where('mml_mir.entidad_ejecutora',$areaAux[1])
            ->where('mml_mir.area_funcional',$areaAux[0]) */
            ->where('metas.clv_fondo', $fondo)
            ->where('metas.mir_id', $mir)
			->where('mml_mir.deleted_at', null)
            ->where('metas.deleted_at', null)->get();
            if(count($metas)==0){
            return ["status" => true,"id"=>null];
            }else{
                return ["status" => true,"id"=>$metas[0]->id];
            }

        }
        
        public static function guardarMeta($key)
    {
        Log::debug("creando meta");
        $meta = Metas::create([
            'mir_id' => $key->mir_id,
            'clv_fondo' => $key->clv_fondo,
            'estatus' => 0,
            'tipo' => $key->tipo,
            'beneficiario_id' => $key->beneficiario_id,
            'unidad_medida_id' => $key->unidad_medida_id,
            'cantidad_beneficiarios' => $key->cantidad_beneficiarios,
            'total' => $key->total,
            'enero' => $key->enero,
            'febrero' => $key->febrero,
            'marzo' => $key->marzo,
            'abril' => $key->abril,
            'mayo' => $key->mayo,
            'junio' => $key->junio,
            'julio' => $key->julio,
            'agosto' => $key->agosto,
            'septiembre' => $key->septiembre,
            'octubre' => $key->octubre,
            'noviembre' => $key->noviembre,
            'diciembre' => $key->diciembre,
            'created_user' => $key->created_user,
        ]);
        Log::debug($meta);

    }
    public static function editarMeta($key)
    {
        Log::debug("Editando meta");
        $meta = Metas::where('id', $key->meta_id)->firstOrFail();
        $fecha = Carbon::now()->toDateTimeString();
        if ($meta) {
            $meta->tipo = $key->tipo;
            $meta->beneficiario_id = $key->beneficiario_id;
            $meta->unidad_medida_id = $key->unidad_medida_id;
            $meta->cantidad_beneficiarios = $key->cantidad_beneficiarios;
            $meta->total = $key->total;
            $meta->enero = $key->enero;
            $meta->febrero = $key->febrero;
            $meta->marzo = $key->marzo;
            $meta->abril = $key->abril;
            $meta->mayo = $key->mayo;
            $meta->junio = $key->junio;
            $meta->julio = $key->julio;
            $meta->agosto = $key->agosto;
            $meta->septiembre = $key->septiembre;
            $meta->octubre = $key->octubre;
            $meta->noviembre = $key->noviembre;
            $meta->diciembre = $key->diciembre;
            $meta->updated_at = $fecha;
            $meta->updated_user = auth::user()->username;
            $meta->save();
            Log::debug($meta);

        }
    }

}