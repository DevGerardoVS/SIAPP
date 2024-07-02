<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Helpers\Calendarizacion\MetasHelper;
use App\Models\notificaciones;
use Log;

class ExportPlantillaMetas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $anio;
    protected $upp;
    protected $user;
    public function __construct($upp,$anio,$user)
    {
        $this->upp = $upp;
        $this->anio=$anio;
        $this->user=$user;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $payloadsent = json_encode(
                array(
                    "TypeButton" => 1,// 0 es mensaje, 1 es que si es botton, 2 ahref 
                    "route" => "",
                    "blocked" => 5, // 0 es Carga masiva Calendarizacion, 1 es Reportes SAPP,3 Carga Masiva SAPP 5 plantilla metas
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
            $perfil = $this->user->id_grupo == 4 ? false : true;
            Log::debug('ExportPlantillaMetas');
            $data = MetasHelper::actividadesFaltantes($this->upp,$this->anio,$perfil);
            $payload = json_encode(["upp"=> $this->upp,"anio"=> $this->anio,"datos"=>$data]);
            $payloadsent = json_encode(
                array(
                    "TypeButton" => 1,
                    "route" => "'/metas/actividades/plantilla-calendario'",
                    'blocked' => 5,
                    "mensaje" => "Plantilla UPP: ".$this->upp,
                    "payload" => $payload
                )
    
            );
            notificaciones::where('id', $datos->id)
            ->update([
                'payload' => $payloadsent,
                'status' => 4,
                'updated_user' => $this->user->username
            ]);
        } catch (\Throwable $th) {
            Log::debug($th);
        }
    }
}
