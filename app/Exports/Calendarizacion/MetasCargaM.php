<?php 
namespace App\Exports\Calendarizacion;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Exports\Calendarizacion\MetasIndex;
use App\Exports\Calendarizacion\Beneficiarios;
use App\Exports\Calendarizacion\UnidadMedida;
use App\Exports\Calendarizacion\TipoCalendar;
use App\Exports\Calendarizacion\ActividadesAdmon;



class MetasCargaM implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];

            $sheets[] = new MetasIndex();
            $sheets[] = new Beneficiarios();
            $sheets[] = new UnidadMedida();
            $sheets[] = new TipoCalendar();

        return $sheets;
    }

}
