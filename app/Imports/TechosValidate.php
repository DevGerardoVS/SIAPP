<?php

namespace App\Imports;

use App\Models\calendarizacion\TechosFinancieros;
use App\Models\ProgramacionPresupuesto;

use Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TechosValidate
{
    public static function validate($row)
    {
        if (count($row) >= 1) {
            $pre = [];
            $index = 1;
            for ($i = 0; $i < count($row); $i++) {
                $index++;
                if ($row[$i][3] == null && $row[$i][4] == null) {
                    $pre[] = [
                        'val' => '1'
                    ];
                }
                $existC = TechosValidate::existClave($row[$i][0]);
                if (count($existC)) {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => 'No se puede cargar la informacion existe clave presupuestal registrada para el fondo: ' . $row[$i][0] . '. Revisa la fila: "' . $index . '"'
                    );
                    return $error;
                }
                $ejercicio = DB::table('epp')->select('ejercicio')->where('ejercicio', $row[$i][0])->groupBy('ejercicio')->get();
                if (count($ejercicio) == 0) {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => 'El ejercicio  ' . $row[$i][0] . ' no esta activo. Revisa la fila: "' . $index . '"'
                    );
                    return $error;
                }
                $upp = DB::table('epp')->select('catalogo.clave')->join('catalogo', 'catalogo.id', '=', 'epp.upp_id')->where('epp.ejercicio', $row[$i][0])->where('catalogo.clave', $row[$i][1])->groupBy('catalogo.clave')->get();
                if (count($upp) == 0) {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => 'La upp  ' . $row[$i][1] . ' no es valida. Revisa la fila: "' . $index . '"'
                    );
                    return $error;
                }
                $fondo = DB::table('fondo')->select('clv_fondo_ramo')->where('clv_fondo_ramo', $row[$i][2])->get();
                if (count($fondo) == 0) {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => 'El fondo  ' . $row[$i][2] . ' no es valido. Revisa la fila: "' . $index . '"'
                    );
                    return $error;
                }
                if ($row[$i][3] != '') {
                    if (!is_numeric($row[$i][3])) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'El presupuesto debe ser numerico. Revisa la fila: "' . $index . '"'
                        );
                        return $error;
                    }
                    if (!is_int(($row[$i][3]))) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'El monto debe ser entero. Revisa la fila: "' . $index . '"'
                        );
                        return $error;
                    }
                    if ($row[$i][3] == 0) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'El monto debe ser mayor a cero. Revisa la fila: "' . $index . '"'
                        );
                        return $error;
                    }
                    if ($row[$i][3] < 0) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'El monto debe ser positivo. Revisa la fila: "' . $index . '"'
                        );
                        return $error;
                    }
                }
                if ($row[$i][4] != '') {
                    if (!is_numeric($row[$i][4])) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'El presupuesto debe ser numerico. Revisa la fila: "' . $index . '"'
                        );
                        return $error;
                    }
                    if (!is_int(($row[$i][4]))) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'El monto debe ser entero. Revisa la fila: "' . $index . '"'
                        );
                        return $error;
                    }
                    if ($row[$i][4] == 0) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'El monto debe ser mayor a cero. Revisa la fila: "' . $index . '"'
                        );
                        return $error;
                    }
                    if ($row[$i][4] <= 0) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'El monto debe ser positivo. Revisa la fila: "' . $index . '"'
                        );
                        return $error;
                    }
                }
            }
            if (count($row) != count($pre)) {
                $val_tech = TechosFinancieros::select('ejercicio')
                    ->where('ejercicio', $row[0][0])
                    ->count();
                if ($val_tech > 0) {
                    TechosFinancieros::where('ejercicio', $row[0][0])->delete();
                    $techos = TechosValidate::insert($row);
                    return $techos;
                } else {
                    $techos = TechosValidate::insert($row);
                    return $techos;
                }
            } else {
                $error = array(
                    "icon" => 'error',
                    "title" => 'Error',
                    "text" => 'La plantilla no tiene presupuestos'
                );
                return $error;

            }

        } else {
            $error = array(
                "icon" => 'error',
                "title" => 'Error',
                "text" => 'El documento esta vacio'
            );
            return $error;
        }

    }

    public static function existClave($ejercicio)
    {
        $val_clave = ProgramacionPresupuesto::select('upp')
            ->where('ejercicio', $ejercicio)
            ->get();
        return $val_clave;
    }

    public static function insert($row)
    {
        foreach ($row as $key) {
            if (!empty($key)) {
                $user = Auth::user()->username;
                $monto = 0;
                if ($key[3] != NULL && $key[3] > 0 && $key[4] != NULL && $key[4] > 0 && $key[3] != 'UPP' && $key[2] != 'FO') {
                    $ejercicio = $key[0];
                    $upp = $key[1];
                    $fondo = $key[2];
                    TechosFinancieros::create([
                        'clv_upp' => $upp,
                        'clv_fondo' => $fondo,
                        'tipo' => 'Operativo',
                        'presupuesto' => $key[3],
                        'ejercicio' => $ejercicio,
                        'updated_user' => $user,
                        'created_user' => $user
                    ]);
                    TechosFinancieros::create([
                        'clv_upp' => $upp,
                        'clv_fondo' => $fondo,
                        'tipo' => 'RH',
                        'presupuesto' => $key[4],
                        'ejercicio' => $ejercicio,
                        'updated_user' => $user,
                        'created_user' => $user
                    ]);

                } else {
                    if ($key[3] != NULL) {
                        $ejercicio = $key[0];
                        $upp = $key[1];
                        $fondo = $key[2];
                        $tipo = 'Operativo';
                        $monto = $key[3];
                    }
                    if ($key[4] != NULL) {
                        $ejercicio = $key[0];
                        $upp = $key[1];
                        $fondo = $key[2];
                        $tipo = 'RH';
                        $monto = $key[4];
                    }
                    if ($monto != 0 && $key[3] != 'UPP' && $key[2] != 'FO') {
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
        return 'done';
    }
}