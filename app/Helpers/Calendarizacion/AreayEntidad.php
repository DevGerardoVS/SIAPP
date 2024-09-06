<?php

namespace App\Helpers\Calendarizacion;

use Auth;
class AreayEntidad
{
    public $clv_finalidad;
    public $clv_funcion;
    public $clv_subfuncion;
    public $clv_eje;
    public $clv_linea_accion;
    public $clv_programa_sectorial;
    public $clv_tipologia_conac;
    public $clv_upp;
    public $clv_ur;
    public $clv_programa;
    public $clv_subprograma;
    public $clv_proyecto;
    public $area_funcional;
    function __construct($areaFuncional,$entidadEjecutora) {

/*         $this->clv_finalidad=strval($area[0]);
        $this->clv_funcion=strval($area[1]);
        $this->clv_subfuncion=strval($area[2]);
        $this->clv_eje=strval($area[3]);
        $this->clv_linea_accion=strval($area[4]);
        $this->clv_programa_sectorial=strval($area[5]);
        $this->clv_tipologia_conac=strval($area[6]);
        $this->clv_upp=strval($entidad[0]);
        $this->clv_ur=strval($entidad[2]);
        $this->clv_programa=strval($area[7]);
        $this->clv_subprograma=strval($area[8]);
        $this->clv_proyecto=strval($area[9]);
        $this->area_funcional = implode($area);
        $this->entidad_ejecutora = implode($entidad); */
        $area=str_split($areaFuncional);
        $entidad=str_split($entidadEjecutora);
        $this->clv_finalidad=strval($area[0]);
        $this->clv_funcion=strval($area[1]);
        $this->clv_subfuncion=strval($area[2]);
        $this->clv_eje=strval($area[3]);
        $this->clv_linea_accion=strval($area[4].$area[5]);
        $this->clv_programa_sectorial=strval($area[6]);
        $this->clv_tipologia_conac=strval($area[7]);
        $this->clv_upp=strval($entidad[0].$entidad[1].$entidad[2]);
        $this->clv_ur=strval($entidad[4].$entidad[5]);
        $this->clv_programa=strval($area[8].$area[9]);
        $this->clv_subprograma=strval($area[10].$area[11].$area[12]);
        $this->clv_proyecto=strval($area[13].$area[14].$area[15]);
        $this->area_funcional = $area;
        $this->entidad_ejecutora = $entidad;
    }
    public function variables()
    {
        $num = array($this->clv_finalidad,$this->clv_funcion,$this->clv_subfuncion,$this->clv_eje,$this->clv_linea_accion,$this->clv_programa_sectorial,$this->clv_tipologia_conac,$this->clv_upp,$this->clv_ur,$this->clv_programa,$this->clv_subprograma,$this->clv_proyecto); //create an array
        $obj = (object)$num; //change array to stdClass object
        return  $obj; 
    }
}