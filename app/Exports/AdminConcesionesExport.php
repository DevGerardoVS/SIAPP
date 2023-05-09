<?php

namespace App\Exports;

use App\Helpers\AdminPolizasConcesionesHelper;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class AdminConcesionesExport implements FromCollection, ShouldAutoSize, WithHeadings, WithColumnWidths
{
    protected $request;

    function __construct($request) { 
        $this->request = $request;
    }

    public function collection() {
        $data = AdminPolizasConcesionesHelper::getPolizasConcesionQuery($this->request,true);
        
        return $data;
    }

    /**
     * Retorna un arreglo con los encabezados del excel en orden de las columnas
     * @return array Encabezados de los usuarios
     */
    public function headings(): array {
        return ["Concesión", "Placa", "Num. de serie", "RFC", "Tipo de servicio", "Fecha de registro de póliza", "Modalidad", "Num. de póliza", "Aseguradora", "Fecha de vencimiento de póliza", "Usuario de creación", "Estatus de pago"];
    }

    public function columnWidths(): array {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 15,
            'D' => 15,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 20,
            'K' => 20,
            'L' => 20,
        ];
    }
}