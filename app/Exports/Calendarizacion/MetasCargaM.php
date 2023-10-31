<?php 
namespace App\Exports\Calendarizacion;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Exports\Calendarizacion\MetasIndex;
use App\Exports\Calendarizacion\Beneficiarios;
use App\Exports\Calendarizacion\UnidadMedida;
use App\Exports\Calendarizacion\TipoCalendar;
use App\Exports\Calendarizacion\ActividadesPp;



class MetasCargaM implements WithMultipleSheets
{
    protected $upp;
    function __construct($upp) {

        $this->upp= $upp;
    }
    public function sheets(): array
    {
        $sheets = [];

            $sheets[] = new MetasIndex($this->upp);
            $sheets[] = new ActividadesPp($this->upp);
            $sheets[] = new Beneficiarios();
            $sheets[] = new UnidadMedida();
            $sheets[] = new TipoCalendar();

        return $sheets;
    }

}
