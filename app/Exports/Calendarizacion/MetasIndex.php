<?php
namespace App\Exports\Calendarizacion;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Schema\Blueprint;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\Auth;
use App\Models\Mir;
use DB;
use Log;

class MetasIndex implements FromCollection, ShouldAutoSize, WithHeadings, WithTitle, WithStyles, WithEvents
{
    protected $filas;

    public function collection()
    {

        $anio = DB::table('cierre_ejercicio_metas')->max('ejercicio');
        $claves = DB::table('catalogo')
            ->select('clave AS sub')
            ->where('deleted_at', null)
            ->where('grupo_id', 20)
            ->get();
        $c = [];
        foreach ($claves as $key) {
            $c[] = $key->sub;
        }
        $data3 = DB::table('mml_mir')
            ->leftJoin('mml_avance_etapas_pp', 'mml_avance_etapas_pp.clv_upp', '=', 'mml_mir.clv_upp')
            ->leftJoin('programacion_presupuesto AS pp', 'pp.upp', '=', 'mml_mir.clv_upp')
            ->leftJoin('v_epp', 'v_epp.id', '=', 'mml_mir.id_epp')
            ->select(
                'mml_mir.clv_upp',
                'mml_mir.entidad_ejecutora',
                'mml_mir.area_funcional',
                DB::raw('"N/A" AS clv_actadmon'),
                DB::raw('mml_mir.id AS mir_act'),
                DB::raw('indicador AS actividad'),
                DB::raw('"" AS fondo'),
            )
            ->where(function ($query) use ($c) {
                foreach ($c as $sub) {
                    $query->where('v_epp.clv_subprograma', '!=', $sub);
                }
            })
            ->where('mml_mir.deleted_at', null)
            ->where('mml_mir.nivel', 11)
            ->where('mml_avance_etapas_pp.estatus', 3)
            ->where('pp.estado', 1)
            ->where('mml_mir.ejercicio', $anio)
            ->where('pp.ejercicio', $anio)
            ->groupByRaw('mml_mir.id_epp')
            ->distinct();
        if (Auth::user()->id_grupo == 4) {
            $upp = Auth::user()->clv_upp;
            $data3 = $data3->leftJoin('cierre_ejercicio_metas', 'cierre_ejercicio_metas.clv_upp', '=', 'mml_mir.clv_upp')
                ->where('mml_mir.clv_upp', $upp)
                ->where('cierre_ejercicio_metas.deleted_at', null)
                ->where('cierre_ejercicio_metas.ejercicio', $anio)
                ->where('cierre_ejercicio_metas.estatus', 'Abierto');
        }
        $prueba = DB::table('programacion_presupuesto AS pp')
            ->leftJoin('mml_avance_etapas_pp', 'mml_avance_etapas_pp.clv_upp', '=', 'pp.upp')
            ->join('mml_mir', function (JoinClause $join) {
                $join->on('pp.upp', '=', 'mml_mir.clv_upp');
    })
        ->select(
            'mml_mir.clv_upp',
            DB::raw('mml_mir.entidad_ejecutora'),
            DB::raw('mml_mir.area_funcional'),
            DB::raw('"N/A" AS clv_actadmon'),
            DB::raw('mml_mir.id AS mir_act'),
            DB::raw('mml_mir.indicador AS actividad'),
            DB::raw('pp.fondo_ramo AS fondo'),
        )
        ->where(function ($query) use ($c) {
            foreach ($c as $sub) {
                $query->where('pp.subprograma_presupuestario', '!=', $sub);
            }
        })
        ->where('pp.estado', 1)
        ->where('pp.deleted_at', null)
        ->where('mml_mir.nivel', 11)
        ->where('pp.ejercicio', '=', $anio)
        ->where('mml_avance_etapas_pp.estatus', 3)
        ->groupByRaw('fondo_ramo,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario')
        ->distinct();

    if (Auth::user()->id_grupo == 4) {
        $upp = Auth::user()->clv_upp;
        $prueba = $prueba->leftJoin('cierre_ejercicio_metas', 'cierre_ejercicio_metas.clv_upp', '=', 'pp.upp')
            ->where("pp.upp", $upp)
            ->where('cierre_ejercicio_metas.deleted_at', null)
            ->where('cierre_ejercicio_metas.ejercicio', $anio)
            ->where('cierre_ejercicio_metas.estatus', 'Abierto');
    }
        $checkpp = $prueba->get();
    Schema::create('pptemp', function (Blueprint $table) {
        $table->temporary();
        $table->increments('id');
        $table->string('clv_upp', 25)->nullable(false);
        $table->string('entidad_ejecutora', 55)->nullable(false);
        $table->string('area_funcional', 55)->nullable(false);
        $table->string('clv_actadmon', 55)->nullable(false);
        $table->string('mir_act', 55)->nullable(false);
        $table->string('actividad', 55)->nullable(false);
        $table->string('fondo', 55)->nullable(false);
    });

        $data2 = DB::table('programacion_presupuesto AS pp')
            ->leftJoin('mml_avance_etapas_pp', 'mml_avance_etapas_pp.clv_upp', '=', 'pp.upp')
            ->leftJoin('v_epp', 'v_epp.clv_upp', '=', 'pp.upp')
            ->select(
                'pp.upp AS clv_upp',
                DB::raw('CONCAT(pp.upp,pp.subsecretaria,pp.ur) AS entidad_ejecutora'),
                DB::raw('CONCAT(pp.finalidad,pp.funcion,pp.subfuncion,pp.eje,pp.linea_accion,pp.programa_sectorial,pp.tipologia_conac,pp.programa_presupuestario,pp.subprograma_presupuestario,pp.proyecto_presupuestario) AS area_funcional'),
                DB::raw('"ot" AS clv_actadmon'),
                DB::raw('"N/A" AS mir_act'),
                DB::raw('"" AS actividad'),
                DB::raw('pp.fondo_ramo AS fondo'),
            )
            ->where('v_epp.con_mir', 0)
            ->where('pp.estado', 1)
            ->where('pp.deleted_at', null)
            ->where('pp.ejercicio', '=', $anio)
            ->where('mml_avance_etapas_pp.estatus', 3)
            ->where(function ($query) use ($c) {
                foreach ($c as $sub) {
                    $query->where('pp.subprograma_presupuestario', '!=', $sub);
                }
            })
            ->groupByRaw('pp.fondo_ramo,pp.finalidad,pp.funcion,pp.subfuncion,pp.eje,pp.linea_accion,pp.programa_sectorial,pp.tipologia_conac,pp.programa_presupuestario,pp.subprograma_presupuestario,pp.proyecto_presupuestario')
            ->distinct();
        if (Auth::user()->id_grupo == 4) {
            $upp = Auth::user()->clv_upp;
            $data2 = $data2->leftJoin('cierre_ejercicio_metas', 'cierre_ejercicio_metas.clv_upp', '=', 'pp.upp')
                ->where("pp.upp", $upp)
                ->where('cierre_ejercicio_metas.deleted_at', null)
                ->where('cierre_ejercicio_metas.ejercicio', $anio)
                ->where('cierre_ejercicio_metas.estatus', 'Abierto');
        }
        $data2 = $data2->get();
        foreach ($data2 as $key) {
            DB::table('pptemp')->insert(get_object_vars($key));
            
        }
        $mirdatos = $data3->get();

        $newdata2 = DB::table('pptemp')
            ->leftJoin('mml_mir', 'mml_mir.clv_upp', '=', 'pptemp.clv_upp')
            ->select(
                'pptemp.clv_upp',
                'pptemp.entidad_ejecutora',
                'pptemp.area_funcional',
                'pptemp.clv_actadmon',
                'pptemp.mir_act',
                'pptemp.actividad',
                'pptemp.fondo'
            )
            ->where('mml_mir.nivel', 11)
            ->where(function ($query) use ($mirdatos) {
                foreach ($mirdatos as $sub) {
                    $query->where('pptemp.entidad_ejecutora', '!=', $sub->entidad_ejecutora);
                }
            })
            ->distinct();
        $data = DB::table('programacion_presupuesto')
            ->leftJoin('mml_avance_etapas_pp', 'mml_avance_etapas_pp.clv_upp', '=', 'programacion_presupuesto.upp')
            ->leftJoin('catalogo', 'catalogo.clave', '=', 'programacion_presupuesto.subprograma_presupuestario')
            ->select(
                'upp AS clv_upp',
                DB::raw('CONCAT(upp,subsecretaria,ur) AS entidad_ejecutora'),
                DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario) AS area_funcional'),
                DB::raw('IFNULL(catalogo.id,"N/A") AS clv_actadmon'),
                DB::raw('"N/A"AS mir_act'),
                DB::raw('IFNULL(catalogo.descripcion," ") AS actividad'),
                DB::raw('programacion_presupuesto.fondo_ramo AS fondo'),
            )
            ->where('programacion_presupuesto.estado', 1)
            ->where('programacion_presupuesto.deleted_at', null)
            ->where('programacion_presupuesto.ejercicio', '=', $anio)
            ->where('catalogo.deleted_at', null)
            ->where('catalogo.grupo_id', 20)
            ->where('mml_avance_etapas_pp.estatus', 3)
            ->groupByRaw('fondo_ramo,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario')
            ->unionAll( $data3 )
            ->unionAll($newdata2)
            ->distinct();

        if (Auth::user()->id_grupo == 4) {
            $upp = Auth::user()->clv_upp;
            $data = $data->leftJoin('cierre_ejercicio_metas', 'cierre_ejercicio_metas.clv_upp', '=', 'programacion_presupuesto.upp')
                ->where("programacion_presupuesto.upp", $upp)
                ->where('cierre_ejercicio_metas.deleted_at', null)
                ->where('cierre_ejercicio_metas.ejercicio', $anio)
                ->where('cierre_ejercicio_metas.estatus', 'Abierto');
        }
        $data = $data->get();
        $dataSet = [];
        foreach ($data as $key) {
            $area = str_split($key->area_funcional);
            $entidad = str_split($key->entidad_ejecutora);
            
            $i = array(
                $area[0],
                $area[1],
                $area[2],
                $area[3],
                '' . strval($area[4]) . strval($area[5]) . '',
                $area[6],
                $area[7],
                '' . strval($entidad[0]) . strval($entidad[1]) . strval($entidad[2]) . '',
                '' . strval($entidad[4]) . strval($entidad[5]) . '',
                '' . strval($area[8]) . strval($area[9]) . '',
                '' . strval($area[10]) . strval($area[11]) . strval($area[12]) . '',
                '' . strval($area[13]) . strval($area[14]) . strval($area[15]) . '',
                $key->fondo,
                $key->clv_actadmon,
                $key->mir_act,
                $key->actividad,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',

                '',
                '',
                '',
                '',
                ''
            );
            $dataSet[] = $i;
        }
        $this->filas = 0;
        return collect($dataSet);
    }

    public function title(): string
    {
        return 'Metas';
    }
    public function headings(): array
    {
        return ["FINALIDAD", "FUNCION", "SUBFUNCION", "EJE", "L ACCION", "PRG SECTORIAL", "TIPO CONAC", "UPP", "UR", "PRG", "SPR", "PY", "FONDO", "CVE_ACTADMON", "MIR_ACT", "ACTIVIDAD", "CVE_CAL", "TIPO_CALENDARIO", "ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE", "CVE_BENEF", "BENEFICIARIO", "N.BENEFICIARIOS", "CVE_UM", "UNIDAD_MEDIDA"];

    }
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],

            // Styling an entire column.
            'A' => ['font' => ['size' => 10]],
            'B' => ['font' => ['size' => 10]],
            'C' => ['font' => ['size' => 10]],
            'D' => ['font' => ['size' => 10]]
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $event->sheet->getDelegate()
                    ->getStyle('A1:AH' . $this->filas)
                    ->applyFromArray(['alignment' => ['wrapText' => true]]);

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000'],
                        ],
                    ],
                ];

                $event->sheet->getStyle('A1:AH' . $this->filas)->applyFromArray($styleArray);
            },

        ];
    }

}