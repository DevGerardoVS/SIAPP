<?php

namespace App\Imports;
use Illuminate\Support\Facades\DB;
use App\Models\ProgramacionPresupuesto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\calendarizacion\clasificacion_geografica;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Log;


class ClavePresupuestaria implements ToModel,WithHeadingRow, WithBatchInserts,WithChunkReading,SkipsOnFailure
{
    use RemembersRowNumber;
    use Importable,SkipsFailures;

    public function model(array $row)
    {
        $currentRowNumber = $this->getRowNumber();
        return new ProgramacionPresupuesto([
          'clasificacion_administrativa'  =>  $row['admconac'],
          'entidad_federativa'  => $row['ef'],
          'region'  => $row['reg'],
          'municipio'  => $row['mpio'],
          'localidad'  => $row['loc'],
          'upp'    => $row['upp'],
          'subsecretaria'    => $row['subsecretaria'],
          'ur'    => $row['ur'],
          'finalidad'    => $row['finalidad'],
          'funcion'    => $row['funcion'],
          'subfuncion'    => $row['subfuncion'],
          'eje'    => $row['eg'],
          'linea_accion'    => $row['pt'],
          'programa_sectorial'    => $row['ps'],
          'tipologia_conac'    => $row['sprconac'],
          'programa_presupuestario'    => $row['prg'],
          'subprograma_presupuestario'    => $row['spr'],
          'proyecto_presupuestario'   => $row['py'],
          'periodo_presupuestal'    =>  '01-ENE',
          'posicion_presupuestaria'    => $row['idpartida'],
          'tipo_gasto'    => $row['tipogasto'],
          'anio'    => $row['ano'], //no detecta la ñ
          'etiquetado' => $row['no_etiquetado_y_etiquetado'], 
          'fuente_financiamiento' => $row['fconac'],
          'ramo' => $row['ramo'],
          'fondo_ramo' => $row['fondo'],
          'capital' => $row['ci'],
          'proyecto_obra' => $row['obra'],
          'ejercicio' =>  2023, //quitar hardcode despues de probar
          'fondo_ramo'    => $row['fondo'],
          'enero'    => $row['enero'],
          'febrero'    => $row['febrero'],
          'marzo'    => $row['marzo'],
          'abril'    => $row['abril'],
          'mayo'    => $row['mayo'],
          'junio'    => $row['junio'],
          'julio'    => $row['julio'],
          'agosto'    => $row['agosto'],
          'septiembre'    => $row['septiembre'],
          'octubre'    => $row['octubre'],
          'noviembre'    => $row['noviembre'],
          'diciembre'    => $row['diciembre'],
          'total'    => $row['total'],
          'estado'    => 0,
          'tipo'    => 'RH', 
          'updated_at' => null,
          'created_user' => Auth::user()->username


        ]);


    }
    
    public function rules(): array
    {
        return [
            
             '*.total' => 'integer|size:*.enero+*.febrero+*.marzo+*.abril+*.mayo+*.junio+*.julio+*.agosto+*.septiembre+*.octubre+*.noviembre+*.diciembre',
            
        ];
    }

    public function batchSize(): int
    {
        return 300;
    }

    public function chunkSize(): int
    {
        return 300;
    }
}
