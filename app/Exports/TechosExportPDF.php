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
        $data = DB::table('techos_financieros as tf')
            ->select('tf.clv_upp','vee.upp as descPre','tf.tipo','tf.clv_fondo','f.fondo_ramo','tf.presupuesto','tf.ejercicio')
            ->leftJoinSub('select distinct clv_upp, upp, ejercicio as Ej from v_epp','vee','tf.clv_upp','=','vee.clv_upp')
            ->leftJoinSub('select distinct clv_fondo_ramo, fondo_ramo from fondo','f','tf.clv_fondo','=','f.clv_fondo_ramo')
            ->where('tf.ejercicio','=',$this->ejercicio)
            ->where('vee.Ej','=',$this->ejercicio)
            ->get();

        return view('calendarizacion.techos.plantillaPDF', [
            'data' => $data
        ]);
    }
}