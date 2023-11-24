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
use App\Helpers\Calendarizacion\MetasHelper;

class TipoCalendar implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths,WithTitle,WithStyles,WithEvents
{
    protected $filas;
    protected $upp;
    function __construct($upp) {

        $this->upp= $upp;
    }

    public function collection(){
        $tipo = MetasHelper::tCalendario($this->upp);
        $this->filas = count($tipo);
        return collect($tipo);
    }

    public function title(): string
    {
        return 'TipoCalendario';
    }
    public function headings(): array
    {
        return [
        "CLAVE",
        "TIPO",
        ""];
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
            'A'=>8,
            'B'=>15
        ];
    }
    public function registerEvents():array{
        return[
            AfterSheet::class=> function(AfterSheet $event){
                $sheet = $event -> sheet;
                $event->sheet->getDelegate()
                ->getStyle('A1:B'.$this->filas)
                ->applyFromArray(['alignment'=>['wrapText'=>true]]);

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000'],
                        ],
                    ],
                ];
    
                $event->sheet->getStyle('A1:B'.$this->filas)->applyFromArray($styleArray);
               },
             
        ];
    }

}
