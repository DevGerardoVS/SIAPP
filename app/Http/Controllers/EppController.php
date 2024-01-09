<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Log;
use PDF;
use App\Exports\EppExport;
use Maatwebsite\Excel\Facades\Excel;

class EppController extends Controller
{
    public function index(){
        Controller::check_permission('viewGetEpp');
        $perfil = Auth::user()->id_grupo;
        $dataSet = array();
        $max_anio = DB::table('epp')->max('ejercicio');
        $listaUpp = DB::table('catalogo')
            ->select('clave as clv_upp','descripcion as upp')
            ->where('ejercicio', $max_anio)
            ->where('grupo_id', 6)
            ->orderBy('clv_upp')
            ->get();

        if($perfil == 5){
            $listaUpp = DB::table('uppautorizadascpnomina as u')
                ->join('catalogo as c', 'u.clv_upp', '=', 'c.clave')
                ->where('c.grupo_id', '=', 6)
                ->where('c.deleted_at')
                ->where('c.ejercicio', $max_anio)
                ->select('u.clv_upp','c.descripcion as upp')->distinct()
                ->orderBy('u.clv_upp')->get();
        }
        $listaAnio = DB::table('epp')->distinct()->where('deleted_at')
            ->orderBy('ejercicio','DESC')
            ->get(['ejercicio']);
        $perfil = Auth::user()->id_grupo;
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
        Controller::check_permission('getEpp');
        $perfil = Auth::user()->id_grupo;
        $upp = '000';
        $ur = '00';
        $anio = '0000';

        //OBTENER UPP
        if($perfil == 1 || $perfil == 3 || $perfil == 5){
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
        if($request->anio == '0000') $anio = DB::table('epp')->select('ejercicio')->max('ejercicio');
        else $anio = "'$request->anio'";

        //OBTENER TABLA
        $data = DB::select('call sp_epp(0,'.$upp.','.$ur.','.$anio.')');
        if($perfil == 5) $data = DB::select('call sp_epp(1,'.$upp.','.$ur.','.$anio.')');
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
        $listaUR = DB::table('epp as e')
            ->join('catalogo as c1', 'e.upp_id', '=', 'c1.id')
            ->join('catalogo as c2', 'e.ur_id', '=', 'c2.id')
            ->where('e.ejercicio', '=', $request->anio)
            ->where('c1.clave', '=', $request->upp)
            ->distinct()->orderBy('c2.clave')
            ->get(['c2.clave as clv_ur','c2.descripcion as ur']);

        return response()->json([
            'listaUR'=> $listaUR
        ]);
    }

    public function getUPP(Request $request){
        $perfil = Auth::user()->id_grupo;

        $listaUPP = '';
        if($perfil == 5){
            $listaUPP = DB::table('catalogo as c')
                ->join('uppautorizadascpnomina as u', 'u.clv_upp', '=', 'c.clave')
                ->where('c.ejercicio','=',$request->anio)
                ->where('c.grupo_id', 6)
                ->orderBy('c.clave')
                ->get(['c.clave as clv_upp','c.descripcion as upp']);
        }
        else {
            $listaUPP = DB::table('catalogo')
                ->where('ejercicio','=',$request->anio)
                ->where('grupo_id', 6)
                ->orderBy('clv_upp')
                ->get(['clave as clv_upp','descripcion as upp']);
        }

        return response()->json([
            'listaUPP'=> $listaUPP
        ]);
    }

    public function exportExcelEPP(Request $request){
        /*Si no coloco estas lineas Falla*/
        ob_end_clean();
        ob_start();
        /*Si no coloco estas lineas Falla*/
        $b = array(
            "username" => Auth::user()->username,
            "accion" => 'Descargar EPP Excel',
            "modulo" => 'EPP'
        );
        Controller::bitacora($b);
        $anio = $request->anio;
        $nombre = "Lista de EPP $anio.xlsx";
        return Excel::download(new EppExport($request), $nombre, \Maatwebsite\Excel\Excel::XLSX);
    }
}