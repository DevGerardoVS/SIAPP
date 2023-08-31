<?php

namespace App\Imports;
use Illuminate\Support\Facades\DB;
use App\Models\ProgramacionPresupuesto;
use App\Models\PosicionPresupuestaria;
use App\Models\Fondos;
use App\Models\v_epp;
use App\Models\Obra;
use App\Models\RelEconomicaAdministrativa;
use App\Models\V_entidad_ejecutora;
use App\Models\Catalogo;
use App\Models\calendarizacion\clasi;
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
use Illuminate\Support\Facades\Log;
class ClavePresupuestaria implements ToModel,WithHeadingRow,WithValidation,SkipsEmptyRows, WithBatchInserts,WithChunkReading
{

    
    use RemembersRowNumber;
    use Importable,SkipsFailures;
    
    public function prepareForValidation($row,$index)
    {

        ///validaciones de catalogo
        $valcat= Catalogo::select()
        ->where('grupo_id','6')
        ->where('clave',$row['upp'])
        ->count();
        $valcat >= 1 ? $row['upp'] : $row['upp']=NULL; 

        $valcat= Catalogo::select()
        ->where('grupo_id','7')
        ->where('clave',$row['subsecretaria'])
        ->count();
        $valcat >= 1 ? $row['subsecretaria'] : $row['subsecretaria']=NULL; 

        $valcat= Catalogo::select()
        ->where('grupo_id','8')
        ->where('clave',$row['ur'])
        ->count();
        $valcat >= 1 ? $row['ur'] : $row['ur']=NULL; 

        $valcat= Catalogo::select()
        ->where('grupo_id','9')
        ->where('clave',$row['finalidad'])
        ->count();
        $valcat >= 1 ? $row['finalidad'] : $row['finalidad']=NULL; 

        $valcat= Catalogo::select()
        ->where('grupo_id','10')
        ->where('clave',$row['funcion'])
        ->count();
        $valcat >= 1 ? $row['funcion'] : $row['funcion']=NULL; 

        $valcat= Catalogo::select()
        ->where('grupo_id','11')
        ->where('clave',$row['subfuncion'])
        ->count();
        $valcat >= 1 ? $row['subfuncion'] : $row['subfuncion']=NULL; 

        $valcat= Catalogo::select()
        ->where('grupo_id','12')
        ->where('clave',$row['eg'])
        ->count();
        $valcat >= 1 ? $row['eg'] : $row['eg']=NULL ; 

        $valcat= Catalogo::select()
        ->where('grupo_id','13')
        ->where('clave',$row['pt'])
        ->count();
        $valcat >= 1 ? $row['pt'] : $row['pt']=NULL; 

        $valcat= Catalogo::select()
        ->where('grupo_id','14')
        ->where('clave',$row['ps'])
        ->count();
        $valcat >= 1 ? $row['ps'] : $row['ps']=NULL; 

        $valcat= Catalogo::select()
        ->where('grupo_id','15')
        ->where('clave',$row['sprconac'])
        ->count();
        $valcat >= 1 ? $row['sprconac'] : $row['sprconac']=NULL; 

        $valcat= Catalogo::select()
        ->where('grupo_id','16')
        ->where('clave',$row['prg'])
        ->count();
        $valcat >= 1 ? $row['prg'] : $row['prg']=NULL; 

        $valcat= Catalogo::select()
        ->where('grupo_id','18')
        ->where('clave',$row['py'])
        ->count();
        $valcat >= 1 ? $row['py'] : $row['py']=NULL; 
        



         $arraypos = str_split($row['idpartida'], 1);
        if(count($arraypos)>=4){
            if($row['spr']=='UUU'){
                $row['tipo']='RH';
                $row['obra'] == '000000'? $row['obra']: $row['obra'] =NULL;
    
                 
    
                if($arraypos[0]==1 ){
                    $valpos = PosicionPresupuestaria::select()
                    ->where('clv_capitulo',$arraypos[0])
                    ->where('clv_concepto',$arraypos[1])
                    ->where('clv_partida_generica',$arraypos[2])
                    ->where('clv_partida_especifica',$arraypos[3].$arraypos[4])
                    ->where('clv_tipo_gasto',$row['tipogasto'])
                    ->count();
                    if($valpos < 1  ){
                        $row['idpartida']=NULL;
                    }
                }
    
             }
             
            else{
                $row['tipo']='Operativo';
    
                $valObra = obra::select()
                ->where('clv_proyecto_obra',$row['obra'])
                ->count();
                if($valObra > 0 ){
                    $valpos = PosicionPresupuestaria::select()
                    ->where('clv_capitulo',$arraypos[0])
                    ->where('clv_concepto',$arraypos[1])
                    ->where('clv_partida_generica',$arraypos[2])
                    ->where('clv_partida_especifica',$arraypos[3].$arraypos[4])
                    ->where('clv_tipo_gasto',$row['tipogasto'])
                    ->count();
                    if($valpos < 1  ){
                        $row['idpartida']=NULL;
                    }
    
               }
               else{
                    //la obra no existe
                    $row['obra']=NULL;
               }
            
    
            } 
        }
        else{
            $row['idpartida']=NULL;
 
        }

        

         

        //validacion nueva sobre idpartida/tipo gasto en combinacion con admonac
        
        $valrelEco= RelEconomicaAdministrativa::select()
        ->where('clasificacion_administrativa',$row['admconac'])
        ->where('clasificacion_economica',$row['idpartida'].$row['tipogasto'])
        ->count();
        if($valrelEco < 1 ){
            $row['admconac']='0';
        }
        
        //validacion de fondos
        $valfondo = Fondos::select()
        ->where('clv_etiquetado', $row['no_etiquetado_y_etiquetado'])
        ->where('clv_fuente_financiamiento',$row['fconac'])
        ->where('clv_ramo',$row['ramo'])
        ->where('clv_fondo_ramo', $row['fondo'])
        ->where('clv_capital',$row['ci'])
        ->count();
        if($valfondo < 1 ){
            $row['no_etiquetado_y_etiquetado']=NULL;
        }


        //validacion de codigo admconac
        if (isset($row['admconac']) && $row['admconac']!=='0') {
            $arrayadmconac = str_split($row['admconac'], 1);

            $valadm= v_epp::select()
            ->where('clv_sector_publico',$arrayadmconac[0])
            ->where('clv_sector_publico_f',$arrayadmconac[1])
            ->where('clv_sector_economia',$arrayadmconac[2])
            ->where('clv_subsector_economia',$arrayadmconac[3])
            ->where('clv_ente_publico',$arrayadmconac[4])
            ->count();
            if($valadm < 1 ){
                $row['admconac']=NULL;
    
            }
           //validacion de presupuestable
            $valpresup= v_epp::select()
            ->where('clv_sector_publico',$arrayadmconac[0])
            ->where('clv_sector_publico_f',$arrayadmconac[1])
            ->where('clv_sector_economia',$arrayadmconac[2])
            ->where('clv_subsector_economia',$arrayadmconac[3])
            ->where('clv_ente_publico',$arrayadmconac[4])
            ->where('clv_upp',$row['upp'])
            ->where('clv_subsecretaria',$row['subsecretaria'])
            ->where('clv_ur',$row['ur'])
            ->where('clv_finalidad',$row['finalidad'])
            ->where('clv_funcion',$row['funcion'])
            ->where('clv_subfuncion',$row['subfuncion'])
            ->where('clv_eje',$row['eg'])
            ->where('clv_linea_accion',$row['pt'])
            ->where('clv_programa_sectorial',$row['ps'])
            ->where('clv_tipologia_conac',$row['sprconac'])
            ->where('clv_programa',$row['prg'])
            ->where('clv_subprograma',$row['spr'])
            ->where('clv_proyecto',$row['py'])
            ->where('ejercicio','20'.$row['ano'])
            ->where('presupuestable',1)
            ->count();
            if($valpresup < 1 ){
                 $row['ano']='2';
     
            }
        }

        //validacion que el conjunto sea una clave valida
        $valcomb= v_epp::select()
        ->where('clv_finalidad',$row['finalidad'])
        ->where('clv_funcion',$row['funcion'])
        ->where('clv_subfuncion',$row['subfuncion'])
        ->where('clv_eje',$row['eg'])
        ->where('clv_linea_accion',$row['pt'])
        ->where('clv_programa_sectorial',$row['ps'])
        ->where('clv_tipologia_conac',$row['sprconac'])
        ->where('clv_programa',$row['prg'])
        ->where('clv_subprograma',$row['spr'])
        ->where('clv_proyecto',$row['py'])
        ->get();
        if(count($valcomb) < 1 ){
            $row['spr']=NULL;

        }

        //validacion de trio upp/ur/sub en vista
        $valv_eje= v_epp::select()
        ->where('clv_upp',$row['upp'])
        ->where('clv_ur',$row['ur'])
        ->where('clv_subsecretaria',$row['subsecretaria'])
        ->count();
        if($valv_eje < 1 ){
            $row['ur']=NULL;

        }
        //validacion de total
        $suma=$row['enero']+$row['febrero']+$row['marzo']+$row['abril']+$row['mayo']+$row['junio']+$row['julio']+$row['agosto']+$row['septiembre']+$row['octubre']+$row['noviembre']+$row['diciembre'];
        if($row['total']!=$suma){
         $row['total']=NULL;
        }

         //validacion de parte clave geografica
         $valgeo= clasificacion_geografica::select()
         ->where('clv_entidad_federativa',$row['ef'])
         ->where('clv_region',$row['reg'])
         ->where('clv_municipio',$row['mpio'])
         ->where('clv_localidad',$row['loc'])->count();
         $valgeo < 1 ? $row['ef']=NULL : $row['ef']; 



        //validacion de año 
        if($row['ano']!=='2'){
            $year = '20'.$row['ano'];
            $row['ano']=$year;
        }
        $row['user']='CargaMasiva'.Auth::user()->username;
        //validacion si la upp tiene firmados claves presupuestales
        $valupp= ProgramacionPresupuesto::select('estado')->where('upp', $row['upp'])->where('estado', 1)->where('ejercicio',$row['ano'])->value('estado');
        $valupp==1 ? $row['upp']='0' : $row['upp']; 

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
          'ejercicio' =>  $row['ano'], 
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
          'tipo'    => $row['tipo'], 
          'updated_at' => null,
          'created_user' => $row['user']

        ]);


    }
    
    public function rules(): array
    {
        return [
            '*.tipo' => Rule::in(['RH', 'Operativo']),
             //cambiar validacion de autorizadas unicamente a operativo
             '*.upp' => ['required','string',
                Rule::notIn(['0'])                                       
            ], 
            '*.admconac' => ['required','string',
            Rule::notIn(['0'])                                       
        ],
            '*.ano' =>  Rule::notIn(['2']),
            '*.ef' =>  ['required','string'],
            '*.subsecretaria' =>  ['required','string'],
            '*.finalidad' =>  ['required','string'],
            '*.funcion' =>  ['required','string'],
            '*.subfuncion' =>  ['required','string'],
            '*.pt' =>  ['required','string'],
            '*.ps' =>  ['required','string'],
            '*.sprconac' =>   ['required','string'],
            '*.prg' =>   ['required','string'],
            '*.no_etiquetado_y_etiquetado' =>   ['required','string'],
            '*.spr' => ['required','string'],
            '*.py' =>   ['required','string'],
            '*.obra' =>  ['required',
            Rule::exists('proyectos_obra','clv_proyecto_obra')                                        
        ],
            '*.idpartida' =>   ['required','string'],
            '*.tipogasto' =>  ['required',
            Rule::exists('posicion_presupuestaria','clv_tipo_gasto')                                        
        ],
            '*.ur' =>  ['required'],
            '*.total' => ['required','integer'],
            '*.enero'    =>   ['required','integer'],
            '*.febrero'    =>   ['required','integer'],
            '*.marzo'    =>   ['required','integer'],
            '*.abril'    =>   ['required','integer'],
            '*.mayo'    =>   ['required','integer'],
            '*.junio'    =>   ['required','integer'],
            '*.julio'    =>   ['required','integer'],
            '*.agosto'    =>   ['required','integer'],
            '*.septiembre'  =>   ['required','integer'],
            '*.octubre'    =>   ['required','integer'],
            '*.noviembre'    =>   ['required','integer'],
            '*.diciembre'    =>   ['required','integer'],
           
                         
             

            
        ];
    }

     public function customValidationMessages()
{
    return [
        '*.admconac.required' => 'La clave de admonac es invalida',
        '*.ef' => 'La combinacion de las claves de la celda B a E es invalida',
        '*.upp.required' => 'El valor de upp asignado no es valido',
        '*.upp.not_in' => 'No se pueden registrar las claves porque ya tiene claves firmadas ',
        '*.total' => 'El total no coincide con los meses',
        '*.subsecretaria' =>  'La clave de subsecretaria introducida no es valida',
        '*.finalidad' =>  'La clave de finalidad introducida no es valida',
        '*.funcion' =>  'La clave de funcion introducida no es valida',
        '*.subfuncion' =>  'La clave de subfuncion introducida no es valida',
        '*.pt' =>  'La clave de pt introducida no es valida',
        '*.ps' =>  'La clave de ps introducida no es valida',
        '*.sprconac' =>  'La clave de sprconac introducida no es valida',
        '*.prg' =>  'La clave de prg introducida no es valida',
        '*.spr' =>  'La combinacion de claves de la celda I a la R es invalida',
        '*.py' =>  'La clave de py introducida no es valida',
        '*.ur' => 'El campo ur no existe o la combinacion de ur upp y secretaria es invalida',
        '*.no_etiquetado_y_etiquetado' => 'La combinacion de las claves de la celda V a Z es invalida',
          //Para palabras compuestas en rules usar '_' para que reconozca el tipo
        '*.ano.not_in' => 'El programa seleccionado no es presupuestable, verifica las columnas A, F a R y el año.',
        '*.idpartida' => 'La combinacion de id partida con tipo de gasto es invalida',
        '*.admconac.not_in' => 'La clasificacion economica introducida es invalida para esta clave administrativa',

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
