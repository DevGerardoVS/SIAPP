<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;

class MetasImport implements WithMultipleSheets 
{
    use WithConditionalSheets;

    public function conditionalSheets(): array
    {
        mb_http_output('UTF-8');
        mb_internal_encoding('UTF-8');
        return [
            'Metas' => new MetasSheetImport()
        ];
    }
}
