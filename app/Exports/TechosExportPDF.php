<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;

use App\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;


class TechosExportPDF implements FromView{

    use Exportable;

    public function __construct( int $ejercicio){
        $this->ejercicio = $ejercicio;
        return $this;
    }

    public function view(): View
    {
        /* $data = DB::table('techos_financieros as tf')
            ->select('tf.clv_upp','vee.upp as descPre','tf.tipo','tf.clv_fondo','f.fondo_ramo','tf.presupuesto','tf.ejercicio')
            ->leftJoinSub('select distinct clv_upp, upp, ejercicio as Ej from v_epp','vee','tf.clv_upp','=','vee.clv_upp')
            ->leftJoinSub('select distinct clv_fondo_ramo, fondo_ramo from fondo','f','tf.clv_fondo','=','f.clv_fondo_ramo')
            ->where('tf.deleted_at','=',null)
            ->where('tf.ejercicio','=',$this->ejercicio)
            ->where('vee.Ej','=',$this->ejercicio)
            ->orderBy('vee.clv_upp','asc')
        ->get(); */

        $data = DB::table('techos_financieros as tf')
            ->select('tf.clv_upp','ve.upp as descPre','tf.tipo','tf.clv_fondo','c.descripcion as fondo_ramo','tf.presupuesto','tf.ejercicio')
            ->leftJoin('catalogo as c','tf.clv_fondo','=','c.clave')
            ->leftJoin('v_epp as ve','tf.clv_upp','=','ve.clv_upp')
            ->where('tf.deleted_at','=',null)
            ->where('tf.ejercicio','=',intval($this->ejercicio))
            ->where('ve.ejercicio','=',intval($this->ejercicio))
            ->where('c.grupo_id','=','FONDO DEL RAMO')
            ->orderBy('tf.clv_upp','asc')
            ->distinct()
        ->get();

        return view('calendarizacion.techos.plantillaPDF', [
            'data' => $data
        ]);
    }
}