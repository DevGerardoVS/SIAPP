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
    public function getClaves(Request $request){
        $claves = DB::table('programacion_presupuesto')
        ->SELECT('programacion_presupuesto.id','clave_presupuestal', DB::raw ('SUM(enero + febrero + marzo + abril + mayo + junio + julio + agosto + septiembre + octubre + noviembre + diciembre) AS totalByClave'),'catalogo.clave','catalogo.descripcion')
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
        ->groupBy('catalogo.clave')
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
        ->leftJoin('catalogo','area_funcional.programa_presupuestario_id', '=', 'catalogo.id')
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
    

}