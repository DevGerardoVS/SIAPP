<?php
namespace App\Exports\Calendarizacion;

use Illuminate\Database\Query\JoinClause;
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
            ->leftJoin('programacion_presupuesto', 'programacion_presupuesto.upp', '=', 'mml_mir.clv_upp')
            ->select(
                'mml_mir.id',
                'mml_mir.clv_upp',
                'mml_mir.clv_ur',
                'mml_mir.entidad_ejecutora',
                'mml_mir.area_funcional',
                DB::raw('"NULL" AS clv_actadmon'),
                DB::raw('mml_mir.id AS mir_act'),
                DB::raw('indicador AS actividad'),
                DB::raw('programacion_presupuesto.fondo_ramo AS fondo'),
            )
            ->where('mml_mir.deleted_at', null)
            ->where('mml_mir.nivel', 11)
            ->where('mml_avance_etapas_pp.estatus', 3)
            ->where('programacion_presupuesto.estado', 1)
            ->where('mml_mir.ejercicio', $anio)
            ->groupByRaw('programacion_presupuesto.fondo_ramo,entidad_ejecutora,area_funcional')
            ->distinct();
        if (Auth::user()->id_grupo == 4) {
            $upp = Auth::user()->clv_upp;
            $data3 = $data3->leftJoin('cierre_ejercicio_metas', 'cierre_ejercicio_metas.clv_upp', '=', 'mml_mir.clv_upp')
                ->where('mml_mir.clv_upp', $upp)
                ->where('cierre_ejercicio_metas.deleted_at', null)
                ->where('cierre_ejercicio_metas.ejercicio', $anio)
                ->where('cierre_ejercicio_metas.estatus', 'Abierto');
        }

        $data2 = DB::table('programacion_presupuesto')
            ->leftJoin('mml_avance_etapas_pp', 'mml_avance_etapas_pp.clv_upp', '=', 'programacion_presupuesto.upp')
            ->joinSub($data3, 'mir', function (JoinClause $join) {
                $join->on('mir.clv_upp', '=', 'programacion_presupuesto.upp');
            })
            ->select(
                'upp AS clv_upp',
                DB::raw('CONCAT(upp,subsecretaria,ur) AS entidad_ejecutora'),
                DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario) AS area_funcional'),
                DB::raw('"NULL" AS clv_actadmon'),
                DB::raw('IFNULL(mir.id,"NULL") AS mir_act'),
                DB::raw('IFNULL(mir.actividad,"NULL") AS actividad'),
                DB::raw('programacion_presupuesto.fondo_ramo AS fondo'),
            )
            ->where('mir.clv_ur', '=', 'programacion_presupuesto.ur')
            ->where('programacion_presupuesto.estado', 1)
            ->where('programacion_presupuesto.deleted_at', null)
            ->where('programacion_presupuesto.ejercicio', '=', $anio)
            ->where('mml_avance_etapas_pp.estatus', 3)
            ->where(function ($query) use ($c) {
                foreach ($c as $sub) {
                    $query->where('subprograma_presupuestario', '!=', $sub);
                }
            })
            ->groupByRaw('fondo_ramo,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario')
            ->distinct();
        if (Auth::user()->id_grupo == 4) {
            $upp = Auth::user()->clv_upp;
            $data2 = $data2->leftJoin('cierre_ejercicio_metas', 'cierre_ejercicio_metas.clv_upp', '=', 'programacion_presupuesto.upp')
                ->where("programacion_presupuesto.upp", $upp)
                ->where('cierre_ejercicio_metas.deleted_at', null)
                ->where('cierre_ejercicio_metas.ejercicio', $anio)
                ->where('cierre_ejercicio_metas.estatus', 'Abierto');
        }

        $data = DB::table('programacion_presupuesto')
            ->leftJoin('mml_avance_etapas_pp', 'mml_avance_etapas_pp.clv_upp', '=', 'programacion_presupuesto.upp')
            ->leftJoin('catalogo', 'catalogo.clave', '=', 'programacion_presupuesto.subprograma_presupuestario')
            ->select(
                'upp AS clv_upp',
                DB::raw('CONCAT(upp,subsecretaria,ur) AS entidad_ejecutora'),
                DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario) AS area_funcional'),
                DB::raw('IFNULL(catalogo.id,"NULL") AS clv_actadmon'),
                DB::raw('"NULL"AS mir_act'),
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
            //->unionAll($data3)
            ->unionAll($data2)
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