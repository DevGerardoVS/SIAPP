<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Validator;
use Log;
use Illuminate\Validation\Rule;
use App\Models\calendarizacion\Metas;
use App\Imports\utils\FunFormats;

use Auth;

class MetasSheetImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $validator =Validator::make($rows->toArray(),
        [
            '*.upp'=>'required',
            '*.ur'=>'required',
            '*.prg'=>'required',
            '*.spr'=>'required',
            '*.py'=>'required',
            '*.fondo'=>'required',
            '*.cve_act'=>'required',
            '*.cve_cal'=>'required',
            '*.enero'=>'required',
            '*.febrero'=>'required',
            '*.marzo'=>'required',
            '*.abril'=>'required',
            '*.mayo'=>'required',
            '*.junio'=>'required',
            '*.julio'=>'required',
            '*.agosto'=>'required',
            '*.septiembre'=>'required',
            '*.octubre'=>'required',
            '*.noviembre'=>'required',
            '*.diciembre'=>'required',
            '*.cve_benef'=>'required',
            '*.nbeneficiarios'=>'required',
            '*.cve_um'=>'required',
        ]
        )->validate();

        Log::debug($validator);

    }
}