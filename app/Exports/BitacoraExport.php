<?php

namespace App\Exports;

use App\Models\Bitacora;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use App\Http\Controllers\Administracion\BitacoraController;

class BitacoraExport implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths
{
    /**
* @return \Illuminate\Support\Collection
    
*/
    protected $anio;
    protected $mes;

    
    function __construct($anio,$mes){
        $this->anio = $anio;
        $this->mes = $mes;

    }

    public function collection()
    {
        $bitacora = BitacoraController::getBitacora($this->anio,$this->mes);
        return collect($bitacora);
    }

    /**
     * Retorna un arreglo con los encabezados del excel en orden de las columnas
     * @return array Encabezados de los usuarios
     */
    public function headings(): array
    {
        return ["Nombre Usuario","Movimiento o Acción","Módulo", "Ip Origen", "Fecha Movimiento","Fecha/Hora Creación"];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 15,
            'C' => 25,
            'D' => 10,
            'E' => 15,
            'F' => 20
        ];
    }
    
    
    
    
   
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

}
