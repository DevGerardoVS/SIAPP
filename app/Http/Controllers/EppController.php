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
        $listaUpp = DB::table('v_epp')->select('clv_upp','upp')->distinct()->orderBy('clv_upp')->get();
        if($perfil == 5){
            $listaUpp = DB::table('uppautorizadascpnomina as u')
                ->leftjoin('v_epp as ve', 'u.clv_upp', '=', 've.clv_upp')
                ->select('u.clv_upp','ve.upp')->distinct()
                ->where('ve.deleted_at')->orderBy('u.clv_upp')->get();
        }
        $listaAnio = DB::table('v_epp')->distinct()->where('deleted_at')->get(['ejercicio']);
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
        if($request->anio == '0000') $anio = DB::table('v_epp')->select('ejercicio')->max('ejercicio');
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
        $listaUR = DB::table('v_epp')
            ->where('clv_upp','=',$request->upp)
            ->where('ejercicio','=',$request->anio)
            ->distinct()
            ->get(['clv_ur','ur']);

        return response()->json([
            'listaUR'=> $listaUR
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
        return Excel::download(new EppExport($anio), $nombre, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportPdfEPP(Request $request){
        return view('epp/eppPDFA', 
            [
                'dataSet' => $dataSet
            ]
        );

        ini_set('max_execution_time', 5000);
        ini_set('memory_limit', '10240M');
        $data = DB::table('v_epp')
                ->select(DB::raw("
                    CONCAT(
                        clv_sector_publico,
                        clv_sector_publico_f,
                        clv_sector_economia,
                        clv_subsector_economia,
                        clv_ente_publico
                    ) AS clas_admin,
                    concat(clv_upp,' ',
                    upp) upp,
                    concat(clv_subsecretaria,' ',
                    subsecretaria) subsecretaria,
                    concat(clv_ur,' ',
                    ur) ur,
                    concat(clv_finalidad,' ',
                    finalidad) finalidad,
                    concat(clv_funcion,' ',
                    funcion) funcion,
                    concat(clv_subfuncion,' ',
                    subfuncion) subfuncion,
                    concat(clv_eje,' ',
                    eje) eje,
                    concat(clv_linea_accion,' ',
                    linea_accion) linea_accion,
                    concat(clv_programa_sectorial,' ',
                    programa_sectorial) programa_sectorial,
                    concat(clv_tipologia_conac,' ',
                    tipologia_conac) tipologia_conac,
                    concat(clv_programa,' ',
                    programa) programa,
                    concat(clv_subprograma,' ',
                    subprograma) subprograma,
                    concat(clv_proyecto,' ',
                    proyecto) proyecto
                "))
                ->where('ejercicio', $request->anio)
                ->get();
        view()->share('data', $data);
        $pdf = PDF::loadView('epp.eppPDF',$data)->setPaper('a3', 'landscape');
        $b = array(
            "username" => Auth::user()->username,
            "accion" => 'Descargar EPP PDF',
            "modulo" => 'EPP'
        );
        Controller::bitacora($b);
        $nombre = "Listado de EPP $request->anio.pdf";
        return $pdf->download($nombre);
    }
}