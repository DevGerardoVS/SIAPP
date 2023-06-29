<?php

namespace App\Imports;

use App\Models\calendarizacion\TechosFinancieros;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class Techos implements ToModel, WithValidation, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
     public function prepareForValidation($row,$index)
    {
        //validar que no exciste la combinacion 
        $val_op= TechosFinancieros::select()
        ->where('clv_upp',$row['upp'])
        ->where('clv_fondo',$row['fondo'])
        ->where('ejercicio',$row['ejercicio'])
        ->where('presupuesto',$row['operativo'] != 0)
        ->count();
        if($val_op > 1){
            $row['clv_upp']=NULL;
        }
        $val_rh= TechosFinancieros::select()
        ->where('clv_upp',$row['upp'])
        ->where('clv_fondo',$row['fondo'])
        ->where('ejercicio',$row['ejercicio'])
        ->where('presupuesto',$row['recursos_humanos'] != 0)
        ->count();
        if($val_rh > 1){
            $row['clv_upp']=NULL;
        }
        Log::debug("final");
        return $row;
    }

    public function model(array $row)
    {
        Log::debug($row);
        $user = Auth::user()->username;
        $tipo = '';
        $monto = 0;
        //if (($row[3] != '' && $row[3] > 0 && is_float($row[3]) != 1) && ($row[4]!= '' && $row[3] > 0 && is_float($row[4]) != 1) && $row[1] != 'UPP' && $row[2] != 'FO') {
            if ($row['operativo'] != '' && $row['operativo'] > 0 && $row['recursos_humanos'] != '' &&$row['recursos_humanos'] > 0 && $row['upp'] != 'UPP' && $row['fondo'] != 'FO') {
            $ejercicio = $row['ejercicio'];
            $upp = $row['upp'];
            $fondo = $row['fondo'];
            Log::debug($ejercicio."-".$upp."-".$fondo);
            TechosFinancieros::create([
                'clv_upp' => $upp,
                'clv_fondo' => $fondo,
                'tipo' => 'Operativo',
                'presupuesto' => $row['operativo'],
                'ejercicio' => $ejercicio,
                'updated_user' => $user,
                'created_user' => $user
            ]);
            TechosFinancieros::create([
                'clv_upp' => $upp,
                'clv_fondo' => $fondo,
                'tipo' => 'RH',
                'presupuesto' => $row['recursos_humanos'],
                'ejercicio' => $ejercicio,
                'updated_user' => $user,
                'created_user' => $user
            ]);

        } else {
            if ($row['operativo'] != '' && is_float($row['operativo']) != 1) {
                $ejercicio = $row['ejercicio'];
                $upp = $row['upp'];
                $fondo = $row['fondo'];
                $tipo = 'Operativo';
                $monto = $row['operativo'];
            }
            if ($row['recursos_humanos'] != '' && is_float($row['recursos_humanos']) != 1) {
                $ejercicio = $row['ejercicio'];
                $upp = $row['upp'];
                $fondo = $row['fondo'];
                $tipo = 'RH';
                $monto = $row['recursos_humanos'];
            }
            if ($tipo != '' && $monto != 0 && $row['upp'] != 'UPP' && $row['fondo'] != 'FO') {
                TechosFinancieros::create([
                    'clv_upp' => $upp,
                    'clv_fondo' => $fondo,
                    'tipo' => $tipo,
                    'presupuesto' => $monto,
                    'ejercicio' => $ejercicio,
                    'updated_user' => $user,
                    'created_user' => $user
                ]);
            }
        }

    }

    public function rules(): array
    {
        return [
            '*.tipo' => Rule::in(['RH', 'Operativo']),
            '*.upp' => [
                'required'
            ],

            '*.ejercicio' => 'required|integer',
            '*.fondo' => 'required|string',

        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.ejercicio' => 'Falta ejercicio',
            '*.upp.exists' => 'El valor de upp asignado no es valido',
            '*.upp.notIn' => 'No se pueden registrar las claves porque no esta autorizada la upp',
            '*.fondo' => 'Debe registrar la clave de fondo',
            '*.operativo' => 'El valor debe ser entero',
            '*.recursos_humanos' => 'El valor debe ser entero',
        ];
    }
}