<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Log;
use Illuminate\Validation\Rule;
use App\Models\calendarizacion\Metas;

class MetasSheetImport implements ToCollection, WithHeadingRow, WithValidation
{

    protected $existentes = [];
    protected $decimal = [];
    protected $exisClave = [];
    protected $activo = [];
    protected $tipo = '';
   /*  public function prepareForValidation($row, $index)
    {
        $existentes []= "Ya existe esta informacion para el ejercicio de techos financieros";
        $decimal []= "El monto debe ser entero";
        $exisClave []= "No se puede cargar la informacion existe clave presupuestal registrada";

    } */
    public function collection(Collection $rows)
    {
        if (count($rows)>0) {
            return ["El excel esta vacio"];
        } else {
            $aux = [];
            foreach ($rows as $key => $value) {
                Log::debug($value);
            }
        }
   /*      for ($i=1; $i <count($rows); $i++) {
            $a=[];
            Log::debug($rows[$i]);
            foreach ($rows[$i] as $key => $value) {
                if($key==0 || $key == 5){
                    $text=preg_replace('([^0-9])', '', $value);
                    $a[] = $text;
                }else{
                    if ($key != 28 && $key != 24 && $key !=9 && $key !=7) {
                        $a[] = $value;
                    } 
                }
                
            }
            $aux[] = $a;
        } */

/*         for ($i = 0; $i < count($aux); $i++) {
            
            $meta=Metas::create([
                'actividad_id' =>$aux[$i][6] ,
                'clv_fondo' => $aux[$i][5],
                'estatus' => 0,
                'tipo' => $aux[$i][8],
                'beneficiario_id' => $aux[$i][21],
                'unidad_medidad_id' =>$aux[$i][23] ,
                'cantidad_beneficiarios' => $aux[$i][22],
                'total' =>$aux[$i][10],
                'enero' =>$aux[$i][11],
                'febrero' =>$aux[$i][12],
                'marzo' =>$aux[$i][13],
                'abril' =>$aux[$i][14],
                'mayo' =>$aux[$i][15],
                'junio' =>$aux[$i][16],
                'julio' =>$aux[$i][17],
                'agosto' =>$aux[$i][18],
                'septiembre' =>$aux[$i][18],
                'octubre' => $aux[$i][19],
                'noviembre' => $aux[$i][20],
                'diciembre' => $aux[$i][21],
            ]);


            Log::debug($meta);

        } */
    }
    public function rules(): array
    {
        return [
            '*.upp'=>'required|string',
            '*.ur'=>'required|string',
            '*.prg'=>'required|string',
            '*.spr'=>'required|string',
            '*.py'=>'required|string',
            '*.fondo'=>'required|string',
            '*.cve_act'=>'required|string',
            '*.cve_cal'=>'required|string',
            '*.total_metas'=>'required|string',
            '*.enero'=>'required|string',
            '*.febrero'=>'required|string',
            '*.marzo'=>'required|string',
            '*.abril'=>'required|string',
            '*.mayo'=>'required|string',
            '*.junio'=>'required|string',
            '*.julio'=>'required|string',
            '*.agosto'=>'required|string',
            '*.septiembre'=>'required|string',
            '*.octubre'=>'required|string',
            '*.noviembre'=>'required|string',
            '*.diciembre'=>'required|string',
            '*.cve_benef'=>'required|string',
            '*.nbeneficiarios'=>'required|string',
            '*.cve_um'=>'required|string',
        ];
         
    }

    public function customValidationMessages()
    {
        return [
            '*.upp'=>'Falta el campo upp',
            '*.ur'=>'Falta el campo ur',
            '*.prg'=>'Falta el campo prg',
            '*.spr'=>'Falta el campo spr',
            '*.py'=>'Falta el campo py',
            '*.fondo'=>'Falta el campo fondo',
            '*.cve_act'=>'Falta el campo clave de Actividad',
            '*.cve_cal'=>'Falta el campo tipo de calendarop',
            '*.total_metas'=>'Falta el campo total anual',
            '*.enero'=>'Falta el campo enero',
            '*.febrero'=>'Falta el campo febrero',
            '*.marzo'=>'Falta el campo marzo',
            '*.abril'=>'Falta el campo abril',
            '*.mayo'=>'Falta el campo mayo',
            '*.junio'=>'Falta el campo junio',
            '*.julio'=>'Falta el campo julio',
            '*.agosto'=>'Falta el campo agosto',
            '*.septiembre'=>'Falta el campo septiembre',
            '*.octubre'=>'Falta el campo octubre',
            '*.noviembre'=>'Falta el campo noviembre',
            '*.diciembre'=>'Falta el campo diciembre',
            '*.cve_benef'=>'Falta el campo clave beneficiario',
            '*.nbeneficiarios'=>'Falta el campo numero de beneficiarios',
            '*.cve_um'=>'Falta el campo clave unidad de medida',
        ];
    }
}
