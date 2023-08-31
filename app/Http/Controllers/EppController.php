<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EppController extends Controller
{
    public function index(){
        $dataSet = array();
        $listaUpp = DB::table('v_epp')->distinct()->get(['clv_upp','upp']);
        $listaAnio = DB::table('v_epp')->distinct()->get(['ejercicio']);
        $perfil = Auth::user()->id_grupo;
        //dd(Auth::user()->clv_upp);
        return view('epp/epp', 
            [
                'dataSet' => $dataSet,
                'listaUpp'=> $listaUpp,
                'perfil'=> $perfil,
                'anios'=> $listaAnio
            ]
        );
    }

    public function getEpp(Request $request){
        $perfil = Auth::user()->id_grupo;
        $upp = '000';
        $ur = '00';
        $anio = '0000';

        //OBTENER UPP
        if($perfil == 1){
            if($request->upp == '000') $upp = "null";
            else $upp = "'$request->upp'";
        }else if($perfil == 4){
            $upp = Auth::user()->clv_upp;
            $upp = "'$upp'";
        }

        //OBTENER UR
        if($request->upp == '000') $ur = "null";
        else{
            if($request->ur == '00') $ur = "null";
            else $ur = "'$request->ur'";
        }

        //OBTENER AÑO
        if($request->anio == '0000') $anio = "null";
        else $anio = "'$request->anio'";

        //OBTENER TABLA
        $data = DB::select('call sp_epp('.$upp.','.$ur.','.$anio.')');
        $dataSet = array();
        foreach($data as $d){
            $ds = array(
                $d->clasificacion_administrativa,
                $d->upp,
                $d->subsecretaria,
                $d->unidad_responsable,
                $d->finalidad,
                $d->funcion,
                $d->subfuncion,
                $d->eje,
                $d->linea_accion,
                $d->programa_sectorial,
                $d->tipologia_conac,
                $d->programa,
                $d->subprograma,
                $d->proyecto,
                $d->ejercicio
            );
            $dataSet[] = $ds;
        }

        return response()->json([
            "dataSet" => $dataSet,
            "catalogo" => "epp"
        ]);
    }

    public function getUR(Request $request){
        $listaUR = DB::table('v_epp')
            ->where('clv_upp','=',$request->upp)
            ->distinct()
            ->get(['clv_ur','ur']);

        return response()->json([
            'listaUR'=> $listaUR
        ]);
    }
}