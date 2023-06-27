<?php

namespace App\Exports;

use App\Models\ProgramacionPresupuesto;
use Maatwebsite\Excel\Concerns\FromCollection;

class ImportErrorsExport implements FromCollection
{
    protected $file;

    function __construct($file) { 
        $this->file =$file;

    }

    public function collection()
    {
        return ProgramacionPresupuesto::all();
    }
}
