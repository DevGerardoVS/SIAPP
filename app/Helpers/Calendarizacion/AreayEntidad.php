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
    public $clv_fondo;
    public $area_funcional;
    function __construct($area,$entidad) {

        $this->clv_finalidad=strval($area[0]);
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
        $this->entidad_ejecutora = implode($entidad);
    }
    public function variables()
    {
        $num = array($this->clv_finalidad,$this->clv_funcion,$this->clv_subfuncion,$this->clv_eje,$this->clv_linea_accion,$this->clv_programa_sectorial,$this->clv_tipologia_conac,$this->clv_upp,$this->clv_ur,$this->clv_programa,$this->clv_subprograma,$this->clv_proyecto,$this->clv_fondo); //create an array
        $obj = (object)$num; //change array to stdClass object
        return  $obj; 
    }
}