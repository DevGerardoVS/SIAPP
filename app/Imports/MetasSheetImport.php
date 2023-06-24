<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Log;

class MetasSheetImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        for ($i=0; $i <count($rows); $i++) {
            Log::debug(utf8_encode($rows[$i][7]));
           
        }

    }
}
