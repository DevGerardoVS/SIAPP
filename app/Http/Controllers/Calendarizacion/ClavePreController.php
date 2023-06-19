<?php

namespace App\Http\Controllers\Calendarizacion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Log;

class ClavePreController extends Controller
{
    public function getPanel(){
        return view('clavePresupuestaria.index');
    }
    public function getCreate(){
        return view('clavePresupuestaria.create');
    }
    public function getClaves(Request $request){
        $claves = DB::table('programacion_presupuesto')
        ->SELECT('programacion_presupuesto.id','clave_presupuestal', (DB::raw('enero + febrero + marzo + abril + mayo + junio + julio + agosto + septiembre + octubre + noviembre + diciembre AS totalByClave')),'catalogo.clave','catalogo.descripcion')
        ->leftJoin('entidad_ejecutora','programacion_presupuesto.entidad_ejecutora_id', '=', 'entidad_ejecutora.id')
        ->leftJoin('catalogo', 'entidad_ejecutora.upp_id','=','catalogo.id')
        ->where('programacion_presupuesto.deleted_at', '=', null)
        ->groupBy('catalogo.clave')
        ->get();
        
        //Log::debug($claves);
        return response()->json($claves, 200);

    }
    public function getRegiones(){
        $regiones = DB::table('clasificacion_geografica')
        ->SELECT('clasificacion_geografica.region_id','catalogo.clave','catalogo.descripcion')
        ->leftJoin('catalogo', 'clasificacion_geografica.region_id', '=', 'catalogo.id')
        ->where('catalogo.deleted_at', '=', null)
        ->orderBy('catalogo.clave')
        ->distinct()
        ->get();
        return response()->json($regiones, 200);
    }
    public function getMunicipios($id){
        $municipios = DB::table('clasificacion_geografica')
        ->SELECT('clasificacion_geografica.municipio_id','catalogo.clave','catalogo.descripcion')
        ->leftJoin('catalogo', 'clasificacion_geografica.municipio_id', '=', 'catalogo.id')
        ->where('catalogo.deleted_at', '=', null)
        ->where('clasificacion_geografica.region_id', '=', $id)
        ->orderBy('catalogo.clave')
        ->distinct()
        ->get();
        return response()->json($municipios, 200);
    }
    public function getLocalidades($id){
        $localidades = DB::table('clasificacion_geografica')
        ->SELECT('clasificacion_geografica.localidad_id', 'catalogo.clave', 'catalogo.descripcion')
        ->leftJoin('catalogo','clasificacion_geografica.localidad_id', '=', 'catalogo.id')
        ->WHERE('clasificacion_geografica.municipio_id','=' ,$id)
        ->WHERE('catalogo.deleted_at', '=', null)
        ->orderBy('catalogo.clave')
        ->DISTINCT()
        ->get();
        return response()->json($localidades,200);
    }
    public function getUpp(){
        $upp = DB::table('entidad_ejecutora')
        ->select('entidad_ejecutora.upp_id','catalogo.clave','catalogo.descripcion')
        ->leftJoin('catalogo','entidad_ejecutora.upp_id','=','catalogo.id')
        ->orderBy('catalogo.clave')
        ->DISTINCT()
        ->get();
        return response()->json($upp,200);
    }
    public function getUnidadesResponsables($id){
        $unidadResponsable = DB::table('entidad_ejecutora')
        ->SELECT('ur_id','catalogo.clave', 'catalogo.descripcion')
        ->leftJoin('catalogo','entidad_ejecutora.ur_id', '=', 'catalogo.id')
        ->WHERE('entidad_ejecutora.upp_id', '=', $id)
        ->WHERE('catalogo.deleted_at', '=' , null)
        ->orderBy('catalogo.clave')
        ->DISTINCT()
        ->get();
        return response()->json($unidadResponsable,200);
    }
    public function getProgramaPresupuestarios($id){
        $programasPresupuestales = DB::table('area_funcional_entidad_ejecutora')
        ->SELECT( 'programa_presupuestario_id','catalogo.clave', 'catalogo.descripcion')
        ->leftJoin('entidad_ejecutora','entidad_ejecutora.id','=', 'area_funcional_entidad_ejecutora.entidad_ejecutora_id')     
        ->leftJoin('area_funcional','area_funcional.id','=','area_funcional_entidad_ejecutora.area_funcional_id')
        ->leftJoin('catalogo','area_funcional.programa_presupuestario_id','=','catalogo.id')    
        ->WHERE('catalogo.deleted_at','=', null)
        ->WHERE('entidad_ejecutora.ur_id','=',$id)
        ->orderBy('catalogo.clave')
        ->DISTINCT()
        ->get();
        return response()->json($programasPresupuestales,200);
    }
    public function getSubProgramas($id){
        $subProgramas = DB::table('area_funcional_entidad_ejecutora')
        ->SELECT('area_funcional.subprograma_presupuestario_id','catalogo.clave', 'catalogo.descripcion')
        ->leftjoin('entidad_ejecutora','entidad_ejecutora.id', '=', 'area_funcional_entidad_ejecutora.entidad_ejecutora_id')
        ->leftJoin('area_funcional', 'area_funcional.id', '=','area_funcional_entidad_ejecutora.area_funcional_id')
        ->leftJoin('catalogo','area_funcional.subprograma_presupuestario_id', '=', 'catalogo.id')
        ->WHERE('catalogo.deleted_at','=', null)
        ->WHERE('area_funcional.programa_presupuestario_id','=',$id)
        ->orderBy('catalogo.clave')
        ->DISTINCT()
        ->get();
        return response()->json($subProgramas,200);
    }
    public function getProyectos($id){
        $proyectos = DB::table('area_funcional_entidad_ejecutora')
        ->SELECT('catalogo.clave', 'catalogo.descripcion')
        ->leftJoin('entidad_ejecutora', 'entidad_ejecutora.id','=' , 'area_funcional_entidad_ejecutora.entidad_ejecutora_id')
        ->leftJoin('area_funcional', 'area_funcional.id', '=','area_funcional_entidad_ejecutora.area_funcional_id')
        ->leftJoin('catalogo','area_funcional.proyecto_presupuestario_id', '=', 'catalogo.id')
        ->WHERE('catalogo.deleted_at','=', null)
        ->WHERE('area_funcional.subprograma_presupuestario_id','=',$id)
        ->orderBy('catalogo.clave')
        ->DISTINCT()
        ->get();
        return response()->json($proyectos,200);
    }
    public function getLineaAccion($id){
        $linea = DB::table('area_funcional_entidad_ejecutora')
        ->SELECT('catalogo.clave','catalogo.descripcion')
        ->leftJoin('entidad_ejecutora','entidad_ejecutora.id', '=', 'area_funcional_entidad_ejecutora.entidad_ejecutora_id')
        ->leftJoin('area_funcional','area_funcional.id','=','area_funcional_entidad_ejecutora.area_funcional_id')
        ->leftJoin('catalogo','area_funcional.linea_accion_id', '=', 'catalogo.id')
        ->WHERE('catalogo.deleted_at','=', null)
        ->WHERE('entidad_ejecutora.ur_id','=',$id)
        ->orderBy('catalogo.clave')
        ->DISTINCT()
        ->get();
        return response()->json($linea,200);
    }
    public function getPartidas($id){
        $partidas = DB::table('partida_upp')
        ->SELECT('catalogo.clave', 'catalogo.descripcion')
        ->leftJoin('posicion_presupuestaria','partida_upp.posicion_presupuestaria_id','=','posicion_presupuestaria.id')
        ->leftJoin('catalogo','posicion_presupuestaria.partida_especifica_id','=','catalogo.id')
        ->WHERE('catalogo.deleted_at','=',null)
        ->WHERE('partida_upp.upp_id','=',$id)
        ->DISTINCT()
        ->get();
        return response()->json($partidas,200);
    }
    public function getPresupuestoAsignado(){
        $Totcalendarizado = 0;
        $disponible = 0;
        $presupuestoAsignado = DB::table('presupuesto_upp_asignado')
        ->SELECT(DB::raw('SUM(presupuesto_asignado) as totalAsignado'))->get();
        $calendarizados = DB::table('programacion_presupuesto')
        ->SELECT(DB::raw('enero + febrero + marzo + abril + mayo + junio + julio + agosto + septiembre + octubre + noviembre + diciembre as calendarizados'))->get();
        foreach ($calendarizados as $key => $value) {
            log::debug(json_encode($value));
            //$Totcalendarizado = $Totcalendarizado + $value;
        }
        //$disponible = $presupuestoAsignado - $Totcalendarizado;
        $response = [
            'presupuestoAsignado'=>$presupuestoAsignado,
            'disponible'=>$disponible,
            'Totcalendarizado'=>$Totcalendarizado
        ];

        return response()->json($response,200);
    }
    public function getConceptosClave(){
        $nom = array("Sector Público", 
                        "Sector Público Financiero/No Financiero",
                        "Sector Economía",
                        "Subsector Economía",
                        "Ente Público",
                        "Entidad Federativa",
                        "Región",
                        "Municipio",
                        "Localidad",
                        "Unidad Programática Presupuestal",
                        "Subsecretaría",
                        "Unidad Responsable",
                        "Finalidad",
                        "Función",
                        "Subfunción",
                        "Eje",
                        "Linea de Acción",
                        "Programa Sectorial",
                        "Tipología General",
                        "Programa Presupuestal",
                        "Subprograma Presupuestal",
                        "Proyecto Presupuestal",
                        "Mes de Afectación",
                        "Capítulo",
                        "Concepto",
                        "Partida Genérica",
                        "Partida Específica",
                        "Tipo de Gasto",
                        "Añp (Fondo del Ramo)",
                        "Etiquetado/No Etiquetado",
                        "Fuente de Financiamiento",
                        "Ramo",
                        "Fondo del Ramo",
                        "Capital/Interes",
                        "Proyecto de Obra",);
       
      $clave = DB::select("CALL conceptos_clave('2111116010230010072061523NAMM4XA7989601-ENE211011231101021000000')");
         $dataset=[];
        $i=0;
        foreach($clave as $key){
            $a = array($nom[$i],$key->clave,$key->concepto);
            $dataset[] =$a;
            $i++;
        }  
        return $dataset;
    }
}