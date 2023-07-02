<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdmonCapturaController extends Controller
{
    //
    public function index(){
        $dataSet = array();
        $anio_activo = DB::select('SELECT ejercicio FROM cierre_ejercicio_claves WHERE activos = 1 LIMIT 1');
        $anio = $anio_activo[0]->ejercicio;
        $upps = DB::select('SELECT c.clave, c.descripcion FROM catalogo c join cierre_ejercicio_claves cec on c.clave = cec.clv_upp WHERE grupo_id = 6 AND activos = 1 ORDER BY clave ASC');
        return view("captura.admonCaptura", [
            'dataSet' => json_encode($dataSet),
            'anio' => $anio,
            'upps' => $upps,
        ]);
    }  

    public function clavesPresupuestarias(Request $request){
        $estatus = $request->estatus;
        $dataSet = array();
        $array_where = [];
        
        if($estatus != null){
            array_push($array_where,['cec.estatus','=',$estatus]);
        }
        
        $data = DB::table("catalogo as c")
        ->join("cierre_ejercicio_claves as cec", function($join){
            $join->on("cec.clv_upp", "=", "c.clave");
        })
        ->select("cec.clv_upp", "c.descripcion", "cec.estatus", "cec.updated_at", "cec.updated_user")
        ->where("c.grupo_id", "=", 6)
        ->where("cec.activos", "=", 1)
        ->where($array_where)
        ->orderBy("cec.estatus","desc")
        ->orderBy("cec.clv_upp","asc")
        ->get();

        foreach ($data as $d) {
            
            $ds = array($d->clv_upp, $d->descripcion, Carbon::parse($d->updated_at)->format('d/m/Y'), $d->estatus, $d->updated_user);
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }

    public function metasActividades(Request $request){
        $estatus = $request->estatus;
        $dataSet = array();
        $array_where = [];
        
        if($estatus != null){
            array_push($array_where,['cem.estatus','=',$estatus]);
        }

        
        $data = DB::table("catalogo as c")
        ->join("cierre_ejercicio_metas as cem", function($join){
            $join->on("cem.clv_upp", "=", "c.clave");
        })
        ->select("cem.clv_upp", "c.descripcion", "cem.estatus", "cem.updated_at", "cem.updated_user")
        ->where("c.grupo_id", "=", 6)
        ->where("cem.activos", "=", 1)
        ->where($array_where)
        ->orderBy("cem.estatus","desc")
        ->orderBy("cem.clv_upp","asc")
        ->get();

        
        foreach ($data as $d) {
            
            $ds = array($d->clv_upp, $d->descripcion, Carbon::parse($d->updated_at)->format('d/m/Y') , $d->estatus, $d->updated_user);
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }

    public function update(Request $request){
        $upp = $request->upp_filter;
        $modulo = $request->modulo_filter;
        $habilitar = $request->capturaRadio;
        $usuario = Auth::user()->username;
        $checar_upp = '';
        if($upp != null) $checar_upp = "AND clv_upp = $upp";

        try {
            DB::beginTransaction();
            
            $actualizar = str_contains($request->modulo_filter,',') ? DB::update("update $modulo set cec.estatus = '$habilitar', cec.updated_user = '$usuario', cem.estatus = '$habilitar', cem.updated_user = >'$usuario' WHERE cec.activos = 1 AND cem.activos = 1 $checar_upp") : DB::update("update $modulo set estatus = '$habilitar', updated_user = '$usuario' WHERE activos = 1 $checar_upp");

            DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            dd('error');
        }
       
        // dd($comprobacion);
        // try {//-----------------------------
        //     DB::beginTransaction();
        //     // $upp_filter
        //     // $modulo_filter
        //     // $capturaRadio}

        //      //archivo
        //     $fileExt = $request->archivo->getClientOriginalExtension();
        //     $municipio = Municipio::findOrFail(Auth::user()->idMunicipio);
        //     $fondo = Fondo::findOrFail($comprobacion->idFondo);
        //     $tipo = TipoDocumento::findOrFail($comprobacion->idTipo);
        //     $mes = Mese::findOrFail($comprobacion->idMes);

        //     $fileName = str_replace(" ", "_", $municipio->nombre . "_" . $fondo->acronimo . "_". $mes->nombre . "_" . $comprobacion->anio . "_" . $tipo->nombre . "." . $fileExt);
        //     $ruta = $request->archivo->storeAs('public/comprobaciones', $fileName);
            
        //     $prevData = Comprobacion::where('id', $auxId)->first();//* Bitacora
            
        //     $comprobacion->idMunicipio = Auth::user()->idMunicipio;
        //     $doesExist = Storage::disk('s3')->has($ruta);  
        //     if($doesExist == 1 && $doesExist != null && $doesExist != ''){
        //         $comprobacion->rutaArchivo = $ruta;
        //     }
        //     $comprobacion->nombreArchivo = pathinfo($fileName, PATHINFO_FILENAME);
        //     $comprobacion->nombreOriginalArchivo = request()->archivo->getClientOriginalName();
        //     $comprobacion->tipoArchivo = request()->archivo->extension();

        //     $comprobacion->save();

        //     $newData = Comprobacion::where('id', $auxId)->first();//* Bitacora

        //     BitacoraHelper::crearBitacora("Editar Comprobacion", "Modificacion", "comprobaciones", $prevData, $newData);

        //     $successMSG = "La comprobacion fue editada.";

        //     //$this->llenarPlantilla();
            
        //     DB::commit();

        //     return redirect()->route($auxRoute)->withSuccess($successMSG);
        // }
        // catch (\Exception $e) {
        //     DB::rollBack();
        //     dd('error');
        //     return back()->withErrors(['msg'=>'error']);
        // }
    }
}
