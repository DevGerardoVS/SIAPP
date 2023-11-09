<?php

namespace App\Jobs;

use App\Events\ActualizarSesionUsuario;
use App\Models\carga_masiva_estatus;
use App\Models\ProgramacionPresupuesto;
use App\Models\uppautorizadascpnomina;
use Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\calendarizacion\clasificacion_geografica;
use App\Models\Catalogo;
use App\Models\Fondos;
use App\Models\Obra;
use App\Models\PosicionPresupuestaria;
use App\Models\RelEconomicaAdministrativa;
use App\Models\v_epp;
use DB;
use Illuminate\Support\Facades\Session;
class CargaMasivaClaves implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filearray;
    protected $user;

    public function __construct($filearray,$user)
    {
        $this->filearray = $filearray;
        $this->user = $user;

    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //si todo sale bien procedemos al import
        try {


            //por si el de arriba no funciona session(['key' => 'value']);
            $arrayErrores = array();
            $tipoclave = '';
            $añoclave = 0;
            $usuarioclave = '';
            $currentrow=1;
            $usuarioclave = 'CargaMasiva' . $this->user->username;

            DB::beginTransaction();
                            //validacion de año 
                if (strlen($this->filearray['0']['20']) == 2 && is_numeric($this->filearray['0']['20'])) {
                    $year = '20' . $this->filearray['0']['20'];
                    $añoclave = $year;
                } else {
                    if ($this->filearray['0']['20'] != 2) {
                        $añoclave = 2024;
                    }

                }
                
            if ($this->user->id_grupo == 1 || $this->user->id_grupo == 5) {
                $uppsautorizadas = uppautorizadascpnomina::where('clv_upp',$this->filearray['0']['5'])->count();

                $arrayadmconac = str_split($this->filearray['0']['0'], 1);

                $valadm = v_epp::select()
                    ->where('clv_sector_publico', $arrayadmconac[0])
                    ->where('clv_sector_publico_f', $arrayadmconac[1])
                    ->where('clv_sector_economia', $arrayadmconac[2])
                    ->where('clv_subsector_economia', $arrayadmconac[3])
                    ->where('clv_ente_publico', $arrayadmconac[4])
                    ->where('ejercicio', $añoclave)
                    ->count();
                if ($valadm < 1) {
                    array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de admonac es invalida. ');

                }
                 }

            foreach ($this->filearray as  $k) {
                if ($this->user->id_grupo == 3) {
                    $uppsautorizadas = uppautorizadascpnomina::where('clv_upp',$this->filearray['0']['5'])->count();
    
                    $arrayadmconac = str_split($this->filearray['0']['0'], 1);
    
                    $valadm = v_epp::select()
                        ->where('clv_sector_publico', $arrayadmconac[0])
                        ->where('clv_sector_publico_f', $arrayadmconac[1])
                        ->where('clv_sector_economia', $arrayadmconac[2])
                        ->where('clv_subsector_economia', $arrayadmconac[3])
                        ->where('clv_ente_publico', $arrayadmconac[4])
                        ->where('ejercicio', $añoclave)
                        ->count();
                    if ($valadm < 1) {
                        array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de admonac es invalida. ');
    
                    }
                     }


                ///validaciones de catalogo
                $valcat = Catalogo::select()
                    ->where('grupo_id', '6')
                    ->where('clave', $k['5'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['5'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor de upp asignado no es valido. ');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '7')
                    ->where('clave', $k['6'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['6'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de subsecretaria introducida no es valida. ');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '8')
                    ->where('clave', $k['7'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['7'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El campo ur no existe o la combinacion de ur upp y secretaria es invalida. ');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '9')
                    ->where('clave', $k['8'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['8'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de finalidad introducida no es valida. ');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '10')
                    ->where('clave', $k['9'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['9'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de funcion introducida no es valida. ');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '11')
                    ->where('clave', $k['10'])
                    ->count();
                $valcat >= 1 ? $k['10'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de subfuncion introducida no es valida. ');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '12')
                    ->where('clave', $k['11'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['11'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de eje es invalida. ');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '13')
                    ->where('clave', $k['12'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['12'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de pt es invalida. ');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '14')
                    ->where('clave', $k['13'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['13'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de ps es invalida. ');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '15')
                    ->where('clave', $k['14'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['14'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de sprconac es invalida. ');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '16')
                    ->where('clave', $k['15'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['15'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de prg es invalida. ');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '17')
                    ->where('clave', $k['16'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['16'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de spr es invalida. ');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '18')
                    ->where('clave', $k['17'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['17'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de py es invalida. ');




                $arraypos = str_split($k['18'], 1);


                if (count($arraypos) >= 4) {
                    if ($k['16'] == 'UUU') {
                        if ($uppsautorizadas) {
                            $tipoclave = 'RH';
                        } else {
                            $tipoclave = 'Operativo';
                        }

                        $k['26'] == '000000' ? $k['26'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La obra es invalida. ');



                        if ($arraypos[0] == 1) {
                            $valpos = PosicionPresupuestaria::select()
                                ->where('clv_capitulo', $arraypos[0])
                                ->where('clv_concepto', $arraypos[1])
                                ->where('clv_partida_generica', $arraypos[2])
                                ->where('clv_partida_especifica', $arraypos[3] . $arraypos[4])
                                ->where('clv_tipo_gasto', $k['19'])
                                ->count();
                            if ($valpos < 1) {
                                array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de posición presupuestaria es invalida. ');
                            }
                        }

                    } else {
                        $tipoclave = 'Operativo';

                        $valObra = Obra::select()
                            ->where('clv_proyecto_obra', $k['26'])
                            ->count();
                        if ($valObra > 0) {
                            $valpos = PosicionPresupuestaria::select()
                                ->where('clv_capitulo', $arraypos[0])
                                ->where('clv_concepto', $arraypos[1])
                                ->where('clv_partida_generica', $arraypos[2])
                                ->where('clv_partida_especifica', $arraypos[3] . $arraypos[4])
                                ->where('clv_tipo_gasto', $k['19'])
                                ->count();
                            if ($valpos < 1) {
                                array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de posición presupuestaria es invalida. ');
                            }

                        } else {
                            //la obra no existe
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La obra no existe. ');
                        }


                    }
                } else {
                    array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de posición presupuestaria es invalida. ');

                }





                //validacion nueva sobre idpartida/tipo gasto en combinacion con admonac

                $valrelEco = RelEconomicaAdministrativa::select()
                    ->where('clasificacion_administrativa', $k['0'])
                    ->where('clasificacion_economica', $k['18'] . $k['19'])
                    ->count();
                if ($valrelEco < 1) {
                    array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clasificacion economica introducida es invalida para esta clave administrativa. ');
                }

                //validacion de fondos
                $valfondo = Fondos::select()
                    ->where('clv_etiquetado', $k['21'])
                    ->where('clv_fuente_financiamiento', $k['22'])
                    ->where('clv_ramo', $k['23'])
                    ->where('clv_fondo_ramo', $k['24'])
                    ->where('clv_capital', $k['25'])
                    ->count();
                if ($valfondo < 1) {
                    array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La combinacion de las claves de la celda V a Z es invalida. ');
                }


                //validacion de codigo admconac
                if (isset($k['0']) && $k['0'] !== '0') {

                    //validacion de presupuestable
                    $valpresup = v_epp::select()
                        ->where('clv_sector_publico', $arrayadmconac[0])
                        ->where('clv_sector_publico_f', $arrayadmconac[1])
                        ->where('clv_sector_economia', $arrayadmconac[2])
                        ->where('clv_subsector_economia', $arrayadmconac[3])
                        ->where('clv_ente_publico', $arrayadmconac[4])
                        ->where('clv_upp', $k['5'])
                        ->where('clv_subsecretaria', $k['6'])
                        ->where('clv_ur', $k['7'])
                        ->where('clv_finalidad', $k['8'])
                        ->where('clv_funcion', $k['9'])
                        ->where('clv_subfuncion', $k['10'])
                        ->where('clv_eje', $k['11'])
                        ->where('clv_linea_accion', $k['12'])
                        ->where('clv_programa_sectorial', $k['13'])
                        ->where('clv_tipologia_conac', $k['14'])
                        ->where('clv_programa', $k['15'])
                        ->where('clv_subprograma', $k['16'])
                        ->where('clv_proyecto', $k['17'])
                        ->where('ejercicio', $añoclave)
                        ->where('presupuestable', 1)
                        ->count();
                    if ($valpresup < 1) {
                        array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El programa seleccionado no es presupuestable, verifica las columnas A, F a R y el año. ');

                    }
                }

                //validacion que el conjunto sea una clave valida
                $valcomb = v_epp::select()
                    ->where('clv_finalidad', $k['8'])
                    ->where('clv_funcion', $k['9'])
                    ->where('clv_subfuncion', $k['10'])
                    ->where('clv_eje', $k['11'])
                    ->where('clv_linea_accion', $k['12'])
                    ->where('clv_programa_sectorial', $k['13'])
                    ->where('clv_tipologia_conac', $k['14'])
                    ->where('clv_programa', $k['15'])
                    ->where('clv_subprograma', $k['16'])
                    ->where('clv_proyecto', $k['17'])
                    ->where('ejercicio', $añoclave)
                    ->get();
                if (count($valcomb) < 1) {
                    array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La combinacion de claves de la celda I a la R es invalida. ');

                }

                //validacion de trio upp/ur/sub en vista
                $valv_eje = v_epp::select()
                    ->where('clv_upp', $k['5'])
                    ->where('clv_ur', $k['7'])
                    ->where('clv_subsecretaria', $k['6'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                if ($valv_eje < 1) {
                    array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': la combinacion de ur upp y secretaria es invalida. ');


                }
                //validacion de total
                $suma = $k['28'] + $k['29'] + $k['30'] + $k['31'] + $k['32'] + $k['33'] + $k['34'] + $k['35'] + $k['36'] + $k['37'] + $k['38'] + $k['39'];
                if ($k['27'] != $suma) {
                    array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El total no coincide con los meses. ');


                }

/*                 $k['0'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda admconac no puede ir vacio. ') : null;
                $k['1'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda ef no puede ir vacio. ') : null;
                $k['2'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda reg no puede ir vacio. ') : null;
                $k['3'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda mpio no puede ir vacio. ') : null;
                $k['4'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda loc no puede ir vacio. ') : null;
                $k['5'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda upp no puede ir vacio. ') : null;
                $k['6'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda subsecretaria no puede ir vacio. ') : null;
                $k['7'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda ur no puede ir vacio. ') : null;
                $k['8'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda finalidad no puede ir vacio. ') : null;
                $k['9'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda función no puede ir vacio. ') : null;
                $k['10'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda subfunción no puede ir vacio. ') : null;
                $k['11'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda eje no puede ir vacio. ') : null;
                $k['12'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda pt no puede ir vacio. ') : null;
                $k['13'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda ps no puede ir vacio. ') : null;
                $k['14'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda sprconac no puede ir vacio. ') : null;
                $k['15'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda prg no puede ir vacio. ') : null;
                $k['16'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda spr no puede ir vacio. ') : null;
                $k['17'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda py no puede ir vacio. ') : null;
                $k['18'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda idpartida no puede ir vacio. ') : null;
                $k['19'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda tipo de gasto no puede ir vacio. ') : null;
                $k['20'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda año no puede ir vacio. ') : null;
                $k['21'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda etiquetado y no etiquetado no puede ir vacio. ') : null;
                $k['22'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda fconac no puede ir vacio. ') : null;
                $k['23'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda ramo no puede ir vacio. ') : null;
                $k['24'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda fondo no puede ir vacio. ') : null;
                $k['25'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda ci no puede ir vacio. ') : null;
                $k['26'] == '' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La celda obra no puede ir vacio. ') : null;
                $k['27'] == '' ? $k['27'] = 0 : null;
                $k['28'] == '' ? $k['28'] = 0 : null;
                $k['29'] == '' ? $k['29'] = 0 : null;
                $k['30'] == '' ? $k['30'] = 0 : null;
                $k['31'] == '' ? $k['31'] = 0 : null;
                $k['32'] == '' ? $k['32'] = 0 : null;
                $k['33'] == '' ? $k['33'] = 0 : null;
                $k['34'] == '' ? $k['34'] = 0 : null;
                $k['35'] == '' ? $k['35'] = 0 : null;
                $k['36'] == '' ? $k['36'] = 0 : null;
                $k['37'] == '' ? $k['37'] = 0 : null;
                $k['38'] == '' ? $k['38'] = 0 : null;
                $k['39'] == '' ? $k['39'] = 0 : null; 


                is_numeric($k['27']) ? null : $k['27'] = 0;
                is_numeric($k['28']) ? null : $k['28'] = 0;
                is_numeric($k['29']) ? null : $k['29'] = 0;
                is_numeric($k['30']) ? null : $k['30'] = 0;
                is_numeric($k['31']) ? null : $k['31'] = 0;
                is_numeric($k['32']) ? null : $k['32'] = 0;
                is_numeric($k['33']) ? null : $k['33'] = 0;
                is_numeric($k['34']) ? null : $k['34'] = 0;
                is_numeric($k['35']) ? null : $k['35'] = 0;
                is_numeric($k['36']) ? null : $k['36'] = 0;
                is_numeric($k['37']) ? null : $k['37'] = 0;
                is_numeric($k['38']) ? null : $k['38'] = 0;
                is_numeric($k['39']) ? null : $k['39'] = 0;*/


                //validacion de parte clave geografica
                $valgeo = clasificacion_geografica::select()
                    ->where('clv_entidad_federativa', $k['1'])
                    ->where('clv_region', $k['2'])
                    ->where('clv_municipio', $k['3'])
                    ->where('clv_localidad', $k['4'])->count();
                $valgeo < 1 ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La combinacion de las claves de la celda B a E es invalida') : null;



                $clave = ProgramacionPresupuesto::create([
                    'clasificacion_administrativa' => $k['0'],
                    'entidad_federativa' => $k['1'],
                    'region' => $k['2'],
                    'municipio' => $k['3'],
                    'localidad' => $k['4'],
                    'upp' => $k['5'],
                    'subsecretaria' => $k['6'],
                    'ur' => $k['7'],
                    'finalidad' => $k['8'],
                    'funcion' => $k['9'],
                    'subfuncion' => $k['10'],
                    'eje' => $k['11'],
                    'linea_accion' => $k['12'],
                    'programa_sectorial' => $k['13'],
                    'tipologia_conac' => $k['14'],
                    'programa_presupuestario' => $k['15'],
                    'subprograma_presupuestario' => $k['16'],
                    'proyecto_presupuestario' => $k['17'],
                    'periodo_presupuestal' => '01-ENE',
                    'posicion_presupuestaria' => $k['18'],
                    'tipo_gasto' => $k['19'],
                    'anio' => $k['20'],
                    'etiquetado' => $k['21'],
                    'fuente_financiamiento' => $k['22'],
                    'ramo' => $k['23'],
                    'fondo_ramo' => $k['24'],
                    'capital' => $k['25'],
                    'proyecto_obra' => $k['26'],
                    'ejercicio' => $añoclave,
                    'enero' => $k['28'],
                    'febrero' => $k['29'],
                    'marzo' => $k['30'],
                    'abril' => $k['31'],
                    'mayo' => $k['32'],
                    'junio' => $k['33'],
                    'julio' => $k['34'],
                    'agosto' => $k['35'],
                    'septiembre' => $k['36'],
                    'octubre' => $k['37'],
                    'noviembre' => $k['38'],
                    'diciembre' => $k['39'],
                    'total' => $k['27'],
                    'estado' => 0,
                    'tipo' => $tipoclave,
                    'updated_at' => null,
                    'created_user' => $usuarioclave
                ]) ;
                $currentrow++;

            }
            if (count($arrayErrores) > 0) {
                DB::rollBack();
                \Log::debug($arrayErrores);

                $payload=  json_encode($arrayErrores);
                carga_masiva_estatus::create([
                    'id_usuario' => $this->user->id,
                    'cargapayload' => $payload,
                    'cargaMasClav' => 2,
                    'created_user' =>$this->user->username
                ]);            
            }



            $b = array(
                "username" => $this->user->username,
                "accion" => 'Carga masiva',
                "modulo" => 'Claves presupuestales'
            );
            Controller::bitacora($b);
            DB::commit();

      
            \Log::debug('Trabajo  exitoso');
            $array_exito=array();
            array_push($array_exito,'Carga masiva exitosa');
            $payload=  json_encode($array_exito);
            carga_masiva_estatus::create([
                'id_usuario' => $this->user->id,
                'cargapayload' => $payload,
                'cargaMasClav' => 1,
                'created_user' =>$this->user->username
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            carga_masiva_estatus::create([
                'id_usuario' => $this->user->id,
                'cargapayload' =>  $e,
                'cargaMasClav' => 2,
                'created_user' =>$this->user->username
            ]);



        }
    }
}
