<?php 
namespace App\Exports\Calendarizacion;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Exports\Calendarizacion\MetasIndexDelegacion;
use App\Exports\Calendarizacion\Beneficiarios;
use App\Exports\Calendarizacion\UnidadMedida;
use App\Exports\Calendarizacion\TipoCalendar;
use App\Exports\Calendarizacion\ActividadesPpDelegacion;



class MetasCargaMDelegacion implements WithMultipleSheets
{
    protected $upp;
    public function sheets(): array
    {
        $sheets = [];

            $sheets[] = new MetasIndexDelegacion();
            $sheets[] = new ActividadesPpDelegacion();
        return $sheets;
    }

}
