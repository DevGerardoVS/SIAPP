<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Imports\utils\FunFormatsNew;
use App\Jobs\InsertCMActividades;
use App\Models\notificaciones;
use Log;



class CargaMasivaActividades implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $filearray;
    private $user;
    private $upp;

    public function __construct($filearray,$gen)
    {
        $arreglo = json_decode($gen);
        $this->filearray=$filearray;
        $this->user=$arreglo->user;
        $this->upp=$arreglo->clv_upp;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payloadsent = json_encode(
            array(
                "TypeButton" => 0,// 0 es mensaje, 1 es que si es botton, 2 ahref 
                "route" => "",
                "blocked" => 0, // 0 es Carga masiva Calendarizacion, 1 es Reportes SAPP,3 Carga Masiva SAPP
                "mensaje" => trans('messages.carga_masiva_cargando'),
                "payload" => ""
            )
        );

        $datos = notificaciones::create([
            'id_usuario' => $this->user->id,
            'id_sistema' => 2,
            'payload' => $payloadsent,
            'status' => 0,
            'created_user' => $this->user->username
        ]);
        $resul = FunFormatsNew::saveImport($this->filearray,$this->user);
        switch ($resul['icon']) {
            case 'success':
                Log::debug('success');
                InsertCMActividades::dispatch($resul['arreglo'],$this->user,$this->upp, $datos->id)->onQueue('high');
                
                break;


            default:
            Log::debug('default');
            $payload = json_encode($resul['arreglo']);
            $payloadsent = json_encode(
                array(
                    "TypeButton" => 1,
                    "route" => "'/metas/errores/carga-masiva/'",
                    'blocked' => 4,
                    "mensaje" => trans('messages.carga_masiva_error'),
                    "payload" => $payload
                )
            );
         $noty=   notificaciones::where('id', $datos->id)
                ->update([
                    'payload' => $payloadsent,
                    'status' => 4,
                    'updated_user' => $this->user->username
                ]);
                Log::debug(json_encode($noty));
                break;
        }


    }

}
