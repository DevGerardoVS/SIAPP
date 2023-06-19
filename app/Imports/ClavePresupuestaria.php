<?php

namespace App\Imports;
use Illuminate\Support\Facades\DB;
use App\Models\ProgramacionPresupuesto;
use App\Models\v_epp;
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
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ClavePresupuestaria implements ToModel,WithHeadingRow, WithBatchInserts,WithChunkReading,WithValidation,SkipsEmptyRows
{

    
    use RemembersRowNumber;
    use Importable;
    
    public function prepareForValidation($row,$index)
    {
        //validacion de codigo admconac
        $arrayadmconac = str_split($row['admconac'], 1);
        $valadm= v_epp::select()->where('clv_sector_publico',$arrayadmconac[0])
        ->where('clv_region',$arrayadmconac[1])
        ->where('clv_municipio',$arrayadmconac[2])
        ->where('clv_localidad',$arrayadmconac[3])
        ->where('clv_localidad',$arrayadmconac[4])
        ->count();
        \Log::debug('//////////// valos de consulta de admconac');
        \Log::debug($valadm);
         //validacion de parte clave geografica
         $valgeo= ProgramacionPresupuesto::select('clv_entidad_federativa')->where('clv_entidad_federativa',$row['ef'])
         ->where('clv_region',$row['reg'])
         ->where('clv_municipio',$row['mpio'])
         ->where('clv_localidad','loc')->value('clv_entidad_federativa');
         \Log::debug('//////////// valos de consulta de clave geografica');
         \Log::debug($valgeo);
         $valgeo==$row['ef'] ? $row['ef']=NULL : $row['ef']; 
        //validacion de tipo de usuario
      $row['tipo']='RH';
       //validacion si la upp tiene firmados claves presupuestales
       $valupp= ProgramacionPresupuesto::select('estado')->where('upp', $row['upp'])->where('estado', 1)->value('estado');
        $valupp==1 ? $row['upp']=NULL : $row['upp']; 
        return $row;
    }

    public function model(array $row)
    {


        $currentRowNumber = $this->getRowNumber();
        return new ProgramacionPresupuesto([
          'clasificacion_administrativa'  =>  $row['admconac'],
          'entidad_federativa'  => $row['ef'],
          'region'  => $row['reg'],
          'municipio'  => $row['mpio'],
          'localidad'  => $row['loc'],
          'upp'    =>  $row['upp'],
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
            '*.tipo' => Rule::in(['RH', 'Operativo']),

             //validacion 7 verificar que exista
             '*.ef' =>  'required|string',
/*              '*.reg' =>  Rule::exists('clasificacion_geografica','clv_region'),                                        
             '*.mpio' =>  Rule::exists('clasificacion_geografica','clv_municipio'),                                        
             '*.loc' =>  Rule::exists('clasificacion_geografica','clv_localidad'),                                        
            */                             
             
             //validacion 3 verificar que upp este autorizada comentada porque no hay upps autorizadas aun
/*              '*.upp' => ['required',
                Rule::exists('uppautorizadascpnomina','upp_id')                                        
            ], */
            
              '*.upp' =>  'required|string', 
        ];
    }

     public function customValidationMessages()
{
    return [
        '*.upp.exists' => 'No se pueden registrar las claves porque no esta autorizada la upp ',
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
