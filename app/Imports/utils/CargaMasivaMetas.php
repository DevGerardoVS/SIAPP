<?php
namespace App\Imports\utils;

use Auth;
class CargaMasivaMetas
{
    public $finalidad;
    public $funcion;
    public $subfuncion;
    public $eje;
    public $linea_accion;
    public $programa_sectorial;
    public $tipologia_conac;
    public $clv_upp;
    public $clv_ur;
    public $programa_presupuestario;
    public $subprograma_presupuestario;
    public $proyecto_presupuestario;
    public $clv_fondo;
    public $actividad_id;
    public $mir_id;
    public $nombre_actividad;
    public $clv_cal;
    public $tipo;
    public $enero;
    public $febrero;
    public $marzo;
    public $abril;
    public $mayo;
    public $junio;
    public $julio;
    public $agosto;
    public $septiembre;
    public $octubre;
    public $noviembre;
    public $diciembre;
    public $total;
    public $beneficiario_id;
    public $cantidad_beneficiarios;
    public $unidad_medida_id;
    public $ejercicio;
    public $area_funcional;
    public $entidad_ejecutora;
    public $is3u;
    public $tipoMeta;
    public $created_user;
    public $updated_user;
    public $id_grupo;
    function __construct($k,$anio,$user) {

        $this->finalidad = $k[0];
        $this->funcion = $k[1];
        $this->subfuncion = $k[2];
        $this->eje = $k[3];
        $this->linea_accion = $k[4];
        $this->programa_sectorial = $k[5];
        $this->tipologia_conac = $k[6];
        $this->programa_presupuestario = $k[9];
        $this->subprograma_presupuestario = $k[10];
        $this->is3u = strtoupper($k[10]) != 'UUU' ? false : true;
        $this->proyecto_presupuestario = $k[11];
        $this->area_funcional = strval(strval($k[0]) . strval($k[1]) . strval($k[2]) . strval($k[3]) . strval($k[4]) . strval($k[5]) . strval($k[6]) . strval($k[9]) . strval($k[10]) . strval($k[11]));
        $this->clv_upp = strval($k[7]);
        $this->clv_ur = strval($k[8]);
        $this->clv_pp = strval($k[9]);
        $this->clv_fondo = strval($k[12]);
        $this->actividad_id = is_numeric($k[13])? intval($k[13]):strval($k[13]);
        $this->mir_id = is_numeric($k[14])? intval($k[14]):strval($k[14]);
        $this->nombre_actividad = strval($k[15]);
        $this->clv_cal = strval($k[16]);
        $this->tipo = $k[17];
        $this->beneficiario_id = $k[30];
        $this->unidad_medida_id = $k[33];
        $this->cantidad_beneficiarios = $k[32];
        $this->enero = $this->is3u ==false? $k[18] : 2;
        $this->febrero = $this->is3u ==false ? $k[19] : 2;
        $this->marzo = $this->is3u ==false ? $k[20] : 2;
        $this->abril = $this->is3u ==false ?$k[21] : 2;
        $this->mayo = $this->is3u ==false ? $k[22] : 2;
        $this->junio = $this->is3u ==false ? $k[23] : 2;
        $this->julio = $this->is3u ==false ? $k[24] : 2;
        $this->agosto = $this->is3u ==false ? $k[25] : 2;
        $this->septiembre = $this->is3u ==false? $k[26] : 2;
        $this->octubre = $this->is3u ==false ? $k[27] : 2;
        $this->noviembre = $this->is3u ==false ? $k[28] : 2;
        $this->diciembre = $this->is3u ==false ? $k[29] : 3;
        $this->ejercicio = $anio;
       // $this->entidad_ejecutora = getEntidadEje($this->clv_upp,$this->clv_ur,$this->area_funcional );
        $this->created_user = $user->username;
        $this->updated_user= $user->username;
        $this->id_grupo= $user->id_grupo;
    }
    public function MetasPp()
    {
        $num = array($this->finalidad,$this->funcion,$this->subfuncion,$this->eje,$this->linea_accion,$this->programa_sectorial,$this->tipologia_conac,$this->clv_upp,$this->clv_ur,$this->programa_presupuestario,$this->subprograma_presupuestario,$this->proyecto_presupuestario,$this->clv_fondo,$this->actividad_id,$this->mir_id,$this->nombre_actividad,$this->clv_cal,$this->tipo,$this->enero,$this->febrero,$this->marzo,$this->abril,$this->mayo,$this->junio,$this->julio,$this->agosto,$this->septiembre,$this->octubre,$this->noviembre,$this->diciembre,  $this->total,$this->beneficiario_id,$this->cantidad_beneficiarios,$this->unidad_medida_id,$this->ejercicio,$this->area_funcional,$this->entidad_ejecutora,$this->is3u,$this->tipoMeta,$this->id_grupo); //create an array
        $obj = (object)$num; //change array to stdClass object
        return  $obj; 
    }
}