<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use App\Helpers\Calendarizacion\MetasHelper;
use App\Http\Controllers\Calendarizacion\MetasController;
use Auth;
use Log;


class MetasExport implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths
{
    protected $filas;
    protected $upp;
    protected $ur;
    protected $anio;

    function __construct($upp,$ur,$anio)
    {
        $this->upp = $upp;
        $this->ur = $ur;
        $this->anio = $anio;
    }
    public function collection()
    {
        try {
            if(Auth::user()->id_grupo == 4){
                $dataSet = MetasHelper::actividades($this->upp,0,$this->anio);
            $this->filas = count($dataSet);
                $newDataset = [];
                foreach ($dataSet as $key) {
                    $area = str_split($key->area);
                    $i = array(
                        $key->id,
                        $area[0],
                        $area[1],
                        $area[2],
                        $area[3],
                        '' . strval($area[4]) . strval($area[5]) . '',
                        $area[6],
                        $area[7],
                        $key->upp,
                        $key->clv_ur,
                        $key->clv_pp,
                        '' . strval($area[10]) . strval($area[11]) . strval($area[12]) . '',
                        '' . strval($area[13]) . strval($area[14]) . strval($area[15]) . '',
                        $key->fondo,
                        $key->actividad,
                        $key->tipo,
                        $key->total,
                        $key->cantidad_beneficiarios,
                        $key->beneficiario,
                        $key->unidad_medida,
                    );
                    $newDataset[] = $i;
                }
            }else{
                $newDataset = MetasController::getActivAdm($this->anio);
            }
            
            return collect($newDataset);
        } catch (\Throwable $th) {
            Log::debug($th);
        }
 
    }

    /**
     * Retorna un arreglo con los encabezados del excel en orden de las columnas
     * @return array Encabezados de los usuarios
     */
    public function headings(): array
    {
        return ["ID", "FINALIDAD","FUNCION","SUBFUNCION","EJE","L ACCION","PRG SECTORIAL","TIPO CONAC","UPP","UR", "Programa", "Subprograma", "proyecto",'Fondo', "Actividad","Tipo Actividad","Meta anual", "# Beneficiarios", "beneficiarios","U de medida"];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 10,
            'C' => 10,
            'D' => 10,
            'E' => 10,
            'F' => 25,
            'G' => 20,
            'H' => 10,
            'I' => 10,
            'J' => 10,
            'K' => 10,
            'L' => 10,
            'M' => 10,
            'N' => 10,
            'O' => 10,
            'P' => 10,
            'Q' => 10,
            'R' => 10,
            'S' => 10,
            'T' => 10,
        ];
    }
    public function registerEvents():array{
        return[
            AfterSheet::class=> function(AfterSheet $event){
                $sheet = $event -> sheet;
                $event->sheet->getDelegate()
                ->getStyle('A1:S'.$this->filas)
                ->applyFromArray(['alignment'=>['wrapText'=>true]]);

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000'],
                        ],
                    ],
                ];
    
                $event->sheet->getStyle('A1:S'.$this->filas)->applyFromArray($styleArray);
               },
             
        ];
    }
}
