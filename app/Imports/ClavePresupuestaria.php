<?php

namespace App\Imports;
use Illuminate\Support\Facades\DB;
use App\Models\calendarizacion\ProgramacionPresupuesto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\calendarizacion\clasificacion_geografica;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\WithValidation;


class ClavePresupuestaria implements ToModel,WithHeadingRow, WithBatchInserts,WithChunkReading
{
    use RemembersRowNumber;

    public function model(array $row)
    {
        $currentRowNumber = $this->getRowNumber();

        return new ProgramacionPresupuesto([
          'clasificacion_administrativa_id'  =>    $row['ADMCONAC'],
          'entidad_federativa'  => $row['EF'],
          'region'  => $row['REG'],
          'municipio'  => $row['MPIO'],
          'localidad'  => $row['LOC'],
          'upp'    => $row['UPP'],
          'subsecretaria'    => $row['SUBSECRETARIA'],
          'ur'    => $row['UR'],
          'finalidad'    => $row['FINALIDAD'],
          'funcion'    => $row['FUNCION'],
          'subfuncion'    => $row['SUBFUNCION'],
          'eje'    => $row['EG'],
          'linea_accion'    => $row['PT'],
          'programa_sectorial'    => $row['PS'],
          'tipologia_conac'    => $row['PRG'],
          'programa_presupuestario'    => $row['SPR'],
          'subprograma_presupuestario'    => $row['PY'],
          'proyecto_presupuestario'   => $row['IDPARTIDA'],
          'periodo_presupuestal'    => $row['TIPOGASTO'],
          'posicion_presupuestaria'    => $row['TIPOGASTO'],
          'tipo_gasto'    => $row['TIPOGASTO'],
          'anio'    => $row['AÑO'],
          'etiquetado' => $row['NO ETIQUETADO Y ETIQUETADO'],
          'fuente_financiamiento' => $row['NO ETIQUETADO Y ETIQUETADO'],
          'ramo' => $row['RAMO'],
          'fondo_ramo' => $row['RAMO'],
          'capital' => $row['RAMO'],
          'proyecto_obra' => $row['RAMO'],
          'ejercicio' => $row['RAMO'],
          'fondo_id'    => $row['FONDO'],
          'enero'    => $row['ENERO'],
          'febrero'    => $row['FEBRERO'],
          'marzo'    => $row['MARZO'],
          'abril'    => $row['ABRIL'],
          'mayo'    => $row['MAYO'],
          'junio'    => $row['JUNIO'],
          'julio'    => $row['JULIO'],
          'agosto'    => $row['AGOSTO'],
          'septiembre'    => $row['SEPTIEMBRE'],
          'octubre'    => $row['OCTUBRE'],
          'noviembre'    => $row['NOVIEMBRE'],
          'diciembre'    => $row['DICIEMBRE'],
          'total'    => $row['TOTAL'],
          'estado'    => $row['ESTADO'],
          'tipo'    => $row['TIPO'], 



        ]);


    }
    
    public function rules(): array
    {
        return [
            
             '*.total' => 'integer|size:*.ENERO+*.FEBRERO+*.MARZO+*.ABRIL+*.MAYO+*.JUNIO+*.JULIO+*.AGOSTO+*.SEPTIEMBRE+*.OCTUBRE+*.NOVIEMBRE+*.DICIEMBRE',
             'clasificacion_geografica_id' =>  'required|string',
            
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
