<?php

namespace App\Imports;

use App\Models\ProgramacionPresupuesto;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class PresupuestosImport implements ToModel, WithProgressBar, SkipsEmptyRows
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $tipo;
        $row[17]=='UUU' ? $tipo = 'RH' : $tipo = 'Operativo';

        return new ProgramacionPresupuesto([
            //
            'clasificacion_administrativa' => $row[1], 
            'entidad_federativa' => $row[2],
            'region' => $row[3],
            'municipio' => $row[4],
            'localidad' => $row[5],
            'upp' => $row[6],
            'subsecretaria' => $row[7],
            'ur' => $row[8],
            'finalidad' => $row[9],
            'funcion' => $row[10],
            'subfuncion' => $row[11],
            'eje' => $row[12],
            'linea_accion' => $row[13],
            'programa_sectorial' => $row[14],
            'tipologia_conac' => $row[15],
            'programa_presupuestario' => $row[16],
            'subprograma_presupuestario' => $row[17],
            'proyecto_presupuestario' => $row[18],
            'periodo_presupuestal' => $row[19],
            'posicion_presupuestaria' => $row[34],
            'tipo_gasto' => $row[21],
            'anio' => $row[22],
            'etiquetado' => $row[23],
            'fuente_financiamiento' => $row[24],
            'ramo' => $row[25],
            'fondo_ramo' => $row[26],
            'capital' => $row[27],
            'proyecto_obra' => $row[31],
            'ejercicio' => 2023,
            'enero' => $row[36],
            'febrero' => $row[37],
            'marzo' => $row[38],
            'abril' => $row[39],
            'mayo' => $row[40],
            'junio' => $row[41],
            'julio' => $row[42],
            'agosto' => $row[43],
            'septiembre' => $row[44],
            'octubre' => $row[45],
            'noviembre' => $row[46],
            'diciembre' => $row[47],
            'total' => $row[35],
            'estado' => 0,
            'tipo' => $tipo,
            'created_user' => 'SEEDER',
        ]);
    }
}
