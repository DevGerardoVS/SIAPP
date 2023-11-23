<?php
namespace App\Exports\Calendarizacion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use App\Helpers\Calendarizacion\MetasHelper;
class MetasIndex implements FromCollection, ShouldAutoSize, WithHeadings, WithTitle, WithStyles, WithEvents,WithColumnFormatting
{
    protected $filas;
    protected $upp;

    function __construct($upp) {

        $this->upp= $upp;
    }

    public function collection()
    {

        $data = MetasHelper::MetasIndex($this->upp);
        $dataSet = [];
        foreach ($data as $key) {
            $area = str_split($key->area_funcional);
            $entidad = str_split($key->entidad_ejecutora);
            $spr = '' . strval($area[10]) . strval($area[11]) . strval($area[12]) . '';
            if($spr=='UUU'){
                
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
                $spr,
                '' . strval($area[13]) . strval($area[14]) . strval($area[15]) . '',
                $key->fondo,
                $key->clv_actadmon,
                $key->mir_act,
                $key->actividad,
                '0',
                'Acumulativa',
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                3,
                '',
                '',
                '',
                '',
                '',
                ''
            );
            }else{
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
                $spr,
                '' . strval($area[13]) . strval($area[14]) . strval($area[15]) . '',
                $key->fondo,
                $key->clv_actadmon,
                $key->mir_act,
                $key->actividad,
                '',
                '',
                $key->enero?:'0',
                $key->febrero?:'0',
                $key->marzo?:'0',
                $key->abril?:'0',
                $key->mayo?:'0',
                $key->junio?:'0',
                $key->julio?:'0',
                $key->agosto?:'0',
                $key->septiembre?:'0',
                $key->octubre?:'0',
                $key->noviembre?:'0',
                $key->diciembre?:'0',
                '',
                '',
                '',
                '',
                '',
                '',
            );}
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
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
            'M' => NumberFormat::FORMAT_TEXT,
            'N' => NumberFormat::FORMAT_TEXT,
            'O' => NumberFormat::FORMAT_TEXT,
            'P' => NumberFormat::FORMAT_TEXT,
            'R' => NumberFormat::FORMAT_TEXT,
        ];
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