<?php 
namespace App\Exports\Calendarizacion;
use App\Models\MmlMirCatalogo;
use Auth;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use App\Helpers\Calendarizacion\MetasHelper;
use Log;

class ActividadesPp implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths,WithTitle,WithStyles,WithEvents
{
    protected $filas;

    public function collection(){
        $anio = DB::table('cierre_ejercicio_metas')->max('ejercicio');
        $data = DB::table('programacion_presupuesto')
        ->leftJoin('mml_avance_etapas_pp', 'mml_avance_etapas_pp.clv_upp', '=', 'programacion_presupuesto.upp')
        ->leftJoin('catalogo', 'catalogo.clave', '=', 'programacion_presupuesto.subprograma_presupuestario')
        ->select(
            'upp AS clv_upp',
            'ur AS clv_ur',
            DB::raw('CONCAT(upp,subsecretaria,ur) AS entidad_ejecutora'),
            DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario) AS area_funcional'),
            DB::raw('programacion_presupuesto.fondo_ramo AS fondo'),
        )
        ->where('programacion_presupuesto.estado', 1)
        ->where('programacion_presupuesto.deleted_at', null)
        ->where('programacion_presupuesto.ejercicio', '=', $anio)
        ->where('catalogo.deleted_at', null)
        ->where('catalogo.grupo_id', 20)
        ->where('mml_avance_etapas_pp.estatus', 3)
        ->groupByRaw('fondo_ramo,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario')
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
        $newData = [];
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
            );
            $newData[] = $i;
        }
 
         $this->filas = count($newData)+1;
        return collect($newData);
    }

    public function title(): string
    {
        return 'Actividades de Administrativas';
    }
    public function headings(): array
    {
        return [
            "CLAVE",
            "ACTIVIDAD",
            ""
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
            
            // Styling an entire column.
            'A'  => ['font' => ['size' => 10]],
            'B'  => ['font' => ['size' => 10]],
            'C'  => ['font' => ['size' => 10]]
       ];
    }

    public function columnWidths():array{
        return[
            'A'=>10,
            'B'=>14,
            'C'=>25
        ];
    }
    public function registerEvents():array{
        return[
            AfterSheet::class=> function(AfterSheet $event){
                $sheet = $event -> sheet;
                $event->sheet->getDelegate()
                ->getStyle('A1:C'.$this->filas)
                ->applyFromArray(['alignment'=>['wrapText'=>true]]);

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000'],
                        ],
                    ],
                ];
    
                $event->sheet->getStyle('A1:C'.$this->filas)->applyFromArray($styleArray);
               },
             
        ];
    }

}
