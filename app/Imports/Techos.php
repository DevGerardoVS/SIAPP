<?php

namespace App\Imports;

use App\Models\calendarizacion\TechosFinancieros;
use App\Models\ProgramacionPresupuesto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

use Illuminate\Support\Facades\Validator;
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
    protected $negativo = [];
    protected $decimal = [];
    protected $exisClave = [];
    protected $activo = [];
    protected $presupuestro = [];
    protected $tipo = '';
    public function prepareForValidation($row, $index)
    {
        $negativo[] = "El monto debe ser positivo y mayor a cero";
        $decimal[] = "El monto debe ser entero";
        $exisClave[] = "No se puede cargar la informacion existe clave presupuestal registrada";

        // validar que el ejercicio y la upp estan activos
        $ejercicio = DB::table('epp')->select('ejercicio')->where('ejercicio', $row['ejercicio'])->groupBy('ejercicio')->get();
        if (count($ejercicio) == 0) {
            $activo[] = "El ejercicio " . $row['ejercicio'] . " no esta activo";
            $row = null;
            return $activo;
        }
        //validar que es numero, entero y positivo
        if ($row['operativo'] != null) {
            if (!is_numeric($row['operativo']) || !is_int(($row['operativo']))) {
                $row = null;
                return $decimal;
            }

            if ($row['operativo'] <= 0) {
                $row = null;
                return $negativo;
            } else {
                DB::table('temp_techos')->insert([
                    'clv_upp' => $row['upp'],
                    'clv_fondo' => $row['fondo'],
                    'tipo' => 'Operativo',
                    'presupuesto' => $row['operativo'],
                    'ejercicio' => $row['ejercicio']
                ]);
            }
        }
        if ($row['recursos_humanos'] != NULL) {
            if (!is_numeric($row['recursos_humanos']) || !is_int(($row['recursos_humanos']))) {
                $row = null;
                return $decimal;
            }
            if ($row['recursos_humanos'] <= 0) {
                $row = null;
                return $negativo;
            } else {
                DB::table('temp_techos')->insert([
                    'clv_upp' => $row['upp'],
                    'clv_fondo' => $row['fondo'],
                    'tipo' => 'RH',
                    'presupuesto' => $row['recursos_humanos'],
                    'ejercicio' => $row['ejercicio']
                ]);
            }
        }
        //validar que no exista clave presupuestaria registrada 
        if ($row['recursos_humanos'] != 0) {
            $tipo = 'RH';
            $val_clave = ProgramacionPresupuesto::select()
                ->where('upp', $row['upp'])
                ->where('fondo_ramo', $row['fondo'])
                ->where('ejercicio', $row['ejercicio'])
                ->where('tipo', $tipo)
                ->count();
            if ($val_clave != 0) {
                $row = null;
                return $exisClave;
            }
        }
        //valiar que tengan presupuesto las upp para los dos tipos
        $op = DB::table('temp_techos')->select('presupuesto')
            ->where('clv_upp', $row['upp'])
            ->where('tipo', 'Operativo')
            ->where('presupuesto', '>', 0)
            ->where('ejercicio', $row['ejercicio'])
            ->sum('presupuesto');
        $rh = DB::table('temp_techos')->select('*')
            ->where('clv_upp', $row['upp'])
            ->where('tipo', 'RH')
            ->where('presupuesto', '>', 0)
            ->where('ejercicio', $row['ejercicio'])
            ->sum('presupuesto');
        if ($op != 0 && $rh == 0) {
            $row = null;
            $presupuesto[] = 'Cada upp debe tener presupuesto para Recursos Humanos';
            return $presupuesto;

        }
        if ($op == 0 && $rh != 0) {
            $row = null;
            $presupuesto[] = 'Cada upp debe tener presupuesto para Operativo';
            return $presupuesto;
        }

        return $row;
    }

    public function model(array $row)
    {
        //validar que no exciste la combinacion 
        $val_op = TechosFinancieros::select('*')
            ->where('clv_upp', $row['upp'])
            ->where('clv_fondo', $row['fondo'])
            ->where('tipo', 'Operativo')
            ->where('ejercicio', $row['ejercicio'])
            ->count();
        $val_rh = TechosFinancieros::select('*')
            ->where('clv_upp', $row['upp'])
            ->where('clv_fondo', $row['fondo'])
            ->where('tipo', 'RH')
            ->where('ejercicio', $row['ejercicio'])
            ->count();

        if (!empty($row)) {
            $user = Auth::user()->username;
            $monto = 0;

            if ($val_rh != 0) {
                DB::table('techos_financieros')
                    ->where('clv_upp', $row['upp'])
                    ->where('clv_fondo', $row['fondo'])
                    ->where('tipo', 'RH')
                    ->where('ejercicio', $row['ejercicio'])
                    ->update([
                        'presupuesto' => $row['recursos_humanos'],
                        'updated_user' => $user,
                    ]);
            }
            if ($val_op != 0) {
                DB::table('techos_financieros')
                    ->where('clv_upp', $row['upp'])
                    ->where('clv_fondo', $row['fondo'])
                    ->where('tipo', 'Operativo')
                    ->where('ejercicio', $row['ejercicio'])
                    ->update([
                        'presupuesto' => $row['operativo'],
                        'updated_user' => $user,
                    ]);
            } else {
                if ($row['operativo'] != NULL && $row['operativo'] > 0 && $row['recursos_humanos'] != NULL && $row['recursos_humanos'] > 0 && $row['upp'] != 'UPP' && $row['fondo'] != 'FO') {
                    $ejercicio = $row['ejercicio'];
                    $upp = $row['upp'];
                    $fondo = $row['fondo'];
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
                    if ($row['operativo'] != NULL) {
                        $ejercicio = $row['ejercicio'];
                        $upp = $row['upp'];
                        $fondo = $row['fondo'];
                        $tipo = 'Operativo';
                        $monto = $row['operativo'];
                    }
                    if ($row['recursos_humanos'] != NULL) {
                        $ejercicio = $row['ejercicio'];
                        $upp = $row['upp'];
                        $fondo = $row['fondo'];
                        $tipo = 'RH';
                        $monto = $row['recursos_humanos'];
                    }
                    if ($monto != 0 && $row['upp'] != 'UPP' && $row['fondo'] != 'FO') {
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
        }


    }

    public function rules(): array
    {
        return [
            '*.tipo' => Rule::in(['RH', 'Operativo']),
            '*.upp' => [
                'required', Rule::exists('catalogo', 'clave'), Rule::notIn(['0'])
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
        ];
    }
}