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
class MetasIndex implements FromCollection, ShouldAutoSize, WithHeadings,WithTitle,WithStyles,WithEvents
{
    protected $filas;

    public function collection(){
        $data = [];
        $this->filas = count($data);
        return collect($data);
    }

    public function title(): string
    {
        return 'Metas';
    }
    public function headings(): array
    {
        return ["UPP", "UR", "PRG", "SPR", "PY", "FONDO", "CVE_ACT", "ACTIVIDAD", "CVE_CAL", "TIPO_CALENDARIO", "TOTAL_METAS", "ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE", "CVE_BENEF", "BENEFICIARIO", "CVE_UM", "UNIDAD_MEDIDA"];

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
                ->getStyle('A1:AA'.$this->filas)
                ->applyFromArray(['alignment'=>['wrapText'=>true]]);

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000'],
                        ],
                    ],
                ];
    
                $event->sheet->getStyle('A1:AA'.$this->filas)->applyFromArray($styleArray);
               },
             
        ];
    }

}
