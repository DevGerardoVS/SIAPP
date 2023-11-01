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
use Log;

class Beneficiarios implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths,WithTitle,WithStyles,WithEvents
{
    protected $filas;

    public function collection(){
        $data = MetasHelper::beneficiarios();
        $newData = [];
       foreach ($data as $key) {
            $a = array(
                $key->id,
                $key->beneficiario,
                ""
            );
            $newData[] = $a;
        }

        $this->filas = count($newData)+1;
        return collect($newData);
    }

    public function title(): string
    {
        return 'Beneficiarios';
    }
    public function headings(): array
    {
        return [
            "CLAVE",
            "BENEFICIARIO",
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
            'B'=>25
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
