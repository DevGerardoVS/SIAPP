<?php 
namespace App\Exports\Calendarizacion;
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
class MetasIndex implements FromCollection, ShouldAutoSize, WithHeadings,WithTitle,WithStyles,WithEvents
{
    protected $filas;

    public function collection(){

        $anio = DB::table('cierre_ejercicio_metas')->max('ejercicio');
        Log::debug($anio);
        $data = DB::table('programacion_presupuesto')
        ->leftJoin('mml_mir', 'mml_mir.clv_upp', '=', 'programacion_presupuesto.upp')
        ->leftJoin('catalogo', 'catalogo.clave', '=', 'programacion_presupuesto.subprograma_presupuestario')
			->select(
				'upp AS clv_upp',
				DB::raw('CONCAT(upp,subsecretaria,ur) AS entidad_ejecutora'),
				DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario) AS area_funcional'),
				'programacion_presupuesto.fondo_ramo AS fondo',
                DB::raw('IFNULL(catalogo.id,"NULL") AS clv_actadmon'),
                DB::raw('IFNULL(mml_mir.id,"NULL") AS mir_act'),
                DB::raw('IFNULL(mml_mir.indicador,IFNULL(catalogo.descripcion,mml_mir.indicador)) AS actividad'),
                )
			->where('programacion_presupuesto.deleted_at', null)
			->where('programacion_presupuesto.ejercicio', '=', $anio)
            ->where('mml_mir.deleted_at', '=', $anio)
            ->where('mml_mir.nivel', '=', 11)
            ->where('mml_mir.ejercicio', '=', $anio)
            ->where('catalogo.deleted_at', null)
            ->where('catalogo.grupo_id', 20)
			->groupByRaw('fondo_ramo,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario')
			->distinct();
        if( Auth::user()->id_grupo==4){
            $upp = Auth::user()->clv_upp;
            $data = $data->where("programacion_presupuesto.upp",$upp)
            ->where('mml_mir.clv_upp', '=', $upp);
        }
        $data = $data->get();
        Log::debug($data);
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
                '',
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
                '',

				
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
        return ["FINALIDAD","FUNCION","SUBFUNCION","EJE","L ACCION","PRG SECTORIAL","TIPO CONAC","UPP", "UR", "PRG", "SPR", "PY", "FONDO", "CVE_ACTADMON","MIR_ACT", "ACTIVIDAD", "CVE_CAL", "TIPO_CALENDARIO", "ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE", "CVE_BENEF", "BENEFICIARIO","N.BENEFICIARIOS" ,"CVE_UM", "UNIDAD_MEDIDA"];

    }
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
            
            // Styling an entire column.
            'A'  => ['font' => ['size' => 10]],
            'B'  => ['font' => ['size' => 10]],
            'C'  => ['font' => ['size' => 10]],
            'D'  => ['font' => ['size' => 10]]
       ];
    }
    public function registerEvents():array{
        return[
            AfterSheet::class=> function(AfterSheet $event){
                $sheet = $event -> sheet;
                $event->sheet->getDelegate()
                ->getStyle('A1:AH'.$this->filas)
                ->applyFromArray(['alignment'=>['wrapText'=>true]]);

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000'],
                        ],
                    ],
                ];
    
                $event->sheet->getStyle('A1:AH'.$this->filas)->applyFromArray($styleArray);
               },
             
        ];
    }

}
