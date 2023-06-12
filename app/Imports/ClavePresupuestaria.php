<?php

namespace App\Imports;
use Illuminate\Support\Facades\DB;
use App\Models\calendarizacion\ProgramacionPresupuesto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\calendarizacion\clasificacion_geografica;


class ClavePresupuestaria implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        return new ProgramacionPresupuesto([
          //'clasificacion_administrativa_id'  =>    $row['ADMCONAC'],
            'clasificacion_geografica_id' => clasificacion_geografica::select()
            ->where('entidad_federativa_id', $row['EF'])
            ->where('region_id', $row['REG'])
            ->where('municipio_id', $row['MPIO'])
            ->where('localidad_id', $row['LOC'])->value('id'),
            'at'    => $row['UPP'],
            'at'    => $row['SUBSECRETARIA'],
            'at'    => $row['UR'],
            'at'    => $row['FINALIDAD'],
            'at'    => $row['FUNCION'],
            'at'    => $row['SUBFUNCION'],
            'at'    => $row['EG'],
            'at'    => $row['PT'],
            'at'    => $row['PS'],
            'at'    => $row['SPRCONAC'],
            'at'    => $row['PRG'],
            'at'    => $row['SPR'],
            'at'    => $row['PY'],
            'at'    => $row['IDPARTIDA'],
            'at'    => $row['TIPOGASTO'],
            'at'    => $row['AÑO'],
            'at'    => $row['NO ETIQUETADO Y ETIQUETADO'],
            'at'    => $row['FCONAC'],
            'at'    => $row['RAMO'],
            'at'    => $row['FONDO'],
            'at'    => $row['CI'],
            'at'    => $row['OBRA'],
            'at'    => $row['TOTAL'],
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



        ]);
    }
}
