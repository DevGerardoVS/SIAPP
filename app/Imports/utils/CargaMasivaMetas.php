<?php
namespace App\Imports\utils;

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
    public $beneficiario_id;
    public $cantidad_beneficiarios;
    public $unidad_medida_id;
    public $ejercicio;
    public $area_funcional;
    public $enidad_ejecutora;
    public $is3u;
    public $tipoMeta;
    public function saveImport($k,$anio)
    {

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
        $this->area_funcional = strval(strval($k[0]) . strval($k[1]) . strval($k[2]) . strval($k[3]) . strval($k[4]) . strval($k[5]) . strval($k[6]) . strval($k[6]) . strval($k[10]) . strval($k[11]));
        $this->clv_upp = strval($k[7]);
        $this->clv_ur = strval($k[8]);
        $this->clv_pp = strval($k[9]);
        $this->clv_fondo = strval($k[12]);
        $this->actividad_id = strval($k[13]);
        $this->mir_id = strval($k[14]);
        $this->clv_cal = strval($k[16]);
        $this->tipo = $k[17];
        $this->beneficiario_id = $k[30];
        $this->unidad_medida_id = $k[33];
        $this->cantidad_beneficiarios = $k[32];
        $this->enero = $this->is3u ? $k[18] : 2;
        $this->febrero = $this->is3u ? $k[19] : 2;
        $this->marzo = $this->is3u ? $k[20] : 2;
        $this->abril = $this->is3u ? $k[21] : 2;
        $this->mayo = $this->is3u ? $k[22] : 2;
        $this->junio = $this->is3u ? $k[23] : 2;
        $this->julio = $this->is3u ? $k[24] : 2;
        $this->agosto = $this->is3u ? $k[25] : 2;
        $this->septiembre = $this->is3u ? $k[26] : 2;
        $this->octubre = $this->is3u ? $k[27] : 2;
        $this->noviembre = $this->is3u ? $k[28] : 2;
        $this->diciembre = $this->is3u ? $k[29] : 3;
        $this->ejercicio = $anio;
        $this->enidad_ejecutora = getEntidadEje($this->clv_upp,$this->clv_ur,$anio);

    }
}