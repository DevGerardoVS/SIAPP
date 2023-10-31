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
    protected $upp;

    function __construct($upp) {

        $this->upp= $upp;
    }
    public function collection(){
        $anio = DB::table('cierre_ejercicio_metas')->max('ejercicio');
        $data = DB::table('programacion_presupuesto')
        ->leftJoin('mml_cierre_ejercicio', 'mml_cierre_ejercicio.clv_upp', '=', 'programacion_presupuesto.upp')
        ->select(
            'upp AS clv_upp',
            'ur AS clv_ur',
            DB::raw('CONCAT(upp,subsecretaria,ur) AS entidad_ejecutora'),
            DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario) AS area_funcional'),
            DB::raw('programacion_presupuesto.fondo_ramo AS fondo'),
            DB::raw('IF(enero>=1,"",0) AS enero'),
            DB::raw('IF(febrero>=1,"",0) AS febrero'),
            DB::raw('IF(marzo>=1,"",0) AS marzo'),
            DB::raw('IF(abril>=1,"",0) AS abril'),
            DB::raw('IF(mayo>=1,"",0) AS mayo '),
            DB::raw('IF(junio>=1,"",0) AS junio'),
            DB::raw('IF(julio>=1,"",0) AS julio'),
            DB::raw('IF(agosto>=1,"",0) AS agosto'),
            DB::raw('IF(septiembre>=1,"",0) AS septiembre '),
            DB::raw('IF(octubre>=1,"",0) AS octubre'),
            DB::raw('IF(noviembre>=1,"",0) AS noviembre'),
            DB::raw('IF(diciembre>=1,"",0) AS diciembre')
        )
        ->where("programacion_presupuesto.upp", $this->upp)
        ->where('programacion_presupuesto.estado', 1)
        ->where('programacion_presupuesto.deleted_at', null)
        ->where('programacion_presupuesto.ejercicio', '=', $anio)
        ->where('mml_cierre_ejercicio.ejercicio', '=', $anio)
        ->where('mml_cierre_ejercicio.statusm',1)
        ->groupByRaw('clv_ur,fondo_ramo,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario')
        ->distinct();

    if (Auth::user()->id_grupo == 4) {
        $data = $data->leftJoin('cierre_ejercicio_metas', 'cierre_ejercicio_metas.clv_upp', '=', 'programacion_presupuesto.upp')
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
                $key->enero,
                $key->febrero,
                $key->marzo,
                $key->abril,
                $key->mayo,
                $key->junio,
                $key->julio,
                $key->agosto,
                $key->septiembre,
                $key->octubre,
                $key->noviembre,
                $key->diciembre,

            );
            $newData[] = $i;
        }
 
         $this->filas = count($newData)+1;
        return collect($newData);
    }

    public function title(): string
    {
        return 'claves PP';
    }
    public function headings(): array
    {
        return ["FINALIDAD", "FUNCION", "SUBFUNCION", "EJE", "L ACCION", "PRG SECTORIAL", "TIPO CONAC", "UPP", "UR", "PRG", "SPR", "PY", "FONDO" ,"ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE",];

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
            'D'  => ['font' => ['size' => 10]],
            'F'  => ['font' => ['size' => 10]],
            'G'  => ['font' => ['size' => 10]],
            'H'  => ['font' => ['size' => 10]],
            'I'  => ['font' => ['size' => 10]],
            'J'  => ['font' => ['size' => 10]],
            'K'  => ['font' => ['size' => 10]],
            'L'  => ['font' => ['size' => 10]],
            'M'  => ['font' => ['size' => 10]],
            'N'  => ['font' => ['size' => 10]],
            'O'  => ['font' => ['size' => 10]],
            'P'  => ['font' => ['size' => 10]],
            'Q'  => ['font' => ['size' => 10]],
            'R'  => ['font' => ['size' => 10]],
            'S'  => ['font' => ['size' => 10]],
            'T'  => ['font' => ['size' => 10]],
            'U'  => ['font' => ['size' => 10]],
            'V'  => ['font' => ['size' => 10]],
            'W'  => ['font' => ['size' => 10]],
            'X'  => ['font' => ['size' => 10]],
            'Y'  => ['font' => ['size' => 10]],

            
       ];
    }

    public function columnWidths():array{
        return[
            'A'  =>10,
            'B'  =>10,
            'C'  =>13,
            'D'  =>5,
            'F'  =>10,
            'G'  =>10,
            'H'  =>5,
            'I'  =>5,
            'J'  =>8,
            'K'  =>8,
            'L'  =>8,
            'M'  =>8,
            'N'  =>12,
            'O'  =>12,
            'P'  =>12,
            'Q'  =>12,
            'R'  =>12,
            'S'  =>12,
            'T'  =>12,
            'U'  =>12,
            'V'  =>12,
            'W'  =>12,
            'X'  =>12,
            'Y'  =>12,

        ];
    }
    public function registerEvents():array{
        return[
            AfterSheet::class=> function(AfterSheet $event){
                $sheet = $event -> sheet;
                $event->sheet->getDelegate()
                ->getStyle('A1:Y'.$this->filas)
                ->applyFromArray(['alignment'=>['wrapText'=>true]]);

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000'],
                        ],
                    ],
                ];
    
                $event->sheet->getStyle('A1:Y'.$this->filas)->applyFromArray($styleArray);
               },
             
        ];
    }

}
